<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorPayment extends Model
{
    protected $fillable = ['vendor_id', 'period_id', 'amount', 'date', 'notes'];

    protected $casts = [
        'date' => 'date:Y-m-d',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(VendorPeriod::class, 'period_id');
    }
}
