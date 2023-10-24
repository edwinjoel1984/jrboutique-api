<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleSize extends Model
{
    use HasFactory;
    protected $fillable = ["id", "purchase_price", "sale_price", "quantity", "article_id", "size_id", "uniquecode"];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
    public function transaction(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function orderDetail(): BelongsTo
    {
        return $this->belongsTo(OrderDetail::class);
    }
}
