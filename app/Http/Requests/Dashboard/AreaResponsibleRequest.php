<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AreaResponsibleRequest extends FormRequest
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
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:255'],
        ];

        // قاعدة التحقق من 'aid_id' تختلف بين الإنشاء والتحديث
        if ($this->isMethod('POST')) { // عند إنشاء سجل جديد (Store)
            $rules['aid_id'] = ['required', 'integer', 'unique:area_responsibles,aid_id'];
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) { // عند تحديث سجل موجود (Update)
            // عند التحديث، يجب أن نتجاهل الـ aid_id الخاص بالسجل الحالي نفسه
            // $this->area_responsible هو الموديل الذي يتم تمريره للـ Form Request في حالة التحديث
            $rules['aid_id'] = ['required', 'integer', Rule::unique('area_responsibles', 'aid_id')->ignore($this->area_responsible)];
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('area_responsibles.attributes');
    }
}