<?php
namespace App\Services\AI\Providers;

use Illuminate\Support\Facades\Http;

class OpenAI
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
        $this->model = 'gpt-4.1-mini';
    }

    public function ask(string $prompt, array $options = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $this->model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => $options['temperature'] ?? 0.7,
        ]);

        return $response['choices'][0]['message']['content'] ?? null;
    }
}