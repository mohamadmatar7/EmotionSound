<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

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

    // VERY simple keyword logic
    $newEmotion = 'neutral';
    $response = "Let me suggest something different...";

    if (str_contains($message, 'relax') || str_contains($message, 'calm')) {
        $newEmotion = 'sad';
        $response = "How about something calmer and softer?";
    } elseif (str_contains($message, 'happy') || str_contains($message, 'fun')) {
        $newEmotion = 'happy';
        $response = "Got it! Here's something with more positive energy.";
    } elseif (str_contains($message, 'angry') || str_contains($message, 'intense')) {
        $newEmotion = 'intense';
        $response = "Alright, here's something with stronger vibes.";
    }

    return redirect('/result')
        ->with('show_chat', true)
        ->with('chat_response', $response)
        ->with('new_emotion', $newEmotion);
});
