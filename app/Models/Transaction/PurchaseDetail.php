<?php

namespace App\Models\Transaction;

use App\Models\User;
use App\Models\Master\Product;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseDetail extends Model
{
    use SoftDeletes, Blameable;

    protected $fillable = [
        'purchase_id',
        'product_id',

        'qty',
        'price',
        'discount',
        'total',

        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /* =========================
       RELATION
    ==========================*/

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

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
}