<?php

namespace App\Mail;

use App\Models\UserRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UserRequestReviewed extends Mailable
{
    use Queueable, SerializesModels;

    public $userRequest;
    public $companyName;
    public $companyLogo;

    /**
     * Create a new message instance.
     */
    public function __construct(UserRequest $userRequest)
    {
        $this->userRequest = $userRequest;
        $this->companyName = \Auth::user()->company->name ?? config('app.name');
        $this->companyLogo = \Auth::user()->company->logo ? \Storage::disk('s3')->url(\Auth::user()->company->logo) : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Actualización de tu Solicitud',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.requests.reviewed',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
