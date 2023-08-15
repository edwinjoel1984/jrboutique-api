<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCommitment extends Model
{
    use HasFactory;
    protected  $fillable = ['date', 'amount', 'commitment_id', 'payment_id'];
}
