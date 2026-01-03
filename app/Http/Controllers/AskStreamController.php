<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SimpleAskStreamService;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AskStreamController extends Controller
{
    public function __construct(
        private SimpleAskStreamService $streamService,
        private ImageService $imageService
    ) {}

    /**
     * Affiche la page de streaming.
     */
    public function index(Request $request): Response
    {
        $modelId = $request->input('model')
            ?? auth()->user()?->preferred_model
            ?? SimpleAskStreamService::DEFAULT_MODEL;

        return Inertia::render('AskStream/Index', [
            'models' => $this->streamService->getModelsLight(),
            'selectedModel' => $modelId,
            'selectedModelDetails' => fn() => $this->streamService->getModelDetails($modelId),
        ]);
    }

    /**
     * Endpoint de streaming.
     */
    public function stream(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'message' => 'required_without:image_url|string|max:100000',
            'image_url' => 'nullable|string',
            'model' => 'required|string',
            'temperature' => 'nullable|numeric|min:0|max:2',
            'reasoning_effort' => 'nullable|string|in:low,medium,high',
        ]);

        // Update user's preferred model
        $user = auth()->user();
        if ($user && $user->preferred_model !== $validated['model']) {
            $user->update(['preferred_model' => $validated['model']]);
        }

        // Build multimodal content: array of blocks (text, image_url)
        $contentBlocks = [];
        if (!empty($validated['message'])) {
            $contentBlocks[] = ['type' => 'text', 'text' => $validated['message']];
        }

        if (!empty($validated['image_url'])) {
            $contentBlocks[] = ['type' => 'image_url', 'image_url' => ['url' => $validated['image_url']]];
        }

        $messages = [['role' => 'user', 'content' => $contentBlocks]];
        $model = $validated['model'];
        $temperature = (float) ($validated['temperature'] ?? 1.0);
        $reasoningEffort = $validated['reasoning_effort'] ?? null;

        return response()->stream(
            function () use ($messages, $model, $temperature, $reasoningEffort): void {
                $this->streamService->streamToOutput($messages, $model, $temperature, $reasoningEffort);
            },
            headers: [
                'Content-Type' => 'text/plain; charset=utf-8',
                'Cache-Control' => 'no-cache, no-store',
                'X-Accel-Buffering' => 'no',
            ]
        );
    }

    /**
     * Upload image, optimize it server-side and return a data URL for sending to the LLM.
     */
    public function uploadImage(Request $request)
    {
        $validated = $request->validate([
            'image' => 'required|image|max:10240', // max 10MB upload
        ]);

        $file = $validated['image'] instanceof \Illuminate\Http\UploadedFile
            ? $validated['image']
            : $request->file('image');

        $tmpPath = $file->getRealPath();

        // Optimize image (returns binary JPEG data)
        $optimizedBinary = $this->imageService->optimizeImage($tmpPath);
        $base64 = base64_encode($optimizedBinary);
        $dataUrl = 'data:image/jpeg;base64,' . $base64;

        return response()->json(['url' => $dataUrl]);
    }
}
