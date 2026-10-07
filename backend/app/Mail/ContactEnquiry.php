<?php

namespace App\Mail;

use App\Models\Enquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Enquiry $enquiry, public string $siteName) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            replyTo: [new Address($this->enquiry->email, $this->enquiry->name)],
            subject: $this->siteName.' enquiry: '.$this->enquiry->enquiry_type_label.($this->enquiry->interest ? ' — '.$this->enquiry->interest : ''),
        );
    }

    public function content(): Content
    {
        return new Content(
            html: 'emails.contact-enquiry-html',
            text: 'emails.contact-enquiry',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
