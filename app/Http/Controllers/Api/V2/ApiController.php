<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\AreaResponsible;
use App\Models\Block;
use App\Models\Person;
use App\Models\IncompletePerson;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiController extends Controller
{
    /**
     * التحقق من المستخدم عبر remember_token
     */
    private function authUser(Request $request)
    {
        $auth = $request->header('auth');
        if (!$auth) {
            return null;
        }
        return User::where('remember_token', $auth)->first();
    }

    private function authError()
    {
        return response()->json([
            'status' => false,
            'message' => 'يرجى تسجيل الدخول',
            'data' => [],
        ], 203);
    }

    /**
     * جلب معرفات المربعات (Blocks) لمسؤول المنطقة
     */
    private function getUserBlockIds($user)
    {
        if ($user->area_responsible_id) {
            $responsible = AreaResponsible::find($user->area_responsible_id);
            if ($responsible) {
                return $responsible->blocks->pluck('id')->toArray();
            }
        }
        return [];
    }

    /**
     * إحصائيات لوحة التحكم
     */
    public function index(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $data = ['reportAll' => []];
        $type = $user->type;

        if ($type == 'supervisor') {
            $blockIds = $this->getUserBlockIds($user);
            $blocks = Block::whereIn('id', $blockIds ?: [0])
                ->withCount(['people' => function($q) { $q->familyHead(); }])
                ->get();

            foreach ($blocks as $block) {
                $data['reportAll'][] = [
                    'count' => $block->people_count,
                    'name' => $block->title
                ];
            }

            // إحصائيات عامة للمشرف
            $data['reportAll'][] = ['count' => Person::whereIn('block_id', $blockIds)->where('gender', 'ذكر')->sum('relatives_count'), 'name' => 'إجمالي عدد الذكور'];
            $data['reportAll'][] = ['count' => Person::whereIn('block_id', $blockIds)->where('gender', 'أنثى')->sum('relatives_count'), 'name' => 'إجمالي عدد الإناث'];
            $data['reportAll'][] = ['count' => 0, 'name' => 'عدد الافراد اقل من 3 سنوات']; // يحتاج معالجة تاريخ ميلاد
            $data['reportAll'][] = ['count' => Person::whereIn('block_id', $blockIds)->where('has_condition', 1)->count(), 'name' => 'عدد الافراد ذوي الامراض المزمنه'];
            $data['reportAll'][] = ['count' => Person::whereIn('block_id', $blockIds)->whereNotNull('condition_description')->count(), 'name' => 'عدد الافراد ذوي الاحتياجات الخاصه'];

        } else if ($type == 'admin') {
            $personCount = Person::familyHead()->count();
            $data['reportAll'][] = ['count' => $personCount, 'name' => 'عدد الاسر المسجلة'];
            $data['reportAll'][] = ['count' => Block::count(), 'name' => 'عدد الأحياء'];
            
            $aidCount = DB::table('project_beneficiaries')->count();
            $data['reportAll'][] = ['count' => $aidCount, 'name' => 'عدد المشاريع المنفذة'];

            $avg = ($aidCount == 0 || $personCount == 0) ? 0 : round($aidCount / $personCount, 2);
            $data['reportAll'][] = ['count' => $avg, 'name' => 'متوسط عدد الاستفادة للاسرة'];

            $data['reportAll'][] = ['count' => Person::sum('relatives_count'), 'name' => 'إجمالي عدد الافراد'];
            $data['reportAll'][] = ['count' => Person::where('gender', 'ذكر')->sum('relatives_count'), 'name' => 'إجمالي عدد الذكور'];
            $data['reportAll'][] = ['count' => Person::where('gender', 'أنثى')->sum('relatives_count'), 'name' => 'إجمالي عدد الإناث'];
            $data['reportAll'][] = ['count' => 0, 'name' => 'عدد الافراد اقل من 3 سنوات'];
            $data['reportAll'][] = ['count' => Person::where('has_condition', 1)->count(), 'name' => 'عدد الافراد ذوي الامراض المزمنه'];
            $data['reportAll'][] = ['count' => Person::whereNotNull('condition_description')->where('condition_description', '!=', '')->count(), 'name' => 'عدد الافراد ذوي الاحتياجات الخاصه'];

            $blockCount = Block::count();
            $avgBlock = ($personCount == 0 || $blockCount == 0) ? 0 : round($personCount / $blockCount, 2);
            $data['reportAll'][] = ['count' => $avgBlock, 'name' => 'متوسط عدد الاسر في الحي'];
        } else {
            $data['reportAll'][] = ['count' => DB::table('project_beneficiaries')->where('status', 'مستلم')->count(), 'name' => 'عدد  الكابونات الموزعه'];
            $data['reportAll'][] = ['count' => Project::count(), 'name' => 'عدد  المشاريع الموزعه'];
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب العناصر بنجاح',
            'data' => $data,
        ], 200);
    }

    /**
     * جلب المساعدات الجديدة للأشخاص
     */
    public function get_new_persone_aid(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        if ($user->type == 'customer') {
            return response()->json(['status' => false, 'message' => 'لا توجد بيانات متاحة', 'count' => 0, 'data' => [], 'aid' => []], 200);
        }

        $row = $request->input('start', 0);
        $length = $request->input('length', 10);
        $lastAction = $request->input('last_action');

        $query = DB::table('project_beneficiaries')
            ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
            ->select('project_beneficiaries.*', 'projects.name as project_name', 'projects.description as project_note');

        if ($lastAction) {
            $query->where('project_beneficiaries.created_at', '>', $lastAction . " 00:00:00");
        }

        $data_aid = $query->offset($row)->limit($length)->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب البيانات بنجاح',
            'data' => $data_aid
        ], 200);
    }

    /**
     * جلب الأشخاص الجدد
     */
    public function get_new_persone(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $row = $request->input('start', 0);
        $length = $request->input('length', 10);
        $lastAction = $request->input('last_action');

        $query = Person::query()
            ->select(
                'persons.id as p_id', 'persons.id_num as p_pid', 'persons.first_name as p_fname',
                'persons.father_name as p_sname', 'persons.grandfather_name as p_tname', 'persons.family_name as p_lname',
                'persons.phone as p_mob_1', 'persons.relatives_count as p_f_num', 'persons.block_id as p_block_id',
                'persons.gender as p_sex', 'persons.person_status as p_f_status', 'persons.created_at as p_insert_date',
                'persons.updated_at as p_update_date', 'persons.relative_id as p_wifi_id', 'persons.relationship as p_wifi_name',
                'persons.has_condition as p_f_num_ill', 'persons.condition_description as p_f_num_sn',
                'persons.employment_status as p_income', 'persons.housing_damage_status as p_home_status',
                'persons.landmark as p_note', 'persons.area_responsible_id as p_region',
                'blocks.title as b_title', 'area_responsibles.name as region_title'
            )
            ->join('blocks', 'blocks.id', '=', 'persons.block_id')
            ->leftJoin('area_responsibles', 'area_responsibles.id', '=', 'persons.area_responsible_id');

        if ($user->type == 'supervisor') {
            $blockIds = $this->getUserBlockIds($user);
            $query->whereIn('persons.block_id', $blockIds ?: [0]);
        }

        if ($lastAction) {
            $query->where('persons.created_at', '>', $lastAction . " 00:00:00");
        }

        $count = $query->count();
        $res = $query->orderBy('persons.id', 'desc')->offset($row)->limit($length)->get();

        // معالجة صلة القرابة "زوجة"
        foreach ($res as $person) {
            if ($person->p_wifi_name == 'زوجة' || $person->p_wifi_name == 'wife') {
                $person->p_wifi_name = 'زوجة';
                // p_wifi_id هنا هو الـ relative_id (رقم هوية الزوج)
            }
        }

        $data_aid = [];
        if ($res->isNotEmpty()) {
            $p_ids = $res->pluck('p_id');
            $data_aid = DB::table('project_beneficiaries')
                ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
                ->whereIn('person_id', $p_ids)
                ->where('project_beneficiaries.status', 'مستلم')
                ->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب البيانات بنجاح',
            'count' => $count,
            'data' => $res,
            'aid' => $data_aid
        ], 200);
    }

    /**
     * جلب ثوابت النظام
     */
    public function constant_var(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $regions = AreaResponsible::all();
        $blocks = Block::all();

        return response()->json([
            'status' => true,
            'message' => 'ثوابت النظام',
            'data' => [
                'income' => [
                    ['id' => 'لا يعمل', 'title' => 'لا يعمل'],
                    ['id' => 'عامل', 'title' => 'عامل'],
                    ['id' => 'موظف', 'title' => 'موظف'],
                    ['id' => 'مزارع', 'title' => 'مزارع'],
                    ['id' => 'صياد', 'title' => 'صياد'],
                ],
                'home_status' => [
                    ['id' => 'سيئ', 'title' => 'سيئ'],
                    ['id' => 'جيد', 'title' => 'جيد'],
                    ['id' => 'ممتاز', 'title' => 'ممتاز'],
                    ['id' => 'هدم كلي', 'title' => 'هدم كلي'],
                    ['id' => 'هدم جزئي', 'title' => 'هدم جزئي'],
                ],
                'SOCIAL_STATUS' => [
                    ['id' => 'أعزب', 'title' => 'أعزب'],
                    ['id' => 'متزوج', 'title' => 'متزوج'],
                    ['id' => 'مطلق', 'title' => 'مطلق'],
                    ['id' => 'أرمل', 'title' => 'أرمل'],
                ],
                'SEX' => [
                    ['id' => 'ذكر', 'title' => 'ذكر'],
                    ['id' => 'أنثى', 'title' => 'أنثى'],
                ],
                'Block' => $blocks,
                'AreaManager' => $regions,
                'General' => $regions
            ]
        ], 201);
    }

    /**
     * تسجيل الدخول
     */
    public function Login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:5',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الدخول غير صحيحة',
                'data' => [],
            ], 201);
        }

/*
        if (!$user->email_verified_at) {
            return response()->json([
                'status' => false,
                'message' => 'الحساب غير مفعل',
                'data' => [],
            ], 201);
        }
*/

        $token = Str::random(32);
        $user->update(['remember_token' => $token]);

        return response()->json([
            'status' => true,
            'message' => 'تم تسجيل الدخول بنجاح',
            'data' => [
                'id' => $user->id,
                'hash' => $token,
                'name' => $user->name,
                'email' => $user->email,
                'permissions' => ($user->type == 'admin' ? 1 : ($user->type == 'supervisor' ? 3 : 4)),
                'block' => []
            ]
        ], 200);
    }

    /**
     * إضافة/تحديث مستفيد
     */
    public function create(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $pid = $request->input('pid');
        if (!$this->CheckId($pid)) {
            return response()->json(['status' => 201, 'messages' => 'رقم الهوية المدخل خطأ', 'data' => []], 200);
        }

        $data = [
            'id_num' => $pid,
            'first_name' => $request->input('fname'),
            'father_name' => $request->input('sname'),
            'grandfather_name' => $request->input('tname'),
            'family_name' => $request->input('lname'),
            'phone' => $request->input('mob_1'),
            'relatives_count' => $request->input('f_num'),
            'gender' => $request->input('sex'),
            'person_status' => $request->input('f_status'),
            'block_id' => $request->input('block'),
            'area_responsible_id' => $request->input('REGION'),
            'relative_id' => $request->input('wifi_id'),
            'relationship' => $request->input('wifi_name'),
            'has_condition' => $request->input('f_num_ill'),
            'condition_description' => $request->input('f_num_sn'),
            'employment_status' => $request->input('income'),
            'housing_damage_status' => $request->input('home_status'),
            'landmark' => $request->input('note'),
        ];

        $person = Person::updateOrCreate(['id_num' => $pid], $data);

        return response()->json([
            'status' => true,
            'messages' => 'تمت العملية بنجاح',
            'data' => [],
            'id' => $person->id
        ], 200);
    }

    /**
     * جلب الأشخاص المحدثين
     */
    public function get_update_persone(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $row = $request->input('start', 0);
        $length = $request->input('length', 10);
        $lastAction = $request->input('last_action');

        $query = Person::query()
            ->select(
                'persons.id as p_id', 'persons.id_num as p_pid', 'persons.first_name as p_fname',
                'persons.father_name as p_sname', 'persons.grandfather_name as p_tname', 'persons.family_name as p_lname',
                'persons.phone as p_mob_1', 'persons.relatives_count as p_f_num', 'persons.block_id as p_block_id',
                'persons.gender as p_sex', 'persons.person_status as p_f_status', 'persons.created_at as p_insert_date',
                'persons.updated_at as p_update_date', 'persons.relative_id as p_wifi_id', 'persons.relationship as p_wifi_name',
                'persons.has_condition as p_f_num_ill', 'persons.condition_description as p_f_num_sn',
                'persons.employment_status as p_income', 'persons.housing_damage_status as p_home_status',
                'persons.landmark as p_note', 'persons.area_responsible_id as p_region',
                'blocks.title as b_title'
            )
            ->join('blocks', 'blocks.id', '=', 'persons.block_id');

        if ($user->type == 'supervisor') {
            $blockIds = $this->getUserBlockIds($user);
            $query->whereIn('persons.block_id', $blockIds ?: [0]);
        }

        // الأشخاص المحدثين (الذين تم تعديلهم بعد 60 ثانية من الإنشاء وحدث بعد آخر مزامنة)
        $query->whereRaw('TIMESTAMPDIFF(SECOND, persons.created_at, persons.updated_at) >= 60');
        if ($lastAction) {
            $query->where('persons.updated_at', '>', $lastAction . " 00:00:00");
        }

        $count = $query->count();
        $res = $query->orderBy('persons.id', 'desc')->offset($row)->limit($length)->get();

        // معالجة صلة القرابة "زوجة"
        foreach ($res as $person) {
            if ($person->p_wifi_name == 'زوجة' || $person->p_wifi_name == 'wife') {
                $person->p_wifi_name = 'زوجة';
            }
        }

        $data_aid = [];
        if ($res->isNotEmpty()) {
            $p_ids = $res->pluck('p_id');
            $data_aid = DB::table('project_beneficiaries')
                ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
                ->whereIn('person_id', $p_ids)
                ->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب البيانات بنجاح',
            'count' => $count,
            'data' => $res,
            'aid' => $data_aid
        ], 200);
    }

    /**
     * مزامنة عامة للأشخاص (إضافة/تعديل/حذف)
     */
    public function get_persone(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $row = $request->input('start', 0);
        $length = $request->input('length', 10);
        $lastAction = $request->input('last_action');

        $query = Person::query()
            ->select(
                'persons.id as p_id', 'persons.id_num as p_pid', 'persons.first_name as p_fname',
                'persons.father_name as p_sname', 'persons.grandfather_name as p_tname', 'persons.family_name as p_lname',
                'persons.phone as p_mob_1', 'persons.relatives_count as p_f_num', 'persons.block_id as p_block_id',
                'persons.gender as p_sex', 'persons.person_status as p_f_status', 'persons.created_at as p_insert_date',
                'persons.updated_at as p_update_date', 'persons.relative_id as p_wifi_id', 'persons.relationship as p_wifi_name',
                'persons.has_condition as p_f_num_ill', 'persons.condition_description as p_f_num_sn',
                'persons.employment_status as p_income', 'persons.housing_damage_status as p_home_status',
                'persons.landmark as p_note', 'persons.area_responsible_id as p_region',
                'blocks.title as b_title'
            )
            ->join('blocks', 'blocks.id', '=', 'persons.block_id')
            ->withTrashed();

        if ($user->type == 'supervisor' || $user->type == 'mobile_worker') {
            $blockIds = $this->getUserBlockIds($user);
            $query->whereIn('persons.block_id', $blockIds ?: [0]);
        }

        if ($lastAction) {
            $query->where(function($q) use ($lastAction) {
                $q->where('persons.created_at', '>', $lastAction . " 00:00:00")
                  ->orWhere('persons.updated_at', '>', $lastAction . " 00:00:00")
                  ->orWhere('persons.deleted_at', '>', $lastAction . " 00:00:00");
            });
        }

        $count = $query->count();
        $res = $query->orderBy('persons.id', 'desc')->offset($row)->limit($length)->get();

        // معالجة صلة القرابة "زوجة"
        foreach ($res as $person) {
            if ($person->p_wifi_name == 'زوجة' || $person->p_wifi_name == 'wife') {
                $person->p_wifi_name = 'زوجة';
            }
        }

        $data_aid = [];
        if ($res->isNotEmpty()) {
            $p_ids = $res->pluck('p_id');
            $data_aid = DB::table('project_beneficiaries')
                ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
                ->whereIn('person_id', $p_ids)
                ->where('project_beneficiaries.status', 'مستلم')
                ->get();
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب البيانات بنجاح',
            'count' => $count,
            'data' => $res,
            'aid' => $data_aid
        ], 200);
    }

    /**
     * جلب المساعدات المستلمة حديثاً
     */
    public function get_new_aid(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $lastAction = $request->input('last_action');

        $data_aid = DB::table('project_beneficiaries')
            ->join('projects', 'projects.id', '=', 'project_beneficiaries.project_id')
            ->where('project_beneficiaries.status', 'مستلم')
            ->where('project_beneficiaries.delivery_date', '>', $lastAction . " 00:00:00")
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'تم جلب البيانات بنجاح',
            'data' => $data_aid,
        ], 200);
    }

    /**
     * جلب المربعات (Blocks) المتاحة للمستخدم
     */
    public function get_blocks(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        if ($user->type == 'customer') {
            return response()->json(['status' => false, 'message' => 'لا توجد بيانات متاحة', 'data' => []], 200);
        }

        $query = Block::query();
        if ($user->type == 'supervisor') {
            $ids = $this->getUserBlockIds($user);
            $query->whereIn('id', $ids ?: [0]);
        }

        return response()->json([
            'status' => true,
            'message' => 'تم جلب المربعات',
            'data' => $query->get(),
        ], 200);
    }

    /**
     * تحديث مستفيد
     */
    public function update(Request $request, $id)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $pid = $request->input('pid');
        if (!$this->CheckId($pid)) {
            return response()->json(['status' => 201, 'messages' => 'رقم الهوية المدخل خطأ', 'data' => []]);
        }

        $person = Person::find($id);
        if (!$person) {
            return response()->json(['status' => 201, 'messages' => 'المستفيد غير موجود', 'data' => []]);
        }

        $data = [
            'id_num' => $pid,
            'first_name' => $request->input('fname'),
            'father_name' => $request->input('sname'),
            'grandfather_name' => $request->input('tname'),
            'family_name' => $request->input('lname'),
            'phone' => $request->input('mob_1'),
            'relatives_count' => $request->input('f_num'),
            'gender' => $request->input('sex'),
            'person_status' => $request->input('f_status'),
            'block_id' => $request->input('block'),
            'area_responsible_id' => $request->input('REGION'),
            'relative_id' => $request->input('wifi_id'),
            'relationship' => $request->input('wifi_name'),
            'has_condition' => $request->input('f_num_ill'),
            'condition_description' => $request->input('f_num_sn'),
            'employment_status' => $request->input('income'),
            'housing_damage_status' => $request->input('home_status'),
            'landmark' => $request->input('note'),
        ];

        $person->update($data);

        return response()->json([
            'status' => true,
            'messages' => 'تم التعديل بنجاح',
            'data' => []
        ], 200);
    }

    /**
     * جلب المشاريع المتاحة للتوزيع
     */
    public function get_aid_manage(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $query = Project::select('projects.id as aid_manage_id', 'projects.name as aid_manage_title', 'projects.start_date as aid_manage_date', 'projects.description as aid_manage_note', 'projects.updated_at as updated_at')
            ->join('project_beneficiaries', 'project_beneficiaries.project_id', '=', 'projects.id')
            ->where('project_beneficiaries.status', '!=', 'مستلم')
            ->groupBy('projects.id', 'projects.name', 'projects.start_date', 'projects.description', 'projects.updated_at')
            ->orderBy('projects.id', 'desc');

        if ($user->type == 'mobile_worker' || $user->type == 'supervisor') {
            $query->where('project_beneficiaries.user_id', $user->id);
        }

        $res = $query->limit(30)->get();

        return response()->json([
            'status' => true,
            'message' => 'تم تحميل البيانات بنجاح',
            'data' => $res
        ], 200);
    }

    /**
     * جلب المستفيدين من مشروع معين
     */
    public function get_aid_manage_by_id(Request $request, $id)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $project = Project::find($id);
        if (!$project) {
            return response()->json(['status' => false, 'message' => 'المشروع غير موجود', 'data' => []], 203);
        }

        $beneficiaries = DB::table('project_beneficiaries')
            ->leftJoin('persons', 'persons.id', '=', 'project_beneficiaries.person_id')
            ->leftJoin('incomplete_people', 'incomplete_people.id', '=', 'project_beneficiaries.incomplete_person_id')
            ->select(
                'project_beneficiaries.id as aid_person_id', 
                'project_beneficiaries.quantity as aid_person_quantity',
                'project_beneficiaries.status as aid_person_status_rec', 
                DB::raw('COALESCE(persons.first_name, incomplete_people.first_name) as person_fname'),
                DB::raw('COALESCE(persons.father_name, incomplete_people.father_name) as person_sname'),
                DB::raw('COALESCE(persons.grandfather_name, incomplete_people.grandfather_name) as person_tname'),
                DB::raw('COALESCE(persons.family_name, incomplete_people.family_name) as person_lname'),
                DB::raw('COALESCE(persons.phone, incomplete_people.phone) as person_mob_1'), 
                DB::raw('COALESCE(persons.id_num, incomplete_people.id_num) as person_pid'),
                DB::raw("CASE WHEN persons.id IS NOT NULL THEN 'regular' ELSE 'incomplete' END as person_type")
            )
            ->where('project_beneficiaries.project_id', $id)
            ->where('project_beneficiaries.status', '!=', 'مستلم');

        if ($user->type == 'mobile_worker' || $user->type == 'supervisor') {
            $beneficiaries->where('project_beneficiaries.user_id', $user->id);
        }

        if ($request->filled('last_id')) {
            $beneficiaries->where('project_beneficiaries.id', '>', $request->last_id);
        }

        $beneficiaries = $beneficiaries->orderBy('person_fname')->get();

        return response()->json([
            'status' => $user->id,
            'message' => 'تم تحميل البيانات بنجاح',
            'data' => [
                'aid_manage_id' => $project->id,
                'aid_manage_title' => $project->name,
                'aid_manage_date' => $project->start_date,
                'aid_manage_note' => $project->description
            ],
            'person' => $beneficiaries
        ], 200);
    }

    /**
     * تسجيل استلام المساعدات
     */
    public function set_aid_manage_receive(Request $request)
    {
        $user = $this->authUser($request);
        if (!$user) return $this->authError();

        $dataRaw = $request->input('data');
        $data = is_string($dataRaw) ? json_decode($dataRaw) : $dataRaw;

        if (empty($data)) {
            return response()->json(['status' => false, 'message' => 'البيانات المرسلة خطأ', 'data' => []], 203);
        }

        // Normalize each item to be an object to maintain compatibility with the code below (e.g. $item->id)
        if (is_array($data)) {
            $data = json_decode(json_encode($data));
        }

        $batchTime = now();
        // Capture at root or from variations
        $rootCapturedQty = $request->input('captured_qty') ?? $request->input('point_qty');
        $externalNotes = $request->input('external_notes') ?? $request->input('notes_external');

        foreach ($data as $item) {
            $beneficiaryId = $item->id;
            $beneficiary = DB::table('project_beneficiaries')->where('id', $beneficiaryId)->first();
            if (!$beneficiary) continue;

            // الأولوية للكمية المحرزة لكل فرد، وإذا لم توجد نستخدم الكمية العامة للدفعة
            $itemCapturedQty = isset($item->captured_qty) ? (int)$item->captured_qty : null;
            $itemQty = ($itemCapturedQty && $itemCapturedQty > 0) ? $itemCapturedQty : ($rootCapturedQty ?: null);
            
            // فحص وجود سجل استلام سابق (مستلم) لنفس الشخص في نفس المشروع
            $existing = DB::table('project_beneficiaries')
                ->where('project_id', $beneficiary->project_id)
                ->where('id', '!=', $beneficiaryId)
                ->where(function($q) use ($beneficiary) {
                    if ($beneficiary->person_id) $q->where('person_id', $beneficiary->person_id);
                    else $q->where('incomplete_person_id', $beneficiary->incomplete_person_id);
                })
                ->where('status', 'مستلم')
                ->orderBy('id', 'asc') // نختار السجل الأول المضاف قديماً
                ->first();

            if ($existing) {
                // حالة دمج الاستلام الثاني في السجل الأول
                $finalQty = (int)$existing->quantity + (int)($itemQty ?: $beneficiary->quantity);
                $newNotes = trim($existing->notes . ' | ' . ($item->title ?? ''));
                if (!str_contains($newNotes, 'إعادة تكليف')) {
                    $newNotes .= ' (إعادة تكليف)';
                }

                DB::table('project_beneficiaries')
                    ->where('id', $existing->id)
                    ->update([
                        'quantity' => $finalQty,
                        'notes' => $newNotes,
                        'captured_qty' => $finalQty,
                        'sub_warehouse_id' => $beneficiary->sub_warehouse_id, // الحفاظ على المخزن الذي تم فيه الاستلام الفعلي
                        'delivery_date' => $batchTime, // تحديث تاريخ الاستلام ليكون الأحدث
                        'updated_at' => $batchTime
                    ]);
                
                // حذف السجل الحالي لأنه تم دمجه
                DB::table('project_beneficiaries')->where('id', $beneficiaryId)->delete();
            } else {
                // استلام طبيعي لأول مرة
                $updateData = [
                    'status' => 'مستلم',
                    'delivery_date' => $batchTime,
                    'notes' => $item->title ?? '',
                    'captured_qty' => $itemQty,
                    'external_notes' => $externalNotes,
                    'updated_at' => $batchTime
                ];

                if ($itemQty && $itemQty > 0) {
                    $updateData['quantity'] = (int) $itemQty;
                } elseif (isset($item->aid_person_quantity)) {
                    $updateData['quantity'] = (int) $item->aid_person_quantity;
                } elseif (isset($item->quantity)) {
                    $updateData['quantity'] = (int) $item->quantity;
                }

                DB::table('project_beneficiaries')
                    ->where('id', $beneficiaryId)
                    ->update($updateData);
            }
        }

        return response()->json([
            'status' => true,
            'message' => 'تم معالجة البيانات',
            'data' => count($data),
        ], 200);
    }

    public function logout(Request $request)
    {
        $auth = $request->header('auth');
        $user = User::where('remember_token', $auth)->first();
        if ($user) {
            $user->update(['remember_token' => null]);
            return response()->json(['status' => true, 'message' => 'تم تسجيل الخروج بنجاح', 'data' => []], 200);
        }
        return response()->json(['status' => false, 'message' => 'هذا المستخدم غير مسجل بالنظام', 'data' => []], 201);
    }

    /**
     * خوارزمية التحقق من رقم الهوية
     */
    private function CheckId($idno)
    {
        $idno = trim($idno);
        if (strlen($idno) !== 9 || !is_numeric($idno)) return false;

        $MultID = [1, 2, 1, 2, 1, 2, 1, 2];
        $SumID = 0;

        for ($i = 0; $i < 8; $i++) {
            $x = $MultID[$i] * (int)$idno[$i];
            if ($x > 9) {
                $x = (int)strval($x)[0] + (int)strval($x)[1];
            }
            $SumID += $x;
        }

        $checkDigit = (10 - ($SumID % 10)) % 10;
        return $checkDigit === (int)$idno[8];
    }
}
