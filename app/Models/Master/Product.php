<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'barcode',
        'name',
        'het'
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($product) {
            if (!$product->isForceDeleting()) {
                $product->barcode = $product->barcode . '__del' . time();
                $product->saveQuietly();
            }
        });
    }
}
