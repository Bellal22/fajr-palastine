<?php

namespace App\Http\Controllers;

use App\Models\CouponType;
use App\Models\GameWinning;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class GameController extends Controller
{
    /**
     * Display the game page.
     */
    public function index()
    {
        $realTypes = CouponType::all();
        if ($realTypes->isEmpty()) {
            $realTypes = collect([
                (object)['id' => 1, 'name' => 'طرد غذائي'],
                (object)['id' => 2, 'name' => 'قسيمة شراء'],
                (object)['id' => 3, 'name' => 'كوبون خضار'],
            ]);
        }
        
        // Interleave Hard Luck segments
        $segments = collect();
        foreach ($realTypes as $type) {
            $segments->push(['id' => $type->id, 'name' => $type->name, 'type' => 'win']);
            $segments->push(['id' => 0, 'name' => 'حظ أوفر', 'type' => 'loss']);
        }

        return view('game', [
            'segments' => $segments,
            'realTypes' => $realTypes
        ]);
    }

    /**
     * Handle the spin logic.
     */
    public function spin(Request $request)
    {
        $request->validate([
            'id_number' => ['required', 'numeric', 'digits:9'],
        ], [
            'id_number.required' => 'يرجى إدخال رقم الهوية للمشاركة.',
            'id_number.numeric' => 'رقم الهوية يجب أن يتكون من أرقام فقط.',
            'id_number.digits' => 'رقم الهوية يجب أن يتكون من 9 أرقام.',
        ]);

        try {
            $idNum = $request->id_number;

            // 1. Check Ban List
            $isBanned = \App\Models\BanList::where('id_num', $idNum)->exists();
            if ($isBanned) {
                return response()->json([
                    'success' => false,
                    'message' => 'عذراً، هذا الرقم محظور من المشاركة في النشاطات.',
                ], 403);
            }

            // 2. Check if registered
            $person = \App\Models\Person::where('id_num', $idNum)->first();
            if (!$person) {
                // Not registered - store in session and tell frontend to redirect
                session(['id_num' => $idNum]);
                return response()->json([
                    'success' => false,
                    'redirect' => route('persons.create'),
                    'message' => 'رقم الهوية غير مسجل مسبقاً، سيتم تحويلك لصفحة التسجيل لتتمكن من اللعب.',
                ]);
            }

            // 2. Head of Household Check
            // Must have passkey AND relative_id is null
            if (empty($person->passkey) || !is_null($person->relative_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'يمكنك اللعب برقم هوية رب الأسرة فقط.',
                ]);
            }

            // 3. Area & Block Assignment Check
            if (empty($person->area_responsible_id) || empty($person->block_id)) {
                return response()->json([
                    'success' => false,
                    'message' => 'توجه لمسؤول منطقتك لاعتماد بياناتك لتتمكن من اللعب.',
                ]);
            }

            // 4. Daily Limit Check
            $today = now()->toDateString();
            $cacheKey = 'last_spin_' . $idNum;
            $lastSpin = \Illuminate\Support\Facades\Cache::get($cacheKey);

            if ($lastSpin === $today) {
                return response()->json([
                    'success' => false,
                    'message' => 'عذراً، لقد استنفذت محاولتك اليومية. حاول مرة أخرى غداً!',
                ]);
            }

            \Illuminate\Support\Facades\Cache::put($cacheKey, $today, now()->addDay());
            
            // Prepare segments logic again to match View
            // (In a real app, this should be a shared service/helper)
            $couponTypes = CouponType::all();
            if ($couponTypes->isEmpty()) {
                 $couponTypes = collect([
                    (object)['id' => 1, 'name' => 'طرد غذائي'],
                    (object)['id' => 2, 'name' => 'قسيمة شراء'],
                    (object)['id' => 3, 'name' => 'كوبون خضار'],
                ]);
            }
            $segments = collect();
            foreach ($couponTypes as $type) {
                $segments->push(['id' => $type->id, 'name' => $type->name, 'type' => 'win']);
                $segments->push(['id' => 0, 'name' => 'حظ أوفر', 'type' => 'loss']);
            }

            // 4. Win Probability Check (5%)
            if (rand(1, 100) > 5) {
                // LOST
                // Pick a random losing segment index
                $lossIndices = $segments->keys()->filter(function ($key) use ($segments) {
                    return $segments[$key]['type'] === 'loss';
                })->values();
                
                $targetIndex = $lossIndices->random();

                return response()->json([
                    'success' => true,
                    'is_win' => false,
                    'prize_index' => $targetIndex,
                    'message' => 'حظ أوفر! لم يحالفك الحظ هذه المرة.',
                ]);
            }

            DB::beginTransaction();
            if ($couponTypes->isEmpty()) {
                 $couponTypes = collect([
                    (object)['id' => 1, 'name' => 'طرد غذائي'],
                ]);
            }

            // Pick a random prize
            $prize = $couponTypes->random();

            // Generate a unique code
            $code = 'FAJR-' . strtoupper(Str::random(10));
            
            // Save the winning
            $winning = GameWinning::create([
                'code' => $code,
                'person_id' => $person->id,
                'coupon_type_id' => $prize->id ?? 1,
                'status' => 'pending',
            ]);

            DB::commit();

            // Find index of this prize in segments
            // We know prizes are at interleaved positions. 
            // We need to match ID.
            $targetIndex = $segments->search(function ($item) use ($prize) {
                return $item['id'] == $prize->id;
            });

            return response()->json([
                'success' => true,
                'is_win' => true,
                'prize_index' => $targetIndex,
                'prize' => $prize->name ?? 'طرد مفاجئ',
                'code' => $code,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'حدث خطأ غير متوقع، يرجى المحاولة لاحقاً.'], 500);
        }
    }
}
