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
        $stockQuantity = 0;
        foreach ($this->stock as $articleSize) {
            $stockQuantity = $stockQuantity + $articleSize->quantity;
        }
        return [
            'name' => $this->name,
            'ref' => $this->ref,
            'code' => $this->barcode,
            'brand' => $this->brand->name,
            'stock' => $stockQuantity,
            'detail_stock' => $this->stock
        ];
    }
}
