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
     * Render the Chatbot full-page view
     */
    public function index()
    {
        $companyId = Auth::user()->company_id;
        $documents = KnowledgeBaseDocument::where('company_id', $companyId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('chatbot.index', [
            'title' => 'Asisten Virtual',
            'documents' => $documents,
            'css' => ['resources/css/pages/chatbot/index.css'],
            'js' => ['resources/js/pages/chatbot/index.js'],
        ]);
    }

    /**
     * List Knowledge Base documents for the authenticated user's company
     */
    public function listDocuments()
    {
        try {
            $companyId = Auth::user()->company_id;
            $documents = KnowledgeBaseDocument::where('company_id', $companyId)
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse('Success', $documents);
        } catch (\Exception $e) {
            return $this->errorResponse('Gagal memuat daftar dokumen: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Handle chat messages
     */
    public function ask(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        try {
            $companyId = Auth::user()->company_id;
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
            $companyId = Auth::user()->company_id;
            $file = $request->file('file');
            
            // Store file
            $path = $file->store('knowledge_base/' . $companyId, 'local');
            
            // Create record
            $document = KnowledgeBaseDocument::create([
                'company_id' => $companyId,
                'filename' => $file->getClientOriginalName(),
                'file_path' => $path,
                'type' => $file->getClientOriginalExtension(),
                'status' => 'pending'
            ]);

            // Process in background using Job
            \App\Jobs\ProcessKnowledgeBaseDocument::dispatch($document);

            return $this->successResponse('Dokumen berhasil diunggah. AI sedang mempelajari dokumen Anda.', $document);

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal mengunggah dokumen: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Delete a Document from DB, file storage, and Pinecone
     */
    public function deleteDocument($id, Request $request)
    {
        try {
            $companyId = Auth::user()->company_id ?? $request->company_id;
            $document = KnowledgeBaseDocument::where('company_id', $companyId)->find($id);

            // 1. Delete vectors from Pinecone
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
            $companyId = Auth::user()->company_id ?? $request->company_id;
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

            return $this->successResponse('Seluruh dokumen berhasil dihapus dari basis pengetahuan.');

        } catch (\Exception $e) {
            return $this->errorResponse('Gagal menghapus seluruh dokumen: ' . $e->getMessage(), 500);
        }
    }
}
