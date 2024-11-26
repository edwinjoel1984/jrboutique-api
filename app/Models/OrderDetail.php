<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderDetail extends Model
{
    use HasFactory;
    protected $fillable = ['order_id', 'article_size_id', 'unit_price', 'quantity', 'offer_id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function articleSize(): belongsTo
    {
        return $this->belongsTo(ArticleSize::class);
    }

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }
}
