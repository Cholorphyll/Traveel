<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\UserSearchHistory;
use Symfony\Component\HttpFoundation\Response;

class TrackUserSearch
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->isMethod('get') && $this->shouldTrack($request)) {
            $this->trackSearch($request);
        }

        return $response;
    }

    protected function shouldTrack(Request $request): bool
    {
        $path = $request->path();
        
        return preg_match('/^lo-/', $path) ||
               preg_match('/^ho-/', $path) ||
               preg_match('/^hd-/', $path) ||
               str_contains($path, '/explore/') || 
               str_contains($path, '/hotels/') || 
               str_contains($path, '/hotel/') ||
               str_contains($path, '-hotels') ||
               str_contains($path, '-things-to-do');
    }

    protected function trackSearch(Request $request)
    {
        try {
            $path = $request->path();
            $segments = $request->segments();
            
            $searchData = $this->extractSearchData($request, $path, $segments);
            
            if ($searchData) {
                // Get authenticated user ID from FrontendUserLogin
                $userId = null;
                
                if ($request->session()->has('frontend_user')) {
                    $frontendUser = $request->session()->get('frontend_user');
                    if (is_object($frontendUser)) {
                        $userId = $frontendUser->UserId ?? $frontendUser->id ?? null;
                    } elseif (is_array($frontendUser)) {
                        $userId = $frontendUser['UserId'] ?? $frontendUser['id'] ?? null;
                    }
                } elseif ($request->session()->has('UserId')) {
                    $userId = $request->session()->get('UserId');
                } elseif ($request->session()->has('user_id')) {
                    $userId = $request->session()->get('user_id');
                } elseif ($request->user()) {
                    $userId = $request->user()->UserId ?? $request->user()->id ?? null;
                } elseif (\Illuminate\Support\Facades\Auth::check()) {
                    $user = \Illuminate\Support\Facades\Auth::user();
                    $userId = $user->UserId ?? $user->id ?? null;
                }
                
                $searchData['user_id'] = $userId;
                UserSearchHistory::trackSearch($searchData);
            }
        } catch (\Exception $e) {
            \Log::error('Error tracking search: ' . $e->getMessage());
        }
    }

    protected function extractSearchData(Request $request, string $path, array $segments): ?array
    {
        $searchType = null;
        $locationSlug = null;
        $locationSlugid = null;
        
        if (preg_match('/^lo-(.+?)(?:\/|$)/', $path, $matches)) {
            $searchType = 'explore';
            $locationSlug = $matches[1];
        } elseif (preg_match('/^ho-(.+?)(?:\/|$)/', $path, $matches)) {
            $searchType = 'hotels';
            $locationSlug = $matches[1];
        } elseif (preg_match('/^hd-(.+?)(?:\/|$)/', $path, $matches)) {
            $searchType = 'hotel_detail';
            $locationSlug = $matches[1];
        } elseif (str_contains($path, '/explore/')) {
            $searchType = 'explore';
            $locationSlug = end($segments);
        } elseif (str_contains($path, '/hotels/') || str_contains($path, '-hotels')) {
            $searchType = 'hotels';
            $locationSlug = end($segments);
        } elseif (str_contains($path, '-things-to-do')) {
            $searchType = 'things-to-do';
            $locationSlug = str_replace('-things-to-do', '', end($segments));
        }

        if (!$searchType || !$locationSlug) {
            return null;
        }

        $locationData = $this->getLocationData($locationSlug, $searchType);

        return [
            'search_type' => $searchType,
            'location_id' => $locationData['id'] ?? null,
            'location_name' => $locationData['name'] ?? null,
            'location_slug' => $locationSlug,
            'location_slugid' => $locationData['slugid'] ?? null,
            'search_query' => $request->fullUrl(),
            'search_params' => $request->except(['_token', 'password']),
        ];
    }

    protected function getLocationData(string $slug, string $searchType = 'explore'): array
    {
        try {
            if ($searchType === 'hotel_detail') {
                // Parse hotel URL formats:
                // Format 1: hd-{location_id}-{hotel_id} (e.g., hd-113700010002-3739035)
                // Format 2: hd-{location_id}-{hotel_id}-{slug} (e.g., hd-1264000800130021-2350771-laksharee-guest-house)
                // Ignore sqx, nqx, aqx keywords (landing page identifiers)
                
                $parts = explode('-', $slug, 3);
                $hotel = null;
                
                if (count($parts) >= 2 && is_numeric($parts[0]) && is_numeric($parts[1])) {
                    // Format: {location_id}-{hotel_id} or {location_id}-{hotel_id}-{slug}
                    $locationId = $parts[0]; // e.g., 113700010002 or 1264000800130021
                    $hotelId = $parts[1];    // e.g., 3739035 or 2350771
                    
                    // Get hotel by id (most efficient - uses primary key)
                    $hotel = \DB::table('TPHotel')
                        ->where('id', $hotelId)
                        ->select('location_id', 'slugid')
                        ->first();
                    
                    // If hotel found, use the location_id from URL for better accuracy
                    if ($hotel && !$hotel->location_id) {
                        $hotel->location_id = $locationId;
                    }
                } else {
                    // Fallback: treat entire slug as hotel slug (old format)
                    // Remove landing page keywords first
                    $cleanSlug = preg_replace('/-(sqx|nqx|aqx)\d+$/', '', $slug);
                    
                    $hotel = \DB::table('TPHotel')
                        ->where('slug', $cleanSlug)
                        ->select('location_id', 'slugid')
                        ->first();
                }

                if ($hotel) {
                    // Get location name from Location table
                    $location = \DB::table('Location')
                        ->where('slugid', $hotel->location_id)
                        ->select('LocationId', 'Name', 'slugid')
                        ->first();
                    
                    return [
                        'id' => $location->LocationId ?? $hotel->location_id ?? null,
                        'name' => $location->Name ?? 'Unknown',
                        'slugid' => $location->slugid ?? $hotel->location_id ?? null,
                    ];
                }
            }

            if (preg_match('/^(\d+)-/', $slug, $matches)) {
                $slugid = $matches[1];
                
                $location = \DB::table('Location')
                    ->where('slugid', $slugid)
                    ->first();

                if ($location) {
                    return [
                        'id' => $location->LocationId ?? $location->id ?? null,
                        'name' => $location->Name ?? null,
                        'slugid' => $location->slugid ?? null,
                    ];
                }
            }

            $location = \DB::table('Location')
                ->where('Slug', $slug)
                ->orWhere('slugid', $slug)
                ->orWhere('LocationId', $slug)
                ->first();

            if ($location) {
                return [
                    'id' => $location->LocationId ?? $location->id ?? null,
                    'name' => $location->Name ?? null,
                    'slugid' => $location->slugid ?? null,
                ];
            }

            $tpLocation = \DB::table('TPLocations')
                ->where('slug', $slug)
                ->orWhere('locationId', $slug)
                ->orWhere('id', $slug)
                ->first();

            if ($tpLocation) {
                return [
                    'id' => $tpLocation->id,
                    'name' => $tpLocation->cityName ?? $tpLocation->name ?? null,
                    'slugid' => $tpLocation->locationId ?? null,
                ];
            }
        } catch (\Exception $e) {
            \Log::error('Error fetching location data: ' . $e->getMessage());
        }

        return [];
    }
}
