<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'model',
        'context',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Récupère l'utilisateur propriétaire de la conversation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère tous les messages de la conversation.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class)->orderBy('created_at', 'asc');
    }

    /**
     * Récupère tous les messages formatés pour l'API.
     *
     * @return array<int, array{role: string, content: string}>
     */
    public function getFormattedMessages(): array
    {
        return $this->messages
            ->map(fn (Message $message) => $message->toApiFormat())
            ->toArray();
    }

    /**
     * Ajoute un message à la conversation.
     */
    public function addMessage(string $role, array|string $content): Message
    {
        return $this->messages()->create([
            'role' => $role,
            'content' => $content,
        ]);
    }
}
