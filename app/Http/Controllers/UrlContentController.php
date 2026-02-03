<?php

namespace App\Http\Controllers;

use App\Models\UrlContent;
use App\Models\UrlListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class UrlContentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function extractHotelId($url) 
    {
        if (preg_match('/hd-\d+-(\d+)-/', $url, $matches)) {
            return $matches[1];
        }
        return null;
    }

    public function store(Request $request)
    {
        Log::channel('stack')->info('Request data:', $request->all());

        // Get URL and extract hotel ID
        $listing = UrlListing::findOrFail($request->listing_id);
        $hotelId = $this->extractHotelId($listing->url);

        // Check for existing content
        $existingContent = UrlContent::where('hotelid', $hotelId)->first();

        // Prepare update data with only the fields that were sent
        $updateData = [
            'status' => $request->has('from_view_all') && $request->from_view_all ? UrlContent::STATUSES['updated'] : UrlContent::STATUSES['pending'],
            'listing_id' => $request->listing_id,
            'hotelid' => $hotelId,
            'added_by' => Auth::id()
        ];

        // Only include fields that were sent in the request
        if ($request->has('about')) {
            $updateData['about'] = $request->about;
        }
        if ($request->has('amenities')) {
            $updateData['amenities'] = $request->amenities;
        }
        if ($request->has('HotelQuestion') && $request->has('HotelAnswer')) {
            $updateData['HotelQuestion'] = $request->HotelQuestion;
            $updateData['HotelAnswer'] = $request->HotelAnswer;
        }

        Log::channel('stack')->info('Update data:', $updateData);

        // Handle content storage based on whether it's from View All section
        if ($request->has('from_view_all') && $request->from_view_all) {
            // View All section - save to both contents and respective tables
            if ($existingContent) {
                $updateFields = array_intersect_key($updateData, array_flip(['status', 'about', 'amenities', 'HotelQuestion', 'HotelAnswer']));
                $existingContent->update($updateFields);
                $content = $existingContent;
            } else {
                $updateData['category'] = 'About'; // Default value since required by DB
                $content = UrlContent::create($updateData);
            }

            // Update TPHotel table if about content is provided
            if ($request->has('about')) {
                \DB::table('TPHotel')
                    ->where('id', $hotelId)
                    ->update(['about' => $request->about]);
            }

            // Update facilities in TPHotel if amenities are provided
            if ($request->has('amenities')) {
                $amenityNames = explode(',', $request->amenities);
                $amenityNames = array_map('trim', $amenityNames);
                
                // Get amenity IDs from TPhotel_amenities table
                $amenityIds = \DB::table('TPHotel_amenities')
                    ->whereIn('name', $amenityNames)
                    ->pluck('id')
                    ->toArray();

                if (!empty($amenityIds)) {
                    // Get existing facilities
                    $existingFacilities = \DB::table('TPHotel')
                        ->where('id', $hotelId)
                        ->value('facilities');

                    // If there are existing facilities, merge them with new ones
                    if (!empty($existingFacilities)) {
                        $existingIds = array_map('trim', explode(',', $existingFacilities));
                        // Merge existing and new IDs, remove duplicates
                        $amenityIds = array_unique(array_merge($existingIds, $amenityIds));
                    }

                    \DB::table('TPHotel')
                        ->where('id', $hotelId)
                        ->update(['facilities' => implode(',', $amenityIds)]);
                }
            }

            // Save FAQ to HotelQuestion table if provided
            if ($request->has('HotelQuestion') && $request->has('HotelAnswer')) {
                \DB::table('HotelQuestion')->insert([
                    'HotelId' => $hotelId,
                    'Question' => $request->HotelQuestion,
                    'Answer' => $request->HotelAnswer
                ]);
            }
        } else {
            // Regular section - only save to contents table
            if ($existingContent) {
                $updateFields = array_intersect_key($updateData, array_flip(['status', 'about', 'amenities', 'HotelQuestion', 'HotelAnswer']));
                $existingContent->update($updateFields);
                $content = $existingContent;
            } else {
                $updateData['category'] = 'About'; // Default value since required by DB
                $content = UrlContent::create($updateData);
            }
        }

        Log::channel('stack')->info('Content operation completed:', $content->toArray());
        return response()->json(['success' => true, 'message' => 'Content processed', 'content' => $content]);
    }

    public function getListingContent($listingId)
    {
        $listing = UrlListing::with([
            'contents' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'contents.addedBy:id,name',
            'contents.acceptedBy:id,name'
        ])->findOrFail($listingId);

        return response()->json([
            'url' => $listing->url,
            'contents' => $listing->contents->map(function($content) {
                return [
                    'id' => $content->id,
                    'category' => $content->category,
                    'status' => $content->status,
                    'about' => $content->about,
                    'amenities' => $content->amenities,
                    'HotelQuestion' => $content->HotelQuestion,
                    'HotelAnswer' => $content->HotelAnswer,
                    'added_by_name' => $content->addedBy->name ?? 'Unknown',
                    'accepted_by_name' => $content->acceptedBy->name ?? null,
                    'created_at' => $content->created_at
                ];
            })
        ]);
    }

    public function showQaDashboard()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->with('error', 'Unauthorized access');
        }
        return view('url-management.qa-dashboard');
    }

    public function getPendingContents(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        Log::channel('stack')->info('Filter parameter:', [$request->filter]); // Log the filter parameter

        $query = UrlContent::with(['listing', 'addedBy'])
            ->where('status', 'pending');

        // Check if a filter is provided
        if ($request->has('filter') && $request->filter) {
            $filter = $request->filter;
            $query->whereHas('listing', function($q) use ($filter) {
                $q->where('url', 'LIKE', '%' . $filter . '%');
            });
        }

        $pendingContents = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json($pendingContents);
    }

    public function acceptContent(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized access'], 403);
        }

        $request->validate([
            'content_id' => 'required|exists:contents,id'
        ]);

        $content = UrlContent::findOrFail($request->content_id);
        $content->update([
            'status' => 'accepted',
            'accepted_by' => Auth::id(),
            'accepted_at' => now()
        ]);

        return response()->json([
            'message' => 'Content accepted successfully',
            'content' => $content
        ]);
    }

    public function requestUpdate(Request $request)
    {
        $request->validate([
            'content_id' => 'required|exists:contents,id'
        ]);

        $content = UrlContent::findOrFail($request->content_id);
        
        if (Auth::id() !== $content->added_by && !Auth::user()->isAdmin()) {
            return response()->json(['error' => 'Unauthorized action'], 403);
        }

        $content->update([
            'status' => 'pending',
            'accepted_by' => null,
            'accepted_at' => null
        ]);

        return response()->json([
            'message' => 'Content update requested',
            'content' => $content
        ]);
    }
}
