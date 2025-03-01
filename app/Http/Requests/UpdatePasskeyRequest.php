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

class UpdatePasskeyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->session()->get('id_num');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'passkey'               => [
                'required',
                'string',
                'min:9',
                'max:15',
                'regex:/[A-Z]/',      // على الأقل حرف كبير
                'regex:/[a-z]/',      // على الأقل حرف صغير
                'regex:/[0-9]/',      // على الأقل رقم
                'regex:/[\W_]/'
            ]
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
            'passkey.required'      => 'كلمة المرور مطلوبة.',
            'passkey.min'           => 'يجب أن تكون كلمة المرور على الأقل 9 أحرف.',
            'passkey.max'           => 'يجب ألا تتجاوز كلمة المرور 15 حرفًا.',
            'passkey.regex'         => 'يجب أن تحتوي كلمة المرور على حرف كبير، حرف صغير، رقم ورمز خاص (!@#$%^&*).',
        ];
    }

}