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
        'category',
        'content_text',
        'status',
        'added_by',
        'accepted_by',
        'accepted_at',
        'HotelQuestion',
        'HotelAnswer'
    ];

    protected $casts = [
        'accepted_at' => 'datetime'
    ];

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
