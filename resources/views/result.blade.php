<x-layouts.app>
@php
    $primary = $primary ?? trim(explode('+', str_replace(' ', '+', $emotion))[0] ?? '');
    $quotes = config('emotion.quotes');

    if (empty($primary) || !array_key_exists($primary, $quotes)) {
        $primary = 'neutral';
    }

    $selectedTrack = request()->query('track');
    $trackFile = $selectedTrack === 'music'
        ? asset("music/{$primary}.mp3")
        : asset("music/{$primary} song.mp3");

    $quote = $quotes[$primary][array_rand($quotes[$primary])] ?? null;
@endphp

<h1 class="text-2xl font-bold mb-4 text-white">
    Based on your mood: <span class="capitalize text-pink-400">{{ str_replace('+', ', ', $emotion) }}</span>
</h1>
<p class="text-white text-sm mb-4">
    🎶 We're playing a track inspired by <strong class="capitalize">{{ $primary }}</strong>.
</p>

@if(!request()->has('track'))
    <div class="text-center mt-6">
        <p class="text-white mb-2 text-lg font-semibold">Do you want a vocal song or just mood music?</p>
        <div class="flex gap-4 justify-center">
            <a href="{{ request()->fullUrlWithQuery(['track' => 'song']) }}"
               class="bg-pink-500 hover:bg-pink-600 text-white px-4 py-2 rounded-lg shadow-md transition">🎤 Song</a>
            <a href="{{ request()->fullUrlWithQuery(['track' => 'music']) }}"
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow-md transition">🎵 Music</a>
        </div>
    </div>
@endif

@if(request()->has('track'))
    <p class="mb-2 font-semibold text-white mt-6">Now playing: {{ basename($trackFile) }}</p>
    <audio id="audioPlayer" controls autoplay class="mb-6 text-white">
        <source src="{{ $trackFile }}" type="audio/mpeg">
        Your browser does not support the audio tag.
    </audio>

    <div class="text-center mb-6">
        <a href="{{ request()->fullUrlWithQuery(['track' => $selectedTrack === 'music' ? 'song' : 'music']) }}"
           class="underline text-pink-400 hover:text-pink-300 transition text-sm">
            {{ $selectedTrack === 'music' ? 'Didn’t like it? Try the vocal song instead' : 'Not feeling it? Try the instrumental music' }}
        </a>
    </div>

    <canvas id="waveform" class="w-full max-w-md h-32 mb-6 rounded-lg bg-transparent"></canvas>

    @if(!empty($quotes[$primary]))
        <div id="quoteBox"
             class="mt-4 max-w-xl mx-auto bg-white/10 backdrop-blur-lg border border-white/20 text-white rounded-xl px-6 py-4 shadow-lg overflow-hidden transition-all">
            <p id="quoteText"
               class="text-white/90 italic text-center text-lg transition-opacity duration-700 ease-in-out opacity-100">
                {{ $quotes[$primary][0] }}
            </p>
        </div>

        <script>
            const quotes = @json($quotes[$primary]);
            let current = 0;
            const quoteText = document.getElementById('quoteText');

            setInterval(() => {
                quoteText.classList.remove('opacity-100');
                quoteText.classList.add('opacity-0');

                setTimeout(() => {
                    current = (current + 1) % quotes.length;
                    quoteText.textContent = `“${quotes[current]}”`;
                    quoteText.classList.remove('opacity-0');
                    quoteText.classList.add('opacity-100');
                }, 500);
            }, 6000);
        </script>
    @endif
@endif

<div 
    x-data="{ showThanks: false }"
    x-init="if (window.location.href.includes('thanks=true')) { showThanks = true; setTimeout(() => window.location.href = '/', 4000); }"
    class="flex flex-col space-y-4 w-full max-w-md mt-6"
>
    <form method="POST" action="/feedback" class="flex flex-col space-y-4">
        @csrf
        <input type="hidden" name="emotion" value="{{ $emotion }}">
        <p class="text-center text-white">Do you like the sound?</p>
        <div class="flex justify-center space-x-4">
            <button name="feedback" value="yes"
                    formaction="/feedback?thanks=true"
                    class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-green-600">
                Yes
            </button>
            <button name="feedback" value="no"
                    class="bg-red-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-red-600">
                No
            </button>
        </div>
    </form>

    {{-- Thank You Modal --}}
<div x-show="showThanks"
     x-transition.opacity.scale.duration.500ms
     class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50"
>
    <div class="relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-2xl shadow-2xl px-8 py-6 w-full max-w-sm text-center text-white space-y-3 animate-fade-in">
        {{-- Sparkling Gradient Circle --}}
        <div class="mx-auto w-16 h-16 rounded-full bg-gradient-to-br from-pink-400 via-purple-500 to-indigo-500 flex items-center justify-center text-3xl shadow-lg">
            💖
        </div>

        <h2 class="text-2xl font-semibold">Thank you!</h2>
        <p class="text-white/80">We're so glad you enjoyed the vibe.<br>Sending you back to the homepage…</p>

        <div class="absolute top-2 right-3 text-white/40 text-xs italic">EmotionSound</div>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
    .animate-fade-in {
        animation: fade-in 0.4s ease-out both;
    }
</style>

</div>


@if(session('chat_response') || session('show_chat'))
    <div class="mt-10 w-full max-w-md">
        <form method="POST" action="/chat-reply" class="bg-white/10 backdrop-blur-lg p-6 rounded-2xl shadow-xl space-y-4">
            @csrf
            <label class="block font-semibold text-white text-lg">Tell us what you feel or want:</label>
            <input type="text" name="user_message"
                   class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/20 focus:ring-2 focus:ring-pink-400 focus:outline-none"
                   placeholder="e.g. I want something more relaxing..." required>
            <button type="submit"
                    class="bg-gradient-to-r from-pink-500 via-purple-600 to-blue-500 hover:from-pink-600 hover:to-indigo-600 text-white font-semibold px-6 py-2 rounded-lg shadow-lg transition duration-300 cursor-pointer">
                Send
            </button>
        </form>

        @php
            $moodClusters = [
                'sad' => ['anxious', 'lonely', 'calm', 'nostalgic'],
                'happy' => ['excited', 'grateful', 'hopeful', 'curious'],
                'angry' => ['intense', 'anxious', 'confused', 'lonely'],
                'calm' => ['ambient', 'grateful', 'neutral', 'nostalgic'],
                'neutral' => ['calm', 'nostalgic', 'curious', 'happy'],
                'nostalgic' => ['sad', 'lonely', 'calm', 'peaceful'],
                'anxious' => ['sad', 'calm', 'neutral'],
                'intense' => ['angry', 'excited', 'curious'],
                'bored' => ['curious', 'excited', 'nostalgic'],
            ];
            $original = explode(' ', session('emotion', 'neutral'))[0];
            $related = $moodClusters[$original] ?? ['neutral'];
            $suggestions = collect($related)->shuffle()->take(3);
        @endphp

        <div class="mt-4 flex gap-2 flex-wrap">
            @foreach($suggestions as $mood)
                <form method="POST" action="/chat-reply">
                    @csrf
                    <input type="hidden" name="user_message" value="{{ $mood }}">
                    <button type="submit"
                            class="px-4 py-2 text-sm bg-pink-500 hover:bg-pink-600 text-white rounded-full transition">
                        {{ ucfirst($mood) }}
                    </button>
                </form>
            @endforeach
        </div>

        @if(session('chat_response'))
            <div class="mt-4 p-4 bg-white/10 backdrop-blur-lg border border-white/20 text-white rounded-xl shadow-md">
                <strong class="text-pink-400">🎧 AI:</strong>
                <span class="ml-1 text-white/90">{!! session('chat_response') !!}</span>
            </div>
        @endif
    </div>
@endif

<script>
    const audio = document.getElementById('audioPlayer');
    const canvas = document.getElementById('waveform');
    const ctx = canvas?.getContext('2d');

    if (audio && canvas && ctx) {
        canvas.width = window.innerWidth * 0.8;
        canvas.height = 100;

        let audioCtx, analyser, source, dataArray;

        const emotionColors = {
            happy: '#facc15', sad: '#3b82f6', calm: '#22d3ee', angry: '#ef4444',
            nostalgic: '#f472b6', intense: '#a855f7', anxious: '#6b7280', grateful: '#34d399',
            curious: '#8b5cf6', lonely: '#64748b', neutral: '#cbd5e1', ambient: '#38bdf8', hopeful: '#4ade80'
        };

        const emotion = '{{ $primary }}';
        const waveColor = emotionColors[emotion] || '#ffffff';

        audio.addEventListener('play', () => {
            if (!audioCtx) {
                audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                analyser = audioCtx.createAnalyser();
                source = audioCtx.createMediaElementSource(audio);
                source.connect(analyser);
                analyser.connect(audioCtx.destination);

                analyser.fftSize = 256;
                const bufferLength = analyser.frequencyBinCount;
                dataArray = new Uint8Array(bufferLength);

                function draw() {
                    requestAnimationFrame(draw);
                    analyser.getByteFrequencyData(dataArray);
                    ctx.clearRect(0, 0, canvas.width, canvas.height);

                    const barWidth = (canvas.width / bufferLength) * 1.5;
                    let x = 0;
                    for (let i = 0; i < bufferLength; i++) {
                        const barHeight = dataArray[i] / 2;
                        ctx.fillStyle = waveColor;
                        ctx.fillRect(x, canvas.height - barHeight, barWidth, barHeight);
                        x += barWidth + 1;
                    }
                }

                draw();
            }
        });
    }
</script>
</x-layouts.app>
