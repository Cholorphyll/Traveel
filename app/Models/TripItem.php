<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Trip;

class TripItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'trip_id',
        'entity_id',
        'entity_type',
    ];

    public function trip()
    {
        return $this->belongsTo(Trip::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }
}
