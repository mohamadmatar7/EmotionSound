<x-layouts.app>
    <h1 class="text-2xl font-bold mb-6">🎧 Your Emotional Report</h1>

    <div class="text-left w-full max-w-lg space-y-2">
        <p><strong>Name:</strong> {{ $result->name }}</p>
        <p><strong>Age:</strong> {{ $result->age }}</p>
        <p><strong>Gender:</strong> {{ ucfirst($result->gender) }}</p>
        <p><strong>Initial Emotion:</strong> {{ ucfirst($result->emotional_state) }}</p>
        <p><strong>Detected Mood Tag:</strong> <span class="text-blue-600 font-semibold">{{ ucfirst($result->final_emotion) }}</span></p>
    </div>

    <h3 class="text-lg font-semibold mt-6 mb-2">Your answers:</h3>
    <ul class="list-disc pl-6 text-left w-full max-w-lg">
        @foreach(json_decode($result->answers, true) as $answer)
            <li>{{ ucfirst($answer) }}</li>
        @endforeach
    </ul>

    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="/music/{{ urlencode($result->final_emotion) }}" class="bg-blue-600 text-white px-4 py-2 rounded">🔊 Listen to your music</a>
        <a href="/" class="text-gray-600 underline">← Start Over</a>
    </div>
</x-layouts.app>
