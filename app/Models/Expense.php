<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    /** @use HasFactory<\Database\Factories\ExpenseFactory> */
    use HasFactory, SoftDeletes;

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
    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }
    public function payer()
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function payee()
    {
        return $this->belongsTo(User::class, 'payee_id');
    }
}
