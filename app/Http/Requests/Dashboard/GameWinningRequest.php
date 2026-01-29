<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class GameWinningRequest extends FormRequest
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
            'code' => ['required', 'string', 'max:255', 'unique:game_winnings,code' . ($this->game_winning ? ',' . $this->game_winning->id : '')],
            'person_id' => ['nullable', 'exists:persons,id'],
            'coupon_type_id' => ['required', 'exists:coupon_types,id'],
            'status' => ['required', 'in:pending,redeemed'],
            'delivered_at' => ['nullable', 'date'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('game_winnings.attributes');
    }
}
