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
        $response = $this->processAsk($companyId, $question, false);
        return is_string($response) ? $response : (string) $response;
    }

    /**
     * Ask a question using Retrieval-Augmented Generation (RAG) and return as a stream.
     */
    public function askStream(int $companyId, string $question)
    {
        return $this->processAsk($companyId, $question, true);
    }

    private function processAsk(int $companyId, string $question, bool $stream = false)
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
                topK: 3,
                includeMetadata: true,
                filter: [
                    'company_id' => $companyId
                ]
            );

            $matches = $pineconeResponse->json('matches') ?? [];

            if (empty($matches)) {
                return "Maaf, saya tidak memiliki informasi mengenai pertanyaan Anda di dalam basis pengetahuan Jari AI 🙏";
            }

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
            $chatProvider = config('ai.chatbot.chat_provider');
            $chatModel = config('ai.chatbot.chat_model');

            if ($stream) {
                return $agent->stream($userPrompt, provider: $chatProvider, model: $chatModel);
            }

            return (string) $agent->prompt($userPrompt, provider: $chatProvider, model: $chatModel);

        } catch (\Exception $e) {
            Log::error('ChatbotService RAG Error: ' . $e->getMessage());
            return "Terjadi kesalahan pada sistem AI kami, Mohon coba lagi beberapa saat lagi 🙏";
        }
    }
}
