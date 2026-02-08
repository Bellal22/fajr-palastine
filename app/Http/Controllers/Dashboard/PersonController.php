<?php

namespace App\Http\Controllers\Dashboard;

use App\Exports\FamilyChildrenExport;
use App\Exports\PeopleExport;
use App\Models\Block;
use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Http\Requests\Dashboard\PersonRequest;
use App\Jobs\UpdateAreaResponsiblePeopleCount;
use App\Jobs\UpdateBlockPeopleCount;
use App\Models\AreaResponsible;
use App\Models\City;
use App\Models\Neighborhood;
use App\Models\Choose;
use DB;
use Exception;
use Gate;
use Illuminate\Http\Client\RequestException;
use Http;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Client\Response;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Cache;

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
     * Upload filter file and cache IDs
     */
    public function uploadFilterFile(Request $request)
    {
        $request->validate([
            'id_file' => 'required|file|mimes:txt,csv,xlsx,xls|max:10240', // 10MB max
        ]);

        try {
            $ids = [];
            $file = $request->file('id_file');
            $extension = $file->getClientOriginalExtension();

            if (in_array($extension, ['xlsx', 'xls'])) {
                $data = Excel::toArray([], $file);
                if (!empty($data) && count($data) > 0) {
                    // Flatten all rows in the first sheet
                    foreach ($data[0] as $row) {
                        // Assuming the ID is in the first column
                        if (isset($row[0])) {
                            $ids[] = trim($row[0]);
                        }
                    }
                }
            } else {
                // Text/CSV
                $content = file_get_contents($file->getRealPath());
                // Split by newline or comma
                $ids = preg_split("/\r\n|\n|\r|,/", $content);
                $ids = array_filter(array_map('trim', $ids));
            }

            // Filter empty values
            $ids = array_filter($ids, fn($id) => !empty($id));

            if (empty($ids)) {
                 return response()->json(['success' => false, 'message' => trans('people.messages.file_empty')]);
            }

            $key = 'filter_file_' . md5(uniqid(rand(), true));
            Cache::put($key, array_values($ids), 60); // Cache for 60 minutes

            return response()->json([
                'success' => true,
                'key' => $key,
                'count' => count($ids),
                'message' => trans('people.messages.file_uploaded_success', ['count' => count($ids)])
            ]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
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
                ->whereNull('block_id')
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
            $processedIds = []; // الحالة الثالثة: موجود ولكن تمت معالجته

            $searchedIds = [];

            if (request()->filled('id_num')) {
                $searchedIds = array_merge($searchedIds, array_filter(
                    preg_split("/\r\n|\n|\r/", request('id_num')),
                    fn($id) => !empty(trim($id))
                ));
            }

            if (request()->filled('filter_file_key')) {
                $cachedIds = Cache::get(request('filter_file_key'));
                if ($cachedIds) {
                    $searchedIds = array_merge($searchedIds, $cachedIds);
                }
            }
            
            // Remove duplicates and trim
            $searchedIds = array_unique(array_map('trim', $searchedIds));

            if (!empty($searchedIds)) {
                $allExistingIds = Person::pluck('id_num')->toArray();
                $availableIds = $people->pluck('id_num')->toArray();

                // الحصول على الأشخاص الموجودين ولكن ليسوا في النتائج المتاحة
                $existingButNotAvailable = array_values(array_diff(
                    array_intersect($searchedIds, $allExistingIds),
                    $availableIds
                ));

                // تحديد الأشخاص المعالجين (موجودين ولكن لديهم relationship أو block_id)
                $processedPersons = Person::whereIn('id_num', $existingButNotAvailable)
                    ->where(function ($q) {
                        $q->whereNotNull('relationship')
                            ->orWhereNotNull('block_id');
                    })
                    ->pluck('id_num')
                    ->toArray();

                $notFoundIds = array_values(array_diff($searchedIds, $allExistingIds));
                $unavailableIds = array_values(array_diff($existingButNotAvailable, $processedPersons));
                $processedIds = array_values($processedPersons);
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
                'unavailable_count' => count($unavailableIds),
                'processed_count' => count($processedIds)
            ]);

            return view('dashboard.people.index', [
                'people' => $people,
                'blocks' => $blocks,
                'notFoundIds' => $notFoundIds,
                'unavailableIds' => $unavailableIds,
                'processedIds' => $processedIds
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
                'unavailableIds' => [],
                'processedIds' => []
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
            $request->input('perPage', 1000)
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

    public function generalSearch(Request $request)
    {
        $person = null;
        if ($request->filled('q')) {
            $person = Person::where('id_num', $request->q)
                ->with(['areaResponsible', 'block', 'wife', 'spouse', 'familyHead'])
                ->first();

            if ($person) {
                // جلب جميع أرقام الـ ID الخاصة بأفراد العائلة (لجلب كافة الكوبونات المرتبطة بالعائلة)
                $familyIds = collect([$person->id]);
                
                if ($person->relative_id) {
                    // إذا كان الشخص تابعاً، نجلب رب الأسرة وكل أتباعه
                    $head = Person::where('id_num', $person->relative_id)->with('familyMembers')->first();
                    if ($head) {
                        $familyIds->push($head->id);
                        $familyIds = $familyIds->merge($head->familyMembers->pluck('id'));
                    }
                } else {
                    // إذا كان الشخص رب أسرة، نجلب كل أتباعه
                    $person->load('familyMembers');
                    $familyIds = $familyIds->merge($person->familyMembers->pluck('id'));
                }

                $familyIds = $familyIds->unique();
                // تحميل المشاريع لجميع أفراد العائلة
                $coupons = \DB::table('project_beneficiaries')
                    ->join('projects', 'project_beneficiaries.project_id', '=', 'projects.id')
                    ->leftJoin('sub_warehouses', 'project_beneficiaries.sub_warehouse_id', '=', 'sub_warehouses.id')
                    ->leftJoin('persons', 'project_beneficiaries.person_id', '=', 'persons.id')
                    ->whereIn('project_beneficiaries.person_id', $familyIds)
                    ->select(
                        'projects.id as project_id',
                        'projects.name',
                        'projects.description as project_description',
                        'project_beneficiaries.quantity',
                        'project_beneficiaries.status',
                        'project_beneficiaries.delivery_date',
                        'project_beneficiaries.person_id',
                        'project_beneficiaries.notes',
                        'sub_warehouses.name as warehouse_name',
                        \DB::raw("CONCAT(persons.first_name, ' (', persons.relationship, ')') as recipient_name")
                    )
                    ->get();

                // جلب أنواع الكوبونات لكل مشروع
                $projectIds = $coupons->pluck('project_id')->unique();
                $allCouponTypes = \DB::table('project_coupon_types')
                    ->join('coupon_types', 'project_coupon_types.coupon_type_id', '=', 'coupon_types.id')
                    ->whereIn('project_coupon_types.project_id', $projectIds)
                    ->select('project_coupon_types.project_id', 'coupon_types.name', 'project_coupon_types.quantity')
                    ->get()
                    ->groupBy('project_id');

                foreach ($coupons as $coupon) {
                    $coupon->coupon_types = $allCouponTypes->get($coupon->project_id) ?? collect();
                }
                
                $person->setRelation('familyCoupons', $coupons);
            }

            if (!$person) {
                flash()->error('لم يتم العثور على شخص برقم الهوية المدخل.');
            }
        }

        return view('dashboard.people.general_search', compact('person'));
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
        $blocks_all = Block::when(auth()->user()?->isSupervisor(), function ($query) {
            $query->where('area_responsible_id', auth()->user()->id);
        })->orderBy('name')->get();

        $cities = City::orderBy('name')->pluck('name', 'name');
        $chooses = Choose::orderBy('order')->get()->groupBy('type');

        $neighborhoodsGroupedByCity = City::with('neighborhoods')->get()->mapWithKeys(function ($city) {
            return [$city->name => $city->neighborhoods->map(function ($n) {
                return ['value' => $n->name, 'label' => $n->name];
            })];
        });

        return view('dashboard.people.create', compact(
            'blocks_all', 'cities', 'chooses',
            'neighborhoodsGroupedByCity'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \App\Http\Requests\Dashboard\PersonRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PersonRequest $request)
    {
        try {
            DB::beginTransaction();

            $data = $request->validated();
            $familyMembersData = $data['family_members'] ?? [];
            unset($data['family_members']);

            // Map slugs to names for DB compatibility (No migration)
            $data = $this->mapSlugsToNames($data);

            // Set head of household relationship if not set
            $data['relationship'] = 'رب الأسرة';
            $data['relatives_count'] = count($familyMembersData) + 1;

            $person = Person::create($data);

            // Create family members
            // Create family members
            foreach ($familyMembersData as $memberData) {
                $memberData = $this->mapSlugsToNames($memberData);

                // ربط فقط برقم هوية رب الأسرة - باقي الحقول NULL
                $memberData['relative_id'] = $person->id_num;

                Person::create($memberData);
            }

            DB::commit();

            flash()->success(trans('people.messages.created'));

            return redirect()->route('dashboard.people.show', $person);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء حفظ البيانات: ' . $e->getMessage()]);
        }
    }

    /**
     * Map slugs to names based on Lookups (chooses table)
     */
    protected function mapSlugsToNames(array $data)
    {
        $fields = [
            'social_status',
            'employment_status',
            'housing_type',
            'housing_damage_status',
            'gender',
            'relationship'
        ];

        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $choose = Choose::where('type', $field)
                    ->where('slug', $data[$field])
                    ->first();

                if ($choose) {
                    $data[$field] = $choose->name;
                }
            }
        }

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Person $person
     * @return \Illuminate\Http\Response
     */
    public function show(Person $person)
    {
        $person->load([
            'block',
            'areaResponsible',
            'projects.couponTypes' // ✅ بس couponTypes بدون items
        ]);

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
        $blocks_all = Block::when(auth()->user()?->isSupervisor(), function ($query) {
            $query->where('area_responsible_id', auth()->user()?->id);
        })->orderBy('name')->get();

        $cities = City::orderBy('name')->pluck('name', 'name');
        $chooses = Choose::orderBy('order')->get()->groupBy('type');

        $neighborhoodsGroupedByCity = City::with('neighborhoods')->get()->mapWithKeys(function ($city) {
            return [$city->name => $city->neighborhoods->map(function ($n) {
                return ['value' => $n->name, 'label' => $n->name];
            })];
        });

        return view('dashboard.people.edit', compact(
            'person', 'blocks_all', 'cities', 'chooses',
            'neighborhoodsGroupedByCity'
        ));
    }

    public function toggleFreeze(Person $person)
    {
        $person->update([
            'is_frozen' => !$person->is_frozen
        ]);

        $message = $person->is_frozen 
            ? 'تم تجميد بيانات الفرد بنجاح' 
            : 'تم إلغاء تجميد بيانات الفرد بنجاح';

        flash()->success($message);

        return back();
    }

    public function bulkToggleFreeze(Request $request)
    {
        logger()->info('بدء عملية التجميد الجماعي', [
            'raw_items' => $request->input('items'),
            'freeze' => $request->input('freeze'),
            'user' => auth()->id()
        ]);
        try {
            $peopleIds = $request->input('items', []);
            if (!is_array($peopleIds)) {
                $peopleIds = array_filter(explode(',', $peopleIds));
            }

            $freeze = $request->input('freeze', true);
            $freeze = filter_var($freeze, FILTER_VALIDATE_BOOLEAN);

            if (!empty($peopleIds)) {
                Person::whereIn('id', $peopleIds)->update(['is_frozen' => $freeze]);

                $message = $freeze ? 'تم تجميد الأشخاص المحددين بنجاح' : 'تم إلغاء تجميد الأشخاص المحددين بنجاح';
                return response()->json(['success' => true, 'message' => $message]);
            }

            return response()->json(['success' => false, 'message' => 'لم يتم تحديد أي أشخاص']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'حدث خطأ: ' . $e->getMessage()]);
        }
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
        try {
            DB::beginTransaction();

            if ($person->is_frozen) {
                $housingFields = [
                    'area_responsible_id', 'block_id'
                ];

                foreach ($housingFields as $field) {
                    if ($request->has($field) && $request->input($field) != $person->$field) {
                        flash()->error('تم تجميد بيانات هذا الفرد وتجميد التعديل على بيانات السكن تجنباً لفقدان حقه في الإدراج على بيانات المستفيدين يرجى مراجعة الإدارة بهذا الخصوص');
                        return back()->withInput();
                    }
                }
            }

            $oldIdNum = $person->id_num;
            $data = $request->validated();
            $familyMembersData = $data['family_members'] ?? [];
            unset($data['family_members']);

            // Map slugs to names for DB compatibility (No migration)
            $data = $this->mapSlugsToNames($data);

            // Update Head
            $data['relatives_count'] = count($familyMembersData) + 1;
            $person->update($data);

            // Sync Family Members: Delete old and create new
            Person::where('relative_id', $oldIdNum)->delete();

            foreach ($familyMembersData as $memberData) {
                $memberData = $this->mapSlugsToNames($memberData);

                $memberData['relative_id'] = $person->id_num;
                $memberData['city'] = $person->city;
                $memberData['current_city'] = $person->current_city;
                $memberData['neighborhood'] = $person->neighborhood;
                $memberData['block_id'] = $person->block_id;
                $memberData['area_responsible_id'] = $person->area_responsible_id;

                Person::create($memberData);
            }

            DB::commit();

            flash()->success(trans('people.messages.updated'));

            return redirect()->route('dashboard.people.show', $person);
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'حدث خطأ أثناء تحديث البيانات: ' . $e->getMessage()]);
        }
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
        // احفظ رقم الهوية للشخص الذي سيتم حذفه
        $idNum = $person->id_num;

        // احذف الشخص
        $person->delete();

        // ابحث عن الأفراد المرتبطين بنفس رقم الهوية في عمود relative_id
        $relatedPeople = Person::where('relative_id', $idNum)->get();

        // احذف الأفراد المرتبطين
        foreach ($relatedPeople as $relatedPerson) {
            $relatedPerson->delete();
        }

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

        // استرجاع الأشخاص المحذوفين
        $people = Person::onlyTrashed()->latest('deleted_at')->paginate();

        // استرجاع الأفراد المرتبطين بنفس رقم الهوية
        $relatedPeople = Person::onlyTrashed()
            ->whereIn('relative_id', $people->pluck('id_num'))
            ->get();

        // دمج النتائج
        $allTrashedPeople = $people->merge($relatedPeople)->unique('id');

        return view('dashboard.people.trashed', compact('allTrashedPeople'));
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

        // احفظ رقم الهوية للشخص الذي سيتم حذفه
        $idNum = $person->id_num;

        // احذف الشخص
        $person->forceDelete();

        // ابحث عن الأفراد المرتبطين بنفس رقم الهوية في عمود relative_id
        $relatedPeople = Person::where('relative_id', $idNum)->get();

        // احذف الأفراد المرتبطين
        foreach ($relatedPeople as $relatedPerson) {
            $relatedPerson->forceDelete();
        }

        flash()->success(trans('people.messages.deleted'));

        return redirect()->route('dashboard.people.trashed');
    }

    /**
     * Export filtered data from index page
     */
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

    /**
     * تصدير بيانات الأسر مع الأطفال الذين تنطبق عليهم الفلاتر.
     */
    public function exportChildren(Request $request)
    {
        try {
            $request->validate([
                'area_responsible_id' => 'nullable|exists:area_responsibles,id',
                'block_id' => 'nullable|exists:blocks,id',
                'child_id_num' => 'nullable|string|max:20',
                'child_gender' => 'nullable|in:ذكر,أنثى',
                'child_dob_from' => 'nullable|date',
                'child_dob_to' => 'nullable|date|after_or_equal:child_dob_from',
                'child_age_months_from' => 'nullable|integer|min:0|max:300',
                'child_age_months_to' => 'nullable|integer|min:0|max:300|gte:child_age_months_from',
            ]);

            $request->merge(['export_type' => 'children']);

            logger()->info('بدء تصدير بيانات الأطفال', [
                'user_id' => auth()->id(),
                'filters' => $request->except(['_token', '_method']),
                'timestamp' => now()->toDateTimeString()
            ]);

            ini_set('memory_limit', '1024M');
            set_time_limit(0);

            $filename = 'children_data_' . now()->format('Y_m_d_His') . '.xlsx';
            $export = new FamilyChildrenExport($request);

            logger()->info('تم تصدير بيانات الأطفال بنجاح', [
                'user_id' => auth()->id(),
                'filename' => $filename,
            ]);

            // ✅ إشعار نجاح (يظهر في الصفحة التالية)
            session()->flash('success', 'تم تصدير بيانات الأطفال بنجاح!');

            return Excel::download($export, $filename);
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->warning('فشل التحقق من البيانات', [
                'user_id' => auth()->id(),
                'errors' => $e->errors(),
            ]);

            flash()->error('يوجد أخطاء في البيانات المدخلة.');
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            logger()->error('خطأ في تصدير بيانات الأطفال', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => auth()->id(),
            ]);

            flash()->error('حدث خطأ أثناء التصدير. يرجى المحاولة مرة أخرى.');
            return back();
        }
    }

    public function assignToSupervisor(Person $person)
    {
        if ($person->is_frozen) {
            flash()->error('لا يمكن تخصيص الفرد لك لأنه مجمّد.');
            return back();
        }

        $person->update([
            'area_responsible_id' => auth()->user()?->id
        ]);

        // تشغيل جوب تحديث عدد الأشخاص لمسؤول المنطقة الجديد
        UpdateAreaResponsiblePeopleCount::dispatch(auth()->user()->id);

        flash()->success('تم إضافة الفرد لك بنجاح');

        return redirect()->route('dashboard.people.index');
    }

    public function assignBlock(Person $person, Request $request)
    {
        try {
            if ($person->is_frozen) {
                flash()->error('لا يمكن تخصيص مندوب للفرد لأنه مجمّد.');
                return back();
            }

            $oldBlockId = $person->block_id;

            $person->update([
                'block_id' => $request->block_id
            ]);

            // تحديث العدد في الخلفية
            if ($oldBlockId) {
                UpdateBlockPeopleCount::dispatch($oldBlockId);
            }

            if ($request->block_id) {
                UpdateBlockPeopleCount::dispatch($request->block_id);
            }

            flash()->success('تم إضافة المندوب وسيتم تحديث العدد قريباً');

            return redirect()->route('dashboard.people.index', compact('person'));
        } catch (\Exception $e) {
            logger()->error('خطأ في تخصيص مندوب', [
                'person_id' => $person->id,
                'block_id' => $request->block_id,
                'error' => $e->getMessage()
            ]);

            flash()->error('حدث خطأ في تخصيص المندوب');
            return back();
        }
    }

    public function assignBlocks(Request $request)
    {
        try {
            $peopleIds = $request->input('items', []);
            if (!is_array($peopleIds)) {
                $peopleIds = array_filter(explode(',', $peopleIds));
            }

            $blockId = $request->input('block_id');

            if (!empty($peopleIds) && $blockId) {
                $frozenCount = 0;
                Person::whereIn('id', $peopleIds)->each(function ($person) use ($blockId, &$updatedCount, &$areaResponsibleId, &$frozenCount) {
                    if (Gate::allows('update', $person)) {
                        if ($person->is_frozen) {
                            $frozenCount++;
                            return;
                        }

                        $person->update([
                            'block_id' => $blockId,
                            'area_responsible_id' => auth()->user()?->id,
                        ]);

                        $updatedCount++;
                        $areaResponsibleId = auth()->user()?->id;
                    }
                });

                // ... (jobs)

                if ($updatedCount > 0) {
                    $msg = "تم تحديث {$updatedCount} شخص بنجاح.";
                    if ($frozenCount > 0) {
                        $msg .= " (تم استثناء {$frozenCount} شخص لأن بياناتهم مجمّدة)";
                    }
                    flash()->success($msg);
                } else {
                    $msg = "لم يتم تحديث أي سجل.";
                    if ($frozenCount > 0) {
                        $msg .= " (تم استثناء {$frozenCount} شخص لأن بياناتهم مجمّدة تجنباً لفقدان حقهم في الإدراج على بيانات المستفيدين)";
                        flash()->error($msg);
                    } else {
                        flash()->warning($msg);
                    }
                }
            } else {
                flash()->error('يرجى تحديد فرد أو مجموعة أفراد.');
            }

            return redirect()->route('dashboard.people.index');
        } catch (\Exception $e) {
            logger()->error('خطأ في تخصيص مندوبين متعددين', [
                'error' => $e->getMessage(),
                'people_ids' => $request->input('items', []),
                'block_id' => $request->input('block_id')
            ]);

            flash()->error('حدث خطأ في تخصيص المندوبين');
            return back();
        }
    }

    public function deleteAreaResponsible(Person $person)
    {
        try {
            if ($person->is_frozen) {
                flash()->error('لا يمكن إلغاء ربط المسؤول للفرد لأنه مجمّد.');
                return back();
            }

            $person->update([
                'area_responsible_id' => null,
                'block_id' => null
            ]);

            // تسجيل العملية
            logger()->info('تم حذف المسؤول من شخص', [
                'user_id' => auth()->id(),
                'person_id' => $person->id,
                'person_name' => $person->first_name . ' ' . $person->family_name
            ]);

            flash()->success('تم إلغاء الربط بنجاح');
            return back();
        } catch (\Exception $e) {
            logger()->error('خطأ في حذف المسؤول من الشخص', [
                'error' => $e->getMessage(),
                'person_id' => $person->id,
                'user_id' => auth()->id()
            ]);

            flash()->error('حدث خطأ أثناء إلغاء الربط');
            return back();
        }
    }

    public function deleteAreaResponsibles(Request $request)
    {
        $request->validate([
            'items' => 'required|string',
            'action' => 'sometimes|in:area_responsible_only,block_only,both'
        ]);

        try {
            // تنظيف وتجهيز الـ IDs
            $personIds = explode(',', $request->items);
            $personIds = array_map('trim', $personIds);
            $personIds = array_filter($personIds, function ($id) {
                return !empty($id) && is_numeric($id);
            });

            if (empty($personIds)) {
                flash()->error('لم يتم تحديد أي أشخاص صالحين');
                return back();
            }

            // تحديد نوع العملية
            $action = $request->get('action', 'both');

            // إعداد البيانات للتحديث
            $updateData = [];
            switch ($action) {
                case 'area_responsible_only':
                    $updateData['area_responsible_id'] = null;
                    $actionText = 'مسؤول المنطقة';
                    break;
                case 'block_only':
                    $updateData['block_id'] = null;
                    $actionText = 'المندوب';
                    break;
                case 'both':
                default:
                    $updateData['area_responsible_id'] = null;
                    $updateData['block_id'] = null;
                    $actionText = 'مسؤول المنطقة والمندوب';
                    break;
            }

            // تنفيذ التحديث
            $updatedCount = Person::whereIn('id', $personIds)
                ->where('is_frozen', false)
                ->update($updateData);

            $frozenCount = Person::whereIn('id', $personIds)->where('is_frozen', true)->count();

            if ($updatedCount > 0) {
                $msg = "تم إلغاء ربط {$actionText} من {$updatedCount} شخص بنجاح";
                if ($frozenCount > 0) {
                    $msg .= " (تم استثناء {$frozenCount} شخص لأن بياناتهم مجمّدة)";
                }
                flash()->success($msg);
            } else {
                $msg = "لم يتم تحديث أي سجل.";
                if ($frozenCount > 0) {
                    $msg .= " (تم استثناء {$frozenCount} شخص لأن بياناتهم مجمّدة)";
                }
                flash()->warning($msg);
            }

            return back();
        } catch (\Exception $e) {
            flash()->error('حدث خطأ أثناء إلغاء الربط. يرجى المحاولة مرة أخرى.');
            return back();
        }
    }

    // أضف هذه الدالة مؤقتاً في الـ Controller لتشخيص المشكلة
    public function assignToUsers(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|string',
                'area_responsible_id' => 'required|exists:area_responsibles,id',
                'block_id' => 'required|exists:blocks,id'
            ]);

            $peopleIds = explode(',', $request->items);
            $people = Person::whereIn('id', $peopleIds)->get();

            if ($people->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم العثور على أشخاص للتحديث'
                ], 404);
            }

            $oldResponsibleIds = [];
            $oldBlockIds = [];

            $updatedCount = 0;
            $frozenCount = 0;

            foreach ($people as $person) {
                if ($person->is_frozen) {
                    $frozenCount++;
                    continue;
                }

                if ($person->area_responsible_id) {
                    $oldResponsibleIds[] = $person->area_responsible_id;
                }
                if ($person->block_id) {
                    $oldBlockIds[] = $person->block_id;
                }

                $person->update([
                    'area_responsible_id' => $request->area_responsible_id,
                    'block_id' => $request->block_id
                ]);
                $updatedCount++;
            }

            DB::commit();

            if ($updatedCount === 0 && $frozenCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'لم يتم تحديث أي سجل لأن جميع الأشخاص المحددين بياناتهم مجمّدة تجنباً لفقدان حقهم في الإدراج على بيانات المستفيدين.'
                ], 422);
            }

            // تشغيل الجوبات لتحديث العدادات
            foreach (array_unique($oldResponsibleIds) as $oldResponsibleId) {
                UpdateAreaResponsiblePeopleCount::dispatch($oldResponsibleId);
            }

            UpdateAreaResponsiblePeopleCount::dispatch($request->area_responsible_id);

            foreach (array_unique($oldBlockIds) as $oldBlockId) {
                UpdateBlockPeopleCount::dispatch($oldBlockId);
            }

            UpdateBlockPeopleCount::dispatch($request->block_id);

            $message = 'تم تخصيص مسؤول المنطقة والمندوب بنجاح';
            if ($frozenCount > 0) {
                $message .= " (تم استثناء {$frozenCount} شخص لأن بياناتهم مجمّدة)";
            }

            return response()->json([
                'success' => true,
                'message' => $message
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطأ في البيانات المدخلة',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            // فقط تسجيل الأخطاء المهمة
            logger()->error('خطأ في تخصيص مسؤول المنطقة والمندوب', [
                'error' => $e->getMessage(),
                'items_count' => count(explode(',', $request->items ?? '')),
                'area_responsible_id' => $request->area_responsible_id,
                'block_id' => $request->block_id
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في التخصيص'
            ], 500);
        }
    }

    // AJAX Method لجلب المندوبين حسب مسؤول المنطقة
    public function getBlocksByResponsible(Request $request)
    {
        try {
            $responsibleId = $request->get('responsible_id');
            logger()->info('طلب جلب المندوبين لمسؤول المنطقة', [
                'responsible_id' => $responsibleId,
                'url' => $request->fullUrl(),
                'user_id' => auth()->id()
            ]);

            if (!$responsibleId) {
                return response()->json(['blocks' => []]);
            }

            // التحقق من وجود مسؤول المنطقة
            $areaResponsible = AreaResponsible::find($responsibleId);
            if (!$areaResponsible) {
                logger()->warning('مسؤول المنطقة غير موجود', ['responsible_id' => $responsibleId]);
                return response()->json([
                    'blocks' => [],
                    'message' => 'مسؤول المنطقة غير موجود'
                ], 404);
            }

            // استعلام لجلب المندوبين التابعين لمسؤول المنطقة
            $blocks = Block::where('area_responsible_id', $responsibleId)
                ->select('id', 'name')
                ->orderBy('name')
                ->get();

            logger()->info('تم جلب المندوبين بنجاح', [
                'responsible_id' => $responsibleId,
                'blocks_count' => $blocks->count()
            ]);

            return response()->json([
                'blocks' => $blocks,
                'message' => 'تم جلب المندوبين بنجاح'
            ]);
        } catch (\Exception $e) {
            logger()->error('خطأ في جلب المندوبين', [
                'responsible_id' => $request->get('responsible_id'),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'blocks' => [],
                'message' => 'حدث خطأ في جلب المندوبين'
            ], 500);
        }
    }

    /**
     * AJAX Method لجلب مسؤولي المنطقة حسب الحي السكني
     */
    public function getResponsiblesByNeighborhood(Request $request)
    {
        try {
            $neighborhoodName = $request->get('neighborhood_name');

            if (!$neighborhoodName) {
                return response()->json(['responsibles' => []]);
            }

            $neighborhood = Neighborhood::where('name', $neighborhoodName)->first();

            if (!$neighborhood) {
                return response()->json([
                    'responsibles' => [],
                    'message' => 'الحي غير موجود'
                ], 404);
            }

            $responsibles = $neighborhood->areaResponsibles()
                ->select('area_responsibles.id', 'area_responsibles.name')
                ->orderBy('name')
                ->get();

            return response()->json([
                'responsibles' => $responsibles,
                'message' => 'تم جلب المسؤولين بنجاح'
            ]);
        } catch (\Exception $e) {
            logger()->error('خطأ في جلب مسؤولي المنطقة', [
                'neighborhood_name' => $request->get('neighborhood_name'),
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'responsibles' => [],
                'message' => 'حدث خطأ في جلب المسؤولين'
            ], 500);
        }
    }

    public function api(Person $person)
    {
        try {
            // Load relationships if not already loaded
            $person->loadMissing(['block', 'familyMembers', 'wife']);

            // Prepare API data
            $data = $this->prepareApiData($person);

            // Make API request
            $response = $this->makeApiRequest($data);

            // Update person sync status
            $this->updateSyncStatus($person, $response);

            return response()->json([
                'person' => $person->getFullName(),
                'status' => $response->status(),
                'response' => $response->json() ?? $response->body(),
                // أضف هذا للتشخيص: البيانات اللي اترسلت
                'sent_data_wife' => [
                    'wifi_id' => $data['wifi_id'] ?? 'not set',
                    'wifi_name' => $data['wifi_name'] ?? 'not set'
                ]
            ]);
        } catch (RequestException $e) {  // أعد الـ full namespace
            // Handle HTTP client exceptions
            $person->update([
                'api_sync_status' => 'failed',
                'api_sync_error' => 'HTTP Error: ' . $e->getMessage()
            ]);

            return response()->json([
                'error' => 'API request failed: ' . $e->getMessage(),
                'response' => $e->response?->json() ?? $e->response?->body()  // أضف response للتشخيص
            ], 503);
        } catch (Exception $e) {
            // Handle general exceptions
            $person->update([
                'api_sync_status' => 'failed',
                'api_sync_error' => 'System Error: ' . $e->getMessage()
            ]);

            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function bulkApiSync()
    {
        try {
            // جلب كل أرباب الأسر اللي عندهم passkey و block_id
            $familyHeads = Person::whereNotNull('passkey')
                ->whereNotNull('block_id')
                ->where('relationship', 'رب الأسرة') // أو أي condition تانية تحدد رب الأسرة
                ->with(['block', 'familyMembers', 'wife']) // Load relationships مسبقاً
                ->get();

            $results = [
                'total' => $familyHeads->count(),
                'successful' => 0,
                'failed' => 0,
                'details' => []
            ];

            \Log::info('Bulk API Sync Started', [
                'total_persons' => $familyHeads->count()
            ]);

            foreach ($familyHeads as $person) {
                try {
                    // Prepare API data للشخص
                    $data = $this->prepareApiData($person);

                    // Make API request
                    $response = $this->makeApiRequest($data);

                    // Update person sync status
                    $this->updateSyncStatus($person, $response);

                    if ($response->successful()) {
                        $results['successful']++;
                        $results['details'][] = [
                            'person' => $person->getFullName(),
                            'id' => $person->id,
                            'status' => 'success',
                            'response_status' => $response->status()
                        ];
                    } else {
                        $results['failed']++;
                        $results['details'][] = [
                            'person' => $person->getFullName(),
                            'id' => $person->id,
                            'status' => 'failed',
                            'error' => 'HTTP ' . $response->status() . ': ' . $response->body()
                        ];
                    }

                    // أضف تأخير صغير عشان ما تحمّلش على الـ API
                    usleep(500000); // 0.5 seconds

                } catch (RequestException $e) {
                    $results['failed']++;

                    $person->update([
                        'api_sync_status' => 'failed',
                        'api_sync_error' => 'HTTP Error: ' . $e->getMessage()
                    ]);

                    $results['details'][] = [
                        'person' => $person->getFullName(),
                        'id' => $person->id,
                        'status' => 'failed',
                        'error' => 'HTTP Error: ' . $e->getMessage()
                    ];

                    \Log::error('API Sync Failed - HTTP Error', [
                        'person_id' => $person->id,
                        'error' => $e->getMessage()
                    ]);
                } catch (Exception $e) {
                    $results['failed']++;

                    $person->update([
                        'api_sync_status' => 'failed',
                        'api_sync_error' => 'System Error: ' . $e->getMessage()
                    ]);

                    $results['details'][] = [
                        'person' => $person->getFullName(),
                        'id' => $person->id,
                        'status' => 'failed',
                        'error' => 'System Error: ' . $e->getMessage()
                    ];

                    \Log::error('API Sync Failed - System Error', [
                        'person_id' => $person->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            \Log::info('Bulk API Sync Completed', $results);

            return response()->json([
                'message' => 'Bulk sync completed',
                'results' => $results
            ]);
        } catch (Exception $e) {
            \Log::error('Bulk API Sync Failed', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Bulk sync failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Prepare data for API request
     */
    private function prepareApiData(Person $person): array
    {
        $wifeId = $person->getWifeId();
        $wifeName = $person->getWifeName();

        // Log للتشخيص
        \Log::info('API Sync - Person ID: ' . $person->id, [
            'wife_loaded' => $person->relationLoaded('wife'),
            'wifi_id' => $wifeId,
            'wifi_name' => $wifeName,
            'person_id_num' => $person->id_num
        ]);

        return [
            'pid' => $person->id_num ?? '',
            'fname' => $person->first_name ?? '',
            'sname' => $person->father_name ?? '',
            'tname' => $person->grandfather_name ?? '',
            'lname' => $person->family_name ?? '',
            'fcount' => $person->relatives_count ?? 0,
            'mob_1' => $person->phone ?? '',
            'mob_2' => $person->secondary_phone ?? '',
            'block' => $person->block?->aid_id ?? '',
            'note' => $person->notes ?? 'تم المزامنة تلقائياً',
            'wifi_id' => $wifeId,
            'wifi_name' => $wifeName,
            'num_mail' => null,
            'num_femail' => null,
            'f_num_liss_3' => $person->getChildrenUnder3Count(),
            'f_num_ill' => null,
            'f_num_sn' => null,
            'income' => $person->income_level ?? '1',
            'home_status' => $person->getHomeStatus(),
            'date_of_birth' => $person->dob?->format('Y-m-d') ?? '',
            'Original_governorate' => $person->original_governorate ?? '',
            'marital_status' => $person->social_status ?? '',
        ];
    }

    /**
     * Make API request
     */
    private function makeApiRequest(array $data): Response
    {
        $apiUrl = config('services.aid_api.url', 'https://aid.fajeryouth.org/public/API/convert/person/reg');
        $authToken = config('services.aid_api.token', 'aaa@aaa@aaa@rrr');

        // Log الـ data قبل الإرسال (خاصة wife)
        \Log::info('API Request Data', [
            'url' => $apiUrl,
            'wif_id_sent' => $data['wifi_id'] ?? 'missing',
            'wifi_name_sent' => $data['wifi_name'] ?? 'missing',
            'full_data' => $data  // احذف ده بعد الاختبار عشان الـ log ميكبرش
        ]);

        $response = Http::timeout(30)
            ->retry(3, 1000)
            ->withHeaders(['auth' => $authToken])
            ->asMultipart()
            ->post($apiUrl, $data);

        // Log الـ response
        \Log::info('API Response', [
            'status' => $response->status(),
            'body' => $response->body(),
            'json' => $response->json()
        ]);

        return $response;
    }

    /**
     * Update person sync status
     */
    private function updateSyncStatus(Person $person, Response $response): void
    {
        if ($response->successful()) {
            $person->update([
                'api_synced_at' => now(),
                'api_sync_status' => 'success',
                'api_sync_error' => null, // Clear previous errors
            ]);
        } else {
            $person->update([
                'api_sync_status' => 'failed',
                'api_sync_error' => 'HTTP ' . $response->status() . ': ' . $response->body(),
            ]);
        }
    }

    /**
     * Get coupon details for a person.
     *
     * @param Person $person
     * @return \Illuminate\Http\JsonResponse
     */
    public function coupons(Person $person)
    {
        $person->load(['projects.couponTypes']);

        $personCoupons = $person->projects
            ->map(function ($project) {
                $beneficiary = $project->pivot;

                return [
                    'project_name' => $project->name,
                    'project_id' => $project->id,
                    'total_quantity' => $beneficiary->quantity ?? 0,
                    'status' => $beneficiary->status ?? 'غير مستلم',
                    'delivery_date' => $beneficiary->delivery_date ?? null,
                    'coupon_types' => $project->couponTypes->map(function($type) {
                        return [
                            'id' => $type->id,
                            'name' => $type->name,
                            'quantity' => $type->pivot->quantity ?? 1,
                        ];
                    }),
                ];
            })
            ->filter(fn($p) => $p['total_quantity'] > 0);

        return response()->json([
            'person' => $person->getFullName(),
            'coupons' => $personCoupons->values(),
        ]);
    }
}