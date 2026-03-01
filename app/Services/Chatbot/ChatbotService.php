<?php

namespace App\Services\Chatbot;

use App\Services\AI\EmbeddingService;
use Probots\Pinecone\Client as PineconeClient;
use Illuminate\Support\Facades\Log;

class ChatbotService
{
    private PineconeClient $pinecone;
    private EmbeddingService $embeddingService;

    public function __construct(PineconeClient $pinecone, EmbeddingService $embeddingService)
    {
        $this->pinecone = $pinecone;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Ask a question using Retrieval-Augmented Generation (RAG).
     */
    public function ask(int $companyId, string $question): string
    {
        try {
            // 1. Generate embedding for the user's question
            $questionEmbeddingResp = $this->embeddingService->generateEmbeddings($question);
            // Laravel-AI EmbeddingsResponse returns an array of float arrays.
            $embeddingArray = $questionEmbeddingResp->embeddings;
            
            if (empty($embeddingArray)) {
                return "Maaf, saya tidak bisa memproses pertanyaan Anda saat ini.";
            }

            $vectorFloat = $embeddingArray[0];

            // 2. Query Pinecone for similar context
            $pineconeResponse = $this->pinecone->data()->vectors()->query(
                vector: $vectorFloat,
                topK: 5,
                includeMetadata: true,
                filter: [
                    'company_id' => $companyId
                ]
            );

            $matches = $pineconeResponse->json('matches') ?? [];

            // 3. Build Context String
            $contextText = "";
            if (!empty($matches)) {
                foreach ($matches as $match) {
                    if (isset($match['metadata']['text'])) {
                        $contextText .= $match['metadata']['text'] . "\n\n";
                    }
                }
            }

            $userPrompt = "Context Information:\n" . $contextText . "\n\n" . "User Question: " . $question;

            $agent = \App\AI\Agents\ChatbotAgent::make();
            return (string) $agent->prompt($userPrompt);

        } catch (\Exception $e) {
            Log::error('ChatbotService RAG Error: ' . $e->getMessage());
            return "Terjadi kesalahan pada sistem AI kami: " . $e->getMessage();
        }
    }
}
