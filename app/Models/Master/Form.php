<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    protected $fillable = ['name'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
