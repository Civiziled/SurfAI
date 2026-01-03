<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class InstructionsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        return Inertia::render('Help/Instructions', [
            'instructions' => $user->instructions ?? null,
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'instructions' => 'nullable|string|max:5000',
        ]);

        $user = Auth::user();
        $user->update(['instructions' => $request->input('instructions')]);

        return redirect()->route('instructions.edit');
    }
}
