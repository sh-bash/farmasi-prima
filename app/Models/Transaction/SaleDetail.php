<?php

namespace App\Models\Transaction;

use App\Models\Master\Product;
use Illuminate\Database\Eloquent\Model;

class SaleDetail extends Model
{
    protected $fillable = [
        'sale_id',
        'product_id',
        'qty',
        'price',
        'discount',
        'total'
    ];

    protected $casts = [
        'qty'      => 'decimal:2',
        'price'    => 'decimal:2',
        'discount' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    /* =========================
       RELATION
    ==========================*/

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* =========================
       AUTO HITUNG TOTAL
    ==========================*/

    protected static function booted()
    {
        static::saving(function ($detail) {
            $detail->total = ($detail->qty * $detail->price) - $detail->discount;
        });

        static::saved(function ($detail) {
            $detail->sale->recalcTotals();
        });

        static::deleted(function ($detail) {
            $detail->sale->recalcTotals();
        });
    }
}