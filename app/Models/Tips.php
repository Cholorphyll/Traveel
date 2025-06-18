<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tips extends Model
{
    protected $table = 'Tips';
    
    protected $fillable = [
        'username',
        'review',
        'SightId'
    ];
}
