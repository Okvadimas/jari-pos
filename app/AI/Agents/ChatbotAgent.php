<?php

namespace App\AI\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Promptable;

class ChatbotAgent implements Agent, HasTools
{
    use Promptable;

    public function instructions(): string
    {
        return "You are a helpful assistant for a Point of Sales application called JARI POS. Answer the user's question based strictly on the provided context. If the context does not contain the answer, say 'Maaf, saya tidak memiliki informasi tersebut di dalam basis pengetahuan saya.' Give answers in Indonesian. You also have access to tools, feel free to use them if the user asks something related.";
    }

    /**
     * Define the tools available to this agent.
     */
    public function tools(): iterable
    {
        return [
            new \App\AI\Tools\ServerTimeTool(),
        ];
    }
}
