<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EmotionResult;

class EmotionForm extends Component
{
    public $currentStep = 1;
    public $name;
    public $age;
    public $gender;
    public $emotionalState;

    public $questions = [];
    public $answers = [];

    protected $rules = [
        'name' => 'required|string|min:2',
        'age' => 'required|integer|min:1|max:99',
        'gender' => 'required|string',
        'emotionalState' => 'required|string',
        'answers.*' => 'required|string',
    ];

    protected $allQuestions = [
        [
            'text' => "You are sitting in a sunny park and children start shouting nearby. What do you do?",
            'options' => ['stay' => 'Stay and enjoy the atmosphere', 'leave' => 'Move to a quieter place'],
        ],
        [
            'text' => "How do you feel when you are alone in a quiet room?",
            'options' => ['peaceful' => 'Peaceful and calm', 'anxious' => 'Lonely or anxious'],
        ],
        [
            'text' => "Which sound makes you feel most comfortable: silence, soft rain, or a thunderstorm?",
            'options' => ['silence' => 'Silence', 'rain' => 'Gentle rain', 'storm' => 'A thunderstorm'],
        ],
        [
            'text' => "What type of environment gives you more energy?",
            'options' => ['busy' => 'Busy and social places', 'calm' => 'Calm and quiet places'],
        ],
        [
            'text' => "How do you usually respond when your plans suddenly change?",
            'options' => ['adapt' => 'I adapt easily', 'panic' => 'I get stressed or unsettled'],
        ],
    ];

    public function mount()
    {
        $this->questions = collect($this->allQuestions)->shuffle()->take(5)->values()->all();
    }


    public function submit()
    {
        if ($this->currentStep === 1) {
            $this->validateOnly('name');
            $this->validateOnly('age');
            $this->validateOnly('gender');
            $this->validateOnly('emotionalState');
            $this->currentStep = 2;
            return;
        }

        if ($this->currentStep === 2) {
            // ❗ Reset alle errors eerst om te voorkomen dat oude blijven hangen
            $this->resetErrorBag();

            // Check of ALLE antwoorden zijn ingevuld
            foreach ($this->questions as $index => $q) {
                if (!isset($this->answers[$index]) || empty($this->answers[$index])) {
                    $this->addError("answers.$index", 'Please select an answer for each question.');
                }
            }

            // Als er fouten zijn, stop de submit
            if ($this->getErrorBag()->isNotEmpty()) {
                return;
            }

            // 👉 Analyse & opslaan
            $scoreMap = [
                'happy' => 0,
                'sad' => 0,
                'calm' => 0,
                'intense' => 0,
                'ambient' => 0,
            ];

            foreach ($this->answers as $answer) {
                $answer = strtolower($answer);
                if (in_array($answer, ['adapt', 'happy'])) $scoreMap['happy']++;
                if (in_array($answer, ['leave', 'anxious'])) $scoreMap['sad']++;
                if (in_array($answer, ['stay', 'peaceful', 'silence', 'calm'])) $scoreMap['calm']++;
                if (in_array($answer, ['storm', 'panic', 'busy'])) $scoreMap['intense']++;
                if ($answer === 'rain') $scoreMap['ambient']++;
            }

            if (!empty($this->emotionalState) && isset($scoreMap[$this->emotionalState])) {
                $scoreMap[$this->emotionalState]++;
            }

            arsort($scoreMap);
            $topEmotions = array_slice(array_keys($scoreMap), 0, 3);
            $emotionQuery = implode(' ', $topEmotions);

            session()->put('emotion', $emotionQuery);

            $result = EmotionResult::create([
                'name' => $this->name,
                'age' => $this->age,
                'gender' => $this->gender,
                'emotional_state' => $this->emotionalState,
                'answers' => json_encode($this->answers),
                'final_emotion' => $emotionQuery,
            ]);

            return redirect()->to('/report/' . $result->id);
        }
    }



    public function render()
    {
        return view('livewire.emotion-form');
    }
}
