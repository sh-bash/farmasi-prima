<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_ingredients')
            ->withPivot('strength', 'unit')
            ->withTimestamps();
    }
}
