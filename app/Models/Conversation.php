<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'model_used',
        'model_parameters',
        'is_archived'
    ];

    protected $casts = [
        'model_parameters' => 'array',
        'is_archived' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at');
    }

    public function files(): HasMany
    {
        return $this->hasMany(File::class);
    }

    public function generateTitle(): string
    {
        $firstMessage = $this->messages()->where('role', 'user')->first();
        if ($firstMessage) {
            return Str::limit($firstMessage->content, 50);
        }
        return 'New Conversation';
    }
}