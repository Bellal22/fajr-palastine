<?php

namespace App\Http\Requests;

use App\Enums\Person\PersonCity;
use App\Enums\Person\PersonCurrentCity;
use App\Enums\Person\PersonDamageHousingStatus;
use App\Enums\Person\PersonHousingType;
use App\Enums\Person\PersonNeighborhood;
use App\Enums\Person\PersonSocialStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->session()->get('id_num');
    }

    public function prepareForValidation()
    {
        $this->merge([
            'phone' => str_replace('-', '', $this->phone),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // 'id_num'                  => ['required', 'numeric', 'digits:9', 'unique:persons,id_num'],
            'first_name'              => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'father_name'             => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'grandfather_name'        => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'family_name'             => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'gender'                  => ['required', 'in:male,female,ذكر,أنثى'],
            'dob'                     => ['required', 'date'],
            'phone'                   => ['required', 'string', 'regex:/^0?(56|59)\d{7}$/'],
            'social_status' => [
                'required',
                Rule::exists('chooses', 'slug')->where('type', 'social_status'),
                function ($attribute, $value, $fail) {
                    $gender = request('gender');

                    // 1. منع تسجيل الزوجة كصاحب طلب إذا كانت متزوجة أو في حالة تعدد
                    if (($gender === 'أنثى') &&
                        (in_array($value, ['متزوج/ة','متعدد الزوجات']))) {
                        $fail('يمنع التسجيل ببيانات الزوجة يرجى التسجيل ببيانات الزوج');
                    }

                    // 2. منع تسجيل الذكر الأعزب
                    if (($gender === 'ذكر') && (in_array($value, ['أعزب/انسة']))) {
                        $fail('ممنوع التسجيل للذكر الغير متزوج');
                    }
                }
            ],
            'employment_status'       => ['required', Rule::exists('chooses', 'slug')->where('type', 'employment_status')],
            'has_condition'           => ['required', 'boolean'],
            'condition_description'   => ['nullable', 'string', 'required_if:has_condition,true', 'regex:/^[\p{Arabic}0-9\s,.()!?-]+$/u'],
            'city'                    => ['required', Rule::exists('cities', 'name')],
            'current_city'            => ['required', Rule::exists('cities', 'name')],
            'neighborhood'            => ['required', Rule::exists('neighborhoods', 'name')],
            'area_responsible_id'     => [
                'nullable',
                Rule::exists('area_responsibles', 'id'),
                function ($attribute, $value, $fail) {
                    $neighborhood = request('neighborhood');
                    if ($neighborhood) {
                        $hasResponsibles = \App\Models\AreaResponsible::whereHas('neighborhoods', function($q) use ($neighborhood) {
                            $q->where('name', $neighborhood);
                        })->exists();
                        if ($hasResponsibles && empty($value)) {
                            $fail('يرجى اختيار مسؤول المنطقة.');
                        }
                    }
                }
            ],
            'landmark'                => ['required','string', 'regex:/^[\p{Arabic}0-9 ]+$/u', 'max:255'],
            'housing_type'            => ['required', Rule::exists('chooses', 'slug')->where('type', 'housing_type')],
            'housing_damage_status'   => ['required', Rule::exists('chooses', 'slug')->where('type', 'housing_damage_status')],
            'block_id'                => ['nullable', Rule::exists('blocks', 'id')],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required'      => 'الرجاء إدخال الاسم الأول بشكل صحيح.',
            'first_name.regex'         => 'الاسم الأول يجب أن يحتوي فقط على حروف عربية.',
            'first_name.max'           => 'الاسم الأول يجب ألا يتجاوز 255 حرفاً.',

            'father_name.required'     => 'الرجاء إدخال اسم الأب بشكل صحيح.',
            'father_name.regex'        => 'اسم الأب يجب أن يحتوي فقط على حروف عربية.',
            'father_name.max'          => 'اسم الأب يجب ألا يتجاوز 255 حرفاً.',

            'grandfather_name.required' => 'الرجاء إدخال اسم الجد بشكل صحيح.',
            'grandfather_name.regex'   => 'اسم الجد يجب أن يحتوي فقط على حروف عربية.',
            'grandfather_name.max'     => 'اسم الجد يجب ألا يتجاوز 255 حرفاً.',

            'family_name.required'     => 'الرجاء إدخال اسم العائلة بشكل صحيح.',
            'family_name.regex'        => 'اسم العائلة يجب أن يحتوي فقط على حروف عربية.',
            'family_name.max'          => 'اسم العائلة يجب ألا يتجاوز 255 حرفاً.',

            'gender.required'          => 'يرجى اختيار الجنس.',
            'gender.exists'            => 'لا يمكنك اختيار "غير محدد"، يرجى اختيار "ذكر" أو "أنثى".',
            'gender.in'                => 'لا يمكنك اختيار "غير محدد"، يرجى اختيار "ذكر" أو "أنثى".',

            'dob.required'             => 'الرجاء إدخال تاريخ الميلاد بشكل صحيح.',
            'dob.date'                 => 'الرجاء إدخال تاريخ ميلاد صحيح.',

            'phone.required'           => 'الرجاء إدخال رقم الهاتف بشكل صحيح.',
            'phone.regex'              => 'رقم الهاتف يجب أن يبدأ بـ 059 أو 056 ويتكون من 10 أرقام.',

            'social_status.required'   => 'الرجاء تحديد الحالة الاجتماعية.',

            'employment_status.required' => 'الرجاء تحديد حالة العمل.',
            'employment_status.in'     => 'الرجاء تحديد حالة العمل بين الموظف، العامل أو لا يعمل.',

            'has_condition.boolean'    => 'الرجاء تحديد ما إذا كانت لديك حالة صحية أم لا.',

            'condition_description.required_if' => 'الرجاء وصف الحالة الصحية التي تعاني منها.',
            'condition_description.string'    => 'وصف الحالة الصحية يجب أن يكون نصاً.',
            'condition_description.regex'     => 'وصف الحالة الصحية يجب أن يكون باللغة العربية.',

            'city.required'            => 'الرجاء اختيار المدينة بشكل صحيح.',

            'current_city.required'    => 'الرجاء اختيار المدينة الحالية بشكل صحيح.',

            'neighborhood.required'    => 'الرجاء إدخال الحي بشكل صحيح.',

            'area_responsible_id.required' => 'يرجى اختيار مسؤول المنطقة.',
            'area_responsible_id.exists' => 'مسؤول المنطقة المحدد غير موجود.',

            'landmark.required'        => 'الرجاء إدخال المعلم بشكل صحيح.',
            'landmark.regex'           => 'المعلم يجب أن يكون باللغة العربية فقط.',

            'housing_type.required'    => 'الرجاء تحديد نوع السكن.',

            'housing_damage_status.required' => 'الرجاء تحديد حالة السكن.'
        ];
    }

}