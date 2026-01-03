<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;


class ChatService
{
    public const DEFAULT_MODEL = 'openai/gpt-4o-mini';

    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
        // Auto-correct common misconfig where base URL misses /api/v1 and hits the marketing site.
        if (!str_contains($baseUrl, '/api/')) {
            $baseUrl .= '/api/v1';
        }
        $this->baseUrl = $baseUrl;
    }


    public function createConversation(?string $model = null): Conversation
    {
        $model = $model ?? Auth::user()->preferred_model ?? self::DEFAULT_MODEL;

        return Conversation::create([
            'user_id' => Auth::id(),
            'model' => $model,
        ]);
    }

    /**
     * @param array<int, array{type: string, text?: string, image_url?: array{url: string, detail?: string}}> $userBlocks
     */
    public function sendMessage(Conversation $conversation, array $userBlocks): string
    {
        $conversation->addMessage('user', $userBlocks);

        $messages = $conversation->getFormattedMessages();

        try {
            $response = $this->callOpenRouterAPI($messages, $conversation->model);

            $conversation->addMessage('assistant', $response);

            if ($conversation->messages->count() === 2) { // 1 user + 1 assistant
                $conversation->update([
                    'title' => $this->generateTitle($userMessage, $response),
                ]);
            }

            return $response;
        } catch (\Exception $e) {
            $conversation->messages()->latest()->first()?->delete();
            throw $e;
        }
    }

    /**
     * Appelle l'API OpenRouter.
     *
     * @param array<int, array{role: string, content: string|array<int, array<string, mixed>>}> $messages
     */
    private function callOpenRouterAPI(array $messages, string $model): string
    {
        $systemPrompt = $this->getSystemPrompt();
        $allMessages = [$systemPrompt, ...$messages];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Referer' => config('app.url'),
            'User-Agent' => 'Laravel/12 SurferAI Chat',
        ])
            ->timeout(120)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $allMessages,
                'temperature' => 1.0,
            ]);

        if ($response->failed()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            throw new \RuntimeException("Erreur API: {$error}");
        }

        return $response->json('choices.0.message.content', '');
    }


    private function generateTitle(string $userMessage, string $aiResponse): string
    {
        try {
            $shortMessage = substr($userMessage, 0, 100) . (strlen($userMessage) > 100 ? '...' : '');

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Referer' => config('app.url'),
                'User-Agent' => 'Laravel/12 SurferAI Chat',
            ])
                ->timeout(30)
                ->post($this->baseUrl . '/chat/completions', [
                    'model' => 'openai/gpt-3.5-turbo',
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Tu es un générateur de titres. Génère un titre très court (2-5 mots) et naturel pour cette conversation en français. Réponds UNIQUEMENT avec le titre, sans guillemets ni explications.',
                        ],
                        [
                            'role' => 'user',
                            'content' => "Message utilisateur: {$shortMessage}\n\nRéponse: " . substr($aiResponse, 0, 100),
                        ],
                    ],
                    'temperature' => 0.5,
                ]);

            if ($response->successful()) {
                $title = trim($response->json('choices.0.message.content', ''));
                return $title ?: 'Nouvelle conversation';
            }
        } catch (\Exception) {
        }

        return 'Nouvelle conversation';
    }

    /**
     *
     * @return array{role: 'system', content: string}
     */
    private function getSystemPrompt(): array
    {
        $user = Auth::user();
        $userName = $user?->name ?? 'l\'utilisateur';
        $now = now()->locale('fr')->format('l d F Y H:i');

        $base = "Tu es un assistant de chat intelligent et utile. La date et l'heure actuelle est le {$now}. Tu es actuellement utilisé par {$userName}. Réponds en français sauf si l'utilisateur te demande autrement.";

        // Injecter les instructions personnalisées si présentes
        $instructionsText = '';
        if ($user && !empty($user->instructions)) {
            if (is_array($user->instructions)) {
                // si stocké comme tableau, joindre les éléments
                $instructionsText = implode("\n", $user->instructions);
            } else {
                $instructionsText = (string) $user->instructions;
            }
            $base = "[INSTRUCTIONS UTILISATEUR]\n{$instructionsText}\n---\n" . $base;
        }

        return [
            'role' => 'system',
            'content' => $base,
        ];
    }

    /**
     * Public wrapper to get the chat system prompt for external callers (controllers).
     *
     * @return array{role: 'system', content: string}
     */
    public function getChatSystemPrompt(): array
    {
        return $this->getSystemPrompt();
    }

    /**
     * Met à jour le modèle préféré de l'utilisateur.
     */
    public function updatePreferredModel(string $model): void
    {
        Auth::user()?->update(['preferred_model' => $model]);
    }

    /**
     * Récupère les conversations de l'utilisateur.
     *
     * @return array<int, array{id: int, title: string, model: string, updated_at: string}>
     */
    public function getUserConversations(): array
    {
        return Auth::user()
            ->conversations()
            ->select('id', 'title', 'model', 'updated_at')
            ->get()
            ->map(fn (Conversation $conv) => [
                'id' => $conv->id,
                'title' => $conv->title ?? 'Nouvelle conversation',
                'model' => $conv->model,
                'updated_at' => $conv->updated_at?->diffForHumans() ?? 'à l\'instant',
            ])
            ->toArray();
    }
}
