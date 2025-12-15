<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'package' => (bool) $this->package,
            'type' => $this->type,
            'weight' => $this->weight,
            'quantity' => $this->quantity,
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
            'deleted_at' => method_exists($this->resource, 'trashed') && $this->trashed()
                ? optional($this->deleted_at)->toISOString()
                : null,
        ];
    }
}
