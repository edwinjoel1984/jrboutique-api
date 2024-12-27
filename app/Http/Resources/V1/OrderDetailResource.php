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
        return [
            'id' => $this->id,
            'sale_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'sale_total' => $this->quantity * $this->unit_price,
            'name' => $this->whenLoaded('articleSize') ? $this->whenLoaded('articleSize')?->article?->name . ' (Talla ' . $this->whenLoaded('articleSize')?->size?->name . ')' : "PROM - " . $this->whenLoaded('Offer')?->name,
            'code' => $this->whenLoaded('articleSize') ? $this->whenLoaded('articleSize')?->uniquecode : '0000000000'
        ];
    }
}
