<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$agent = \App\AI\Agents\ChatbotAgent::make();
try {
    $response = $agent->prompt('Sekarang jam berapa? Coba cek waktu servermu.');
    echo "Response: " . (string) $response . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
