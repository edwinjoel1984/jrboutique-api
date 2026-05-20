<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VendorInventory extends Model
{
    protected $fillable = ['vendor_id', 'period_id', 'article_size_id', 'custom_name', 'custom_price', 'quantity_assigned', 'quantity_returned', 'status'];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(VendorPeriod::class, 'period_id');
    }

    public function articleSize(): BelongsTo
    {
        return $this->belongsTo(ArticleSize::class, 'article_size_id');
    }
}
