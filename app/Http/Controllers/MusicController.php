<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MusicController extends Controller
{
    public function fetch($emotion)
    {
        $query = urldecode($emotion);

        $response = Http::get('https://freesound.org/apiv2/search/text/', [
            'query' => $query,
            'filter' => 'duration:[35 TO 60]',
            'sort' => 'score',
            'fields' => 'previews,name,url,duration',
            'page_size' => 1,
            'token' => config('services.freesound.key'),
        ]);

        $data = $response->json();

        // Default values
        $preview = null;
        $name = null;
        $error = null;

        if (!empty($data['results'])) {
            $preview = $data['results'][0]['previews']['preview-hq-mp3'] ?? null;
            $name = $data['results'][0]['name'] ?? 'Unknown';
        } else {
            // Try fallback: only first mood word
            $firstMood = explode(' ', $query)[0];

            $fallback = Http::get('https://freesound.org/apiv2/search/text/', [
                'query' => $firstMood,
                'filter' => 'duration:[35 TO 60]',
                'sort' => 'score',
                'fields' => 'previews,name,url,duration',
                'page_size' => 1,
                'token' => config('services.freesound.key'),
            ])->json();

            if (!empty($fallback['results'])) {
                $preview = $fallback['results'][0]['previews']['preview-hq-mp3'] ?? null;
                $name = $fallback['results'][0]['name'] ?? 'Unknown';
                $emotion = $firstMood; // update for the view
            } else {
                $error = "No sound found for this mood.";
            }
        }

        return view('result', compact('preview', 'name', 'emotion', 'error'));
    }
}
