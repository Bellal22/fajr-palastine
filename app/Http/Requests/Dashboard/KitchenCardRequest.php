<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KitchenCardRequest extends FormRequest
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
        $cardId = $this->route('kitchen_card') ? $this->route('kitchen_card')->id : null;

        return [
            'camp_name' => ['required', 'string', 'max:255'],
            'card_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('kitchen_cards')->where(function ($query) {
                    return $query->where('camp_name', $this->camp_name);
                })->ignore($cardId),
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('kitchen_cards.attributes');
    }
}
