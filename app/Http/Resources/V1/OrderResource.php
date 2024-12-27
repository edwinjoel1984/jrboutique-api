<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'date' => $this->order_date,
            'customer' => $this->whenLoaded('customer'),
            'status' => $this->status,
            'total' => $this->total,
            'first_payment' => $this->first_payment,
            'discount_value' => $this->discount_value,
            'details' => OrderDetailResource::collection($this->whenLoaded('order_details'))
        ];
    }
}
