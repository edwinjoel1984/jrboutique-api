<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commitment extends Model
{
    use HasFactory;
    protected $fillable = ['date', 'total_amount', 'pending_amount', 'customer_id', 'order_id', 'memo'];
}
