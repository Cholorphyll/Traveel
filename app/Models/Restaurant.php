<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Restaurant extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $primaryKey  = 'RestaurantId';
    protected $table = "Restaurant";
}
