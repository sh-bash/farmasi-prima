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
        'het',
        'category_id',
        'form_id',
        'is_generic'
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

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function form()
    {
        return $this->belongsTo(Form::class);
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'product_ingredients')
            ->withPivot('strength', 'unit')
            ->withTimestamps();
    }

    public function getIngredientLabelAttribute()
    {
        return $this->ingredients->map(function ($i) {
            return $i->name . ' ' . $i->pivot->strength . $i->pivot->unit;
        })->join(', ');
    }
}
