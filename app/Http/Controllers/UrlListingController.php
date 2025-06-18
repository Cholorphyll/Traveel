<?php

namespace App\Http\Controllers;

use App\Models\UrlListing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UrlListingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function showUploadForm()
    {
        return view('url-management.upload');
    }

    public function uploadUrls(Request $request)
    {
        $urls = [];
        
        // Get URLs from textarea
        if ($request->has('urls')) {
            $urls = array_filter(explode("\n", $request->urls), 'trim');
        }

        // Get URLs from file
        if ($request->hasFile('url_file')) {
            $file = $request->file('url_file');
            $extension = $file->getClientOriginalExtension();
            
            if ($extension === 'csv') {
                // Handle CSV file
                $handle = fopen($file->path(), 'r');
                while (($row = fgetcsv($handle)) !== false) {
                    if (!empty($row[0])) {
                        $urls[] = trim($row[0]);
                    }
                }
                fclose($handle);
            } else {
                // Handle TXT file
                $fileUrls = array_filter(explode("\n", file_get_contents($file->path())), 'trim');
                $urls = array_merge($urls, $fileUrls);
            }
        }

        if (empty($urls)) {
            return back()->withErrors(['error' => 'Please provide URLs either in the text area or upload a file']);
        }

        // Remove duplicates
        $urls = array_unique($urls);

        $results = [];
        foreach ($urls as $url) {
            if (empty($url)) continue;
            
            $isExisting = UrlListing::where('url', $url)->exists();
            if (!$isExisting) {
                UrlListing::create([
                    'url' => $url,
                    'added_by' => Auth::id()
                ]);
            }
            $results[] = [
                'url' => $url,
                'isExisting' => $isExisting
            ];
        }

        return back()->with('success', 'URLs processed successfully')->with('results', $results);
    }

    public function showListings()
    {
        return view('url-management.listing');
    }

    public function getListing(Request $request)
    {
        $listings = UrlListing::with(['contents' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
        ->orderBy('created_at', 'desc')
        ->paginate(15);

        return response()->json($listings);
    }

    public function search(Request $request)
    {
        return view('url-management.search');
    }

    public function searchUrls(Request $request)
    {
        $url = $request->input('url');
        $listings = collect();
        $error = null;

        if ($url) {
            $listings = UrlListing::with(['contents' => function($query) {
                $query->orderBy('created_at', 'desc');
            }])
            ->where('url', 'like', '%' . $url . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

            if ($listings->isEmpty()) {
                $error = 'No URLs found matching your search criteria';
            }
        }

        return response()->json([
            'listings' => $listings,
            'error' => $error
        ]);
    }
}
