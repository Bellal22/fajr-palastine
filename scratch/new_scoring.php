    private function calculateScores($heads, $allPersons)
    {
        $tiers = [
            'critical' => ['سرطان', 'كيماوي', 'فشل كلوي', 'غسيل', 'شلل', 'بتر أطراف', 'مقعد', 'كفيف', 'أعمى', 'أورام خبيثة', 'قلب مفتوح'],
            'serious' => ['مصاب', 'إصابة', 'بتر', 'إعاقة', 'جلطة', 'نزيف', 'صرع', 'شظايا', 'اصابة حرب', 'بكم', 'صم'],
            'stable' => ['كسر', 'عملية', 'تشنج', 'حامل', 'ولادة', 'نزوح', 'حالة صحية', 'سكر', 'ضغط']
        ];
        
        $results = [];

        foreach ($heads as $head) {
            $members = $allPersons->where('relative_id', $head->id_num);
            $score = 0;
            $reasons = [];

            // دالة الفحص الطبي
            $getHealthScore = function($person) use ($tiers) {
                if (!$person) return 0;
                $text = implode(' ', [$person->condition_description ?? '', $person->family_name ?? '', $person->first_name ?? '']);
                foreach ($tiers['critical'] as $w) if (mb_strpos($text, $w) !== false) return 60;
                foreach ($tiers['serious'] as $w) if (mb_strpos($text, $w) !== false) return 35;
                foreach ($tiers['stable'] as $w) if (mb_strpos($text, $w) !== false) return 15;
                return 0;
            };

            // 1. الأولوية القصوى: الأب والأم (المعيلين الأساسيين)
            $father = ($head->gender == 'ذكر' || in_array($head->relationship, ['أب', 'father'])) ? $head : null;
            $mother = ($head->gender == 'أنثى' || in_array($head->relationship, ['أم', 'mother'])) ? $head : null;
            
            // البحث عن الزوج/الزوجة في الأفراد إذا لم يكن هو رب الأسرة
            if (!$father) $father = $members->firstWhere('relationship', 'زوج');
            if (!$mother) $mother = $members->firstWhere('relationship', 'زوجة');

            $fatherHealth = $getHealthScore($father);
            $motherHealth = $getHealthScore($mother);

            if ($fatherHealth > 0) { $score += $fatherHealth; $reasons[] = "إصابة الأب (المعيل)"; }
            if ($motherHealth > 0) { $score += $motherHealth; $reasons[] = "إصابة الأم (المعيلة)"; }

            // 2. إذا كان المعيلين غير موجودين أو مصابين، نبحث عن المعيل البديل (> 18 سنة)
            $isParentsHealthy = ($father && $fatherHealth == 0) || ($mother && $motherHealth == 0);
            $backupProvider = null;

            if (!$isParentsHealthy) {
                $backupProvider = $members->filter(function($m) {
                    try { return $m->dob && \Carbon\Carbon::parse($m->dob)->age >= 18; } catch(\Exception $e) { return false; }
                })->sortBy('dob')->first();

                if ($backupProvider) {
                    $providerHealth = $getHealthScore($backupProvider);
                    if ($providerHealth > 0) {
                        $score += ($providerHealth * 0.8);
                        $reasons[] = "إصابة المعيل البديل (الابن الأكبر)";
                    } else {
                        $score -= 10; // المعيل البديل سليم يقلل الخطر
                    }
                } else {
                    $score += 25; // لا يوجد معيل بالغ
                    $reasons[] = "لا يوجد معيل بالغ";
                }
            }

            // 3. احتياجات الأطفال (بامبرز وحليب)
            $infantsCount = 0;
            foreach ($members as $member) {
                if ($member->dob) {
                    try {
                        $age = \Carbon\Carbon::parse($member->dob)->age;
                        if ($age < 2) { 
                            $infantsCount++; 
                            $score += 18; // وزن الحليب والبامبرز
                        } elseif ($age < 5) {
                            $score += 10;
                        }
                    } catch(\Exception $e) {}
                }
            }
            if ($infantsCount > 0) $reasons[] = "احتياج حليب وبامبرز ($infantsCount)";

            // 4. الحالة الصحية لباقي أفراد العائلة
            $otherMembers = $members->whereNotIn('id', [
                $father->id ?? 0, 
                $mother->id ?? 0, 
                $backupProvider->id ?? 0
            ]);
            foreach ($otherMembers as $other) {
                $otherHealth = $getHealthScore($other);
                if ($otherHealth > 0) {
                    $score += ($otherHealth * 0.3);
                    $reasons[] = "إصابة فرد تابع";
                }
            }

            // 5. السكن والوضع الاقتصادي
            if (in_array($head->housing_type, ['خيمة', 'مركز إيواء'])) $score += 15;
            if ($head->housing_damage_status == 'مدمر كلي') $score += 20;

            // 6. التميز الفريد لمنع التشابه
            $uniqueFactor = ($head->id_num % 1000) / 10000;
            $finalScore = min($score + $uniqueFactor, 100);

            if ($finalScore >= 85) { $category = "A"; $badge = "danger"; }
            elseif ($finalScore >= 60) { $category = "B"; $badge = "warning"; }
            elseif ($finalScore >= 35) { $category = "C"; $badge = "info"; }
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
                'address' => ($head->areaResponsible->name ?? '') . ' / ' . ($head->block->name ?? ''),
                'last_delivery' => null
            ];
        }

        return collect($results)->sortByDesc('score');
    }
