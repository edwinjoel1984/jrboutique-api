<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'customer_id' => $this->customer_id,
            'customer_name' => $this->getCustomerName($this->whenLoaded('customer')),
            'amount' => $this->amount,
            'date' => $this->date
        ];
    }

    private function getCustomerName($customer)
    {
        return $customer->first_name . ' ' . $customer->last_name;
    }
}
