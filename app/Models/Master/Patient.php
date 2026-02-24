<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'medical_record_number',
        'name',
        'birth_date',
        'gender',
        'address',
        'phone',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
