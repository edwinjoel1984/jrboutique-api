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
        $details = $this->order_details()->get();
        $total = 0;
        foreach ($details as $item) {
            $total = $total + $item->quantity * $item->unit_price;
        }
        return [
            'id' => $this->id,
            'date' => $this->order_date,
            'customer' => $this->customer,
            'status' => $this->status,
            'total' => $total,
            'details' => OrderDetailResource::collection($details)
        ];
    }
}
