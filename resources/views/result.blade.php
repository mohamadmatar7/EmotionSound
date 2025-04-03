<x-layouts.app>
    <h1 class="text-2xl font-bold mb-4">
        Music for: <span class="capitalize text-blue-600">{{ str_replace('+', ', ', $emotion) }}</span>
    </h1>

    @if(isset($preview))
        <p class="mb-2 font-semibold">Track: {{ $name }}</p>
        <audio controls autoplay class="mb-6">
            <source src="{{ $preview }}" type="audio/mpeg">
            Your browser does not support the audio tag.
        </audio>
    @elseif(isset($error))
        <p class="text-red-600">{{ $error }}</p>
    @endif

    {{-- Like/Dislike Buttons --}}
    <form method="POST" action="/feedback" class="flex flex-col space-y-4 w-full max-w-md mt-6">
        @csrf
        <p class="text-center">Do you like the sound?</p>
        <div class="flex justify-center space-x-4">
            <button name="feedback" value="yes" class="bg-green-500 text-white px-4 py-2 rounded">Yes</button>
            <button name="feedback" value="no" class="bg-red-500 text-white px-4 py-2 rounded">No</button>
        </div>
    </form>

    {{-- AI Chatbox --}}
    @if(session('chat_response') || session('show_chat'))
        <div class="mt-10 w-full max-w-md">
            <form method="POST" action="/chat-reply" class="bg-white p-4 rounded shadow">
                @csrf
                <label class="block font-medium mb-2">Tell us what you feel or want:</label>
                <input type="text" name="user_message" class="w-full px-4 py-2 border rounded mb-3" placeholder="e.g. I want something more relaxing..." required>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Send</button>
            </form>

            @if(session('chat_response'))
                <div class="mt-4 p-3 bg-gray-100 rounded">
                    <strong>AI:</strong> {{ session('chat_response') }}
                </div>
            @endif
        </div>
    @endif
</x-layouts.app>
