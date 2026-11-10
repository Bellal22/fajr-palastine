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

            // الدعم النقدي (اختياري أو عند تحديد نوع الكوبون نقدي)
            'is_cash' => ['nullable', 'boolean'],
            'cash_amount_type' => ['nullable', 'string', 'in:fixed,custom'],
            'cash_amount' => [
                'nullable',
                Rule::requiredIf(fn () => $this->boolean('is_cash') && $this->input('cash_amount_type', 'fixed') === 'fixed'),
                'numeric',
                'min:0.01',
            ],
            'cash_currency' => ['nullable', 'required_if:is_cash,1', 'string', 'in:ILS,USD,JOD,EUR'],
            'default_delivery_method' => ['nullable', 'string', 'max:50'],

            // الشركاء (متعدد)
            'granting_entities' => ['required', 'array', 'min:1'],
            'granting_entities.*' => ['exists:suppliers,id'],
            'executing_entities' => ['required', 'array', 'min:1'],
            'executing_entities.*' => ['exists:suppliers,id'],

            // أنواع الكوبونات (قائمة بالنوع والكمية)
            'coupon_types' => ['nullable', 'array'],
            'coupon_types.*.coupon_type_id' => ['nullable', 'exists:coupon_types,id'],
            'coupon_types.*.quantity' => ['nullable', 'integer', 'min:1'],

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
            'cash_amount.required_if' => 'يرجى إدخال قيمة الكوبون النقدي',
            'cash_amount.numeric' => 'قيمة الكوبون النقدي يجب أن تكون رقماً',
            'cash_amount.min' => 'قيمة الكوبون النقدي يجب أن تكون أكبر من صفر',
            'cash_currency.required_if' => 'يرجى اختيار عملة الكوبون النقدي',
            'cash_currency.in' => 'العملة المختارة غير صالحة',
        ];
    }
}
