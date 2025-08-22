<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class File extends Model
{
    protected $fillable = [
        'user_id',
        'conversation_id',
        'message_id',
        'original_name',
        'file_name',
        'file_type',
        'mime_type',
        'file_path',
        'file_size',
        'metadata',
        'is_processed'
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_processed' => 'boolean',
        'file_size' => 'integer'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function embeddings(): HasMany
    {
        return $this->hasMany(Embedding::class);
    }
}