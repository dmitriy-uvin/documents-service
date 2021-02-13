<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Field extends Model
{
    use HasFactory;

    protected $table = 'fields';

    protected $fillable = [
        'value',
        'type',
        'confidence',
        'document_id',
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id', 'id');
    }
}
