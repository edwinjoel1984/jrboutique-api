<?php

namespace App\Http\Resources\V1;

use App\Models\Brand;
use App\Models\GroupSize;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollectionResource extends ResourceCollection
{
    public $collects  = ArticleResource::class;
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        return [
            'articles' => $this->collection,
            'brands' => Brand::all(),
            'group_sizes' => GroupSize::all()
        ];
    }
}
