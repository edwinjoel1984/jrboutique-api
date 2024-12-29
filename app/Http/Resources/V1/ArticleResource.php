<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
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
            'name' => $this->name,
            'ref' => $this->ref,
            'code' => $this->barcode,
            'brand' => $this->whenLoaded('brand')?->name,
            'stock' => $this->stock_quantity,
            'detail_stock' => ArticleSizeResource::collection($this->whenLoaded('stock')->load('size')),
        ];
    }
}
