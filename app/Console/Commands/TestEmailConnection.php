<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;
use Symfony\Component\Mailer\Transport\Smtp\Stream\SocketStream;

class TestEmailConnection extends Command
{
    protected $signature = 'mail:test {--to= : Email address to send test email to}';
    protected $description = 'Test SMTP email server connection and optionally send a test email';

    public function handle(): int
    {
        $this->info('===========================================');
        $this->info('  📧 Email Server Connection Test');
        $this->info('===========================================');
        $this->newLine();

        // Display current config
        $host     = config('mail.mailers.smtp.host');
        $port     = config('mail.mailers.smtp.port');
        $username = config('mail.mailers.smtp.username');
        $scheme   = config('mail.mailers.smtp.scheme');
        $from     = config('mail.from.address');
        $fromName = config('mail.from.name');
        $mailer   = config('mail.default');

        $this->info('📋 Current Mail Configuration:');
        $this->table(
            ['Setting', 'Value'],
            [
                ['Default Mailer', $mailer],
                ['SMTP Host', $host],
                ['SMTP Port', $port],
                ['SMTP Username', $username],
                ['Encryption', $scheme ?: 'auto'],
                ['From Address', $from],
                ['From Name', $fromName],
            ]
        );
        $this->newLine();

        // Step 1: Test SMTP Connection
        $this->info('🔌 Step 1: Testing SMTP Connection...');

        try {
            $encryption = $scheme;
            if (!$encryption) {
                // Auto-detect based on port
                $encryption = match ((int) $port) {
                    465 => 'smtps',
                    587 => 'tls',
                    default => '',
                };
            }

            $useTls = in_array($encryption, ['ssl', 'smtps', 'tls']);

            $transport = new EsmtpTransport(
                host: $host,
                port: (int) $port,
                tls: $useTls,
            );

            $transport->setUsername($username);
            $transport->setPassword(config('mail.mailers.smtp.password'));

            // Try to start the transport (establishes connection)
            $transport->start();
            $transport->stop();

            $this->info('  ✅ SMTP connection successful!');
            $this->newLine();
        } catch (\Throwable $e) {
            $this->error('  ❌ SMTP connection FAILED!');
            $this->error('  Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('💡 Troubleshooting tips:');
            $this->line('  - Verify MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD in .env');
            $this->line('  - Check if the mail server allows connections from your IP');
            $this->line('  - For port 465, ensure SSL/TLS is configured');
            $this->line('  - For port 587, ensure STARTTLS is configured');
            $this->line('  - Check firewall/network connectivity');
            return self::FAILURE;
        }

        // Step 2: Send test email
        $to = $this->option('to');
        if (!$to) {
            $to = $this->ask('📨 Enter email address to send test email (or press Enter to skip)');
        }

        if (empty($to)) {
            $this->warn('⏩ Skipping test email send.');
            $this->newLine();
            $this->info('✅ SMTP connection test passed! Server is reachable.');
            return self::SUCCESS;
        }

        $this->info("🚀 Step 2: Sending test email to {$to}...");

        // Force SMTP mailer for this test
        $previousMailer = config('mail.default');

        try {
            config(['mail.default' => 'smtp']);

            Mail::raw(
                "Ini adalah email test dari JariPOS.\n\n" .
                "Jika Anda menerima email ini, berarti konfigurasi email server sudah benar.\n\n" .
                "Detail Konfigurasi:\n" .
                "- Host: {$host}\n" .
                "- Port: {$port}\n" .
                "- Username: {$username}\n" .
                "- Encryption: " . ($scheme ?: 'auto') . "\n" .
                "- Waktu Pengiriman: " . now()->format('d M Y H:i:s') . "\n\n" .
                "-- JariPOS System",
                function (Message $message) use ($to) {
                    $message->to($to)
                            ->subject('🧪 Test Email - JariPOS Email Server Connection');
                }
            );

            $this->info('  ✅ Test email sent successfully!');
            $this->newLine();
            $this->info('==========================================');
            $this->info('  ✅ All tests passed!');
            $this->info('==========================================');
            $this->newLine();
            $this->line("📬 Check inbox of: {$to}");

            if ($previousMailer === 'log') {
                $this->newLine();
                $this->warn('⚠️  Note: MAIL_MAILER in .env is set to "log".');
                $this->warn('   Change to "smtp" when ready for production:');
                $this->line('   MAIL_MAILER=smtp');
            }

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('  ❌ Failed to send test email!');
            $this->error('  Error: ' . $e->getMessage());
            $this->newLine();

            if (str_contains($e->getMessage(), 'authentication')) {
                $this->warn('💡 Authentication error - check MAIL_USERNAME and MAIL_PASSWORD');
            }

            return self::FAILURE;
        } finally {
            // Restore original mailer
            config(['mail.default' => $previousMailer]);
        }
    }
}
