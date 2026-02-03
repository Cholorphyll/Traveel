<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEmailPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_recommendations',
        'hotel_deals',
        'explore_suggestions',
        'weekly_digest',
        'email_frequency',
        'last_email_sent_at',
    ];

    protected $casts = [
        'location_recommendations' => 'boolean',
        'hotel_deals' => 'boolean',
        'explore_suggestions' => 'boolean',
        'weekly_digest' => 'boolean',
        'last_email_sent_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function canSendEmail($type)
    {
        if (!$this->{$type}) {
            return false;
        }

        if (!$this->last_email_sent_at) {
            return true;
        }

        $now = now();
        $lastSent = $this->last_email_sent_at;

        switch ($this->email_frequency) {
            case 'daily':
                return $lastSent->diffInDays($now) >= 1;
            case 'weekly':
                return $lastSent->diffInDays($now) >= 7;
            case 'monthly':
                return $lastSent->diffInDays($now) >= 30;
            default:
                return true;
        }
    }

    public static function getOrCreateForUser($userId)
    {
        return self::firstOrCreate(
            ['user_id' => $userId],
            [
                'location_recommendations' => true,
                'hotel_deals' => true,
                'explore_suggestions' => true,
                'weekly_digest' => true,
                'email_frequency' => 'weekly',
            ]
        );
    }
}
