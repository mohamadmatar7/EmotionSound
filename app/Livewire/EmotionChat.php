<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Str;

class EmotionChat extends Component
{
    public $input = '';
    public $messages = [];
    public $loading = false;
    public $readyToRedirect = false;
    public $redirectUrl = null;

    public $state = [];
    public $questionIndex = 0;

    public $questions = [];

    protected $greetings = ['hi', 'hello', 'hey', 'yo', 'sup', 'greetings', 'good morning', 'good afternoon', 'good evening'];

    public function mount()
    {
        $this->generateQuestionList();
        $this->ask($this->questions[0]['text']);
    }

    public function generateQuestionList()
    {
        $fixed = [
            ['key' => 'name', 'text' => "Hey there! 👋 Can I know your name?"],
            ['key' => 'age', 'text' => "And how many candles on your last birthday cake? 🎂"],
            ['key' => 'mood_today', 'text' => "Alright, quick check-in: how are you feeling right now? 😊"], // Moved here
        ];

        $optional = collect([
            ['key' => 'alone_feel', 'text' => "When you're alone, what does that feel like to you? Do you enjoy your own company, or prefer being with others?"],
            ['key' => 'change_response', 'text' => "Plans suddenly changed! Are you flexible, or does that usually stress you out? How do you typically react when the day doesn’t go as expected?"],
            ['key' => 'music_mood', 'text' => "If I were to play you a song right now, what mood would you want it to match? 🎧"],
            ['key' => 'last_weekend', 'text' => "What did your last weekend feel like — chill, wild, boring, or... something else?"],
            ['key' => 'sleep_quality', 'text' => "How well have you been sleeping lately? That can often tell us a lot about how we feel! 😴"],
            ['key' => 'stress_handle', 'text' => "How do you usually cope when stress hits you?"],
        ])
            ->shuffle()
            ->unique('key')
            ->take(2) // Still take 2 optional questions
            ->values()
            ->all();

        $this->questions = array_merge($fixed, $optional);
    }

    public function send()
    {
        $userText = trim($this->input);
        if (!$userText) return;

        $this->messages[] = ['from' => 'user', 'text' => e($userText)];
        $this->input = '';
        $this->loading = true;

        sleep(1); // Simulate thinking...

        $current = $this->questions[$this->questionIndex];
        $key = $current['key'];

        switch ($key) {
            case 'name':
                $name = $this->parseName($userText);
                if (!$name) {
                    $friendlyRetries = [
                        "Oops 😅 I didn't quite catch your name — mind trying again?",
                        "Hmm, could you please repeat your name? I want to make sure I get it right! 😊",
                        "Just making sure... what should I call you?",
                    ];
                    $this->ask($friendlyRetries[array_rand($friendlyRetries)]);
                    $this->loading = false;
                    return;
                }
                $this->state['name'] = $name;
                $this->ask("Nice to meet you, {$name}! 👋");
                break;

            case 'age':
                $age = $this->parseAge($userText);
                if (!$age) {
                    $this->ask("Hmm, could you give me your age as a number? It helps me understand you better! (between 5 and 110 please).");
                    $this->loading = false;
                    return;
                }
                $this->state['age'] = $age;
                // No specific response here, as the next question is fixed.
                break;

            case 'mood_today':
                $this->state[$key] = $userText;
                $this->say("Thanks for sharing, {$this->state['name']}! That gives me a better idea.");
                break;

            default:
                $this->state[$key] = $userText;
                $this->say("Got it. Thanks for letting me know!"); // More generic, positive response
                break;
        }

        $this->questionIndex++;

        if ($this->questionIndex < count($this->questions)) {
            $this->ask($this->questions[$this->questionIndex]['text']);
        } else {
            $this->finishConversation();
        }

        $this->loading = false;
    }

    public function finishConversation()
    {
        $allText = implode(' ', array_values($this->state));
        $emotion = $this->detectEmotion($allText);

        if (!$emotion) {
            $this->ask("I'm not quite sure how you feel from what you've shared. Could you describe your mood in other words, or tell me more about how you're feeling overall?");
            $this->questionIndex--; // Keep on the last question to try again for emotion detection
            return;
        }

        session([
            'emotion' => $emotion,
            'user_name' => $this->state['name'] ?? 'Guest',
            'user_age' => $this->state['age'] ?? null,
        ]);

        $friendlyEmotion = str_replace('+', ', ', $emotion);
        $this->say("Okay, {$this->state['name']}! Based on our chat, it seems you're feeling <strong>{$friendlyEmotion}</strong>. ✨");
        $this->say("Ready to hear something tailored for your mood? Click the button below! 👇");

        $this->readyToRedirect = true;
        $this->redirectUrl = url('/result');
    }

    public function redirectNow()
    {
        return redirect($this->redirectUrl);
    }

    public function ask($text)
    {
        $this->messages[] = ['from' => 'ai', 'text' => $text];
    }

    public function say($text)
    {
        $this->messages[] = ['from' => 'ai', 'text' => $text];
    }

    private function parseName($text)
    {
        $text = trim(strtolower($text));

        // Reject if it's just a greeting or too short
        if (in_array($text, $this->greetings) || strlen($text) < 2) {
            return null;
        }

        // Common patterns for extracting name
        $patterns = [
            '/^my name is\s+([a-zA-Z\s\'-]+)/i',
            '/^i am\s+([a-zA-Z\s\'-]+)/i',
            '/^call me\s+([a-zA-Z\s\'-]+)/i',
            '/^it\'s\s+([a-zA-Z\s\'-]+)/i',
            '/^i\'m\s+([a-zA-Z\s\'-]+)/i',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $matches)) {
                $name = trim($matches[1]);
                // Ensure extracted name is not a greeting and contains only letters, spaces, hyphens, and apostrophes
                if (strlen($name) >= 2 && preg_match('/^[a-zA-Z\s\'-]+$/', $name) && !in_array($name, $this->greetings)) {
                    return Str::title($name);
                }
            }
        }

        // If no pattern matched, check if the input itself looks like a name
        if (preg_match('/^[a-zA-Z\s\'-]{2,}$/', $text) && !in_array($text, $this->greetings)) {
            return Str::title($text);
        }

        return null;
    }

    private function parseAge($text)
    {
        preg_match('/\d+/', $text, $matches);
        $age = (int) ($matches[0] ?? 0);
        return $age >= 5 && $age <= 110 ? $age : null; // More reasonable age range
    }

    private function detectEmotion($text)
    {
        $text = strtolower($text);

        $emotionGroups = [
            'happy' => ['happy', 'joyful', 'excited', 'cheerful', 'upbeat', 'great', 'good', 'fantastic', 'amazing', 'positive', 'elated', 'optimistic', 'blissful', 'giddy', 'vibrant', 'delighted', 'pleased', 'thrilled', 'content'],
            'sad' => ['sad', 'depressed', 'gloomy', 'down', 'unhappy', 'low', 'melancholy', 'tearful', 'heartbroken', 'despondent', 'discouraged', 'somber', 'blue'],
            'calm' => ['calm', 'peaceful', 'relaxed', 'tranquil', 'serene', 'mellow', 'chill', 'quiet', 'restful', 'composed', 'settled', 'unbothered'],
            'angry' => ['angry', 'mad', 'furious', 'irritated', 'frustrated', 'annoyed', 'pissed', 'resentful', 'infuriated', 'livid', 'enraged'],
            'anxious' => ['anxious', 'worried', 'nervous', 'stressed', 'uneasy', 'tense', 'apprehensive', 'fretting', 'panicked', 'restless', 'on edge'],
            'bored' => ['bored', 'boring', 'tired', 'dull', 'monotonous', 'uninterested', 'listless', 'apathetic', 'weary'],
            'lonely' => ['alone', 'lonely', 'isolated', 'solitary', 'unaccompanied', 'miss company'],
            'nostalgic' => ['nostalgic', 'miss', 'memory', 'past', 'reminisce', 'longing'],
            'intense' => ['intense', 'overwhelmed', 'stressed', 'overstimulated', 'high energy', 'hyper', 'agitated'],
            'hopeful' => ['hopeful', 'motivated', 'optimistic', 'positive', 'aspiring', 'promising', 'encouraged'],
            'ambient' => ['ambient', 'background', 'neutral', 'chilled'],
            'neutral' => ['neutral', 'fine', 'meh', 'okay', 'alright', 'indifferent', 'average'],
            'thoughtful' => ['pensive', 'contemplative', 'reflective', 'thinking', 'pondering'],
            'surprised' => ['surprised', 'shocked', 'amazed', 'stunned'],
        ];

        $scores = [];

        foreach ($emotionGroups as $emotion => $keywords) {
            foreach ($keywords as $keyword) {
                if (Str::contains($text, $keyword)) {
                    $scores[$emotion] = ($scores[$emotion] ?? 0) + 1;
                }
            }
        }

        if (empty($scores)) {
            // Fallback to neutral if no specific emotion detected but some text was provided
            if (!empty($text) && strlen($text) > 5) {
                return 'neutral';
            }
            return null;
        }

        // Apply a basic conflict resolution (e.g., happy/sad cancel out)
        $conflicts = [
            ['happy', 'sad'],
            ['angry', 'calm'],
            ['anxious', 'peaceful'],
            ['excited', 'bored'],
            ['hopeful', 'sad']
        ];

        foreach ($conflicts as [$a, $b]) {
            if (isset($scores[$a]) && isset($scores[$b])) {
                // Remove the one with the lower score, or both if scores are equal
                if ($scores[$a] > $scores[$b]) {
                    unset($scores[$b]);
                } elseif ($scores[$b] > $scores[$a]) {
                    unset($scores[$a]);
                } else { // If scores are equal, remove both for a stronger signal
                    unset($scores[$a], $scores[$b]);
                }
            }
        }

        // Prioritize more specific emotions
        $priorityOrder = ['happy', 'sad', 'angry', 'anxious', 'calm', 'lonely', 'nostalgic', 'intense', 'hopeful', 'surprised', 'thoughtful', 'bored', 'neutral', 'ambient'];
        $finalScores = [];
        foreach ($priorityOrder as $emotion) {
            if (isset($scores[$emotion])) {
                $finalScores[$emotion] = $scores[$emotion];
            }
        }

        if (empty($finalScores)) return null;

        arsort($finalScores); // Sort by highest score

        // Return top 1-3 emotions, joined by '+'
        return implode('+', array_slice(array_keys($finalScores), 0, min(count($finalScores), 3)));
    }


    public function render()
    {
        return view('livewire.emotion-chat');
    }
}
