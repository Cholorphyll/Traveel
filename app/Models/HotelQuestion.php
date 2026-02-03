<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelQuestion extends Model
{
    protected $table = 'HotelQuestion';

    protected $primaryKey = 'hotelQuestionId';

    protected $fillable = [
        'HotelId',
        'Question',
        'Answer',
        'IsActive',
        'CreatedDate',
        'User_Name',
    ];
    

    public $timestamps = false;
}