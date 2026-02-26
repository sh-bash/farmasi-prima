<?php

namespace App\Models\Master;

use App\Models\User;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes, Blameable;
    protected $fillable = [
        'barcode',
        'name',
        'het',
        'category_id',
        'form_id',
        'is_generic',
        'created_by',
        'updated_by',
        'deleted_by'
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
