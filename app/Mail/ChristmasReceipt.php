<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ChristmasReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $payroll;
    public $companyName;
    public $companyLogo;

    /**
     * Create a new message instance.
     */
    public function __construct($payroll)
    {
        $this->payroll = $payroll;
        $this->companyName = \Auth::user()->company->name ?? config('app.name');
        $this->companyLogo = \Auth::user()->company->logo ? \Storage::disk('s3')->url(\Auth::user()->company->logo) : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprobante de Pago - Salario de Navidad',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payroll.christmas_receipt',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
