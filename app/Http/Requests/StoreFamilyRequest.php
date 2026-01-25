<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFamilyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->session()->has('id_num');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'persons.*.id_num'                     => ['required', 'numeric', 'digits:9', 'unique:persons,id_num'],
            'persons.*.first_name'                 => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.father_name'                => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.grandfather_name'           => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.family_name'                => ['nullable', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.dob'                        => ['required', 'date'],
            'persons.*.gender'                     => ['required', 'in:ذكر,أنثى'],
            'persons.*.relationship'               => ['required', 'string'],
            'persons.*.phone'                      => ['nullable', 'required_if:persons.*.relationship,زوجة', 'regex:/^(056|059)\d{7}$/'],
            'persons.*.condition_description'      => ['nullable', 'string', 'regex:/^[\p{Arabic}0-9\s,.()!?-]+$/u'],
            'persons' => [
                'nullable',
                'array',
                function ($attribute, $value, $fail) {
                    $mainPerson = session('person');
                    if (!$mainPerson) return;

                    $gender = $mainPerson['gender'];
                    $socialStatus = $mainPerson['social_status'];

                    $familyMembers = $value;
                    $wivesCount = 0;
                    $hasHusbandOrWife = false;

                    foreach ($familyMembers as $member) {
                        $rel = $member['relationship'];
                        if (in_array($rel, ['زوجة'])) {
                            $wivesCount++;
                            $hasHusbandOrWife = true;
                        }
                        if (in_array($rel, ['زوج'])) {
                            $hasHusbandOrWife = true;
                        }
                    }

                    // 1. Married Male: Exactly 1 Wife
                    if (($gender === 'ذكر') &&
                        (in_array($socialStatus, ['متزوج/ة'])) &&
                        $wivesCount !== 1) {
                        $fail('الشخص المتزوج يجب أن يكون لديه زوجة واحدة فقط في قائمة الأفراد.');
                    }

                    // 2. Polygamous Male: 2-4 Wives
                    if (($gender === 'ذكر') &&
                        (in_array($socialStatus, ['متعدد الزوجات'])) &&
                        ($wivesCount < 2 || $wivesCount > 4)) {
                        $fail('الشخص المتعدد يجب أن يكون لديه من 2 إلى 4 زوجات في قائمة الأفراد.');
                    }

                    // 3. Single/Divorced/Widowed: No Husband/Wife
                    if (in_array($socialStatus, ['أعزب/انسة','مطلق/ة', 'أرمل/ة']) && $hasHusbandOrWife) {
                        $fail('لا يمكن تسجيل أفراد الأسرة ذات علاقات زوج/زوجة إذا كانت الحالة الاجتماعية أعزب/انسة أو مطلق/ة أو أرمل/ة.');
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'persons.*.id_num.required'            => 'الرجاء ادخال رقم الهوية',
            'persons.*.id_num.numeric'             => 'رقم الهوية يجب ان يكون ارقام فقط',
            'persons.*.id_num.digits'              => 'رقم الهوية يجب ان يكون 9 أرقام',
            'persons.*.first_name.required'        => 'الرجاء ادخال الاسم الاول',
            'persons.*.first_name.regex'           => 'الاسم الاول يجب ان يحتوي فقط على حروف عربية',
            'persons.*.father_name.required'       => 'الرجاء ادخال اسم الاب',
            'persons.*.father_name.regex'          => 'اسم الاب يجب ان يحتوي فقط على حروف عربية',
            'persons.*.grandfather_name.required'  => 'الرجاء ادخال اسم الجد',
            'persons.*.grandfather_name.regex'     => 'اسم الجد يجب ان يحتوي فقط على حروف عربية',
            'persons.*.dob.required'               => 'الرجاء ادخال تاريخ الميلاد',
            'persons.*.dob.date'                   => 'الرجاء ادخال تاريخ الميلاد بشكل صحيح',
            'persons.*.relationship.required'      => 'الرجاء ادخال العلاقة',
            'persons.*.family_name.regex'          => 'اسم العائلة يجب ان يحتوي فقط على حروف عربية إن تم إدخاله',
            'persons.*.phone.required_if'          => 'رقم جوال الزوجة إلزامي عند اختيار علاقة "زوجة".',
            'persons.*.phone.regex'                 => 'رقم الهاتف يجب أن يبدأ بـ 059 أو 056 ويتكون من 10 أرقام.',
            'persons.*.condition_description.regex' => 'وصف الحالة الصحية يجب أن يكون باللغة العربية.',
        ];
    }

}