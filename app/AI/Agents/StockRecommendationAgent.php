<?php

namespace App\AI\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;

class StockRecommendationAgent implements Agent
{
    use Promptable;

    public function instructions(): string
    {
        return "You are an expert retail inventory analyst. Your task is to analyze the provided stock data and recommend a restock quantity and provide a brief reasoning (in Indonesian) for each product.\n\nPlease respond with valid JSON formatting.";
    }
}
