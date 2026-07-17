<?php

namespace App\Http\Controllers\AI;

use Illuminate\Http\Request;
use App\Services\AI\AIService;
use App\Models\AIRequest;
use App\Models\Material;
use Illuminate\Support\Facades\Cache;
use Smalot\PdfParser\Parser; // PDF parser package
use App\Http\Controllers\Controller;

class AIController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function ask(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
            'program_id' => 'nullable|integer|exists:programs,id',
            'use_case' => 'required|string',
        ]);

        $provider = $request->provider ?? config('ai.default_provider', 'gemini');

        // 1️⃣ Rate limit
        $limitPerDay = 5;
        $requestsToday = AIRequest::where('user_id', auth()->id())
            ->whereDate('created_at', now())
            ->count();

        if ($requestsToday >= $limitPerDay) {
            return response()->json(['answer' => "Daily AI limit reached"], 429);
        }

        // 2️⃣ RAG: extract PDF content
        $material = Material::findOrFail($request->material_id);
        $pdfPath = storage_path('app/' . $material->filename);

        $parser = new Parser();
        $pdf = $parser->parseFile($pdfPath);
        $text = $pdf->getText();

        // optional: split into paragraphs, take relevant chunks
        $keywords = explode(' ', $request->prompt);
        $paragraphs = explode("\n", $text);
        $relevantChunks = collect($paragraphs)->filter(function($para) use ($keywords) {
            foreach($keywords as $kw) {
                if (stripos($para, $kw) !== false) return true;
            }
            return false;
        })->take(5);

        $context = implode("\n", $relevantChunks);
        $finalPrompt = "You are an AI tutor. Use ONLY the lesson content below. Do NOT answer test questions.\n\n$context\n\nStudent question: ".$request->prompt;

        // 3️⃣ Cache repeated prompts
        $cacheKey = 'ai_prompt_' . md5(auth()->id() . $provider . $request->prompt);
        if (Cache::has($cacheKey)) {
            $responseText = Cache::get($cacheKey);
        } else {
            $responseText = $this->aiService->ask($provider, $finalPrompt);
            Cache::put($cacheKey, $responseText, now()->addHours(12));
        }

        // 4️⃣ Log request
        AIRequest::create([
            'user_id' => auth()->id(),
            'provider' => $provider,
            'prompt' => $request->prompt,
            'response' => $responseText,
            'material_id' => $material->id,
        ]);

        return response()->json(['answer' => $responseText]);
    }
}
