<x-layouts.app>
    <h1 class="text-2xl font-bold mb-4">Music for emotion: <span class="capitalize text-blue-600">{{ $emotion }}</span></h1>

    @if(isset($preview))
        <p class="mb-2 font-semibold">Track: {{ $name }}</p>
        <audio controls class="mb-6">
            <source src="{{ $preview }}" type="audio/mpeg">
            Your browser does not support the audio tag.
        </audio>
    @elseif(isset($error))
        <p class="text-red-600">{{ $error }}</p>
    @endif

    <a href="/" class="text-blue-500 underline mt-4 inline-block">← Back to Home</a>
</x-layouts.app>
