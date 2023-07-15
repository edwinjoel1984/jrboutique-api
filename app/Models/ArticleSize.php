<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArticleSize extends Model
{
    use HasFactory;
    protected $fillable = ["price", "quantity", "article_id", "size_id"];
}
