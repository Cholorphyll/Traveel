<!DOCTYPE html>
<html lang="en-US">
<head>

    <meta name="robots" content="index, follow">

 <link rel="canonical" href="{{ url()->current() }}" />

  <!-- Google Tag Manager -->
 <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3G0VLZYF7R"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3G0VLZYF7R');
</script>
  <!-- End Google Tag Manager -->

<title id="page-title">
  @if(!empty($searchresult) && !empty($searchresult[0]->metaTagTitle))
    {{ $searchresult[0]->metaTagTitle . ' -Travell (2025)' }}
  @elseif (!empty($searchresult))
    {{ e($searchresult[0]->name) }} in {{ e($searchresult[0]->cityName) }} - Compare Prices & Get the Best Deal
  @else
    Static Title Here - Travell
  @endif
</title>


<meta name="description" id="meta-description" content="@if(!empty($searchresult) && !empty($searchresult[0]->MetaTagDescription)){{$searchresult[0]->MetaTagDescription}}@elseif(!empty($searchresult))Discover {{$searchresult[0]->name}} in {{$searchresult[0]->cityName}}, featuring luxury rooms, central location, amenities. Compare prices across multiple booking platforms to find the best deal for your stay. Book smarter and save on your next trip.@else Discover great hotels with the best deals and amenities for your perfect stay.@endif">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">

 <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
    <!-- nav css -->
    <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
    <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <!-- new css -->

    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}">
  </script>

  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">
  <!-- start custom css -->
  <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
  <!-- end custom css -->
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/font_awesome.css')}}">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
  <!-- new css -->
<link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
	<script src="{{ asset('js/cookie-consent.js') }}"></script>


	  <!-- COLOUR CSS FOR MAP -->


	 <style>
        #map1 {
            width: 100%;
            height: 500px;
            position: relative;
        }

        /* Styling for white streets, green parks, and blue rivers overlay */
        .street-overlay {
            color: white;
            weight: 2;
            opacity: 1;
        }

        .park-overlay {
            color: green;
            weight: 2;
            fillColor: green;
            fillOpacity: 0.6;
        }

        .river-overlay {
            color: blue;
            weight: 2;
            opacity: 1;
        }
.tr-things-know .Icon {
    width: 1em;  /* Set width relative to text size */
    height: 1em; /* Set height relative to text size */
}
        #grey-overlay {
            filter: grayscale(100%);
        }
		 
		 .tr-overview-section .tr-overview-content button.tr-anchor-btn {
    margin-top: 15px;
}
		 @media (max-width: 768px) {
#review-not-foundimg {
    align-items: center; /* Center horizontally */
    justify-content: center; /* Center vertically */
    margin: 0 auto; /* Center horizontally within parent */
  }
		 }
button.tr-anchor-btn {
    font-weight: 600;
    font-size: 16px;
    line-height: 19px;
    letter-spacing: 0.01em;
    color: #FF4B01;
    border: none;
    border-bottom: 1px solid #FF4B01;
    background: transparent;
    cursor: pointer;
}
 .first-section, .second-section {
        display: flex;
        flex-wrap: nowrap;
        list-style-type: none;
        padding: 0;
        margin: 0;
    }
    .clear-break {
        width: 100%;
        height: 1px;
        clear: both;
        margin: 15px 0;
    }

/* Hide the button by default */
.tr-show-all {
    display: none;
}
#highlight-review-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}
/* Show the button only on mobile screens */
@media (max-width: 768px) {
    .tr-show-all {
        display: block;
    }
}
    </style>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Hotel",
      "@id": "{{ url('hd-' . $searchresult[0]->slugid . '-' . $searchresult[0]->id . '-' . strtolower(str_replace(' ', '_', str_replace('#', '!', $searchresult[0]->slug)))) }}",
      "name": "{{ $searchresult[0]->name }}",
      "url": "{{ url('hd-' . $searchresult[0]->slugid . '-' . $searchresult[0]->id . '-' . strtolower(str_replace(' ', '_', str_replace('#', '!', $searchresult[0]->slug)))) }}",
      @if(!empty($searchresult[0]->hotelid))
      <?php $hoteid = $searchresult[0]->hotelid; ?>
      "image": [
        @if(isset($images[$hoteid][0]) && $images[$hoteid][0] != "")
          "https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/628/567.auto"
        @endif
        @if(isset($images[$hoteid][1]) && $images[$hoteid][1] != "")
          ,"https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][1] }}/628/567.auto"
        @endif
        @if(isset($images[$hoteid][2]) && $images[$hoteid][2] != "")
          ,"https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][2] }}/628/567.auto"
        @endif
        @if(isset($images[$hoteid][3]) && $images[$hoteid][3] != "")
          ,"https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][3] }}/628/567.auto"
        @endif
        @if(isset($images[$hoteid][4]) && $images[$hoteid][4] != "")
          ,"https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][4] }}/628/567.auto"
        @endif
      ],
      @endif
      "address": {
        "@type": "PostalAddress",
        "streetAddress": "{{ $searchresult[0]->address }}",
        @if(!$getlocationexp->isEmpty())
        "addressLocality": "{{ $getlocationexp[0]->Name }}",
        @endif
        @if(!$getcontlink->isEmpty())
        "addressCountry": "{{ $getcontlink[0]->Name }}"
        @endif
      },
      "additionalProperty": [
        {
          "@type": "PropertyValue",
          "name": "Full Address",
          "value": "{{ $searchresult[0]->address }}"
        }
      ],
      @if(!empty($searchresult[0]->Email))
      "email": "{{ $searchresult[0]->Email }}",
      @endif
      @if(!empty($searchresult[0]->Phone))
      "telephone": "+{{ $searchresult[0]->Phone }}",
      @endif
      @if(!empty($searchresult[0]->Website))
      "sameAs": "{{ $searchresult[0]->Website }}",
      @endif
      @if(isset($searchresult[0]->amenities) && !empty($searchresult[0]->amenities))
      <?php $amenities = json_decode($searchresult[0]->amenities, true); ?>
      @if(is_array($amenities) && count($amenities) > 0)
      "amenityFeature": [
        @foreach(array_slice($amenities, 0, 5) as $index => $amenity)
        {
          "@type": "LocationFeatureSpecification",
          "name": "{{ $amenity }}",
          "value": true
        }@if($index < min(4, count($amenities)-1)),@endif
        @endforeach
      ],
      @endif
      @endif
      @if(!empty($searchresult[0]->checkin_time))
      "checkinTime": "{{ $searchresult[0]->checkin_time }}",
      @endif
      @if(!empty($searchresult[0]->checkout_time))
      "checkoutTime": "{{ $searchresult[0]->checkout_time }}",
      @endif
      @if(isset($searchresult[0]->pets_allowed))
      "petsAllowed": {{ $searchresult[0]->pets_allowed ? 'true' : 'false' }},
      @endif
      @if(!empty($searchresult[0]->stars))
      "starRating": {
        "@type": "Rating",
        "ratingValue": "{{ $searchresult[0]->stars }}"
      }@if(!empty($searchresult[0]->latitude) || !empty($searchresult[0]->longitude) || (!$review->isEmpty() && !empty($searchresult[0]->rating))),@endif
      @endif
      @if(!empty($searchresult[0]->latitude) && !empty($searchresult[0]->longitude))
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": {{ $searchresult[0]->latitude }},
        "longitude": {{ $searchresult[0]->longitude }}
      }@if(!$review->isEmpty() && !empty($searchresult[0]->rating)),@endif
      @endif
      @if(!$review->isEmpty() && !empty($searchresult[0]->rating))
      "aggregateRating": {
        "@type": "AggregateRating",
        "ratingValue": "{{ $searchresult[0]->rating }}",
        "bestRating": "100",
        "worstRating": "1",
        "ratingCount": {{ !$review->isEmpty() ? $review->count() : "0" }},
        "reviewCount": {{ !$review->isEmpty() ? $review->count() : "0" }}
      }@if(!$review->isEmpty()),@endif
      @endif
      @if(!$review->isEmpty())
      "review": [
        @foreach($review as $key => $reviews)
        {
          "@type": "Review",
          "author": {
            "@type": "Person",
            "name": "{{ $reviews->Name }}"
          },
          "reviewBody": "{{ $reviews->Description }}"
        }@if(!$loop->last),@endif
        @endforeach
      ]
      @endif
    },
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Travell",
          "item": "https://www.travell.co"
        },
        @if(!$getcontlink->isEmpty())
        {
          "@type": "ListItem",
          "position": 2,
          "name": "{{ $getcontlink[0]->cName }}",
          "item": "{{ route('explore_continent_list',[$getcontlink[0]->contid,$getcontlink[0]->cName]) }}"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "{{ $getcontlink[0]->Name }}",
          "item": "{{ route('explore_country_list',[$getcontlink[0]->CountryId,$getcontlink[0]->slug]) }}"
        },
        @endif
        @if(!empty($locationPatent))
        <?php 
        $locationPatents = array_reverse($locationPatent);
        $position = !$getcontlink->isEmpty() ? 4 : 2;
        ?>
        @foreach($locationPatents as $location)
        {
          "@type": "ListItem",
          "position": {{ $position++ }},
          "name": "{{ $location['Name'] }}",
          "item": "{{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}"
        },
        @endforeach
        @endif
        @if(!$getlocationexp->isEmpty())
        {
          "@type": "ListItem",
          "position": {{ !empty($locationPatent) ? count($locationPatent) + (!$getcontlink->isEmpty() ? 4 : 2) : (!$getcontlink->isEmpty() ? 4 : 2) }},
          "name": "{{ $getlocationexp[0]->Name }}",
          "item": "{{ route('search.results', [$getlocationexp[0]->slugid.'-'.strtolower($getlocationexp[0]->Slug)]) }}"
        },
        <?php
        $currentUrl = url()->current();
        preg_match("/(\d+)-/", $currentUrl, $matches);
        $firstId = isset($matches[1]) ? $matches[1] : '';
        ?>
        {
          "@type": "ListItem",
          "position": {{ !empty($locationPatent) ? count($locationPatent) + (!$getcontlink->isEmpty() ? 5 : 3) : (!$getcontlink->isEmpty() ? 5 : 3) }},
          "name": "{{ $getlocationexp[0]->Name }} Hotels",
          "item": "{{ route('hotel.list',[$firstId.'-'.$getlocationexp[0]->Slug]) }}"
        },
        @endif
        {
          "@type": "ListItem",
          "position": {{ !empty($locationPatent) ? count($locationPatent) + (!$getcontlink->isEmpty() ? 6 : 4) : (!$getcontlink->isEmpty() ? 6 : 4) }},
          "name": "{{ $searchresult[0]->name }}",
          "item": "{{ url()->current() }}"
        }
      ]
    },
    @php
    // Get hotel questions directly in the view for JSON-LD
    $hotelQuestions = DB::table('HotelQuestion')
        ->select('User_Name','CreatedDate','Question','updatedOn','Listing','Answer','faq_image')
        ->where('HotelId', $searchresult[0]->id)
        ->get();
    @endphp
    @if(isset($hotelQuestions) && is_countable($hotelQuestions) && count($hotelQuestions) > 0)
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($hotelQuestions as $index => $faq)
        {
          "@type": "Question",
          "name": "{{ $faq->Question }}",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "{{ $faq->Answer }}"
          }
        }@if($index < count($hotelQuestions) - 1),@endif
        @endforeach
      ]
    },
    @endif
    {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}#webpage",
      "name": "{{ $searchresult[0]->name }} | Travell",
      "url": "{{ url()->current() }}",
      "inLanguage": "en",
      "isPartOf": {
        "@type": "WebSite",
        "@id": "https://www.travell.co/#website"
      },
      "breadcrumb": {
        "@id": "{{ url()->current() }}#breadcrumb"
      }
    },
    {
      "@type": "WebSite",
      "@id": "https://www.travell.co/#website",
      "name": "Travell",
      "url": "https://www.travell.co"
    },
    {
      "@type": "Organization",
      "@id": "https://www.travell.co/#organization",
      "name": "Travell",
      "url": "https://www.travell.co",
      "logo": "https://www.travell.co/images/logo.png"
    }
  ]
}
</script>
</head>

<body>
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!--HEADER-->
  @include('frontend.header')

  <!--TAB1 - HOTEL DETAILS - START-->
  <div class="tr-hotel-info-tabs" id="hotelInfoTabs1">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <ul>
            <li><a href="#overviewTab" class="active">Overview</a></li>
            <li><a href="#roomTab">Room</a></li>
            <li><a href="#amenitiesTab">Amenities</a></li>
            <li><a href="#reviewTab">Review</a></li>
            <li><a href="#locationTab">Location</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <!--TAB1 - HOTEL DETAILS - END-->

  <!-- Refresh Banner -->
  @php
  // Convert current time to milliseconds since epoch (for JavaScript)
  $currentTimeMillis = $currentTime->timestamp * 1000;
  @endphp
  <div id="refresh-banner" class="tr-refresh-banner d-none">
    <p>Unfortunately, the search results are no longer valid.</p>
    <button class="tr-refresh-btn"><a href="">Refresh now</a></button>
  </div>

  <!-- Mobile Navigation-->
  @include('frontend.mobile_nav')

  <!--Hotel Destails - Like Name, rating, address, gallries and etc...-->
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="tr-hotel-informations">
          <div class="tr-hotel-info">
            <h1 class="tr-hotel-name">{{$searchresult[0]->name}}</h1>
            <div class="tr-hotel-address">{{$searchresult[0]->address}}</div>
            <div class="tr-raiting">
              @if($searchresult[0]->rating !="" && $searchresult[0]->rating !=0)
              <?php
                  $rating = (float)$searchresult[0]->rating;
                  $result = round($rating * 10);
                  if ($result > 95) {
                      $ratingtext = 'Superb';
                      $color = 'green';
                  } elseif ($result >= 91 && $result <= 95) {
                      $ratingtext = 'Excellent';
                      $color = 'green';
                  } elseif ($result >= 81 && $result <= 90) {
                      $ratingtext = 'Great';
                      $color = 'green';
                  } elseif ($result >= 71 && $result <= 80) {
                      $ratingtext = 'Good';
                      $color = '#FFE135';
                  } elseif ($result >= 61 && $result <= 70) {
                      $ratingtext = 'Okay';
                      $color = '#FFE135';
                  } elseif ($result >= 51 && $result <= 60) {
                      $ratingtext = 'Average';
                      $color = '#FFE135';
                  } elseif ($result >= 41 && $result <= 50) {
                      $ratingtext = 'Poor';
                      $color = 'red';
                  } elseif ($result >= 21 && $result <= 40) {
                      $ratingtext = 'Disappointing';
                      $color = 'red';
                  } else {
                      $ratingtext = 'Bad';
                      $color = 'red';
                  }


                ?>
              <a href="javascript:void(0);" class="tr-excellent" style="color:{{$color}}">{{$result}}% &nbsp;{{$ratingtext}}</a>
              @endif
              <a href="javascript:void(0);" class="tr-share" data-bs-toggle="modal"
                data-bs-target="#shareModal">Share</a>
              <a href="javascript:void(0);" class="tr-save">Save</a>
            </div>
          </div>
          <!--Gallery-->
          <div class="tr-hotel-galleries responsive-container" >
            <div class="tr-desktop d-none d-sm-block d-md-block"  id="hotel-galary-image" >
             @if(!empty($searchresult[0]->hotelid))
            <?php $hoteid = $searchresult[0]->hotelid; ?>


              <div class="tr-main-image">
                @if(isset($images[$hoteid][0] ) && $images[$hoteid][0] !="")
                <img
                  src="https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/628/567.auto" alt="Room Image" style="height: 440px;width: 440px;">
                @else
                <img src="{{asset('/images/Hotel lobby-image.png')}}" alt="" style="height: 440px;width: 440px;">
                @endif
              </div>
              <div class="tr-thumb-images">
                <ul>
                  @php
                  $remainingImages = isset($images[$hoteid]) ? array_slice($images[$hoteid], 1) : [];
                  @endphp

                  @for ($i = 0; $i < 6; $i++)
                   @if ($i < count($remainingImages)) @php $image=$remainingImages[$i];
                    @endphp <li>
                    @if(!empty($image))
                    <img src="https://photo.hotellook.com/image_v2/limit/{{ $image }}/312/234.auto" alt="" height="216" width="259" >
                    @else
                    <img src="{{asset('/images/Hotel lobby-image.png')}}" alt="" height="216" width="259" >
                    @endif
                    </li>

                    @else
                    <li>
                      <img  src="{{asset('/images/Hotel lobby-image.png')}}" alt="Room Image" height="216" width="259">
                    </li>
                    @endif
                    @endfor
                </ul>
                <a href="javascript:void(0);" class="tr-show-all-photos">Show all photos</a>
              </div>

              @endif
            </div>
            <div class="tr-mobile-galleries d-block d-sm-none d-md-none responsive-container">
              <div id="demo" class="carousel slide" data-bs-ride="carousel" data-interval="false">
                <!-- Indicators/dots -->
                <div class="carousel-indicators">
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="0" class="active"></button>
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="1"></button>
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="2"></button>
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="3"></button>
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="4"></button>
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="5"></button>
                  <button type="button" data-bs-target="#demo" data-bs-slide-to="6"></button>
                </div>
                <!-- The slideshow/carousel -->
                <div class="carousel-inner">

                  @if(!empty($searchresult[0]->hotelid))
                <?php $hoteid = $searchresult[0]->hotelid; ?>

                  <div class="carousel-item active">
                  @if(isset($images[$hoteid][0] ) && $images[$hoteid][0] !="")
                  <img
                    src="https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/412/344.auto" alt="Room Image"  height="344" width="412">
                  @else
                      <img  src="{{ asset('/images/Hotel lobby-image.png') }}" alt="" class="d-block w-100" height="344" width="412">
                  @endif
                  </div>
                  @php
                  $remainingImages = isset($images[$hoteid]) ? array_slice($images[$hoteid], 1) : [];
                  @endphp

                  @for ($i = 0; $i < 6; $i++)
                  @if ($i < count($remainingImages)) @php $image=$remainingImages[$i];
                    @endphp
                    @if(!empty($image))
                  <div class="carousel-item">
                    <img  src="https://photo.hotellook.com/image_v2/limit/{{ $image }}/412/344.auto" alt="" class="d-block w-100" height="344" width="412">
                  </div>
                  @else
                  <div class="carousel-item">
                    <img  src="{{ asset('/images/Hotel lobby-image.png') }}" alt="" class="d-block w-100" height="344" width="412">
                  </div>
                    @endif
                  @else
                  <div class="carousel-item">
                    <img  src="{{ asset('/images/Hotel lobby-image.png') }}" alt="" class="d-block w-100" height="344" width="412">
                  </div>

                  @endif
                 @endfor

                @endif
                </div>
                <!-- The thumbs/carousel -->
                <div class="carousel-thumbs">
                  <!-- <div class="carousel-thumb">
                    <img  src="images/room-image-2.png" alt="">
                  </div>
                  <div class="carousel-thumb">
                    <img  src="images/room-image-3.png" alt="">
                  </div>
                  <div class="carousel-thumb">
                    <img  src="images/room-image-4.png" alt="">
                  </div> -->
                  <div class="tr-thumbs-count tr-show-all-photos">Show all 12+</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="container">
    <div class="row">
      <div class="col-lg-7 col-md-12 tr-left-col">
        <div class="tr-content-section">
          <div class="tr-hotel-deatils">
  <div class="tr-hotel-certificated">
    <div class="row" style="display: flex;">
      <!-- Families Favourite Section -->
      <div class="tr-families-favourite col-md-6" style="flex: 1;">
        <img src="{{ asset('/frontend/hotel-detail/images/leaf.png') }}" alt="leaf">
        <span>Families Favourite</span>
        <p>The hotel is praised for its great location near the city center and attractions in London City. Guests
          appreciate the convenient and easy access to nearby exclusive all-suite hotels providing 4-star
          facilities...</p>
      </div>

      <!-- Spotlight and Business Section -->
      <div class="col-md-6" style="flex: 1; display: flex; flex-direction: column;">
        <!-- Spotlight Section -->
        @if (!empty($searchresult[0]->Spotlights))
        <div class="tr-spotlight" style="flex: 1; margin-bottom: 10px;">
          <div class="tr-heading">
            <svg width="24" height="7" viewBox="0 0 24 7" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M23.3868 3.5L20.5 0.613249L17.6132 3.5L20.5 6.38675L23.3868 3.5ZM0.5 4H20.5V3H0.5V4Z"
                fill="#09707A" />
            </svg>
            IN THE SPOTLIGHT
            <svg width="24" height="7" viewBox="0 0 24 7" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path d="M0.613249 3.5L3.5 0.613249L6.38675 3.5L3.5 6.38675L0.613249 3.5ZM23.5 4H3.5V3H23.5V4Z"
                fill="#09707A" />
            </svg>
          </div>
          <div>{{ $searchresult[0]->Spotlights }}</div>
        </div>
        @endif

        <!-- Great For Section -->
        @if($searchresult[0]->GreatFor != "" || $searchresult[0]->GreatForScore != "" && $searchresult[0]->GreatForScore != 0.0)
        <div class="tr-business-rated" style="flex: 1;">
          <div class="tr-title">Great for</div>
          <div class="tr-business">
            @if($searchresult[0]->GreatFor != ""){{ $searchresult[0]->GreatFor }} @endif
          </div>
          <div class="@if($searchresult[0]->GreatFor != "") tr-rated @else tr-business @endif">
            @if($searchresult[0]->GreatForScore != "" && $searchresult[0]->GreatForScore != 0.0)
            Rated {{ $searchresult[0]->GreatForScore }}
            @endif
          </div>
          <img src="{{ asset('/frontend/hotel-detail/images/icons/marketeq-icon.svg') }}" alt="">
        </div>
        @endif
      </div>
    </div>
  </div>
</div>

            <!--TAB - HOTEL DETAILS - START-->
            <div class="tr-hotel-info-tabs d-none d-md-block d-lg-blobk" id="hotelInfoTabs">
              <ul>
                <li><a href="#overviewTab" class="active">Overview</a></li>
                <li><a href="#roomTab">Room</a></li>
                <li><a href="#amenitiesTab">Amenities</a></li>
                <li><a href="#reviewTab">Review</a></li>
                <li><a href="#locationTab">Location</a></li>
              </ul>
            </div>
            <!--TAB - HOTEL DETAILS - END-->
            <span class="d-none" id="hotelid">{{$searchresult[0]->hotelid}}</span>
            <span class="d-none" id="locationid">{{$searchresult[0]->location_id}}</span>
            <span class="d-none" id="cityName">{{$searchresult[0]->cityName}}</span>

            <span class="d-none" id="Latitude">{{$searchresult[0]->Latitude}}</span>
            <span class="d-none" id="longnitude">{{$searchresult[0]->longnitude}}</span>


            <span class="d-none" id="stars">{{$searchresult[0]->stars}}</span>
            <span class="d-none" id="propertyTypeId">{{$searchresult[0]->propertyTypeId}}</span>
            <span class="d-none" id="hid">{{$searchresult[0]->id}}</span>
            <span class="d-none" id="hname">{{$searchresult[0]->name}}</span>
            <span class="d-none" id="tid">{{$tid}}</span>
            <span class="d-none" id="location_slugid">{{$location_slugid}}</span>
            <span class="d-none" id="photoCount">{{$searchresult[0]->photoCount}}</span>
			<span class="d-none" id="pagetype">{{$pgtype}}</span>
            <!--OVERVIEW SECTION - START-->
            
            
            <div class="tr-overview-section tr-tab-section" id="overviewTab">

              <div class="tr-overview-details">
                <ul>
            @if (!empty($searchresult[0]->rating))
                  <li>
                      
               <div class="tr-rating">
    {{ $searchresult[0]->rating !== null ? $searchresult[0]->rating : 'N/A' }}
</div>

<div class="tr-rating-type">
      @if ($searchresult[0]->rating !== null && $searchresult[0]->rating !== '')
        @if ($searchresult[0]->rating > 9)
            Superb
        @elseif ($searchresult[0]->rating >= 8)
            Excellent
        @elseif ($searchresult[0]->rating >= 7)
            Great
        @elseif ($searchresult[0]->rating >= 6)
            Good
        @elseif ($searchresult[0]->rating >= 5)
            Okay
        @elseif ($searchresult[0]->rating >= 4)
            Average
        @else
            Bad
        @endif
    @else
        No Rating
    @endif
</div>

<div class="tr-review">
    {{ $searchresult[0]->ratingcount !== null ? $searchresult[0]->ratingcount . ' reviews' : '0 reviews' }}
</div>
               
                  </li>
 @endif

@php
    // Remove empty highlights and ensure we have values to display
    $filteredHighlights = array_filter($highlightWords, function($value) {
        return !empty($value);
    });
@endphp
@if (!empty($filteredHighlights))
    <div class="highlights-container">
    @php
            function getHighlightIcon($highlight) {
                $highlight = strtolower($highlight);
                $iconPath = '/frontend/hotel-detail/images/amenities/';
                
                $iconMappings = [
                    'breakfast' => 'breakfast-icon.svg',
                    'station' => 'airport-shuttle.svg',
                    'transport' => 'airport-shuttle.svg',
                    'airport' => 'airport-shuttle.svg',
                    'wifi' => 'wifi-icon.svg',
                    'internet' => 'wifi-icon.svg',
                    'restaurant' => 'Restaurant-icon.svg',
                    'dining' => 'Restaurant-icon.svg',
                    'food' => 'Food.svg',
                    'pool' => 'Swimming-pool-icon.svg',
                    'swimming' => 'Swimming-pool-icon.svg',
                    'spa' => 'spa-icon.svg',
                    'massage' => 'spa-icon.svg',
                    'gym' => 'Gym.svg',
                    'fitness' => 'Fitness.svg',
                    'garden' => 'garden-icon.svg',
                    'terrace' => 'Terrace-icon.svg',
                    'beach' => 'Beach Access-icon.svg',
                    'tennis' => 'Tenis-court.svg',
                    'laundry' => 'Laundary-icon.svg',
                    'cleaning' => 'daily-housekeeping.svg',
                    'parking' => 'Parking.svg',
                    'room service' => 'Room Service.svg',
                    'housekeeping' => 'Daily Housekeeping.svg'
                ];
                
                foreach ($iconMappings as $keyword => $icon) {
                    if (strpos($highlight, $keyword) !== false) {
                        return $icon;
                    }
                }
                
                return 'amenity-default.svg';
            }
        @endphp
        <ul class="first-section">
            @foreach (array_slice($filteredHighlights, 0, 4) as $highlight)
                <li style="background-color: white;">
                    <img src="{{ asset('/frontend/hotel-detail/images/amenities/' . getHighlightIcon($highlight)) }}" alt="{{ $highlight }}" style="width: 24px; height: 24px;">
                    <div class="tr-sub-title">{{ $highlight }}</div>
                </li>
            @endforeach
        </ul>
        
        @if (count($filteredHighlights) > 4)
            <div class="clear-break"></div> <!-- Clear line break -->
            <ul class="second-section">
                @foreach (array_slice($filteredHighlights, 4, 4) as $highlight)
                    <li style="background-color: white;">
                        <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M6.96777 19.7314L4.96777 21.7314M18.9678 19.7314L20.9678 21.7314M9.96777 15.7314H9.97677M15.9588 15.7314H15.9678M5.96777 9.73145C9.96777 13.7314 16.4678 13.7314 19.9678 9.73145" stroke="#222222" stroke-width="1.44" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6.24068 8.62544C7.06168 4.44745 8.19768 3.73145 12.4227 3.73145H13.5127C17.7377 3.73145 18.8727 4.44745 19.6947 8.62544L20.2477 11.4414C21.0027 15.2814 21.3797 17.2014 20.2797 18.4664C19.1797 19.7314 17.1097 19.7314 12.9677 19.7314C8.82668 19.7314 6.75568 19.7314 5.65568 18.4664C4.55568 17.2014 4.93268 15.2814 5.68768 11.4414L6.24068 8.62544Z" stroke="#222222" stroke-width="1.44" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <div class="tr-sub-title">{{ $highlight }}</div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

@endif
              </div>


@if (!empty($searchresult[0]->about) || !empty($searchresult[0]->OverviewShortDesc))
    <div class="tr-things-know">
        <h3>About</h3>
        <ul class="thingsToKnowContent1" style="text-align: justify; list-style-position: inside; max-height: 100px; overflow: hidden;">
            @php
                // Get the 'about' and 'OverviewShortDesc' content
                $aboutContent = $searchresult[0]->about;
                $overviewContent = $searchresult[0]->OverviewShortDesc;
 				$aboutContent = str_replace(['[', ']', "''", "'"], '', $aboutContent);
                $overviewContent = str_replace(['[', ']', "''", "'"], '', $overviewContent);
            @endphp

            {{-- Handle 'about' content if it's a string --}}
            @if (is_string($aboutContent))
                {{-- Split content by commas to create multiple bullet points --}}
                @php
                    $aboutArray = explode(',', $aboutContent); // Split by comma
                @endphp
                @foreach ($aboutArray as $item)
                    @if (!empty(trim($item))) <!-- Avoid displaying empty items -->
                        <li>{{ trim($item) }}.</li>
                    @endif
                @endforeach
            @endif

            {{-- Handle 'OverviewShortDesc' content if it's a string --}}
            @if (is_string($overviewContent))
                {{-- Split content by commas to create multiple bullet points --}}
                @php
                    $overviewArray = explode(',', $overviewContent); // Split by comma
                @endphp
                @foreach ($overviewArray as $item)
                    @if (!empty(trim($item))) <!-- Avoid displaying empty items -->
                        <li ><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon" style="margin-right: 10px;">{{ trim($item) }}.</li>
                    @endif
                @endforeach
            @endif
        </ul>
        <button id="toggleButton1" onclick="toggleReadMore1()" class="tr-anchor-btn" style="margin-top: -15px;">
            Read More
        </button>
    </div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const button = document.getElementById("toggleButton1"); // Using the id directly

    button.addEventListener("click", function() {
        const content = this.previousElementSibling; // Select the <ul> content above the button

        if (content.style.maxHeight === "100px") {
            content.style.maxHeight = "none";
            this.textContent = "Read Less";
        } else {
            content.style.maxHeight = "100px";
            this.textContent = "Read More";
        }
    });
});
</script>

@endif



@if(!empty($searchresult[0]->knownfor))
<div class="tr-overview-items" style="margin-top: 40px;">
    <div class="tr-overview-items-icon">
        <img src="images/icons/overview-star-icon.svg" alt="">
    </div>
    <div>
        <h4>Known For</h4>
        <p>{{ $searchresult[0]->knownfor }}</p>
    </div>
</div>
@endif

@if(!empty($searchresult[0]->AmenitiesRest))
<div class="tr-overview-items">
    <div class="tr-overview-items-icon">
        <img src="images/icons/overview-beverages-icon.svg" alt="">
    </div>
    <div>
        <h4>Amenities</h4>
        <div class="tr-amenities-lists">
            @foreach(explode(',', $searchresult[0]->AmenitiesRest) as $amenity)
                <span>{{ trim($amenity) }}</span>
            @endforeach
        </div>
    </div>
</div>
@endif

@if(!empty($searchresult[0]->PlaceKnownFor))
<div class="tr-overview-items">
    <div class="tr-overview-items-icon">
        <img src="images/icons/overview-compass-icon.svg" alt="">
    </div>
    <div>
        <h4>People Say This Place Is Known For</h4>
        <p>{{ $searchresult[0]->PlaceKnownFor }}</p>
    </div>
</div>
@endif

@if(!empty($searchresult[0]->PriceRange))
<div class="tr-overview-items">
    <div class="tr-overview-items-icon">
        <img src="images/icons/overview-dollar-icon.svg" alt="">
    </div>
    <div>
        <h4>Typical Price Range</h4>
        @foreach(explode(',', $searchresult[0]->PriceRange) as $price)
            @if(!empty(trim($price)))
                <p>{{ trim($price) }}</p>
            @endif
        @endforeach
        <div class="tr-price-desc">Exclusive of applicable taxes and charges, if any</div>
        <div class="tr-price-short-desc">How do we calculate cost for two?</div>
    </div>
</div>
@endif

@if(!empty($searchresult[0]->PopularRoomTypes))
<div class="tr-overview-items">
    <div class="tr-overview-items-icon">
        <img src="images/icons/overview-dollar-icon.svg" alt="">
    </div>
    <div>
        <h4>Popular Room Types</h4>
        @foreach(explode(',', $searchresult[0]->PopularRoomTypes) as $roomType)
            @if(!empty(trim($roomType)))
                <p>{{ trim($roomType) }}</p>
            @endif
        @endforeach
        <div class="tr-price-desc">Exclusive of applicable taxes and charges, if any</div>
        <div class="tr-price-short-desc">How do we calculate cost for two?</div>
    </div>
</div>
@endif

@if(!empty($searchresult[0]->Moreinfo))
<div class="tr-overview-items">
    <div class="tr-overview-items-icon">
        <img src="images/icons/overview-info-icon.svg" alt="">
    </div>
    <div>
        <h4>More info</h4>
        <div class="tr-more-info">
            <ul>
                @foreach(explode(',', $searchresult[0]->Moreinfo) as $info)
                    <li>{{ trim($info) }}</li>
                @endforeach
            </ul>
        </div>
    </div>
</div>
@endif

                @if (!empty($searchresult[0]->ThingstoKnow))
                <div class="tr-things-know">
                    <h3>Things to Know</h3>
                    <ul id="thingsToKnowContent" style="text-align: justify; list-style-position: inside; max-height: 100px; overflow: hidden;">
                        @if (is_array($searchresult[0]->ThingstoKnow))
                            @foreach ($searchresult[0]->ThingstoKnow as $thing)
                                <li> {{ $thing }}.</li>
                            @endforeach
                        @elseif (is_string($searchresult[0]->ThingstoKnow))
                            @php
                                $thingsArray = preg_split('/\r\n|\r|\n/', $searchresult[0]->ThingstoKnow); // Split by line breaks
                            @endphp
                            @foreach ($thingsArray as $thing)
                                @if (!empty(trim($thing)))
                                    <li><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon"> {{ trim($thing) }}.</li>
                                @endif
                            @endforeach
                        @endif
						@if (is_array($searchresult[0]->Children_Policy))
                            @foreach ($searchresult[0]->Children_Policy as $thing)
                                <li> {{ $thing }}.</li>
                            @endforeach
                        @elseif (is_string($searchresult[0]->Children_Policy))
                            @php
                                $thingsArray = preg_split('/\r\n|\r|\n/', $searchresult[0]->Children_Policy); // Split by line breaks
                            @endphp
                            @foreach ($thingsArray as $thing)
                                @if (!empty(trim($thing)))
                                    <li><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon"> {{ trim($thing) }}.</li>
                                @endif
                            @endforeach
                        @endif
						@if (is_array($searchresult[0]->Beds_Policy))
                            @foreach ($searchresult[0]->Beds_Policy as $thing)
                                <li> {{ $thing }}.</li>
                            @endforeach
                        @elseif (is_string($searchresult[0]->Beds_Policy))
                            @php
                                $thingsArray = preg_split('/\r\n|\r|\n/', $searchresult[0]->Beds_Policy); // Split by line breaks
                            @endphp
                            @foreach ($thingsArray as $thing)
                                @if (!empty(trim($thing)))
                                    <li><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon"> {{ trim($thing) }}.</li>
                                @endif
                            @endforeach
                        @endif
						@if (isset($searchresult[0]->Smoking_Policy))
                <li><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon"> <strong>Smoking Policy:</strong> 
                    @if($searchresult[0]->Smoking_Policy === 0)
                        No Smoking Allowed
                    @else
                        Smoking Allowed
                    @endif
                </li>
            @endif
@if (is_array($searchresult[0]->Curfew_Policy))
                            @foreach ($searchresult[0]->Curfew_Policy as $thing)
                                <li> {{ $thing }}.</li>
                            @endforeach
                        @elseif (is_string($searchresult[0]->Curfew_Policy))
                            @php
                                $thingsArray = preg_split('/\r\n|\r|\n/', $searchresult[0]->Curfew_Policy); // Split by line breaks
                            @endphp
                            @foreach ($thingsArray as $thing)
                                @if (!empty(trim($thing)))
                                    <li><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon"> {{ trim($thing) }}.</li>
                                @endif
                            @endforeach
                        @endif
@if (is_array($searchresult[0]->QuiteHours_Policy))
                            @foreach ($searchresult[0]->QuiteHours_Policy as $thing)
                                <li> {{ $thing }}.</li>
                            @endforeach
                        @elseif (is_string($searchresult[0]->QuiteHours_Policy))
                            @php
                                $thingsArray = preg_split('/\r\n|\r|\n/', $searchresult[0]->QuiteHours_Policy); // Split by line breaks
                            @endphp
                            @foreach ($thingsArray as $thing)
                                @if (!empty(trim($thing)))
                                    <li><img src="{{ asset('/frontend/hotel-detail/images/amenities/circle.svg') }}" alt="icon" class="Icon"> {{ trim($thing) }}.</li>
                                @endif
                            @endforeach
                        @endif
                    </ul>
                    <button id="toggleButton" onclick="toggleReadMore()" class="tr-anchor-btn">
                        Read More
                    </button>
                </div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const button = document.getElementById("toggleButton"); // Using the id directly

    button.addEventListener("click", function() {
        const content = this.previousElementSibling; // Select the <ul> content above the button

        if (content.style.maxHeight === "100px") {
            content.style.maxHeight = "none";
            this.textContent = "Read Less";
        } else {
            content.style.maxHeight = "100px";
            this.textContent = "Read More";
        }
    });
});
</script>

            @endif

            </div>
            <!--OVERVIEW SECTION - END-->
          </div>
        </div>
   

      <div class="col-lg-5 col-md-12 tr-right-col">
        <div class="tr-hotel-certificated">
          <div class="tr-families-favourite">
            <img  src="{{ asset('/frontend/hotel-detail/images/leaf.png')}}" alt="leaf">
            <span>Families Favourite</span>
          </div>
         <div class="tr-spotlight">
            <div class="tr-heading"><svg width="24" height="7" viewBox="0 0 24 7" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M23.3868 3.5L20.5 0.613249L17.6132 3.5L20.5 6.38675L23.3868 3.5ZM0.5 4H20.5V3H0.5V4Z"
                  fill="#09707A" />
              </svg>IN THE SPORTLIGHT<svg width="24" height="7" viewBox="0 0 24 7" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M0.613249 3.5L3.5 0.613249L6.38675 3.5L3.5 6.38675L0.613249 3.5ZM23.5 4H3.5V3H23.5V4Z"
                  fill="#09707A" />
              </svg></div>
            <div>Batch View. Delicious Food.</div>
            <div>Kids- Friendly</div>
          </div>
          <div class="tr-business-rated">
            <div class="tr-title">Great for</div>
            <div class="tr-business">Business</div>
            <div class="tr-rated">Rated 4.0</div>
            <img  src="images/icons/marketeq-icon.svg" alt="">
          </div>
        </div> 
        <?php 
            $sessionData = session()->all(); 
            $checkinDate = "";
            $checkoutDate = "";
            $guest = "";
            if (isset($sessionData['checkin'])) {
              $checkin = str_replace('_', ' - ', $sessionData['checkin']);
              $dates = explode('_', $sessionData['checkin']);                    
              // $checkinDate = $dates[0];  
              // $checkoutDate = $dates[1]; 
              $checkinDatet = new DateTime($dates[0]);
              $checkoutDatet = new DateTime($dates[1]);
              $checkinDate = $checkinDatet->format('j M Y'); // 6 Sep 2024
              $checkoutDate = $checkoutDatet->format('j M Y'); // 9 Sep 2024
            } else {
              $checkin = 'Add date';
            }
            $guest = isset($sessionData['guest']) ? $sessionData['guest'] : ''; 
            $rooms = isset($sessionData['rooms']) ? $sessionData['rooms'] : ''; 
            
          ?>
        <div class="tr-booking-deatils">
          <div class="tr-date-section section-2">
            <label class="tr-room-guest"> <span class="room-count">@if($rooms !=''){{ $rooms }}@else 1 @endif </span> room , <span class="guest-count"> @if($guest !=''){{ $guest }}@else 2 @endif </span> guests</label>
            <div class="tr-add-edit-guest-count">
              <div class="tr-guests-modal">
                <div class="tr-add-edit-guest tr-total-num-of-rooms">
                  <div class="tr-guest-type">
                    <label class="tr-guest">Room</label>
                  </div>
                  <div class="tr-qty-box">
                    <button class="minus disabled" value="minus">-</button>
                    <input type="text" id="totalRoom"  class="totalRoom" value="@if($rooms !=''){{ $rooms }}@else 1 @endif" id="" min="1" max="10" name="" readonly />
                    <button class="plus" value="plus">+</button>
                  </div>
                </div>
                <div class="tr-add-edit-guest tr-total-guest">
                  <div class="tr-guest-type">
                    <label class="tr-guest">Adults</label>
                    <div class="tr-age">Ages 13 or above</div>
                  </div>
                  <div class="tr-qty-box">
                    <button class="minus disabled" value="minus">-</button>
                    <input type="text" id="totalAdultsGuest" value="@if($guest !=''){{ $guest }}@else 2 @endif" id="" min="1" max="10" name="" readonly />
                    <button class="plus" value="plus">+</button>
                  </div>
                </div>
                <div class="tr-add-edit-guest tr-total-children">
                  <div class="tr-guest-type">
                    <label class="tr-guest">Children</label>
                    <div class="tr-age">Ages 2 - 12</div>
                  </div>
                  <div class="tr-qty-box">
                    <button class="minus disabled" value="minus">-</button>
                    <input type="text" id="totalChildrenGuest" value="0" id="" min="1" max="10" name="" readonly />
                    <button class="plus" value="plus">+</button>
                  </div>
                </div>
                <div class="tr-add-edit-guest tr-total-infants">
                  <div class="tr-guest-type">
                    <label class="tr-guest">Infants</label>
                    <div class="tr-age">Under 2</div>
                  </div>
                  <div class="tr-qty-box">
                    <button class="minus disabled" value="minus">-</button>
                    <input type="text" id="totalChildrenInfants" value="0" id="" min="1" max="10" name="" readonly />
                    <button class="plus" value="plus">+</button>
                  </div>
                </div>
              </div>
            </div>

            <?php
            $checkin = "";
            $checkout = "";
            $checkin1 = request()->query("checkin");
            $checkout1 = request()->get("checkout");
            if ($checkin1 != "" && $checkout1 != "") {
                $checkin = DateTime::createFromFormat(
                    "Y-m-d",
                    $checkin1
                )->format("d M Y");
                $checkout = DateTime::createFromFormat(
                    "Y-m-d",
                    $checkout1
                )->format("d M Y");
            }

            if (!empty($searchresult)) {
                $hotelid = $searchresult[0]->hotelid;
                $photoCount = $searchresult[0]->photoCount;
            }
            ?>
            <span class="checkin d-none">{{$checkin}}</span>
            <span class="checkout d-none">{{$checkout}}</span>
            <div class="tr-quick-search-hotel">
              <div class="tr-quick-search">
                <div class="tr-stay-date">
                  <div class="tr-col">
                    <label>Check-in</label>
                    <input type="text" @if(Session::has('checkin')) value="{{$checkin}}" @endif id="checkInSelectDate"
                      class="checkinval2 checkin" name="" placeholder="select date" name="" autocomplete="off">
                  </div>
                  <div class="tr-col">
                    <label>Check-out</label>
                    <!-- <input type="text" @if(Session::has('checkout')) value="{{$checkout}}" @endif id="checkOutSelectDate"
                      class="checkoutval2" name="" placeholder="--/--/----" name="" autocomplete="off"> -->
                      <input type="text" @if(Session::has('checkin')) value="{{$checkout}}" @endif id="checkOutSelectDate"
                      class="checkoutval2 checkout" name="" placeholder="select date" name="" autocomplete="off">
                  </div>
                </div>
                <button type="submit" class="tr-btn filterchackinout">Get Price</button>
                <!-- <span class="d-none price-loader"> -->
                <button type="submit" class="tr-btn is-loading d-none price-loader">Get Price</button>
                <div class="tr-loader d-none price-loader">
                    <div class="ball"></div>
                    <div class="ball"></div>
                    <div class="ball"></div>
                    <div class="ball"></div>
                  </div>
                  <!-- </span> -->
              </div>
                <!-- start calendar code -->
              <!-- <div class="tr-calenders-modal-2">
                <div class="tr-calenders-top">
                  <div class="tr-search-info">
                      <div class="tr-total-nights" id="totalNights"></div>
                   <div class="tr-check-in-checkout-date" id="totaldates" >@if(Session::has('checkin')) {{$checkin}} @endif -
                      @if(Session::has('checkin')){{$checkout}} @endif</div>
                  </div>
                    <div class="tr-stay-date">
                    <div class="tr-col">
                        <label for="checkInInput2">Check-in</label>

                        <input type="text" id="checkInInput2" class="dateInput checkinval2" @if(Session::has('checkin'))
                            value="{{$checkin}}" @endif placeholder="Select a date" name="" readonly autocomplete="off">
                    </div>
                    <div class="tr-col">
                        <label for="checkOutDate">Check-out</label>

                        <input type="text" id="checkOutDate" class="dateInput checkoutval2" @if(Session::has('checkin'))
                            value="{{$checkout}}" @endif placeholder="Select a date" name="" readonly autocomplete="off">
                    </div>
                </div>
                </div>
               <div class="tr-calenders-section">
                    <div class="navigation">
                      <button class="prevMonth">Previous</button>
                      <button class="nextMonth">Next</button>
                    </div>
                    <div id="calendar3" class="custom-calendar">
                      <div class="monthYear"></div>
                      <div class="calendarBody"></div>
                    </div>
                    <div id="calendar4" class="custom-calendar">
                      <div class="monthYear"></div>
                      <div class="calendarBody"></div>
                    </div>
                  </div>
                  <div class="tr-action">
                    <button class="tr-clear-details">Clear dates</button>
                    <button class="tr-close-btn">Close</button>
                  </div>
              </div> -->
              <div class="tr-calenders-modal-2">
                <div class="tr-calenders-top">
                  <div class="tr-search-info">
                      <div class="tr-total-nights" id="totalNights"></div>
                    <div class="tr-check-in-checkout-date" id="totaldates" >@if(Session::has('checkin')) {{$checkin}} @endif -
                      @if(Session::has('checkin')){{$checkout}} @endif</div>
                  </div>
                    <div class="tr-stay-date">
                    <div class="tr-col">
                        <label for="checkInInput2">Check-in</label>

                        <input type="text" id="checkInInput2" class="checkIn dateInput checkinval2" @if(Session::has('checkin'))
                            value="{{$checkin}}" @endif placeholder="Select a date" name="" readonly autocomplete="off">
                    </div>
                    <div class="tr-col">
                        <label for="checkOutInput2">Check-out</label>

                        <input type="text" id="checkOutInput2" class="checkOut dateInput checkoutval2" @if(Session::has('checkin'))
                            value="{{$checkout}}" @endif placeholder="Select a date" name="" readonly autocomplete="off">
                    </div>
                </div>
                </div>
                <div class="tr-calenders-section">
                  <div id="calendarPair2" class="calendarPair">
                    <div class="navigation">
                      <button type="button" class="prevMonth" id="prevMonth2">Previous</button>
                      <button type="button" class="nextMonth" id="nextMonth2">Next</button>
                    </div>
                    <div class="custom-calendar checkInCalendar" id="checkInCalendar2">
                      <div class="monthYear"></div>
                      <div class="calendarBody"></div>
                    </div>
                    <div class="custom-calendar checkOutCalendar" id="checkOutCalendar2">
                      <div class="monthYear"></div>
                      <div class="calendarBody"></div>
                    </div>
                  </div>
                </div>
                <div class="tr-action">
                  <button type="button" class="tr-clear-details" id="reset2">Clear dates</button>
                  <button class="tr-close-btn">Close</button>
                </div>
          </div>
              <!-- end calendar code -->
            </div>
          </div>

          <div class="tr-travel-sites">
            <span id="hotel_price" style="display: block;">
              <!-- start code -->
              @if (!$getprice->isEmpty())
    @php
        $lowesttotals = [];
        $othertotals = [];
        $displayedPrices = []; // To store already displayed prices to ensure uniqueness
        $uniquePrices = []; // To track all unique prices

        // Prepare the lowest and other totals
        foreach ($getprice as $room) {
            $agencyName = $room->agency_name;
            $total = $room->price;
            $agencyId = $room->agency_id;
            $fullBookingURL = $room->booking_link;

            // Store unique prices
            if (!in_array($total, $uniquePrices)) {
                $uniquePrices[] = $total;
            }

            // Find the lowest price for each agency
            if (!isset($lowesttotals[$agencyName]) || $total < $lowesttotals[$agencyName]['total']) {
                $lowesttotals[$agencyName] = ['total' => $total, 'fullBookingURL' => $fullBookingURL, 'agencyId' => $agencyId];
            }

            // Store other totals if not already displayed
            if (!isset($displayedPrices[$agencyName][$total])) {
                $othertotals[$agencyName][] = ['total' => $total, 'fullBookingURL' => $fullBookingURL, 'agencyId' => $agencyId];
                $displayedPrices[$agencyName][$total] = true;
            }
        }

        $latval = null;
        $count = 0;
        $showMore = false;
        $prices = []; // To store prices for later access
    @endphp

    <!-- Display the lowest totals -->
    <div style="text-align: center;">
        @foreach ($uniquePrices as $index => $total)
            @php
                $agencyData = null;
                foreach ($getprice as $room) {
                    if ($room->price == $total) {
                        $agencyData = $room;
                        break;
                    }
                }
                $isLowestPrice = isset($lowesttotals[$agencyData->agency_name]) && $total == $lowesttotals[$agencyData->agency_name]['total'];
                $prices[] = $total; // Store the price
            @endphp

            @if ($index == 0)
                <!-- Show the cheapest deal label with the lowest price -->
                <div class="tr-travel-site">
                    <div class="tr-travel-img">
                        <div style="color: green;">cheapest deal</div>
                        <img loading="lazy" src="{{ 'http://pics.avs.io/hl_gates/100/30/' . $agencyData->agency_id . '.png' }}" alt="Booking">
                    </div>
                    <div class="tr-travel-price">
                        <a href="{{ $agencyData->booking_link }}" target="_blank"><strong>${{ $total }} </strong> /night</a>
                    </div>
                </div>
            @elseif ($index < 3)
                <!-- Show the first 3 prices -->
                <div class="tr-travel-site" style="border-top: 1px solid #E9E9E9;">
                    <div class="tr-travel-img">
                        <img src="{{ 'http://pics.avs.io/hl_gates/100/30/' . $agencyData->agency_id . '.png' }}" alt="Booking">
                    </div>
                    <div class="tr-travel-price">
                        <a href="{{ $agencyData->booking_link }}" target="_blank"><strong>${{ $total }} </strong> /night</a>
                    </div>
                </div>
            @else
                @php $showMore = true; @endphp
                <div class="tr-travel-site d-none" style="border-top: 1px solid #E9E9E9;">
                    <div class="tr-travel-img">
                        <img src="{{ 'http://pics.avs.io/hl_gates/100/30/' . $agencyData->agency_id . '.png' }}" alt="Booking">
                    </div>
                    <div class="tr-travel-price">
                        <a href="{{ $agencyData->booking_link }}" target="_blank"><strong>${{ $total }} </strong> /night</a>
                    </div>
                </div>
            @endif
        @endforeach

        @php
            // Update $latval to the fourth price if it exists
            if (count($prices) > 3) {
                $latval = $prices[3];
            }
        @endphp
    </div>

    <!-- Show the 'Show More' button only if there are more than 3 prices -->
    @if($showMore)
        @php $remainingDeals = count($uniquePrices) - 3; @endphp
        <div class="tr-show-all">
            <button id="showMoreDeal" type="button" value="More">{{ $remainingDeals }} more deals from ${{$latval}}</button>
            <button id="showLessDeal" type="button" value="Less" class="d-none">Show less deals from ${{$latval}}</button>
        </div>
    @endif
@else
   @if(empty($prices) && !empty(request()->get('checkin')) && !empty(request()->get('checkout')))
    @if(!isset($soldOutDisplayed)) 
        @php $soldOutDisplayed = true; @endphp
        <div class="tr-sold-out">
            <img src="{{ asset('/frontend/hotel-detail/images/sold-out.png') }}" alt="Sold Out">
			<p>Please try different dates</p>
        </div>
    @endif
@endif

@endif

              <!-- end price code -->
            </span>
          </div>
          <!-- Price Sticky on Small Device -->
          <div class="tr-price-fixed-section">
            <div class="tr-price-section">
              @if($searchresult[0]->pricefrom !="")
              <div class="tr-price">${{$searchresult[0]->pricefrom}}</div>
              <div class="tr-sale-price">${{$searchresult[0]->pricefrom}}/night</div>
              <div class="tr-price-tax">+850 taxes &amp; fees</div>
              @endif
            </div>
            <div class="tr-select-room-section">
              <button class="tr-btn">Select Room</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

 <div class="container">
  <div class="row">
    <div class="col-sm-12 responsive-container">

      <!-- ROOM SECTION - START -->
      <div class="tr-room-section tr-tab-section" id="roomTab">

        @if(!empty($amenitiesListroom))
          <h3>Room</h3>
          <div class="tr-room-filters">
            <span class="tr-filter-label">Filter rooms by</span>
           <form class="tr-filter-lists" id="tr-filter-lists">
  @foreach($amenitiesListroom as $key => $value)
    @if($value != 'doublebed' && $value != 'twin')
      <div class="tr-filter-list filter_hotel_room">
        <input type="checkbox" name="values[]" id="filter1-{{ $value }}" class="filter allamenities {{ $value }}" data-value="{{ $value }}" value="{{ $value }}">
        <label for="filter1-{{ $value }}">
          <i class="{{ fontArray($value) }}"></i> {{ strip_tags(getAmenitiesNames($value)) }}
        </label>
      </div>
    @endif
  @endforeach
</form>
          </div>
        @endif

        <div class="tr-hotel-lists-first" id="get_room_result">
          @php
            $groupedRooms = [];
            if (!empty($RoomsData)) {
              foreach($RoomsData as $room) {
                $groupedRooms[$room->roomType][] = $room;
              }
            }
          @endphp

          @if(!empty($groupedRooms))
            @foreach($groupedRooms as $roomType => $roomEntries)
              <div class="tr-hotel-deatils">

                <!-- Room Image -->
                <div class="tr-hotal-image">
                  <div id="roomSlider{{ $loop->index }}">
                    <img loading="lazy" src="https://photo.hotellook.com/image_v2/limit/h{{ $roomEntries[0]->hotelid }}_0/300.jpg" alt="Room Image" height="203" width="296">
                  </div>
                </div>

                <!-- Room Title and Amenities -->
                <div class="tr-hotel-deatil">
                  <h2 style="text-align: left;">{{ $roomType }}</h2>
                <ul class="tr-desktop">
  @foreach($amenitiesListroom as $amenity)
    @if(isset($roomEntries[0]->$amenity) && $roomEntries[0]->$amenity == 1)
      <li><i class="Roomamenities {{ fontArray($amenity) }}"></i> {{ strip_tags(getAmenitiesNames($amenity)) }}</li>
    @endif
  @endforeach
</ul>
                </div>

                <!-- Prices Section -->
                <div class="tr-hotel-price-lists">
                  @foreach($roomEntries as $roomEntry)
                    <div class="tr-hotel-price-list">

                      @php
                        $amenitiesList = [];
                        foreach($roomEntry as $amenity => $hasAmenity) {
                          if ($hasAmenity == 1) {
                              $amenitiesList[] = getAmenitiesNames($amenity);
                          }
                        }
                      @endphp

                      @if(!empty($amenitiesList))
                        <h4>Room with {{ implode(', ', $amenitiesList) }}</h4>
                      @endif

                      <div class="tr-hotel-facilities">
                      <ul>
  @foreach($amenitiesListroom as $amenity)
    @if(isset($roomEntry->$amenity) && $roomEntry->$amenity == 1)
      <li>{{ strip_tags(getAmenitiesNames($amenity)) }}</li>
    @endif
  @endforeach
</ul>
                      </div>

                      <div class="tr-site-details">
                        <div class="tr-deal">Cheapest deal</div>
                        @php
    $agencyImage = '';
    if (!empty($roomEntry->agencyName)) {
        $agencyName = strtolower($roomEntry->agencyName);
        $agencyName = explode('.com', $agencyName)[0];
        $agencyName = str_replace(['https://', 'http://', 'www.'], '', $agencyName);
        $agencyImage = asset('agency/' . $agencyName . '.png');
    }
  @endphp

  @if(!empty($agencyImage))
    <img loading="lazy" src="{{ asset('agency/' . $agencyName . '.png') }}" alt="{{ ucfirst($agencyName) }}" width="80" height="40">
  @endif
                        <div class="tr-hotel-facilities">
                          <ul>
                            @foreach($roomEntry as $amenity => $hasAmenity)
                              @if($hasAmenity == 1)
                                <li>{{ getAmenitiesNames($amenity) }}</li>
                              @endif
                            @endforeach
                          </ul>
                        </div>
                      </div>

                      @php
  $bookingUrl = $roomEntry->booking_url;
  if (!empty($bookingUrl) && !preg_match('/^https?:\/\//', $bookingUrl)) {
      $bookingUrl = 'https://' . $bookingUrl;
  }
@endphp

<a href="{{ $bookingUrl }}" target="_blank" rel="noopener noreferrer">
  <span><strong>${{ $roomEntry->price }}</strong> /night</span>
  <i class="tr-arrow"></i>
</a>


                    </div> <!-- /.tr-hotel-price-list -->
                  @endforeach
                </div> <!-- /.tr-hotel-price-lists -->

              </div> <!-- /.tr-hotel-deatils -->
            @endforeach
          @endif
        </div> 
      </div>
      <!-- ROOM SECTION - END -->
    </div>
  
  
        <!--FACILITIES SECTION - START-->
        <div class="tr-facilities-section tr-tab-section" id="amenitiesTab">
        <h3>Facilities</h3>
          <?php
          $popamenities = [
              "City view" => "City view",
              "Lake view" => "Lake view",
              "Mountain view" => "Mountain view",
              "River view" => "River view",
              "Sea view" => "Sea view",
              "View" => "View",
              "Apartments" => "Apartments",
              "Garden" => "Garden",
              "Pets allowed" => "Pets allowed",
              "Swimming Pool" => "Swimming Pool",
              "Outdoor Swimming Pool" => "Outdoor Swimming Pool",
              "Free WiFi" => "Wi-Fi in room",
              "Free WiFi" => "Wi-Fi in  areas",
              "Free WiFi" => "Wi-Fi Available",
              "Air conditioning" => "Air conditioning",
              "24-hour front desk" => "24h. Reception",
              "Free parking" => "Free parking",
              "Car parking" => "Car parking",
              "parking" => "Parking",
              "Parking on site" => "Parking on site",
              "Shuttle service (free)" => "Shuttle service (free)",
              "Airport shuttle (free)" => "Airport shuttle (free)",
              "Key card access" => "Key card access",
              "Balcony" => "Balcony",
              "Luggage storage" => "Luggage storage",
              "Non-smoking rooms" => "Non-smoking rooms",
              "Terrace" => "Terrace",
              "BBQ facilities" => "BBQ facilities",
              "Bath" => "Bath",
              "Daily housekeeping" => "Daily Housekeeping",
              "Entire apartment" => "Entire apartment",
              "Entire home" => "Entire home",
              "Facilities for disabled guests" =>
               "Facilities for disabled guests",
              "Grocery deliveries" => "Grocery deliveries",
              "Heating" => "Heating",
              "Kitchen" => "Kitchen",
              "Lift" => "Lift",
              "Private bathroom" => "Private Bathroom",
              "Safety deposit box" => "Safety deposit box",
              "Sauna" => "Sauna",
              "Washing machine" => "Washing machine",
              "Laundry service" => "Laundry service",
              "Laundry" => "Laundry service",
          ];
      
          // return print_r($shortFacilities);
          ?>
          <div class="tr-facilities-details">
            @if(!empty($shortFacilities))
            <?php
              $matchingFacilities = collect();

              // Gather only unique matching facilities in the order specified by $popamenities
              foreach ($popamenities as $amenity) {
                  $match = $shortFacilities->firstWhere('name', $amenity);
                  if ($match && !$matchingFacilities->contains('name', $match->name)) {
                      $matchingFacilities->push($match);
                  }
              }

              // Gather facilities that don’t match $popamenities
              $nonMatchingFacilities = $shortFacilities->reject(function ($facility) use ($popamenities) {
                  return in_array($facility->name, array_keys($popamenities));
              });

              // Combine collections, keeping the order specified in $popamenities
              $sortedFacilities = $matchingFacilities->merge($nonMatchingFacilities);
              ?>

            <ul class="tr-facilities-box-lists">
              @foreach ($sortedFacilities as $mnt)
              <li> 
              @php
           $iconName = \App\Helpers\AmenityHelper::getIconName($mnt->name);
              @endphp
              <img src="{{ asset('/frontend/hotel-detail/images/amenities/'.$iconName.'.svg') }}" alt="{{ $mnt->name }}">
                <div class="tr-sub-title">{{$mnt->shortName}}</div>
              </li>
              @endforeach
            </ul>
            @else
            <p>Amenities Not Available.</p>
            @endif
          </div>

          <div class="tr-facilities-lists tr-desktop">
            <h3 class="tr-mobile">Facilities</h3>
            @if(!$facilityNames->isEmpty())
            @php
            $groupedFacilities = [];
            foreach ($facilityNames as $facility) {
            $groupedFacilities[$facility->groupName][] = $facility->name;
            }
            @endphp

            @foreach ($groupedFacilities as $groupName => $facilityNames)
            <div class="tr-facilities-list">
              <div class="facility-group">
                <h4>
                  <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M16.6615 17.8091V16.1424C16.6615 15.2584 16.3103 14.4105 15.6851 13.7854C15.06 13.1603 14.2122 12.8091 13.3281 12.8091H6.66146C5.7774 12.8091 4.92956 13.1603 4.30444 13.7854C3.67931 14.4105 3.32813 15.2584 3.32812 16.1424V17.8091"
                      stroke="#222222" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M10.0052 9.47575C11.8462 9.47575 13.3385 7.98336 13.3385 6.14242C13.3385 4.30147 11.8462 2.80908 10.0052 2.80908C8.16426 2.80908 6.67188 4.30147 6.67188 6.14242C6.67188 7.98336 8.16426 9.47575 10.0052 9.47575Z"
                      stroke="#222222" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                  {{ $groupName }}
                </h4>
                <ul>
                  @foreach ($facilityNames as $facilityName)
                  <li class="tr-available">{{ $facilityName }}</li>
                  @endforeach
                </ul>
              </div>
            </div>
            @endforeach
			  @else
			     <p>Amenities Not Available.</p>
            @endif
          </div>
        
<div class="tr-facilities-lists tr-mobile d-block d-md-none">
    @if(!empty($amenitiesArray) && count($amenitiesArray) > 0)
        @foreach ($amenitiesArray as $groupName => $amenity)
            <div class="tr-facilities-list">
                <div class="facility-group">
                    <h4>
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M16.6615 17.8091V16.1424C16.6615 15.2584 16.3103 14.4105 15.6851 13.7854C15.06 13.1603 14.2122 12.8091 13.3281 12.8091H6.66146C5.7774 12.8091 4.92956 13.1603 4.30444 13.7854C3.67931 14.4105 3.32813 15.2584 3.32812 16.1424V17.8091"
                                stroke="#222222" stroke-linecap="round" stroke-linejoin="round" />
                            <path
                                d="M10.0052 9.47575C11.8462 9.47575 13.3385 7.98336 13.3385 6.14242C13.3385 4.30147 11.8462 2.80908 10.0052 2.80908C8.16426 2.80908 6.67188 4.30147 6.67188 6.14242C6.67188 7.98336 8.16426 9.47575 10.0052 9.47575Z"
                                stroke="#222222" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ $groupName }}
                    </h4>
                    <ul>
                        <li class="tr-available">{{ $amenity }}</li>
                    </ul>
                </div>
            </div>
        @endforeach
    @else
        <p>Amenities Not Available.</p>
    @endif
</div>
<div class="tr-show-all"><a href="javascript:void(0);">Show all</a></div>
        </div>

        </div>
        <!--FACILITIES SECTION - END-->

        <!--LOCATION SECTION - START-->
        <div class="tr-Location-section tr-tab-section" id="locationTab">
          <h3>Location</h3>
          <div class="tr-Location-details">
            <ul>
              @if($searchresult[0]->address != "") <li class="tr-location-address">{{$searchresult[0]->address}}
              </li>@endif
              <li class="tr-phone">@if(!empty($searchresult[0]->Phone)) +{{$searchresult[0]->Phone}} @else Not
                Available. @endif</li>
              <li class="tr-email">@if(!empty($searchresult[0]->Email)) {{$searchresult[0]->Email}} @else Not
                Available. @endif</li>
              <li class="tr-website-link"><a href="@if(!empty($searchresult[0]->Website)) {{$searchresult[0]->Website}} @endif" target="_blank">Visit website</a></li>
            </ul>
          </div>

          <?php
          $latitude = "";
          $longitude = "";

          $latitude = $searchresult[0]->Latitude;
          $longitude = $searchresult[0]->longnitude;
          ?>

          
<div class="tr-location-map">
    @if($searchresult[0]->Latitude != "" && $searchresult[0]->longnitude != "")
        <div id="map1" class="" style="width: 100%; height: 400px;"></div>
        
        @php
            $hotelId = $searchresult[0]->id;
            $nearbySights = DB::table('Tripalong.TPhotel_neaby_sight')
                ->where('hotelid', $hotelId)
                ->get();
        @endphp
        <script>
            var nearbySightsData = {!! json_encode($nearbySights) !!};
        </script>

        <div id="screenshotContainer"></div>
    @endif
</div>
          <!-- static content 3 -->
          <!-- <p>The neighborhood is a blend of ancient history and modern dynamism. Walk among towering skyscrapers and
            historic pubs, enjoying the juxtaposition of old and new. Explore the bustling Leadenhall Market,
            brimming with charm and vibrant stalls. Financial district by day, the City transforms into a tranquil
            area by night, offering unique experiences like night tours of iconic landmarks. Its rich history and
            contemporary vibrancy make the City of London a captivating destination.</p> -->
        </div>
        <!--LOCATION SECTION - END-->

        <!--EXPLORE OTHER OPTIONS IN AND AROUND SECTION - START-->
        <div class="tr-explore-section responsive-container">
          <h3 class="d-none d-md-block">Explore other options in and around</h3>
          <div class="tr-tourist-places-row">
            <div class="tr-tourist-places">
              <div class="tr-title open">
                <h4><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M17.3335 5.05859C17.3335 6.20565 16.2305 7.30859 15.0835 7.30859C16.2305 7.30859 17.3335 8.41154 17.3335 9.55859C17.3335 8.41154 18.4364 7.30859 19.5835 7.30859C18.4364 7.30859 17.3335 6.20565 17.3335 5.05859Z"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M17.3335 15.0586C17.3335 16.2056 16.2305 17.3086 15.0835 17.3086C16.2305 17.3086 17.3335 18.4115 17.3335 19.5586C17.3335 18.4115 18.4364 17.3086 19.5835 17.3086C18.4364 17.3086 17.3335 16.2056 17.3335 15.0586Z"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M9.3335 8.05859C9.3335 10.2252 7.25015 12.3086 5.0835 12.3086C7.25015 12.3086 9.3335 14.3919 9.3335 16.5586C9.3335 14.3919 11.4168 12.3086 13.5835 12.3086C11.4168 12.3086 9.3335 10.2252 9.3335 8.05859Z"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>Top attractions</h4>
                <span class="d-none d-md-block">Sorted by nearest @if(!$nearby_sight->isEmpty()) within
                  {{$nearby_sight[0]->radius}} km @endif</span>
              </div>
              <div class="tr-tourist-place-lists" id="nba">

                @if(!$nearby_sight->isEmpty())
                @foreach($nearby_sight as $value)

                <div class="tr-tourist-place">
                  <div class="tr-img">
                    <a href="javascript:void(0);"><img  src="images/tourist-places-1.png"
                        alt="tourist-places"></a>
                  </div>
                  <div class="tr-details">
                    <div class="tr-place-name"><a
                        href="{{ asset('at-'.$value->LocationId.'-'.$value->SightId.'-'.strtolower($value->slug)) }}">{{$value->name}}</a>
                    </div>
                    <div class="tr-place-type">Shopping area</div>
                    <div class="tr-like-review">
                      <span class="tr-likes">35% (static)</span>
                      <span class="tr-review">. 22 reviews</span>
                    </div>
                    <div class="tr-distance">{{ round($value->distance,2) }} Km</div>
                  </div>
                </div>
                @endforeach
                @else
                <div class="tr-tourist-place">
                  <p>Nearby attraction not found.</p>
                </div>
                @endif

              </div>
            </div>
            <div class="tr-tourist-places" id="nearbyrest">
              <div class="tr-title">
                <h4><svg width="24" height="20" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M1.63492 1.90625L15.6158 15.8871C15.8811 16.1524 16.0301 16.5122 16.0301 16.8874C16.0301 17.2626 15.8811 17.6224 15.6158 17.8877C15.3504 18.1529 14.9906 18.3018 14.6155 18.3018C14.2404 18.3018 13.8806 18.1529 13.6152 17.8877L10.2 14.4136C9.97647 14.1867 9.85109 13.881 9.85086 13.5625V13.3526C9.85089 13.1918 9.81897 13.0326 9.75695 12.8842C9.69494 12.7358 9.60406 12.6012 9.4896 12.4882L9.04865 12.081C8.89896 11.9429 8.71694 11.8447 8.51933 11.7953C8.32172 11.746 8.11487 11.7471 7.91783 11.7987C7.6071 11.8798 7.28058 11.8782 6.97065 11.7942C6.66071 11.7101 6.37816 11.5464 6.15101 11.3194L2.90918 8.07722C0.986021 6.15405 0.278307 3.24996 1.63492 1.90625Z"
                      stroke="#131313" stroke-linejoin="round"></path>
                    <path
                      d="M14.6312 1.30859L11.6998 4.24001C11.4742 4.46554 11.2953 4.73329 11.1732 5.02798C11.0511 5.32267 10.9883 5.63852 10.9883 5.9575V6.52139C10.9883 6.60118 10.9726 6.68018 10.9421 6.75389C10.9115 6.82759 10.8668 6.89456 10.8103 6.95096L10.3811 7.38014M11.5954 8.59445L12.0246 8.16526C12.081 8.10883 12.148 8.06407 12.2217 8.03353C12.2954 8.00299 12.3744 7.98728 12.4542 7.98729H13.0181C13.3371 7.98729 13.6529 7.92446 13.9476 7.80238C14.2423 7.6803 14.51 7.50136 14.7356 7.27578L17.667 4.34437M16.1491 2.82648L13.1133 5.86225M7.04179 14.0588L3.2577 17.8642C2.97305 18.1487 2.58704 18.3086 2.18455 18.3086C1.78207 18.3086 1.39605 18.1487 1.11141 17.8642C0.826849 17.5795 0.666992 17.1935 0.666992 16.791C0.666992 16.3885 0.826849 16.0025 1.11141 15.7179L4.30959 12.541"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round"></path>
                  </svg>Restaurants</h4>
                <span class="d-none d-md-block">@if(!$nearby_rest->isEmpty()) Within {{ $restradus}} KM
                  @endif</span>
              </div>
              <div class="tr-tourist-place-lists">
                @if(!$nearby_rest->isEmpty())
                @foreach($nearby_rest as $value)
                <div class="tr-tourist-place">
                  <div class="tr-img">
                    <a href="javascript:void(0);"><img  src="images/tourist-places-4.png"
                        alt="tourist-places"></a>
                  </div>
                  <div class="tr-details">
                    <div class="tr-place-name"><a
                        href="{{ asset('rd-'.$value->slugid.'-'.$value->RestaurantId.'-'.strtolower($value->Slug)) }}">{{$value->Title}}</a>
                    </div>
                    <div class="tr-place-type">Shopping area</div>
                    <div class="tr-like-review">
                      <span class="tr-likes">{{$value->TATrendingScore}}% </span>
                      <span class="tr-review">.{{$value->review_count}} reviews</span>
                    </div>
                    <div class="tr-distance">{{ round($value->distance,2) }} km</div>
                  </div>
                </div>
                @endforeach
                @else
                <p>Nearby restaurant not found.</p>
                @endif

              </div>
            </div>


            <!-- end data rest -->
            <!-- <div class="tr-tourist-places">
              <div class="tr-title">
                <h4><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                      d="M17.3335 5.05859C17.3335 6.20565 16.2305 7.30859 15.0835 7.30859C16.2305 7.30859 17.3335 8.41154 17.3335 9.55859C17.3335 8.41154 18.4364 7.30859 19.5835 7.30859C18.4364 7.30859 17.3335 6.20565 17.3335 5.05859Z"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M17.3335 15.0586C17.3335 16.2056 16.2305 17.3086 15.0835 17.3086C16.2305 17.3086 17.3335 18.4115 17.3335 19.5586C17.3335 18.4115 18.4364 17.3086 19.5835 17.3086C18.4364 17.3086 17.3335 16.2056 17.3335 15.0586Z"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
                    <path
                      d="M9.3335 8.05859C9.3335 10.2252 7.25015 12.3086 5.0835 12.3086C7.25015 12.3086 9.3335 14.3919 9.3335 16.5586C9.3335 14.3919 11.4168 12.3086 13.5835 12.3086C11.4168 12.3086 9.3335 10.2252 9.3335 8.05859Z"
                      stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>Getting there</h4>
                <span class="d-none d-md-block">Walkable places</span>
              </div>
              <div class="tr-tourist-place-lists">
                <div class="tr-getting-lists">
                  <div class="tr-getting-list">
                    <div class="tr-place-info">
                      <div class="tr-place-name">Great for walks</div>
                      <span>Grade: 100 out of 100</span>
                    </div>
                    <div class="tr-distance">100</div>
                  </div>
                  <div class="tr-getting-list">
                    <div class="tr-place-info">
                      <div class="tr-place-name"><svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M11.6677 8.21686L11.4782 8.25293L11.4075 8.43241L8.58835 15.5894L7.6097 15.7757L8.3748 9.30726L8.4309 8.83302L7.96178 8.92232L3.5739 9.75759L3.37156 9.79611L3.30699 9.99171L2.47522 12.5116L1.87823 12.6253L1.98228 9.2217L1.98466 9.14395L1.95388 9.07252L0.606627 5.94522L1.20367 5.83157L2.90392 7.86957L3.03583 8.02769L3.23812 7.98919L7.626 7.15392L8.09517 7.0646L7.86869 6.64412L4.77982 0.909331L5.75841 0.723048L11.0099 6.34373L11.1416 6.48469L11.3311 6.44861L15.7902 5.59979C16.0247 5.55515 16.2673 5.60549 16.4647 5.73973L16.6615 5.45033L16.4647 5.73973C16.6621 5.87398 16.798 6.08113 16.8426 6.31561C16.8873 6.55009 16.8369 6.79271 16.7027 6.99007L16.9921 7.18692L16.7027 6.99007C16.5685 7.18744 16.3613 7.3234 16.1268 7.36803L11.6677 8.21686Z"
                            stroke="black" stroke-width="0.7" />
                        </svg>London city airport</div>
                      <span class="tr-distance-time"><svg width="13" height="14" viewBox="0 0 13 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M2.7085 12.1211L1.3335 13.4961M10.9585 12.1211L12.3335 13.4961M4.771 9.3711H4.77718M8.88981 9.3711H8.896M2.021 5.24609C4.771 7.99609 9.23975 7.99609 11.646 5.24609"
                            stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                          <path
                            d="M2.20862 4.48572C2.77305 1.61334 3.55406 1.12109 6.45874 1.12109H7.20812C10.1128 1.12109 10.8931 1.61334 11.4582 4.48572L11.8384 6.42172C12.3575 9.06172 12.6167 10.3817 11.8604 11.2514C11.1042 12.1211 9.68106 12.1211 6.83343 12.1211C3.98649 12.1211 2.56268 12.1211 1.80643 11.2514C1.05018 10.3817 1.30937 9.06172 1.82843 6.42172L2.20862 4.48572Z"
                            stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>20 min</span>
                      <a href="javascript:void(0);" class="tr-travel-type">See all flghts</a>
                    </div>
                    <div class="tr-distance">0.88 km</div>
                  </div>
                  <div class="tr-getting-list">
                    <div class="tr-place-info">
                      <div class="tr-place-name"><svg width="18" height="17" viewBox="0 0 18 17" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M11.6677 8.21686L11.4782 8.25293L11.4075 8.43241L8.58835 15.5894L7.6097 15.7757L8.3748 9.30726L8.4309 8.83302L7.96178 8.92232L3.5739 9.75759L3.37156 9.79611L3.30699 9.99171L2.47522 12.5116L1.87823 12.6253L1.98228 9.2217L1.98466 9.14395L1.95388 9.07252L0.606627 5.94522L1.20367 5.83157L2.90392 7.86957L3.03583 8.02769L3.23812 7.98919L7.626 7.15392L8.09517 7.0646L7.86869 6.64412L4.77982 0.909331L5.75841 0.723048L11.0099 6.34373L11.1416 6.48469L11.3311 6.44861L15.7902 5.59979C16.0247 5.55515 16.2673 5.60549 16.4647 5.73973L16.6615 5.45033L16.4647 5.73973C16.6621 5.87398 16.798 6.08113 16.8426 6.31561C16.8873 6.55009 16.8369 6.79271 16.7027 6.99007L16.9921 7.18692L16.7027 6.99007C16.5685 7.18744 16.3613 7.3234 16.1268 7.36803L11.6677 8.21686Z"
                            stroke="black" stroke-width="0.7" />
                        </svg>Heatthrow airport</div>
                      <span class="tr-distance-time"><svg width="13" height="14" viewBox="0 0 13 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M2.7085 12.1211L1.3335 13.4961M10.9585 12.1211L12.3335 13.4961M4.771 9.3711H4.77718M8.88981 9.3711H8.896M2.021 5.24609C4.771 7.99609 9.23975 7.99609 11.646 5.24609"
                            stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                          <path
                            d="M2.20862 4.48572C2.77305 1.61334 3.55406 1.12109 6.45874 1.12109H7.20812C10.1128 1.12109 10.8931 1.61334 11.4582 4.48572L11.8384 6.42172C12.3575 9.06172 12.6167 10.3817 11.8604 11.2514C11.1042 12.1211 9.68106 12.1211 6.83343 12.1211C3.98649 12.1211 2.56268 12.1211 1.80643 11.2514C1.05018 10.3817 1.30937 9.06172 1.82843 6.42172L2.20862 4.48572Z"
                            stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>20 min</span>
                      <a href="javascript:void(0);" class="tr-travel-type">See all flghts</a>
                    </div>
                    <div class="tr-distance">0.88 km</div>
                  </div>
                  <div class="tr-getting-list">
                    <div class="tr-place-info">
                      <div class="tr-place-name"><svg width="20" height="17" viewBox="0 0 20 17" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M17.0835 9.43359H14.8335M3.58349 9.43359H5.83349M17.4116 5.68359L15.9485 1.78191C15.7289 1.19645 15.1693 0.808594 14.544 0.808594H6.12299C5.49773 0.808594 4.93804 1.19645 4.7185 1.78191L3.25537 5.68359M17.4116 5.68359L17.6299 6.26568C17.7524 6.59225 18.0646 6.80859 18.4133 6.80859C18.9068 6.80859 19.293 7.23341 19.2463 7.72462L18.8335 12.0586M17.4116 5.68359H18.9585M3.25537 5.68359L3.03708 6.26568C2.91462 6.59225 2.60243 6.80859 2.25366 6.80859C1.76023 6.80859 1.37395 7.23341 1.42073 7.72462L1.83349 12.0586M3.25537 5.68359H1.70849M1.83349 12.0586L1.95418 13.3258C2.0275 14.0957 2.67408 14.6836 3.44742 14.6836H3.5835M1.83349 12.0586V12.0586C1.55735 12.0586 1.3335 12.2824 1.3335 12.5586V15.0586C1.3335 15.4728 1.66928 15.8086 2.0835 15.8086H2.8335C3.24771 15.8086 3.5835 15.4728 3.5835 15.0586V14.6836M3.5835 14.6836H17.0835M17.0835 14.6836H17.2196C17.9929 14.6836 18.6395 14.0957 18.7128 13.3258L18.8335 12.0586M17.0835 14.6836V15.0586C17.0835 15.4728 17.4193 15.8086 17.8335 15.8086H18.5835C18.9977 15.8086 19.3335 15.4728 19.3335 15.0586V12.5586C19.3335 12.2825 19.1096 12.0586 18.8335 12.0586V12.0586M6.24161 3.33425L5.41255 5.82142C5.25067 6.30707 5.61214 6.80859 6.12406 6.80859H14.5429C15.0548 6.80859 15.4163 6.30707 15.2544 5.82142L14.4254 3.33425C14.2212 2.72174 13.648 2.68359 13.0024 2.68359H7.66463C7.01899 2.68359 6.44578 2.72174 6.24161 3.33425Z"
                            stroke="black" stroke-width="0.7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>King's Cross St. Pancras</div>
                      <span class="tr-distance-time"><svg width="13" height="14" viewBox="0 0 13 14" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M2.7085 12.1211L1.3335 13.4961M10.9585 12.1211L12.3335 13.4961M4.771 9.3711H4.77718M8.88981 9.3711H8.896M2.021 5.24609C4.771 7.99609 9.23975 7.99609 11.646 5.24609"
                            stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                          <path
                            d="M2.20862 4.48572C2.77305 1.61334 3.55406 1.12109 6.45874 1.12109H7.20812C10.1128 1.12109 10.8931 1.61334 11.4582 4.48572L11.8384 6.42172C12.3575 9.06172 12.6167 10.3817 11.8604 11.2514C11.1042 12.1211 9.68106 12.1211 6.83343 12.1211C3.98649 12.1211 2.56268 12.1211 1.80643 11.2514C1.05018 10.3817 1.30937 9.06172 1.82843 6.42172L2.20862 4.48572Z"
                            stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>20 min</span>
                      <span class="tr-travel-type">Underground</span>
                    </div>
                    <div class="tr-distance">0.88 km</div>
                  </div>
                  <div class="tr-getting-list">
                    <div class="tr-place-info">
                      <div class="tr-place-name"><svg width="20" height="17" viewBox="0 0 20 17" fill="none"
                          xmlns="http://www.w3.org/2000/svg">
                          <path
                            d="M17.0835 9.43359H14.8335M3.58349 9.43359H5.83349M17.4116 5.68359L15.9485 1.78191C15.7289 1.19645 15.1693 0.808594 14.544 0.808594H6.12299C5.49773 0.808594 4.93804 1.19645 4.7185 1.78191L3.25537 5.68359M17.4116 5.68359L17.6299 6.26568C17.7524 6.59225 18.0646 6.80859 18.4133 6.80859C18.9068 6.80859 19.293 7.23341 19.2463 7.72462L18.8335 12.0586M17.4116 5.68359H18.9585M3.25537 5.68359L3.03708 6.26568C2.91462 6.59225 2.60243 6.80859 2.25366 6.80859C1.76023 6.80859 1.37395 7.23341 1.42073 7.72462L1.83349 12.0586M3.25537 5.68359H1.70849M1.83349 12.0586L1.95418 13.3258C2.0275 14.0957 2.67408 14.6836 3.44742 14.6836H3.5835M1.83349 12.0586V12.0586C1.55735 12.0586 1.3335 12.2824 1.3335 12.5586V15.0586C1.3335 15.4728 1.66928 15.8086 2.0835 15.8086H2.8335C3.24771 15.8086 3.5835 15.4728 3.5835 15.0586V14.6836M3.5835 14.6836H17.0835M17.0835 14.6836H17.2196C17.9929 14.6836 18.6395 14.0957 18.7128 13.3258L18.8335 12.0586M17.0835 14.6836V15.0586C17.0835 15.4728 17.4193 15.8086 17.8335 15.8086H18.5835C18.9977 15.8086 19.3335 15.4728 19.3335 15.0586V12.5586C19.3335 12.2825 19.1096 12.0586 18.8335 12.0586V12.0586M6.24161 3.33425L5.41255 5.82142C5.25067 6.30707 5.61214 6.80859 6.12406 6.80859H14.5429C15.0548 6.80859 15.4163 6.30707 15.2544 5.82142L14.4254 3.33425C14.2212 2.72174 13.648 2.68359 13.0024 2.68359H7.66463C7.01899 2.68359 6.44578 2.72174 6.24161 3.33425Z"
                            stroke="black" stroke-width="0.7" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>Rental cars</div>
                      <a href="javascript:void(0);" class="tr-travel-type">See rental cars</a>
                    </div>
                  </div>
                </div>
              </div>
            </div> -->

          </div>
        </div>
        <!--EXPLORE OTHER OPTIONS IN AND AROUND SECTION - END-->

        <!--TOP WAYS TO EXPERIENCE TOWER BRIDGE SECTION - START-->
        <div class="tr-top-ways-section responsive-container">
          <h3>Top ways to experience {{$searchresult[0]->name}}</h3>
          <div class="tr-top-ways-lists" id="nearby_exp">
            @if(!$get_experience->isEmpty())
            @foreach($get_experience as $get_experiences)
            <div class="tr-top-ways-list">
              <div class="tr-img">
                <a
                  href="{{route('experince',[$get_experiences->slugid.'-'.$get_experiences->ExperienceId.'-'.$get_experiences->Slug])}}"><img

                    src='@if($get_experiences->Img1 !=""){{$get_experiences->Img1}}  @else {{asset("/images/Hotel lobby.svg")}} @endif'
                    alt="top-places"></a>
              </div>
              <div class="tr-details">
                <div class="tr-place-type">Classes</div>
                <div class="tr-place-name"><a
                    href='@if($get_experiences->viator_url != ""){{$get_experiences->viator_url}}  @endif'>{{$get_experiences->Name}}</a>
                </div>
                <div class="tr-like-review">
                  <span class="tr-likes"><svg width="14" height="13" viewBox="0 0 14 13" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M6.99847 2.82194C5.79052 1.46927 3.7762 1.10541 2.26273 2.34405C0.749264 3.58269 0.536189 5.65364 1.72472 7.11858L6.99847 12.0026L12.2722 7.11858C13.4607 5.65364 13.2737 3.56966 11.7342 2.34405C10.1947 1.11844 8.20641 1.46927 6.99847 2.82194Z"
                        stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>{{ $get_experiences->TAAggregationRating }}%</span>
                  <span class="tr-review">({{ $get_experiences->review_count }} reviews)</span>
                </div>
                <div class="tr-price-section"><span> {{ $get_experiences->Cost }}</span> /adult</div>
              </div>
            </div>

            @endforeach
            @else
            <p>Experience not found.</p>
            @endif

          </div>
        </div>
        <!--TOP WAYS TO EXPERIENCE TOWER BRIDGE SECTION - END-->

	<!--HIGHLIGHTED REVIEWS SECTION - START-->
            <div class="tr-review-section tr-highlighted-reviews tr-tab-section">
              <h3>Tips by Locals</h3>
              <div class="row">
                <div class="col-sm-12">
                  <div class="tr-customer-highlighted-reviews">
                    <div class="tr-customer-review">
                      <div class="tr-recommended-title tr-heart-green"></div>
                      <!--p>@if(!empty($tips_reviews))
    @foreach($tips_reviews as $tip)
            <div class="username">{{ $tip->username }}</div>
            <div class="review-text">{{ $tip->review }}</div>
    @endforeach
@endif</p-->
                    </div>
</div>
                  <div class="tr-show-all-review">
                  <button type="button" class="tr-show-all-review-btn" data-toggle="modal" data-target="#highlight-review-modal">Add Highlight Review</button>
                  </div>
</div>
</div>
</div>
            <!--HIGHLIGHTED REVIEWS SECTION - END-->

        <!--REVIEWS SECTION - START-->
        <div class="tr-review-section tr-tab-section" id="reviewTab">
          <div class="row">
            <h3>Reviews</h3>

            <div class="col-lg-7">
            @if($searchresult[0]->ReviewSummary !="" && $searchresult[0]->photoCount !="" && $searchresult[0]->photoCount !=0)
              <div class="tr-review-content mb-3">
                <div class="tr-power-by">
                  <ul>
                    <li><img  src="{{asset('/frontend/hotel-detail/images/ellipse-1.png')}}"
                        alt="ellipse"></li>
                    <li><img  src="{{asset('/frontend/hotel-detail/images/ellipse-2.png')}}"
                        alt="ellipse"></li>
                    <li><img  src="{{asset('/frontend/hotel-detail/images/ellipse-3.png')}}"
                        alt="ellipse"></li>
                    <li><img  src="{{asset('/frontend/hotel-detail/images/ellipse-4.png')}}"
                        alt="ellipse"></li>
                  </ul>
                  <span>Powered by AI</span>
                </div>
                <h4>Reviews summary</h4>
                <div class="tr-short-decs">This summary was created by AI, based on recent reviews.</div>
                   @if($searchresult[0]->ReviewSummaryLabel !="")
                <ul class="tr-revies-recomm">

                <?php $reviewSummaryLabel = $searchresult[0]->ReviewSummaryLabel;
                       $expreviewSummaryLabel = explode(',',$reviewSummaryLabel);
                       $t =1;
                ?>
            @foreach($expreviewSummaryLabel as $sumval)
             @if( $t ==1)
                  <li>
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M12 13.8086C11.2583 13.8086 10.5333 13.5887 9.91661 13.1766C9.29993 12.7646 8.81928 12.1789 8.53545 11.4937C8.25162 10.8084 8.17736 10.0544 8.32206 9.32701C8.46675 8.59958 8.8239 7.93139 9.34835 7.40695C9.8728 6.8825 10.541 6.52535 11.2684 6.38065C11.9958 6.23596 12.7498 6.31022 13.4351 6.59405C14.1203 6.87788 14.706 7.35852 15.118 7.97521C15.5301 8.59189 15.75 9.31692 15.75 10.0586C15.7488 11.0528 15.3533 12.0059 14.6503 12.7089C13.9473 13.4119 12.9942 13.8074 12 13.8086ZM12 7.8086C11.555 7.8086 11.12 7.94056 10.75 8.18779C10.38 8.43502 10.0916 8.78642 9.92127 9.19756C9.75098 9.60869 9.70642 10.0611 9.79324 10.4975C9.88005 10.934 10.0943 11.3349 10.409 11.6496C10.7237 11.9643 11.1246 12.1785 11.561 12.2654C11.9975 12.3522 12.4499 12.3076 12.861 12.1373C13.2722 11.967 13.6236 11.6786 13.8708 11.3086C14.118 10.9386 14.25 10.5036 14.25 10.0586C14.2494 9.46204 14.0122 8.89009 13.5903 8.46826C13.1685 8.04644 12.5966 7.80919 12 7.8086Z"
                        fill="#222222" />
                      <path
                        d="M12 22.8086L5.67301 15.3468C5.58509 15.2348 5.49809 15.1221 5.41201 15.0086C4.33179 13.5846 3.74799 11.8459 3.75001 10.0586C3.75001 7.87056 4.6192 5.77214 6.16637 4.22496C7.71355 2.67779 9.81197 1.80859 12 1.80859C14.188 1.80859 16.2865 2.67779 17.8336 4.22496C19.3808 5.77214 20.25 7.87056 20.25 10.0586C20.2517 11.8451 19.6682 13.5829 18.5888 15.0063L18.588 15.0086C18.588 15.0086 18.363 15.3041 18.3293 15.3438L12 22.8086ZM6.60976 14.1048C6.60976 14.1048 6.78451 14.3358 6.82426 14.3853L12 20.4896L17.1825 14.3771C17.2155 14.3358 17.391 14.1033 17.3918 14.1026C18.2747 12.9395 18.7518 11.5189 18.75 10.0586C18.75 8.26838 18.0388 6.55149 16.773 5.28562C15.5071 4.01975 13.7902 3.30859 12 3.30859C10.2098 3.30859 8.49291 4.01975 7.22703 5.28562C5.96116 6.55149 5.25001 8.26838 5.25001 10.0586C5.24815 11.5198 5.72584 12.9413 6.60976 14.1048Z"
                        fill="#222222" />
                    </svg>
                    <div>Location</div>
                    <div>{{$sumval}}</div>
                  </li>
                  @elseif($t ==2)
                  <li>
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M5.03906 19.9805C4.97656 19.9805 4.91016 19.9692 4.83984 19.9467C4.76953 19.9242 4.71094 19.883 4.66406 19.823C4.60156 19.763 4.55859 19.703 4.53516 19.643C4.51172 19.583 4.5 19.523 4.5 19.463C4.5 19.388 4.51172 19.3205 4.53516 19.2605C4.55859 19.2005 4.60156 19.1405 4.66406 19.0805C4.71094 19.0355 4.76953 18.998 4.83984 18.968C4.91016 18.938 4.97656 18.923 5.03906 18.923H6.44531V5.44547C6.44531 5.34047 6.46875 5.23922 6.51562 5.14172C6.5625 5.04422 6.625 4.95797 6.70312 4.88297C6.78125 4.79297 6.87109 4.72922 6.97266 4.69172C7.07422 4.65422 7.17969 4.63547 7.28906 4.63547H13.6406C13.7656 4.63547 13.8789 4.65047 13.9805 4.68047C14.082 4.71047 14.1719 4.76297 14.25 4.83797C14.3281 4.91297 14.3906 4.99547 14.4375 5.08547C14.4844 5.17547 14.5078 5.27297 14.5078 5.37797V5.55797H16.7109C16.8047 5.55797 16.9023 5.58047 17.0039 5.62547C17.1055 5.67047 17.2031 5.73047 17.2969 5.80547C17.375 5.89547 17.4375 5.98922 17.4844 6.08672C17.5312 6.18422 17.5547 6.28547 17.5547 6.39047V18.923H18.9609C19.0234 18.923 19.0898 18.938 19.1602 18.968C19.2305 18.998 19.2891 19.043 19.3359 19.103C19.3984 19.163 19.4414 19.223 19.4648 19.283C19.4883 19.343 19.5 19.403 19.5 19.463C19.5 19.538 19.4883 19.6055 19.4648 19.6655C19.4414 19.7255 19.3984 19.778 19.3359 19.823C19.2891 19.883 19.2305 19.9242 19.1602 19.9467C19.0898 19.9692 19.0234 19.9805 18.9609 19.9805H17.2969C17.1875 19.9805 17.082 19.9617 16.9805 19.9242C16.8789 19.8867 16.7891 19.823 16.7109 19.733C16.6172 19.658 16.5508 19.5717 16.5117 19.4742C16.4727 19.3767 16.4531 19.2755 16.4531 19.1705V6.61547H14.5078V19.1705C14.5078 19.2755 14.4844 19.3767 14.4375 19.4742C14.3906 19.5717 14.3203 19.658 14.2266 19.733C14.1484 19.823 14.0586 19.8867 13.957 19.9242C13.8555 19.9617 13.75 19.9805 13.6406 19.9805H5.03906ZM12.3047 12.308C12.3047 12.203 12.2852 12.1055 12.2461 12.0155C12.207 11.9255 12.1484 11.843 12.0703 11.768C11.9922 11.693 11.9062 11.6367 11.8125 11.5992C11.7188 11.5617 11.6172 11.543 11.5078 11.543C11.3984 11.543 11.2969 11.5617 11.2031 11.5992C11.1094 11.6367 11.0234 11.693 10.9453 11.768C10.8672 11.843 10.8086 11.9255 10.7695 12.0155C10.7305 12.1055 10.7109 12.203 10.7109 12.308C10.7109 12.413 10.7305 12.5105 10.7695 12.6005C10.8086 12.6905 10.8672 12.773 10.9453 12.848C11.0234 12.923 11.1094 12.9792 11.2031 13.0167C11.2969 13.0542 11.3984 13.073 11.5078 13.073C11.6172 13.073 11.7188 13.0542 11.8125 13.0167C11.9062 12.9792 11.9922 12.923 12.0703 12.848C12.1484 12.773 12.207 12.6905 12.2461 12.6005C12.2852 12.5105 12.3047 12.413 12.3047 12.308ZM7.54688 18.923H13.4062V5.69297H7.54688V18.923Z"
                        fill="black" />
                    </svg>
                    <div>Rooms</div>
                    <div>{{$sumval}}</div>
                  </li>
                  @elseif($t ==3)
                  <li>
                    <svg width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M19.5 7.05859H4.5C3.25736 7.05859 2.25 8.06595 2.25 9.30859V18.3086C2.25 19.5512 3.25736 20.5586 4.5 20.5586H19.5C20.7426 20.5586 21.75 19.5512 21.75 18.3086V9.30859C21.75 8.06595 20.7426 7.05859 19.5 7.05859Z"
                        stroke="#222222" stroke-width="1.5" stroke-linejoin="round" />
                      <path
                        d="M19.2825 7.05784V5.65159C19.2824 5.30669 19.2062 4.96606 19.0592 4.65401C18.9123 4.34196 18.6984 4.06618 18.4326 3.84635C18.1668 3.62652 17.8558 3.46805 17.5217 3.38226C17.1877 3.29647 16.8388 3.28546 16.5 3.35003L4.155 5.45706C3.6189 5.55922 3.13526 5.84527 2.78749 6.26586C2.43972 6.68645 2.24963 7.21522 2.25 7.76096V10.0578"
                        stroke="#222222" stroke-width="1.5" stroke-linejoin="round" />
                      <path
                        d="M17.25 15.3086C16.9533 15.3086 16.6633 15.2206 16.4166 15.0558C16.17 14.891 15.9777 14.6567 15.8642 14.3826C15.7506 14.1085 15.7209 13.8069 15.7788 13.516C15.8367 13.225 15.9796 12.9577 16.1893 12.7479C16.3991 12.5382 16.6664 12.3953 16.9574 12.3374C17.2483 12.2795 17.5499 12.3092 17.824 12.4228C18.0981 12.5363 18.3324 12.7286 18.4972 12.9752C18.662 13.2219 18.75 13.5119 18.75 13.8086C18.75 14.2064 18.592 14.5879 18.3107 14.8693C18.0294 15.1506 17.6478 15.3086 17.25 15.3086Z"
                        fill="black" />
                    </svg>
                    <div>Value</div>
                    <div>{{$sumval}}</div>
                  </li>
                 @endif
                <?php $t++ ;?>

                @endforeach
                </ul>
                @endif
                <p>{{$searchresult[0]->ReviewSummary}}</p>
                @if(!$review->isEmpty())   <a href="javascript:void(0);" class="tr-jump-to-all-review">Jump to all reviews</a>@endif
                <div class="tr-helpful">
                  Was this helpful?
                  <button class="tr-like-button">Like</button>
                  <button class="tr-dislike-button">Dislike</button>
                </div>
              </div>
              @endif
              @if(!$review->isEmpty())
              <div class="tr-reviews-graph">
                <div class="tr-reviews-graph-details">
                  <h4 class="d-none d-md-block">Customers Reviews</h4>
                  <div class="tr-likes-count"><svg width="17" height="15" viewBox="0 0 17 15" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M8.24504 2.98321C6.7955 1.36 4.37832 0.923362 2.56215 2.40973C0.745992 3.8961 0.490302 6.38124 1.91654 8.13917L8.24504 14L14.5735 8.13917C15.9998 6.38124 15.7753 3.88047 13.9279 2.40973C12.0805 0.938998 9.69457 1.36 8.24504 2.98321Z"
                        stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>{{ number_format($reviewStats['ratingPercentage'], 0) }}%</div>
                  <span class="tr-vgood">{{ $reviewStats['ratingText'] }}</span>
                  <div class="tr-reviews-count">({{ $reviewStats['totalReviews'] }} {{ $reviewStats['totalReviews'] == 1 ? 'Review' : 'Reviews' }})</div>
                </div>
                <div class="tr-rating-types">
                  <div class="tr-rating-type">
                    <div class="tr-title">Cleanliness</div>
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" 
                        aria-valuenow="{{ $reviewStats['avgCleanliness'] * 20 }}" 
                        aria-valuemin="0"
                        aria-valuemax="100" 
                        style="width:{{ $reviewStats['avgCleanliness'] * 20 }}%">
                      </div>
                    </div>
                    <div class="tr-reviews-nums">{{ number_format($reviewStats['avgCleanliness'], 1) }}</div>
                  </div>
                  <div class="tr-rating-type">
                    <div class="tr-title">Location</div>
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" 
                        aria-valuenow="{{ $reviewStats['avgLocation'] * 20 }}" 
                        aria-valuemin="0"
                        aria-valuemax="100" 
                        style="width:{{ $reviewStats['avgLocation'] * 20 }}%">
                      </div>
                    </div>
                    <div class="tr-reviews-nums">{{ number_format($reviewStats['avgLocation'], 1) }}</div>
                  </div>
                  <div class="tr-rating-type">
                    <div class="tr-title">Service</div>
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" 
                        aria-valuenow="{{ $reviewStats['avgService'] * 20 }}" 
                        aria-valuemin="0"
                        aria-valuemax="100" 
                        style="width:{{ $reviewStats['avgService'] * 20 }}%">
                      </div>
                    </div>
                    <div class="tr-reviews-nums">{{ number_format($reviewStats['avgService'], 1) }}</div>
                  </div>
                  <div class="tr-rating-type">
                    <div class="tr-title">Value</div>
                    <div class="progress">
                      <div class="progress-bar" role="progressbar" 
                        aria-valuenow="{{ $reviewStats['avgValue'] * 20 }}" 
                        aria-valuemin="0"
                        aria-valuemax="100" 
                        style="width:{{ $reviewStats['avgValue'] * 20 }}%">
                      </div>
                    </div>
                    <div class="tr-reviews-nums">{{ number_format($reviewStats['avgValue'], 1) }}</div>
                  </div>
                </div>
              </div>

              <div class="tr-reviews-mentioned">
                <?php
                // return  print_r($reviews);
                ?>
                <h4>Reviews that mentioned</h4>
                <ul>
                  @foreach($reviewStats['topMentions'] as $category => $count)
                    <li><a href="javascript:void(0);" class="{{ $loop->first ? 'active' : '' }}">
                      {{ $category }} ({{ $count }})
                    </a></li>
                  @endforeach
                </ul>
                <div class="tr-shotby">
                  <div class="custom-select">
                    <label>Sort by:</label>
                    <select>
                      <option>Oldest</option>
                      <option>Newest</option>
                      <option>High rated</option>
                      <option>Low rated</option>
                      <option>Cleanliness</option>
                      <option>Location</option>
                      <option>Service</option>
                      <option>Value</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="tr-customer-reviews" id="getreview">
                @if(!$review->isEmpty())
                @foreach($review as $reviews)
                <div class="tr-customer-review">
                  <div class="tr-customer-details">
                    <div class="tr-customer-detail">
                      <img  src="{{asset('/frontend/hotel-detail/images/customer.png')}}"
                        alt="customer">
                      <div class="tr-customer-name">{{$reviews->Name}}</div>
                      <div class="tr-hotel-place">London</div>
                    </div>
                    <div class="tr-report">
                      <div class="tr-report-icon"></div>
                      <div class="tr-report-popup">
                        <h5>Report this</h5>
                        <div class="tr-follow">Follow</div>
                      </div>
                    </div>
                  </div>
                  <div class="tr-ratings">
                    <span>
                      @for ($i = 0; $i < 5; $i++) @if($i < $reviews->Rating )
                        <span><i class="fa fa-star" aria-hidden="true"></i></span>
                        @else
                        <span><i class="fa fa-star-o" aria-hidden="true"></i></span>
                        @endif
                        @endfor
                    </span>
                    <span class="tr-time-ago">1 week ago</span>
                  </div>
                  <ul>
                    <li><svg width="20" height="22" viewBox="0 0 20 22" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M4.19922 17.5532C4.14714 17.5532 4.0918 17.5435 4.0332 17.5239C3.97461 17.5044 3.92578 17.4686 3.88672 17.4165C3.83464 17.3644 3.79883 17.3123 3.7793 17.2603C3.75977 17.2082 3.75 17.1561 3.75 17.104C3.75 17.0389 3.75977 16.9803 3.7793 16.9282C3.79883 16.8761 3.83464 16.8241 3.88672 16.772C3.92578 16.7329 3.97461 16.7004 4.0332 16.6743C4.0918 16.6483 4.14714 16.6353 4.19922 16.6353H5.37109V4.93604C5.37109 4.84489 5.39062 4.757 5.42969 4.67236C5.46875 4.58773 5.52083 4.51286 5.58594 4.44775C5.65104 4.36963 5.72591 4.31429 5.81055 4.28174C5.89518 4.24919 5.98307 4.23291 6.07422 4.23291H11.3672C11.4714 4.23291 11.5658 4.24593 11.6504 4.27197C11.735 4.29801 11.8099 4.34359 11.875 4.40869C11.9401 4.4738 11.9922 4.54541 12.0312 4.62354C12.0703 4.70166 12.0898 4.7863 12.0898 4.87744V5.03369H13.9258C14.0039 5.03369 14.0853 5.05322 14.1699 5.09229C14.2546 5.13135 14.3359 5.18343 14.4141 5.24854C14.4792 5.32666 14.5312 5.40804 14.5703 5.49268C14.6094 5.57731 14.6289 5.6652 14.6289 5.75635V16.6353H15.8008C15.8529 16.6353 15.9082 16.6483 15.9668 16.6743C16.0254 16.7004 16.0742 16.7394 16.1133 16.7915C16.1654 16.8436 16.2012 16.8957 16.2207 16.9478C16.2402 16.9998 16.25 17.0519 16.25 17.104C16.25 17.1691 16.2402 17.2277 16.2207 17.2798C16.2012 17.3319 16.1654 17.3774 16.1133 17.4165C16.0742 17.4686 16.0254 17.5044 15.9668 17.5239C15.9082 17.5435 15.8529 17.5532 15.8008 17.5532H14.4141C14.3229 17.5532 14.235 17.5369 14.1504 17.5044C14.0658 17.4718 13.9909 17.4165 13.9258 17.3384C13.8477 17.2733 13.7923 17.1984 13.7598 17.1138C13.7272 17.0291 13.7109 16.9412 13.7109 16.8501V5.95166H12.0898V16.8501C12.0898 16.9412 12.0703 17.0291 12.0312 17.1138C11.9922 17.1984 11.9336 17.2733 11.8555 17.3384C11.7904 17.4165 11.7155 17.4718 11.6309 17.5044C11.5462 17.5369 11.4583 17.5532 11.3672 17.5532H4.19922ZM10.2539 10.8931C10.2539 10.8019 10.2376 10.7173 10.2051 10.6392C10.1725 10.561 10.1237 10.4894 10.0586 10.4243C9.99349 10.3592 9.92188 10.3104 9.84375 10.2778C9.76562 10.2453 9.68099 10.229 9.58984 10.229C9.4987 10.229 9.41406 10.2453 9.33594 10.2778C9.25781 10.3104 9.1862 10.3592 9.12109 10.4243C9.05599 10.4894 9.00716 10.561 8.97461 10.6392C8.94206 10.7173 8.92578 10.8019 8.92578 10.8931C8.92578 10.9842 8.94206 11.0688 8.97461 11.147C9.00716 11.2251 9.05599 11.2967 9.12109 11.3618C9.1862 11.4269 9.25781 11.4757 9.33594 11.5083C9.41406 11.5409 9.4987 11.5571 9.58984 11.5571C9.68099 11.5571 9.76562 11.5409 9.84375 11.5083C9.92188 11.4757 9.99349 11.4269 10.0586 11.3618C10.1237 11.2967 10.1725 11.2251 10.2051 11.147C10.2376 11.0688 10.2539 10.9842 10.2539 10.8931ZM6.28906 16.6353H11.1719V5.15088H6.28906V16.6353Z"
                          fill="black"></path>
                      </svg>Deluxe room</li>
                    <li><svg width="25" height="25" viewBox="0 0 25 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M5.16406 9.47949C5.16406 8.37492 6.05949 7.47949 7.16406 7.47949H17.6641C18.7686 7.47949 19.6641 8.37492 19.6641 9.47949V17.9795C19.6641 19.0841 18.7686 19.9795 17.6641 19.9795H7.16406C6.05949 19.9795 5.16406 19.0841 5.16406 17.9795V9.47949Z"
                          stroke="#222222" stroke-width="1.28571" stroke-linecap="round" stroke-linejoin="round">
                        </path>
                        <path d="M8.41406 5.48145V8.98144" stroke="#222222" stroke-width="1.28571"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M16.4141 5.48145V8.98144" stroke="#222222" stroke-width="1.28571"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M8.16406 11.4814H16.6641" stroke="#222222" stroke-width="1.28571"
                          stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>1 night. {{ date('F j', strtotime($reviews->CreatedOn)) }}</li>
                    <li><svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M16.6615 17.8091V16.1424C16.6615 15.2584 16.3103 14.4105 15.6851 13.7854C15.06 13.1603 14.2122 12.8091 13.3281 12.8091H6.66146C5.7774 12.8091 4.92956 13.1603 4.30444 13.7854C3.67931 14.4105 3.32813 15.2584 3.32812 16.1424V17.8091"
                          stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path
                          d="M10.0052 9.47575C11.8462 9.47575 13.3385 7.98336 13.3385 6.14242C13.3385 4.30147 11.8462 2.80908 10.0052 2.80908C8.16426 2.80908 6.67188 4.30147 6.67188 6.14242C6.67188 7.98336 8.16426 9.47575 10.0052 9.47575Z"
                          stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>{{$reviews->go_with}} Traveller</li>
                  </ul>
                  <p>{{ $reviews->Description }}</p>
                  <a href="javascript:void(0);" class="tr-read-more">Read more</a>
                 <!-- <div class="tr-hotel-response">
                    <h5><svg width="20" height="21" viewBox="0 0 20 21" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <path
                          d="M17.5 9.89195C17.5029 10.9918 17.2459 12.0769 16.75 13.0586C16.162 14.2351 15.2581 15.2246 14.1395 15.9164C13.021 16.6081 11.7319 16.9748 10.4167 16.9753C9.31678 16.9782 8.23176 16.7212 7.25 16.2253L2.5 17.8086L4.08333 13.0586C3.58744 12.0769 3.33047 10.9918 3.33333 9.89195C3.33384 8.57675 3.70051 7.28766 4.39227 6.16908C5.08402 5.05049 6.07355 4.14659 7.25 3.55862C8.23176 3.06273 9.31678 2.80575 10.4167 2.80862H10.8333C12.5703 2.90444 14.2109 3.63759 15.4409 4.86767C16.671 6.09775 17.4042 7.73833 17.5 9.47528V9.89195Z"
                          stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      </svg>Hotel response :</h5>
                    <div class="tr-customer-msg">Dear guest,</div>
                    <div class="tr-customer-msg">Thank you for taking the time to share your...</div>
                    <a href="javascript:void(0)">Continue reading</a>
                  </div> -->
                  <div class="tr-helpful">
                    Was this helpful?
                    <button class="tr-like-button">Like</button>
                    <button class="tr-dislike-button">Dislike</button>
                  </div>
                </div>
                @endforeach
                @else
                review not found.
                @endif
              </div>
              @if(!$review->isEmpty())
                         <!-- <div class="tr-pagination">
                <nav aria-label="Page navigation example">
                  <ul class="pagination">
                    <li class="page-item">
                      <a class="page-link" href="#" aria-label="Last Previous">
                        <span aria-hidden="true"><img  src="images/left-double-arrow.png"
                            alt="Left Arrow"></span>
                      </a>
                    </li>
                    <li class="page-item">
                      <a class="page-link" href="#" aria-label="Previous">
                        <span aria-hidden="true"><img  src="images/arrow-left.png"
                            alt="Left Arrow"></span>
                      </a>
                    </li>
                    <li class="page-item"><a class="page-link active" href="javascript:void(0);">1</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">2</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">3</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">4</a></li>
                    <li class="page-item"><a class="page-link" href="javascript:void(0);">...</a></li>
                    <li class="page-item">
                      <a class="page-link" href="#" aria-label="Next">
                        <span aria-hidden="true"><img  src="images/arrow-right.png"
                            alt="Left Arrow"></span>
                      </a>
                    </li>
                    <li class="page-item">
                      <a class="page-link" href="#" aria-label="Last Next">
                        <span aria-hidden="true"><img  src="images/right-double-arrow.png"
                            alt="Left Arrow"></span>
                      </a>
                    </li>
                  </ul>
                </nav>
              </div> -->
              @endif
              <div class="tr-show-all d-block d-md-none">
                <a href="javascript:void(0);">Show all reviews</a>
              </div>

            @else
            <div class="col-lg-7" id="review-not-foundimg" >
              <img  src="{{asset('/frontend/hotel-detail/images/no-review-found.png')}}"
              alt="Left Arrow" height="height: 200;">Review not found</div>
            @endif
            </div>

            <div class="col-lg-5">
              <div class="tr-enjoyed-the-stay">
                <h5>Enjoyed the stay?</h5>
                <button class="tr-btn tr-write-review">Write a review</button>
                <p>Let others know your experience here.</p>
              </div>
            </div>
          </div>
        </div>
        <!--REVIEWS SECTION - END-->

        <!--FAQS - START-->
        <div class="tr-faqs-section">
          <h3 class="d-none d-md-block">FAQ’s about the {{$searchresult[0]->name}}</h3>
          <h3 class="d-block d-sm-block d-md-none">FAQ’s</h3>
          <span id="detailfaqdata">
            @if(isset($faq) && !empty((array)$faq))
            <div class="tr-faqs-ques-ans">
            </div>
            @else
            <p>No Faq found.</p>
            @endif
          </span>

          <!-- <div class="tr-faqs-ques-ans">
            <div class="tr-faqs-ques">Which facilities are available in the hotel?</div>
            <div class="tr-faqs-ans">Let’s embody your beautiful ideas together, simplify the way you visualize your
              next big things.</div>
          </div> -->

          <button class="tr-ask-a-ques d-block d-sm-block d-md-none">Ask a question</button>
          <div class="tr-popup">
            <div class="tr-popup-content">
              <h3>Ask a question</h3>
              <form>
                <label>Question</label>
                <textarea placeholder="Type your question here"></textarea>
                <button class="tr-popup-btn">Submit</button>
              </form>
            </div>
          </div>
        </div>
        <!--FAQS - END-->

        <!--HOUSE RULES - START-->
        <div class="col-lg-7">
          <div class="tr-house-rules tr-desktop">
            <h3>Property rules</h3>
            <div class="tr-rules-ques-ans">
              <div class="tr-rules-ques">Check-in</div>
              <div class="tr-rules-ans">
                <div class="tr-rules-heading"> @if(!empty($searchresult[0]->checkIn)) {{$searchresult[0]->checkIn}} @else
                  Not Available. @endif</div>
                <p>Guests must present a photo ID at the time of check-in</p>
              </div>
            </div>
            <div class="tr-rules-ques-ans">
              <div class="tr-rules-ques">Check-out</div>
              <div class="tr-rules-ans">
                <div class="tr-rules-heading">@if(!empty($searchresult[0]->checkOut)) {{$searchresult[0]->checkOut}} @else
                  Not Available. @endif</div>
              </div>
            </div>
			@if(!empty($searchresult[0]->CheckIn_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Check-in Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->CheckIn_Policy }}</p>
        </div>
    </div>
@endif

@if(!empty($searchresult[0]->Damage_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Damage Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->Damage_Policy }}</p>
        </div>
    </div>
@endif
            <!--div class="tr-rules-ques-ans">
              <div class="tr-rules-ques">Cancellation/prepayment</div>
              <div class="tr-rules-ans">
                <p>Cancellation and prepayment policies vary according to accomodations type. Check what conditions might
                  apply to each option when making your selection.</p>
              </div>
            </div-->
      @if(!empty($searchresult[0]->Children_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Children Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->Children_Policy }}</p>
        </div>
    </div>
@endif

@if(!empty($searchresult[0]->Beds_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Beds Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->Beds_Policy }}</p>
        </div>
    </div>
@endif
			@if(!empty($searchresult[0]->Payment_Policy))
            <div class="tr-rules-ques-ans">
              <div class="tr-rules-ques">Cards accepted at this hotel</div>
              <div class="tr-rules-ans">
                <p>{{ $searchresult[0]->Payment_Policy }}</p>
              </div>
            </div>
            @endif
           @if(!empty($searchresult[0]->Age_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Age restriction</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->Age_Policy }}</p>
        </div>
    </div>
@endif

            <div class="tr-rules-ques-ans">
              <div class="tr-rules-ques">Pets</div>
              <div class="tr-rules-ans">
                <p>Pets are allowed. Charges may apply.</p>
              </div>
            </div>
            @if($searchresult[0]->Smoking_Policy === 0)
                <div class="tr-rules-ques-ans">
                    <div class="tr-rules-ques">Smoking Policy</div>
                    <div class="tr-rules-ans">
                        <div class="tr-rules-heading">Smoking is prohibited.</div>
                    </div>
                </div>
            @endif
        @if(!empty($searchresult[0]->Curfew_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Curfew Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->Curfew_Policy }}</p>
        </div>
    </div>
@endif

@if(!empty($searchresult[0]->Parties_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Parties Policy</div>
        <div class="tr-rules-ans">
            <p>
                @if($searchresult[0]->Parties_Policy == 1)
                    Parties Allowed
                @else
                    No Parties
                @endif
            </p>
        </div>
    </div>
@endif

@if(!empty($searchresult[0]->QuiteHours_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Quiet Hours Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->QuiteHours_Policy }}</p>
        </div>
    </div>
@endif

@if(!empty($searchresult[0]->Groups_Policy))
    <div class="tr-rules-ques-ans">
        <div class="tr-rules-ques">Groups Policy</div>
        <div class="tr-rules-ans">
            <p>{{ $searchresult[0]->Groups_Policy }}</p>
        </div>
    </div>
@endif

            <div class="tr-rules-ques-ans">
              <div class="tr-rules-ques">Cards accepted at this hotel</div>
              <div class="tr-rules-ans">
                <svg width="224" height="30" viewBox="0 0 224 30" fill="none" xmlns="http://www.w3.org/2000/svg">
                  <rect x="0.307692" y="0.616286" width="42.4615" height="28.9231" rx="3.38461" fill="white"
                    stroke="#D9D9D9" stroke-width="0.615385" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M13.0778 20.3216H10.4682L8.51131 12.8559C8.41843 12.5125 8.22121 12.2089 7.93111 12.0658C7.20714 11.7062 6.40936 11.42 5.53906 11.2757V10.9883H9.74297C10.3232 10.9883 10.7583 11.42 10.8308 11.9215L11.8462 17.3067L14.4545 10.9883H16.9916L13.0778 20.3216ZM18.4333 20.3216H15.9688L17.9982 10.9883H20.4628L18.4333 20.3216ZM23.6628 13.5743C23.7353 13.0716 24.1704 12.7842 24.6781 12.7842C25.4759 12.712 26.3449 12.8564 27.0702 13.2147L27.5053 11.2052C26.7801 10.9178 25.9823 10.7734 25.2583 10.7734C22.8663 10.7734 21.1257 12.0662 21.1257 13.8605C21.1257 15.2255 22.3586 15.9422 23.2289 16.3739C24.1704 16.8045 24.5331 17.0919 24.4605 17.5224C24.4605 18.1682 23.7353 18.4556 23.0113 18.4556C22.141 18.4556 21.2707 18.2404 20.4742 17.8808L20.0391 19.8915C20.9094 20.2499 21.8509 20.3942 22.7212 20.3942C25.4034 20.4651 27.0702 19.1736 27.0702 17.235C27.0702 14.7937 23.6628 14.6506 23.6628 13.5743ZM35.6901 20.3216L33.7332 10.9883H31.6312C31.1961 10.9883 30.7609 11.2757 30.6159 11.7062L26.9922 20.3216H29.5293L30.0357 18.9579H33.153L33.4431 20.3216H35.6901ZM31.9929 13.5L32.7169 17.0176H30.6875L31.9929 13.5Z"
                    fill="#172B85" />
                  <rect x="60.6153" y="0.616286" width="42.4615" height="28.9231" rx="3.38461" fill="white"
                    stroke="#D9D9D9" stroke-width="0.615385" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M82.1436 21.4293C80.6774 22.6936 78.7759 23.4568 76.6983 23.4568C72.0599 23.4568 68.2998 19.653 68.2998 14.9608C68.2998 10.2686 72.0599 6.46484 76.6983 6.46484C78.7759 6.46484 80.6774 7.22803 82.1436 8.49239C83.6098 7.22803 85.5113 6.46484 87.5889 6.46484C92.2273 6.46484 95.9874 10.2686 95.9874 14.9608C95.9874 19.653 92.2273 23.4568 87.5889 23.4568C85.5113 23.4568 83.6098 22.6936 82.1436 21.4293Z"
                    fill="#ED0006" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M82.1592 21.4293C83.9663 19.8709 85.1124 17.5513 85.1124 14.9608C85.1124 12.3704 83.9663 10.0507 82.1592 8.49239C83.6254 7.22803 85.5268 6.46484 87.6045 6.46484C92.2428 6.46484 96.003 10.2686 96.003 14.9608C96.003 19.653 92.2428 23.4568 87.6045 23.4568C85.5268 23.4568 83.6254 22.6936 82.1592 21.4293Z"
                    fill="#F9A000" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M82.1436 21.4293C83.9507 19.871 85.0968 17.5513 85.0968 14.9609C85.0968 12.3704 83.9507 10.0508 82.1436 8.49243C80.3365 10.0508 79.1904 12.3704 79.1904 14.9609C79.1904 17.5513 80.3365 19.871 82.1436 21.4293Z"
                    fill="#FF5E00" />
                  <rect x="120.923" y="0.616286" width="42.4615" height="28.9231" rx="3.38461" fill="#1F72CD"
                    stroke="#D9D9D9" stroke-width="0.615385" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M128.349 10.7734L124.318 19.6925H129.143L129.741 18.2704H131.109L131.707 19.6925H137.018V18.6071L137.491 19.6925H140.238L140.711 18.5842V19.6925H151.757L153.1 18.3074L154.357 19.6925L160.03 19.704L155.987 15.2579L160.03 10.7734H154.445L153.138 12.133L151.92 10.7734H139.904L138.872 13.0755L137.816 10.7734H133.001V11.8219L132.466 10.7734H128.349ZM144.842 12.043H151.184L153.124 14.1384L155.127 12.043H157.067L154.119 15.2596L157.067 18.4392H155.039L153.099 16.3194L151.086 18.4392H144.842V12.043ZM146.404 14.5327V13.3644V13.3633H150.362L152.089 15.2317L150.285 17.1104H146.404V15.8349H149.864V14.5327H146.404ZM129.278 12.043H131.629L134.303 18.091V12.043H136.879L138.944 16.3794L140.847 12.043H143.411V18.443H141.851L141.838 13.428L139.564 18.443H138.168L135.882 13.428V18.443H132.673L132.064 17.0082H128.778L128.17 18.4417H126.451L129.278 12.043ZM129.342 15.681L130.425 13.125L131.506 15.681H129.342Z"
                    fill="white" />
                  <rect x="181.231" y="0.616286" width="42.4615" height="28.9231" rx="3.38461" fill="white"
                    stroke="#D9D9D9" stroke-width="0.615385" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M193.111 19.0664H191.196C191.065 19.0664 190.954 19.1616 190.934 19.2909L190.159 24.2005C190.144 24.2974 190.219 24.3848 190.317 24.3848H191.231C191.362 24.3848 191.474 24.2895 191.494 24.16L191.703 22.8358C191.723 22.7061 191.835 22.611 191.966 22.611H192.572C193.833 22.611 194.561 22.0007 194.751 20.7913C194.836 20.2622 194.754 19.8465 194.507 19.5552C194.235 19.2356 193.752 19.0664 193.111 19.0664ZM193.332 20.8596C193.227 21.5466 192.702 21.5466 192.195 21.5466H191.906L192.109 20.2635C192.121 20.186 192.188 20.1289 192.266 20.1289H192.399C192.744 20.1289 193.071 20.1289 193.239 20.3259C193.339 20.4436 193.37 20.6182 193.332 20.8596ZM198.834 20.8376H197.917C197.839 20.8376 197.772 20.8947 197.76 20.9723L197.719 21.2287L197.655 21.1358C197.457 20.8477 197.014 20.7514 196.572 20.7514C195.559 20.7514 194.694 21.5188 194.525 22.5952C194.438 23.1321 194.562 23.6455 194.867 24.0036C195.146 24.3328 195.546 24.47 196.022 24.47C196.838 24.47 197.291 23.9451 197.291 23.9451L197.25 24.1999C197.234 24.2973 197.309 24.3846 197.407 24.3846H198.233C198.364 24.3846 198.475 24.2894 198.496 24.1599L198.991 21.0218C199.007 20.9253 198.932 20.8376 198.834 20.8376ZM197.556 22.622C197.468 23.1458 197.052 23.4974 196.522 23.4974C196.256 23.4974 196.043 23.4121 195.906 23.2503C195.771 23.0896 195.719 22.8608 195.762 22.6061C195.845 22.0867 196.268 21.7237 196.79 21.7237C197.05 21.7237 197.262 21.8101 197.401 21.9734C197.541 22.1383 197.596 22.3684 197.556 22.622ZM202.796 20.8374H203.717C203.846 20.8374 203.922 20.9821 203.848 21.088L200.784 25.5112C200.734 25.5829 200.653 25.6255 200.565 25.6255H199.645C199.516 25.6255 199.44 25.4796 199.515 25.3735L200.469 24.0267L199.454 21.0485C199.419 20.945 199.496 20.8374 199.606 20.8374H200.511C200.629 20.8374 200.732 20.9146 200.766 21.0272L201.305 22.826L202.576 20.9542C202.625 20.8811 202.708 20.8374 202.796 20.8374Z"
                    fill="#253B80" />
                  <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M212.785 24.2008L213.571 19.2014C213.583 19.1238 213.65 19.0667 213.728 19.0664H214.613C214.711 19.0664 214.786 19.154 214.77 19.2509L213.995 24.1602C213.975 24.2898 213.864 24.385 213.732 24.385H212.942C212.845 24.385 212.77 24.2977 212.785 24.2008ZM206.767 19.0667H204.852C204.722 19.0667 204.61 19.1619 204.59 19.2912L203.815 24.2008C203.8 24.2977 203.875 24.385 203.973 24.385H204.955C205.047 24.385 205.125 24.3184 205.139 24.2277L205.359 22.8361C205.379 22.7064 205.491 22.6113 205.621 22.6113H206.227C207.489 22.6113 208.216 22.001 208.407 20.7916C208.493 20.2625 208.41 19.8467 208.162 19.5555C207.891 19.2359 207.408 19.0667 206.767 19.0667ZM206.988 20.8598C206.884 21.5469 206.359 21.5469 205.851 21.5469H205.562L205.765 20.2638C205.777 20.1863 205.844 20.1291 205.923 20.1291H206.055C206.401 20.1291 206.727 20.1291 206.896 20.3262C206.996 20.4438 207.027 20.6185 206.988 20.8598ZM212.49 20.8378H211.574C211.495 20.8378 211.428 20.8949 211.416 20.9725L211.376 21.2289L211.312 21.136C211.113 20.8479 210.671 20.7517 210.229 20.7517C209.216 20.7517 208.351 21.519 208.182 22.5955C208.095 23.1324 208.219 23.6458 208.523 24.0039C208.803 24.3331 209.203 24.4703 209.678 24.4703C210.495 24.4703 210.947 23.9453 210.947 23.9453L210.906 24.2002C210.891 24.2976 210.966 24.3849 211.064 24.3849H211.89C212.021 24.3849 212.132 24.2897 212.152 24.1601L212.648 21.022C212.663 20.9255 212.588 20.8378 212.49 20.8378ZM211.212 22.6223C211.124 23.1461 210.708 23.4977 210.178 23.4977C209.912 23.4977 209.699 23.4123 209.562 23.2505C209.427 23.0899 209.376 22.8611 209.418 22.6064C209.501 22.087 209.923 21.724 210.446 21.724C210.706 21.724 210.918 21.8104 211.057 21.9736C211.197 22.1385 211.253 22.3686 211.212 22.6223Z"
                    fill="#179BD7" />
                  <path
                    d="M200.2 17.3126L200.435 15.8175L199.911 15.8054H197.407L199.147 4.77296C199.152 4.73965 199.17 4.7086 199.196 4.68655C199.221 4.6645 199.254 4.65234 199.288 4.65234H203.51C204.912 4.65234 205.879 4.94398 206.384 5.51961C206.62 5.78965 206.771 6.07184 206.844 6.38238C206.921 6.70824 206.922 7.09754 206.847 7.57236L206.842 7.60701V7.91125L207.079 8.04537C207.278 8.15113 207.436 8.2722 207.558 8.41082C207.76 8.6417 207.891 8.93514 207.947 9.28304C208.004 9.64084 207.985 10.0666 207.891 10.5486C207.783 11.1031 207.609 11.586 207.373 11.9812C207.156 12.3453 206.88 12.6472 206.552 12.8813C206.238 13.1036 205.866 13.2724 205.445 13.3805C205.037 13.4866 204.573 13.5402 204.063 13.5402H203.734C203.499 13.5402 203.271 13.6248 203.092 13.7765C202.912 13.9314 202.793 14.1428 202.757 14.3742L202.732 14.5088L202.316 17.1438L202.298 17.2406C202.293 17.2713 202.284 17.2866 202.271 17.2969C202.26 17.3063 202.244 17.3126 202.228 17.3126H200.2Z"
                    fill="#253B80" />
                  <path
                    d="M207.3 7.64453C207.287 7.72509 207.273 7.80745 207.257 7.89206C206.7 10.7504 204.795 11.7379 202.363 11.7379H201.124C200.827 11.7379 200.576 11.9539 200.53 12.2473L199.895 16.2691L199.716 17.4091C199.686 17.6017 199.834 17.7755 200.029 17.7755H202.225C202.486 17.7755 202.706 17.5864 202.747 17.3299L202.769 17.2183L203.183 14.5935L203.209 14.4495C203.25 14.1921 203.471 14.0031 203.731 14.0031H204.06C206.188 14.0031 207.854 13.139 208.341 10.6384C208.545 9.59375 208.439 8.72153 207.901 8.10809C207.738 7.92311 207.536 7.76964 207.3 7.64453Z"
                    fill="#179BD7" />
                  <path
                    d="M206.72 7.41076C206.635 7.38601 206.547 7.3635 206.457 7.34325C206.367 7.32346 206.274 7.3059 206.179 7.2906C205.845 7.23659 205.479 7.21094 205.087 7.21094H201.778C201.697 7.21094 201.619 7.22939 201.55 7.26269C201.397 7.33605 201.284 7.48053 201.256 7.6574L200.552 12.1157L200.532 12.2458C200.579 11.9523 200.829 11.7363 201.127 11.7363H202.365C204.798 11.7363 206.703 10.7484 207.259 7.89053C207.276 7.80592 207.29 7.72356 207.303 7.643C207.162 7.56829 207.009 7.50438 206.845 7.44991C206.804 7.43642 206.763 7.42337 206.72 7.41076Z"
                    fill="#222D65" />
                  <path
                    d="M201.25 7.65644C201.277 7.47956 201.391 7.3351 201.543 7.26218C201.613 7.22887 201.69 7.21043 201.771 7.21043H205.08C205.472 7.21043 205.838 7.23608 206.172 7.29009C206.267 7.30539 206.36 7.32294 206.451 7.34274C206.541 7.363 206.628 7.38551 206.714 7.41025C206.756 7.42286 206.798 7.4359 206.839 7.44896C207.003 7.50341 207.155 7.56778 207.296 7.64204C207.462 6.58574 207.295 5.86654 206.724 5.21529C206.094 4.49835 204.958 4.19141 203.504 4.19141H199.282C198.985 4.19141 198.732 4.40743 198.686 4.70132L196.927 15.8471C196.893 16.0676 197.063 16.2666 197.285 16.2666H199.891L200.546 12.1148L201.25 7.65644Z"
                    fill="#253B80" />
                </svg>
              </div>
            </div>
          </div>
          <div class="tr-house-rules tr-mobile d-block d-sm-block d-md-none">
            <h3>House rules</h3>
            <div>Check-in: 6:00 PM - 11:00 PM</div>
            <div>Checkout before 9:00 AM</div>
            <div>2 guests maximum</div>
            <button class="tr-show-more">Show more</button>
          </div>
        </div>
        <!--HOUSE RULES - END-->

        <!--THE FINE PRINT - START -->
        <!-- static content 4 -->
        <!--
        <div class="col-lg-7">
          <div class="tr-fine-print">
            <h3>The fine print</h3>
            <div class="tr-sub-title">Must-know information for guest at this property</div>
            <p>When booking 8 rooms or more, different policies and additional supplements may apply.</p>
            <p>Guests are required to show a photo ID and credit card upon check-in. Please note that all special requests
              are subject to availability and additional charges may apply.</p>
            <p>Please inform the cloud one new york-downtown, byy the motel one group of your expected arrival time in
              advance, You can use the special requests box when booking, or contact the property directly using the
              contact details in your confirmation.</p>
          </div>
        </div>
        -->
        <!--THE FINE PRINT - END -->

        <!--WHERE TO STAY - START-->
        <!-- static content 4 -->
        <div class="d-none d-md-block" id="where-to-stay">

        </div>
        <!--WHERE TO STAY - END-->
  
        <!--NEARBY HOTEL - START-->
        <div class="tr-nearby-hotel">
          <h3 class="d-none d-md-block">Nearby hotel</h3>
          <h3 class="d-block d-sm-block d-md-none">You might also like</h3>
          <div class="row tr-nearby-hotel-lists" id="sim-hotel">

            @if(!$nearby_hotel->isEmpty())
            @foreach($nearby_hotel as $index =>$nearbhotel)
            @if($nearbhotel->id != $searchresult[0]->id)
            <div class="tr-nearby-hotel-list">
              <div class="tr-hotel-img">
                <a href="javascript:void(0):">
                  @if($nearbhotel->hotel_id !="")
                  <img
                    src="https://photo.hotellook.com/image_v2/limit/h{{$nearbhotel->hotel_id}}_1/376/229.jpg"
                    alt="NearBy Hotel">
                  @else
                  <img  src="{{asset('/images/Hotel lobby.svg')}}" alt="NearBy Hotel">
                  @endif
                </a>
              </div>
              <div class="tr-hotel-deatils">
                <div class="tr-hotel-name"><a
                   href="{{route('hotel.detail',[$nearbhotel->LocationId.'-'.$nearbhotel->hotelid.'-'.strtolower( str_replace(' ', '_', str_replace('#', '!', $nearbhotel->slug))) ])}}?checkin={{ $_GET['checkin'] ?? '' }}&check_out={{ $GET['checkout'] ?? '' }}">{{$nearbhotel->name}}</a>
                </div>
                <!-- {{$nearbhotel->address}} -->
                <div class="tr-rating d-block d-sm-block d-md-none">
                  <span><i class="fa fa-star" aria-hidden="true"></i></span> 5.0
                </div>
                <div class="tr-like-review">
                  <span class="tr-likes"><svg width="21" height="21" viewBox="0 0 21 21" fill="none"
                      xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M10.6655 6.37175C9.45752 5.01908 7.44319 4.65521 5.92972 5.89385C4.41626 7.13249 4.20318 9.20344 5.39172 10.6684L10.6655 15.5524L15.9392 10.6684C17.1277 9.20344 16.9407 7.11946 15.4012 5.89385C13.8617 4.66824 11.8734 5.01908 10.6655 6.37175Z"
                        stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>94%</span>
                  <span class="tr-total-rating">4.2</span>
                  <span class="tr-vgood">Very Good</span>
                </div>
                @if($nearbhotel->pricefrom !="")
                <div class="tr-price-section"><span>${{$nearbhotel->pricefrom}}</span> /night</div>
                @endif
              </div>
            </div>
            @endif
            @endforeach

            @else
            <p>Hotel not found.</p>
            @endif


          </div>
          <a href="javascript:void(0);" class="tr-show-all-hotels">Show all hotels</a>
        </div>
        <!--NEARBY HOTEL - END-->

                      
                               
        <!--Also Known As Section - START-->
<div class="tr-also-known-as-section" style="margin-top: 50px;">
  <h3 class="d-none d-md-block">Also Known As for {{$searchresult[0]->name}}</h3>
  <h3 class="d-block d-sm-block d-md-none">Also Known As</h3>
  <span id="detailalsoknownasdata">
    @if($searchresult[0]->Also_known_as && !empty($searchresult[0]->Also_known_as))
      <div class="tr-also-known-as-list" style="margin-top: 20px;">
        @php
          $aliasTerms = explode(',', $searchresult[0]->Also_known_as);
          $formattedAliases = [];
          foreach($aliasTerms as $alias) {
            $formattedAliases[] = '<a href="'.url()->current().'?alias='.urlencode(trim($alias)).'" class="tr-alias-name" style="font-size: 18px; margin-right: 5px;">'.trim($alias).'</a>';
          }
        @endphp
        {!! implode('<span style="font-size: 22px; margin-right: 5px;">, </span>', $formattedAliases) !!}
      </div>
    @else
      <p style="margin-top: 20px;">No aliases found.</p>
    @endif
  </span>
</div>
<!--Also Known As Section - END-->

             
<!--People Also Search Section - START-->
<div class="tr-people-also-search-section" style="margin-top: 30px;">
  <h3 class="d-none d-md-block">People Also Search for {{$searchresult[0]->name}}</h3>
  <h3 class="d-block d-sm-block d-md-none">People Also Search</h3>
  <span id="detailpeoplealsosearchdata">
    @if($searchresult[0]->People_also_search && !empty($searchresult[0]->People_also_search))
      <div class="tr-people-also-search-list" style="margin-top: 20px;">
        @php
          $searchTerms = explode(',', $searchresult[0]->People_also_search);
          $formattedTerms = [];
          foreach($searchTerms as $search) {
            $formattedTerms[] = '<a href="'.url()->current().'?search='.urlencode(trim($search)).'" class="tr-search-name" style="font-size: 18px; margin-right: 5px;">'.trim($search).'</a>';
          }
        @endphp
        {!! implode('<span style="font-size: 22px; margin-right: 5px;">, </span>', $formattedTerms) !!}
      </div>
    @else
      <p style="margin-top: 20px;">No people also searched found.</p>
    @endif
  </span>
</div>
<!--People Also Search Section - END-->



             
                      
                      
        <!--BREADCRUMB - START-->
        <div class="tr-breadcrumb-section">
          <ul class="tr-breadcrumb">
                         <li><a href="https://www.travell.co">Travell</a></li>
            @if(!$getcontlink->isEmpty())
            <li>
              <a href="{{ route('explore_continent_list',[$getcontlink[0]->contid,$getcontlink[0]->cName])}}">
                {{$getcontlink[0]->cName}}</a>
            </li>
            <li>
              <a href="{{ route('explore_country_list',[$getcontlink[0]->CountryId,$getcontlink[0]->slug])}}">
                {{$getcontlink[0]->Name}}</a>
            </li>
            @endif
            @if(!empty($searchresult))


            @if(!empty($locationPatent))
            <?php $locationPatents = array_reverse($locationPatent); ?>
            @foreach ($locationPatents as $location)
            <li>
              <a href="@if(!empty($location)){{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug']) ]) }}@endif"
                target="_blank">
                {{ $location['Name'] }}</a>
            </li>
            @endforeach
            @endif
            @if(!$getlocationexp->isEmpty())
            <li><a
                href="{{ route('search.results', [$getlocationexp[0]->slugid.'-'.strtolower($getlocationexp[0]->Slug)]) }}"
                target="_blank">{{$getlocationexp[0]->Name}}</a>
            </li>
            @endif
            <?php
            $currentUrl = url()->current();
            preg_match("/(\d+)-/", $currentUrl, $matches);
            $firstId = $matches[1];
            ?>

            <li><a
   href="@if(isset($getlocationexp) && !empty($getlocationexp) && isset($getlocationexp[0]) && isset($getlocationexp[0]->Slug)){{ route('hotel.list',[$firstId.'-'.$getlocationexp[0]->Slug]) }}@endif"
   target="_blank">
   @if(isset($getlocationexp) && !empty($getlocationexp) && isset($getlocationexp[0]) && isset($getlocationexp[0]->Name)){{$getlocationexp[0]->Name}} Hotels @else Hotels @endif</a>
</li>
<li>@if(isset($searchresult) && !empty($searchresult) && isset($searchresult[0]) && isset($searchresult[0]->name)){{$searchresult[0]->name}}@endif</li>
            @endif
            <!-- <li><a href="javascript:void(0);">Home</a></li>
            <li><a href="javascript:void(0);">Hotel listing</a></li>
            <li>Hyatt Regency Houston West</li> -->
          </ul>
        </div>
        <!--BREADCRUMB - END-->
      </div>
    </div>
  </div>

  <!--FOOTER-->
  @include('frontend.footer')
 <div class="overlay" id="overLay"></div>

 <!-- Share Modal -->
 <div class="modal" id="shareModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        <h3>Share this experience</h3>
        <div class="tr-share-infos">
             <div class="tr-hotel-img">
        @if(!empty($searchresult[0]->hotelid) && isset($images[$searchresult[0]->hotelid][0]))
            <img src="https://photo.hotellook.com/image_v2/limit/{{ $images[$searchresult[0]->hotelid][0] }}/628/567.auto">
        @else
            <img src="{{ asset('/images/Hotel lobby-image.png') }}">
        @endif
    </div>
          <div class="tr-share-details">
            <span class="tr-hotel-name">{{ $searchresult[0]->name }}</span>
				<span class="tr-rating">
                @if(!empty($searchresult[0]->rating))
                    <?php
                        $rating = (float)$searchresult[0]->rating;
                        $formattedRating = round($rating, 2); // Rounded to two decimal places
                    ?>
                    {{ $formattedRating }}
                @else
                    N/A
                @endif
            </span>
            <span class="tr-bedrooms">
              <span>2 bedrooms</span>
              <span>3 beds</span>
              <span>2 bathrooms</span>
            </span>
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

<!--Add Highlight Review Modal-->
<div class="tr-write-review-modal" id="highlight-review-modal">
    <div class="tr-popup-content">
            <h3>Add Highlight Review</h3>
        <div class="tr-write-review-content">
            <div class="tr-hotel-reviews-details">
                <div class="tr-main-image">
                    <img src="{{asset('/frontend/hotel-detail/images/no-data-place-image.png')}}" alt="img" class="d-block w-100" height="200" width="482">                    
                   </div>
                <div class="tr-hotel-info">
                    <h4 class="tr-hotel-name">{{$searchresult[0]->name}}</h4>
                </div>
            </div>

            <form id="highlight-review" class="add_highlight_review">
            <input type="hidden" name="sight_id" value="{{$searchresult[0]->id}}">
                <div class="tr-review-msg">
                    <label>Write a highlight review</label>
                    <textarea class="form-control" id="highlight-review-text" placeholder="Type your highlight review here" name="highlight_review"></textarea>
                    <div class="error-msg" id="highlight-review-error"></div>
                </div>
                <div class="tr-review-image">
                    <label>Add some photos (optional)</label>
                    <div class="tr-file-upload">
                        <div class="tr-image-upload-wrap">
                            <input class="tr-file-upload-input" type='file' id="highlight-files" name="files[]" onchange="readHighlightURL(this);" accept="image/*" multiple/>
                            <div class="tr-drag-image">
                                <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <g clip-path="url(#clip0_441_7517)">
                                        <path d="M15.8385 12.6667C15.8385 13.0203 15.6981 13.3594 15.448 13.6095C15.198 13.8595 14.8588 14 14.5052 14H2.50521C2.15159 14 1.81245 13.8595 1.5624 13.6095C1.31235 13.3594 1.17188 13.0203 1.17188 12.6667V5.33333C1.17188 4.97971 1.31235 4.64057 1.5624 4.39052C1.81245 4.14048 2.15159 4 2.50521 4H5.17188L6.50521 2H10.5052L11.8385 4H14.5052C14.8588 4 15.198 4.14048 15.448 4.39052C15.6981 4.64057 15.8385 4.97971 15.8385 5.33333V12.6667Z" stroke="#6A6A6A" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M8.49479 11.3333C9.96755 11.3333 11.1615 10.1394 11.1615 8.66667C11.1615 7.19391 9.96755 6 8.49479 6C7.02203 6 5.82812 7.19391 5.82812 8.66667C5.82812 10.1394 7.02203 11.3333 8.49479 11.3333Z" stroke="#6A6A6A" stroke-linecap="round" stroke-linejoin="round"/>
                                    </g>
                                </svg>
                                <div>Click to add photos</div>
                                <span>or drag and drop</span>
                            </div>
                        </div>
                        <div class="tr-file-upload-content"> 
                            <img class="tr-file-upload-image" src="#" alt="your image" />
                            <div class="image-title-wrap">
                                <button type="button" onclick="removeHighlightUpload()" class="tr-remove-image">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="tr-popup-btn" id="addHighlightReview">Submit</button>
            </form>
        </div>
    </div>
</div>


  <!-- Gallery Modal-->
  <div class="tr-gallery-popup">
    <div class="tr-gallery-header">
      <div class="tr-gallery-action">
        <div class="tr-close-button">
          <button type="button" class="btn-close"></button>
        </div>
        <div class="tr-share-save">
          <a href="javascript:void(0);" class="tr-share" data-bs-toggle="modal" data-bs-target="#shareModal">Share</a>
          <a href="javascript:void(0);" class="tr-save">Save</a>
        </div>
      </div>
      <ul class="tr-galleries-nav-tab">
      <li><a href="#galleryOutdoor" class="active">All Images</a></li>
      </ul>
    </div>

    <div class="tr-gallery-categories responsive-container">
      <div class="tr-galleries-section" id="galleryOutdoor">
      
       <!---- new code -->
      <h3>Indoor and Outdoor</h3>
         <!-- start -->
        <div class="tr-gallery-category ">

    
             @if($hotelid !="")
            <?php
            $hoteid = $hotelid;
            $imgcount = isset($images[$hoteid]) ? count($images[$hoteid]) : 0;
            ?>
            <ul>
              @if(isset($images[$hoteid][0]) && $images[$hoteid][0] != "")
              <li data-bs-toggle="modal" data-bs-target="#gallerySliderModal"  class="">
                <img 
                  src="https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/1000/1000.auto"
                  alt="Outdoor Pictures 1">
              </li>
              @endif
              @php
             $remainingImages = isset($images[$hoteid]) ? array_slice($images[$hoteid], 1) : [];
              @endphp
              <?php $a = 2; ?>
              @for ($i = 0; $i <$imgcount; $i++) 
              @if ($i < count($remainingImages))
               @php $image=$remainingImages[$i];

                @endphp 
              
                @if(!empty($image)) <li data-bs-toggle="modal" data-bs-target="#gallerySliderModal" class="">
                <img  src="https://photo.hotellook.com/image_v2/limit/{{ $image }}/1000/1000.auto"
                  alt="Outdoor Pictures {{$a}}">
                </li>
                @endif
                @endif
                <?php $a++; ?>
                @endfor
            </ul>
            @endif

     
       
       
        </div>

      <!-- end -->
      
      
      
     <!-- end all image code  -->
        
   
    
     
    </div>
  </div>
    
</div>
  <!-- Gallery Slider Modal -->
  <div class="modal responsive-container" id="gallerySliderModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div id="gallerySlider" class="carousel slide" data-bs-ride="carousel">
            <!-- Indicators/dots -->
            <div class="carousel-indicators">
              <!--Button comming from JS-->
            </div>
            <!-- The slideshow/carousel -->
            <div class="carousel-inner">
              <!--Images comming from JS-->
            </div>
            <!-- Left and right controls/icons -->
            <button class="carousel-control-prev" type="button" data-bs-target="#gallerySlider"
              data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
            <button class="carousel-control-next" type="button" data-bs-target="#gallerySlider"
              data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!--Write a Review Modal-->
  <div class="tr-write-review-modal">
    <div class="tr-popup-content">
      <h3>Write a review</h3>
      <div class="tr-write-review-content">
        <div class="tr-hotel-reviews-details">
          <div class="tr-main-image">
            @if(!empty($searchresult[0]->hotelid))
            <?php $hoteid = $searchresult[0]->hotelid; ?>
            @if(isset($images[$hoteid][0] ) && $images[$hoteid][0] !="")
            <img  src="https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/628/567.auto"
              height="50" class="h-100" alt="" alt="Room Image">
            @else
            <img src="{{asset('/images/Hotel lobby.svg')}}" alt="">
            @endif
            @endif
          </div>
          <div class="tr-hotel-info">
            <h4 class="tr-hotel-name">{{$searchresult[0]->name}}</h4>
            <div class="tr-hotel-address">{{$searchresult[0]->address}}</div>
            <div class="tr-rated">
              @for($i =1;$i < 5;$i++) @if($i < $searchresult[0]->address )
                <span class="tr-star"><img
                    src="{{ asset('/frontend/hotel-detail/images/icons/star-fill-icon.svg')}}"></span>
                @else
                <span class="tr-star"><img
                    src="{{ asset('/frontend/hotel-detail/images/icons/star-blank-icon.svg')}}"></span>
                @endif
                @endfor
                <span>Excellent</span>
            </div>
          </div>
        </div>
        <form id="addReview">
          <div class="tr-overall-rating">
            <div id="msg"></div>

            <div class="tr-rating">
              <label>How would you rate your experience?</label>
              <div class="tr-stars star-exp">
                <input type="radio" data-rating="1" class="star" name="expStar" id="expStar5" value="5"><label
                  for="expStar5"></label>
                <input type="radio" data-rating="2" class="star" name="expStar" id="expStar4" value="4"><label
                  for="expStar4"></label>
                <input type="radio" data-rating="3" class="star" name="expStar" id="expStar3" value="3"><label
                  for="expStar3"></label>
                <input type="radio" data-rating="4" class="star" name="expStar" id="expStar2" value="2"><label
                  for="expStar2"></label>
                <input type="radio" data-rating="5" class="star" name="expStar" id="expStar1" value="1"><label
                  for="expStar1"></label>
              </div>
            </div>
            <div id="rating-error"></div>
          </div>
          <div class="tr-go-with">
            <label>Who did you go with?</label>
            <ul class="go-with">
              <li onclick="selectButton(this)">Business</li>
              <li onclick="selectButton(this)" class="selected">Couple</li>
              <li onclick="selectButton(this)">Family</li>
              <li onclick="selectButton(this)">Friends</li>
              <li onclick="selectButton(this)">Solo</li>
              <div class="error-msg" id="go-with-error"></div>
            </ul>
          </div>
          <div class="tr-review-types">
            <div class="tr-rating">
              <label>Cleanliness :</label>
              <div class="tr-stars star-cleanliness">
                <input type="radio" class="star" data-rating="1" name="cleanlinessStar" id="cleanlinessStar5"
                  value="5"><label for="cleanlinessStar5"></label>
                <input type="radio" class="star" data-rating="2" name="cleanlinessStar" id="cleanlinessStar4"
                  value="4"><label for="cleanlinessStar4"></label>
                <input type="radio" class="star" data-rating="3" name="cleanlinessStar" id="cleanlinessStar3"
                  value="3"><label for="cleanlinessStar3"></label>
                <input type="radio" class="star" data-rating="4" name="cleanlinessStar" id="cleanlinessStar2"
                  value="2"><label for="cleanlinessStar2"></label>
                <input type="radio" class="star" data-rating="5" name="cleanlinessStar" id="cleanlinessStar1"
                  value="1"><label for="cleanlinessStar1"></label>
              </div>
              <div class="error-msg" id="cleanliness-rating-error"></div>
            </div>
            <div class="tr-rating">
              <label>Location :</label>
              <div class="tr-stars star-location">
                <input type="radio" class="star" data-rating="1" name="locationStar" id="locationStar5" value="5"><label
                  for="locationStar5"></label>
                <input type="radio" class="star" data-rating="2" name="locationStar" id="locationStar4" value="4"><label
                  for="locationStar4"></label>
                <input type="radio" class="star" data-rating="3" name="locationStar" id="locationStar3" value="3"><label
                  for="locationStar3"></label>
                <input type="radio" class="star" data-rating="4" name="locationStar" id="locationStar2" value="2"><label
                  for="locationStar2"></label>
                <input type="radio" class="star" data-rating="5" name="locationStar" id="locationStar1" value="1"><label
                  for="locationStar1"></label>
              </div>
              <div class="error-msg" id="location-rating-error"></div>
            </div>
          </div>
          <div class="tr-review-types">
            <div class="tr-rating star-service">
              <label>Service :</label>
              <div class="tr-stars">
                <input type="radio" class="star" data-rating="1" name="serviceStar" id="serviceStar5" value="5"><label
                  for="serviceStar5"></label>
                <input type="radio" class="star" data-rating="2" name="serviceStar" id="serviceStar4" value="4"><label
                  for="serviceStar4"></label>
                <input type="radio" class="star" data-rating="3" name="serviceStar" id="serviceStar3" value="3"><label
                  for="serviceStar3"></label>
                <input type="radio" class="star" data-rating="4" name="serviceStar" id="serviceStar2" value="2"><label
                  for="serviceStar2"></label>
                <input type="radio" class="star" data-rating="5" name="serviceStar" id="serviceStar1" value="1"><label
                  for="serviceStar1"></label>
              </div>
              <div class="error-msg" id="service-rating-error"></div>
            </div>
            <div class="tr-rating">
              <label>Value :</label>
              <div class="tr-stars star-value">
                <input type="radio" class="star" data-rating="1" name="valueStar" id="valueStar5" value="5"><label
                  for="valueStar5"></label>
                <input type="radio" class="star" data-rating="2" name="valueStar" id="valueStar4" value="4"><label
                  for="valueStar4"></label>
                <input type="radio" class="star" data-rating="3" name="valueStar" id="valueStar3" value="3"><label
                  for="valueStar3"></label>
                <input type="radio" class="star" data-rating="4" name="valueStar" id="valueStar2" value="2"><label
                  for="valueStar2"></label>
                <input type="radio" class="star" data-rating="5" name="valueStar" id="valueStar1" value="1"><label
                  for="valueStar1"></label>
              </div>
              <div class="error-msg" id="value-rating-error"></div>
            </div>
          </div>
          <div class="tr-review-msg">
            <label>Name</label>
            <input type="text" class="form-control" placeholder="Enter Name" name="name" id="name">
            <div class="error-msg" id="name-error"></div>
          </div>
          <div class="tr-review-msg">
            <label>Email</label>
            <input type="text" class="form-control" placeholder="Enter Email" name="email" id="email">
            <div class="error-msg" id="email-error"></div>
          </div>
          <div class="tr-review-msg">
            <label>Write a review</label>
            <textarea class="form-control" placeholder="Type your review here" name="review" id="review"></textarea>
          </div>
          <div class="error-msg" id="review-error"></div>
          <div class="tr-review-image">
            <label>Add some photos (optional)</label>
            <div class="tr-file-upload">
              <!--<button class="file-upload-btn" type="button" onclick="$('.file-upload-input').trigger( 'click' )">Add Image</button>-->
              <div class="tr-image-upload-wrap">
                <input class="tr-file-upload-input" type='file' onchange="readURL(this);" accept="image/*" id="files"
                  name="files[]" />
                <div class="tr-drag-image">
                  <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#clip0_441_7517)">
                      <path
                        d="M15.8385 12.6667C15.8385 13.0203 15.6981 13.3594 15.448 13.6095C15.198 13.8595 14.8588 14 14.5052 14H2.50521C2.15159 14 1.81245 13.8595 1.5624 13.6095C1.31235 13.3594 1.17188 13.0203 1.17188 12.6667V5.33333C1.17188 4.97971 1.31235 4.64057 1.5624 4.39052C1.81245 4.14048 2.15159 4 2.50521 4H5.17188L6.50521 2H10.5052L11.8385 4H14.5052C14.8588 4 15.198 4.14048 15.448 4.39052C15.6981 4.64057 15.8385 4.97971 15.8385 5.33333V12.6667Z"
                        stroke="#6A6A6A" stroke-linecap="round" stroke-linejoin="round" />
                      <path
                        d="M8.49479 11.3333C9.96755 11.3333 11.1615 10.1394 11.1615 8.66667C11.1615 7.19391 9.96755 6 8.49479 6C7.02203 6 5.82812 7.19391 5.82812 8.66667C5.82812 10.1394 7.02203 11.3333 8.49479 11.3333Z"
                        stroke="#6A6A6A" stroke-linecap="round" stroke-linejoin="round" />
                    </g>
                    <defs>
                      <clipPath id="clip0_441_7517">
                        <rect width="16" height="16" fill="white" transform="translate(0.5)" />
                      </clipPath>
                    </defs>
                  </svg>
                  <div>Click to add photos</div>
                  <span>or drag and drop</span>
                </div>
              </div>
              <div class="tr-file-upload-content">
                <img class="tr-file-upload-image" src="#" alt="your image" />
                <div class="image-title-wrap">
                  <button type="button" onclick="removeUpload()" class="tr-remove-image">
                    <i class="fa fa-times" aria-hidden="true"></i>
                    <!--<span class="image-title">Uploaded Image</span>-->
                  </button>
                </div>
              </div>
            </div>
          </div>
          <button type="submit" class="tr-popup-btn">Submit</button>
        </form>
      </div>
    </div>
  </div>
</body>

</html>
<script src="{{asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
<script src="{{ asset('/js/custom.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js')}} "></script>





<!-- start map js -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/leaflet.js"></script>
<script src="{{ asset('/js/map_leaflet.js')}}"></script>

<script src="{{ asset('/js/hotelDetails.js') }}"></script>
<script src="{{ asset('/frontend/hotel-detail/js/leaflet-simple-map-screenshoter.js') }}"></script>
<script src="{{ asset('/frontend/hotel-detail/js/FileSaver.js') }}"></script>

<script>
    // Ensure the map is initialized after the DOM is fully loaded
    document.addEventListener("DOMContentLoaded", function() {
        let startTime = {{ $currentTimeMillis }};
        let timeDifference = 0;

        function checkTime() {
            let currentTime = Date.now();
            timeDifference = Math.floor((currentTime - startTime) / (1000 * 60));

            if (timeDifference >= 10) {
                document.getElementById("refresh-banner").classList.remove("d-none");
            }
        }

        checkTime();
        setInterval(checkTime, 60000);

        // Initialize the map with latitude and longitude
        var latitude = {{ $latitude ?? '0' }};
        var longitude = {{ $longitude ?? '0' }};

        console.log("Map initialized with coordinates:", latitude, longitude);

        var mapOptions = {
            center: [latitude, longitude],
            zoom: 15,  // Adjust the zoom level if needed
            zoomControl: true,
            scrollWheelZoom: true,
            dragging: true,
            boxZoom: true
        };

        var map = L.map('map1', mapOptions);

        // Use CartoDB Positron basemap
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://carto.com/">CARTO</a>',
            maxZoom: 19
        }).addTo(map);

        // Overpass API query to fetch rivers, lakes, and streets
        var overpassApiUrl = 'https://overpass-api.de/api/interpreter?data=[out:json];(way["highway"];way["natural"="water"];relation["waterway"="riverbank"];>;);out body;';

        // Fetch data from Overpass API
        fetch(overpassApiUrl)
            .then(response => response.json())
            .then(data => {
                var geoJsonData = osmtogeojson(data);  // Convert to GeoJSON format

                // Apply GeoJSON for Streets (white)
                L.geoJSON(geoJsonData, {
                    filter: function(feature) {
                        return feature.properties.highway;
                    },
                    style: function() {
                        return { color: 'white', weight: 2 };
                    }
                }).addTo(map);

                // Apply GeoJSON for Lakes/Rivers (blue)
                L.geoJSON(geoJsonData, {
                    filter: function(feature) {
                        return feature.properties.natural === 'water' || feature.properties.waterway === 'riverbank';
                    },
                    style: function() {
                        return { color: 'blue', weight: 2, fillColor: 'blue', fillOpacity: 0.6 };
                    }
                }).addTo(map);
            })
            .catch(error => console.log('Error loading Overpass API data: ', error));

        // Custom marker with icon
        var customIcon = L.icon({
            iconUrl: '{{ asset("/js/images/Hotel.svg") }}',
            iconSize: [62, 62],
            iconAnchor: [16, 32]
        });

        // Add marker to the map
        var marker = L.marker([latitude, longitude], {
            icon: customIcon
        }).addTo(map);

        // Initialize screenshot functionality
        var simpleMapScreenshoter = L.simpleMapScreenshoter().addTo(map);

        // Function to capture screenshot after map loads
        function captureScreenshot() {
            simpleMapScreenshoter.takeScreen('blob', {}).then(blob => {
                const screenshotImage = new Image();
                screenshotImage.src = URL.createObjectURL(blob);
                var mapContainer = document.getElementById('map1');
                mapContainer.classList.add('d-none');
                const screenshotContainer = document.getElementById('screenshotContainer');
                screenshotContainer.appendChild(screenshotImage);
            }).catch(e => {
                console.error(e.toString());
            });
        }

        // Delay the screenshot to allow map to load
        window.onload = function() {
            setTimeout(captureScreenshot, 1000);
        };
    });
</script>

	<script>
    // Function to update share links with the current page URL
    function updateShareLinks() {
      var currentUrl = window.location.href;

      // Set each share button's link for direct sharing on mobile
      document.getElementById("emailShare").href = `mailto:?subject=Check this out&body=${encodeURIComponent(currentUrl)}`;
      document.getElementById("smsShare").href = `sms:?body=${encodeURIComponent("Check this out: " + currentUrl)}`;
      document.getElementById("whatsappShare").href = `https://api.whatsapp.com/send?text=${encodeURIComponent("Check this out: " + currentUrl)}`;
      document.getElementById("facebookShare").href = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(currentUrl)}`;
      document.getElementById("twitterShare").href = `https://twitter.com/intent/tweet?url=${encodeURIComponent(currentUrl)}&text=${encodeURIComponent("Check this out!")}`;
      document.getElementById("messengerShare").href = `https://m.me/?link=${encodeURIComponent(currentUrl)}`;
    }

    // Event listener to update links each time the modal is shown
    document.getElementById('shareModal').addEventListener('show.bs.modal', updateShareLinks);

    // Copy link function
    function copyLink() {
      var copyText = document.createElement("textarea");
      copyText.value = window.location.href;
      document.body.appendChild(copyText);
      copyText.select();
      document.execCommand("copy");
      document.body.removeChild(copyText);

      // Show feedback alert
      var alert = document.getElementById("copyAlert");
      alert.style.display = "block";
      setTimeout(function() {
        alert.style.display = "none";
      }, 2000);
    }

    // Copy embed code function
    function copyEmbedCode() {
      var embedCode = `<iframe src="${window.location.href}" width="600" height="400"></iframe>`;
      var tempInput = document.createElement("textarea");
      document.body.appendChild(tempInput);
      tempInput.value = embedCode;
      tempInput.select();
      document.execCommand("copy");
      document.body.removeChild(tempInput);

      var alert = document.getElementById("embedAlert");
      alert.style.display = "block";
      setTimeout(function() {
        alert.style.display = "none";
      }, 2000);
    }
  </script>

<script>
// Open highlight review modal
$(".tr-show-all-review-btn").click(function() {
    $("#highlight-review-modal").show();
});

// Handle highlight review form submission
$("#highlight-review").submit(function(e) {
    e.preventDefault();
    
    let reviewText = $("#highlight-review-text").val();
    if (!reviewText.trim()) {
        $("#highlight-review-error").text("Please write a review");
        return;
    }

    // Get form data
    let formData = new FormData();
    formData.append('highlight_review', reviewText);
	formData.append('hotel_id', {{$searchresult[0]->id}});
    
    // Submit the review
    $.ajax({
        url: '/store-highlight-review',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                // Clear form and hide modal
                $("#highlight-review")[0].reset();
                $("#highlight-review-modal").fadeOut();
                $('body').css('overflow', 'auto');
                
                // Show success message (you can customize this)
                alert('Review submitted successfully!');
                
                // Optionally refresh the reviews section
                // location.reload();
            }
        },
        error: function(xhr, status, error) {
            let errorMessage = 'Error submitting review. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            $("#highlight-review-error").text(errorMessage);
        }
    });
});
$(document).mouseup(function(e) {
    var modal = $("#highlight-review-modal .tr-popup-content");
    var modalContainer = $("#highlight-review-modal");
    
    // If the target of the click isn't the modal container nor a descendant of the modal content
    if (!modal.is(e.target) && modal.has(e.target).length === 0 && modalContainer.is(e.target)) {
        modalContainer.fadeOut();
        $('body').css('overflow', 'auto'); // Restore background scrolling
    }
});

// Also add escape key functionality
$(document).keydown(function(e) {
    if (e.key === "Escape") {
        $("#highlight-review-modal").fadeOut();
        $('body').css('overflow', 'auto');
    }
});
// Handle file upload preview for highlight review
function readHighlightURL(input) {
    if (input.files && input.files[0]) {
        let reader = new FileReader();
        
        reader.onload = function(e) {
            $('.tr-image-upload-wrap').hide();
            $('.tr-file-upload-image').attr('src', e.target.result);
            $('.tr-file-upload-content').show();
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Handle removing uploaded file for highlight review
function removeHighlightUpload() {
    $('.tr-file-upload-input').val('');
    $('.tr-file-upload-content').hide();
    $('.tr-image-upload-wrap').show();
}
</script>