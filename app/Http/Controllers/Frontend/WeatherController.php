<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Response; 
class WeatherController extends Controller
{
    public function weatherindex(){
        request()->session()->forget('widget_theme');
        $lname = "NEW YORK";
        $TRAVEL_PAYOUT_TOKEN = "Q4UYYMAP3RLBNPRN2SS3REYW4";       
        $getweather = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/$lname?key=$TRAVEL_PAYOUT_TOKEN";
        $weatherdata = Http::withoutVerifying()->get($getweather);
        $weatherdata = json_decode($weatherdata);
        return view('weather/weather_index', ['weatherdata' => $weatherdata, 'lname' => $lname]);
    }
    public function searchloc(request $request){
        $search = $request->get('val');

        $result = array();
    
        $query = DB::table('Location')
        ->leftJoin('Country', 'Location.CountryId', '=', 'Country.CountryId')
        ->select('Location.LocationId', 'Location.Name as lname', 'Country.Name as cname', 'Country.CountryId')
        ->where('Location.Name', 'LIKE', $search . '%') // Updated the LIKE condition
        ->limit(4)
        ->get();
    
        if($query->isEmpty()){
            $query = DB::table('Location')
            ->leftJoin('Country', 'Location.CountryId', '=', 'Country.CountryId')
            ->select('Location.LocationId', 'Location.Name as lname', 'Country.Name as cname', 'Country.CountryId')
            ->where(function ($query) use ($search) {
              
                $query->whereRaw("CONCAT(Location.Name, ' ', Country.Name) LIKE ?", ["%{$search}%"]);
            })
            ->limit(4)
            ->get();
    
        }
        foreach ($query as $loc) {
            $result[] = [
                'id' => $loc->LocationId,
                'value' => $loc->lname,
                'country' => $loc->cname,
                'countryid' => $loc->CountryId
            ];
        }
    
        return response()->json($result);
    }
    public function getwearapi(request $request){
       
        $request->session()->forget('widget_theme');
        $theme = $request->get('theme');
        $request->session()->put('widget_theme', $theme);
        $lname = $request->get('locname');
        $theme = $request->get('theme');
        $width = $request->get('width');
        $height = $request->get('height');

        $loc = $request->get('locname');
        $base_url = url('/');
        
        $url = $base_url.'/weather-widget?locname='.$loc;
        // Logic to generate or fetch the widget code
        $widgetCode = '<iframe src="'.$url.'" width="'.$width.'" height="'.$height.'" Theme="'. $theme.'" frameborder="0" scrolling="no"></iframe>';
        return response($widgetCode);
      //  return response()->json(['widget_code' => $widgetCode]);

        // $TRAVEL_PAYOUT_TOKEN = "Q4UYYMAP3RLBNPRN2SS3REYW4";       
        // $getweather = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/$lname?key=$TRAVEL_PAYOUT_TOKEN";
        // $weatherdata = Http::withoutVerifying()->get($getweather);
        // $weatherdata = json_decode($weatherdata);

        // return view('weather/get_weather_result',['weatherdata'=>$weatherdata,'lname'=>$lname ]);
       // weather_widget 
    }
    public function weather_widget(Request $request)
{
    $theme = $request->session()->get('widget_theme');
    $lname = $request->get('locname');
    $TRAVEL_PAYOUT_TOKEN = "Q4UYYMAP3RLBNPRN2SS3REYW4";       
    $getweather = "https://weather.visualcrossing.com/VisualCrossingWebServices/rest/services/timeline/$lname?key=$TRAVEL_PAYOUT_TOKEN";
    $weatherdata = Http::withoutVerifying()->get($getweather);
    $weatherdata = json_decode($weatherdata);

    return view('weather/weather_widget', ['weatherdata' => $weatherdata, 'lname' => $lname,'theme'=>$theme]);
}

    public function createwidget(Request $request)

    {    
        $loc = $request->get('locname');     
        $base_url = url('/');
        $width = $request->get('width');
        $height = $request->get('height');
        $height = $request->get('height');

        $request->session()->forget('widget_theme');
        $theme = $request->get('theme');
        $request->session()->put('widget_theme', $theme);
        
        $url = $base_url.'/weather-widget?locname='.$loc;
      
        $widgetCode = '<iframe src="'.$url.'" width="'.$width.'" height="'.$height.'" Theme="'. $theme.'"  frameborder="0" scrolling="no"></iframe>';
        
        return response()->json(['widget_code'=> $widgetCode]);
    }

}
