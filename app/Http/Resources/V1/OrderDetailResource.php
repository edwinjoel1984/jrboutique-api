<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'sale_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'sale_total' => $this->quantity * $this->unit_price,
            'code' => 123,
            'name' => $this->articleSize->article->name . ' (Talla ' . $this->articleSize->size->name . ')',
            'code' => $this->articleSize->uniquecode
        ];
    }
}
