<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldHistory extends Model
{
    use HasFactory;

    protected $table = 'field_history';

    protected $fillable = [
        'author_id',
        'field_id',
        'individual_id',
        'before',
        'after'
    ];

    protected $with = [
        'author',
        'field'
    ];

    public function author()
    {
        return $this->belongsTo(User::class);
    }

    public function field()
    {
        return $this->belongsTo(Field::class);
    }

    public function individual()
    {
        return $this->belongsTo(Individual::class, 'individual_id', 'id');
    }
}
