<div class="w-full max-w-xl p-6 bg-gradient-to-br from-indigo-900 to-purple-900 rounded-2xl shadow-2xl space-y-4 border border-white/10">

    {{-- Chat Box --}}
    <div id="chatBox" class="space-y-4 max-h-[400px] overflow-y-auto bg-white/10 p-4 rounded-xl scroll-smooth">
        @foreach($messages as $msg)
            @if ($msg['from'] === 'user')
                <div class="flex justify-end">
                    <div class="px-4 py-2 bg-pink-500 text-white text-base rounded-2xl rounded-br-none max-w-[75%] shadow-md">
                        {{ $msg['text'] }}
                    </div> 
                </div>
            @elseif ($msg['from'] === 'ai-button')
                <div class="flex justify-start">
                    <button 
                        wire:click="goToMusic"
                        class="text-base bg-pink-600 hover:bg-pink-700 transition text-white px-4 py-2 rounded-full shadow font-semibold">
                        {{ $msg['text'] }}
                    </button>
                </div>
            @else
                <div class="flex justify-start">
                    <div class="px-4 py-2 bg-blue-500 text-white text-base rounded-2xl rounded-bl-none max-w-[75%] shadow-md">
                        {!! $msg['text'] !!}
                    </div>
                </div>
            @endif
        @endforeach

        @if ($loading)
            <div class="flex justify-start">
                <div class="px-4 py-2 bg-blue-500 text-white text-base rounded-2xl rounded-bl-none max-w-[75%] shadow animate-pulse">
                    <span class="italic opacity-80">AI is typing...</span>
                </div>
            </div>
        @endif
    </div>

    {{-- Input Field --}}
    <form wire:submit.prevent="send" class="flex gap-2 pt-2">
        <input wire:model="input" type="text" placeholder="Type your response..."
               class="flex-1 px-4 py-2 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/20 focus:outline-none focus:ring-2 focus:ring-pink-400"
               @disabled($loading)>
        <button type="submit" 
                class="bg-gradient-to-r from-pink-500 to-pink-600 text-white px-4 py-2 rounded-lg font-semibold hover:from-pink-600 hover:to-pink-700 transition cursor-pointer shadow-lg focus:outline-none focus:ring-2 focus:ring-pink-400"
                @disabled($loading)>
            Send
        </button>
    </form>

    {{-- Play Music CTA (optional override) --}}
    @if ($readyToRedirect)
        <div class="flex justify-center mt-4">
            <button wire:click="redirectNow"
                    class="bg-gradient-to-r from-pink-500 to-purple-500 hover:from-pink-600 hover:to-purple-600 transition text-white font-semibold px-6 py-3 rounded-full shadow-lg">
🎵 Play Music for {{ ucfirst($state['name'] ?? 'you') }}
            </button>
        </div>
    @endif
</div>

{{-- Final Scroll Fix --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const chatBox = document.getElementById('chatBox');

        if (!chatBox) return;

        // Scroll function
        const scrollToBottom = () => {
            chatBox.scrollTop = chatBox.scrollHeight;
        };

        // Create MutationObserver
        const observer = new MutationObserver(() => {
            scrollToBottom();
        });

        // Watch changes inside chatBox
        observer.observe(chatBox, {
            childList: true,
            subtree: true
        });

        // Initial scroll
        scrollToBottom();
    });
</script>

