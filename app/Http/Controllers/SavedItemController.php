<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SavedItemController extends Controller
{
    /**
     * Toggle an item's saved status (save or unsave)
     */
    public function toggleSavedItem(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'item_id' => 'required|string',
            'item_type' => 'required|string',
            'item_title' => 'required|string',
            'item_image' => 'nullable|string',
            'action' => 'required|in:save,unsave'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid input data',
                'errors' => $validator->errors()
            ], 422);
        }

        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $userId = Auth::id();
        $itemId = $request->item_id;
        $itemType = $request->item_type;
        $action = $request->action;

        // Get location ID from the item
        $locationId = null;
        if ($itemType === 'attraction') {
            $locationId = DB::table('Sights')
                ->where('SightId', $itemId)
                ->value('LocationId');
        } elseif ($itemType === 'restaurant') {
            $locationId = DB::table('Restaurants')
                ->where('SightId', $itemId)
                ->value('LocationId');
        }

        if ($action === 'save') {
            // Check if item is already saved
            $exists = DB::table('saved_items')
                ->where('user_id', $userId)
                ->where('item_id', $itemId)
                ->exists();

            if (!$exists) {
                // Save the item
                DB::table('saved_items')->insert([
                    'user_id' => $userId,
                    'item_id' => $itemId,
                    'item_type' => $itemType,
                    'title' => $request->item_title,
                    'image_path' => $request->item_image,
                    'location_id' => $locationId,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item saved successfully'
            ]);
        } else {
            // Unsave the item
            DB::table('saved_items')
                ->where('user_id', $userId)
                ->where('item_id', $itemId)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item removed from saved items'
            ]);
        }
    }

    /**
     * Get saved items for the current location
     */
    public function getSavedItems(Request $request)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated'
            ], 401);
        }

        $userId = Auth::id();
        $locationId = $request->location_id;

        // Get saved items for this location
        $savedItems = DB::table('saved_items')
            ->where('user_id', $userId)
            ->where('location_id', $locationId)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        // Get total count
        $savedItemsCount = DB::table('saved_items')
            ->where('user_id', $userId)
            ->where('location_id', $locationId)
            ->count();

        // Get location name
        $locationName = DB::table('Location')
            ->where('LocationId', $locationId)
            ->value('Name');

        // Generate HTML for the saved items section
        $html = view('partials.saved-items', [
            'savedItems' => $savedItems,
            'savedItemsCount' => $savedItemsCount,
            'locationName' => $locationName
        ])->render();

        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }
}
