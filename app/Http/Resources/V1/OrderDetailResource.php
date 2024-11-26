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
            'name' => $this->articleSize ? $this->articleSize->article->name . ' (Talla ' . $this->articleSize->size->name . ')' : "PROM - ".$this->Offer->name,
            'code' => $this->articleSize ? $this->articleSize->uniquecode : '0000000000'
        ];
    }
}
