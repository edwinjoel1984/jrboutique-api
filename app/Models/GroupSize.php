<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupSize extends Model
{
    use HasFactory;

    public function sizes()
    {
        return $this->belongsToMany(Size::class, 'groupsizes_sizes');
    }
}
