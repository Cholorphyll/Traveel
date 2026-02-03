<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UrlContent extends Model
{
    use HasFactory;

    protected $table = 'contents';
    
    protected $fillable = [
        'listing_id',
        'hotelid',
        'category',
        'about',
        'amenities',
        'HotelQuestion',
        'HotelAnswer',
        'status',
        'added_by',
        'accepted_by',
        'accepted_at'
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
        'status' => 'string'
    ];

    // Define the enum values for status
    public const STATUSES = [
        'pending' => 'pending',
        'updated' => 'updated',
        'accepted' => 'accepted'
    ];

    public function setStatusAttribute($value)
    {
        if (!array_key_exists($value, self::STATUSES)) {
            $value = 'pending';
        }
        $this->attributes['status'] = self::STATUSES[$value];
    }

    public function listing()
    {
        return $this->belongsTo(UrlListing::class, 'listing_id');
    }

    public function addedBy()
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function acceptedBy()
    {
        return $this->belongsTo(User::class, 'accepted_by');
    }
}
