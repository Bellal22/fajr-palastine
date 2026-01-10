<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectRequest extends FormRequest
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
        // الحصول على ID المشروع في حالة التعديل
        $projectId = $this->route('project') ? $this->route('project')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('projects', 'name')->ignore($projectId)->whereNull('deleted_at')
            ],
            'description' => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'status' => ['nullable', 'in:active,completed,suspended'],

            // الشركاء (متعدد)
            'granting_entities' => ['required', 'array', 'min:1'],
            'granting_entities.*' => ['exists:suppliers,id'],
            'executing_entities' => ['required', 'array', 'min:1'],
            'executing_entities.*' => ['exists:suppliers,id'],

            // أنواع الكوبونات (قائمة بالنوع والكمية)
            'coupon_types' => ['nullable', 'array'],
            'coupon_types.*.coupon_type_id' => ['required', 'exists:coupon_types,id'],
            'coupon_types.*.quantity' => ['required', 'integer', 'min:1'],

            // الطرود (متعدد - جاهز وداخلي)
            'ready_packages' => ['nullable', 'array'],
            'ready_packages.*' => ['exists:ready_packages,id'],
            'internal_packages' => ['nullable', 'array'],
            'internal_packages.*' => ['exists:internal_packages,id'],

            // التعارض مع مشاريع أخرى
            'conflicts' => ['nullable', 'array'],
            'conflicts.*' => ['exists:projects,id'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('projects.attributes');
    }

    /**
     * Get custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'اسم المشروع مطلوب',
            'name.unique' => 'يوجد مشروع بنفس الاسم مسبقاً! الرجاء اختيار اسم آخر.',
            'name.max' => 'اسم المشروع يجب ألا يتجاوز 255 حرف',
            'end_date.after_or_equal' => 'تاريخ الانتهاء يجب أن يكون بعد أو يساوي تاريخ البدء',
            'granting_entities.required' => 'يجب اختيار جهة مانحة واحدة على الأقل',
            'granting_entities.min' => 'يجب اختيار جهة مانحة واحدة على الأقل',
            'executing_entities.required' => 'يجب اختيار جهة منفذة واحدة على الأقل',
            'executing_entities.min' => 'يجب اختيار جهة منفذة واحدة على الأقل',
        ];
    }
}
