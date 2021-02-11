<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Individual extends Model
{
    use HasFactory;

    protected $table = 'individuals';

    protected $fillable = [
        'name',
        'surname',
        'patronymic'
    ];

    protected $casts = [
        'create_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
