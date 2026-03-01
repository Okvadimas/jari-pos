<?php

namespace App\AI\Tools;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Tools\Request;

class ServerTimeTool implements Tool
{
    /**
     * Get the description of the tool's purpose.
     */
    public function description(): string
    {
        return 'Dapatkan jam dan waktu server saat ini (WIB) atau (Waktu Indonesia Barat). Alat ini bisa kamu panggil jika ditanya tentang hari ataupun waktu.';
    }

    /**
     * Execute the tool.
     */
    public function handle(Request $request): string
    {
        return 'Waktu sekarang adalah: ' . now()->format('Y-m-d H:i:s l');
    }

    /**
     * Get the tool's schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'timezone' => $schema->string()->description('Timezone location, e.g. "Asia/Jakarta" if not specified.'),
        ];
    }
}
