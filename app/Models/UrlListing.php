<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UrlListing extends Model
{
    protected $table = 'listings';

    protected $fillable = ['url', 'added_by'];

    /**
     * Get the contents for this URL listing
     */
    public function contents(): HasMany
    {
        return $this->hasMany(UrlContent::class, 'listing_id');
    }

    /**
     * Get the user who added this listing
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
