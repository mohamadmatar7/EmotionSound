<div class="w-full mx-auto bg-white/10 backdrop-blur-lg text-white p-10 rounded-2xl shadow-2xl mt-2 space-y-4 text-lg">

    {{-- Introductie tekst --}}
    <div class="text-center space-y-2">
        <h1 class="text-4xl font-extrabold text-white drop-shadow-md">Welcome to Emotion Sound</h1>
        <p class="text-white/80 text-lg max-w-2xl mx-auto">
            This project helps you discover music based on your emotions. Answer a few short questions and we’ll generate a unique sound experience tailored to your mood.
        </p>
    </div>

    {{-- Step Title --}}
    <h2 class="text-3xl font-bold text-center tracking-wide flex items-center justify-center gap-2 mt-6">
        🎧 Step {{ $currentStep }}
    </h2>

    {{-- STEP 1 --}}
    @if($currentStep === 1)
        <div class="space-y-5">
            {{-- Name --}}
            <div>
                <input type="text" wire:model="name" placeholder="Name"
                    class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/20 focus:ring-2 focus:ring-pink-400 focus:outline-none" />
                @error('name') <p class="text-pink-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Age --}}
            <div>
                <input type="number" wire:model="age" placeholder="Age" min="6" max="100"
                    class="w-full px-4 py-3 rounded-lg bg-white/20 text-white placeholder-gray-300 border border-white/20 focus:ring-2 focus:ring-pink-400 focus:outline-none" />
                @error('age') <p class="text-pink-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Gender --}}
            <div>
                <x-dropdown label="Select gender" model="gender" :options="['male' => 'Male', 'female' => 'Female', 'other' => 'Other']" />
                @error('gender') <p class="text-pink-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Emotion --}}
            <div>
                <x-dropdown label="How are you feeling right now?" model="emotionalState" :options="[
                    'happy' => 'Happy',
                    'sad' => 'Sad',
                    'calm' => 'Calm',
                    'angry' => 'Angry',
                ]" />
                @error('emotionalState') <p class="text-pink-400 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
    @endif

    {{-- STEP 2 --}}
    @if($currentStep === 2)
        <div class="space-y-6">
            <h3 class="text-2xl font-semibold text-pink-400">Answer these questions:</h3>
    
            @foreach($questions as $index => $q)
                <div class="space-y-1">
                    <label class="block font-medium text-white/80 text-base">
                        {{ $q['text'] }}
                    </label>
                    <x-dropdown
                        label="Select an answer"
                        model="answers.{{ $index }}"
                        :options="$q['options']"
                    />
                    @error("answers.$index")
                        <p class="text-pink-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            @endforeach
        </div>
    @endif
    

    {{-- Submit --}}
    <div class="text-center pt-4">
        <button wire:click="submit"
            class="bg-gradient-to-r from-pink-500 via-purple-600 to-blue-500 hover:from-pink-600 hover:to-indigo-600 text-white font-semibold px-8 py-3 rounded-xl shadow-lg transition duration-300 text-lg cursor-pointer">
            Next
        </button>
    </div>
</div>
