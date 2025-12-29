<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class InboundShipmentRequest extends FormRequest
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
            'supplier_id' => ['required', 'integer', 'exists:suppliers,id'],
            'shipment_number' => ['required', 'string', 'max:255', 'unique:inbound_shipments,shipment_number'],
            'inbound_type' => ['required', 'in:single_item,ready_package'],
            'notes' => ['nullable', 'string'],
            
            // قواعد الأصناف المفردة
            'items' => ['required_if:inbound_type,single_item', 'array'],
            'items.*.name' => ['required_if:inbound_type,single_item', 'string', 'max:255'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.quantity' => ['required_if:inbound_type,single_item', 'integer', 'min:1'],
            'items.*.weight' => ['nullable', 'numeric', 'min:0'],
            
            // قواعد الطرود الجاهزة
            'packages' => ['required_if:inbound_type,ready_package', 'array'],
            'packages.*.name' => ['required_if:inbound_type,ready_package', 'string', 'max:255'],
            'packages.*.description' => ['nullable', 'string'],
            'packages.*.quantity' => ['nullable', 'integer', 'min:1'],
            'packages.*.weight' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('inbound_shipments.attributes');
    }
}
