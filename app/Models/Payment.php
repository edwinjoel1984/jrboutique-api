<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'customer_id', 'amount', 'payment_method'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
