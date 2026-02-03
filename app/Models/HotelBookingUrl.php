<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Hotel;

class HotelBookingUrl extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'agency_id',
        'price',
        'booking_url',
        'options',
        'last_updated'
    ];

    protected $casts = [
        'options' => 'array',
        'last_updated' => 'datetime',
        'price' => 'decimal:2'
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }
}
