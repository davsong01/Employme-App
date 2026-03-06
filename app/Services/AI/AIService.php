<?php
namespace App\Services\AI;

use App\Services\AI\Providers\Gemini;
use App\Services\AI\Providers\OpenAI;

class AIService
{
    protected $providers = [];

    public function __construct()
    {
        $this->providers = [
            'gemini' => new Gemini(),
            'openai' => new OpenAI(),
            // 'local' => new Local(),
        ];
    }

    public function ask(string $providerSlug, string $prompt, array $options = [])
    {
        if (!isset($this->providers[$providerSlug])) {
            throw new \Exception("AI provider {$providerSlug} not available");
        }

        return $this->providers[$providerSlug]->ask($prompt, $options);
    }
}