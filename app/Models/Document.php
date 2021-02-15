<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $table = 'documents';

    protected $fillable = [
        'individual_id',
        'type'
    ];

    protected static function booted()
    {
        static::created(function ($user) {
            $user->updated_at = null;
            $user->save();
        });
    }

    protected $with = [
        'fields',
        'documentImage'
    ];

    public function individual()
    {
        return $this->belongsTo(Individual::class);
    }

    public function fields()
    {
        return $this->hasMany(Field::class);
    }

    public function documentImage()
    {
        return $this->hasMany(
            DocumentImage::class
        );
    }
}
