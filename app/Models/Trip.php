<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TripItem;
use App\Models\User;

class Trip extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'destination_city',
        'destination_country',
        'start_date',
        'end_date',
        'budget',
        'ai_assistant_enabled',
        'notes',
        'settings',
        'reservations',
        'flights',
        'places',
        'itinerary'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'ai_assistant_enabled' => 'boolean',
        'settings' => 'array',
        'reservations' => 'array',
        'flights' => 'array',
        'places' => 'array',
        'itinerary' => 'array',
    ];

    // Example relation hooks for future use
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->morphMany(TripItem::class, 'entity');
    }
}
      


