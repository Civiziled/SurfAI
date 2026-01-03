<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\ChatService;
use App\Services\ImageService;
use App\Services\SimpleAskService;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function __construct(
        private ChatService $chatService,
        private SimpleAskService $askService,
    ) {}

    public function index()
    {
        $conversations = $this->chatService->getUserConversations();

        return Inertia::render('Chat/Index', [
            'conversations' => $conversations,
            'models' => $this->askService->getModels(),
            'userPreferredModel' => Auth::user()->preferred_model ?? ChatService::DEFAULT_MODEL,
        ]);
    }


    public function show(Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $messages = $conversation->messages->map(fn ($msg) => [
            'role' => $msg->role,
            'content' => $msg->content,
            'id' => $msg->id,
            'created_at' => $msg->created_at->toIso8601String(),
        ])->toArray();

        return Inertia::render('Chat/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title ?? 'Nouvelle conversation',
                'model' => $conversation->model,
                'messages' => $messages,
            ],
            'conversations' => $this->chatService->getUserConversations(),
            'models' => $this->askService->getModels(),
            'userPreferredModel' => Auth::user()->preferred_model ?? ChatService::DEFAULT_MODEL,
        ]);
    }

    /**
     * Crée une nouvelle conversation.
     */
    public function create(Request $request)
    {
        $model = $request->input('model', ChatService::DEFAULT_MODEL);
        $conversation = $this->chatService->createConversation($model);

        return redirect()->route('chat.show', $conversation);
    }

    /**
     * Envoie un message.
     */
    public function sendMessage(Request $request, Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $request->validate([
            'message' => 'nullable|string|max:5000',
            'image' => 'nullable|file|image|max:5120',
            'image_url' => 'nullable|url|max:2048',
            'model' => 'required|string',
        ]);

        if (!$request->filled('message') && !$request->hasFile('image') && !$request->filled('image_url')) {
            return response()->json(['error' => 'Un texte ou une image est requis'], 422);
        }

        $imageDataUrl = null;

        if ($request->hasFile('image')) {
            $imageService = new ImageService();
            $optimized = $imageService->optimizeImage($request->file('image')->getRealPath());
            $imageDataUrl = 'data:image/jpeg;base64,' . base64_encode($optimized);
        } elseif ($request->filled('image_url')) {
            $imageDataUrl = $request->string('image_url');
        }

        $userBlocks = [];

        if ($request->filled('message')) {
            $userBlocks[] = [
                'type' => 'text',
                'text' => $request->string('message')->toString(),
            ];
        }

        if ($imageDataUrl) {
            $userBlocks[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => is_string($imageDataUrl) ? $imageDataUrl : (string) $imageDataUrl,
                ],
            ];
        }

        // Mettre à jour le modèle s'il a changé
        if ($conversation->model !== $request->model) {
            $conversation->update(['model' => $request->model]);
        }

        // Mettre à jour le modèle préféré de l'utilisateur
        $this->chatService->updatePreferredModel($request->model);

        $error = null;
        $response = null;

        try {
            $response = $this->chatService->sendMessage($conversation, $userBlocks);
        } catch (\Exception $e) {
            $error = $e->getMessage();
        }

        // Rafraîchir la conversation pour obtenir tous les nouveaux messages
        $conversation->refresh();

        $messages = $conversation->messages()->get()->map(fn ($msg) => [
            'role' => $msg->role,
            'content' => $msg->content,
            'id' => $msg->id,
            'created_at' => $msg->created_at->toIso8601String(),
        ])->toArray();

        return response()->json([
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title ?? 'Nouvelle conversation',
                'model' => $conversation->model,
                'messages' => $messages,
            ],
            'error' => $error,
        ]);
    }

    /**
     * Supprime une conversation.
     */
    public function destroy(Conversation $conversation)
    {
        if ($conversation->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $conversation->delete();

        return redirect()->route('chat.index');
    }

    /**
     * Envoie un message et stream la réponse via SSE.
     */
    public function sendMessageStream(Request $request, Conversation $conversation)
    {
        Log::info('sendMessageStream called', ['conversation_id' => $conversation->id, 'auth_id' => Auth::id(), 'payload' => $request->all()]);

        if ($conversation->user_id !== Auth::id()) {
            Log::warning('Unauthorized sendMessageStream attempt', ['conversation_id' => $conversation->id, 'auth_id' => Auth::id()]);
            abort(403, 'Accès non autorisé');
        }

        Log::info('Authorized sendMessageStream start', ['conversation_id' => $conversation->id, 'user_id' => Auth::id()]);

        $request->validate([
            'message' => 'nullable|string|max:5000',
            'image_base64' => 'nullable|string',
            'model' => 'nullable|string',
        ]);

        if (!$request->filled('message') && !$request->filled('image_base64')) {
            abort(422, 'Un texte ou une image est requis');
        }

        $userBlocks = [];

        if ($request->filled('message')) {
            $userBlocks[] = [
                'type' => 'text',
                'text' => $request->string('message')->toString(),
            ];
        }

        if ($request->filled('image_base64')) {
            $userBlocks[] = [
                'type' => 'image_url',
                'image_url' => [
                    'url' => $request->string('image_base64')->toString(),
                ],
            ];
        }

        // Ajouter le message utilisateur
        Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $userBlocks,
        ]);

        // Préparer le contexte pour l'API
        $apiMessages = $conversation->getFormattedMessages();

        // Valider le modèle demandé avant l'appel upstream (éviter d'envoyer une valeur invalide)
        try {
            $available = collect($this->askService->getModels())->pluck('id')->toArray();
        } catch (\Throwable $e) {
            $available = [];
        }

        $requestedModel = $request->input('model', $conversation->model);
        if (! in_array($requestedModel, $available, true)) {
            Log::warning('Requested model not available, falling back to conversation/default', ['requested' => $requestedModel]);
            $requestedModel = $conversation->model ?? \App\Services\ChatService::DEFAULT_MODEL;
        }

        // Utiliser Guzzle pour faire une requête stream vers l'API OpenRouter
        $baseUrl = rtrim(config('services.openrouter.base_url', 'https://openrouter.ai/api/v1'), '/');
        if (!str_contains($baseUrl, '/api/')) {
            $baseUrl .= '/api/v1';
        }
        // Ensure base_uri ends with a slash so relative paths concatenate correctly
        $baseUrl = rtrim($baseUrl, '/') . '/';

        $guzzle = new GuzzleClient([
            'base_uri' => $baseUrl,
            'timeout' => 0,
            'headers' => [
                'Authorization' => 'Bearer ' . config('services.openrouter.api_key'),
                'Content-Type' => 'application/json',
                'Accept' => 'text/event-stream',
                'Referer' => config('app.url'),
                'User-Agent' => 'Laravel/12 SurferAI Streaming'
            ],
        ]);

        $body = [
            'model' => $requestedModel,
            'messages' => array_merge([$this->chatService->getChatSystemPrompt()], $apiMessages),
            'temperature' => 1.0,
            'stream' => true,
        ];

        Log::info('Opening upstream stream', ['model' => $body['model']]);

        // Important: use a relative path so we keep the /api/v1 prefix from base_uri.
        // POST to relative path 'chat/completions' to keep the /api/v1 prefix
        $response = $guzzle->post('chat/completions', [
            'json' => $body,
            'stream' => true,
        ]);

        // Log response status and headers for debugging (helps detect HTML redirects)
        try {
            Log::info('Upstream initial response', ['status' => $response->getStatusCode(), 'headers' => $response->getHeaders()]);
        } catch (\Throwable $e) {
            Log::warning('Could not log upstream response status', ['error' => $e->getMessage()]);
        }

        $stream = $response->getBody();

        try {
            return new StreamedResponse(function () use ($stream, $conversation, $guzzle, $body, $available) {
            // Make sure buffering is disabled
            if (! headers_sent()) {
                header('X-Accel-Buffering: no');
            }

            $buffer = '';
            $full = '';
            $hasRetried = false;
            $currentModel = $body['model'] ?? null;

            while (! $stream->eof()) {
                $chunk = $stream->read(1024);
                if ($chunk === '') {
                    usleep(50000);
                    continue;
                }

                $buffer .= $chunk;

                // Split on any newline
                $lines = preg_split("/\r\n|\n|\r/", $buffer);

                // Keep the last partial line in the buffer
                $buffer = array_pop($lines) ?? '';

                foreach ($lines as $line) {
                    // Preserve chunk whitespace: don't fully trim the line (we need leading/trailing spaces)
                    $rawLine = $line;
                    if (trim($rawLine) === '') continue;

                    // If it's an SSE-style line starting with "data:", strip only the prefix, keep remaining whitespace
                    if (preg_match('/^\s*data:/i', $rawLine)) {
                        $line = preg_replace('/^\s*data:\s*/i', '', $rawLine);
                    } else {
                        $line = $rawLine;
                    }

                    // Handle OpenAI-style [DONE]
                    if ($line === '[DONE]') {
                        echo "data: [DONE]\n\n";
                        if (ob_get_level()) { ob_flush(); }
                        flush();
                        break 2;
                    }

                    // Ignore OpenRouter keep-alive comments or debug messages
                    if (str_starts_with($line, ':') || str_contains($line, 'OPENROUTER PROCESSING')) {
                        continue;
                    }

                    // Try to decode JSON chunk
                    $decoded = json_decode($line, true);
                    $content = '';

                    if (is_array($decoded)) {
                        if (isset($decoded['choices'][0]['delta']['content'])) {
                            $content = $decoded['choices'][0]['delta']['content'];
                        } elseif (isset($decoded['choices'][0]['message']['content'])) {
                            $content = $decoded['choices'][0]['message']['content'];
                        } elseif (isset($decoded['text'])) {
                            $content = $decoded['text'];
                        }
                    } else {
                        // Not JSON: strictly ignore raw text to avoid leaking debug info unless it's a known format
                        // Log unexpected format for debugging but do not echo to user
                        Log::debug('Ignored non-JSON stream line', ['line' => substr($line, 0, 100)]);
                        continue; 
                    }

                    if ($content !== '') {
                        // Detect upstream HTML (error page)
                        if (stripos($content, '<!doctype') !== false || stripos($content, '<html') !== false) {
                            Log::error('Upstream returned HTML instead of JSON stream; handling.', ['sample' => substr($content, 0, 200), 'current_model' => $currentModel]);

                            // Try one retry with a fallback model if available
                            if (! $hasRetried) {
                                $hasRetried = true;
                                $fallback = $conversation->model ?? \App\Services\ChatService::DEFAULT_MODEL;
                                if (! empty($available) && is_array($available)) {
                                    // prefer first available model
                                    $fallback = $available[0];
                                }

                                if ($fallback && $fallback !== $currentModel) {
                                    Log::warning('Retrying upstream stream with fallback model', ['from' => $currentModel, 'to' => $fallback]);
                                    // reopen upstream stream with fallback
                                    try {
                                        $body['model'] = $fallback;
                                        $resp2 = $guzzle->post('chat/completions', ['json' => $body, 'stream' => true]);
                                        $stream = $resp2->getBody();
                                        $buffer = '';
                                        $full = '';
                                        $currentModel = $fallback;
                                        // continue the outer while loop to read from new stream
                                        break;
                                    } catch (\Throwable $e) {
                                        Log::error('Retry upstream failed', ['error' => $e->getMessage()]);
                                        $errMsg = 'Erreur upstream après tentative de reprise.';
                                        echo "data: " . json_encode(['error' => $errMsg]) . "\n\n";
                                        if (ob_get_level()) { ob_flush(); }
                                        flush();
                                        break 2;
                                    }
                                }
                            }

                            // If we already retried or cannot find fallback, return error to client
                            $errMsg = 'Réponse invalide du service upstream (modèle introuvable ou erreur).';
                            echo "data: " . json_encode(['error' => $errMsg]) . "\n\n";
                            if (ob_get_level()) { ob_flush(); }
                            flush();
                            break 2;
                        }

                        Log::debug('Chunk content parsed', ['len' => strlen($content)]);
                        // Ensure SSE multi-line safety
                        $data = str_replace("\n", "\ndata: ", $content);
                        echo "data: " . $data . "\n\n";
                        if (ob_get_level()) { ob_flush(); }
                        flush();
                        $full .= $content;
                    }
                }
            }

            // Persist the assistant reply
            if (strlen(trim($full)) > 0) {
                    Message::create([
                        'conversation_id' => $conversation->id,
                        'role' => 'assistant',
                        'content' => $full,
                    ]);
                    Log::info('Assistant reply persisted', ['conversation_id' => $conversation->id, 'len' => strlen($full)]);
            }
            }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-transform',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
            } catch (\Throwable $e) {
                Log::error('Streaming failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                abort(500, 'Erreur de streaming');
            }
    }
}
