<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VendorPeriod extends Model
{
    protected $fillable = ['vendor_id', 'year', 'month', 'status'];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function inventory(): HasMany
    {
        return $this->hasMany(VendorInventory::class, 'period_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(VendorPayment::class, 'period_id');
    }
}
