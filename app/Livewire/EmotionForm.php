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

    protected $allQuestions = [
        "You're sitting in a park on a sunny day. Children start screaming and running around. Do you stay or do you leave?",
        "What do you feel when you're completely alone in a silent room?",
        "Choose: silence, rain, or storm — and why?",
        "Do you gain energy from busy environments or from calmness?",
        "What's your first reaction to unexpected changes?",
    ];

    public function mount()
    {
        $this->questions = collect($this->allQuestions)->shuffle()->take(3)->values()->all();
    }

    public function submit()
    {
        if ($this->currentStep === 1) {
            $this->currentStep = 2;
            return;
        }

        if ($this->currentStep === 2) {
            // Initialize score map
            $scoreMap = [
                'happy' => 0,
                'sad' => 0,
                'calm' => 0,
                'intense' => 0,
                'ambient' => 0,
            ];

            foreach ($this->answers as $answer) {
                $answer = strtolower($answer);

                if (in_array($answer, ['adapt', 'happy'])) {
                    $scoreMap['happy'] += 1;
                }
                if (in_array($answer, ['leave', 'anxious'])) {
                    $scoreMap['sad'] += 1;
                }
                if (in_array($answer, ['stay', 'peaceful', 'silence', 'calm'])) {
                    $scoreMap['calm'] += 1;
                }
                if (in_array($answer, ['storm', 'panic', 'busy'])) {
                    $scoreMap['intense'] += 1;
                }
                if ($answer === 'rain') {
                    $scoreMap['ambient'] += 1;
                }
            }

            // Include initial emotional state as bonus weight
            if (!empty($this->emotionalState) && isset($scoreMap[$this->emotionalState])) {
                $scoreMap[strtolower($this->emotionalState)] += 1;
            }

            // Sort by score and get top 2 or 3 dominant emotions
            arsort($scoreMap);
            $topEmotions = array_slice(array_keys($scoreMap), 0, 3);

            // Convert to final mood string
            $emotionQuery = implode(' ', $topEmotions);

            session()->put('emotion', $emotionQuery);

            // Save result
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
