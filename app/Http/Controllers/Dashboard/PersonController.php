<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\PeopleExport;
use App\Models\Block;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\PersonRequest;
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
        $people = Person::filter()
            ->whereNull('relationship')
            // ->whereNull('block_id')
            ->withCount('familyMembers')
            ->when(auth()->user()?->isSupervisor(), function ($query) {
                // إذا كان المستخدم مشرفاً، اعرض رؤساء الأسر الذين يستوفون الشروط التالية:
                $query->where(function ($q) { // تجميع شروط area_responsible_id معاً
                    $q->where('area_responsible_id', auth()->user()->id) // مسؤول المنطقة هو ID المشرف
                        ->orWhereNull('area_responsible_id'); // أو ليس لديه مسؤول منطقة معين
                })
                    ->whereNull('block_id'); // هذا الشرط (block_id IS NULL) سيطبق الآن على الكتلة المجمعة أعلاه
                // تمت إزالة whereNull('relationship') من هنا لأنه مكرر وموجود في بداية الاستعلام
            })
            ->latest()->paginate();

        $blocks = Block::when(auth()->user()?->isSupervisor(),function ($query){
            $query->where('area_responsible_id',auth()->user()?->id);
        })->orderBy('name')->pluck('name','id');


        return view('dashboard.people.index', compact('people','blocks'));
    }

    public function listPersonFamily(Person $person)
    {
        $people = Person::filter()
            ->where('relative_id',$person->id_num)
            ->latest()->paginate();

        return view('dashboard.people.families.index', compact('people'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $blocks = Block::when(auth()->user()?->isSupervisor(),function ($query){
            $query->where('area_responsible_id',auth()->user()->id);
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
        return view('dashboard.people.show', compact('person'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function edit(Person $person)
    {
        $blocks = Block::when(auth()->user()?->isSupervisor(),function ($query){
            $query->where('area_responsible_id',auth()->user()?->id);
        })->orderBy('name')->get();
        return view('dashboard.people.edit', compact('person','blocks'));
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
        // dd('Export Request Filters:', $request->all());
        $filters = $request->all(); // احصل على جميع قيم الفلاتر من الـ URL
        return Excel::download(new PeopleExport($request, $filters), 'filtered_people.xlsx');
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

        flash()->success('تم إضافة المسؤول');

        return redirect()->route('dashboard.people.index');

    }

}