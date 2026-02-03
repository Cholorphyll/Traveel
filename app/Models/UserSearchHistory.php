<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSearchHistory extends Model
{
    use HasFactory;

    protected $table = 'user_search_history';

    protected $fillable = [
        'user_id',
        'session_id',
        'search_type',
        'location_id',
        'location_name',
        'location_slug',
        'location_slugid',
        'search_query',
        'search_params',
        'ip_address',
        'user_agent',
        'search_count',
        'last_searched_at',
    ];

    protected $casts = [
        'search_params' => 'array',
        'last_searched_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function trackSearch($data)
    {
        $userId = $data['user_id'] ?? (\Illuminate\Support\Facades\Auth::check() ? \Illuminate\Support\Facades\Auth::id() : null);
        $sessionId = \Illuminate\Support\Facades\Session::getId();

        $existing = self::where(function ($query) use ($userId, $sessionId) {
            if ($userId) {
                $query->where('user_id', $userId);
            } else {
                $query->where('session_id', $sessionId);
            }
        })
        ->where('location_id', $data['location_id'] ?? null)
        ->where('search_type', $data['search_type'])
        ->first();

        if ($existing) {
            $updateData = [
                'search_count' => $existing->search_count + 1,
                'last_searched_at' => now(),
                'search_params' => $data['search_params'] ?? null,
            ];
            
            if ($userId && !$existing->user_id) {
                $updateData['user_id'] = $userId;
            }
            
            $existing->update($updateData);
            return $existing;
        }

        return self::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'search_type' => $data['search_type'],
            'location_id' => $data['location_id'] ?? null,
            'location_name' => $data['location_name'] ?? null,
            'location_slug' => $data['location_slug'] ?? null,
            'location_slugid' => $data['location_slugid'] ?? null,
            'search_query' => $data['search_query'] ?? null,
            'search_params' => $data['search_params'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'last_searched_at' => now(),
        ]);
    }
}
