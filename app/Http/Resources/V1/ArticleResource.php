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
        $stock = 0;
        foreach ($this->stock as $articleSize) {
            $stock = $stock + $articleSize->quantity;
        }
        return [
            'name' => $this->name,
            'ref' => $this->ref,
            'code' => $this->barcode,
            'brand' => $this->brand->name,
            'stock' => $stock
        ];
        // return parent::toArray($request);
    }
}
