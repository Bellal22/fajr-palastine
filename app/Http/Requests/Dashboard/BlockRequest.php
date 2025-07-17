<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BlockRequest extends FormRequest
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
        // تعريف قواعد التحقق من الصحة الأساسية
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'area_responsible_id' => ['nullable', 'exists:area_responsibles,aid_id'], // nullable لأن العمود في DB nullable
            'phone' => ['required', 'integer'], // int(11) في DB
            'limit_num' => ['required', 'integer'], // int(11) في DB
            'lan' => ['required', 'string', 'max:200'],
            'lat' => ['required', 'string', 'max:200'],
            'note' => ['required', 'string'], // text في DB
        ];

        // قاعدة التحقق من 'title' تختلف بين الإنشاء والتحديث
        if ($this->isMethod('POST')) { // عند إنشاء سجل جديد (Store)
            $rules['title'] = ['required', 'string', 'max:255', 'unique:blocks,title'];
        } elseif ($this->isMethod('PUT') || $this->isMethod('PATCH')) { // عند تحديث سجل موجود (Update)
            // عند التحديث، يجب أن نتجاهل الـ title الخاص بالسجل الحالي نفسه
            // $this->block هو الموديل الذي يتم تمريره للـ Form Request في حالة التحديث
            $rules['title'] = ['required', 'string', 'max:255', Rule::unique('blocks', 'title')->ignore($this->block)];
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
        return trans('blocks.attributes');
    }
}