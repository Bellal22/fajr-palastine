<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Region;
use App\Models\AreaResponsible;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Dashboard\RegionRequest;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class RegionController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * RegionController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Region::class, 'region');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regions = Region::with('areaResponsible')
            ->filter()
            ->latest()
            ->paginate();

        return view('dashboard.regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areaResponsibles = AreaResponsible::all();

        // كل المناطق الحالية لعرضها فقط على الخريطة
        $regions = Region::whereNotNull('boundaries')->get(['id', 'name', 'color', 'boundaries']);

        return view('dashboard.regions.create', compact('areaResponsibles', 'regions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'area_responsible_id' => 'nullable|exists:area_responsibles,id',
            'boundaries' => 'required|json',
            'is_active' => 'nullable|boolean'
        ], [
            'name.required' => 'اسم المنطقة مطلوب',
            'color.required' => 'يجب اختيار لون للمنطقة',
            'boundaries.required' => 'يجب تحديد حدود المنطقة على الخريطة',
            'boundaries.json' => 'صيغة الحدود غير صحيحة'
        ]);

        // تحويل boundaries من JSON إلى array
        $boundaries = json_decode($validated['boundaries'], true);

        // حساب مركز المنطقة تلقائياً
        $centerLat = (float) collect($boundaries)->avg('lat');
        $centerLng = (float) collect($boundaries)->avg('lng');

        // إنشاء المنطقة
        $region = Region::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'],
            'area_responsible_id' => $validated['area_responsible_id'] ?? null,
            'boundaries' => $boundaries,
            'center_lat' => $centerLat,
            'center_lng' => $centerLng,
            'is_active' => $validated['is_active'] ?? true
        ]);

        // تعيين اللوكيشنات الموجودة داخل المنطقة
        $this->assignLocationsToRegion($region);

        flash()->success(trans('regions.messages.created'));

        return redirect()->route('dashboard.regions.show', $region);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function show(Region $region)
    {
        $region->load(['areaResponsible', 'locations']);

        return view('dashboard.regions.show', compact('region'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function edit(Region $region)
    {
        $areaResponsibles = AreaResponsible::all();

        return view('dashboard.regions.edit', compact('region', 'areaResponsibles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Region $region)
    {
        // التحقق من صحة البيانات
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'color' => 'required|string|max:7',
            'area_responsible_id' => 'nullable|exists:area_responsibles,id',
            'boundaries' => 'required|json',
            'is_active' => 'nullable|boolean'
        ]);

        // تحويل boundaries من JSON إلى array
        $boundaries = json_decode($validated['boundaries'], true);

        // إعادة حساب المركز إذا تم تعديل الحدود
        $centerLat = (float) collect($boundaries)->avg('lat');
        $centerLng = (float) collect($boundaries)->avg('lng');

        // تحديث المنطقة
        $region->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color' => $validated['color'],
            'area_responsible_id' => $validated['area_responsible_id'] ?? null,
            'boundaries' => $boundaries,
            'center_lat' => $centerLat,
            'center_lng' => $centerLng,
            'is_active' => $validated['is_active'] ?? $region->is_active
        ]);

        // تحديث اللوكيشنات بناءً على الحدود الجديدة
        $this->assignLocationsToRegion($region);

        flash()->success(trans('regions.messages.updated'));

        return redirect()->route('dashboard.regions.show', $region);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Region $region
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Region $region)
    {
        $region->delete();

        flash()->success(trans('regions.messages.deleted'));

        return redirect()->route('dashboard.regions.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Region::class);

        $regions = Region::onlyTrashed()
            ->with('areaResponsible')
            ->latest('deleted_at')
            ->paginate();

        return view('dashboard.regions.trashed', compact('regions'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Region $region)
    {
        $this->authorize('viewTrash', $region);

        return view('dashboard.regions.show', compact('region'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Region $region
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Region $region)
    {
        $this->authorize('restore', $region);

        $region->restore();

        flash()->success(trans('regions.messages.restored'));

        return redirect()->route('dashboard.regions.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Region $region
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Region $region)
    {
        $this->authorize('forceDelete', $region);

        $region->forceDelete();

        flash()->success(trans('regions.messages.deleted'));

        return redirect()->route('dashboard.regions.trashed');
    }

    /**
     * Get all regions data as JSON for map display
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getMapData()
    {
        $regions = Region::with('areaResponsible')
            ->where('is_active', true)
            ->get()
            ->map(function ($region) {
                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'description' => $region->description,
                    'color' => $region->color,
                    'boundaries' => $region->boundaries,
                    'center' => [
                        'lat' => (float) $region->center_lat,
                        'lng' => (float) $region->center_lng
                    ],
                    'area_responsible' => $region->areaResponsible ? [
                        'name' => $region->areaResponsible->name,
                        'phone' => $region->areaResponsible->phone
                    ] : null
                ];
            });

        return response()->json([
            'success' => true,
            'regions' => $regions
        ]);
    }

    public function showByResponsible(AreaResponsible $responsible)
    {
        $region = Region::where('area_responsible_id', $responsible->id)->firstOrFail();

        return redirect()->route('dashboard.regions.show', $region);
    }

    /**
     * تعيين اللوكيشنات للمنطقة بناءً على:
     * 1. الموقع الجغرافي (Point-In-Polygon)
     * 2. التبعية الإدارية (Area Responsible -> Blocks -> Locations)
     */
    /**
     * تعيين اللوكيشنات للمنطقة بناءً على:
     * 1. التبعية الإدارية: إنشاء/تحديث لوكيشنات لكل مندوب (Block) تابع لمسؤول المنطقة.
     * 2. الموقع الجغرافي: ربط اللوكيشنات الموجودة مسبقاً التي تقع داخل الحدود.
     */
    private function assignLocationsToRegion(Region $region)
    {
        // 1. التبعية الإدارية: إنشاء/تحديث لوكيشن لكل مندوب
        if ($region->area_responsible_id) {
            $blocks = \App\Models\Block::where('area_responsible_id', $region->area_responsible_id)->get();

            foreach ($blocks as $block) {
                // ننشئ اللوكيشن فقط إذا كان للمندوب إحداثيات
                if ($block->lat && $block->lan) {
                    \App\Models\Location::updateOrCreate(
                        ['block_id' => $block->id], // البحث عن لوكيشن لهذا المندوب
                        [
                            'region_id' => $region->id,
                            'name' => $block->name,
                            'latitude' => $block->lat,
                            'longitude' => $block->lan,
                            'phone' => $block->phone,
                            'type' => 'other',
                            'description' => $block->title . ' - ' . ($block->note ?? ''),
                            'is_active' => true,
                            'icon_color' => '#1976D2' // لون مميز لمناديب المنطقة
                        ]
                    );
                }
            }
        }

        // 2. الموقع الجغرافي: التعامل مع اللوكيشنات الأخرى (التي ليست مناديب) أو تم إنشاؤها يدوياً
        if (!empty($region->boundaries)) {
            // نستثني اللوكيشنات المرتبطة بمناديب لأننا عالجناها في الخطوة الأولى
            $locations = \App\Models\Location::whereNull('block_id')->get();
            $polygon = $region->boundaries;

            foreach ($locations as $location) {
                // إذا كان اللوكيشن مسجل مسبقاً لنفس المنطقة، لا داعي لتحديثه (إلا إذا أردنا التحقق من خروجه)
                if ($location->region_id == $region->id) {
                    continue;
                }

                if ($this->isPointInPolygon($location->getCoordinatesAttribute(), $polygon)) {
                    $location->update(['region_id' => $region->id]);
                }
            }
        }
    }

    /**
     * التحقق مما إذا كانت النقطة داخل المضلع
     */
    private function isPointInPolygon($point, $polygon)
    {
        $x = $point['lat'];
        $y = $point['lng'];

        $inside = false;
        $count = count($polygon);
        $j = $count - 1;

        for ($i = 0; $i < $count; $i++) {
            $xi = $polygon[$i]['lat'];
            $yi = $polygon[$i]['lng'];
            $xj = $polygon[$j]['lat'];
            $yj = $polygon[$j]['lng'];

            $intersect = (($yi > $y) != ($yj > $y))
                && ($x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi);

            if ($intersect) {
                $inside = !$inside;
            }

            $j = $i;
        }

        return $inside;
    }
}
