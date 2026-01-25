<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class PersonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
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
            'first_name' => ['required', 'string', 'max:191'],
            'father_name' => ['required', 'string', 'max:191'],
            'grandfather_name' => ['required', 'string', 'max:191'],
            'family_name' => ['required', 'string', 'max:191'],
            'id_num' => ['required', 'numeric', 'digits:9'],
            'gender' => ['required', 'string'],
            'dob' => ['required', 'date'],
            'social_status' => ['required', 'string'],
            'employment_status' => ['required', 'string'],
            'city' => ['required', 'string'],
            'current_city' => ['required', 'string'],
            'housing_type' => ['required', 'string'],
            'housing_damage_status' => ['required', 'string'],
            'neighborhood' => ['required', 'string'],
            'area_responsible_id' => ['nullable', 'exists:area_responsibles,id'],
            'block_id' => ['nullable', 'exists:blocks,id'],
            'landmark' => ['nullable', 'string'],
            'case_description' => ['nullable', 'string'],
            'has_condition' => ['nullable', 'boolean'],
            'condition_description' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
            'family_members' => ['nullable', 'array'],
            'family_members.*.first_name' => ['required_with:family_members', 'string', 'max:191'],
            'family_members.*.father_name' => ['required_with:family_members', 'string', 'max:191'],
            'family_members.*.grandfather_name' => ['required_with:family_members', 'string', 'max:191'],
            'family_members.*.family_name' => ['required_with:family_members', 'string', 'max:191'],
            'family_members.*.id_num' => ['required_with:family_members', 'numeric', 'digits:9'],
            'family_members.*.dob' => ['required_with:family_members', 'date'],
            'family_members.*.gender' => ['required_with:family_members', 'string'],
            'family_members.*.relationship' => ['required_with:family_members', 'string'],
            'family_members.*.has_condition' => ['nullable', 'boolean'],
            'family_members.*.condition_description' => ['nullable', 'string'],
            'family_members.*.phone' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('people.attributes');
    }
}
