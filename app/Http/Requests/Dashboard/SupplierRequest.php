<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'name'         => ['required', 'string', 'max:255'],
            'description'  => ['required', 'string', 'max:1024'],
            'image'        => ['nullable', 'image', 'max:5120'],
            'document'     => [
                'nullable',
                'file',
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'max:10240'
            ],
            'type' => 'required|in:donor,operator',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('suppliers.attributes');
    }

    /**
     * Get custom error messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'document.mimetypes' => 'يجب أن يكون الملف من نوع: PDF أو Word (doc, docx)',
            'document.file' => 'يجب أن يكون ملفاً صالحاً',
            'document.max' => 'يجب ألا يتجاوز حجم الملف 10 ميجابايت',
        ];
    }
}