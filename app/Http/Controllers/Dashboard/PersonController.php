<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\PeopleExport;
use App\Models\Block;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\PersonRequest;
use App\Models\AreaResponsible;
use DB;
use Gate;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Facades\Excel;

class PersonController extends Controller
{
    use AuthorizesRequests, ValidatesRequests;

    /**
     * PersonController constructor.
     */
    public function __construct()
    {
        $this->authorizeResource(Person::class, 'person');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $baseQuery = Person::filter()
                ->whereNull('relationship')
                ->withCount('familyMembers');

            // تطبيق فلتر مسؤول المنطقة
            if ($areaResponsibleId = request('area_responsible_id')) {
                $baseQuery->where('area_responsible_id', $areaResponsibleId);
            } elseif (auth()->user()?->isSupervisor()) {
                $baseQuery->where(function ($q) {
                    $q->where('area_responsible_id', auth()->user()->id)
                        ->orWhereNull('area_responsible_id');
                });
            }

            $people = $baseQuery->latest()->paginate();

            $notFoundIds = [];
            $unavailableIds = [];

            if (request()->filled('id_num')) {
                $searchedIds = array_filter(
                    preg_split("/\r\n|\n|\r/", request('id_num')),
                    fn($id) => !empty(trim($id))
                );

                $allExistingIds = Person::pluck('id_num')->toArray();
                $availableIds = $people->pluck('id_num')->toArray();

                $notFoundIds = array_values(array_diff($searchedIds, $allExistingIds));
                $unavailableIds = array_values(array_diff(
                    array_intersect($searchedIds, $allExistingIds),
                    $availableIds
                ));
            }

            $blocks = Block::when(auth()->user()?->isSupervisor(), function ($query) {
                $query->where('area_responsible_id', auth()->user()->id);
            })->orderBy('name')->pluck('name', 'id');

            // تسجيل عملية البحث
            logger()->info('تم عرض الصفحة الرئيسية للأشخاص', [
                'user_id' => auth()->id(),
                'filters' => request()->all(),
                'results_count' => $people->total(),
                'not_found_count' => count($notFoundIds),
                'unavailable_count' => count($unavailableIds)
            ]);

            return view('dashboard.people.index', [
                'people' => $people,
                'blocks' => $blocks,
                'notFoundIds' => $notFoundIds,
                'unavailableIds' => $unavailableIds
            ]);
        } catch (\Exception $e) {
            logger()->error('خطأ في الصفحة الرئيسية للأشخاص', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id()
            ]);

            flash()->error('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.');

            return view('dashboard.people.index', [
                'people' => Person::whereRaw('1=0')->paginate(),
                'blocks' => collect(),
                'notFoundIds' => [],
                'unavailableIds' => []
            ]);
        }
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $people = Person::filter()
            ->whereNull('relationship')
            ->withCount('familyMembers');

        $notFoundIds = [];
        $unavailableIds = [];

        if ($request->filled('id_num')) {
            $searchedIds = array_filter(
                preg_split("/\r\n|\n|\r/", $request->input('id_num')),
                fn($id) => !empty(trim($id))
            );

            $availableIds = $people->pluck('id_num')->toArray();

            $notFoundIds = array_values(array_diff($searchedIds, Person::pluck('id_num')->toArray()));

            $unavailableIds = array_values(array_diff(
                array_intersect($searchedIds, $availableIds),
                $people->pluck('id_num')->toArray()
            ));
        }

        $people = $people->latest()->paginate(
            $request->input('perPage', 15)
        );

        $blocks = Block::orderBy('name')->pluck('name', 'id');
        $areaResponsibles = AreaResponsible::query()
            ->orderBy('name')
            ->pluck('name', 'id')
            ->toArray();

        if ($people->isEmpty()) {
            return view('dashboard.people.search', compact('people', 'blocks', 'areaResponsibles', 'notFoundIds', 'unavailableIds'))
                ->with('message', 'لا توجد نتائج للبحث.');
        }

        return view('dashboard.people.search', compact('people', 'blocks', 'areaResponsibles', 'notFoundIds', 'unavailableIds'));
    }

    public function clearSession()
    {
        session()->forget('notFoundIds');
        session()->forget('unavailableIds');
        return response()->json(['success' => true]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function view()
    {
        try {
            // بناء الاستعلام الأساسي بدون استخدام filter() أولاً
            $query = Person::query()
                ->whereNotNull('area_responsible_id')
                ->whereNotNull('block_id')
                ->withCount('familyMembers')
                ->with(['block', 'areaResponsible']);

            // تطبيق فلتر مسؤول المنطقة أولاً
            if ($areaResponsibleId = request('area_responsible_id')) {
                $query->where('area_responsible_id', $areaResponsibleId);
            } elseif (auth()->user()?->isSupervisor()) {
                $query->where('area_responsible_id', auth()->user()->id);
            }

            // تطبيق فلتر المندوب
            if ($blockId = request('block_id')) {
                $query->where('block_id', $blockId);
            }

            // الآن تطبيق باقي الفلاتر من PersonFilter
            $query = $query->filter();

            $people = $query->latest()->paginate();

            // جلب المندوبين بناءً على صلاحيات المستخدم
            $blocks = Block::query()
                ->when(auth()->user()?->isSupervisor(), function ($query) {
                    $query->where('area_responsible_id', auth()->user()->id);
                })
                ->when(request('area_responsible_id') && auth()->user()?->isAdmin(), function ($query) {
                    $query->where('area_responsible_id', request('area_responsible_id'));
                })
                ->orderBy('name')
                ->pluck('name', 'id');

            // تسجيل عملية البحث للمراقبة
            logger()->info('تم عرض قائمة الأشخاص', [
                'user_id' => auth()->id(),
                'filters' => request()->all(),
                'results_count' => $people->total()
            ]);

            return view('dashboard.people.view', compact('people', 'blocks'));
        } catch (\Exception $e) {
            logger()->error('خطأ في عرض قائمة الأشخاص', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'filters' => request()->all()
            ]);

            flash()->error('حدث خطأ في تحميل البيانات. يرجى المحاولة مرة أخرى.');

            return view('dashboard.people.view', [
                'people' => Person::whereRaw('1=0')->paginate(),
                'blocks' => collect()
            ]);
        }
    }

    /**
     * Export filtered data from view page
     */
    public function exportView(Request $request)
    {
        try {
            $request->validate([
                'area_responsible_id' => 'nullable|exists:area_responsibles,id',
                'block_id' => 'nullable|exists:blocks,id',
                'perPage' => 'nullable|integer|min:1|max:1000'
            ]);

            // إضافة معرف للتمييز أن هذا تصدير من صفحة view
            $request->merge(['export_type' => 'view']);

            logger()->info('بدء تصدير البيانات من صفحة العرض', [
                'user_id' => auth()->id(),
                'filters' => $request->all()
            ]);

            $export = new PeopleExport($request);

            return Excel::download($export, 'people_view_filtered.xlsx');
        } catch (\Exception $e) {
            logger()->error('خطأ في تصدير البيانات من صفحة العرض', [
                'error' => $e->getMessage(),
                'user_id' => auth()->id(),
                'filters' => $request->all()
            ]);

            flash()->error('حدث خطأ أثناء تصدير الملف. يرجى المحاولة مرة أخرى.');
            return back();
        }
    }

    public function listPersonFamily(Person $person)
    {
        $people = Person::filter()
            ->where('relative_id', $person->id_num)
            ->latest()->paginate();

        $blocks = Block::when(auth()->user()?->isSupervisor(), function ($query) {
            $query->where('area_responsible_id', auth()->user()->id);
        })->orderBy('name')->pluck('name', 'id');

        return view('dashboard.people.families.index', compact('people', 'blocks'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blocks = Block::when(auth()->user()?->isSupervisor(), function ($query) {
            $query->where('area_responsible_id', auth()->user()->id);
        })->orderBy('name')->get();

        return view('dashboard.people.create', compact('blocks'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\PersonRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PersonRequest $request)
    {
        $person = Person::create($request->all());

        flash()->success(trans('people.messages.created'));

        return redirect()->route('dashboard.people.show', $person);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        $person->load(['block', 'areaResponsible']);

        $blocks = Block::when(auth()->user()?->isSupervisor(), function ($query) {
            $query->where('area_responsible_id', auth()->user()->id);
        })->orderBy('name')->pluck('name', 'id');

        return view('dashboard.people.show', compact('person', 'blocks'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        $blocks = Block::when(auth()->user()?->isSupervisor(), function ($query) {
            $query->where('area_responsible_id', auth()->user()?->id);
        })->orderBy('name')->get();
        return view('dashboard.people.edit', compact('person', 'blocks'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\PersonRequest $request
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(PersonRequest $request, Person $person)
    {
        $person->update($request->all());

        flash()->success(trans('people.messages.updated'));

        return redirect()->route('dashboard.people.show', $person);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Person $person
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Person $person)
    {
        $person->delete();

        flash()->success(trans('people.messages.deleted'));

        return redirect()->route('dashboard.people.index');
    }

    /**
     * Display a listing of the trashed resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function trashed()
    {
        $this->authorize('viewAnyTrash', Person::class);

        $people = Person::onlyTrashed()->latest('deleted_at')->paginate();

        return view('dashboard.people.trashed', compact('people'));
    }

    /**
     * Display the specified trashed resource.
     *
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function showTrashed(Person $person)
    {
        $this->authorize('viewTrash', $person);

        return view('dashboard.people.show', compact('person'));
    }

    /**
     * Restore the trashed resource.
     *
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restore(Person $person)
    {
        $this->authorize('restore', $person);

        $person->restore();

        flash()->success(trans('people.messages.restored'));

        return redirect()->route('dashboard.people.trashed');
    }

    /**
     * Force delete the specified resource from storage.
     *
     * @param \App\Models\Person $person
     * @throws \Exception
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceDelete(Person $person)
    {
        $this->authorize('forceDelete', $person);

        $person->forceDelete();

        flash()->success(trans('people.messages.deleted'));

        return redirect()->route('dashboard.people.trashed');
    }

    public function export(Request $request)
    {
        try {
            // التحقق من صحة البيانات
            $request->validate([
                'area_responsible_id' => 'nullable|exists:area_responsibles,id',
                'block_id' => 'nullable|exists:blocks,id',
                'perPage' => 'nullable|integer|min:1|max:1000'
            ]);

            // تسجيل عملية التصدير
            logger()->info('بدء عملية تصدير البيانات', [
                'user_id' => auth()->id(),
                'filters' => $request->all()
            ]);

            $filters = $request->all();
            $export = new PeopleExport($request, $filters);

            return Excel::download($export, 'filtered_people.xlsx');
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->warning('بيانات غير صحيحة في التصدير', [
                'errors' => $e->errors(),
                'user_id' => auth()->id()
            ]);

            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            logger()->error('خطأ في تصدير الملف', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
                'filters' => $request->all()
            ]);

            flash()->error('حدث خطأ أثناء تصدير الملف. يرجى المحاولة مرة أخرى.');
            return back();
        }
    }

    public function assignToSupervisor(Person $person)
    {
        $person->update([
            'area_responsible_id' => auth()->user()?->id
        ]);

        flash()->success('تم إضافة الفرد لك بنحاج');

        return redirect()->route('dashboard.people.index');
    }
    public function assignBlock(Person $person, Request $request)
    {
        $person->update([
            'block_id' => $request->block_id
        ]);

        flash()->success('تم إضافة المندوب');

        return redirect()->route('dashboard.people.index', compact('person'));
    }

    public function assignBlocks(Request $request)
    {
        $peopleIds = $request->input('items', []);

        if (!is_array($peopleIds)) {
            $peopleIds = array_filter(explode(',', $peopleIds));
        }

        $blockId = $request->input('block_id');

        if (!empty($peopleIds) && $blockId) {
            Person::whereIn('id', $peopleIds)->each(function ($person) use ($blockId) {
                if (Gate::allows('update', $person)) {
                    $person->update(['block_id' => $blockId]);
                }
            });

            flash()->success(trans('check-all.messages.updated', [
                'type' => 'المندوبين',
            ]));
        } else {
            flash()->error('يرجى تحديد فرد أو مجموعة أفراد.');
        }

        return redirect()->route('dashboard.people.index');
    }
}
