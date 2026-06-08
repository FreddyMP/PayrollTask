<?php

namespace App\Mail;

use App\Models\Payroll;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PayrollReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $payroll;
    public $companyName;
    public $companyLogo;

    /**
     * Create a new message instance.
     */
    public function __construct(Payroll $payroll)
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
            subject: 'Volante de Pago - ' . $this->payroll->period,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.payroll.receipt',
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
