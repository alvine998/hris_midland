<?php

namespace App\Mail;

use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CompanyCredentials extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Company $company,
        public string $plainPassword,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your '.config('app.name').' Account is Ready',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.company-credentials',
            with: [
                'companyName' => $this->company->name,
                'email' => $this->company->email,
                'password' => $this->plainPassword,
            ],
        );
    }
}
