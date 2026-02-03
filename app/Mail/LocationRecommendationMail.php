<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LocationRecommendationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $location;
    public $hotels;
    public $attractions;
    public $restaurants;

    public function __construct($user, $location, $hotels = [], $attractions = [], $restaurants = [])
    {
        $this->user = $user;
        $this->location = $location;
        $this->hotels = $hotels;
        $this->attractions = $attractions;
        $this->restaurants = $restaurants;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Explore ' . $this->location['name'] . ' - Personalized Recommendations',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.location-recommendation',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
