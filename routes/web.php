<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\MusicController;
use App\Models\EmotionResult;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/result', function () {
    $emotion = session('emotion', 'neutral');
    return view('result', ['emotion' => $emotion]);
});

Route::post('/feedback', function (Request $request) {
    if ($request->feedback === 'no') {
        session(['emotion' => $request->input('emotion')]);
        return redirect('/result')->with('show_chat', true);
    }
    return redirect('/')->with('message', 'Thanks for your feedback!');
});


Route::post('/chat-reply', function (Request $request) {
    $message = strtolower($request->input('user_message'));

    $keywordsToEmotions = [
        'relax' => 'calm', 'calm' => 'calm', 'peace' => 'calm',
        'sad' => 'sad', 'cry' => 'sad',
        'happy' => 'happy', 'fun' => 'happy',
        'angry' => 'angry', 'mad' => 'angry',
        'intense' => 'intense',
        'nostalgic' => 'nostalgic', 'miss' => 'nostalgic',
        'alone' => 'lonely', 'lonely' => 'lonely',
        'hope' => 'hopeful',
        'excited' => 'excited',
        'grateful' => 'grateful',
        'bored' => 'bored',
        'curious' => 'curious',
        'confused' => 'confused',
        'anxious' => 'anxious',
        'ambient' => 'ambient',
        'neutral' => 'neutral',
    ];

    $emotionScores = [];

    foreach ($keywordsToEmotions as $keyword => $emotion) {
        if (Str::contains($message, $keyword)) {
            $emotionScores[$emotion] = ($emotionScores[$emotion] ?? 0) + 1;
        }
    }

    // If nothing matched, default to neutral
    if (empty($emotionScores)) {
        $newEmotion = 'neutral';
    } else {
        // Define conflicting pairs
        $conflicts = [
            ['happy', 'sad'],
            ['angry', 'calm'],
            ['anxious', 'hopeful'],
            ['excited', 'bored'],
        ];

        // Resolve conflicts: keep the higher scored emotion
        foreach ($conflicts as [$a, $b]) {
            if (isset($emotionScores[$a]) && isset($emotionScores[$b])) {
                if ($emotionScores[$a] >= $emotionScores[$b]) {
                    unset($emotionScores[$b]);
                } else {
                    unset($emotionScores[$a]);
                }
            }
        }

        // Keep top 2–3 compatible emotions
        arsort($emotionScores);
        $topEmotions = array_keys($emotionScores);
        $newEmotion = implode('+', array_slice($topEmotions, 0, 3));
    }

    $response = "Got it! Here's something for: <strong>" . str_replace('+', ', ', $newEmotion) . "</strong>.";

    return redirect('/music/' . urlencode($newEmotion))
        ->with('chat_response', $response);
});


Route::get('/music/{emotion}', [MusicController::class, 'fetch']);

Route::get('/report/{id}', function ($id) {
    $result = EmotionResult::findOrFail($id);
    return view('report', compact('result'));
});
