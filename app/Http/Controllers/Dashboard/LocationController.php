<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\AreaResponsible;
use App\Models\Location;
use App\Models\Region;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class LocationController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * LocationController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Location::class, 'location');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $locations = Location::with(['region', 'block'])
            ->filter()
            ->latest()
            ->paginate();

        return view('dashboard.locations.index', compact('locations'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $regions = Region::where('is_active', true)->get();
        return view('dashboard.locations.create', compact('regions'));
    }

    /**
     * Get area responsible by region (AJAX)
     */
    public function getAreaResponsibleByRegion(Request $request)
    {
        try {
            $regionId = $request->input('region_id');

            if (!$regionId) {
                return response()->json(['success' => false, 'message' => 'region_id مطلوب'], 400);
            }

            $region = Region::find($regionId);

            if (!$region || !$region->is_active) {
                return response()->json(['success' => false, 'message' => 'المنطقة غير موجودة'], 404);
            }

            if (!$region->area_responsible_id) {
                return response()->json([
                    'success' => true,
                    'area_responsible' => null,
                    'message' => 'لا يوجد مسؤول لهذه المنطقة: ' . $region->name
                ]);
            }

            $areaResponsible = AreaResponsible::find($region->area_responsible_id);
            if (!$areaResponsible) {
                return response()->json(['success' => false, 'message' => 'مسؤول المنطقة غير موجود'], 404);
            }

            return response()->json([
                'success' => true,
                'area_responsible' => [
                    'id' => $areaResponsible->id,
                    'name' => $areaResponsible->name,
                    'phone' => $areaResponsible->phone ?? '',
                    'address' => $areaResponsible->address ?? ''
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('AreaResponsible error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'خطأ في الخادم'], 500);
        }
    }

    /**
     * Get region with boundaries (AJAX)
     */
    public function getRegionWithBoundaries(Request $request)
    {
        try {
            $regionId = $request->input('region_id');

            if (!$regionId) {
                return response()->json([
                    'success' => false,
                    'message' => 'region_id مطلوب'
                ], 400);
            }

            $region = Region::with('areaResponsible')
                ->select('id', 'name', 'boundaries', 'area_responsible_id', 'center_lat', 'center_lng', 'color') // ✅ color مضاف
                ->find($regionId);

            if (!$region) {
                return response()->json([
                    'success' => false,
                    'message' => 'المنطقة غير موجودة'
                ], 404);
            }

            // تحويل boundaries لـ JSON array إذا كان string
            $boundaries = $region->boundaries;
            if ($boundaries && is_string($boundaries)) {
                $boundaries = json_decode($boundaries, true);
            }

            // fallback للإحداثيات وللون
            $centerLat = $region->center_lat ?: 31.3461;
            $centerLng = $region->center_lng ?: 34.3064;
            $regionColor = $region->color ?: '#3388ff'; // ✅ لون المنطقة من الداتابيز

            return response()->json([
                'success' => true,
                'region' => [
                    'id' => $region->id,
                    'name' => $region->name,
                    'boundaries' => $boundaries,
                    'area_responsible_id' => $region->area_responsible_id,
                    'area_responsible_name' => $region->areaResponsible?->name ?? '',
                    'area_responsible_phone' => $region->areaResponsible?->phone ?? '',
                    'center_lat' => (float) $centerLat,
                    'center_lng' => (float) $centerLng,
                    'color' => $regionColor // ✅ اللون محفوظ هنا
                ]
            ]);
        } catch (\Exception $e) {
            \Log::error('Get region with boundaries error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'خطأ في جلب بيانات المنطقة: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get blocks by area responsible (AJAX)
     */
    public function getBlocksByAreaResponsible(Request $request)
    {
        try {
            $areaResponsibleId = $request->input('area_responsible_id');

            if (!$areaResponsibleId) {
                return response()->json(['success' => false, 'message' => 'area_responsible_id مطلوب'], 400);
            }

            $blocks = Block::where('area_responsible_id', $areaResponsibleId)
                ->get();

            return response()->json([
                'success' => true,
                'blocks' => $blocks->map(function ($block) {
                    return [
                        'id' => $block->id,
                        'name' => $block->name ?? 'غير محدد',
                        'title' => $block->title ?? 'غير محدد',
                        'lat' => $block->lat,
                        'lng' => $block->lan ?? $block->lng ?? 0,
                        'phone' => $block->phone ?? '',
                        'address' => $block->title ?? ''
                    ];
                })->toArray()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching blocks: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'خطأ في تحميل البلوكات'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'area_responsible_id' => 'required|exists:area_responsibles,id',
            'region_id' => 'nullable|exists:regions,id',
            'block_id' => 'required|exists:blocks,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:house,shelter,center,other',
            'icon_color' => 'nullable|string|max:7',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ], [
            'name.required' => 'اسم اللوكيشن مطلوب',
            'area_responsible_id.required' => 'يجب اختيار مسؤول المنطقة',
            'block_id.required' => 'يجب اختيار المندوب/البلوك',
            'latitude.required' => 'يجب تحديد الموقع على الخريطة',
            'longitude.required' => 'يجب تحديد الموقع على الخريطة',
            'type.required' => 'يجب اختيار نوع اللوكيشن'
        ]);

        $location = Location::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'region_id' => $validated['region_id'] ?? null,
            'block_id' => $validated['block_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'type' => $validated['type'],
            'icon_color' => $validated['icon_color'] ?? '#9C27B0',
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? true
        ]);

        flash()->success('تم إضافة اللوكيشن بنجاح');
        return redirect()->route('dashboard.locations.show', $location);
    }

    /**
     * Display the specified resource.
     */
    public function show(Location $location)
    {
        $location->load(['region', 'block']);
        return view('dashboard.locations.show', compact('location'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Location $location)
    {
        $regions = Region::where('is_active', true)->get();
        $blocks = Block::all();
        return view('dashboard.locations.edit', compact('location', 'regions', 'blocks'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Location $location)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region_id' => 'required|exists:regions,id',
            'block_id' => 'required|exists:blocks,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'type' => 'required|in:house,shelter,center,other',
            'icon_color' => 'nullable|string|max:7',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'is_active' => 'nullable|boolean'
        ]);

        $location->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'region_id' => $validated['region_id'],
            'block_id' => $validated['block_id'],
            'latitude' => $validated['latitude'],
            'longitude' => $validated['longitude'],
            'type' => $validated['type'],
            'icon_color' => $validated['icon_color'] ?? $location->icon_color,
            'address' => $validated['address'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'is_active' => $validated['is_active'] ?? $location->is_active
        ]);

        flash()->success('تم تحديث اللوكيشن بنجاح');
        return redirect()->route('dashboard.locations.show', $location);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Location $location)
    {
        $location->delete();
        flash()->success('تم حذف اللوكيشن بنجاح');
        return redirect()->route('dashboard.locations.index');
    }

    /**
     * Display a listing of the trashed resource.
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Location::class);
        $locations = Location::onlyTrashed()
            ->with(['region', 'block'])
            ->latest('deleted_at')
            ->paginate();
        return view('dashboard.locations.trashed', compact('locations'));
    }

    /**
     * Display the specified trashed resource.
     */
    public function showTrashed(Location $location)
    {
        $this->authorize('viewTrash', $location);
        return view('dashboard.locations.show', compact('location'));
    }

    /**
     * Restore the trashed resource.
     */
    public function restore(Location $location)
    {
        $this->authorize('restore', $location);
        $location->restore();
        flash()->success('تم استعادة اللوكيشن بنجاح');
        return redirect()->route('dashboard.locations.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     */
    public function forceDelete(Location $location)
    {
        $this->authorize('forceDelete', $location);
        $location->forceDelete();
        flash()->success('تم حذف اللوكيشن نهائياً');
        return redirect()->route('dashboard.locations.trashed');
    }

    /**
     * Get all locations data as JSON for map display
     */
    public function getRegionsData(Request $request)
    {
        $regions = Region::withCount(['locations' => function ($q) {
            $q->where('is_active', true);
        }])
            ->select('id', 'name', 'color', 'boundaries', 'area_responsible_id', 'center_lat', 'center_lng', 'is_active')
            ->where('is_active', true)
            ->get()
            ->map(function ($region) {
                $boundaries = $region->boundaries;
                if ($boundaries && is_string($boundaries)) {
                    $boundaries = json_decode($boundaries, true);
                }

                return [
                    'id' => $region->id,
                    'name' => $region->name,
                    'color' => $region->color ?: '#3388ff',
                    'boundaries' => $boundaries,
                    'area_responsible_id' => $region->area_responsible_id,
                    'locations_count' => $region->locations_count ?? 0,
                    'center_lat' => $region->center_lat ?: 31.3461,
                    'center_lng' => $region->center_lng ?: 34.3064
                ];
            });

        return response()->json([
            'success' => true,
            'regions' => $regions
        ]);
    }


    public function getMapData(Request $request)
    {
        $query = Location::with(['region', 'block'])
            ->where('is_active', true);

        if ($request->has('region_id')) {
            $query->where('region_id', $request->region_id);
        }

        $locations = $query->get()->map(function ($location) {
            return [
                'id' => $location->id,
                'name' => $location->name,
                'description' => $location->description ?? '',
                'coordinates' => [
                    'lat' => (float) $location->latitude,
                    'lng' => (float) $location->longitude
                ],
                'type' => $location->type,
                'icon_color' => $location->icon_color ?? '#9C27B0',
                'address' => $location->address ?? '',
                'phone' => $location->phone ?? '',
                'region' => [
                    'name' => $location->region->name ?? ''
                ],
                'block' => [
                    'name' => $location->block->name ?? '',
                    'title' => $location->block->title ?? ''
                ]
            ];
        });

        return response()->json([
            'success' => true,
            'locations' => $locations
        ]);
    }
}