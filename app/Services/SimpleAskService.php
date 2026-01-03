<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Http;

class SimpleAskService
{
    public const DEFAULT_MODEL = 'openai/gpt-4o-mini';

    private string $apiKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.openrouter.api_key');
        $baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
        if (!str_contains($baseUrl, '/api/')) {
            $baseUrl .= '/api/v1';
        }
        $this->baseUrl = $baseUrl;
    }

    /**
     * @return array<int, array{
     *     id: string,
     *     name: string,
     *     description: string,
     *     context_length: int,
     *     max_completion_tokens: int,
     *     input_modalities: array<string>,
     *     output_modalities: array<string>,
     *     supported_parameters: array<string>
     * }>
     */
    public function getModels(): array
    {
        return cache()->remember('openrouter.models', now()->addHour(), function (): array {
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $this->apiKey,
                ])->timeout(10)->get($this->baseUrl . '/models');

                if ($response->successful()) {
                    return collect($response->json('data', []))
                        ->sortBy('name')
                        ->map(fn (array $model): array => [
                            'id' => $model['id'],
                            'name' => $model['name'],
                            'description' => $model['description'] ?? '',
                            'context_length' => $model['context_length'] ?? 0,
                            'max_completion_tokens' => $model['top_provider']['max_completion_tokens'] ?? 0,
                            'input_modalities' => $model['architecture']['input_modalities'] ?? [],
                            'output_modalities' => $model['architecture']['output_modalities'] ?? [],
                            'supported_parameters' => $model['supported_parameters'] ?? [],
                            'supports_image' => (isset($model['architecture']['modality']) && $model['architecture']['modality'] === 'text+image->text')
                                || in_array('image', $model['architecture']['input_modalities'] ?? [], true),
                        ])
                        ->values()
                        ->toArray();
                }
            } catch (\Exception $e) {
                \Log::warning('Failed to fetch OpenRouter models: ' . $e->getMessage());
            }

            return $this->getFallbackModels();
        });
    }

    private function getFallbackModels(): array
    {
        return [
            [
                'id' => 'openai/gpt-4o-mini',
                'name' => 'OpenAI: GPT-4o Mini',
                'description' => 'Modèle rapide et économique',
                'context_length' => 128000,
                'max_completion_tokens' => 4096,
                'input_modalities' => ['text'],
                'output_modalities' => ['text'],
                'supported_parameters' => ['temperature', 'top_p', 'frequency_penalty'],
                'supports_image' => false,
            ],
            [
                'id' => 'openai/gpt-4-turbo',
                'name' => 'OpenAI: GPT-4 Turbo',
                'description' => 'Modèle puissant avec vision',
                'context_length' => 128000,
                'max_completion_tokens' => 4096,
                'input_modalities' => ['text', 'image'],
                'output_modalities' => ['text'],
                'supported_parameters' => ['temperature', 'top_p', 'frequency_penalty'],
                'supports_image' => true,
            ],
            [
                'id' => 'meta-llama/llama-2-70b-chat',
                'name' => 'Meta: Llama 2 70B Chat',
                'description' => 'Modèle open-source performant',
                'context_length' => 4096,
                'max_completion_tokens' => 2048,
                'input_modalities' => ['text'],
                'output_modalities' => ['text'],
                'supported_parameters' => ['temperature', 'top_p'],
                'supports_image' => false,
            ],
            [
                'id' => 'anthropic/claude-3-haiku',
                'name' => 'Anthropic: Claude 3 Haiku',
                'description' => 'Modèle compact et rapide',
                'context_length' => 200000,
                'max_completion_tokens' => 4096,
                'input_modalities' => ['text'],
                'output_modalities' => ['text'],
                'supported_parameters' => ['temperature', 'max_tokens'],
                'supports_image' => false,
            ],
        ];
    }

    /**
     * @param array<int, array{
     *     role: 'assistant'|'system'|'tool'|'user',
     *     content: array<int, array{
     *         type: 'image_url'|'text',
     *         text?: string,
     *         image_url?: array{url: string, detail?: string}
     *     }>|string
     * }> $messages
     */
    public function sendMessage(array $messages, ?string $model = null, float $temperature = 1.0): string
    {
        $model = $model ?? self::DEFAULT_MODEL;
        $messages = [$this->getSystemPrompt(), ...$messages];

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Referer' => config('app.url'),
            'User-Agent' => 'Laravel/12 SurferAI',
        ])
            ->timeout(120)
            ->post($this->baseUrl . '/chat/completions', [
                'model' => $model,
                'messages' => $messages,
                'temperature' => $temperature,
            ])
        ;
        if ($response->failed()) {
            $error = $response->json('error.message', 'Erreur inconnue');
            throw new \RuntimeException("Erreur API: {$error}");
        }

        return $response->json('choices.0.message.content', '');
    }

    /**
     * @return array{role: 'system', content: string}
     */
    private function getSystemPrompt(): array
    {
        $user = auth()->user()?->name ?? 'l\'utilisateur';
        $now = now()->locale('fr')->format('l d F Y H:i');

        return [
            'role' => 'system',
            'content' => view('prompts.system', [
                'now' => $now,
                'user' => $user,
            ])->render(),
        ];
    }
}
