<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFamilyRequest extends FormRequest
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
    public function prepareForValidation()
    {
        if ($this->has('persons')) {
            $persons = $this->persons;
            foreach ($persons as &$person) {
                if (isset($person['id_num'])) {
                    $person['id_num'] = preg_replace('/\D/', '', $person['id_num']);
                }
            }
            $this->merge(['persons' => $persons]);
        }
    }

    public function rules(): array
    {
        return [
            'persons.*.id_num'                     => ['required', 'numeric', 'digits:9', 'unique:persons,id_num'],
            'persons.*.first_name'                 => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.father_name'                => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.grandfather_name'           => ['required', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.family_name'                => ['nullable', 'string', 'regex:/^[\p{Arabic} ]+$/u'],
            'persons.*.dob'                        => ['required', 'date'],
            'persons.*.relationship'               => ['required', 'string'],
            'persons.*.condition_description'      => ['nullable', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'persons.*.id_num.required'            => 'الرجاء ادخال رقم الهوية',
            'persons.*.id_num.numeric'             => 'رقم الهوية يجب ان يكون ارقام فقط',
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
        ];
    }

}