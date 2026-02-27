<?php

namespace App\Models\Transaction;

use App\Models\User;
use App\Models\Master\Patient;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sale extends Model
{
    use SoftDeletes, Blameable;

    protected $fillable = [
        'sale_number',
        'sale_date',
        'patient_id',

        'subtotal',
        'discount',
        'tax',
        'grand_total',
        'paid_total',
        'balance',

        'status',
        'notes',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $casts = [
        'sale_date'   => 'date:Y-m-d',
        'subtotal'    => 'decimal:2',
        'discount'    => 'decimal:2',
        'tax'         => 'decimal:2',
        'grand_total' => 'decimal:2',
        'paid_total'  => 'decimal:2',
        'balance'     => 'decimal:2',
    ];

    /* =========================
       RELATION MASTER
    ==========================*/

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    /* =========================
       RELATION DETAIL & PAYMENT
    ==========================*/

    public function details()
    {
        return $this->hasMany(SaleDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    /* =========================
       BLAMEABLE RELATION
    ==========================*/

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /* =========================
       BUSINESS LOGIC
    ==========================*/

    public function recalcTotals()
    {
        $subtotal = $this->details()->sum('total');
        $paid = $this->payments()->sum('amount');

        $this->subtotal = $subtotal;
        $this->grand_total = $subtotal - $this->discount + $this->tax;
        $this->paid_total = $paid;
        $this->balance = $this->grand_total - $paid;

        $this->status = match (true) {
            $this->balance <= 0 => 'paid',
            $this->paid_total > 0 => 'partial',
            default => 'posted'
        };

        $this->save();
    }
}