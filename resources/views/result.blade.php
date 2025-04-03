<x-layouts.app>
    <h1 class="text-3xl font-bold mb-4">Your detected emotion:
        <span class="capitalize text-blue-600">{{ $emotion }}</span>
    </h1>

    <audio controls class="mb-6">
        <source src="{{ asset('music/' . $emotion . '.mp3') }}" type="audio/mpeg">
        Your browser does not support the audio element.
    </audio>

    <form method="POST" action="/feedback" class="flex flex-col space-y-4 w-full max-w-md">
        @csrf
        <p class="mb-2 text-center">Do you like the sound?</p>
        <div class="flex justify-center space-x-4">
            <button name="feedback" value="yes" class="bg-green-500 text-white px-4 py-2 rounded">Yes</button>
            <button name="feedback" value="no" class="bg-red-500 text-white px-4 py-2 rounded">No</button>
        </div>
    </form>

    @if(session('show_chat'))
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

                @if(session('new_emotion'))
                    <p class="mt-3">Here's another suggestion for you:</p>
                    <audio controls class="mt-2">
                        <source src="{{ asset('music/' . session('new_emotion') . '.mp3') }}" type="audio/mpeg">
                    </audio>
                @endif
            @endif
        </div>
    @endif
</x-layouts.app>
