<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ==== AI & Chatbot Endpoints (For Testing via Insomnia) ==== //

use App\Http\Controllers\Report\RecommendationController;
use App\Http\Controllers\Chatbot\ChatbotController;

// Feature 1: Stock Recommendation
Route::post('/stock-recommendation/ai-analyse/{historyId}', [RecommendationController::class, 'getAiRecommendations']);

// Feature 2: RAG Chatbot
Route::prefix('chatbot')->group(function () {
    Route::post('/ask', [ChatbotController::class, 'ask']);
    Route::post('/upload', [ChatbotController::class, 'uploadDocument']);
    Route::delete('/document/{id}', [ChatbotController::class, 'deleteDocument']);
    Route::delete('/documents', [ChatbotController::class, 'deleteAllDocuments']);
});
