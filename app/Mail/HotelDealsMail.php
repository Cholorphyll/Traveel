<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class HotelDealsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $location;
    public $hotels;

    public function __construct($user, $location, $hotels = [])
    {
        $this->user = $user;
        $this->location = $location;
        $this->hotels = $hotels;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Best Hotel Deals in ' . $this->location['name'],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.hotel-deals',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
