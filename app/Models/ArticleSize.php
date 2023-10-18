<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleSize extends Model
{
    use HasFactory;
    protected $fillable = ["price", "quantity", "article_id", "size_id"];

    public function articles(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class);
    }
}
