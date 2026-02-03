<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NearByHotel extends Model
{
    use HasFactory;

    protected $table = 'NearByHotel';
    
    // Disable timestamps if the table doesn't have created_at and updated_at
    public $timestamps = false;
    
    protected $fillable = [
        'hotelid',
        'slugid'
    ];

    /**
     * Get the hotel that belongs to this nearby hotel record
     */
    public function hotel()
    {
        return $this->belongsTo(Hotel::class, 'hotelid', 'hotelid');
    }

    /**
     * Get the location that belongs to this nearby hotel record
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'slugid', 'slugid');
    }
}
