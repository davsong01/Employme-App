<?php

namespace App\Services\AI\Providers;

use Illuminate\Support\Facades\Http;

class Gemini
{
    protected $apiKey;
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('ai.gemini_api_key', env('GEMINI_API_KEY'));
        $this->model = config('ai.gemini_model', 'gemini-1.5');
    }

    public function ask(string $prompt, array $options = [])
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey
        ])->post('https://api.generativeai.google/v1beta2/models/'.$this->model.':generate', [
            'prompt' => $prompt,
            'max_output_tokens' => $options['max_tokens'] ?? 500,
        ]);

        return $response->json()['candidates'][0]['content'] ?? null;
    }
}