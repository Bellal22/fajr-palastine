<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function getFamilyMemberData($id)
    {
        $familyMember = Person::find($id);

        if ($familyMember) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $familyMember->id,
                    'first_name' => $familyMember->first_name,
                    'father_name' => $familyMember->father_name,
                    'grandfather_name' => $familyMember->grandfather_name,
                    'family_name' => $familyMember->family_name,
                    'id_num' => $familyMember->id_num,
                    'dob' => $familyMember->dob,
                    'relationship' => $familyMember->relationship,
                    'has_condition' => $familyMember->has_condition,
                    'condition_description' => $familyMember->condition_description
                ],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'لم يتم العثور على بيانات العضو المطلوب.'
        ], 404);
    }
}