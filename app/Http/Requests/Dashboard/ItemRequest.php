<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ItemRequest extends FormRequest
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
            'package' => ['sometimes', 'boolean'],
            'type' => ['nullable', 'integer', 'min:0', 'max:255'],
            'weight' => ['nullable', 'numeric', 'min:0'],
            'quantity' => ['nullable', 'integer', 'min:0'], // Should not be used but kept for compatibility
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('items.attributes');
    }
}
