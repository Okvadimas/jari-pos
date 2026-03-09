<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $user;
    public string $newPassword;

    public function __construct(User $user, string $newPassword)
    {
        $this->user        = $user;
        $this->newPassword = $newPassword;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reset Kata Sandi - Jari POS',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.reset-password',
        );
    }
}
