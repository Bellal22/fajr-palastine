<?php

namespace App\Http\Controllers\Dashboard;

use App\Models\Block;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\BlockRequest;
use App\Models\AreaResponsible;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

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

        return view('dashboard.blocks.index', compact('blocks'));
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
                // إذا كان المستخدم مشرفاً، اعرض فقط مسؤول المنطقة المرتبط بـ ID المشرف الحالي
                // هذا يفترض أن عمود 'id' في جدول area_responsibles هو الذي يشير إلى 'users.id'
                $query->where('id', auth()->user()->id); // تم التعديل هنا: استخدام 'id' بدلاً من 'area_responsible_id'
            })
            ->orderBy('name') // ترتيب الخيارات أبجديًا
            ->pluck('name', 'aid_id') // جلب الاسم كقيمة مرئية و aid_id كقيمة فعلية للخيار
            ->toArray();

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
        $block = Block::create($request->all());

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
        $areaResponsibles = AreaResponsible::pluck('name', 'aid_id')->toArray();

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
        $block->update($request->all());

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
}
