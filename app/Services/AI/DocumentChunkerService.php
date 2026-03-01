<?php

namespace App\Services\AI;

use App\Models\KnowledgeBaseDocument;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Spatie\PdfToText\Pdf;
use Probots\Pinecone\Client as PineconeClient;

class DocumentChunkerService
{
    private PineconeClient $pinecone;
    private EmbeddingService $embeddingService;

    public function __construct(PineconeClient $pinecone, EmbeddingService $embeddingService)
    {
        $this->pinecone = $pinecone;
        $this->embeddingService = $embeddingService;
    }

    /**
     * Parse file, chunk text, generate embeddings, and upsert to Pinecone.
     */
    public function processAndEmbed(KnowledgeBaseDocument $document)
    {
        try {
            $document->update(['status' => 'processing']);
            
            $text = $this->extractText($document);
            if (empty(trim($text))) {
                throw new \Exception("File kosong atau teks tidak bisa diekstrak.");
            }

            $chunks = $this->chunkText($text, 1000); // chunk by ~1000 characters
            if (empty($chunks)) {
                throw new \Exception("Gagal memotong teks dokumen.");
            }

            // Generate embeddings in batch
            $embeddingsResponse = $this->embeddingService->generateEmbeddings($chunks);
            $embeddingsData = $embeddingsResponse->embeddings;

            // Prepare Pinecone Vectors
            $vectors = [];
            foreach ($embeddingsData as $index => $embeddingFloatArray) {
                // Determine ID format
                $vectorId = "doc_{$document->id}_chunk_{$index}";
                
                $vectors[] = [
                    'id' => $vectorId,
                    'values' => $embeddingFloatArray, // the float array
                    'metadata' => [
                        'company_id' => $document->company_id,
                        'doc_id' => $document->id,
                        'chunk_index' => $index,
                        'text' => $chunks[$index] // Store the raw text chunk to context later
                    ]
                ];
            }

            // Upsert vectors to Pinecone
            $this->pinecone->data()->vectors()->upsert(vectors: $vectors);

            $document->update(['status' => 'ready']);

        } catch (\Exception $e) {
            Log::error('DocumentChunkerService: ' . $e->getMessage());
            $document->update([
                'status' => 'error',
                'error_message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete vectors from Pinecone based on doc_id metadata.
     */
    public function deleteDocumentVectors(int $companyId, int $docId)
    {
        try {
            // Pinecone 'delete by metadata' is only available in Serverless index via specific API request.
            // Alternatively, doing a search to find IDs, then delete by IDs. 
            // In Serverless Pinecone, we can pass a filter to delete.
            
            $response = $this->pinecone->data()->vectors()->delete(
                ids: [],
                namespace: null,
                deleteAll: false,
                filter: [
                   'company_id' => $companyId,
                   'doc_id' => $docId
                ]
            );
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('DocumentChunkerService Delete: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Delete all vectors from Pinecone for a specific company
     */
    public function deleteAllCompanyVectors(int $companyId)
    {
        try {
            $response = $this->pinecone->data()->vectors()->delete(
                ids: [],
                namespace: null,
                deleteAll: false,
                filter: [
                   'company_id' => $companyId
                ]
            );
            
            return $response->json();
        } catch (\Exception $e) {
            Log::error('DocumentChunkerService Delete All: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract Raw Text from local storage path.
     */
    private function extractText(KnowledgeBaseDocument $document): string
    {
        $path = Storage::disk('local')->path($document->file_path);
        
        if ($document->type === 'pdf') {
            return Pdf::getText($path);
        } else if ($document->type === 'txt') {
            return file_get_contents($path);
        }
        
        throw new \Exception("Unsupported file type: " . $document->type);
    }

    /**
     * Simple text chunking by overlapping characters
     */
    private function chunkText(string $text, int $chunkSize = 1000, int $overlap = 100): array
    {
        // very basic chunking logic. In production, we'd use tokenizer or sentence boundaries.
        $text = preg_replace('/\s+/', ' ', $text); // normalize whitespaces
        
        $chunks = [];
        $length = strlen($text);
        $start = 0;

        while ($start < $length) {
            $chunk = substr($text, $start, $chunkSize);
            // try not to cut words in half
            if ($start + $chunkSize < $length) {
                // find last space within chunk to keep it clean
                $lastSpace = strrpos($chunk, ' ');
                if ($lastSpace !== false && $lastSpace > $chunkSize * 0.8) {
                    $chunk = substr($text, $start, $lastSpace);
                    $start += $lastSpace - $overlap;
                } else {
                     $start += $chunkSize - $overlap;
                }
            } else {
                 $start += $chunkSize;
            }
            
            $chunks[] = trim($chunk);
        }

        return $chunks;
    }
}
