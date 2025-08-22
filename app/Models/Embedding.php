<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Embedding extends Model
{
    protected $fillable = [
        'file_id',
        'chunk_text',
        'chunk_index',
        'embedding_vector',
        'embedding_model'
    ];

    protected $casts = [
        'embedding_vector' => 'array',
        'chunk_index' => 'integer'
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(File::class);
    }
}