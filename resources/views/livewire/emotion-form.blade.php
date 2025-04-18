<div class="max-w-xl mx-auto bg-white p-6 rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-bold mb-6 text-center">Emotion Sound - Step {{ $currentStep }}</h2>

    {{-- STEP 1: Personal Info --}}
    @if($currentStep === 1)
    <div class="space-y-4">
        <input type="text" wire:model="name" placeholder="Name"
            class="w-full px-4 py-2 border rounded" />

        <input type="number" wire:model="age" placeholder="Age"
            class="w-full px-4 py-2 border rounded" />

        <select wire:model="gender" class="w-full px-4 py-2 border rounded">
            <option value="">Select gender</option>
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="other">Other</option>
        </select>

        <select wire:model="emotionalState" class="w-full px-4 py-2 border rounded">
            <option value="">How are you feeling right now?</option>
            <option value="happy">Happy</option>
            <option value="sad">Sad</option>
            <option value="calm">Calm</option>
            <option value="angry">Angry</option>
        </select>
    </div>
    @endif

    {{-- STEP 2: Questions --}}
    @if($currentStep === 2)
    <div class="mt-4 space-y-4">
        <h3 class="text-lg font-semibold mb-2">Answer these questions:</h3>

        @foreach($questions as $index => $question)
        <div>
            <label class="block font-medium mb-1">{{ $question }}</label>
            <select wire:model="answers.{{ $index }}" class="w-full px-4 py-2 border rounded">
                <option value="">Choose an answer</option>
                @if(str_contains($question, 'park'))
                <option value="stay">I would stay</option>
                <option value="leave">I would leave</option>
                @elseif(str_contains($question, 'silent room'))
                <option value="peaceful">Peaceful</option>
                <option value="anxious">Anxious</option>
                @elseif(str_contains($question, 'silence, rain, or storm'))
                <option value="silence">Silence</option>
                <option value="rain">Rain</option>
                <option value="storm">Storm</option>
                @elseif(str_contains($question, 'busy environments'))
                <option value="busy">Busy environments</option>
                <option value="calm">Calmness</option>
                @elseif(str_contains($question, 'unexpected changes'))
                <option value="adapt">I adapt quickly</option>
                <option value="panic">I feel stressed</option>
                @endif
            </select>

        </div>
        @endforeach
    </div>
    @endif

    {{-- Button at the bottom --}}
    <div class="mt-6 text-center">
        <button wire:click="submit"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded transition cursor-pointer">
            Next
        </button>
    </div>
</div>