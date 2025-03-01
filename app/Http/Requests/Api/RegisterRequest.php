<?php

namespace App\Http\Requests\Api;

use App\Enums\Person\PersonCity;
use App\Enums\Person\PersonCurrentCity;
use App\Enums\Person\PersonDamageHousingStatus;
use App\Enums\Person\PersonHousingType;
use App\Enums\Person\PersonNeighborhood;
use App\Enums\Person\PersonSocialStatus;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Requests\Concerns\WithHashedPassword;

class RegisterRequest extends FormRequest
{
    use WithHashedPassword;

    /**
     * Determine if the supervisor is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
            'avatar' => ['nullable', 'image'],
            'type' => [
                'nullable',
                Rule::in([
                    User::CUSTOMER_TYPE,
                ]),
            ],
            'id_num' => [
                'required',
                'numeric',
                'digits:9',
                function ($attribute, $value, $fail) {
                    if (!$this->isValidLuhn($value)) {
                        $fail('رقم الهوية غير صالح .');
                    }
                },
            ],
            'first_name'              => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'father_name'             => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'grandfather_name'        => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'family_name'             => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'gender'                  => ['required', 'in:ذكر,أنثى'],
            'dob'                     => ['required', 'date'],
            'phone'                   => ['required', 'string', 'regex:/^(059|056)\d{7}$/'],
            'social_status'           => [ 'required', Rule::in(PersonSocialStatus::toValues()),
                function ($attribute, $value, $fail) {
                    if (request('gender') === 'أنثى' && $value === 'married'|| 'polygamous') {
                        $fail('يرجى التسجيل ببيانات الزوج حتى لو كان الزوج متزوج أكثر من زوجة.');
                    }
                }
            ],
            'employment_status'       => ['required',Rule::in(['موظف', 'عامل', 'لا يعمل'])],
            'has_condition'           => ['required', 'boolean'],
            'condition_description'   => ['nullable', 'string', 'required_if:has_condition,true'],
            'city'                    => ['required', 'exists,cities,id'],
            'current_city'            => ['required', 'exists,cities,id'],
            'neighborhood'            => ['required', 'exists,neighborhoods,id'],
            'landmark'                => ['required','string', 'regex:/^[\p{Arabic} ]+$/u', 'max:255'],
            'housing_type'            => ['required', Rule::in(PersonHousingType::toValues())],
            'housing_damage_status'   => ['required', Rule::in(PersonDamageHousingStatus::toValues())],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('customers.attributes');
    }

    private function isValidLuhn($number)
    {
        $sum = 0;
        $shouldDouble = false;

        // بدء التحقق من آخر رقم إلى أول رقم
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = $number[$i];

            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }

            $sum += $digit;
            $shouldDouble = !$shouldDouble;
        }

        return ($sum % 10) === 0; // رقم الهوية صالح إذا كان المجموع يقبل القسمة على 10
    }

}
