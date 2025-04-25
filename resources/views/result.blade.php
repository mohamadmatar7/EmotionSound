<x-layouts.app>
    <h1 class="text-2xl font-bold mb-4 text-white">
        Music for: <span class="capitalize text-pink-400">{{ str_replace('+', ', ', $emotion) }}</span>
    </h1>

    @if(isset($preview))
        <p class="mb-2 font-semibold text-white">Now playing: {{ $name }}</p>
        <audio controls autoplay class="mb-6 text-white">
            <source src="{{ $preview }}" type="audio/mpeg">
            Your browser does not support the audio tag.
        </audio>
    @elseif(isset($error))
        <p class="text-red-600">{{ $error }}</p>
    @endif

    {{-- Like/Dislike Buttons --}}
    <form method="POST" action="/feedback" class="flex flex-col space-y-4 w-full max-w-md mt-6">
        @csrf
        <p class="text-center text-white">Do you like the sound?</p>
        <div class="flex justify-center space-x-4">
            <button name="feedback" value="yes" class="bg-green-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-green-600">Yes</button>
            <button name="feedback" value="no" class="bg-red-500 text-white px-4 py-2 rounded cursor-pointer hover:bg-red-600">No</button>
        </div>
    </form>

    {{-- AI Chatbox --}}
    @if(session('chat_response') || session('show_chat'))
        <div class="mt-10 w-full max-w-md ">
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
            

            @if(session('chat_response'))
            <div class="mt-4 p-4 bg-white/10 backdrop-blur-lg border border-white/20 text-white rounded-xl shadow-md">
                <strong class="text-pink-400">🎧 AI:</strong>
                <span class="ml-1 text-white/90">{{ session('chat_response') }}</span>
            </div>
            
            @endif
        </div>
    @endif
</x-layouts.app>
