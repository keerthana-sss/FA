<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'payer_id',
        'payee_id',
        'amount',
        'currency',
        'description',
        'is_settled'
    ];

    protected $casts = [
        'is_settled' => 'boolean',
    ];

    //Relation
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id');
    }
}
