<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorCustomerPayment extends Model
{
    protected $fillable = ['vendor_id', 'customer_id', 'period_id', 'amount', 'date', 'notes'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(VendorPeriod::class, 'period_id');
    }
}
