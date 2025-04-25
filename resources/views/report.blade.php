<x-layouts.app>
    <div class="flex flex-col items-center  text-white">


        {{-- Report Card --}}
        <div class="bg-white/10 backdrop-blur-xl w-full p-8 rounded-3xl shadow-2xl space-y-6">
            {{-- Header --}}
            <div class="text-center mb-10">
                <h1 class="text-4xl font-bold tracking-tight">Your Emotional Report</h1>
                <p class="text-sm text-white/70 mt-1">Based on your answers, this is your emotional sound profile</p>
            </div>
    
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-3 gap-x-6">
                <p><span class="font-semibold text-white/80">👤 Name:</span> {{ $result->name }}</p>
                <p><span class="font-semibold text-white/80">🎂 Age:</span> {{ $result->age }}</p>
                <p><span class="font-semibold text-white/80">⚧ Gender:</span> {{ ucfirst($result->gender) }}</p>
                <p><span class="font-semibold text-white/80">🧠 Initial Emotion:</span> {{ ucfirst($result->emotional_state) }}</p>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-white/90 mb-2">🎭 Detected Mood Tags:</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(explode(' ', $result->final_emotion) as $tag)
                        <span class="bg-pink-500/30 text-white px-3 py-1 rounded-full text-sm font-medium">{{ ucfirst($tag) }}</span>
                    @endforeach
                </div>
            </div>

            <div>
                <h3 class="text-lg font-semibold text-white/90 mb-2">📝 Your answers:</h3>
                <ul class="list-disc pl-8 space-y-1 text-white/90">
                    @foreach(json_decode($result->answers, true) as $answer)
                        <li>{{ ucfirst($answer) }}</li>
                    @endforeach
                </ul>
            </div>

            <div class="pt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                <a href="/music/{{ urlencode($result->final_emotion) }}"
                   class="bg-gradient-to-r from-pink-500 via-purple-500 to-indigo-500 hover:brightness-110 px-6 py-3 text-white font-semibold rounded-full shadow-lg transition">
                    🔊 Listen to Your Music
                </a>
                <a href="/" class="text-white/80 hover:text-white underline">← Start Over</a>
            </div>
        </div>
    </div>
</x-layouts.app>
