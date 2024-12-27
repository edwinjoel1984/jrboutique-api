<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleSizeResource2 extends JsonResource
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
            'name' => $this->whenLoaded('article')?->name . " Talla (" . $this->whenLoaded('size')?->name . ")",
            'sale_price' => $this->sale_price,
            'size' => $this->whenLoaded('size')?->name,
            'uniquecode' => $this->uniquecode,
            'stock' => $this->quantity,
        ];
    }
}
