<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use HasFactory;

    // Specify the table associated with the model if it's not the plural of the model name
    protected $table = 'Neighborhood'; // Change this to your actual table name if different

    // Specify the primary key if it's not 'id'
    protected $primaryKey = 'NeighborhoodId'; // Change this if your primary key is different

    // Specify the fillable fields
    protected $fillable = [
        'Name', // Add other fields that you want to be mass assignable
        'slug',
        // Add other fields as necessary
    ];
}