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

    public function getDifference()
    {
        $changes = $this->getDirty();

        $before = json_encode(array_intersect_key($this->fresh()->toArray(), $changes));
        $after = json_encode($changes);

        return compact('before', 'after');
    }
}
