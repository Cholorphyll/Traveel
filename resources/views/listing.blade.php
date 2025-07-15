<!DOCTYPE html>
<html lang="en-US">
<head>

   <meta name="robots" content="index, follow">
	<link rel="canonical" href="{{ url()->current() }}" />

  <!-- Google Tag Manager -->
  <script>
   (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-PTHP3JH4');
  </script>
  <!-- End Google Tag Manager -->
  @if(!empty($locationPatent)) @php $locationPatent = array_reverse($locationPatent); @endphp @endif
  @php $month = date('F'); $year = date('Y'); $lname =""; @endphp
  @if(!empty($searchresults)) @php $lname = $location_name @endphp @endif
  @php
    $title = '';

    if ($catheading != "") {
        $title .= 'Top ';
        if ($totalCountResults != 0) {
            $title .= $totalCountResults . ' ';
        }
        if ($catheading == 'mustsee') {
            $title .= 'Attractions';
        } else {
            $title .= $catheading;
        }
        $title .= ' in ' . $lname;
        if ($location_parent_name != "") {
            $title .= ', ' . $location_parent_name;
        }
        $title .= ' with Travell';
    } else {
        $title .= 'Best Places to Visit in ' . $lname;
        if ($location_parent_name != "") {
            $title .= ', ' . $location_parent_name;
        }       
        $title .= '- A Complete Travel Guide - Travell (2025)';
    }
@endphp

<title>{{ $title }}</title>


@php
    $description = 'Explore';

    if ($catheading != "" && $catheading != "mustsee") {
        $description .= $catheading;
    } else {
        $description .= 'Attractions';
    }

    $description .= ' in ' . $lname;

    if ($location_parent_name != "") {
        $description .= ', ' . $location_parent_name;
    }

    $description .= '  like a local with our curated list of must-visit sites. From historic districts to natural wonders, find everything you need to plan your trip and create lasting memories.';
@endphp

<meta name="description" content="{{ $description }}">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/slick.min.js')}}"></script>


  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">
  <link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
	<script src="{{ asset('js/cookie-consent.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
  
  <style>
    /* Results Loader Styles */
    .tr-results-loader {
      min-height: 300px;
      width: 100%;
      background-color: rgba(255, 255, 255, 0.8);
      z-index: 999;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 20px 0;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    
    .tr-loader-container {
      text-align: center;
    }
    
    .tr-spinner {
      width: 50px;
      height: 50px;
      border: 5px solid #f3f3f3;
      border-top: 5px solid #28965A;
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin: 0 auto 20px;
    }
    
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    
    /* Hide the loader when results are loaded */
    .results-loaded .tr-results-loader {
      display: none;
    }
  </style>
 
 <script type="text/javascript">
  $(document).ready(function() {
    $('.tr-search-by-category').slick({
      autoplay: true,
      autoplaySpeed: 2000,
      dots: false,
      arrows: false,
      infinite: false,
      slidesToShow: 1,
      slidesToScroll: 1,
      vertical: true,
    });
    $('.tr-tickets-silder').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: false,
      arrows: true,
      infinite: false,
      slidesToShow: 2.1,
      slidesToScroll: 1,
      responsive: [{
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 1.5,
          slidesToScroll: 1
        }
      }]
    });
    $('.tr-explore-filter-slider').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: false,
      arrows: true,
      infinite: false,
      slidesToShow: 3,
      slidesToScroll: 1,
      variableWidth: true,
      responsive: [{
        breakpoint: 768,
        settings: {
          arrows: false,
        }
      }]
    });
    $('.tr-experience-slider').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: true,
      arrows: false,
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1
    });
    $('.tr-market-slider').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: false,
      arrows: true,
      infinite: false,
      slidesToShow: 3,
      slidesToScroll: 1,
      responsive: [{
        breakpoint: 768,
        settings: {
          arrows: false,
          slidesToShow: 2.5,
          slidesToScroll: 1
        }
      }]
    });
    $('.tr-common-slider').slick({
          autoplay: false,
          autoplaySpeed: 2000,  
          dots: false,           
          arrows: true,         
          infinite: false,       
          slidesToShow: 3,      
          slidesToScroll: 1,
          responsive: [
            {
              breakpoint: 768, 
              settings: {
                arrows: false, 
                slidesToShow: 2.5,  
                slidesToScroll: 1
              }
            }
          ]     
        });

  });
  </script>

  <?php
      $locationPatents = $locationPatent;
      // Initialize position counter
      $position = 1; // Start with Travell
      ?>
 <script type="application/ld+json">
@php
      // Define $currentUrl if it doesn't exist
if (!isset($currentUrl)) {
    $currentUrl = url()->current();
}

// Define $homeUrl if it doesn't exist
if (!isset($homeUrl)) {
    $homeUrl = url('/');
}
// Pre-build the JSON structure to avoid syntax errors with Blade conditionals
$jsonData = [
    "@context" => "https://schema.org",
    "@graph" => [
        // WebPage
        [
            "@type" => "WebPage",
            "@id" => $currentUrl . "#webpage",
            "name" => $title,
            "url" => $currentUrl,
            "isPartOf" => [
                "@id" => "https://www.travell.co/#website"
            ],
            "breadcrumb" => [
                "@id" => $currentUrl . "#breadcrumb"
            ],
            "inLanguage" => "en"
        ],
        
        // BreadcrumbList
        [
            "@type" => "BreadcrumbList",
            "@id" => $currentUrl . "#breadcrumb",
            "itemListElement" => []
        ],
        
        // TouristDestination
        [
            "@type" => "LocalBusiness",
            "name" => !empty($breadcumb) ? $breadcumb[0]->LName : $lname,
            "url" => $currentUrl
        ],
        
        // WebSite
        [
            "@type" => "WebSite",
            "@id" => "https://www.travell.co/#website",
            "url" => $homeUrl,
            "name" => "Travell",
            "publisher" => [
                "@id" => "https://www.travell.co/#organization"
            ]
        ],
        
        // Organization
        [
            "@type" => "Organization",
            "@id" => "https://www.travell.co/#organization",
            "name" => "Travell",
            "url" => $homeUrl,
            "logo" => asset('frontend/images/logo.png')
        ]
    ]
];

// Build breadcrumb items
$breadcrumbItems = [];

// Home
$breadcrumbItems[] = [
    "@type" => "ListItem",
    "position" => 1,
    "name" => "Travell",
    "item" => $homeUrl
];

// Continent (if available)
$position = 2;
if (!empty($breadcumb) && isset($breadcumb[0]->ccName) && $breadcumb[0]->ccName != "") {
    $breadcrumbItems[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "name" => $breadcumb[0]->ccName,
        "item" => route('explore_continent_list', [$breadcumb[0]->contid, $breadcumb[0]->ccName])
    ];
}

// Country
if (!empty($breadcumb) && isset($breadcumb[0]->CountryName) && $breadcumb[0]->CountryName != "") {
    $breadcrumbItems[] = [
        "@type" => "ListItem",
        "position" => $position++,
        "name" => $breadcumb[0]->CountryName,
        "item" => route('explore_country_list', [$breadcumb[0]->CountryId, $breadcumb[0]->cslug])
    ];
}

// Parent locations
if (!empty($locationPatents)) {
    foreach ($locationPatents as $location) {
        $breadcrumbItems[] = [
            "@type" => "ListItem",
            "position" => $position++,
            "name" => $location['Name'],
            "item" => route('search.results', [$location['LocationId'] . '-' . strtolower($location['slug'])])
        ];
    }
}

// Current location
$breadcrumbItems[] = [
    "@type" => "ListItem",
    "position" => $position,
    "name" => !empty($breadcumb) ? $breadcumb[0]->LName : $lname,
    "item" => $currentUrl
];

// Add breadcrumb items to the graph
$jsonData['@graph'][1]['itemListElement'] = $breadcrumbItems;

// Add geo coordinates if available
if (isset($lat) && isset($lng)) {
    $jsonData['@graph'][2]['geo'] = [
        "@type" => "GeoCoordinates",
        "latitude" => $lat,
        "longitude" => $lng
    ];
}

// Add description if available
if (isset($description)) {
    $jsonData['@graph'][2]['description'] = $description;
}

// Add containedIn for country (using containedIn instead of containedInPlace)
if (!empty($breadcumb) && isset($breadcumb[0]->CountryName)) {
    $jsonData['@graph'][2]['containedIn'] = [
        "@type" => "Country",
        "name" => $breadcumb[0]->CountryName
    ];
}

// Add ItemList if searchresults exist
if (isset($searchresults) && count($searchresults) > 0) {
    $itemList = [
        "@type" => "ItemList",
        "name" => "Things to Do in " . (!empty($breadcumb) ? $breadcumb[0]->LName : $lname),
        "itemListOrder" => "https://schema.org/ItemListOrderAscending",
        "numberOfItems" => count($searchresults),
        "itemListElement" => []
    ];
    
    foreach ($searchresults as $key => $attraction) {
        // Get attraction name - use Title property if available
        $attractionName = isset($attraction->Title) ? $attraction->Title : 
                         (isset($attraction->Name) ? $attraction->Name : 
                         (isset($attraction->name) ? $attraction->name : 
                         (!empty($breadcumb) ? $breadcumb[0]->LName . ' Attraction' : 'Tourist Attraction')));
        
        // Get attraction URL using the appropriate format
        $attractionUrl = isset($attraction->slugid) && isset($attraction->SightId) && isset($attraction->Slug) ? 
                        asset('at-'.$attraction->slugid.'-'.$attraction->SightId.'-'.strtolower($attraction->Slug)) : 
                        (isset($attraction->SightId) && isset($attraction->slug) ? 
                        route('sight.details', [$attraction->SightId, $attraction->slug]) : 
                        (isset($attraction->sightId) && isset($attraction->Slug) ? 
                        route('sight.details', [$attraction->sightId, $attraction->Slug]) : $currentUrl));
        
        $item = [
            "@type" => "ListItem",
            "position" => $key + 1,
            "item" => [
                "@type" => "LocalBusiness",
                "name" => $attractionName,
                "url" => $attractionUrl,
                "image" => isset($attraction->MainImage) ? asset('storage/sights/' . $attraction->MainImage) : '',
                "address" => [
                    "@type" => "PostalAddress",
                    "addressLocality" => !empty($breadcumb) ? $breadcumb[0]->LName : $lname,
                    "addressCountry" => !empty($breadcumb) && isset($breadcumb[0]->CountryName) ? $breadcumb[0]->CountryName : ''
                ],
                "priceRange" => "$$",
                "telephone" => isset($attraction->Phone) ? $attraction->Phone : ''
            ]
        ];
        
        if (isset($attraction->Latitude) && isset($attraction->Longitude)) {
            $item['item']['geo'] = [
                "@type" => "GeoCoordinates",
                "latitude" => $attraction->Latitude,
                "longitude" => $attraction->Longitude
            ];
        }
        
        $itemList['itemListElement'][] = $item;
    }
    
    $jsonData['@graph'][] = $itemList;
}

// Add FAQPage only if FAQs section is visible and FAQs exist
$faqSectionVisible = isset($showFAQs) ? $showFAQs : false;

if ($faqSectionVisible && isset($faqs) && count($faqs) > 0) {
    $faqPage = [
        "@type" => "FAQPage",
        "mainEntity" => []
    ];
    
    foreach ($faqs as $faq) {
        $faqPage['mainEntity'][] = [
            "@type" => "Question",
            "name" => $faq->question,
            "acceptedAnswer" => [
                "@type" => "Answer",
                "text" => $faq->answer
            ]
        ];
    }
    
    $jsonData['@graph'][] = $faqPage;
}

// Output the JSON with proper encoding
echo json_encode($jsonData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp
</script>
</head>

<body>
  <!-- No full-page preloader -->
  <!--HEADER-->
  @include('frontend.header')

  <!-- Mobile Navigation-->
  @include('frontend.mobile_nav')

  <div class="tr-explore-listing-section">
    <div class="container">
      <div class="tr-explore-listing">
        <div class="tr-explore-left-section">

           <input type="hidden" id="shown-attraction-ids" value="{{ implode(',', $searchresults->pluck('SightId')->toArray()) }}">
          
         
 
          <?php $url = request()->route('id');
                $parts = explode('-', $url,2);
                $lastPart = $parts[1];
                            ?>
          <span id="locid" class="d-none">@if(!empty($searchresults) && count($searchresults) > 0 && isset($searchresults[0]->LocationId)){{$searchresults[0]->LocationId}}@elseif(!empty($locationID)){{$locationID}}@endif</span>
          <span id="slug" class="d-none"> {{$lastPart}}</span>
          <span class="d-none sightlist">sightlist</span>
          <span>       
          <?php $url = request()->route('id');
                $parts = explode('-', $url,2);
                $lastPart = $parts[1];
                            ?>
          <span id="locid" class="d-none">@if(!empty($searchresults) && count($searchresults) > 0 && isset($searchresults[0]->LocationId)){{$searchresults[0]->LocationId}}@elseif(!empty($locationID)){{$locationID}}@endif</span>
          <span id="slug" class="d-none"> {{$lastPart}}</span>
          <span class="d-none sightlist">sightlist</span>
          <div id="getcatfilterdata">
            <!-- new category session data -->
            <?php $i = 1; $j = 0; ?>
            @if(!empty($searchresults))


            <!-- Now display all non-must-see items in tr-common-listing div -->
            <div class="tr-common-listing">
                @foreach($searchresults as $item)
                @php
                    // Determine item type
                    $itemType = 'attraction';
                    if (isset($item->SightId)) {
                        if (strpos($item->SightId, 'rest_') === 0) {
                            $itemType = 'restaurant';
                        } elseif (strpos($item->SightId, 'exp_') === 0) {
                            $itemType = 'experience';
                        }
                    }
                @endphp
                
                                @if($itemType == 'attraction')
                    <div class="tr-list" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="{{$item->SightId}}" data-type="attraction">
                        @php
                            $attractionHasVideo = false;
                            $mediaUrl = null; // Will store video or image URL
                            $isSightImageVideo = false; // Flag to indicate if the media is a video

                            if (isset($sightImages) && !$sightImages->isEmpty()) {
                                // First, check for a video
                                foreach ($sightImages as $sImage) {
                                    if ($sImage->Sightid == $item->SightId && str_contains($sImage->Image, 'vid')) {
                                        $mediaUrl = "https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/" . $sImage->Image;
                                        $attractionHasVideo = true;
                                        $isSightImageVideo = true;
                                        break; 
                                    }
                                }
                                // If no video was found, look for an image
                                if (!$attractionHasVideo) {
                                    foreach ($sightImages as $sImage) {
                                        if ($sImage->Sightid == $item->SightId && !str_contains($sImage->Image, 'vid')) { // Ensure it's not a video path
                                            $mediaUrl = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/" . $sImage->Image . "&width=920";
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            // Fallback if no specific media found from $sightImages
                            if (!$mediaUrl) {
                                $mediaUrl = asset('/images/Hotel lobby.svg');
                                $isSightImageVideo = false; // It's definitely an image now
                            }
                        @endphp

                        <div class="image-container {{ $isSightImageVideo ? 'video-media' : 'image-media' }}">
                            <a href="{{ asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug)) }}" target="_blank">
                                @if($isSightImageVideo)
                                    <video class="carousel-video w-100 h-100"
                                        autoplay loop playsinline muted
                                        onplay="hideCarouselControls({{ $loop->index }})" 
                                        onpause="showCarouselControls({{ $loop->index }})">
                                        {{-- Assumes this is inside the main @foreach($searchresults as $item => $loop) loop --}}
                                        {{-- If $loop is not available, you need to ensure $i or a similar index is correctly passed and used --}}
                                        <source src="{{ $mediaUrl }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img loading="lazy" src="{{ $mediaUrl }}" alt="{{ $item->Title }}">
                                @endif
                            </a>
                        </div>
                        <div class="tr-list-details">
                            <div class="tr-like-review">
                                <div class="tr-heart">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="12" height="12" rx="6" fill="#28965A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99782 3.90601C5.36052 3.19235 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99782 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99782 3.90601Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="tr-ranting-percent">
                                    @if(isset($item->Averagerating))
                                        {{$item->Averagerating}}%
                                    @else
                                        --%
                                    @endif
                                </div>
                            </div>
                            <h3 class="tr-title-name">
                                <a href="{{ asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug)) }}" target="_blank">{{$item->Title}}</a>                        
                            </h3>
                          <div class="tr-more-inform">
                      <ul>
                        @if(!empty($item->timing))
                        @php
                          $currentDay = strtolower(date('D'));
                          $currentTime = date('H:i');
                        @endphp
                        @foreach ($item->timing as $tm)
                        @if (!empty($tm->timings))
                        @php
                          $schedule = json_decode($tm->timings, true);
                          $isOpen = false; // Initialize isOpen as false by default
                          $formatetime = '';

                          if (isset($schedule['time'][$currentDay])) {
                            $openingtime = $schedule['time'][$currentDay]['start'];
                            $closingTime = $schedule['time'][$currentDay]['end'];

                            if ($openingtime == '00:00' && $closingTime == '23:59') {
                              $formatetime = '12:00';
                              $closingTime = '11:59';
                            }

                            if ($currentTime >= $openingtime && $currentTime <= $closingTime) {
                              $isOpen = true;
                            }
                          }
                        @endphp
                        @if ($isOpen)
                          <li><span class="timing_{{ $loop->parent->index }}">Open</span> {{ $formatetime }}</li>
                        @else
                          <li><span class="timing_{{ $loop->parent->index }}">Closed Today</span></li>
                        @endif
                        @endif
                        @endforeach
                        @endif
                      </ul>
                    </div>  
                        </div>
                    </div>
                @elseif($itemType == 'restaurant')
                    {{-- Keep your existing restaurant code here --}}
                    <div class="tr-list restaurant" onmouseover="highlightRestaurantMarker(this)" onmouseout="unhighlightRestaurantMarker(this)"
                      data-restaurant-id="{{$item->SightId}}" data-type="restaurant">
                      <div class="image-container image-media">
                        <a href="{{ url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug) }}" target="_blank">
                        <img loading="lazy" src="{{ asset('/images/Group 1171275916.png') }}" alt="restaurant image">
                      </a>
                      </div>
                      <div class="tr-list-details">
                        <div class="tr-like-review">
                          <div class="tr-heart">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="12" height="12" rx="6" fill="#28965A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99782 3.90601C5.36052 3.19235 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99782 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99782 3.90601Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                          </div>
                          <div class="tr-ranting-percent">
                            @if(isset($item->Averagerating))
                              {{$item->Averagerating}}%
                            @else
                              --%
                            @endif
                          </div>
                        </div>
                        <h3> <a href="{{ url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug) }}" target="_blank">{{$item->Title}}</a>     
                        </h3>
                      </div>
                    </div>
                @elseif($itemType == 'experience')
                    {{-- Keep your existing experience code here --}}
                    <div class="tr-list experience" onmouseover="highlightExperienceMarker(this)" onmouseout="unhighlightExperienceMarker(this)"
                       data-experience-id="{{$item->SightId ?? ''}}" data-type="experience">
                      <div class="image-container image-media">
                        <a href="@if(!empty($item->viator_url)) {{$item->viator_url}} @else {{route('experince',[$item->slugid.'-'.str_replace('exp_', '', $item->SightId).'-'.$item->Slug])}} @endif" target="_blank">
                          @if(!empty($item->Img1))
                            <img loading="lazy" src="{{$item->Img1}}" alt="Experience Image">
                          @else
                            <img loading="lazy" src="{{ asset('/images/Hotel lobby.svg') }}" alt="Experience Image">
                          @endif
                        </a>
                      </div>
                      <div class="tr-list-details">
                        <div class="tr-like-review">
                          <div class="tr-heart">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="12" height="12" rx="6" fill="#28965A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99782 3.90601C5.36052 3.19235 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99782 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99782 3.90601Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                          </div>
                          <div class="tr-ranting-percent">
                            @if(isset($item->Averagerating))
                              {{$item->Averagerating}}%
                            @else
                              --%
                            @endif
                          </div>
                        </div>
                         <h3><a href="@if(!empty($item->viator_url)) {{$item->viator_url}} @else {{route('experince',[$item->slugid.'-'.str_replace('exp_', '', $item->SightId).'-'.$item->Slug])}} @endif" target="_blank">{{$item->Title ?? $item->Name}}</a>      
                        </h3>
                      </div>
                    </div>
                @endif
                @endforeach
            </div>
            @endif
			<div id="loading" class="tr-page-loader" style="display: none;"> 
   			 <div class="tr-loader-container">
       	 			<div class="tr-spinner"></div>
       	 			<p>Finding the best attractions...</p>
    		</div>
			</div>
			<button type="button" class="tr-btn tr-load-more">Load More</button>
                            
			</div>
                            
		<div class="tr-map-and-filter">
            <button type="button" class="tr-explore-map-btn map"><svg width="14" height="14" viewBox="0 0 14 14"
                fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2464_12970)">
                  <path
                    d="M0.583984 3.4974V12.8307L4.66732 10.4974L9.33398 12.8307L13.4173 10.4974V1.16406L9.33398 3.4974L4.66732 1.16406L0.583984 3.4974Z"
                    stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"></path>
                  <path d="M4.66602 1.16406V10.4974" stroke="white" stroke-width="1.2" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                  <path d="M9.33398 3.5V12.8333" stroke="white" stroke-width="1.2" stroke-linecap="round"
                    stroke-linejoin="round"></path>
                </g>
                <defs>
                  <clipPath id="clip0_2464_12970">
                    <rect width="14" height="14" fill="white"></rect>
                  </clipPath>
                </defs>
              </svg>Map</button>
          </div>

        </div>
        <div class="tr-explore-right-section" style="">
          <div class="tr-map-section">
            <button type="button" class="btn-close"></button>
            <div class="tr-hotel-on-map">
              <form>
              </form>
            </div>
            @if(!empty($searchresults))
            <button type="button" class="btn-close" aria-label="Close"></button>
  		    <div id="map1" class="explore-listing-map"></div>
            @endif
          </div>
          <div class="tr-explore-overlay"></div>
        </div>
      </div>
    </div>
  </div>
	
                    
   <!--Quick Portrait-->
    <div class="row">
        <div class="col-sm-12">
            <div class="tr-single-page">
                <div class="tr-terms-and-conditions-section">
                    <h3 style="font-weight: bold; margin-bottom: 20px; font-size: 24px;">

                    </h3>
                    <p style="margin-top: 20px;">

                    </p>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="tr-single-page">
                    <h2 class="section-title mb-16 font-bold">{{ $cityName ?? ($locn ?? 'City') }}: Quick Portrait
                    </h2>
                    <div class="about-section mb-32">
                    @php
                        $currentCity = $cityName ?? ($locn ?? 'City');
                        $aboutContent = null;
                        
                        // Check if location exists and extract About content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $aboutContent = isset($location->About) && !empty($location->About) ? $location->About : null;
                            } elseif (is_array($location)) {
                                $aboutContent = isset($location['About']) && !empty($location['About']) ? $location['About'] : null;
                            }
                        }
                    @endphp
                    @if(!is_null($aboutContent) && !empty($aboutContent))
                        {!! $aboutContent !!}
                    @else
                        @if($currentCity == 'Dubai')
                            <p>A destination where every sunrise is a golden promise and every sunset a glittering spectacle, Dubai is not just a place to visit – it's a place to be endlessly inspired." Dubai is the UAE's dazzling metropolis – where desert sands meet futuristic skylines. It's a city of contrast and ambition, offering both rich Arabian heritage and ultra-modern experiences. Dubai is the shimmering crown of the United Arab</p>
                            <p>Emirates – a futuristic oasis where golden deserts meet glass skyscrapers that seem to touch the very edge of the sky. It is a place where ancient Bedouin traditions breathe alongside ultramodern lifestyles, where you can stroll through bustling spice-scented souks in the morning and dine atop the world's tallest building by evening.</p>
                            <p>Shopping malls are not just for shopping here; they house indoor ski slopes, giant aquariums, and virtual reality parks, making each visit an adventure of its own. Dubai is a destination that redefines the meaning of luxury and leisure, blending thrilling desert safaris with beach relaxation, Michelin-starred dining with</p>
                            <p>street food delights, and cultural immersions with futuristic innovations. It is a city that promises limitless possibilities, inviting you to witness human ambition carved into architectural marvels while still feeling the timeless soul of Arabia echoing through its warm winds and golden sands.</p>
                        @else
                            <p>{{ $currentCity }} is a captivating destination that offers visitors a unique blend of cultural experiences, scenic beauty, and unforgettable adventures. From its distinctive landmarks to its local cuisine, {{ $currentCity }} presents travelers with countless opportunities to explore and discover.</p>
                            <p>Whether you're interested in historical sites, natural wonders, or vibrant city life, {{ $currentCity }} has something to offer every type of traveler. The city's rich heritage and modern developments create a fascinating contrast that makes it a must-visit destination.</p>
                            <p>Visitors to {{ $currentCity }} can enjoy a diverse range of activities, from exploring museums and galleries to sampling local delicacies at restaurants and street food stalls. The city's unique atmosphere and welcoming locals make every experience memorable.</p>
                        @endif
                    @endif
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Best Time to Visit</h3>
                    @php
                        $bestTimeContent = null;
                        
                        // Check if location exists and extract BestTimeToVisit content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $bestTimeContent = isset($location->BestTimeToVisit) && !empty($location->BestTimeToVisit) ? $location->BestTimeToVisit : null;
                            } elseif (is_array($location)) {
                                $bestTimeContent = isset($location['BestTimeToVisit']) && !empty($location['BestTimeToVisit']) ? $location['BestTimeToVisit'] : null;
                            }
                        }
                    @endphp
                    @if(!is_null($bestTimeContent) && !empty($bestTimeContent))
                        {!! $bestTimeContent !!}
                    @else
                        @if($currentCity == 'Dubai')
                            <p>November – March | 20°C – 30°C (68°F – 86°F)</p>
                            <p>Pleasant weather for outdoor activities, beach days, and desert safaris.</p>
                            <h4 class="mb-8">Avoid:</h4>
                            <p>June – August | Above 40°C (104°F+)</p>
                            <p>Extreme heat limits outdoor plans.</p>
                            <p>Ramadan (Variable Dates): Reduced daytime dining and shorter attraction hours.</p>
                        @else
                            <p>The ideal time to visit {{ $currentCity }} depends on your preferences for weather and activities.</p>
                            <p>Generally, the most comfortable seasons offer moderate temperatures and fewer crowds.</p>
                            <p>Check local seasonal events and festivals when planning your trip to {{ $currentCity }}.</p>
                        @endif
                    @endif
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Top Reasons to Visit</h3>
                    @php
                        $topReasonsContent = null;
                        
                        // Check if location exists and extract TopReasonsToVisit content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $topReasonsContent = isset($location->TopReasonsToVisit) && !empty($location->TopReasonsToVisit) ? $location->TopReasonsToVisit : null;
                            } elseif (is_array($location)) {
                                $topReasonsContent = isset($location['TopReasonsToVisit']) && !empty($location['TopReasonsToVisit']) ? $location['TopReasonsToVisit'] : null;
                            }
                        }
                    @endphp
                    @if(!is_null($topReasonsContent) && !empty($topReasonsContent))
                        {!! $topReasonsContent !!}
                    @else
                        @if($currentCity == 'Dubai')
                            <ul class="styled-list mb-12">
                                <li>Burj Khalifa: World's tallest building with panoramic views</li>
                                <li>Desert Safaris: Dune bashing, camel rides, Bedouin camps</li>
                                <li>Shopping: Dubai Mall, Mall of the Emirates, traditional souks</li>
                                <li>Palm Jumeirah: Iconic man-made island with luxury resorts</li>
                                <li>Cultural Heritage: Al Fahidi Neighbourhood, Dubai Museum</li>
                                <li>Family Fun: Aquaventure Waterpark, IMG Worlds of Adventure</li>
                                <li>Cuisine: From street shawarma to Michelin-star dining</li>
                            </ul>
                        @else
                            <ul class="styled-list mb-12">
                                <li>Cultural Experiences: Discover the unique heritage of {{ $currentCity }}</li>
                                <li>Local Cuisine: Sample the distinctive flavors and dishes of the region</li>
                                <li>Scenic Beauty: Explore the natural landscapes and viewpoints</li>
                                <li>Historical Sites: Visit landmarks that tell the story of {{ $currentCity }}</li>
                                <li>Shopping: Find local crafts, souvenirs, and specialty items</li>
                                <li>Entertainment: Experience the local arts, music, and nightlife</li>
                                <li>Outdoor Activities: Enjoy recreational opportunities in and around {{ $currentCity }}</li>
                            </ul>
                        @endif
                    @endif
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Getting Around</h3>
                    @php
                        $gettingAroundContent = null;
                        
                        // Check if location exists and extract GettingAround content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $gettingAroundContent = isset($location->GettingAround) && !empty($location->GettingAround) ? $location->GettingAround : null;
                            } elseif (is_array($location)) {
                                $gettingAroundContent = isset($location['GettingAround']) && !empty($location['GettingAround']) ? $location['GettingAround'] : null;
                            }
                        }
                    @endphp
                    @if(!is_null($gettingAroundContent) && !empty($gettingAroundContent))
                        {!! $gettingAroundContent !!}
                    @else
                        @if($currentCity == 'Dubai')
                            <ul class="styled-list mb-12">
                                <li>Metro: Fast, affordable, connects major sights</li>
                                <li>Taxis & Ride Apps (Careem, Uber): Readily available</li>
                                <li>Hop-on Hop-off Buses: Great for first-time visitors</li>
                                <li>Car Rentals: Convenient but requires confident city driving</li>
                            </ul>
                        @else
                            <ul class="styled-list mb-12">
                                <li>Public Transportation: Explore the local transit options in {{ $currentCity }}</li>
                                <li>Taxis & Ride Services: Convenient for direct point-to-point travel</li>
                                <li>Walking Tours: Discover {{ $currentCity }} on foot for a more intimate experience</li>
                                <li>Rental Options: Consider bikes, scooters, or cars depending on your needs</li>
                            </ul>
                        @endif
                    @endif
                </div>

                <div class="info-section mb-32">
                    <h3 class="mb-8">Insider Tips</h3>
                    @php
                        $insiderTipsContent = null;
                        
                        // Check if location exists and extract InsiderTips content
                        if (isset($location) && !empty($location)) {
                            if (is_object($location)) {
                                $insiderTipsContent = isset($location->InsiderTips) && !empty($location->InsiderTips) ? $location->InsiderTips : null;
                            } elseif (is_array($location)) {
                                $insiderTipsContent = isset($location['InsiderTips']) && !empty($location['InsiderTips']) ? $location['InsiderTips'] : null;
                            }
                        }
                    @endphp
                    @if(!is_null($insiderTipsContent) && !empty($insiderTipsContent))
                        {!! $insiderTipsContent !!}
                    @else
                        @if($currentCity == 'Dubai')
                            <ul class="styled-list mb-12">
                                <li>Dress Code: Respectful attire in public areas; swimwear only at pools and beaches.</li>
                                <li>Tipping: Not mandatory but appreciated; ~10% in restaurants is common.</li>
                                <li>WiFi & Connectivity: Free WiFi at most public spaces and malls; tourist SIM cards widely available at airports.</li>
                                <li>Cultural Etiquette: Avoid public displays of affection, especially during Ramadan.</li>
                            </ul>
                        @else
                            <ul class="styled-list mb-12">
                                <li>Local Customs: Familiarize yourself with cultural norms and practices in {{ $currentCity }}</li>
                                <li>Best Deals: Look for city passes or discount cards for attractions in {{ $currentCity }}</li>
                                <li>Connectivity: Check mobile network coverage and WiFi availability for travelers</li>
                                <li>Safety Tips: Be aware of common tourist concerns and how to stay safe in {{ $currentCity }}</li>
                            </ul>
                        @endif
                    @endif
                </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Quick Portrait END -->                 

  <!--BREADCRUMB - START-->
            @if(!empty($breadcumb))
            <div class="tr-breadcrumb-section">
            <div class="container">
              <ul class="tr-breadcrumb">
       		<li><a href="https://www.travell.co">Travell</a></li>              
              @if($breadcumb[0]->ccName !="")
                <li><a href="{{ route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])}}">{{$breadcumb[0]->ccName}}</a></li>
                @endif
                <li><a href="{{ route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])}}">@if(!empty($breadcumb)) {{$breadcumb[0]->CountryName}} @endif</a></li>
                @if(!empty($locationPatent))
                <?php
                $locationPatent = $locationPatent;

                ?>
                  @foreach ($locationPatent as $location)
                <li><a href="{{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}">{{ $location['Name'] }}</a></li>
                @endforeach
                @endif
                <li>{{$breadcumb[0]->LName}}</li>
              </ul>
			</div>
            </div>
            @endif
          <!--BREADCRUMB - END-->
        
                
  <!--FOOTER-->
  @include('frontend.footer')
  <div class="overlay" id="overLay"></div>

  <!-- Share Modal -->
 <div class="modal" id="shareModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        <h3>Share this experiences</h3>
		    <div class="tr-share-infos">
          <div class="tr-share-details">
            <span class="tr-hotel-name"> @if(!empty($searchresults)) <h2 class="tr-title">@if($top_attractions == 1)Top Attractions in {{$lname}} 
              @else Things to do in {{$lname}} @endif</h2>@endif</span>
          </div>
        </div>
        <div class="tr-share-options">
          <div class="tr-share-option">
            <a href="javascript:void(0);" class="tr-copy">Copy link</a>
          </div>
          <div class="tr-share-option">
          <a href="#" id="emailShare" target="_blank" class="tr-email">Email</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="smsShare" target="_blank" class="tr-messages">Messages</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="whatsappShare" target="_blank" class="tr-whatsapp">WhatsApp</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="facebookShare" target="_blank" class="tr-facebook">Facebook</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="twitterShare" target="_blank" class="tr-twitter">Twitter</a>
        </div>
        <div class="tr-share-option">
          <a href="#" id="messengerShare" target="_blank" class="tr-messenger">Messenger</a>
        </div>
        <div class="tr-share-option">
          <a href="javascript:void(0);" onclick="copyEmbedCode()" class="tr-embed">Embed</a>
        </div>
      </div>

      <!-- Feedback Alerts -->
      <div class="tr-alert tr-copy-alert" id="copyAlert">Link copied</div>
      <div class="tr-alert tr-copy-alert" id="embedAlert">Embed code copied</div>
    </div>
  </div>
</div>
</body>
</html>
	
<script src="{{ asset('/js/header.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js')}} "></script>
<script src="{{ asset('/js/restaurant.js')}}"></script>
<script src="{{ asset('/js/custom.js')}}"></script>
<script src="{{ asset('/js/map_leaflet.js')}}"></script>
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>


<script>
    // Initialize map variables
    var mapInitialized = false;
    var map, defaultIcon, highlightedIcon, defaultIconRes, highlightedIconRes, experienceIcon, experienceHighlightedIcon;
    var markers = {}, restaurantMarkers = {}, experienceMarkers = {};
    var locations = [], restaurantLocations = [], experienceLocations = [];
    
    // Collect location data
    <?php foreach ($searchresults as $result): ?>
    <?php if (!empty($result->Latitude) && !empty($result->Longitude)): ?>
    locations.push([<?php echo $result->Latitude; ?>, <?php echo $result->Longitude; ?>]);
    <?php endif; ?>
    <?php endforeach; ?>

    @foreach($restaurantdata as $resta)
        restaurantLocations.push({
            lat: {{ $resta['Latitude'] }},
            long: {{ $resta['Longitude'] }},
            name: '{{ $resta['Title'] }}',
            city: '{{ $resta['locname'] }}',
            rating: '{{ $resta['Averagerating'] }}',
            id: '{{ $resta['RestaurantId'] }}',
            PriceRange: '{{ $resta['PriceRange'] }}',
            image: '{{ asset("/images/Hotel lobby-image.png") }}'
        });
    @endforeach
	
   @foreach($getexp as $experience)
    @if (!empty($experience['Latitude']) && !empty($experience['Longitude']))
        experienceLocations.push({
            lat: {{ $experience['Latitude'] }},
            long: {{ $experience['Longitude'] }},
            name: '{{ $experience['Name'] }}',
            city: '{{ $lname }}',  // Use $lname here
            id: '{{ $experience['ExperienceId'] }}',
            rating: '{{ $resta['Averagerating'] ?? "No Rating Available" }}',
            image: '{{ $experience['Img1'] ?? asset("/images/Hotel lobby.svg") }}'
        });
    @endif
@endforeach

    // Initialize map when document is ready
    $(document).ready(function() {
        // Lazy load map initialization
        const mapObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && !mapInitialized) {
                    initMap();
                    setupEventListeners();
                    mapInitialized = true;
                    mapObserver.disconnect();
                }
            });
        }, { threshold: 0.1 });
        
        // Observe the map container
        const mapContainer = document.getElementById('map1');
        if (mapContainer) {
            mapObserver.observe(mapContainer);
        }
        
        // Initialize immediately if map is already visible
        if (isElementInViewport(document.getElementById('map1'))) {
            initMap();
            setupEventListeners();
            mapInitialized = true;
        }
    });
    
    // Helper function to check if element is in viewport
    function isElementInViewport(el) {
        if (!el) return false;
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Initialize the map
    function initMap() {
        // Set default center and determine actual center
        var defaultCenter = [48.8566, 2.3522]; // Default location (Paris)
        var center = locations.length > 0 ? locations[0] : defaultCenter;
        var isMobile = window.innerWidth <= 768;

        // Create map with appropriate options
        var mapOptions = {
            center: center,
            zoom: isMobile ? 18 : 15 // Adjust zoom level for mobile and non-mobile devices
        };

        map = new L.map('map1', mapOptions);
        var layer = new L.TileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
            subdomains: 'abcd',
            maxZoom: 19
        });

        map.addLayer(layer);

        // Disable scroll zoom and dragging on the map to prevent map movement
        map.scrollWheelZoom.disable();
        map.dragging.disable();

        // Initialize marker icons
        initIcons();
        
        // Initialize all markers
        initMarkers();
        initRestaurantMarkers();
        initExperienceMarkers();
        
        // Update marker icons initially
        updateMarkerIcons();
    }
    
    // Initialize custom marker icons
    function initIcons() {
        // Cache icon URLs for better performance
        const iconUrls = {
            attraction: '{{asset('/js/images/3.svg')}}',
            attractionHighlighted: '{{asset('/js/images/4.svg')}}',
            restaurant: '{{asset('/js/images/1.svg')}}',
            restaurantHighlighted: '{{asset('/js/images/1h.svg')}}',
            experience: '{{asset('/js/images/2.svg')}}',
            experienceHighlighted: '{{asset('/js/images/2h.svg')}}'
        };
        
        defaultIcon = L.icon({
            iconUrl: iconUrls.attraction,
            iconSize: [32, 40]
        });

        highlightedIcon = L.icon({
            iconUrl: iconUrls.attractionHighlighted,
            iconSize: [34, 42]
        });

        defaultIconRes = L.icon({
            iconUrl: iconUrls.restaurant,
            iconSize: [32, 40]
        });

        highlightedIconRes = L.icon({
            iconUrl: iconUrls.restaurantHighlighted,
            iconSize: [34, 42]
        });
        
        experienceIcon = L.icon({
            iconUrl: iconUrls.experience,
            iconSize: [32, 40]
        });
        
        experienceHighlightedIcon = L.icon({
            iconUrl: iconUrls.experienceHighlighted,
            iconSize: [34, 42]
        });
    }
    
    // Set up global event listeners
    function setupEventListeners() {
        // Redirect scroll over the map to the hotel listing
        const mapContainer = document.querySelector('#map1');
        const hotelListingContainer = document.querySelector('.tr-explore-left-section');

        if (mapContainer && hotelListingContainer) {
            mapContainer.addEventListener('wheel', function(event) {
                event.preventDefault();
                hotelListingContainer.scrollBy({
                    top: event.deltaY,
                    behavior: 'auto'
                });
            });
        }
        
        // Map event handlers - use throttled versions for better performance
        map.on('zoomend', throttle(updateMarkerIcons, 100));
        map.on('moveend', throttle(updateMarkerIcons, 100));
        
        // Map button click handler
        $('.tr-explore-map-btn').click(function() {
            $(".tr-explore-listing .tr-map-section").css({
                "display": "block"
            });
            $("body").addClass('modal-open');

            setTimeout(function() {
                map.invalidateSize();
                adjustMapZoom();
            }, 100); // Adding a slight delay to ensure the map is fully visible before recalculating its size
        });
        
        // Window resize handler with throttling for better performance
        window.addEventListener('resize', throttle(adjustMapZoom, 250));
        
        // Initial update on window load
        window.addEventListener('load', updateMarkerIcons);
    }
    
    // Throttle function to limit the rate at which a function can fire
    function throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }

    // Initialize attraction markers
    function initMarkers() {
        <?php for ($i = 0; $i < count($searchresults); $i++): ?>
        <?php if (!empty($searchresults[$i]->Latitude) && !empty($searchresults[$i]->Longitude)): ?>
        var name<?php echo $i; ?> = '<?php echo addslashes($searchresults[$i]->Title); ?>';
        var isRecommend<?php echo $i; ?> = document.querySelector('.isrecomd_<?php echo $i; ?>') ? document.querySelector('.isrecomd_<?php echo $i; ?>').textContent : 'N/A';
        var cityName<?php echo $i; ?> = document.querySelector('.cityname_<?php echo $i; ?>') ? document.querySelector('.cityname_<?php echo $i; ?>').textContent.trim() : 'N/A';
        var timing<?php echo $i;?> = document.querySelector('.timing_<?php echo $i;?>') ? document.querySelector('.timing_<?php echo $i;?>').textContent : 'N/A';
        var category<?php echo $i; ?> = document.querySelector('.catname_<?php echo $i; ?>') ? document.querySelector('.catname_<?php echo $i; ?>').textContent.trim() : 'N/A';
        <?php
            $imagePath = asset('/images/Hotellobby-nmustsee-compressed.svg');
            foreach ($sightImages as $sightImage) {
                if ($sightImage->Sightid == $searchresults[$i]->SightId) {
                    $imagePath = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/{$sightImage->Image}&width=280&height=109";
                    break;
                }
            }
        ?>
        var imagePath<?php echo $i; ?> = '<?php echo $imagePath; ?>';
        
        // Create marker
        var marker<?php echo $i; ?> = new L.Marker([<?php echo $searchresults[$i]->Latitude; ?>, <?php echo $searchresults[$i]->Longitude; ?>], { icon: defaultIcon });
        marker<?php echo $i; ?>.addTo(map);
        
        // Set up marker data for later use
        marker<?php echo $i; ?>.markerData = {
            name: name<?php echo $i; ?>,
            isRecommend: isRecommend<?php echo $i; ?>,
            cityName: cityName<?php echo $i; ?>,
            timing: timing<?php echo $i; ?>,
            imagePath: imagePath<?php echo $i; ?>,
            category: category<?php echo $i; ?>
        };

        // Add event listeners
        marker<?php echo $i; ?>.on('click', function(e) {
            showPopup(e.target);
        });
        
        // Store marker reference
        markers[<?php echo $searchresults[$i]->SightId; ?>] = marker<?php echo $i; ?>;
        <?php endif; ?>
        <?php endfor; ?>
        
        // Add global map click handler to close popups
        map.on('click', function(event) {
            if (!event.originalEvent.target.closest('.leaflet-popup-content')) {
                Object.values(markers).forEach(marker => marker.closePopup());
            }
        });
    }
    
    // Initialize restaurant markers
    function initRestaurantMarkers() {
        restaurantLocations.forEach(function(location) {
            // Create marker
            var marker = L.marker([location.lat, location.long], { icon: defaultIconRes }).addTo(map);
            
            // Store data with marker
            marker.markerData = location;
            
            // Create popup content
            var popupContent = createRestaurantPopup(location);
            
            // Bind popup
            marker.bindPopup(popupContent, {
                offset: L.point(0, -20),
                autoPan: true
            });
            
            // Add event listeners
            marker.on('click', function(e) {
                marker.openPopup();
            });
            
            // Store marker reference
            restaurantMarkers[location.id] = marker;
        });
    }
    
    // Initialize experience markers
    function initExperienceMarkers() {
        experienceLocations.forEach(function(location) {
            // Create marker
            var marker = L.marker([location.lat, location.long], { icon: experienceIcon }).addTo(map);
            
            // Store data with marker
            marker.markerData = location;
            
            // Create popup content
            var popupContent = createExperiencePopup(location);
            
            // Bind popup
            marker.bindPopup(popupContent, {
                offset: L.point(0, -20),
                autoPan: true
            });
            
            // Add event listeners
            marker.on('click', function(e) {
                marker.openPopup();
            });
            
            // Store marker reference
            experienceMarkers[location.id] = marker;
        });
    }

    // Show popup for attraction marker
    function showPopup(marker) {
        var data = marker.markerData;
        showTestName({target: marker}, data.name, data.isRecommend, data.cityName, data.timing, data.imagePath, data.category);
    }

    // Function to show popup content
    function showTestName(e, name, isRecommend, cityName, timing, imagePath, category) {
        var marker = e.target;

        // Use default placeholders for missing data
        var popupContent = `
        <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important; right: 0; left: 0; margin: auto; font-size: 14px;">
            <div class="tr-historical-monument">
                <div class="tr-heading-with-distance">
                    <div class="tr-category" style="font-size: 12px;">${category || 'Attraction'}</div>
                </div>
                <div class="tr-image">
    <a href="javascript:void(0);">
        ${
            imagePath && !imagePath.includes('Hotellobby-nmustsee-compressed.svg') ?
            `<img loading="lazy" 
                  src="${imagePath}" 
                  alt="${name || 'Unnamed Attraction'}" 
                  height="109" 
                  width="280">` :
            `<img loading="lazy" 
                  src="{{ asset('/images/Hotel lobby.svg') }}" 
                  alt="${name || 'Unnamed Attraction'}"
                  height="109" 
                  width="280">`
        }
    </a>
</div>
                <div class="tr-details" style="font-size: 14px;">
                    <h3 style="font-size: 16px;">${name || 'Unnamed Attraction'}</h3>
                    <div class="tr-location" style="font-size: 12px;">${cityName || 'Unknown City'}</div>
                    <div class="tr-like-review">
                        <div class="tr-heart">
                            <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.36052 1.20745 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99604 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                            </svg>
                        </div>
                        <div class="tr-ranting-percent" style="font-size: 12px;">${isRecommend || ''}</div>
                    </div>
                </div>
                 <div class="tr-more-inform" style="font-size: 12px;">
                <ul>
                    <li><span>Open:</span> ${timing || ''}</li>
                </ul>
            </div>    
        </div>
        </div>`;

        marker.unbindPopup(); // Unbind existing popups to ensure no conflicts
        marker.bindPopup(popupContent, {
            offset: L.point(0, -20), // Adjust the popup offset for better positioning
            autoPan: true // Ensure the popup stays within the map bounds
        }).openPopup();
    }
    
    // Create restaurant popup content
    function createRestaurantPopup(location) {
        return `
            <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important; right: 0; left: 0; margin: auto; font-size: 14px;">
                <div class="tr-historical-monument">
                    <div class="tr-heading-with-distance">
                        <div class="tr-category" style="font-size: 12px;">Restaurant</div>
                    </div>
                    <div class="tr-image">
                        <a href="javascript:void(0);">
                            <img loading="lazy" src="${location.image || 'default-image.png'}" alt="${location.name || 'Image'}" height="109" width="280">
                        </a>
                    </div>
                    <div class="tr-details" style="font-size: 14px;">
                        <h3 style="font-size: 16px;">${location.name || 'Unnamed Location'}</h3>
                        <div class="tr-location" style="font-size: 12px;">${location.city || 'Unknown City'}</div>
                        <div class="tr-like-review">
                            <div class="tr-heart">
                                <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.36052 1.20745 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99604 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                            </div>
                            <div class="tr-ranting-percent" style="font-size: 12px;">
    ${location.rating ? location.rating + '%' : 'No Rating Available'}
</div>
                        </div>
                    </div>
                    <div class="tr-more-inform" style="font-size: 12px;">
                        <ul>
                            <li><span>Open:</span> ${location.PriceRange || 'No Information Available'}</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Create experience popup content
    function createExperiencePopup(location) {
        return `
    <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important; right: 0; left: 0; margin: auto; font-size: 14px;">
        <div class="tr-historical-monument">
            <div class="tr-heading-with-distance">
                <div class="tr-category" style="font-size: 12px;">Experience</div>
            </div>
            <div class="tr-image">
                <a href="javascript:void(0);">
                    <img loading="lazy" src="${location.image}" alt="${location.name}" height="109" width="280">
                </a>
            </div>
            <div class="tr-details" style="font-size: 14px;">
                <h3 style="font-size: 16px;">${location.name}</h3>
                <div class="tr-location" style="font-size: 12px;">
                    ${location.city || 'Unknown City'}
                </div>
                <div class="tr-like-review">
                    <div class="tr-heart">
                        <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.36052 1.20745 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99604 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </div>
                    <div class="tr-ranting-percent" style="font-size: 12px;">
    ${location.rating ? location.rating + '%' : 'No Rating Available'}
</div>
                </div>
            </div>
        </div>
    </div>
`;
    }

    // Marker highlight functions
    function highlightMarker(element) {
        var sid = element.getAttribute('data-sid');
        var marker = markers[sid];
        if (marker) {
            marker.setIcon(highlightedIcon);

            // Find the marker data using a more efficient approach
            var markerData = marker.markerData;
            if (markerData) {
                // Show the popup with the correct data
                showTestName({target: marker}, markerData.name, markerData.isRecommend, markerData.cityName, markerData.timing, markerData.imagePath, markerData.category);
            }

            marker.openPopup();
            map.panTo(marker.getLatLng());
        }
    }

    function unhighlightMarker(element) {
        var sid = element.getAttribute('data-sid');
        var marker = markers[sid];
        if (marker) {
            marker.setIcon(defaultIcon);
            marker.closePopup();
        }
        updateMarkerIcons();
    }

    function highlightRestaurantMarker(element) {
        var restaurantId = element.getAttribute('data-restaurant-id');
        var marker = restaurantMarkers[restaurantId];
        if (marker) {
            marker.setIcon(highlightedIconRes);
            marker.openPopup();
            map.panTo(marker.getLatLng());
        }
    }

    function unhighlightRestaurantMarker(element) {
        var restaurantId = element.getAttribute('data-restaurant-id');
        var marker = restaurantMarkers[restaurantId];
        if (marker) {
            marker.setIcon(defaultIconRes);
            marker.closePopup();
        }
    }

    function highlightExperienceMarker(element) {
        var experienceId = element.getAttribute('data-experience-id');
        var marker = experienceMarkers[experienceId];
        if (marker) {
            marker.setIcon(experienceHighlightedIcon);
            marker.openPopup();
            map.panTo(marker.getLatLng());
        }
    }

    function unhighlightExperienceMarker(element) {
        var experienceId = element.getAttribute('data-experience-id');
        var marker = experienceMarkers[experienceId];
        if (marker) {
            marker.setIcon(experienceIcon);
            marker.closePopup();
        }
    }

    function updateMarkerIcons() {
        var attractionElements = document.querySelectorAll('.attraction');
        attractionElements.forEach(function(element) {
            var sid = element.getAttribute('data-sid');
            var isMustSee = element.getAttribute('data-ismustsee');

            if (isMustSee === "1") {
                var marker = markers[sid];
                if (marker) {
                    marker.setIcon(defaultIcon);
                }
            }
        });
    }
</script>

<script>
// Handle the results loader
document.addEventListener('DOMContentLoaded', function() {
  // Get the results loader element
  const resultsLoader = document.getElementById('results-loader');
  const resultsContainer = document.getElementById('getcatfilterdata');
  
  // Check if we have search results
  const searchResults = document.querySelectorAll('.tr-museum, .tr-list');
  
  // If we have results, hide the loader after a delay
  if (searchResults.length > 0) {
    // Show the loader for a minimum time to ensure a smooth experience
    setTimeout(function() {
      if (resultsContainer) {
        resultsContainer.classList.add('results-loaded');
      }
    }, 1500); // Show loader for 1.5 seconds minimum
  } else {
    // If no results, still hide the loader
    if (resultsContainer) {
      resultsContainer.classList.add('results-loaded');
    }
  }
  
  // Load more button functionality
  const loadMoreBtn = document.querySelector('.tr-load-more');
  
  if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function() {
      // Show loading indicator
      if (resultsContainer) {
        resultsContainer.classList.remove('results-loaded');
      }
      
      // Your AJAX call to load more content would go here
      // After content is loaded, the button should be moved to the bottom
      
      // For demonstration, let's simulate loading more content
      setTimeout(function() {
        // Hide loading indicator
        if (resultsContainer) {
          resultsContainer.classList.add('results-loaded');
        }
        
        // Move the load more button container to the end of the content
        const loadingContainer = document.querySelector('.tr-loading-container');
        if (loadingContainer) {
          const parentElement = loadingContainer.parentElement;
          parentElement.appendChild(loadingContainer);
        }
      }, 1000);
    });
  }
});
</script>

<script>
    function hideCarouselControls(id) {
        document.getElementById('carousel-controls-' + id).style.display = 'none';
    }

    function showCarouselControls(id) {
        document.getElementById('carousel-controls-' + id).style.display = 'block';
    }
    
    // Add event listeners for mobile touch events
    document.addEventListener('DOMContentLoaded', function() {
        const sliders = document.querySelectorAll('.carousel');
        sliders.forEach(slider => {
            const sliderId = slider.id;
            if (sliderId.startsWith('Slider')) {
                const id = sliderId.replace('Slider', '');
                const controls = document.getElementById('carousel-controls-' + id);
                
                if (controls) {
                    // For mobile: show controls when touching the slider
                    slider.addEventListener('touchstart', function() {
                        showCarouselControls(id);
                    });
                }
            }
        });
    });
function toggleMute(button, event) {
    event.preventDefault();
    event.stopPropagation();
    
    const video = button.parentElement.querySelector('video');
    if(video.muted) {
        video.muted = false;
        button.innerHTML = '<i class="fa fa-volume-up"></i>';
    } else {
        video.muted = true;
        button.innerHTML = '<i class="fa fa-volume-off"></i>';
    }
}
</script>
