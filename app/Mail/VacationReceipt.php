<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class VacationReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $vacation;
    public $payroll;
    public $companyName;
    public $companyLogo;

    /**
     * Create a new message instance.
     */
    public function __construct($vacation, $payroll)
    {
        $this->vacation = $vacation;
        $this->payroll = $payroll;
        $company = $vacation->company ?? (\Auth::user()->company ?? null);
        $this->companyName = $company->name ?? config('app.name');
        $this->companyLogo = ($company && $company->logo) ? \Storage::disk('s3')->url($company->logo) : null;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprobante de Pago de Vacaciones - ' . ($this->companyName ?? config('app.name')),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payroll.vacation_receipt',
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
