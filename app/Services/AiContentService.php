<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class AiContentService
{
    private string $host;
    private string $model;

    public function __construct()
    {
        $this->host  = config('services.ollama.host', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama3');
    }

    /**
     * Generate ad copy for a product.
     * Returns the raw generated text.
     */
    public function generateAdCopy(array $product, string $channelType, string $extraContext = ''): string
    {
        $prompt = $this->buildAdPrompt($product, $channelType, $extraContext);

        return $this->generate($prompt);
    }

    /**
     * Improve a product description for a specific channel.
     */
    public function improveDescription(array $product, string $channelType): string
    {
        $prompt = "Rewrite the following product description to be compelling and optimised for {$channelType}. "
            . "Keep it factual. Product: {$product['title']}. "
            . "Current description: {$product['description']}";

        return $this->generate($prompt);
    }

    private function buildAdPrompt(array $product, string $channelType, string $extra): string
    {
        $lines = [
            "Write a short, persuasive advertisement for the following product to be used on {$channelType}.",
            "Product name: {$product['title']}",
            "Price: " . (isset($product['price']) ? "€{$product['price']}" : 'not specified'),
            "Description: " . ($product['description'] ?? 'no description'),
        ];

        if ($extra) {
            $lines[] = "Additional context: {$extra}";
        }

        $lines[] = "The ad should be concise (2-3 sentences), highlight the key benefit, and include a call to action.";

        return implode("\n", $lines);
    }

    private function generate(string $prompt): string
    {
        $response = Http::timeout(60)->post("{$this->host}/api/generate", [
            'model'  => $this->model,
            'prompt' => $prompt,
            'stream' => false,
        ]);

        if (! $response->successful()) {
            throw new \RuntimeException('Ollama request failed: ' . $response->status());
        }

        return trim($response->json('response', ''));
    }
}
