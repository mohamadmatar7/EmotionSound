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
        // Show chatbot for further feedback
        return redirect('/result')->with('show_chat', true);
    }
    return redirect('/')->with('message', 'Thanks for your feedback!');
});


Route::post('/chat-reply', function (Request $request) {
    $message = strtolower($request->input('user_message'));

    $keywordsToEmotions = [
        'relax' => 'calm',
        'calm' => 'calm',
        'peace' => 'calm',
        'sad' => 'sad',
        'cry' => 'sad',
        'happy' => 'happy',
        'fun' => 'happy',
        'angry' => 'angry',
        'mad' => 'angry',
        'intense' => 'intense',
        'nostalgic' => 'nostalgic',
        'miss' => 'nostalgic',
        'alone' => 'lonely',
        'lonely' => 'lonely',
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

    $newEmotion = 'neutral';
    $response = "Let me suggest something different...";

    foreach ($keywordsToEmotions as $keyword => $emotion) {
        if (Str::contains($message, $keyword)) {
            $newEmotion = $emotion;
            $response = "Okay! Let’s try something that matches how you feel: <strong>$emotion</strong>.";
            break;
        }
    }

    return redirect('/music/' . $newEmotion)
        ->with('chat_response', $response);
});



Route::get('/music/{emotion}', [MusicController::class, 'fetch']);

Route::get('/report/{id}', function ($id) {
    $result = EmotionResult::findOrFail($id);
    return view('report', compact('result'));
});
