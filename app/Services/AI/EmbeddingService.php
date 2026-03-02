<?php

namespace App\Services\AI;

use Laravel\Ai\Ai;
use Laravel\Ai\Embeddings;
use Illuminate\Support\Facades\Log;

class EmbeddingService
{


    /**
     * Generate vector embeddings for a given text
     *
     * @param string|array $text Single string or array of strings to embed
     */
    public function generateEmbeddings(string|array $text)
    {
        try {
             // Convert string to array as Embeddings::for requires array
             $inputs = is_string($text) ? [$text] : $text;
             return Embeddings::for($inputs)->generate();
        } catch (\Exception $e) {
            Log::error('EmbeddingService Error (Embeddings): ' . $e->getMessage());
            throw $e;
        }
    }
}
