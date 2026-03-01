<?php

namespace App\Http\Controllers\Chatbot;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\KnowledgeBaseDocument;
use App\Services\AI\DocumentChunkerService;
use App\Services\Chatbot\ChatbotService;

class ChatbotController extends Controller
{
    private DocumentChunkerService $chunkerService;
    private ChatbotService $chatbotService;

    public function __construct(DocumentChunkerService $chunkerService, ChatbotService $chatbotService)
    {
        $this->chunkerService = $chunkerService;
        $this->chatbotService = $chatbotService;
    }

    /**
     * Handle chat messages from the Admin
     */
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            // For API testing, default to company_id = 1 if not provided
            $companyId = $request->input('company_id', 1);
            $reply = $this->chatbotService->ask($companyId, $request->message);

            return $this->successResponse('Success', [
                'reply' => $reply
            ]);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memproses pesan: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Upload a new document to the Knowledge Base
     */
    public function uploadDocument(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,txt|max:10240', // 10MB max
        ]);

        try {
            // For API testing, default to company_id = 1 if not provided
            $companyId = $request->input('company_id', 1);
            $file = $request->file('file');
            
            // Store file
            $path = $file->store('knowledge_base/' . $companyId, 'local');
            
            // Create record
            $document = KnowledgeBaseDocument::create([
                'company_id' => $companyId,
                'filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'type' => $file->getClientOriginalExtension(),
                'status' => 'pending' // chunking might take time, ideal for jobs, but sync for now
            ]);

            // Process in background using Job
            \App\Jobs\ProcessKnowledgeBaseDocument::dispatch($document);

            return $this->successResponse('Dokumen berhasil diunggah. AI sedang mempelajari dokumen Anda di latar belakang.', $document);

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengunggah dokumen: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a Document from DB, file storage, and Pinecone
     */
    public function deleteDocument(Request $request, $id)
    {
        try {
            // For API testing, default to company_id = 1 if not provided
            $companyId = $request->input('company_id', 1);
            $document = KnowledgeBaseDocument::where('company_id', $companyId)->find($id);

            // 1. Delete vectors from Pinecone (Always attempt to clear vector DB to be strongly idempotent)
            $this->chunkerService->deleteDocumentVectors($companyId, $id);
            
            if ($document) {
                // 2. Delete file from storage
                if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                    Storage::disk('local')->delete($document->file_path);
                }

                // 3. Delete DB record
                $document->delete();
            }

            return $this->successResponse('Dokumen berhasil dihapus dari basis pengetahuan.');

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus dokumen: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete All Documents for a given Company
     */
    public function deleteAllDocuments(Request $request)
    {
        try {
            // For API testing, default to company_id = 1 if not provided
            $companyId = $request->input('company_id', 1);
            $documents = KnowledgeBaseDocument::where('company_id', $companyId)->get();

            // 1. Delete all vectors from Pinecone for this company
            $this->chunkerService->deleteAllCompanyVectors($companyId);
            
            foreach ($documents as $document) {
                // 2. Delete file from storage
                if ($document->file_path && Storage::disk('local')->exists($document->file_path)) {
                    Storage::disk('local')->delete($document->file_path);
                }
            }

            // 3. Delete DB records
            KnowledgeBaseDocument::where('company_id', $companyId)->delete();

            return $this->successResponse('Seluruh dokumen berhasil dihapus dari basis pengetahuan perusahaan ini.');

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus seluruh dokumen: ' . $e->getMessage(), 500);
        }
    }
}
