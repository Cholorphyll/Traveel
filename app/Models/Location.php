<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class Location extends Model
{
    use HasFactory;
    use Searchable;

    // Specify the table associated with the model if it's not the plural of the model name
    protected $table = 'Location'; // Change 'locations' to your actual table name if different

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'LocationId'; // Change this if your primary key is different

    // If your primary key is not an integer
    // protected $keyType = 'string'; // Uncomment if your primary key is a string

    // If the primary key is not auto-incrementing
    // public $incrementing = false; // Uncomment if your primary key is not auto-incrementing

    // Specify the fillable fields
    protected $fillable = [
        'slugid', // Add other fields that you want to be mass assignable
        'slug',
        'Name',
        'LocationId',
        // Add other fields as necessary
    ];

    public function hotels()
    {
        return $this->hasMany(Hotel::class, 'slugid', 'slugid');
    }

    public function neighborhoods()
    {
        return $this->hasMany(Neighborhood::class, 'LocationID', 'LocationId');
    }

    public function sights()
    {
        return $this->hasMany(Sight::class, 'Location_id', 'slugid');
    }
}