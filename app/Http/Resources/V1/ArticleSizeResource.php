<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleSizeResource extends JsonResource
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
            'purchase_price' => $this->purchase_price,
            'sale_price' => $this->sale_price,
            'quantity' => $this->quantity,
            'size' => $this->whenLoaded('size')?->name,
            'size_id' => $this->whenLoaded('size')?->id,
            'uniquecode' => $this->uniquecode,
        ];
    }
}
