<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponTypeRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'is_cash' => ['nullable', 'boolean'],
            'cash_amount_type' => ['nullable', 'string', 'in:fixed,custom'],
            'cash_amount' => [
                'nullable',
                Rule::requiredIf(fn () => $this->boolean('is_cash') && $this->input('cash_amount_type', 'fixed') === 'fixed'),
                'numeric',
                'min:0.01',
            ],
            'cash_currency' => ['nullable', 'required_if:is_cash,1', 'string', 'in:ILS,USD,JOD,EUR'],
        ];
    }

    public function attributes()
    {
        return trans('coupon_types.attributes');
    }
}
