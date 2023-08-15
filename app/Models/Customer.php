<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = ['first_name', 'last_name', 'document', 'phone'];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    public function full_name()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function commitments(): HasMany
    {
        return $this->hasMany(Commitment::class);
    }
}
