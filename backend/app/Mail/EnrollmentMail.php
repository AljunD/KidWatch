<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EnrollmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guardian;
    public $student;
    public $password;

    public function __construct($guardian, $student, $password)
    {
        $this->guardian = $guardian;
        $this->student = $student;
        $this->password = $password;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'KidWatch Guardian Account Created',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.guardian-welcome',
            with: [
                'guardian' => $this->guardian,
                'student'  => $this->student,
                'password' => $this->password,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
