<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MusicController extends Controller
{
    public function fetch($emotion)
    {
        $emotionTags = explode(' ', urldecode($emotion));

        // Emotion to sound search term mapping
        $searchMap = [
            'happy' => ['uplifting', 'bright', 'cheerful', 'melody'],
            'sad' => ['melancholy', 'slow', 'minor', 'soft piano'],
            'calm' => ['relaxing', 'ambient', 'peaceful', 'gentle'],
            'intense' => ['epic', 'dramatic', 'loud', 'cinematic'],
            'ambient' => ['texture', 'nature', 'background', 'atmosphere'],
        ];

        // Build final query terms
        $keywords = [];
        foreach ($emotionTags as $tag) {
            if (isset($searchMap[$tag])) {
                $keywords = array_merge($keywords, $searchMap[$tag]);
            }
        }

        // Remove duplicates and prepare final search string
        $query = implode(' ', array_unique($keywords));

        // Search Freesound
        $response = Http::get('https://freesound.org/apiv2/search/text/', [
            'query' => $query,
            'filter' => 'duration:[30 TO 90] license:"Creative Commons 0"',
            'sort' => 'score',
            'fields' => 'previews,name,url,duration',
            'page_size' => 1,
            'token' => config('services.freesound.key'),
        ]);

        $data = $response->json();

        $preview = null;
        $name = null;
        $error = null;

        if (!empty($data['results'])) {
            $preview = $data['results'][0]['previews']['preview-hq-mp3'] ?? null;
            $name = $data['results'][0]['name'] ?? 'Unknown';
        } else {
            // Fallback: use just the first emotion tag
            $fallbackTag = $emotionTags[0] ?? 'calm';
            $fallbackQuery = implode(' ', $searchMap[$fallbackTag] ?? [$fallbackTag]);

            $fallback = Http::get('https://freesound.org/apiv2/search/text/', [
                'query' => $fallbackQuery,
                'filter' => 'duration:[30 TO 90] license:"Creative Commons 0"',
                'sort' => 'score',
                'fields' => 'previews,name,url,duration',
                'page_size' => 1,
                'token' => config('services.freesound.key'),
            ])->json();

            if (!empty($fallback['results'])) {
                $preview = $fallback['results'][0]['previews']['preview-hq-mp3'] ?? null;
                $name = $fallback['results'][0]['name'] ?? 'Unknown';
                $emotion = ucfirst($fallbackTag);
            } else {
                $error = "No sound found for this mood.";
            }
        }

        return view('result', compact('preview', 'name', 'emotion', 'error'));
    }
}
