<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MusicController extends Controller
{
    public function fetch($emotion)
    {
        $availableFiles = [
            'angry', 'anxious', 'bored', 'calm', 'confused', 'curious', 'excited',
            'grateful', 'happy', 'hopeful', 'intense', 'lonely', 'neutral',
            'nostalgic', 'sad', 'ambient',
        ];

        $selected = 'neutral';

        foreach (explode('+', $emotion) as $word) {
            if (in_array($word, $availableFiles)) {
                $selected = $word;
                break;
            }
        }

        $musicPath = asset("music/{$selected}.mp3");

        return view('result', [
            'preview' => $musicPath,
            'name' => ucfirst($selected) . ".mp3",
            'emotion' => $selected,
            'error' => null,
        ]);
    }
}
