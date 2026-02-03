<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticSitemap extends Model
{
    protected $table = 'static_sitemap'; 
    protected $fillable = ['url'];
}