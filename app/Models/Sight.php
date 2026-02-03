<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Sight extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Searchable;
    
    public $timestamps = false;
    protected $primaryKey = 'SightId';
    protected $table = "Sight";
    
    protected $fillable = [
        'Title',
        'Slug',
        'MetaTagTitle',
        'MetaTagDescription',
        'About',
        'short_description',
        'Address',
        'Neighbourhood',
        'LocationId',
        'Pincode',
        'Latitude',
        'Longitude',
        'Website',
        'Phone',
        'Email',
        'IsMustSee',
        'IsActive',
        'IsLocationUpdated',
        'CreatedOn',
        'duration',
        'NearestStation',
        'GetIn',
    	'Likes',
        'Dislikes'
    ];
}
