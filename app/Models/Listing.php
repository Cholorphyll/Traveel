<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class Listing extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey  = 'LocationId';
    protected $table = "Location";
    
}
