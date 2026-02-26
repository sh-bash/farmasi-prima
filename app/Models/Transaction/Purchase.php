<?php

namespace App\Models\Transaction;

use App\Models\User;
use App\Models\Master\Supplier;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use SoftDeletes, Blameable;

    protected $fillable = [
        'purchase_number',
        'purchase_date',
        'supplier_id',

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

    /* =========================
       RELATION MASTER
    ==========================*/

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /* =========================
       RELATION DETAIL & PAYMENT
    ==========================*/

    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
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