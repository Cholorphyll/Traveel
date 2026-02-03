<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserSearchHistory;
use App\Models\EmailNotification;
use App\Models\UserEmailPreference;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmailNotificationService
{
    public function generateNotificationsForUsers()
    {
        // Get users from FrontendUserLogin who have search history
        $userIds = DB::table('user_search_history')
            ->whereNotNull('user_id')
            ->distinct()
            ->pluck('user_id');

        $users = DB::table('FrontendUserLogin')
            ->whereIn('UserId', $userIds)
            ->whereNotNull('Email')
            ->where('IsActive', 1)
            ->get();

        $notificationsCreated = 0;

        foreach ($users as $user) {
            try {
                // Check email preferences
                $preferences = DB::table('user_email_preferences')
                    ->where('user_id', $user->UserId)
                    ->first();
                
                if (!$preferences || !$preferences->location_recommendations) {
                    continue;
                }
                
                // Check if enough time has passed since last email
                if ($preferences->last_email_sent_at && 
                    now()->diffInDays($preferences->last_email_sent_at) < 7) {
                    continue;
                }

                $topSearches = $this->getUserTopSearches($user->UserId);

                foreach ($topSearches as $search) {
                    $existingNotification = EmailNotification::where('user_id', $user->UserId)
                        ->where('location_id', $search->location_id)
                        ->where('status', 'pending')
                        ->where('created_at', '>=', now()->subDays(7))
                        ->first();

                    if ($existingNotification) {
                        continue;
                    }

                    $locationData = $this->getLocationData($search);
                    $hotels = $this->getTopHotels($search->location_slugid ?? $search->location_id);
                    $attractions = $this->getTopAttractions($search->location_slugid ?? $search->location_id);
                    $restaurants = $this->getTopRestaurants($search->location_slugid ?? $search->location_id);

                    $notificationType = $search->search_type === 'hotels' ? 'hotel_deals' : 'location_recommendations';

                    EmailNotification::create([
                        'user_id' => $user->UserId,
                        'email' => $user->Email,
                        'notification_type' => $notificationType,
                        'location_id' => $search->location_id,
                        'location_name' => $search->location_name,
                        'location_slug' => $search->location_slug,
                        'email_data' => [
                            'location' => $locationData,
                            'hotels' => $hotels,
                            'attractions' => $attractions,
                            'restaurants' => $restaurants,
                        ],
                        'status' => 'pending',
                        'scheduled_at' => now()->addHours(rand(1, 24)),
                    ]);

                    $notificationsCreated++;
                }
            } catch (\Exception $e) {
                Log::error('Error generating notification for user ' . $user->UserId . ': ' . $e->getMessage());
            }
        }

        return $notificationsCreated;
    }

    protected function getUserTopSearches($userId, $limit = 3)
    {
        return UserSearchHistory::where('user_id', $userId)
            ->where('last_searched_at', '>=', now()->subDays(30))
            ->orderBy('search_count', 'desc')
            ->orderBy('last_searched_at', 'desc')
            ->limit($limit)
            ->get();
    }

    protected function getLocationData($search)
    {
        return [
            'id' => $search->location_id,
            'name' => $search->location_name,
            'slug' => $search->location_slug,
            'slugid' => $search->location_slugid,
        ];
    }

    protected function getTopHotels($locationId, $limit = 5)
    {
        try {
            $hotels = DB::table('TPHotel')
                ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                ->where(function($query) use ($locationId) {
                    $query->where('TPHotel.location_id', $locationId)
                          ->orWhere('TPLocations.locationId', $locationId);
                })
                ->select(
                    'TPHotel.id',
                    'TPHotel.name as Title',
                    'TPHotel.slug as Slug',
                    'TPHotel.rating as Averagerating',
                    'TPHotel.number_of_reviews as ReviewCount',
                    'TPHotel.address as Address',
                    'TPHotel.star_rating as StarRating'
                )
                ->where('TPHotel.rating', '>', 0)
                ->orderBy('TPHotel.rating', 'desc')
                ->orderBy('TPHotel.number_of_reviews', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return array_map(function($hotel) {
                return (array) $hotel;
            }, $hotels);
        } catch (\Exception $e) {
            Log::error('Error fetching hotels: ' . $e->getMessage());
            return [];
        }
    }

    protected function getTopAttractions($locationId, $limit = 6)
    {
        try {
            $attractions = DB::table('Sight')
                ->where('LocationId', $locationId)
                ->where('Averagerating', '>', 0)
                ->select(
                    'SightId',
                    'Title',
                    'Slug',
                    'Averagerating',
                    'ReviewCount',
                    'MicroSummary',
                    'Address'
                )
                ->orderBy('PopularityIndex', 'desc')
                ->orderBy('Averagerating', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return array_map(function($attraction) {
                return (array) $attraction;
            }, $attractions);
        } catch (\Exception $e) {
            Log::error('Error fetching attractions: ' . $e->getMessage());
            return [];
        }
    }

    protected function getTopRestaurants($locationId, $limit = 4)
    {
        try {
            $restaurants = DB::table('Sight')
                ->where('LocationId', $locationId)
                ->where('IsRestaurant', 1)
                ->where('Averagerating', '>', 0)
                ->select(
                    'SightId',
                    'Title',
                    'Slug',
                    'Averagerating',
                    'ReviewCount',
                    'cuisines',
                    'Cost',
                    'Address'
                )
                ->orderBy('Averagerating', 'desc')
                ->orderBy('ReviewCount', 'desc')
                ->limit($limit)
                ->get()
                ->toArray();

            return array_map(function($restaurant) {
                return (array) $restaurant;
            }, $restaurants);
        } catch (\Exception $e) {
            Log::error('Error fetching restaurants: ' . $e->getMessage());
            return [];
        }
    }
}
