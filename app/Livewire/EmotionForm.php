<?php

// namespace App\Livewire;

// use Livewire\Component;

// class EmotieForm extends Component
// {
//     public $huidigeStap = 1;
//     public $naam;
//     public $leeftijd;
//     public $geslacht;
//     public $emotioneleStaat;

//     public $vragen = [];
//     public $antwoorden = [];

//     protected $alleVragen = [
//         "Je zit in een park op een zonnige dag. Kinderen beginnen te schreeuwen en rennen rond. Blijf je of ga je weg?",
//         "Wat voel je als je helemaal alleen bent in een stille kamer?",
//         "Kies: stilte, regen of storm — en waarom?",
//         "Krijg je energie van drukte of liever van rust?",
//         "Wat is je eerste reactie bij onverwachte veranderingen?",
//     ];

//     public function mount()
//     {
//         $this->vragen = collect($this->alleVragen)->shuffle()->take(3)->values()->all();
//     }


//     public function submit()
//     {
//         if ($this->huidigeStap === 1) {
//             $this->huidigeStap = 2;
//         } elseif ($this->huidigeStap === 2) {
//             $score = 0;

//             foreach ($this->antwoorden as $antwoord) {
//                 $antwoord = strtolower($antwoord);
//                 if (str_contains($antwoord, 'rust') || str_contains($antwoord, 'stilte')) {
//                     $score += 1;
//                 }
//                 if (str_contains($antwoord, 'weg') || str_contains($antwoord, 'drukte')) {
//                     $score -= 1;
//                 }
//             }

//             $emotie = 'natural';
//             if ($score > 1) $emotie = 'happy';
//             if ($score < 0) $emotie = 'sad';

//             session()->put('emotie', $emotie);
//             return redirect()->to('/result');
//         }
//     }



//     public function render()
//     {
//         return view('livewire.emotie-form');
//     }
// }


namespace App\Livewire;

use Livewire\Component;

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
        } elseif ($this->currentStep === 2) {
            $score = 0;

            foreach ($this->answers as $answer) {
                $answer = strtolower($answer);
                if (str_contains($answer, 'calm') || str_contains($answer, 'silence')) {
                    $score += 1;
                }
                if (str_contains($answer, 'leave') || str_contains($answer, 'busy')) {
                    $score -= 1;
                }
            }

            $emotion = 'neutral';
            if ($score > 1) $emotion = 'happy';
            if ($score < 0) $emotion = 'sad';

            session()->put('emotion', $emotion);
            return redirect()->to('/result');
        }
    }

    public function render()
    {
        return view('livewire.emotion-form');
    }
}
