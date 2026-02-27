<?php

namespace App\Models\Transaction;

use Illuminate\Database\Eloquent\Model;

class SalePayment extends Model
{
    protected $fillable = [
        'sale_id',
        'payment_date',
        'amount',
        'payment_method',
        'notes',
        'payment_proof',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    /* =========================
       RELATION
    ==========================*/

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    protected static function booted()
    {
        static::saved(function ($payment) {
            $payment->sale->recalcTotals();
        });

        static::deleted(function ($payment) {
            $payment->sale->recalcTotals();
        });
    }
}