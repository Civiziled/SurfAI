<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AskController;
use App\Http\Controllers\ChatController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/ask', [AskController::class, 'index'])->name('ask.index');
    Route::post('/ask', [AskController::class, 'ask'])->name('ask.post');
    Route::get('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'index'])->name('stream.index');
    Route::post('/ask-stream', [\App\Http\Controllers\AskStreamController::class, 'stream'])->name('stream.post');
    Route::post('/ask-stream/upload-image', [\App\Http\Controllers\AskStreamController::class, 'uploadImage'])->name('stream.uploadImage');
});

Route::middleware('auth')->group(function () {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/create', [ChatController::class, 'create'])->name('chat.create');
    Route::get('/chat/{conversation}', [ChatController::class, 'show'])->name('chat.show');
    Route::post('/chat/{conversation}/message', [ChatController::class, 'sendMessage'])->name('chat.sendMessage');
    Route::post('/chat/{conversation}/stream', [ChatController::class, 'sendMessageStream'])->name('chat.stream');
    Route::delete('/chat/{conversation}', [ChatController::class, 'destroy'])->name('chat.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/instructions', [\App\Http\Controllers\InstructionsController::class, 'edit'])->name('instructions.edit');
    Route::post('/instructions', [\App\Http\Controllers\InstructionsController::class, 'update'])->name('instructions.update');
});

require __DIR__.'/auth.php';
