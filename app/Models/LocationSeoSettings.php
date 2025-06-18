<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LocationSeoSettings extends Model
{
    protected $table = 'Location';
    
    protected $fillable = [
        'location_id',
        'show_in_index'
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }
}