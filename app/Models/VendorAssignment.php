<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorAssignment extends Model
{
    protected $fillable = ['vendor_id', 'customer_id', 'period_id', 'vendor_inventory_id', 'quantity', 'unit_price'];

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

    public function vendorInventory(): BelongsTo
    {
        return $this->belongsTo(VendorInventory::class, 'vendor_inventory_id');
    }
}
