<?php

namespace App\Http\Controllers\url;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class UrlController extends Controller
{
  public function urlindex() 
  { 
     $getweb = DB::table('website_urls')
     ->orderByRaw('
     (COALESCE(faq, 0) + COALESCE(description, 0) + COALESCE(dataEntry, 0)) DESC
 ')
     ->get();
      return view('url.index')->with('getweb',$getweb);   
  } 

  public function add_url() 
  { 
      return view('url.add_url');   
  } 
    // public function processForm(Request $request)
        // {
        //     $request->validate([
        //         'urls' => 'required|string',
        //     ]);
    
        //     $urls = explode("\n", $request->input('urls'));
    
        //     foreach ($urls as $url) {
        //         $url = trim($url);
    
        //         if (!empty($url)) {
        //             DB::table('website_urls')->insert(['url' => $url]);
        //         }
        //     }
    
        //     return redirect()->route('urlindex')->with('success', 'URLs successfully saved.');
        // }
     
        public function processForm(Request $request)
        {
           
            $request->validate([
                'urls' => 'required|string',
            ]);

            $urls = explode("\n", $request->input('urls'));

            foreach ($urls as $url) {
                $url = trim($url);

                if (!empty($url)) {
                   
                    $existingUrl = DB::table('website_urls')->where('url', $url)->first();

                    if (!$existingUrl) {
                     
                        DB::table('website_urls')->insert(['url' => $url]);
                    }
                   
                }
            }

            return redirect()->route('urlindex')->with('success', 'URLs successfully saved.');
        }


        public function updateurlval(Request $request)
        {
            $urlId = $request->input('url_id');
            $column = $request->input('column');
            $value = $request->input('value');
          
            DB::table('website_urls')->where('id', $urlId)->update([$column => $value]);

            // return redirect()->route('urlindex')->with('success', 'URLs successfully saved.');
            $getweb = DB::table('website_urls')
            ->orderByRaw('
            (COALESCE(faq, 0) + COALESCE(description, 0) + COALESCE(dataEntry, 0)) DESC
        ')
            ->get();
             return view('url.get_url_data')->with('getweb',$getweb); 
        }

    
}
