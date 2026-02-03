<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class TripController extends Controller
{
    /**
     * Display the Trip page.
     */
    public function index(Request $request)
    {
        // Fetch by slug if provided, else get first available trip
        $slug = $request->query('slug');
        $trip = null;
        if ($slug) {
            $trip = Trip::where('slug', $slug)->first();
        }

        
        if (!$trip) {
            if (Auth::check()) {
                $trip = Trip::where('user_id', Auth::id())->orderBy('id', 'asc')->first();
            } else {
                // Frontend session (custom auth): maintain a session-bound trip id to avoid FK to users
                $frontendUser = session('frontend_user');
                if (!empty($frontendUser) && !empty($frontendUser['UserId'])) {
                    $activeTripId = session('frontend_active_trip_id');
                    if ($activeTripId) {
                        $trip = Trip::find($activeTripId);
                    }
                    if (!$trip) {
                        $trip = Trip::create([
                            'user_id' => null, // avoid FK violation to users table
                            'name' => 'My Trip',
                            'slug' => Str::slug('my-trip-' . uniqid()),
                            'ai_assistant_enabled' => false,
                        ]);
                        session(['frontend_active_trip_id' => $trip->id]);
                    }
                } else {
                    $trip = Trip::query()->first();
                }
            }
        }

        // Defaults
        $hotels = collect();
        $activities = collect();
        $activityImagesMap = [];
        $placeItemImages = [];

        if ($trip) {
            $city = trim((string) ($trip->destination_city ?? ''));
            $country = trim((string) ($trip->destination_country ?? ''));
            // Allow the view to hint a different active location via query (?city=...&country=...)
            $viewCity = trim((string) ($request->query('city') ?? ''));
            $viewCountry = trim((string) ($request->query('country') ?? ''));
            if ($viewCity === '' && $viewCountry === '') {
                $viewCity = $city;
                $viewCountry = $country;
            }
            Log::debug('TripController@index: destination', ['city'=>$city, 'country'=>$country, 'trip_id'=>$trip->id, 'view_city'=>$viewCity, 'view_country'=>$viewCountry]);

            // Pull saved items first
            $distinctTypes = DB::table('trip_items')
                ->where('trip_id', $trip->id)
                ->select(DB::raw('LOWER(entity_type) as et'))
                ->distinct()->pluck('et');
            Log::debug('TripController@index: distinct entity_types', ['types'=>$distinctTypes->toArray()]);

            $savedHotelIds = DB::table('trip_items')
                ->where('trip_id', $trip->id)
                ->whereIn(DB::raw('LOWER(entity_type)'), [
                    'hotel','hotels','accommodation','accommodations','stay','stays','lodging','resort','resorts','apartment','apartments'
                ])
                ->orderBy('id', 'asc')
                ->pluck('entity_id')
                ->filter()
                ->values();
            Log::debug('TripController@index: savedHotelIds', ['count'=>$savedHotelIds->count(), 'ids'=>$savedHotelIds->take(20)->toArray()]);

            $savedSightIds = DB::table('trip_items')
                ->where('trip_id', $trip->id)
                ->whereIn(DB::raw('LOWER(entity_type)'), [
                    // Keep clearly experiential categories only; exclude broad generic buckets like "place/poi"
                    'activity','activities','sight','sights','attraction','attractions',
                    'experience','experiences','restaurant','restaurants','food','dining',
                    'shopping','shop','shops','mall','malls','shopping_mall'
                ])
                ->orderBy('id', 'asc')
                ->pluck('entity_id')
                ->filter()
                ->values();
            Log::debug('TripController@index: savedSightIds', ['count'=>$savedSightIds->count(), 'ids'=>$savedSightIds->take(20)->toArray()]);

            // Collect titles from Saved Items lists (trip->places) to help surface Activities
            $placeTitles = collect();
            $placesRaw = $trip->places ?? [];
            if (is_string($placesRaw)) {
                $decoded = json_decode($placesRaw, true);
                $placesRaw = is_array($decoded) ? $decoded : [];
            }
            if (is_array($placesRaw)) {
                foreach ($placesRaw as $list) {
                    $items = is_array($list['items'] ?? null) ? $list['items'] : [];
                    foreach ($items as $it) {
                        $t = trim((string)($it['title'] ?? ''));
                        if ($t !== '') { $placeTitles->push($t); }
                    }
                }
            }
            $placeTitles = $placeTitles->unique()->values();
            // Also pull generic saved titles from trip_items for broad categories (place/poi)
            try {
                $genericSavedTitles = DB::table('trip_items')
                    ->where('trip_id', $trip->id)
                    ->whereIn(DB::raw('LOWER(entity_type)'), ['place','places','poi','point_of_interest'])
                    ->orderBy('id', 'asc')
                    ->pluck('title')
                    ->filter()
                    ->map(fn($t)=> trim((string)$t))
                    ->filter()
                    ->values();
                if ($genericSavedTitles->count() > 0) {
                    $placeTitles = $placeTitles->merge($genericSavedTitles)->unique()->values();
                }
            } catch (\Throwable $e) {
                // ignore if table/columns differ
            }
            Log::debug('TripController@index: placeTitles', ['count'=>$placeTitles->count(), 'titles'=>$placeTitles->take(20)->toArray()]);

            // Pre-compute priority hotels from saved titles (places list), exact + fuzzy match
            $priorityHotels = collect();
            if ($placeTitles->count() > 0) {
                try {
                    $hotelTitleCandidates = $placeTitles->filter(function($t){
                        $s = Str::lower(trim((string)$t));
                        if ($s === '') return false;
                        // quick heuristic for hotel-like names
                        return preg_match('/\b(hotel|resort|inn|suite|suites|apartment|apart|lodg|lodge|hostel|bnb|motel|palace|hyatt|hilton|marriott|ibis|sheraton|radisson|taj|oberoi)\b/i', $s);
                    })->values();

                    if ($hotelTitleCandidates->count() > 0) {
                        // Exact title matches first
                        $priorityHotels = DB::table('TPHotel')
                            ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                            ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                            ->whereIn('TPHotel.name', $hotelTitleCandidates)
                            ->limit(30)
                            ->get();

                        // If too few, add fuzzy matches by tokens
                        if ($priorityHotels->count() < 5) {
                            $more = DB::table('TPHotel')
                                ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                                ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                                ->where(function($q) use ($hotelTitleCandidates){
                                    foreach ($hotelTitleCandidates as $cand) {
                                        $t = trim((string)$cand);
                                        if ($t === '') continue;
                                        $tokens = preg_split('/\W+/u', $t, -1, PREG_SPLIT_NO_EMPTY);
                                        $tokens = array_values(array_filter($tokens, function($w){ return Str::length($w) >= 3; }));
                                        if (!empty($tokens)) {
                                            $q->orWhere(function($qq) use ($t, $tokens){
                                                $qq->where('TPHotel.name', 'like', "%".$t."%");
                                                foreach ($tokens as $tok) {
                                                    $qq->orWhere('TPHotel.name', 'like', "%".$tok."%");
                                                }
                                            });
                                        }
                                    }
                                })
                                ->limit(30)
                                ->get();
                            if ($more->count() > 0) {
                                // merge and unique by id/hotelid/name
                                $seen = [];
                                $priorityHotels = $priorityHotels->merge($more)->filter(function($h) use (&$seen){
                                    $key = (string)($h->hotelid ?? '').'#'.(string)($h->id ?? '').'#'.Str::lower((string)($h->name ?? ''));
                                    if (isset($seen[$key])) return false;
                                    $seen[$key] = true;
                                    return true;
                                })->values();
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore matching issues
                }
            }

            // Order priority hotels: put current destination city/country first
            if (isset($priorityHotels) && $priorityHotels instanceof \Illuminate\Support\Collection && $priorityHotels->count() > 0) {
                $cityLower = Str::lower($viewCity);
                $countryLower = Str::lower($viewCountry);
                $priorityHotels = $priorityHotels->sortBy(function($h) use ($cityLower, $countryLower) {
                    $hc = Str::lower((string)($h->cityName ?? ''));
                    $hco = Str::lower((string)($h->countryName ?? ''));
                    $score = 0;
                    if ($countryLower !== '' && $hco === $countryLower) { $score -= 5; }
                    if ($cityLower !== '' && $hc === $cityLower) { $score -= 10; }
                    // more negative => higher priority in ascending sort
                    return $score;
                })->values();
            }

            // Hotels: exact saved first; otherwise filter by destination; additionally boost by priorityHotels from places
            if ($savedHotelIds->count() > 0) {
                // Split into numeric IDs and possible names
                $hotelIdNums = $savedHotelIds->filter(fn($v)=>preg_match('/^\d+$/', (string)$v))->values();
                $hotelIdStrs = $savedHotelIds->reject(fn($v)=>preg_match('/^\d+$/', (string)$v))->map(fn($v)=>(string)$v)->values();

                // Fetch matches; we'll sort by saved order in PHP to support both hotelid/id and name.
                $hotels = DB::table('TPHotel')
                    ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                    ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                    ->where(function($q) use ($hotelIdNums, $hotelIdStrs) {
                        if ($hotelIdNums->count() > 0) {
                            $q->whereIn('TPHotel.hotelid', $hotelIdNums)
                              ->orWhereIn('TPHotel.id', $hotelIdNums);
                        }
                        if ($hotelIdStrs->count() > 0) {
                            $q->orWhereIn('TPHotel.name', $hotelIdStrs);
                            // fuzzy OR matching on tokens for names
                            Log::debug('TripController@index: fuzzy matching hotel names', ['count'=>$hotelIdStrs->count(), 'names'=>$hotelIdStrs->take(20)->toArray()]);
                            foreach ($hotelIdStrs as $name) {
                                $n = trim((string)$name);
                                if ($n === '') { continue; }
                                $tokens = preg_split('/\W+/u', $n, -1, PREG_SPLIT_NO_EMPTY);
                                $tokens = array_values(array_filter($tokens, function($w){ return Str::length($w) >= 4; }));
                                if (!empty($tokens)) {
                                    $q->orWhere(function($qq) use ($n, $tokens) {
                                        $qq->where(function($qq2) use ($n, $tokens) {
                                            $qq2->where('TPHotel.name', 'like', "%".$n."%");
                                            foreach ($tokens as $tok) {
                                                $qq2->orWhere('TPHotel.name', 'like', "%".$tok."%");
                                            }
                                        });
                                    });
                                }
                            }
                        }
                    })
                    ->limit(50)
                    ->get();
                Log::debug('TripController@index: hotels after saved match', ['count'=>$hotels->count()]);
                // Preserve saved order
                $order = array_values($savedHotelIds->toArray());
                $pos = function($val) use ($order) {
                    $idx = array_search((string)$val, array_map('strval', $order), true);
                    return $idx === false ? PHP_INT_MAX : $idx;
                };
                $hotels = $hotels->sortBy(function($h) use ($pos) {
                    $p1 = $pos($h->hotelid ?? '');
                    $p2 = $pos($h->id ?? '');
                    return min($p1, $p2);
                })->values();

                // If we also have title-based priority hotels, merge them at the very top
                if (isset($priorityHotels) && $priorityHotels->count() > 0) {
                    $seen = [];
                    $merge = $priorityHotels->merge($hotels)->filter(function($h) use (&$seen){
                        $key = (string)($h->hotelid ?? '').'#'.(string)($h->id ?? '');
                        if (isset($seen[$key])) return false;
                        $seen[$key] = true;
                        return true;
                    })->values();
                    $hotels = $merge;
                }

                // If nothing matched the saved IDs (format mismatch), fallback to destination-based filter
                if ($hotels->isEmpty()) {
                    $hotels = DB::table('TPHotel')
                        ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                        ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                        ->when(($city !== '' || $country !== ''), function ($q) use ($city, $country) {
                            if ($city !== '' && $country !== '') {
                                $q->whereRaw('LOWER(TPLocations.cityName) = ?', [Str::lower($city)])
                                  ->whereRaw('LOWER(TPLocations.countryName) = ?', [Str::lower($country)]);
                            } elseif ($city !== '') {
                                $q->whereRaw('LOWER(TPLocations.cityName) = ?', [Str::lower($city)]);
                            } else {
                                $q->whereRaw('LOWER(TPLocations.countryName) = ?', [Str::lower($country)]);
                            }
                        })
                        ->when(($city === '' && $country === ''), function($q){ $q->whereRaw('1 = 0'); })
                        ->limit(8)
                        ->get();
                    Log::debug('TripController@index: hotels strict destination count', ['count'=>$hotels->count(), 'city'=>$city, 'country'=>$country]);

                    // If still empty, try LIKE-based matching
                    if ($hotels->isEmpty() && ($city !== '' || $country !== '')) {
                        $hotels = DB::table('TPHotel')
                            ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                            ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                            ->when(($city !== '' && $country !== ''), function ($q) use ($city, $country) {
                                $q->where('TPLocations.cityName', 'like', '%'.$city.'%')
                                  ->where('TPLocations.countryName', 'like', '%'.$country.'%');
                            })
                            ->when(($city !== '' && $country === ''), function ($q) use ($city) {
                                $q->where('TPLocations.cityName', 'like', '%'.$city.'%');
                            })
                            ->when(($city === '' && $country !== ''), function ($q) use ($country) {
                                $q->where('TPLocations.countryName', 'like', '%'.$country.'%');
                            })
                            ->limit(8)
                            ->get();
                        Log::debug('TripController@index: hotels LIKE destination count', ['count'=>$hotels->count(), 'city'=>$city, 'country'=>$country]);
                    }

                    // As a last resort, country-only LIKE if both provided and previous empty
                    if ($hotels->isEmpty() && ($city !== '' && $country !== '')) {
                        $hotels = DB::table('TPHotel')
                            ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                            ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                            ->where('TPLocations.countryName', 'like', '%'.$country.'%')
                            ->orderByDesc('TPHotel.stars')
                            ->limit(8)
                            ->get();
                        Log::debug('TripController@index: hotels country-only LIKE count', ['count'=>$hotels->count(), 'country'=>$country]);
                    }
                }
            } else {
                $hotels = DB::table('TPHotel')
                    ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                    ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                    ->when(($city !== '' || $country !== ''), function ($q) use ($city, $country) {
                        if ($city !== '' && $country !== '') {
                            $q->whereRaw('LOWER(TPLocations.cityName) = ?', [Str::lower($city)])
                              ->whereRaw('LOWER(TPLocations.countryName) = ?', [Str::lower($country)]);
                        } elseif ($city !== '') {
                            $q->whereRaw('LOWER(TPLocations.cityName) = ?', [Str::lower($city)]);
                        } else {
                            $q->whereRaw('LOWER(TPLocations.countryName) = ?', [Str::lower($country)]);
                        }
                    })
                    ->when(($city === '' && $country === ''), function($q){
                        // No destination and no saved hotels -> show none instead of random
                        $q->whereRaw('1 = 0');
                    })
                    ->limit(8)
                    ->get();
                Log::debug('TripController@index: hotels strict destination (no saved) count', ['count'=>$hotels->count(), 'city'=>$city, 'country'=>$country]);

                // Merge priorityHotels from saved titles to top (for cases where destination differs, e.g., London)
                if (isset($priorityHotels) && $priorityHotels->count() > 0) {
                    $seen = [];
                    $merge = $priorityHotels->merge($hotels)->filter(function($h) use (&$seen){
                        $key = (string)($h->hotelid ?? '').'#'.(string)($h->id ?? '');
                        if (isset($seen[$key])) return false;
                        $seen[$key] = true;
                        return true;
                    })->values();
                    $hotels = $merge;
                }

                if ($hotels->isEmpty() && ($city !== '' || $country !== '')) {
                    $hotels = DB::table('TPHotel')
                        ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                        ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                        ->when(($city !== '' && $country !== ''), function ($q) use ($city, $country) {
                            $q->where('TPLocations.cityName', 'like', '%'.$city.'%')
                              ->where('TPLocations.countryName', 'like', '%'.$country.'%');
                        })
                        ->when(($city !== '' && $country === ''), function ($q) use ($city) {
                            $q->where('TPLocations.cityName', 'like', '%'.$city.'%');
                        })
                        ->when(($city === '' && $country !== ''), function ($q) use ($country) {
                            $q->where('TPLocations.countryName', 'like', '%'.$country.'%');
                        })
                        ->limit(8)
                        ->get();
                    Log::debug('TripController@index: hotels LIKE destination (no saved) count', ['count'=>$hotels->count(), 'city'=>$city, 'country'=>$country]);
                }

                if ($hotels->isEmpty() && ($country !== '')) {
                    $hotels = DB::table('TPHotel')
                        ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                        ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                        ->where('TPLocations.countryName', 'like', '%'.$country.'%')
                        ->orderByDesc('TPHotel.stars')
                        ->limit(8)
                        ->get();
                    Log::debug('TripController@index: hotels country-only LIKE (no saved) count', ['count'=>$hotels->count(), 'country'=>$country]);
                }
            }

            // Fetch Activities (Sights)
            if ($savedSightIds->count() > 0 || $placeTitles->count() > 0) {
                $sightSource = $savedSightIds;
                $sightIdNums = $sightSource->filter(fn($v)=>preg_match('/^\d+$/', (string)$v))->values();
                $sightIdStrs = $sightSource->reject(fn($v)=>preg_match('/^\d+$/', (string)$v))->map(fn($v)=>(string)$v)->values();
                // Merge saved place titles but exclude any that collide with current hotel names
                if ($placeTitles->count() > 0) {
                    $hotelNamesLower = $hotels->pluck('name')->filter()->map(fn($n)=> Str::lower(trim((string)$n)))->unique();
                    $filteredPlaceTitles = $placeTitles->reject(function($t) use ($hotelNamesLower){
                        return $hotelNamesLower->contains(Str::lower(trim((string)$t)));
                    })->values();
                    $sightIdStrs = $sightIdStrs->merge($filteredPlaceTitles)->unique()->values();
                }
                Log::debug('TripController@index: matching activities', ['idNums'=>$sightIdNums->toArray(), 'titles'=>$sightIdStrs->toArray()]);

                $activities = DB::table('Sight')
                    ->leftJoin('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                    ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
                    ->select('Sight.SightId', 'Sight.Title', 'Sight.short_description', 'Sight.Address', 'Sight.Latitude', 'Sight.Longitude', 'Location.Name as cityName', 'Country.Name as countryName')
                    ->where(function($q) use ($sightIdNums, $sightIdStrs){
                        if ($sightIdNums->count() > 0) {
                            $q->whereIn('Sight.SightId', $sightIdNums);
                        }
                        if ($sightIdStrs->count() > 0) {
                            $q->orWhereIn('Sight.Title', $sightIdStrs);
                        }
                    })
                    ->limit(60)
                    ->get();
                Log::debug('TripController@index: activities after exact match', ['count'=>$activities->count()]);
                // Preserve order: prefer trip_items order; then placeTitles order
                $order = array_values(($savedSightIds->count() > 0 ? $savedSightIds : $placeTitles)->toArray());
                $activities = $activities->sortBy(function($a) use ($order) {
                    $idx = array_search((string)($a->SightId ?? ''), array_map('strval', $order), true);
                    return $idx === false ? PHP_INT_MAX : $idx;
                })->values();

                // If no exact matches, try fuzzy title matching against saved strings
                if ($activities->isEmpty() && $sightIdStrs->count() > 0) {
                    Log::debug('TripController@index: fuzzy matching activity titles', ['count'=>$sightIdStrs->count(), 'titles'=>$sightIdStrs->take(20)->toArray()]);
                    $activities = DB::table('Sight')
                        ->leftJoin('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                        ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
                        ->select('Sight.SightId', 'Sight.Title', 'Sight.short_description', 'Sight.Address', 'Sight.Latitude', 'Sight.Longitude', 'Location.Name as cityName', 'Country.Name as countryName')
                        ->where(function($q) use ($sightIdStrs) {
                            foreach ($sightIdStrs as $title) {
                                $t = trim((string)$title);
                                if ($t === '') { continue; }
                                $tokens = preg_split('/\W+/u', $t, -1, PREG_SPLIT_NO_EMPTY);
                                $tokens = array_values(array_filter($tokens, function($w){ return Str::length($w) >= 3; }));
                                $q->orWhere(function($qq) use ($t, $tokens) {
                                    $qq->where(function($qq2) use ($t, $tokens) {
                                        $qq2->where('Sight.Title', 'like', "%".$t."%");
                                        foreach ($tokens as $tok) {
                                            $qq2->orWhere('Sight.Title', 'like', "%".$tok."%");
                                        }
                                    });
                                });
                            }
                        })
                        ->limit(60)
                        ->get();

                    // Sort by best similarity to any saved title
                    $savedTitles = array_map('strval', $sightIdStrs->toArray());
                    $activities = $activities->sortByDesc(function($a) use ($savedTitles) {
                        $best = 0;
                        foreach ($savedTitles as $st) {
                            $pct = 0;
                            similar_text(Str::lower($st), Str::lower((string)($a->Title ?? '')), $pct);
                            if ($pct > $best) { $best = $pct; }
                        }
                        return $best; // higher is better
                    })->values();
                    Log::debug('TripController@index: activities after fuzzy match', ['count'=>$activities->count()]);
                }

                // Fallback to destination-based if still nothing matched
                if ($activities->isEmpty()) {
                    $activities = DB::table('Sight')
                        ->leftJoin('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                        ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
                        ->select('Sight.SightId', 'Sight.Title', 'Sight.short_description', 'Sight.Address', 'Sight.Latitude', 'Sight.Longitude', 'Location.Name as cityName', 'Country.Name as countryName')
                        ->when(($city !== '' || $country !== ''), function ($q) use ($city, $country) {
                            if ($city !== '' && $country !== '') {
                                $q->whereRaw('LOWER(Location.Name) = ?', [Str::lower($city)])
                                  ->whereRaw('LOWER(Country.Name) = ?', [Str::lower($country)]);
                            } elseif ($city !== '') {
                                $q->whereRaw('LOWER(Location.Name) = ?', [Str::lower($city)]);
                            } else {
                                $q->whereRaw('LOWER(Country.Name) = ?', [Str::lower($country)]);
                            }
                        })
                        ->when(($city === '' && $country === ''), function($q){ $q->whereRaw('1 = 0'); })
                        ->limit(8)
                        ->get();
                }
            } else {
                // Strict destination-based activities first
                $activities = DB::table('Sight')
                    ->leftJoin('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                    ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
                    ->select('Sight.SightId', 'Sight.Title', 'Sight.short_description', 'Sight.Address', 'Sight.Latitude', 'Sight.Longitude', 'Location.Name as cityName', 'Country.Name as countryName')
                    ->when(($city !== '' || $country !== ''), function ($q) use ($city, $country) {
                        if ($city !== '' && $country !== '') {
                            $q->whereRaw('LOWER(Location.Name) = ?', [Str::lower($city)])
                              ->whereRaw('LOWER(Country.Name) = ?', [Str::lower($country)]);
                        } elseif ($city !== '') {
                            $q->whereRaw('LOWER(Location.Name) = ?', [Str::lower($city)]);
                        } else {
                            $q->whereRaw('LOWER(Country.Name) = ?', [Str::lower($country)]);
                        }
                    })
                    ->when(($city === '' && $country === ''), function($q){
                        // No destination and no saved activities -> show none instead of random
                        $q->whereRaw('1 = 0');
                    })
                    ->limit(8)
                    ->get();

                // LIKE-based fallback when strict returns none but we do have a destination
                if ($activities->isEmpty() && ($city !== '' || $country !== '')) {
                    $activities = DB::table('Sight')
                        ->leftJoin('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                        ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
                        ->select('Sight.SightId', 'Sight.Title', 'Sight.short_description', 'Sight.Address', 'Sight.Latitude', 'Sight.Longitude', 'Location.Name as cityName', 'Country.Name as countryName')
                        ->when(($city !== '' && $country !== ''), function ($q) use ($city, $country) {
                            $q->where('Location.Name', 'like', '%'.$city.'%')
                              ->where('Country.Name', 'like', '%'.$country.'%');
                        })
                        ->when(($city !== '' && $country === ''), function ($q) use ($city) {
                            $q->where('Location.Name', 'like', '%'.$city.'%');
                        })
                        ->when(($city === '' && $country !== ''), function ($q) use ($country) {
                            $q->where('Country.Name', 'like', '%'.$country.'%');
                        })
                        ->limit(8)
                        ->get();
                }

                // If still empty but we have hotels, infer destination from first hotel and try LIKE
                if ($activities->isEmpty() && $hotels->count() > 0) {
                    $firstHotel = $hotels->first();
                    $hCity = trim((string)($firstHotel->cityName ?? ''));
                    $hCountry = trim((string)($firstHotel->countryName ?? ''));
                    if ($hCity !== '' || $hCountry !== '') {
                        $activities = DB::table('Sight')
                            ->leftJoin('Location', 'Location.LocationId', '=', 'Sight.LocationId')
                            ->leftJoin('Country', 'Country.CountryId', '=', 'Location.CountryId')
                            ->select('Sight.SightId', 'Sight.Title', 'Sight.short_description', 'Sight.Address', 'Sight.Latitude', 'Sight.Longitude', 'Location.Name as cityName', 'Country.Name as countryName')
                            ->when(($hCity !== '' && $hCountry !== ''), function ($q) use ($hCity, $hCountry) {
                                $q->where('Location.Name', 'like', '%'.$hCity.'%')
                                  ->where('Country.Name', 'like', '%'.$hCountry.'%');
                            })
                            ->when(($hCity !== '' && $hCountry === ''), function ($q) use ($hCity) {
                                $q->where('Location.Name', 'like', '%'.$hCity.'%');
                            })
                            ->when(($hCity === '' && $hCountry !== ''), function ($q) use ($hCountry) {
                                $q->where('Country.Name', 'like', '%'.$hCountry.'%');
                            })
                            ->limit(8)
                            ->get();
                    }
                }
            }

            // If no hotels but we do have activities with a clear location, infer destination from activities and try hotels again
            if ($hotels->isEmpty() && $activities->count() > 0) {
                $first = $activities->first();
                $actCity = trim((string)($first->cityName ?? ''));
                $actCountry = trim((string)($first->countryName ?? ''));
                if ($actCity !== '' || $actCountry !== '') {
                    Log::debug('TripController@index: inferring hotel destination from activities', ['city'=>$actCity, 'country'=>$actCountry]);
                    $hotels = DB::table('TPHotel')
                        ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                        ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                        ->when(($actCity !== '' && $actCountry !== ''), function ($q) use ($actCity, $actCountry) {
                            $q->where('TPLocations.cityName', 'like', '%'.$actCity.'%')
                              ->where('TPLocations.countryName', 'like', '%'.$actCountry.'%');
                        })
                        ->when(($actCity !== '' && $actCountry === ''), function ($q) use ($actCity) {
                            $q->where('TPLocations.cityName', 'like', '%'.$actCity.'%');
                        })
                        ->when(($actCity === '' && $actCountry !== ''), function ($q) use ($actCountry) {
                            $q->where('TPLocations.countryName', 'like', '%'.$actCountry.'%');
                        })
                        ->orderByDesc('TPHotel.stars')
                        ->limit(8)
                        ->get();
                    Log::debug('TripController@index: hotels inferred from activities count', ['count'=>$hotels->count(), 'city'=>$actCity, 'country'=>$actCountry]);
                }
            }

            // Post-filter: ensure activities don't include hotels by title collision
            if ($activities->count() > 0 && $hotels->count() > 0) {
                $hotelNames = $hotels->pluck('name')->filter()->map(fn($n)=> Str::lower(trim((string)$n)))->unique()->values();
                if ($hotelNames->count() > 0) {
                    $activities = $activities->reject(function($a) use ($hotelNames) {
                        $t = Str::lower(trim((string)($a->Title ?? '')));
                        return $t !== '' && $hotelNames->contains($t);
                    })->values();
                }
            }

            // Post-filter: restrict activities to the requested categories using keyword heuristics
            if ($activities->count() > 0) {
                $keepPatterns = [
                    // restaurants / food
                    '/\b(restaurant|restaurants|dining|food|eatery|cafe|café|bistro|bar|steakhouse|pizzeria|bakery|coffee|tea)\b/i',
                    // experiences
                    '/\b(experience|experiences|tour|tours|ticket|tickets|show|shows|adventure|ride|rides|safari|cruise|boat|helicopter|skydive)\b/i',
                    // attractions
                    '/\b(attraction|attractions|sight|sights|landmark|landmarks|museum|museums|gallery|galleries|zoo|aquarium|park|parks|tower|observatory|monument|palace|castle|temple|mosque|church)\b/i',
                    // shopping / malls
                    '/\b(shopping|shop|shops|market|markets|bazaar|souq|mall|malls|shopping\s*mall)\b/i',
                ];
                $activities = $activities->filter(function($a) use ($keepPatterns) {
                    $hay = ' '.(string)($a->Title ?? '').' '.(string)($a->short_description ?? '');
                    foreach ($keepPatterns as $re) {
                        if (preg_match($re, $hay)) { return true; }
                    }
                    return false;
                })->values();
            }

            // Map first image per Sight for thumbnails
            if ($activities->count() > 0) {
                $sightIds = $activities->pluck('SightId')->filter()->unique()->values();
                if ($sightIds->count() > 0) {
                    $images = DB::table('Sight_image')
                        ->select('SightId', 'Image')
                        ->whereIn('SightId', $sightIds)
                        ->get();
                    $firstBySight = [];
                    foreach ($images as $row) {
                        $sid = (int) ($row->SightId ?? 0);
                        $img = (string) ($row->Image ?? '');
                        if ($sid === 0 || $img === '') continue;
                        if (stripos($img, 'vid') !== false) continue; // skip videos
                        if (!isset($firstBySight[$sid])) {
                            $firstBySight[$sid] = $img;
                        }
                    }
                    foreach ($firstBySight as $sid => $img) {
                        $inner = 'https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/' . ltrim($img, '/');
                        $activityImagesMap[$sid] = 'https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=' . urlencode($inner) . '&width=240&height=180';
                    }
                }
            }

            // Build images for Saved Items (Places list) by matching titles to Sight images
            if ($placeTitles->count() > 0 && empty($placeItemImages)) {
                // Try exact title matches first
                $placeTitleStrs = $placeTitles->map(fn($t)=> (string)$t)->values();
                $placeSights = DB::table('Sight')
                    ->select('SightId', 'Title')
                    ->whereIn('Title', $placeTitleStrs)
                    ->limit(120)
                    ->get();
                // If not enough, do fuzzy LIKE matches
                if ($placeSights->count() === 0) {
                    $placeSights = DB::table('Sight')
                        ->select('SightId', 'Title')
                        ->where(function($q) use ($placeTitleStrs) {
                            foreach ($placeTitleStrs as $t) {
                                $tt = trim((string)$t);
                                if ($tt === '') continue;
                                $tokens = preg_split('/\W+/u', $tt, -1, PREG_SPLIT_NO_EMPTY);
                                $tokens = array_values(array_filter($tokens, function($w){ return Str::length($w) >= 3; }));
                                $q->orWhere(function($qq) use ($tt, $tokens) {
                                    $qq->where('Title', 'like', "%".$tt."%");
                                    foreach ($tokens as $tok) {
                                        $qq->orWhere('Title', 'like', "%".$tok."%");
                                    }
                                });
                            }
                        })
                        ->limit(120)
                        ->get();
                }
                if ($placeSights->count() > 0) {
                    $psIds = $placeSights->pluck('SightId')->filter()->unique()->values();
                    $imgs = DB::table('Sight_image')
                        ->select('SightId', 'Image')
                        ->whereIn('SightId', $psIds)
                        ->get();
                    $first = [];
                    foreach ($imgs as $row) {
                        $sid = (int) ($row->SightId ?? 0);
                        $img = (string) ($row->Image ?? '');
                        if ($sid === 0 || $img === '') continue;
                        if (stripos($img, 'vid') !== false) continue;
                        if (!isset($first[$sid])) { $first[$sid] = $img; }
                    }
                    // For each saved title, pick the best matching Sight title and map by the saved title key
                    $sightsArr = $placeSights->map(function($s){
                        return [
                            'id' => (int)($s->SightId ?? 0),
                            'title' => (string)($s->Title ?? ''),
                            'title_lc' => Str::lower(trim((string)($s->Title ?? ''))),
                        ];
                    })->toArray();
                    foreach ($placeTitles as $savedTitle) {
                        $saved = (string)$savedTitle;
                        $savedLc = Str::lower(trim($saved));
                        if ($savedLc === '') continue;
                        $bestId = null; $bestPct = 0;
                        foreach ($sightsArr as $sa) {
                            if (empty($sa['title'])) continue;
                            $pct = 0;
                            similar_text($savedLc, $sa['title_lc'], $pct);
                            if ($pct > $bestPct) { $bestPct = $pct; $bestId = $sa['id']; }
                        }
                        if ($bestId && isset($first[$bestId])) {
                            $img = $first[$bestId];
                            $inner = 'https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/' . ltrim($img, '/');
                            $placeItemImages[$savedLc] = 'https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=' . urlencode($inner) . '&width=180&height=136';
                        }
                    }
                }
            }
        }

        // ===== Final ordering just before returning view =====
        try {
            if (isset($trip)) {
                $city = trim((string)($trip->destination_city ?? ''));
                $country = trim((string)($trip->destination_country ?? ''));
            }
            $reqCity = trim((string) (request()->query('city') ?? ''));
            $reqCountry = trim((string) (request()->query('country') ?? ''));

            // Build an inferred location from saved context (hotels/priorityHotels/activities)
            $inferredCity = '';
            $inferredCountry = '';
            $inferFrom = collect();
            if (isset($priorityHotels) && $priorityHotels instanceof \Illuminate\Support\Collection) { $inferFrom = $inferFrom->merge($priorityHotels); }
            if (isset($hotels) && $hotels instanceof \Illuminate\Support\Collection) { $inferFrom = $inferFrom->merge($hotels); }
            if (isset($activities) && $activities instanceof \Illuminate\Support\Collection) { $inferFrom = $inferFrom->merge($activities); }
            if ($inferFrom->count() > 0) {
                $counts = [];
                foreach ($inferFrom as $it) {
                    $c = Str::lower((string)($it->cityName ?? ''));
                    $co = Str::lower((string)($it->countryName ?? ''));
                    if ($c === '' && $co === '') { continue; }
                    $key = $c.'|'.$co;
                    $counts[$key] = ($counts[$key] ?? 0) + 1;
                }
                if (!empty($counts)) {
                    arsort($counts);
                    $top = array_key_first($counts);
                    if ($top !== null) {
                        [$vc,$vco] = explode('|', $top);
                        $inferredCity = $vc ?: '';
                        $inferredCountry = $vco ?: '';
                    }
                }
            }

            // Decide the final view city/country with the following priority:
            // 1) Explicit user query (?city/?country)
            // 2) Inferred from saved items (especially saved hotels)
            // 3) Trip destination
            $viewCity = $reqCity !== '' ? $reqCity : ($inferredCity !== '' ? $inferredCity : ($city ?? ''));
            $viewCountry = $reqCountry !== '' ? $reqCountry : ($inferredCountry !== '' ? $inferredCountry : ($country ?? ''));

            // If inferred/desired city differs from destination, fetch some hotels for that city and merge to top
            if ((Str::lower($viewCity) !== Str::lower((string)$city) || Str::lower($viewCountry) !== Str::lower((string)$country))
                && ($viewCity !== '' || $viewCountry !== '')) {
                $altHotels = DB::table('TPHotel')
                    ->leftJoin('TPLocations', 'TPHotel.location_id', '=', 'TPLocations.id')
                    ->select('TPHotel.id', 'TPHotel.hotelid', 'TPHotel.name', 'TPHotel.address', 'TPHotel.stars', 'TPHotel.pricefrom', 'TPHotel.Latitude', 'TPHotel.longnitude', 'TPLocations.cityName', 'TPLocations.countryName')
                    ->when(($viewCity !== '' || $viewCountry !== ''), function ($q) use ($viewCity, $viewCountry) {
                        // Use LIKE to be robust to variants like "United Kingdom (UK)"
                        if ($viewCity !== '' && $viewCountry !== '') {
                            $q->where('TPLocations.cityName', 'like', '%'.$viewCity.'%')
                              ->where('TPLocations.countryName', 'like', '%'.$viewCountry.'%');
                        } elseif ($viewCity !== '') {
                            $q->where('TPLocations.cityName', 'like', '%'.$viewCity.'%');
                        } else {
                            $q->where('TPLocations.countryName', 'like', '%'.$viewCountry.'%');
                        }
                    })
                    ->orderByDesc('TPHotel.stars')
                    ->limit(8)
                    ->get();
                if (isset($hotels) && $altHotels->count() > 0) {
                    $seen = [];
                    $hotels = $altHotels->merge($hotels)->filter(function($h) use (&$seen){
                        $key = (string)($h->hotelid ?? '').'#'.(string)($h->id ?? '');
                        if (isset($seen[$key])) return false;
                        $seen[$key] = true;
                        return true;
                    })->values();
                }
            }

            if (isset($hotels) && $hotels instanceof \Illuminate\Support\Collection && $hotels->count() > 0) {
                $savedSet = isset($savedHotelIds) ? array_map('strval', $savedHotelIds->toArray()) : [];
                $savedSetLower = array_map('strtolower', $savedSet);
                $viewCityLower = Str::lower($viewCity);
                $viewCountryLower = Str::lower($viewCountry);
                $hotels = $hotels->sortBy(function($h) use ($savedSet, $savedSetLower, $viewCityLower, $viewCountryLower) {
                    $id1 = (string)($h->hotelid ?? '');
                    $id2 = (string)($h->id ?? '');
                    $name = Str::lower((string)($h->name ?? ''));
                    $hc = Str::lower((string)($h->cityName ?? ''));
                    $hco = Str::lower((string)($h->countryName ?? ''));
                    $score = 0;
                    if (in_array($id1, $savedSet, true) || in_array($id2, $savedSet, true) || in_array($name, $savedSetLower, true)) { $score -= 20; }
                    if ($viewCountryLower !== '' && $hco === $viewCountryLower) { $score -= 5; }
                    if ($viewCityLower !== '' && $hc === $viewCityLower) { $score -= 10; }
                    return $score;
                })->values();

                // If we have saved hotels and an inferred view location, filter to that location for relevance (normalized)
                try {
                    $hasSaved = isset($savedHotelIds) && ($savedHotelIds instanceof \Illuminate\Support\Collection) && $savedHotelIds->count() > 0;
                    if ($hasSaved && ($viewCityLower !== '' || $viewCountryLower !== '')) {
                        $normalize = function($s){
                            $x = Str::lower((string)$s);
                            // remove punctuation and parentheses text
                            $x = preg_replace('/\([^\)]*\)/', '', $x); // drop parenthetical e.g., (uk)
                            $x = preg_replace('/[^a-z0-9]+/i', ' ', $x);
                            $x = trim(preg_replace('/\s+/', ' ', $x));
                            // common synonyms
                            if ($x === 'uk') { $x = 'united kingdom'; }
                            return $x;
                        };
                        $vCity = $normalize($viewCityLower);
                        $vCountry = $normalize($viewCountryLower);
                        $filtered = $hotels->filter(function($h) use ($normalize, $vCity, $vCountry, $viewCityLower, $viewCountryLower) {
                            $hc = $normalize($h->cityName ?? '');
                            $hco = $normalize($h->countryName ?? '');
                            // both provided: require both to roughly match (contains or equality)
                            if ($viewCityLower !== '' && $viewCountryLower !== '') {
                                $okCity = ($vCity !== '') ? (strpos($hc, $vCity) !== false || $hc === $vCity) : true;
                                $okCountry = ($vCountry !== '') ? (strpos($hco, $vCountry) !== false || $hco === $vCountry) : true;
                                return $okCity && $okCountry;
                            }
                            if ($viewCityLower !== '') { return ($vCity === '') ? false : (strpos($hc, $vCity) !== false || $hc === $vCity); }
                            return ($vCountry === '') ? false : (strpos($hco, $vCountry) !== false || $hco === $vCountry);
                        })->values();
                        if ($filtered->count() > 0) {
                            $hotels = $filtered;
                        }
                    }
                } catch (\Throwable $e) { Log::debug('TripController@index hotel filter error', ['e' => $e->getMessage()]); }
            }
        } catch (\Throwable $e) { Log::debug('TripController@index final sort error', ['e'=>$e->getMessage()]); }

        return view('trips.trip', [
            'trip' => $trip,
            'hotels' => $hotels,
            'activities' => $activities,
            'activityImagesMap' => $activityImagesMap,
            'placeItemImages' => $placeItemImages,
        ]);
    }

    /**
     * Add a note to a trip (stores notes as JSON array in trips.notes longText)
     */
    public function addNote(Request $request)
    {
        $data = $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $trip = Trip::findOrFail($data['trip_id']);

        // Decode existing notes as array (or initialize)
        $existing = [];
        if (!empty($trip->notes)) {
            $decoded = json_decode($trip->notes, true);
            if (is_array($decoded)) {
                $existing = $decoded;
            } else {
                // If notes was plain text, preserve it as the first note
                $existing = [['text' => (string) $trip->notes, 'created_at' => now()->toDateTimeString()]];
            }
        }

        $noteItem = [
            'text' => $data['note'],
            'created_at' => now()->toDateTimeString(),
            'user_id' => Auth::id(),
        ];
        $existing[] = $noteItem;

        $trip->notes = json_encode($existing);
        $trip->save();

        return response()->json([
            'ok' => true,
            'note' => $noteItem,
        ]);
    }

    /**
     * Create a new Places list for the trip (stored in trips.places as array)
     */
    public function addPlaceList(Request $request)
    {
        $data = $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'name' => ['required', 'string', 'max:120'],
        ]);

        $trip = Trip::findOrFail($data['trip_id']);
        $places = is_array($trip->places ?? null) ? $trip->places : [];

        $new = [
            'id' => uniqid('pl_', true),
            'name' => $data['name'],
            'items' => [],
        ];
        $places[] = $new;
        $trip->places = $places;
        $trip->save();

        return response()->json(['ok' => true, 'list' => $new]);
    }

    /**
     * Add an item to an existing Places list for the trip
     */
    public function addPlaceItem(Request $request)
    {
        $data = $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'list_id' => ['required', 'string'],
            'title' => ['required', 'string', 'max:160'],
            'address' => ['nullable', 'string', 'max:255'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);
        // Require authentication and ownership
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $trip = Trip::where('id', $data['trip_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $places = is_array($trip->places ?? null) ? $trip->places : [];

        foreach ($places as &$list) {
            if (($list['id'] ?? null) === $data['list_id']) {
                $item = [
                    'id' => uniqid('pi_', true),
                    'title' => $data['title'],
                    'address' => $data['address'] ?? null,
                    'lat' => $data['lat'] ?? null,
                    'lng' => $data['lng'] ?? null,
                ];
                $list['items'][] = $item;
                $trip->places = $places;
                $trip->save();
                return response()->json(['ok' => true, 'item' => $item, 'list_id' => $list['id']]);
            }
        }

        return response()->json(['ok' => false, 'message' => 'List not found'], 404);
    }

    /**
     * Return the active trip for the authenticated user (first available).
     */
    public function activeTrip(Request $request)
    {
        // Require Laravel auth. Do not create or expose trips to guests.
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $trip = Trip::where('user_id', Auth::id())->orderBy('id', 'asc')->first();
        if ($trip) {
            return response()->json(['ok' => true, 'trip_id' => $trip->id]);
        }
        // If user has no trip yet, create one for convenience
        $trip = Trip::create([
            'user_id' => Auth::id(),
            'name' => 'My Trip',
            'slug' => Str::slug('my-trip-' . uniqid()),
            'ai_assistant_enabled' => false,
        ]);
        return response()->json(['ok' => true, 'trip_id' => $trip->id]);
    }

    /**
     * Ensure a default places list exists for the trip and return it.
     */
    public function ensureDefaultList(Request $request)
    {
        $data = $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'name' => ['nullable', 'string', 'max:120'],
        ]);
        // Require authentication and ownership
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $name = $data['name'] ?? 'Saved Items';
        $trip = Trip::where('id', $data['trip_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $places = is_array($trip->places ?? null) ? $trip->places : [];

        // Try find by name
        foreach ($places as $list) {
            if (($list['name'] ?? '') === $name) {
                return response()->json(['ok' => true, 'list' => $list]);
            }
        }

        // Create if not found
        $new = [
            'id' => uniqid('pl_', true),
            'name' => $name,
            'items' => [],
        ];
        $places[] = $new;
        $trip->places = $places;
        $trip->save();

        return response()->json(['ok' => true, 'list' => $new]);
    }

    /**
     * Remove an item from a Places list (by item_id if provided, else by title match).
     */
    public function removePlaceItem(Request $request)
    {
        $data = $request->validate([
            'trip_id' => ['required', 'exists:trips,id'],
            'list_id' => ['required', 'string'],
            'item_id' => ['nullable', 'string'],
            'title' => ['nullable', 'string'],
        ]);
        // Require authentication and ownership
        if (!Auth::check()) {
            return response()->json(['ok' => false, 'message' => 'Unauthenticated'], 401);
        }

        $trip = Trip::where('id', $data['trip_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $places = is_array($trip->places ?? null) ? $trip->places : [];

        $removed = false;
        foreach ($places as &$list) {
            if (($list['id'] ?? null) === $data['list_id']) {
                $items = is_array($list['items'] ?? null) ? $list['items'] : [];
                $newItems = [];
                foreach ($items as $it) {
                    $match = false;
                    if (!empty($data['item_id']) && (($it['id'] ?? null) === $data['item_id'])) {
                        $match = true;
                    } elseif (empty($data['item_id']) && !empty($data['title']) && (isset($it['title']) && $it['title'] === $data['title'])) {
                        $match = true;
                    }
                    if ($match) { $removed = true; continue; }
                    $newItems[] = $it;
                }
                $list['items'] = $newItems;
                break;
            }
        }

        if ($removed) {
            $trip->places = $places;
            $trip->save();
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false, 'message' => 'Item not found'], 404);
    }
}
