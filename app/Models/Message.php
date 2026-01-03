<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'role',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Decode JSON content if available, otherwise return raw string.
     *
     * @param mixed $value
     * @return array|string|null
     */
    public function getContentAttribute($value)
    {
        $decoded = json_decode($value, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $value;
    }

    /**
     * Encode arrays to JSON before persisting.
     *
     * @param mixed $value
     */
    public function setContentAttribute($value): void
    {
        $this->attributes['content'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Format the message for the LLM API, preserving multimodal blocks when present.
     *
     * @return array{role: string, content: string|array<int, array<string, mixed>>}
     */
    public function toApiFormat(): array
    {
        $content = $this->content;

        if (is_array($content) || is_string($content)) {
            return [
                'role' => $this->role,
                'content' => $content,
            ];
        }

        return [
            'role' => $this->role,
            'content' => '',
        ];
    }

    /**
     * Récupère la conversation propriétaire du message.
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
