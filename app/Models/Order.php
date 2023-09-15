<?php

namespace App\Models;

use App\Models\Scopes\ClosedScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;
    protected $fillable = ['order_date', 'customer_id', 'status'];

    // protected static function boot()
    // {
    //     parent::boot();
    //     static::addGlobalScope(new ClosedScope);
    // }


    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }


    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'OPEN');
    }

    public function total_order($order_id)
    {
        return OrderDetail::where('order_id', $order_id)->selectRaw('unit_price*quantity as total')
            ->get()->sum('total');
    }
}
