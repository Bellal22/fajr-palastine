<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

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
        return [
            'name' => ['required', 'string', 'max:255'],
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
            // بما أن العميل قال "ممكن اختار اكتر من طرد"، سنفترض قائمة ID للجاهز وقائمة ID للداخلي لتسهيل الـ Validation
            'ready_packages' => ['nullable', 'array'],
            'ready_packages.*' => ['exists:ready_packages,id'],
            'internal_packages' => ['nullable', 'array'],
            'internal_packages.*' => ['exists:internal_packages,id'],
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
}
