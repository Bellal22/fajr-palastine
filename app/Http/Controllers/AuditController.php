<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Exports\AuditExport;
use Maatwebsite\Excel\Facades\Excel;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        if (!auth()->user()?->isAdmin() && !auth()->user()?->hasPermission('manage.admins') && !auth()->user()?->hasPermission('manage.audit')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        set_time_limit(300); // رفع وقت التنفيذ للتعامل مع البيانات الضخمة
        
        $areaId = $request->get('area_responsible_id');
        $areaIds = [];
        if (is_array($areaId)) {
            $areaIds = $areaId;
        } elseif (is_string($areaId)) {
            $areaIds = array_filter(explode(',', $areaId));
        }
        $areaIds = array_map('intval', $areaIds);

        $blockId = $request->get('block_id');
        $categoryFilter = $request->get('category');

        $settings = [
            'age_threshold' => $request->filled('age_threshold') ? (int) $request->get('age_threshold') : 60,
            'infant_score' => $request->filled('infant_score') ? (int) $request->get('infant_score') : 18,
            'child_score' => $request->filled('child_score') ? (int) $request->get('child_score') : 10,
            'tent_score' => $request->filled('tent_score') ? (int) $request->get('tent_score') : 15,
            'destroyed_score' => $request->filled('destroyed_score') ? (int) $request->get('destroyed_score') : 20,
            'cat_a_min' => $request->filled('cat_a_min') ? (int) $request->get('cat_a_min') : 85,
            'cat_b_min' => $request->filled('cat_b_min') ? (int) $request->get('cat_b_min') : 60,
            'cat_c_min' => $request->filled('cat_c_min') ? (int) $request->get('cat_c_min') : 35,
            'custom_keywords' => $request->get('custom_keywords', ''),
            'force_incapacitated' => $request->has('area_responsible_id') ? ($request->get('force_incapacitated') === '1') : true,
            'check_adult_son' => $request->has('area_responsible_id') ? ($request->get('check_adult_son') === '1') : true
        ];

        if (empty($areaIds)) {
            return view('admin.audit.index', [
                'title' => 'نظام التدقيق الذكي',
                'families' => collect([]),
                'outliers' => (object)['over' => [], 'avg' => 0],
                'stats' => ['total' => 0, 'A' => 0, 'B' => 0, 'C' => 0, 'D' => 0],
                'pagination' => null,
                'areas' => \App\Models\AreaResponsible::all(),
                'blocks' => collect([]),
                'message' => 'يرجى اختيار المنطقة لبدء عملية التدقيق والتحليل'
            ]);
        }

        // 1. استعلام القاعدة الأساسي
        $headsQuery = Person::whereNull('relative_id')->where(function($query) use ($areaIds) {
            $query->whereIn('area_responsible_id', $areaIds)
                  ->orWhereHas('block', function($q) use ($areaIds) {
                      $q->whereIn('area_responsible_id', $areaIds);
                  });
        });
        if ($blockId) $headsQuery->where('block_id', $blockId);
        
        if ($request->has('unserved')) {
            $headsQuery->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('project_beneficiaries')
                      ->whereColumn('project_beneficiaries.person_id', 'persons.id')
                      ->where('status', 'مستلم');
            });
        }

        // 2. حساب إحصائيات سريعة لكامل المنطقة عبر SQL (لتجنب التايم آوت)
        $stats = $this->getFastStats($areaIds, $blockId, $request->has('unserved'), $settings);

        // 3. التعامل مع التصدير الكامل (يحتاج وقت أطول ومعالجة كاملة)
        if ($request->has('export')) {
            set_time_limit(900); 
            ini_set('memory_limit', '1024M');
            
            // استخدام Query سريع جداً بدون Eloquent
            $headsQueryFast = DB::table('persons as p')
                ->leftJoin('area_responsibles as a', 'p.area_responsible_id', '=', 'a.id')
                ->leftJoin('blocks as b', 'p.block_id', '=', 'b.id')
                ->whereNull('p.relative_id')
                ->where(function($query) use ($areaIds) {
                    $query->whereIn('p.area_responsible_id', $areaIds)
                          ->orWhereIn('b.area_responsible_id', $areaIds);
                })
                ->select('p.*', 'a.name as area_name', 'b.name as block_name');

            if ($blockId) $headsQueryFast->where('p.block_id', $blockId);
            
            if ($request->has('unserved')) {
                $headsQueryFast->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('project_beneficiaries')
                          ->whereColumn('project_beneficiaries.person_id', 'p.id')
                          ->where('status', 'مستلم');
                });
            }
            
            $allHeads = $headsQueryFast->get();
            $allHeadIds = $allHeads->pluck('id_num')->toArray();
            
            $allMembers = DB::table('persons')->whereIn('relative_id', $allHeadIds)->get();
            $allScored = $this->calculateScores($allHeads, $allMembers, $settings);
            
            $exportData = $categoryFilter ? $allScored->where('category', $categoryFilter) : $allScored;
            $fileName = $request->has('unserved') ? 'unserved_audit_report.xlsx' : 'audit_report_full.xlsx';
            return Excel::download(new AuditExport($exportData), $fileName);
        }

        // 4. العرض في الجدول (باجينيت ذكي)
        if ($categoryFilter) {
            set_time_limit(300);
            
            // تحسين: الفلترة المبدئية عبر SQL لتقليل عدد السجلات التي تحتاج معالجة ثقيلة
            $preFilterKeywords = [
                'A' => 'سرطان|فشل كلوي|غسيل|قلب مفتوح|كيماوي|إشعاعي',
                'B' => 'شلل|بتر|جلطة|إصابة حرب|بلاتين',
                'C' => 'إعاقة|ديسك|غضروف|ثقب|أكسجين|صرع|توحد'
            ];

            $headsQueryFast = DB::table('persons as p')
                ->leftJoin('area_responsibles as a', 'p.area_responsible_id', '=', 'a.id')
                ->leftJoin('blocks as b', 'p.block_id', '=', 'b.id')
                ->whereNull('p.relative_id')
                ->where(function($query) use ($areaIds) {
                    $query->whereIn('p.area_responsible_id', $areaIds)
                          ->orWhereIn('b.area_responsible_id', $areaIds);
                })
                ->select('p.*', 'a.name as area_name', 'b.name as block_name');

            if ($blockId) $headsQueryFast->where('p.block_id', $blockId);

            if ($request->has('unserved')) {
                $headsQueryFast->whereNotExists(function($query) {
                    $query->select(DB::raw(1))
                          ->from('project_beneficiaries')
                          ->whereColumn('project_beneficiaries.person_id', 'p.id')
                          ->where('status', 'مستلم');
                });
            }

            // إذا كانت الفئة A أو B، نفلتر بالكلمات لتقليل الـ 50 ألف سجل إلى بضعة آلاف
            if (isset($preFilterKeywords[$categoryFilter])) {
                $headsQueryFast->where('p.condition_description', 'REGEXP', $preFilterKeywords[$categoryFilter]);
            }

            $allHeads = $headsQueryFast->get();
            $headIds = $allHeads->pluck('id_num')->toArray();
            
            // جلب الأفراد كـ arrays لسرعة فائقة
            $allMembers = DB::table('persons')->whereIn('relative_id', $headIds)->get();
            
            $scored = $this->calculateScores($allHeads, $allMembers, $settings)->where('category', $categoryFilter);
            
            $perPage = 50;
            $currentPage = $request->get('page', 1);
            $pagedData = $scored->slice(($currentPage - 1) * $perPage, $perPage)->all();
            $pagination = new \Illuminate\Pagination\LengthAwarePaginator(
                $pagedData, $scored->count(), $perPage, $currentPage, 
                ['path' => $request->url(), 'query' => $request->query()]
            );
        } else {
            // الوضع العادي: باجينيت من الداتابيز مباشرة لسرعة فائقة
            $pagination = $headsQuery->orderBy('id', 'desc')->paginate(50);
            $headIds = $pagination->pluck('id_num')->toArray();
            $members = DB::table('persons')->whereIn('relative_id', $headIds)->get();
            
            // تحسين العرض العادي بجلب الأسماء المفقودة
            $heads = $pagination->getCollection();
            foreach($heads as $h) {
                $h->area_name = $h->areaResponsible->name ?? '';
                $h->block_name = $h->block->name ?? '';
            }
            
            $scored = $this->calculateScores($heads, $members, $settings);
            $pagination->setCollection($scored);
        }

        return view('admin.audit.index', [
            'title' => 'نظام التدقيق الذكي',
            'families' => $pagination,
            'stats' => $stats,
            'pagination' => $pagination,
            'areas' => \App\Models\AreaResponsible::all(),
            'blocks' => \App\Models\Block::whereIn('area_responsible_id', $areaIds)->get()
        ]);
    }

    private function getFastStats($areaIds, $blockId, $unserved = false, $settings = null)
    {
        $base = DB::table('persons as p')
            ->leftJoin('blocks as b', 'p.block_id', '=', 'b.id')
            ->leftJoin('area_responsibles as a', 'p.area_responsible_id', '=', 'a.id')
            ->whereNull('p.relative_id')
            ->where(function($query) use ($areaIds) {
                $query->whereIn('p.area_responsible_id', $areaIds)
                      ->orWhereIn('b.area_responsible_id', $areaIds);
            });
            
        if ($blockId) $base->where('p.block_id', $blockId);

        if ($unserved) {
            $base->whereNotExists(function($query) {
                $query->select(DB::raw(1))
                      ->from('project_beneficiaries')
                      ->whereColumn('project_beneficiaries.person_id', 'p.id')
                      ->where('status', 'مستلم');
            });
        }

        $allHeads = $base->select('p.*', 'a.name as area_name', 'b.name as block_name')->get();
        $headIds = $allHeads->pluck('id_num')->toArray();
        
        $allMembers = DB::table('persons')
            ->whereIn('relative_id', $headIds)
            ->get();

        $scored = $this->calculateScores($allHeads, $allMembers, $settings);

        return [
            'total' => $scored->count(),
            'A' => $scored->where('category', 'A')->count(),
            'B' => $scored->where('category', 'B')->count(),
            'C' => $scored->where('category', 'C')->count(),
            'D' => $scored->where('category', 'D')->count(),
        ];
    }


    private function getFilteredStats($areaId, $blockId)
    {
        $q = Person::whereNull('relative_id');
        if ($areaId) {
            $q->where(function($query) use ($areaId) {
                $query->where('area_responsible_id', $areaId)
                      ->orWhereHas('block', function($q2) use ($areaId) {
                          $q2->where('area_responsible_id', $areaId);
                      });
            });
        }
        if ($blockId) $q->where('block_id', $blockId);

        return [
            'total' => $q->count(),
            'very_critical' => (clone $q)->where('condition_description', 'REGEXP', 'سرطان|ورم|أورام|فشل كلوي|كلى')->count(),
            'critical' => (clone $q)->where('condition_description', 'REGEXP', 'مصاب|إصابة|بتر|شلل')->count(),
            'avg_score' => 'نطاق محدد'
        ];
    }

    private function getGlobalStats()
    {
        return [
            'total' => Person::whereNull('relative_id')->count(),
            'very_critical' => Person::whereNull('relative_id')
                ->where(function($q) {
                    $q->where('condition_description', 'REGEXP', 'سرطان|ورم|أورام|فشل كلوي|كلى')
                      ->orWhere('family_name', 'REGEXP', 'سرطان|ورم|أورام|فشل كلوي|كلى');
                })->count(),
            'critical' => Person::whereNull('relative_id')
                ->where(function($q) {
                    $q->where('condition_description', 'REGEXP', 'مصاب|إصابة|بتر|شلل')
                      ->orWhere('family_name', 'REGEXP', 'مصاب|إصابة|بتر|شلل');
                })->count(),
            'avg_score' => 'شامل'
        ];
    }

    private static $localHealthCache = [];

    private function getHealthScore($description)
    {
        if (empty($description)) return 0;
        
        $description = mb_strtolower(trim($description));

        if (isset(self::$localHealthCache[$description])) {
            return self::$localHealthCache[$description];
        }

        $keywords = [
            'critical' => ['سرطان', 'كانسر', 'فشل', 'غسيل', 'قلب', 'شرايين', 'كبد', 'لوكيميا', 'كيماوي', 'إشعاعي', 'تضخم'],
            'serious' => ['شلل', 'بتر', 'جلطة', 'إصابة حرب', 'نزيف', 'كسر', 'بلاتين', 'ضمور', 'تصلب'],
            'moderate' => ['إعاقة', 'ديسك', 'غضروف', 'صرع', 'توحد', 'متلازمة', 'ثقب', 'قدم سكرية'],
            'stable' => ['سكري', 'ضغط', 'شظايا', 'ربو', 'حساسية', 'شيخوخة', 'كبر سن', 'عرق نسا', 'سوء تغذية']
        ];

        foreach ($keywords['critical'] as $w) {
            if (mb_strpos($description, $w) !== false) {
                return self::$localHealthCache[$description] = 80;
            }
        }
        foreach ($keywords['serious'] as $w) {
            if (mb_strpos($description, $w) !== false) {
                return self::$localHealthCache[$description] = 55;
            }
        }
        foreach ($keywords['moderate'] as $w) {
            if (mb_strpos($description, $w) !== false) {
                return self::$localHealthCache[$description] = 30;
            }
        }
        foreach ($keywords['stable'] as $w) {
            if (mb_strpos($description, $w) !== false) {
                return self::$localHealthCache[$description] = 10;
            }
        }

        $cacheKey = 'med_score_' . md5($description);
        $score = \Illuminate\Support\Facades\Cache::remember($cacheKey, 604800, function() use ($description) {
            return \App\Audit\MedicalLibrary::calculateHealthScore($description);
        });

        return self::$localHealthCache[$description] = $score;
    }

    private function calculateScores($heads, $allPersons, $settings = null)
    {
        if (!$settings) {
            $settings = [
                'age_threshold' => 60,
                'infant_score' => 18,
                'child_score' => 10,
                'tent_score' => 15,
                'destroyed_score' => 20,
                'cat_a_min' => 85,
                'cat_b_min' => 60,
                'cat_c_min' => 35,
                'custom_keywords' => '',
                'force_incapacitated' => true,
                'check_adult_son' => true
            ];
        }

        $results = [];
        
        // تحسين الأداء: تجميع الأفراد حسب المعيل مرة واحدة بدلاً من البحث المتكرر
        $membersGrouped = $allPersons->groupBy('relative_id');

        foreach ($heads as $head) {
            $members = $membersGrouped->get($head->id_num, collect([]));
            $score = 0;
            $reasons = [];

            // 1. الأولوية القصوى: المعيلين (رب الأسرة والزوج/الزوجة)
            $primaryHeads = collect([$head]);
            $spouse = $members->first(fn($m) => in_array($m->relationship, ['زوج', 'زوجة', 'husband', 'wife', 'Wife', 'Husband']));
            if ($spouse) $primaryHeads->push($spouse);

            $hasHealthyParent = false;
            $isParentIncapacitated = false;

            foreach ($primaryHeads as $p) {
                $cond = mb_strtolower($p->condition_description);
                $pHealth = $this->getHealthScore($cond);
                
                // شبكة أمان طبية: التعرف على الأمراض الخطيرة حتى لو لم تكن في المكتبة
                $hasCriticalDisease = (mb_strpos($cond, 'كانسر') !== false || mb_strpos($cond, 'سرطان') !== false || 
                                       mb_strpos($cond, 'شرايين') !== false || mb_strpos($cond, 'كبد') !== false || 
                                       mb_strpos($cond, 'قلب') !== false || mb_strpos($cond, 'فشل') !== false ||
                                       mb_strpos($cond, 'ورم') !== false || mb_strpos($cond, 'أورام') !== false);

                if (!empty($settings['custom_keywords'])) {
                    $customWords = array_filter(explode('|', $settings['custom_keywords']));
                    foreach ($customWords as $cw) {
                        if (mb_strpos($cond, trim($cw)) !== false) {
                            $hasCriticalDisease = true;
                            break;
                        }
                    }
                }

                // إضافة معيار السن: إذا كان الوالد فوق السن المحدد نعتبره غير قادر على الإعالة
                $isElderly = false;
                try {
                    if (!empty($p->dob)) {
                        $age = \Carbon\Carbon::parse($p->dob)->age;
                        if ($age == 0 && strlen($p->dob) >= 4) { $age = date('Y') - substr($p->dob, 0, 4); }
                        if ($age >= $settings['age_threshold']) $isElderly = true;
                    }
                } catch(\Exception $e) {}

                if ($pHealth > 0 || $isElderly || $hasCriticalDisease) {
                    $score += ($pHealth > 0 ? $pHealth : ($hasCriticalDisease ? 55 : 35)); 
                    $label = (in_array($p->relationship, ['زوجة', 'wife', 'Wife']) || $p->gender == 'أنثى') ? 'الأم' : 'الأب';
                    $reasons[] = $isElderly ? "كبر سن $label (فوق {$settings['age_threshold']})" : "إصابة $label (" . ($hasCriticalDisease ? 'حالة حرجة' : 'معيل أساسي') . ")";
                    $isParentIncapacitated = true;
                } else {
                    $hasHealthyParent = true;
                }
            }

            // البحث عن أكبر ابن ذكر عمره 18 سنة وفوق كمعيل بديل
            $eldestSon = null;
            $eldestSonAge = 0;
            foreach ($members as $member) {
                $rel = mb_strtolower(trim($member->relationship));
                $gen = mb_strtolower(trim($member->gender));
                
                $isSon = ($rel === 'ابن' || $rel === 'son');
                $isMale = ($gen === 'ذكر' || $gen === 'male');
                
                if ($isSon && $isMale && $member->dob) {
                    try {
                        $dob_str = (string)$member->dob;
                        $mAge = 0;
                        if (strlen($dob_str) >= 4) {
                            $year = substr($dob_str, 0, 4);
                            if (is_numeric($year)) $mAge = date('Y') - $year;
                        }
                        if ($mAge >= 18 && $mAge > $eldestSonAge) {
                            $eldestSon = $member;
                            $eldestSonAge = $mAge;
                        }
                    } catch(\Exception $e) {}
                }
            }

            $hasHealthyProvider = $hasHealthyParent;
            $eldestSonIncapacitated = false;

            if ($settings['check_adult_son'] && $eldestSon && !$hasHealthyParent) {
                // إذا لم يكن هناك والد سليم، نفحص حالة الابن البالغ كمعيل بديل
                $sonCond = mb_strtolower($eldestSon->condition_description);
                $sonHealth = $this->getHealthScore($sonCond);
                
                $sonCritical = (mb_strpos($sonCond, 'كانسر') !== false || mb_strpos($sonCond, 'سرطان') !== false || 
                                mb_strpos($sonCond, 'شرايين') !== false || mb_strpos($sonCond, 'كبد') !== false || 
                                mb_strpos($sonCond, 'قلب') !== false || mb_strpos($sonCond, 'فشل') !== false ||
                                mb_strpos($sonCond, 'ورم') !== false || mb_strpos($sonCond, 'أورام') !== false);
                                
                if (!empty($settings['custom_keywords'])) {
                    $customWords = array_filter(explode('|', $settings['custom_keywords']));
                    foreach ($customWords as $cw) {
                        if (mb_strpos($sonCond, trim($cw)) !== false) {
                            $sonCritical = true;
                            break;
                        }
                    }
                }

                if ($sonHealth > 0 || $sonCritical) {
                    $eldestSonIncapacitated = true;
                    $score += ($sonHealth > 0 ? $sonHealth : 35);
                    $reasons[] = "عجز الابن المعيل البديل (" . ($sonCritical ? 'حالة حرجة' : 'مرض/إصابة') . ")";
                } else {
                    $hasHealthyProvider = true;
                    $reasons[] = "وجود ابن بالغ معيل (ذكر {$eldestSonAge} سنة)";
                }
            }

            // 2. معالجة الحالات التي ليس لها معيل سليم أو بها والد عاجز وبدون ابن بالغ معيل
            $noProvider = ($isParentIncapacitated || !$hasHealthyParent) && !$hasHealthyProvider;

            if ($settings['force_incapacitated'] && $noProvider) {
                $score = max($score, $settings['cat_a_min']); // فرض الفئة A فوراً لأي أسرة بها والد عاجز أو بدون معيل وبدون ابن معيل بديل
                $reasons[] = "الأسرة تحت الأولوية القصوى (عجز الوالدين وبلا معيل بديل)";
            }

            // 3. احتياجات الأطفال (بامبرز وحليب)
            $infantsCount = 0;
            foreach ($members as $member) {
                if ($member->dob) {
                    try {
                        $dob_str = (string)$member->dob;
                        $age = 0;
                        if (strlen($dob_str) >= 4) {
                            $year = substr($dob_str, 0, 4);
                            if (is_numeric($year)) $age = date('Y') - $year;
                        }
                        if ($age < 2) { 
                            $infantsCount++; 
                            $score += $settings['infant_score'];
                        } elseif ($age < 5) {
                            $score += $settings['child_score'];
                        }
                    } catch(\Exception $e) {}
                }
            }
            if ($infantsCount > 0) $reasons[] = "احتياج حليب وبامبرز ($infantsCount)";

            // 4. الحالة الصحية لباقي أفراد العائلة
            foreach ($members as $other) {
                $otherHealth = $this->getHealthScore($other->condition_description);
                if ($otherHealth > 0) {
                    $score += ($otherHealth * 0.3);
                    $reasons[] = "إصابة فرد تابع";
                }
            }

            // 5. السكن والوضع الاقتصادي
            if (in_array($head->housing_type, ['خيمة', 'مركز إيواء'])) $score += $settings['tent_score'];
            if ($head->housing_damage_status == 'مدمر كلي') $score += $settings['destroyed_score'];

            // 6. التميز الفريد لمنع التشابه
            $uniqueFactor = ($head->id_num % 1000) / 10000;
            $finalScore = min($score + $uniqueFactor, 100);

            if ($finalScore >= $settings['cat_a_min']) { $category = "A"; $badge = "danger"; }
            elseif ($finalScore >= $settings['cat_b_min']) { $category = "B"; $badge = "warning"; }
            elseif ($finalScore >= $settings['cat_c_min']) { $category = "C"; $badge = "info"; }
            else { $category = "D"; $badge = "success"; }

            $results[] = (object)[
                'id' => $head->id,
                'id_num' => $head->id_num,
                'name' => $head->first_name . ' ' . $head->family_name,
                'full_name' => implode(' ', array_filter([$head->first_name, $head->father_name, $head->grandfather_name, $head->family_name])),
                'phone' => $head->phone,
                'members_count' => $members->count() + 1,
                'score' => round($finalScore, 2),
                'category' => $category,
                'badge' => $badge,
                'reasons' => implode(' • ', array_unique($reasons)) ?: 'حالة مستقرة',
                'address' => ($head->area_name ?? '') . ' / ' . ($head->block_name ?? ''),
                'last_delivery' => null
            ];
        }

        return collect($results)->sortByDesc('score');
    }

    public function outliers(Request $request)
    {
        if (!auth()->user()?->isAdmin() && !auth()->user()?->hasPermission('manage.admins') && !auth()->user()?->hasPermission('manage.audit')) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        set_time_limit(900); // رفع وقت التنفيذ لتفادي الـ 504 Gateway Time-out
        ini_set('memory_limit', '1024M');

        $areaId = $request->get('area_responsible_id');
        $blockId = $request->get('block_id');
        
        $areaIds = [];
        if (is_array($areaId)) {
            $areaIds = $areaId;
        } elseif (is_string($areaId)) {
            $areaIds = array_filter(explode(',', $areaId));
        }
        $areaIds = array_map('intval', $areaIds);
        
        if (empty($areaIds)) {
            return view('admin.audit.outliers', [
                'title' => 'تحليل القيم الشاذة والمخاطر',
                'overServed' => collect([]),
                'neglected' => collect([]),
                'avg' => 0,
                'areas' => \App\Models\AreaResponsible::all(),
                'blocks' => collect([]),
                'message' => 'يرجى اختيار مسؤولي المناطق لبدء تحليل القيم الشاذة'
            ]);
        }

        // 1. Analyze Distributions for THIS Area/Block
        $distQuery = DB::table('project_beneficiaries as pb')
            ->join('persons as p', 'pb.person_id', '=', 'p.id')
            ->leftJoin('blocks as b', 'p.block_id', '=', 'b.id')
            ->where(function($query) use ($areaIds) {
                $query->whereIn('p.area_responsible_id', $areaIds)
                      ->orWhereIn('b.area_responsible_id', $areaIds);
            });
            
        if ($blockId) $distQuery->where('p.block_id', $blockId);
        
        $distributionStats = $distQuery->where('pb.status', 'مستلم')
            ->select('pb.person_id', 
                DB::raw('count(*) as times'), 
                DB::raw('sum(pb.quantity) as total_qty'),
                DB::raw('max(pb.delivery_date) as last_delivery')
            )
            ->groupBy('pb.person_id')
            ->get();

        $avg = $distributionStats->avg('total_qty') ?: 0;
        $stdDev = $this->stdDev($distributionStats->pluck('total_qty')->toArray() ?: [0]);

        // 2. Score only the served families to find Over-served
        $servedHeadIds = $distributionStats->pluck('person_id')->toArray();
        $servedHeads = Person::whereIn('id', $servedHeadIds)->get();
        $servedHeadIdNums = $servedHeads->pluck('id_num')->toArray();
        $servedMembers = Person::whereIn('relative_id', $servedHeadIdNums)->get();
        $scoredServedFamilies = $this->calculateScores($servedHeads, $servedMembers);

        // 3. Find Over-served (Upper Outliers)
        $overServed = $distributionStats->filter(function($s) use ($avg, $stdDev) {
            $z = $stdDev > 0 ? ($s->total_qty - $avg) / $stdDev : 0;
            return $z > 2.5; // Very aggressive over-serving
        })->map(function($s) use ($scoredServedFamilies) {
            $f = $scoredServedFamilies->firstWhere('id', $s->person_id);
            $s->name = $f->name ?? 'غير معروف';
            $s->id_num = $f->id_num ?? '';
            $s->category = $f->category ?? 'D';
            $s->score = $f->score ?? 0;
            return $s;
        });

        // 4. Find High Need - Zero or Old Distribution (Under-served/Neglected)
        $criticalKeywords = 'سرطان|ورم|أورام|فشل كلوي|كلى|مصاب|إصابة|بتر|شلل|إعاقة|كفيف|أعمى|صم|بكم|قلب|مفتوح|جلطة|نزيف|كسر|عملية|تشنج|صرع';
        
        $fiveMonthsAgo = now()->subMonths(5)->format('Y-m-d');
        
        // IDs of people who received aid in the last 5 months
        $recentlyServedIds = $distributionStats->where('last_delivery', '>=', $fiveMonthsAgo)->pluck('person_id')->toArray();

        $neglectedHeadsQuery = Person::whereNull('relative_id')
            ->where(function($query) use ($areaIds) {
                $query->whereIn('area_responsible_id', $areaIds)
                      ->orWhereHas('block', function($q) use ($areaIds) {
                          $q->whereIn('area_responsible_id', $areaIds);
                      });
            })
            ->whereNotIn('id', $recentlyServedIds);
            
        if ($blockId) $neglectedHeadsQuery->where('block_id', $blockId);

        // Filter for potential A/B categories: Medical conditions OR large families OR destroyed housing/tents
        $neglectedHeadsQuery->where(function($q) use ($criticalKeywords) {
            $q->where('condition_description', 'REGEXP', $criticalKeywords)
              ->orWhere('relatives_count', '>=', 5)
              ->orWhere('housing_damage_status', 'مدمر كلي')
              ->orWhereIn('housing_type', ['خيمة', 'مركز إيواء']);
        });

        $isExport = $request->has('export');

        if ($isExport) {
            set_time_limit(900);
            ini_set('memory_limit', '1024M');

            // استخدام استعلام سريع للغاية Raw SQL لتجنب التايم آوت
            $neglectedHeadsQueryFast = DB::table('persons as p')
                ->leftJoin('blocks as b', 'p.block_id', '=', 'b.id')
                ->whereNull('p.relative_id')
                ->where(function($query) use ($areaIds) {
                    $query->whereIn('p.area_responsible_id', $areaIds)
                          ->orWhereIn('b.area_responsible_id', $areaIds);
                })
                ->whereNotIn('p.id', $recentlyServedIds);

            if ($blockId) $neglectedHeadsQueryFast->where('p.block_id', $blockId);

            $neglectedHeadsQueryFast->where(function($q) use ($criticalKeywords) {
                $q->where('p.condition_description', 'REGEXP', $criticalKeywords)
                  ->orWhere('p.relatives_count', '>=', 5)
                  ->orWhere('p.housing_damage_status', 'مدمر كلي')
                  ->orWhereIn('p.housing_type', ['خيمة', 'مركز إيواء']);
            });

            $neglectedHeads = $neglectedHeadsQueryFast->select('p.*')->get();
        } else {
            $neglectedHeads = $neglectedHeadsQuery->limit(250)->get(); // حد أمان للعرض السريع على الويب
        }

        $neglectedMembers = Person::whereIn('relative_id', $neglectedHeads->pluck('id_num'))->get();
        
        $scoredNeglected = $this->calculateScores($neglectedHeads, $neglectedMembers);
        
        $neglected = $scoredNeglected->whereIn('category', ['A', 'B'])->map(function($n) use ($distributionStats) {
            $stats = $distributionStats->firstWhere('person_id', $n->id);
            $n->last_delivery = $stats->last_delivery ?? 'لم يستلم أبداً';
            return $n;
        });

        if (!$isExport) {
            $neglected = $neglected->take(50);
        }

        if ($request->has('export')) {
            return \Maatwebsite\Excel\Facades\Excel::download(
                new \App\Exports\OutliersExport($overServed, $neglected),
                'outliers_report.xlsx'
            );
        }

        return view('admin.audit.outliers', [
            'title' => 'تحليل القيم الشاذة والمخاطر',
            'overServed' => $overServed,
            'neglected' => $neglected,
            'avg' => round($avg, 2),
            'areas' => \App\Models\AreaResponsible::all(),
            'blocks' => \App\Models\Block::whereIn('area_responsible_id', $areaIds)->get(),
            'total_dist' => $distributionStats->count()
        ]);
    }

    public function history($id_num)
    {
        if (!auth()->user()?->isAdmin() && !auth()->user()?->hasPermission('manage.admins') && !auth()->user()?->hasPermission('manage.audit')) {
            abort(403, 'غير مصرح لك');
        }

        $history = DB::table('project_beneficiaries as pb')
            ->join('projects as p', 'pb.project_id', '=', 'p.id')
            ->where('pb.person_id', $id_num)
            ->where('pb.status', 'مستلم')
            ->select('p.name as project_name', 'pb.quantity', 'pb.delivery_date', 'pb.notes')
            ->orderBy('pb.delivery_date', 'desc')
            ->get();

        return response()->json($history);
    }

    private function stdDev($array)
    {
        if (empty($array)) return 0;
        $fMean = array_sum($array) / count($array);
        $fVariance = 0;
        foreach ($array as $i) $fVariance += pow($i - $fMean, 2);
        return sqrt($fVariance / count($array));
    }
}
