<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class OutboundShipmentRequest extends FormRequest
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
            'shipment_number' => ['required', 'string', 'max:255', 'unique:outbound_shipments,shipment_number,' . $this->route('outbound_shipment')?->id],
            'project_id' => ['nullable', 'integer', 'exists:projects,id'],
            'sub_warehouse_id' => ['required', 'integer', 'exists:sub_warehouses,id'],
            'notes' => ['nullable', 'string'],
            'driver_name' => ['nullable', 'string', 'max:255'],
            
            // بيان الصادر
            'shipment_items' => ['required', 'array', 'min:1'],
            'shipment_items.*.type' => ['required', 'in:ready_package,internal_package'],
            'shipment_items.*.package_id' => ['required', 'integer'],
            'shipment_items.*.quantity' => ['required', 'integer', 'min:1'],
            'shipment_items.*.weight' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return trans('outbound_shipments.attributes');
    }
}
