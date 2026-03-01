<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\KnowledgeBaseDocument;
use App\Services\AI\DocumentChunkerService;
use Illuminate\Support\Facades\Log;

class ProcessKnowledgeBaseDocument implements ShouldQueue
{
    use Queueable;

    protected KnowledgeBaseDocument $document;

    /**
     * Create a new job instance.
     */
    public function __construct(KnowledgeBaseDocument $document)
    {
        $this->document = $document;
    }

    /**
     * Execute the job.
     */
    public function handle(DocumentChunkerService $chunkerService): void
    {
        try {
            $chunkerService->processAndEmbed($this->document);
        } catch (\Exception $e) {
            Log::error('Job ProcessKnowledgeBaseDocument Error: ' . $e->getMessage());
            
            // Re-throw so Laravel's queue worker knows it failed, though DocumentChunkerService already handles local status update
            throw $e;
        }
    }
}
