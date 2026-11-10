<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Block;
use App\Models\Location;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\BlockRequest;
use App\Models\AreaResponsible;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BlocksTemplateExport;

class BlockController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * BlockController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Block::class, 'block');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blocks = Block::filter()
            // تطبيق الشرط الخاص بالمشرف فقط إذا كان المستخدم الحالي مشرفاً
            ->when(auth()->user()?->isSupervisor(), function ($query) {
                // إذا كان المستخدم مشرفاً، اعرض البلوكات التي:
                // مسؤول المنطقة الخاص بها هو ID المشرف الحالي
                $query->where('area_responsible_id', auth()->user()->id);
            })
            // ترتيب النتائج حسب الأحدث وإنشاء ترقيم للصفحات
            ->latest()
            ->paginate();

        $areaResponsibles = AreaResponsible::query()
            ->when(auth()->user()?->isSupervisor(), function ($query) {
                $query->where('id', auth()->user()->id);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return view('dashboard.blocks.index', compact('blocks', 'areaResponsibles'));
    }

    /**
     * Download Excel template for importing blocks.
     */
    public function downloadTemplate()
    {
        $this->authorize('create', Block::class);

        return Excel::download(new BlocksTemplateExport, 'blocks_import_template.xlsx');
    }

    /**
     * Import blocks from Excel file assigned to specific Area Responsible.
     */
    public function import(Request $request)
    {
        $this->authorize('create', Block::class);

        $request->validate([
            'area_responsible_id' => 'required|exists:area_responsibles,id',
            'file'                => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'area_responsible_id.required' => 'يرجى اختيار مسؤول المنطقة.',
            'area_responsible_id.exists'   => 'مسؤول المنطقة المختار غير موجود.',
            'file.required'                => 'يرجى اختيار ملف الإكسل.',
            'file.mimes'                   => 'يجب أن يكون الملف بصيغة xlsx أو xls أو csv.',
        ]);

        $data = Excel::toArray([], $request->file('file'));
        $sheet = $data[0] ?? [];

        if (empty($sheet)) {
            flash()->error('ملف الإكسل فارغ.');
            return redirect()->back();
        }

        $areaResponsibleId = $request->area_responsible_id;
        $imported = 0;

        foreach ($sheet as $index => $row) {
            if ($index == 0) {
                $firstCell = trim((string)($row[0] ?? ''));
                if (in_array($firstCell, ['اسم المندوب', 'اسم_المندوب', 'الاسم', 'Name', 'name', 'اسم'])) {
                    continue;
                }
            }

            $name  = trim((string)($row[0] ?? ''));
            $title = trim((string)($row[1] ?? ''));
            $phone = trim((string)($row[2] ?? ''));
            $note  = trim((string)($row[3] ?? ''));
            $lat   = isset($row[4]) && $row[4] !== '' ? trim((string)$row[4]) : null;
            $lan   = isset($row[5]) && $row[5] !== '' ? trim((string)$row[5]) : null;

            if (empty($name) && empty($title)) {
                continue;
            }

            Block::create([
                'area_responsible_id' => $areaResponsibleId,
                'name'                => $name,
                'title'               => $title,
                'phone'               => $phone ?: null,
                'note'                => $note ?: null,
                'lat'                 => $lat,
                'lan'                 => $lan,
            ]);

            $imported++;
        }

        flash()->success("تم استيراد {$imported} مندوب بنجاح لمسؤول المنطقة المحدد.");

        return redirect()->route('dashboard.blocks.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $areaResponsibles = AreaResponsible::query()
            ->when(auth()->user()?->isSupervisor(), function ($query) {
                // أي شروط إضافية للمشرف
            })
            ->orderBy('name')
            ->get() // نجلب البيانات أولاً كـ Collection
            ->mapWithKeys(function ($item) {
                // نحدد بوضوح أن الـ ID هو المفتاح والاسم هو القيمة
                return [$item->id => $item->name];
            });

        return view('dashboard.blocks.create', compact('areaResponsibles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\BlockRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BlockRequest $request)
    {
        $data = $request->all();
        if ($request->filled('lat')) {
            $data['lat'] = Location::cleanCoordinate($request->input('lat'));
        }
        if ($request->filled('lan')) {
            $data['lan'] = Location::cleanCoordinate($request->input('lan'));
        }

        $block = Block::create($data);

        // المندوب هو نفسه لوكيشن المخيم: إنشاء لوكيشن المخيم تلقائياً
        if (!empty($data['lat']) && !empty($data['lan'])) {
            try {
                $regionId = $block->areaResponsible?->regions()->first()?->id
                    ?? \App\Models\Region::where('is_active', true)->first()?->id
                    ?? \App\Models\Region::first()?->id;

                Location::create([
                    'name' => $block->title ?: $block->name,
                    'region_id' => $regionId,
                    'block_id' => $block->id,
                    'latitude' => $data['lat'],
                    'longitude' => $data['lan'],
                    'type' => 'shelter',
                    'phone' => $block->phone,
                    'address' => $block->title,
                    'is_active' => true,
                ]);
            } catch (\Exception $e) {
                \Log::warning('Failed to auto-create location for block: ' . $e->getMessage());
            }
        }

        flash()->success(trans('blocks.messages.created'));

        return redirect()->route('dashboard.blocks.show', $block);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     */
    public function show(Block $block)
    {
        return view('dashboard.blocks.show', compact('block'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     */
    public function edit(Block $block)
    {
        // جلب مسؤولي المناطق: aid_id كـ key والاسم (name) كـ value
        $areaResponsibles = AreaResponsible::query()
            ->when(auth()->user()?->isSupervisor(), function ($query) {
                // أي شروط إضافية للمشرف
            })
            ->orderBy('name')
            ->get() // نجلب البيانات أولاً كـ Collection
            ->mapWithKeys(function ($item) {
                // نحدد بوضوح أن الـ ID هو المفتاح والاسم هو القيمة
                return [$item->id => $item->name];
            });

        return view('dashboard.blocks.edit', compact('block', 'areaResponsibles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\BlockRequest $request
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BlockRequest $request, Block $block)
    {
        $data = $request->all();
        if ($request->filled('lat')) {
            $data['lat'] = Location::cleanCoordinate($request->input('lat'));
        }
        if ($request->filled('lan')) {
            $data['lan'] = Location::cleanCoordinate($request->input('lan'));
        }

        $block->update($data);

        // المندوب هو نفسه لوكيشن المخيم: مزامنة إحداثيات ومخيم اللوكيشن التابع له تلقائياً
        if (!empty($data['lat']) && !empty($data['lan'])) {
            try {
                $locations = $block->locations;
                if ($locations->isNotEmpty()) {
                    foreach ($locations as $loc) {
                        $loc->update([
                            'latitude' => $data['lat'],
                            'longitude' => $data['lan'],
                            'name' => $loc->name ?: ($block->title ?: $block->name),
                            'phone' => $loc->phone ?: $block->phone,
                            'address' => $loc->address ?: $block->title,
                        ]);
                    }
                } else {
                    $regionId = $block->areaResponsible?->regions()->first()?->id
                        ?? \App\Models\Region::where('is_active', true)->first()?->id
                        ?? \App\Models\Region::first()?->id;

                    Location::create([
                        'name' => $block->title ?: $block->name,
                        'region_id' => $regionId,
                        'block_id' => $block->id,
                        'latitude' => $data['lat'],
                        'longitude' => $data['lan'],
                        'type' => 'shelter',
                        'phone' => $block->phone,
                        'address' => $block->title,
                        'is_active' => true,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to sync location for block: ' . $e->getMessage());
            }
        }

        flash()->success(trans('blocks.messages.updated'));

        return redirect()->route('dashboard.blocks.show', $block);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Block $block
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Block $block)
    {
        $block->delete();

        flash()->success(trans('blocks.messages.deleted'));

        return redirect()->route('dashboard.blocks.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Block::class);

        $blocks = Block::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.blocks.trashed', compact('blocks'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Block $block)
    {
        $this->authorize('viewTrash', $block);

        return view('dashboard.blocks.show', compact('block'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Block $block
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Block $block)
    {
        $this->authorize('restore', $block);

        $block->restore();

        flash()->success(trans('blocks.messages.restored'));

        return redirect()->route('dashboard.blocks.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Block $block
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Block $block)
    {
        $this->authorize('forceDelete', $block);

        $block->forceDelete();

        flash()->success(trans('blocks.messages.deleted'));

        return redirect()->route('dashboard.blocks.trashed');
    }

    public function getByAreaResponsible(Request $request)
    {
        $blocks = Block::query()
            ->when($request->area_responsible_id, function ($q) use ($request) {
                $q->where('area_responsible_id', $request->area_responsible_id);
            })
            ->orderBy('name')
            ->pluck('name', 'id');

        return response()->json($blocks);
    }
}
