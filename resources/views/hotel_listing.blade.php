<!DOCTYPE html>
<html lang="en-US">

<head>

<meta name="robots" content="index, follow">

<meta name="csrf-token" content="{{ csrf_token() }}">
 <link rel="canonical" href="{{ url()->current() }}" />
  <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3G0VLZYF7R"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3G0VLZYF7R');
</script>

<!-- Preconnect to external domains -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
  <link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>
  <link rel="preconnect" href="https://photo.hotellook.com" crossorigin>
  <link rel="preconnect" href="https://pics.avs.io" crossorigin>
  <link rel="preconnect" href="https://unpkg.com" crossorigin>

  <!-- Preload critical resources -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="preconnect" href="https://photo.hotellook.com" crossorigin>

  <!-- Preload critical CSS -->
  <link rel="preload" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}" as="style">
  <link rel="preload" href="{{ asset('/frontend/hotel-detail/css/style.css')}}" as="style">
  <link rel="preload" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}" as="style" media="(min-width: 768px)">
  <link rel="preload" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" as="style" media="screen">
  <link rel="preload" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}" as="style">
  <link rel="preload" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}" as="style">
  <link rel="preload" href="https://unpkg.com/leaflet/dist/leaflet.css" as="style">
  <link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
	<script src="{{ asset('js/cookie-consent.js') }}"></script>
  <!-- Regular CSS -->
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}" media="(min-width: 768px)">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}">
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

 @if ($pagetype == "withoutdate")
    @php
        $metaTitle = "";
        $metaDescription = "";
    @endphp

       @if (!empty($sight_info) && count($sight_info) > 0 || !empty($neighborhood_info) && count($neighborhood_info) > 0 || !empty($amenity_info) && count($amenity_info) > 0 || !empty($propertyType_info) && count($propertyType_info) > 0)
    @php
        // Set base title based on property type if available, otherwise use "Hotels"
        $baseType = "Hotels";
        if (!empty($propertyType_info) && count($propertyType_info) > 0) {
            $propertyTypes = collect($propertyType_info)->pluck('type')->implode(', ');
            $baseType = $propertyTypes;
        }
        
        $metaTitle = "Best " . $baseType;
        $metaDescription = "Compare prices for " . $baseType;
        
        // Handle combinations for title
        if (!empty($sight_info) && count($sight_info) > 0) {
            $metaTitle .= " near " . $sight_info->pluck('Title')->implode(', ');
            $metaDescription .= " near " . $sight_info->pluck('Title')->implode(', ');
        }
        if (!empty($neighborhood_info) && count($neighborhood_info) > 0) {
            $metaTitle .= (!empty($sight_info) ? " " : "") . " in " . $neighborhood_info->pluck('Name')->implode(', ');
            $metaDescription .= (!empty($sight_info) ? " in " : " in ") . $neighborhood_info->pluck('Name')->implode(', ');
        }
        if (!empty($amenity_info) && count($amenity_info) > 0) {
            $metaTitle .= (!empty($sight_info) || !empty($neighborhood_info) ? " " : "") . " with " . $amenity_info->pluck('name')->implode(', ');
            $metaDescription .= (!empty($sight_info) || !empty($neighborhood_info) ? " with " : " with ") . $amenity_info->pluck('name')->implode(', ');
        }
        
        $metaTitle .= " in $lname | Travell (2025)";
        $metaDescription .= " in $lname. Browse images, read verified user reviews, and explore nearby restaurants and attractions to find the perfect luxury stay at the best price!";
    @endphp
    @else
      @if (!$metadata->isEmpty() && !empty(trim($metadata[0]->HotelTitleTag)))
            @php
                $metaTitle = trim($metadata[0]->HotelTitleTag);
            @endphp
        @else
            @php
                $metaTitle = "Compare Hotel Prices in $lname | Best Deals on Stays for Every Budget | Travell (2025)";
            @endphp
        @endif

      @if (!$metadata->isEmpty() && !empty(trim($metadata[0]->HotelMetaDescription)))
            @php
                $metaDescription = trim($metadata[0]->HotelMetaDescription);
            @endphp
        @else
            @php
                $metaDescription = "Find and compare the best hotel prices in $lname. Discover deals on luxury, mid-range, and budget accommodations with our easy-to-use price comparison. Get the lowest rates for your ideal stay, tailored to any travel style and budget.";
            @endphp
        @endif
    @endif

    <title>{{ $metaTitle }}</title>
    <meta name="description" content='{{ $metaDescription }}'>
@endif
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}">
  </script> <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/slick.min.js')}}"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="/js/sign_in.js"></script>

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
  <!-- <link rel="stylesheet" href="{{ asset('/css/custom.css')}}"> -->
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}">

  <script type="text/javascript">
 jQuery.noConflict();
jQuery(document).ready(function($) {
  $('.tr-budget-slider').slick({
    autoplay: false,
    autoplaySpeed: 2000,
    dots: false,
    arrows: true,
    infinite: true,
    slidesToShow: 4,
    slidesToScroll: 1,
    responsive: [
      {
        breakpoint: 768,
        settings: {
          arrows: false,
          infinite: false,
          slidesToShow: 1.2,
          slidesToScroll: 1
        }
      }
    ]
  });
});

</script>
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <style>
    #map {
      height: 100%; /* Set a fixed height */
      width: 100%;
    }

    .tr-filter-label {
    margin-top: -30px; /* Adjust this value to control how much you move it up */
}
    .tr-filter-label + .unique-h5 {
    margin-top: 25px;
}
.tr-hotel-detail-link {
    display: block;
    text-decoration: none;
    color: inherit;
}

.tr-hotel-detail-link span {
    color: inherit;
}
@media (max-width: 768px) { /* Apply styles only for mobile screens */
    .tr-breadcrumb-section {
        display: flex;
        flex-wrap: nowrap;  /* Prevent wrapping */
        overflow-x: auto;  /* Enable horizontal scrolling if necessary */
        white-space: nowrap;  /* Prevent text from breaking into multiple lines */
        align-items: center;
        scrollbar-width: none;  /* Hide scrollbar for a cleaner look */
    }

    .tr-breadcrumb {
        display: flex;
        flex-wrap: nowrap;  /* Ensure everything stays in one row */
        overflow-x: auto;  /* Allow scrolling if necessary */
        list-style: none;
        padding: 0;
        margin: 0;
        white-space: nowrap;  /* Prevent wrapping */
    }
}

.tr-more-facilities .short-description-content {
    text-align: justify; /* Justifies the text alignment */
    max-height: 92px;
    overflow: hidden;
    position: relative;
    line-height: 1.5; 
}

.tr-more-facilities .short-description-content.show-more {
    max-height: none; /* Shows full content when toggled */
}

.tr-anchor-btn.toggle-list {
    display: block; /* Ensures the button is visible */
    margin-top: 10px;
}
/* Transition for smoother expansion */
.tr-more-facilities.expanded .paragraph-content {
    max-height: none; /* Remove height restriction when expanded */
}

/* "Read More" button styling */
/* Force visibility for troubleshooting */
.custom-read-more {
    font-weight: 600;
  font-size: 16px;
  line-height: 19px;
  letter-spacing: 0.01em;
  color: #FF4B01;
  border: none;
  border-bottom: 1px solid #FF4B01;
  background: transparent;
}

/* Hide "Read More" button when content is fully expanded */
.tr-more-facilities.expanded .custom-read-more {
    display: none;
}
	  .tr-breadcrumb-section {
    display: block !important;
}
  </style>
   <?php
      $locationPatents = $locationPatent;
       $n = 2;
	  if(!empty($locationPatents)){
       $locationPatents = array_reverse($locationPatent);
	   }
      ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "WebPage",
      "@id": "{{ url()->current() }}#webpage",
      "name": "@if(!$getlocationexp->isEmpty()) {{ $getlocationexp[0]->Name }} Hotels @else Hotels @endif @if($pagetype == 'withoutdate' && !empty($sight_info) && count($sight_info) > 0) near {{ $sight_info->pluck('name')->implode(', ') }} @endif @if($pagetype == 'withoutdate' && !empty($neighborhood_info) && count($neighborhood_info) > 0) in {{ $neighborhood_info->pluck('Name')->implode(', ') }} @endif @if($pagetype == 'withoutdate' && !empty($amenity_info) && count($amenity_info) > 0) with {{ $amenity_info->pluck('name')->implode(', ') }} @endif",
      "url": "{{ url()->current() }}",
      "inLanguage": "en",
      "isPartOf": {
        "@id": "https://www.travell.co/#website"
      },
      "breadcrumb": {
        "@id": "{{ url()->current() }}#breadcrumb"
      }
      @if($pagetype == 'withdate')
      ,"dateModified": "{{ date('Y-m-d') }}"
      @endif
    },
    
	 @if(!empty($hotels) && is_countable($hotels) && count($hotels) > 0)
    {
      "@type": "ItemList",
      "name": "@if(!$getlocationexp->isEmpty()) {{ $getlocationexp[0]->Name }} Hotels @else Hotels @endif @if($pagetype == 'withoutdate' && !empty($sight_info) && count($sight_info) > 0) near {{ $sight_info->pluck('name')->implode(', ') }} @endif @if($pagetype == 'withoutdate' && !empty($neighborhood_info) && count($neighborhood_info) > 0) in {{ $neighborhood_info->pluck('Name')->implode(', ') }} @endif",
      "itemListOrder": "https://schema.org/ItemListOrderDescending",
      "numberOfItems": {{ count($hotels) }},
      "itemListElement": [
        @foreach($hotels as $index => $hotel)
        @if($index < 10)
        {
          "@type": "ListItem",
          "position": {{ $index + 1 }},
          "item": {
            "@type": "Hotel",
            "name": "{{ $hotel->Title }}",
            "url": "{{ route('hotel.details', [$hotel->slugid.'-'.$hotel->HotelId.'-'.strtolower($hotel->Slug)]) }}",
            "address": {
              "@type": "PostalAddress",
              "addressLocality": "@if(!$getlocationexp->isEmpty()) {{ $getlocationexp[0]->Name }} @endif",
              "addressCountry": "@if(!$getcontlink->isEmpty()) {{ $getcontlink[0]->Name }} @endif"
            },
            @if(!empty($hotel->Address))
            "additionalProperty": [
              {
                "@type": "PropertyValue",
                "name": "Full Address",
                "value": "{{ $hotel->Address }}"
              }
            ],
            @endif
            @if(!empty($hotel->stars))
            "starRating": {
              "@type": "Rating",
              "ratingValue": "{{ $hotel->stars }}"
            },
            @endif
            @if(!empty($hotel->Latitude) && !empty($hotel->Longitude))
            "geo": {
              "@type": "GeoCoordinates",
              "latitude": {{ $hotel->Latitude }},
              "longitude": {{ $hotel->Longitude }}
            }
            @endif
            @if($pagetype == 'withdate' && !empty($hotel->price))
            ,"offers": {
              "@type": "Offer",
              "price": "{{ $hotel->price }}",
              "priceCurrency": "@if(!empty($hotel->currency)) {{ $hotel->currency }} @else USD @endif",
              "availability": "https://schema.org/InStock",
              @if(!empty($checkin) && !empty($checkout))
              "validFrom": "{{ $checkin }}",
              "validThrough": "{{ $checkout }}",
              @endif
              @if(!empty($hotel->partner_name))
              "seller": {
                "@type": "Organization",
                "name": "{{ $hotel->partner_name }}"
              },
              @endif
              "url": "{{ route('hotel.details', [$hotel->slugid.'-'.$hotel->HotelId.'-'.strtolower($hotel->Slug)]) }}@if(!empty($checkin) && !empty($checkout))?checkin={{ $checkin }}&checkout={{ $checkout }}@if(!empty($adults))&adults={{ $adults }}@endif@if(!empty($children))&children={{ $children }}@endif@endif"
            }
            @endif
          }
        }@if($index < 9 && $index < count($hotels) - 1),@endif
        @endif
        @endforeach
      ]
    },
    @endif
    
    {
      "@type": "BreadcrumbList",
      "@id": "{{ url()->current() }}#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Travell",
          "item": "https://www.travell.co"
        }
        @if(!$getcontlink->isEmpty())
        ,{
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
        }
        @endif
        
        @if(!empty($locationPatent))
        <?php 
        $locationPatents = array_reverse($locationPatent);
        $position = (!$getcontlink->isEmpty()) ? 4 : 2;
        ?>
        @foreach ($locationPatents as $location)
        ,{
          "@type": "ListItem",
          "position": {{ $position++ }},
          "name": "{{ $location['Name'] }}",
          "item": "{{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}"
        }
        @endforeach
        @endif
        
        @if(!$getlocationexp->isEmpty())
        <?php $position = (!$getcontlink->isEmpty()) ? (4 + (count($locationPatents ?? []))) : (2 + (count($locationPatents ?? []))); ?>
        ,{
          "@type": "ListItem",
          "position": {{ $position++ }},
          "name": "{{ $getlocationexp[0]->Name }}",
          "item": "{{ route('search.results', [$getlocationexp[0]->slugid.'-'.strtolower($getlocationexp[0]->Slug)]) }}"
        }
        @endif
        
        @if(!$getlocationexp->isEmpty())
          @if($pagetype == 'withoutdate' && ((!empty($sight_info) && count($sight_info) > 0) || (!empty($neighborhood_info) && count($neighborhood_info) > 0) || (!empty($amenity_info) && count($amenity_info) > 0)))
          <?php $position = (!$getcontlink->isEmpty()) ? (5 + (count($locationPatents ?? []))) : (3 + (count($locationPatents ?? []))); ?>
          ,{
            "@type": "ListItem",
            "position": {{ $position++ }},
            "name": "{{ $getlocationexp[0]->Name }} Hotels",
            "item": "{{ route('search.results', [$getlocationexp[0]->slugid.'-'.strtolower($getlocationexp[0]->Slug)]) }}"
          },
          {
            "@type": "ListItem",
            "position": {{ $position }},
            "name": "{{ $getlocationexp[0]->Name }} Hotels @if(!empty($sight_info) && count($sight_info) > 0) near {{ $sight_info->pluck('name')->implode(', ') }} @endif @if(!empty($neighborhood_info) && count($neighborhood_info) > 0) @if(!empty($sight_info) && count($sight_info) > 0) @endif in {{ $neighborhood_info->pluck('Name')->implode(', ') }} @endif @if(!empty($amenity_info) && count($amenity_info) > 0) @if(!empty($sight_info) || !empty($neighborhood_info)) with @endif {{ $amenity_info->pluck('name')->implode(', ') }} @endif",
            "item": "{{ url()->current() }}"
          }
          @else
          <?php $position = (!$getcontlink->isEmpty()) ? (5 + (count($locationPatents ?? []))) : (3 + (count($locationPatents ?? []))); ?>
          ,{
            "@type": "ListItem",
            "position": {{ $position }},
            "name": "{{ $getlocationexp[0]->Name }} Hotels",
            "item": "{{ url()->current() }}"
          }
          @endif
        @else
        <?php $position = (!$getcontlink->isEmpty()) ? (4 + (count($locationPatents ?? []))) : (2 + (count($locationPatents ?? []))); ?>
        ,{
          "@type": "ListItem",
          "position": {{ $position }},
          "name": "Hotels",
          "item": "{{ url()->current() }}"
        }
        @endif
      ]
    },
    
    @if(!$getlocationexp->isEmpty() && !empty($getlocationexp[0]->Latitude) && !empty($getlocationexp[0]->Longitude))
    {
      "@type": "City",
      "name": "{{ $getlocationexp[0]->Name }}",
      "containedInPlace": {
        "@type": "Country",
        "name": "@if(!$getcontlink->isEmpty()) {{ $getcontlink[0]->Name }} @endif"
      },
      "geo": {
        "@type": "GeoCoordinates",
        "latitude": {{ $getlocationexp[0]->Latitude }},
        "longitude": {{ $getlocationexp[0]->Longitude }}
      }
    },
    @endif
    
    @if(!empty($faqs) && count($faqs) > 0)
    {
      "@type": "FAQPage",
      "mainEntity": [
        @foreach($faqs as $index => $faq)
        {
          "@type": "Question",
          "name": "{{ $faq->question }}",
          "acceptedAnswer": {
            "@type": "Answer",
            "text": "{{ $faq->answer }}"
          }
        }@if($index < count($faqs) - 1),@endif
        @endforeach
      ]
    },
    @endif
    
    {
      "@type": "WebSite",
      "@id": "https://www.travell.co/#website",
      "url": "https://www.travell.co/",
      "name": "Travell",
      "publisher": {
        "@id": "https://www.travell.co/#organization"
      }
    },
    
    {
      "@type": "Organization",
      "@id": "https://www.travell.co/#organization",
      "name": "Travell",
      "url": "https://www.travell.co/",
      "logo": "{{ asset('/frontend/images/logo.png') }}"
    }
  ]
}
</script>

<?php if ($pagetype == "withdate") {

    $chekin = request()->get('checkin');
    $chkout = request()->get('checkout');

    // Validating and formatting check-in and check-out dates
    $checkin = preg_match('/^\d{4}-\d{2}-\d{2}$/', $chekin) ? $chekin : date('Y-m-d', strtotime(str_replace('-', ' ', $chekin)));
    $checkout = preg_match('/^\d{4}-\d{2}-\d{2}$/', $chkout) ? $chkout : date('Y-m-d', strtotime(str_replace('-', ' ', $chkout)));

    $guest = request('guest') ?: 1; // Default guest to 1 if not set

    // Meta Title and Meta Description
    $metaTitle = "";
    $metaDescription = "";

    if (!$metadata->isEmpty() && !empty(trim($metadata[0]->HotelTitleTag))) {
        $metaTitle = trim($metadata[0]->HotelTitleTag);
    } else {
        $metaTitle = "Compare Hotel Prices in $lname | Best Deals on Stays for Every Budget";
    }

    if (!$metadata->isEmpty() && !empty(trim($metadata[0]->HotelMetaDescription))) {
        $metaDescription = trim($metadata[0]->HotelMetaDescription);
    } else {
        $metaDescription = "Find and compare the best hotel prices in $lname. Discover deals on luxury, mid-range, and budget accommodations with our easy-to-use price comparison. Get the lowest rates for your ideal stay, tailored to any travel style and budget.";
    }
    ?>
    <title><?php echo $metaTitle; ?></title>
    <meta name="description" content="<?php echo $metaDescription; ?>">
<?php } ?>

	<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Hotels in {{$lname}}",
  "description": "Hotels in {{$lname}}, {{$getcontlink[0]->Name}}",
  "itemListOrder": "https://schema.org/ItemListOrderAscending",
  "itemListElement": [
    @if(!$searchresults->isEmpty())
      <?php  $z = 1;   $totalItems = $searchresults->count(); ?>
      @foreach($searchresults as $searchresult)
        <?php if ($pagetype == "withdate") { $ctName = $lname; $cityname = str_replace(' ', '_', $ctName);$CountryName = str_replace(' ', '_', $countryname); $url = $cityname . '-' . $CountryName; $hotel_url = url('hd-' . $searchresult->slugid . '-' . $searchresult->id . '-' . strtolower(str_replace(' ', '_', str_replace('#', '!', $searchresult->slug))) . "?checkin={$checkin}&checkout={$checkout}"); } else {  $hotel_url = url('hd-' . $searchresult->slugid . '-' . $searchresult->id . '-' . strtolower(str_replace(' ', '_', str_replace('#', '!', $searchresult->slug)))); } ?>
        {
          "@type": "ListItem",
          "position": {{$z}},
          "name": "{{ $searchresult->name }}",
          "url": "{{ $hotel_url }}",
          "image": "https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_0/520/460.jpg"
        }@if($z < $totalItems),@endif <?php $z++; ?> @endforeach @endif ]
}
</script>

</head>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
    style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<body>
  <!--HEADER-->
  @if($pagetype=="withoutdate")
  @include('frontend.header_without_search')
  <span class="d-none ptype">withoutdate</span>
  @else
  @include('frontend.header')
  <span class="d-none ptype">withdate</span>
  @endif


  <!-- Mobile Navigation-->
   @include('frontend.mobile_nav')

  <div @if($pagetype=='withoutdate' ) class="tr-listing-without-dates-1" @else class="tr-listing-with-dates-1" @endif>
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
        @if($pagetype=="withoutdate")
          <div class="tr-heading-section">
@php
    $hasAmenities = !empty($amenity_ids) && isset($amenity_info) && count($amenity_info) > 0;
    $hasNeighborhoods = !empty($neighborhood_ids) && isset($neighborhood_info) && count($neighborhood_info) > 0;
    $hasSights = !empty($sight_info) && isset($sight_info['sight_name']);
    $hasPropertyTypes = !empty($propertyType_ids) && isset($propertyType_info) && count($propertyType_info) > 0;
@endphp
            <h1>
              @if($st !="") {{$st}} star @endif
              @if($hasPropertyTypes && !$hasNeighborhoods && !$hasSights && !$hasAmenities && $amenity == "")
                {{ implode(', ', array_map(function($propertyType) { return $propertyType->type; }, $propertyType_info->toArray())) }} in {{$lname}}
              @else
                {{$lname}} Hotels
              @endif

@if(!$getlocationexp->isEmpty())
    @if($pagetype == 'withoutdate' && ($hasAmenities || $hasNeighborhoods || $hasSights))
            @if($hasSights && count($sight_info) > 0)
    near {{ implode(' and ', array_map(function($sight) { 
        return $sight->Title; 
    }, $sight_info->toArray())) }}
@endif

            @if($hasNeighborhoods)
                @if($hasSights) @endif
                in {{ implode(' and ', array_map(function($neighborhood) { 
                    return $neighborhood->Name; 
                }, $neighborhood_info->toArray())) }}
            @endif

            @if($hasAmenities)
                @if($hasSights || $hasNeighborhoods) @endif
               with {{ implode(' and ', array_map(function($amenity) { 
                    return $amenity->name; 
                }, $amenity_info->toArray())) }}
            @endif
    @endif
@endif

              @if($amenity !="")
                  with
                  @if($amenity == "breakfast")Free Breakfast
                  @elseif($amenity == "parking") Free Parking
                  @elseif($amenity == "free_cancellation")Free cancellation
                  @elseif($amenity =="Internet")Free internet
                  @endif
              @elseif($reviewscore !="")
                  with {{$reviewscore}}+ Review Score
              @elseif($price !="")
                  <?php
                      if (strpos($price, '-') !== false) {
                          $price_range = explode("-", $price);
                          $formatted_price = "$" . $price_range[0] . " and $" . $price_range[1];
                      } else {
                          $formatted_price = "$" . $price;
                      }
                  ?>
                  with a price range under {{$formatted_price}}
              @endif
            </h1>
            <h2>Compare prices from 70+ Hotels websites in just a single click</h2>
          </div>
          <!--HOTEL SEARCHES FORM- START-->
          <div class="tr-search-hotel">
            <form class="tr-hotel-form" id="hotelForm3">
              <div class="tr-form-section">
                <div class="tr-date-section">
                  <input type="text" class="tr-room-guest" placeholder="1 room, 2 guests" id="totalRoomAndGuest"
                    value="" name="" readonly="">
                  <div class="tr-add-edit-guest-count">
                    <div class="tr-guests-modal">
                      <div class="tr-add-edit-guest tr-total-num-of-rooms">
                        <div class="tr-guest-type">
                          <label class="tr-guest">Room</label>
                        </div>
                        <div class="tr-qty-box">
                          <button class="minus disabled" value="minus">-</button>
                          <input type="text" id="totalRoom" value="1" min="1" max="10" name="" readonly="">
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
                          <input type="text" id="totalAdultsGuest" value="2" min="1" max="10" name="" readonly="">
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
                          <input type="text" id="totalChildrenGuest" value="0" min="1" max="10" name="" readonly="">
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
                          <input type="text" id="totalChildrenInfants" value="0" min="1" max="10" name="" readonly="">
                          <button class="plus" value="plus">+</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="tr-form-fields">
                  <div class="col tr-mobile">
                    <div class="tr-mobile-where">
                      <label class="tr-lable">Where to?</label>
                      <div class="tr-location-label">Search destinations</div>
                    </div>
                  </div>
                  <div class="col tr-mobile">
                    <div class="tr-mobile-when">
                      <label class="tr-lable">When</label>
                      <div class="tr-add-dates">Add dates</div>
                    </div>
                  </div>
                  <div class="col tr-form-where">
                    <div class="tr-mobile tr-close-btn">Where are you going?</div>
                    <label for="searchDestinations">Where</label>

                    <input id="searchDestinations" type="hidden" tabindex="1" placeholder="&#xF002; Search"
                      autocomplete="off">
                    <input type="text" class="form-control fffffff" id="searchhotel"  value="@if($lname !=''){{$lname}}@endif" placeholder="Search Location"
                      name="" autocomplete="off">

                    <div class="tr-recent-searchs-modal" id="recentSearchsDestination">
                      <p id="hotel_loc_list" class="autoCompletewrapper tr-custom-scrollbar"></p>
                    </div>
                    <span id="slug" class="d-none">{{$slgid}}-{{$slugdata}}</span>
                    <span id="hotel" class="d-none">0</span>
                    <span id="location_id" class="d-none">{{$slgid}}</span>

                    <div class="tr-form-btn tr-mobile">
                      <button type="button" class="tr-btn">Countinue</button>
                    </div>
                  </div>
                  <?php date_default_timezone_set('Asia/Kolkata');

                      $checkinDate = date('Y-m-d', strtotime(' +1 day'));
                      $checkoutDate = date('Y-m-d', strtotime(' +4 day'));
                      ?>
                  <div class="col tr-form-booking-date">
                    <div class="tr-form-checkin">
                      <label for="checkInInput3">Check in</label>
                      <input type="text" value="{{ $checkinDate}}" class="form-control checkIn t-input-check-in"
                        id="checkInInput3" placeholder="Add dates" name="" autocomplete="off" readonly>
                    </div>
                    <div class="tr-form-checkout">
                      <label for="checkOutInput3">Check out</label>
                      <input type="text" value="{{ $checkoutDate}}" class="form-control checkOut t-input-check-out"
                        id="checkOutInput3" placeholder="Add dates" name="checkOut" autocomplete="off" readonly>
                    </div>
                    <div class="tr-calenders-modal" id="calendarsModal3" style="display: none">
                      <div id="calendarPair3" class="calendarPair">
                        <div class="navigation">
                          <button type="button" class="prevMonth" id="prevMonth3">Previous</button>
                          <button type="button" class="nextMonth" id="nextMonth3">Next</button>
                        </div>
                        <div class="custom-calendar checkInCalendar" id="checkInCalendar3">
                          <div class="monthYear"></div>
                          <div class="calendarBody"></div>
                        </div>
                        <div class="custom-calendar checkOutCalendar" id="checkOutCalendar3">
                          <div class="monthYear"></div>
                          <div class="calendarBody"></div>
                        </div>
                        <button type="button" class="tr-clear-details" hidden id="reset3">Clear dates</button>
                      </div>
                    </div>
                    <div class="col tr-form-btn">
                      <button type="button" class="tr-btn tr-mobile">Next</button>
                    </div>
                  </div>
                  <div class="col tr-form-who">
                    <label for="totalRoomAndGuest">Who</label>
                    <input type="text" class="form-control tr-total-room-and-guest" id="totalRoomAndGuest3"
                      placeholder="Add guests" name="" autocomplete="off" readonly>
                    <div class="tr-guests-modal" id="guestQtyModal">
                      <div class="tr-add-edit-guest tr-total-num-of-rooms">
                        <div class="tr-guest-type">
                          <label class="tr-guest">Room</label>
                        </div>
                        <div class="tr-qty-box">
                          <button class="minus disabled" value="minus">-</button>
                          <input type="text" id="totalRoom" value="1" id="" min="1" max="10" name="" readonly />
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
                          <input type="text" id="totalAdultsGuest" value="2" id="" min="1" max="10" name="" readonly />
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
                          <input type="text" id="totalChildrenGuest" value="0" id="" min="1" max="10" name=""
                            readonly />
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
                          <input type="text" id="totalChildrenInfants" value="0" id="" min="1" max="10" name=""
                            readonly />
                          <button class="plus" value="plus">+</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col tr-form-btn">
                  <button class="tr-btn tr-popup-btn filter-chackinouts" id=""><span class="tr-desktop">Get
                      Price</span><span class="tr-mobile">Search</span></button>
                </div>
              </div>
            </form>
          </div>
          <!--HOTEL SEARCHES FORM- START-->
          <!--PARTNERS - START-->
          <div class="tr-partners-section">
            <div class="tr-partners-title">70+ Partners :</div>
            <div class="tr-partners-lists">
              <div class="tr-partners-list">
                <img src="{{ asset('/frontend/hotel-detail/images/booking.png')}}" alt="Booking" />
              </div>
              <div class="tr-partners-list">
                <img src="{{ asset('/frontend/hotel-detail/images/expedia.png')}}" alt="expedia" />
              </div>
              <div class="tr-partners-list">
                <img src="{{ asset('/frontend/hotel-detail/images/agoda.png')}}" alt="agoda" />
              </div>
              <div class="tr-partners-list">
                <img src="{{ asset('/frontend/hotel-detail/images/trip.png')}}" alt="trip" />
              </div>
            </div>
          </div>
          <!--PARTNERS - end-->
          @endif
          @if($pagetype=="withdate")
          <?php            $chekin = request()->get('checkin');
                         $chkout = request()->get('checkout');


                          if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $chekin)) {
                              $checkin = $chekin;
                          } else {
                            $chekin =  str_replace('-',' ',$chekin);
                            $chekin = strtotime($chekin);
                            $checkin = date('Y-m-d', $chekin);


                          }


                          if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $chkout)) {
                              $checkout = $chkout;
                          } else {
                            $chkout =  str_replace('-',' ',$chkout);
                            $chkout = strtotime($chkout);
                            $checkout = date('Y-m-d', $chkout);
                          }
                          if(request('guest') == 0 || request('guest') ==" "){
                            $guest = 1;
                          }else{
                           $guest = request('guest');
                          }

                ?>

          <span id="withdate" class="d-none">withdate</span>
          <span id="Tplocid" class="d-none">{{$locationid}}</span>
          <span id="Cin" class="d-none">{{$checkin}}</span>
          <span id="Cout" class="d-none">{{$checkout}}</span>
          <span id="rooms" class="d-none">{{request('rooms')}}</span>
          <span id="guest" class="d-none">{{$guest}}</span>
          <span class="d-none Tid">{{$Tid}}</span>
          <span class="d-none slugid">{{$slgid}}</span>

          <span class="d-none lname" id="lname">{{$lname}}</span>
          <div class="tr-shortmap-and-shotby-section">
            <div class="tr-short-map">
              <img src="{{ asset('/frontend/hotel-detail/images/icons/map-pin-filled-black-icon.svg')}}"
                alt="map-pin" />
              <button class="tr-btn" data-bs-toggle="modal" data-bs-target="#mapModal">Show on map</button>
            </div>
            <div class="tr-title-filter-section">
              <div class="tr-row">
                 <h1 class="d-none d-md-block">{{$lname}} Hotels: <span class="hotel_count">{{ count(array_unique($searchresults->pluck('hotelid')->toArray())) }}</span></h1>
                <h1 class="d-block d-sm-block d-md-none">{{$lname}}: hotels &amp; places to stay</h1>
                <div class="tr-share-section">
                  <a href="javascript:void(0);" class="tr-share" data-bs-toggle="modal"
                    data-bs-target="#shareModal">Share</a>
                </div>
              </div>
              <div class="tr-row">
                <div class="tr-shotby">
                  <div class="custom-select">
                    <label>Sort by:</label>
                    <select class="hl-filter" id="sort_by">
                      <option value="">Relevance</option>
                      <option value="recommended">Recommended</option>
                      <option value="top-rated">Top-rated</option>
                      <option value="price_desc">Price: High to Low</option>
                      <option value="price_asc">Price: Low to High</option>
                    </select>
                  </div>
                </div>
                <div class="d-none d-md-block">
                  <div class="tr-filter-selected-section selected-data" data-section="1"></div>
                </div>
              </div>
            </div>
          </div>
          @endif
          <div class="tr-hotel-info-section">

            <!--Filter - START-->
            @if($pagetype=="withoutdate")
            <div class="tr-filters-section">
              <h4 class="tr-filter-label">Filter:</h4>
              <div class="tr-filter-lists">
                <h5>Search by</h5>
                <ul>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-st2' ) }}">2+ Star</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-st3' ) }}">3+ Star</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-st4' ) }}">4+ Star</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-st5') }}">5 Star</a></li>
				 </ul>
              </div>
              <div class="tr-filter-lists">
                <h5>Search by review score</h5>
               <ul>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-rs6') }}">6+ Okay</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-rs7') }}">7+ Good</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-rs8') }}">8+ Great</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-rs9') }}">9+ Excellent</a></li>
                </ul>
              </div>
              <div class="tr-filter-lists">
                <h5>Search by price</h5>
                 <ul>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-price50') }}">Cheap Hotels</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-price50') }}">Under $50</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-price100') }}">Under $100</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-price200') }}">Under $200</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-price799') }}">Luxury Hotels</a></li>
                </ul>
              </div>
              <div class="tr-filter-lists">
                <h5>Search by freebies</h5>
                <ul>
                   <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-free_cancellation') }}">Free cancellation</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-breakfast') }}">Free breakfast</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-parking') }}">Free
                      parking</a></li>
                  <li class="tr-filter-list"><a href="{{ url('ho-'.$slgid .'-'.$slugdata.'-Internet') }}">Free internet</a>
                  </li>
                </ul>
              </div>
            </div>

            @else
            <!-- without data -->
            <div class="tr-filters-section" data-section="1">
               <h4 class="tr-filter-label d-block d-sm-block d-md-none">Filters</h4>

              <div class="d-block d-sm-block d-md-none">
                <div class="tr-filter-selected-section selected-data" data-section="1"></div>
              </div>
              <div class="tr-filter-lists">
              <div style="display: flex; justify-content: space-between; align-items: baseline;">
   				 <h4 class="tr-filter-label filter-by-heading" style="margin: 0; padding: 0; line-height: 32px;">Filter by:</h4>
    				<a href="javascript:void(0)" id="clearAllFiltersBtn" onclick="clearAllFilters()" style="display: none; color: #0066cc; cursor: pointer; font-size: 16px; font-weight: 400; text-decoration: none; margin-left: 10px; position: relative; padding-right: 20px;">
                 		 Reset</a>
			  </div>
                 
 				<div class="tr-filter-lists">
  				  <h5>Popular Filters</h5>
    				<ul>
      				  <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="rating" id="" class="filter" value="5">5 Star<span class="checkmark"></span></label></li>
       				  <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="rating" id="" class="filter" value="4">4 Star<span class="checkmark"></span></label></li>
                 	  <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="guest_rating" id="" class="filter" value="8">Rated 80%<span class="checkmark"></span></label></li>
        			  <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="smnt" id="" class="filter" value="breakfast">Free Breakfast<span class="checkmark"></span></label></li>
      				   <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="smnt" id="" class="filter" value="refundable">Free Cancellation<span class="checkmark"></span></label></li>
                 	  <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="smnt" id="" class="filter" value="freeWifi">Wifi<span class="checkmark"></span></label></li>
     			      <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="mnt" id="" class="filter" value="Swimming Pool">Swimming Pool<span class="checkmark"></span></label></li>
   				   </ul>
				</div>
                 
                <h5 class="unique-h5" style="margin-top: 25px;">Pricing</h5>
                <div class="tr-price-graph">
                  <div class="tr-price-graph-col" style="height: 45px;"></div>
                  <div class="tr-price-graph-col" style="height: 64px;"></div>
                  <div class="tr-price-graph-col" style="height: 64px;"></div>
                  <div class="tr-price-graph-col" style="height: 73px;"></div>
                  <div class="tr-price-graph-col" style="height: 84px;"></div>
                  <div class="tr-price-graph-col" style="height: 76px;"></div>
                  <div class="tr-price-graph-col" style="height: 87px;"></div>
                  <div class="tr-price-graph-col" style="height: 81px;"></div>
                </div>
                <div class="tr-price-range-section">
                  <div class="tr-price-slider">
                    <input type="range" min="0" max="5000" value="0" class="min-range" step="1"  id="minRange">
                    <input type="range" min="0" max="5000" value="5000" class="max-range" step="1" id="maxRange">
                  </div>
                  <div class="tr-title min-price-title " id="minPrice">$0</div>
                  <div class="tr-title max-price-title " id="maxPrice">$5000</div>
                  <div class="tr-range-values">
                    <div class="min-price hl-filter">Min Price</div>
                    <span>-</span>
                    <div class="max-price hl-filter">Max Price</div>
                  </div>
                </div>
              </div>

 <div class="tr-filter-lists mnt">
                <h5>Facilities</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $facilityCount = 0; @endphp
                    @foreach([
                      ['name' => 'mnt', 'value' => 'Wi-Fi in areas', 'label' => 'Free Wi-Fi in areas'],
                      ['name' => 'smnt', 'value' => 'breakfast', 'label' => 'Free Breakfast'],
                      ['name' => 'smnt', 'value' => 'freeWifi', 'label' => 'Free Wifi'],
                      ['name' => 'mnt', 'value' => 'Parking', 'label' => 'Parking'],
                      ['name' => 'mnt', 'value' => 'Gym', 'label' => 'Gym'],
                      ['name' => 'mnt', 'value' => 'Laundry service', 'label' => 'Laundry service'],
                      ['name' => 'mnt', 'value' => 'Bar', 'label' => 'Bar'],
                      ['name' => 'mnt', 'value' => 'Restaurant/cafe', 'label' => 'Restaurant/cafe'],
                      ['name' => 'mnt', 'value' => 'Smoke-free', 'label' => 'Smoke-free'],
                      ['name' => 'mnt', 'value' => 'Wheel chair access', 'label' => 'Wheel chair access'],
                      ['name' => 'mnt', 'value' => '24h. Reception', 'label' => '24h. Reception']
                    ] as $facility)
                      @if($facilityCount < 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                        @php $facilityCount++; @endphp
                      @endif
                    @endforeach
                    @if(count([
                      ['name' => 'mnt', 'value' => 'Wi-Fi in areas', 'label' => 'Free Wi-Fi in areas'],
                      ['name' => 'smnt', 'value' => 'breakfast', 'label' => 'Free Breakfast'],
                      ['name' => 'smnt', 'value' => 'freeWifi', 'label' => 'Free Wifi'],
                      ['name' => 'mnt', 'value' => 'Parking', 'label' => 'Parking'],
                      ['name' => 'mnt', 'value' => 'Gym', 'label' => 'Gym'],
                      ['name' => 'mnt', 'value' => 'Laundry service', 'label' => 'Laundry service'],
                      ['name' => 'mnt', 'value' => 'Bar', 'label' => 'Bar'],
                      ['name' => 'mnt', 'value' => 'Restaurant/cafe', 'label' => 'Restaurant/cafe'],
                      ['name' => 'mnt', 'value' => 'Smoke-free', 'label' => 'Smoke-free'],
                      ['name' => 'mnt', 'value' => 'Wheel chair access', 'label' => 'Wheel chair access'],
                      ['name' => 'mnt', 'value' => '24h. Reception', 'label' => '24h. Reception']                    
                    ]) > 7)
                      <li class="tr-filter-list show-more-container">
                        <button type="button" class="tr-anchor-btn show-more-filters">Show more</button>
                      </li>
                    @endif
                  </ul>
                  <ul class="additional-filters" style="display:none;">
                    @foreach([
                      ['name' => 'mnt', 'value' => 'Wi-Fi in areas', 'label' => 'Free Wi-Fi in areas'],
                      ['name' => 'smnt', 'value' => 'breakfast', 'label' => 'Free Breakfast'],
                      ['name' => 'smnt', 'value' => 'freeWifi', 'label' => 'Free Wifi'],
                      ['name' => 'mnt', 'value' => 'Parking', 'label' => 'Parking'],
                      ['name' => 'mnt', 'value' => 'Gym', 'label' => 'Gym'],
                      ['name' => 'mnt', 'value' => 'Laundry service', 'label' => 'Laundry service'],
                      ['name' => 'mnt', 'value' => 'Bar', 'label' => 'Bar'],
                      ['name' => 'mnt', 'value' => 'Restaurant/cafe', 'label' => 'Restaurant/cafe'],
                      ['name' => 'mnt', 'value' => 'Smoke-free', 'label' => 'Smoke-free'],
                      ['name' => 'mnt', 'value' => 'Wheel chair access', 'label' => 'Wheel chair access'],
                      ['name' => 'mnt', 'value' => '24h. Reception', 'label' => '24h. Reception']                    
                    ] as $index => $facility)
                      @if($index >= 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                      @endif
                    @endforeach
                    <li class="tr-filter-list show-more-container">
                      <button type="button" class="tr-anchor-btn show-less-filters">Show less</button>
                    </li>
                  </ul>
                </div>
              </div>

                                 <div class="tr-filter-lists mnt">
                <h5>Room Facilities</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $facilityCount = 0; @endphp
                    @foreach([
                      ['name' => 'mnt', 'value' => 'A/C', 'label' => 'A/C'],
                      ['name' => 'mnt', 'value' => 'Private Bathroom', 'label' => 'Private Bathroom'],
                      ['name' => 'mnt', 'value' => 'TV', 'label' => 'TV'],
                      ['name' => 'mnt', 'value' => 'Balcony/terrace', 'label' => 'Balcony/terrace'],
                      ['name' => 'mnt', 'value' => 'Bathtub', 'label' => 'Bathtub'],
                      ['name' => 'mnt', 'value' => 'Handicapped Room', 'label' => 'Handicapped Room'],
                      ['name' => 'mnt', 'value' => 'Inhouse movies', 'label' => 'Inhouse movies'],
                      ['name' => 'mnt', 'value' => 'Mini bar', 'label' => 'Mini bar']
                    ] as $facility)
                      @if($facilityCount < 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                        @php $facilityCount++; @endphp
                      @endif
                    @endforeach
                    @if(count([
                      ['name' => 'mnt', 'value' => 'A/C', 'label' => 'A/C'],
                      ['name' => 'mnt', 'value' => 'Private Bathroom', 'label' => 'Private Bathroom'],
                      ['name' => 'mnt', 'value' => 'TV', 'label' => 'TV'],
                      ['name' => 'mnt', 'value' => 'Balcony/terrace', 'label' => 'Balcony/terrace'],
                      ['name' => 'mnt', 'value' => 'Bathtub', 'label' => 'Bathtub'],
                      ['name' => 'mnt', 'value' => 'Handicapped Room', 'label' => 'Handicapped Room'],
                      ['name' => 'mnt', 'value' => 'Inhouse movies', 'label' => 'Inhouse movies'],
                      ['name' => 'mnt', 'value' => 'Mini bar', 'label' => 'Mini bar']
                    ]) > 7)
                      <li class="tr-filter-list show-more-container">
                        <button type="button" class="tr-anchor-btn show-more-filters">Show more</button>
                      </li>
                    @endif
                  </ul>
                  <ul class="additional-filters" style="display:none;">
                    @foreach([
                      ['name' => 'mnt', 'value' => 'A/C', 'label' => 'A/C'],
                      ['name' => 'mnt', 'value' => 'Private Bathroom', 'label' => 'Private Bathroom'],
                      ['name' => 'mnt', 'value' => 'TV', 'label' => 'TV'],
                      ['name' => 'mnt', 'value' => 'Balcony/terrace', 'label' => 'Balcony/terrace'],
                      ['name' => 'mnt', 'value' => 'Bathtub', 'label' => 'Bathtub'],
                      ['name' => 'mnt', 'value' => 'Handicapped Room', 'label' => 'Handicapped Room'],
                      ['name' => 'mnt', 'value' => 'Inhouse movies', 'label' => 'Inhouse movies'],
                      ['name' => 'mnt', 'value' => 'Mini bar', 'label' => 'Mini bar']
                    ] as $index => $facility)
                      @if($index >= 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                      @endif
                    @endforeach
                    <li class="tr-filter-list show-more-container">
                      <button type="button" class="tr-anchor-btn show-less-filters">Show less</button>
                    </li>
                  </ul>
                </div>
              </div>

              <div class="tr-filter-lists star-rating">
                <h5>Hotel class</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $starCount = 0; @endphp
                    @foreach([5, 4, 3, 2] as $star)
                      @if($starCount < 7)
                        <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox" name="rating"
                              id="" class="filter" value="{{ $star }}">{{ $star }} Star<span class="checkmark"></span></label></li>
                        @php $starCount++; @endphp
                      @endif
                    @endforeach
                  </ul>
                </div>
              </div>
                 
                           <div class="tr-filter-lists">
                <h5>Guest Ratings</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $ratingCount = 0; @endphp
                    @foreach([['9.5', 'Superb 95% +'], ['9', 'Excellent 90%'], ['8', 'Great 80%'], ['7', 'Good 70%'], ['6', 'Okay 60%'], ['5', 'Average 50%'], ['4', 'Poor 40%'], ['3', 'Disappointing 30%'], ['2', 'Bad 20%']] as $rating)
                      @if($ratingCount < 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="guest_rating" class="filter" value="{{ $rating[0] }}">
                                {{ $rating[1] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                        @php $ratingCount++; @endphp
                      @endif
                    @endforeach
                    @if(count([['9.5', 'Superb 95% +'], ['9', 'Excellent 90%'], ['8', 'Great 80%'], ['7', 'Good 70%'], ['6', 'Okay 60%'], ['5', 'Average 50%'], ['4', 'Poor 40%'], ['3', 'Disappointing 30%'], ['2', 'Bad 20%']]) > 7)
                      <li class="tr-filter-list show-more-container">
                        <button type="button" class="tr-anchor-btn show-more-filters">Show more</button>
                      </li>
                    @endif
                  </ul>
                  <ul class="additional-filters" style="display:none;">
                    @foreach([['9.5', 'Superb 95% +'], ['9', 'Excellent 90%'], ['8', 'Great 80%'], ['7', 'Good 70%'], ['6', 'Okay 60%'], ['5', 'Average 50%'], ['4', 'Poor 40%'], ['3', 'Disappointing 30%'], ['2', 'Bad 20%']] as $index => $rating)
                      @if($index >= 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="guest_rating" class="filter" value="{{ $rating[0] }}">
                                {{ $rating[1] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                      @endif
                    @endforeach
                    <li class="tr-filter-list show-more-container">
                      <button type="button" class="tr-anchor-btn show-less-filters">Show less</button>
                    </li>
                  </ul>
                </div>
            </div>
                 
              @if(!$gethoteltype->isEmpty())
              <div class="tr-filter-lists hoteltype">
                <h5>Property types</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $typeCount = 0; @endphp
                    @foreach($gethoteltype as $val)
                      @if($typeCount < 7)
                        <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox"
                              name="hoteltypes" id="" class="filter" value="{{$val->hid}}">{{$val->type}}<span
                              class="checkmark"></span></label></li>
                        @php $typeCount++; @endphp
                      @endif
                    @endforeach
                    @if(count($gethoteltype) > 7)
                      <li class="tr-filter-list show-more-container">
                        <button type="button" class="tr-anchor-btn show-more-filters">Show more</button>
                      </li>
                    @endif
                  </ul>
                  <ul class="additional-filters" style="display:none;">
                    @foreach($gethoteltype as $index => $val)
                      @if($index >= 7)
                        <li class="tr-filter-list hl-filter"><label class="tr-check-box"><input type="checkbox"
                              name="hoteltypes" id="" class="filter" value="{{$val->hid}}">{{$val->type}}<span
                              class="checkmark"></span></label></li>
                      @endif
                    @endforeach
                    <li class="tr-filter-list show-more-container">
                      <button type="button" class="tr-anchor-btn show-less-filters">Show less</button>
                    </li>
                  </ul>
                </div>
              </div>
              @endif

            <div class="tr-filter-lists mnt">
                <h5>Reservation Policy</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $facilityCount = 0; @endphp
                    @foreach([
                      ['name' => 'smnt', 'value' => 'breakfast', 'label' => 'Breakfast'],
                      ['name' => 'smnt', 'value' => 'refundable', 'label' => 'Refundable'],
                      ['name' => 'smnt', 'value' => 'cardRequired', 'label' => 'Card Required']
                    ] as $facility)
                      @if($facilityCount < 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                        @php $facilityCount++; @endphp
                      @endif
                    @endforeach
                    @if(count([
                      ['name' => 'smnt', 'value' => 'breakfast', 'label' => 'Breakfast'],
                      ['name' => 'smnt', 'value' => 'refundable', 'label' => 'Refundable'],
                      ['name' => 'smnt', 'value' => 'cardRequired', 'label' => 'Card Required']
                    ]) > 7)
                      <li class="tr-filter-list show-more-container">
                        <button type="button" class="tr-anchor-btn show-more-filters">Show more</button>
                      </li>
                    @endif
                  </ul>
                  <ul class="additional-filters" style="display:none;">
                    @foreach([
                      ['name' => 'smnt', 'value' => 'breakfast', 'label' => 'Breakfast'],
                      ['name' => 'smnt', 'value' => 'refundable', 'label' => 'Refundable'],
                      ['name' => 'smnt', 'value' => 'cardRequired', 'label' => 'Card Required']
                    ] as $index => $facility)
                      @if($index >= 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                      @endif
                    @endforeach
                    <li class="tr-filter-list show-more-container">
                      <button type="button" class="tr-anchor-btn show-less-filters">Show less</button>
                    </li>
                  </ul>
                </div>
              </div>

            <div class="tr-filter-lists mnt">
                <h5>Fun Things To Do</h5>
                <div class="filter-items-container">
                  <ul>
                    @php $facilityCount = 0; @endphp
                    @foreach([
                      ['name' => 'mnt', 'value' => 'Bicycle rental', 'label' => 'Bicycle rental'],
                      ['name' => 'mnt', 'value' => 'Tours', 'label' => 'Tours'],
                      ['name' => 'mnt', 'value' => 'Sauna', 'label' => 'Sauna'],
                      ['name' => 'mnt', 'value' => 'Water Sports', 'label' => 'Water Sports']
                    ] as $facility)
                      @if($facilityCount < 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                        @php $facilityCount++; @endphp
                      @endif
                    @endforeach
                    @if(count([
                      ['name' => 'mnt', 'value' => 'Bicycle rental', 'label' => 'Bicycle rental'],
                      ['name' => 'mnt', 'value' => 'Tours', 'label' => 'Tours'],
                      ['name' => 'mnt', 'value' => 'Sauna', 'label' => 'Sauna'],
                      ['name' => 'mnt', 'value' => 'Water Sports', 'label' => 'Water Sports']
                    ]) > 7)
                      <li class="tr-filter-list show-more-container">
                        <button type="button" class="tr-anchor-btn show-more-filters">Show more</button>
                      </li>
                    @endif
                  </ul>
                  <ul class="additional-filters" style="display:none;">
                    @foreach([
                      ['name' => 'mnt', 'value' => 'Bicycle rental', 'label' => 'Bicycle rental'],
                      ['name' => 'mnt', 'value' => 'Tours', 'label' => 'Tours'],
                      ['name' => 'mnt', 'value' => 'Sauna', 'label' => 'Sauna'],
                      ['name' => 'mnt', 'value' => 'Water Sports', 'label' => 'Water Sports']
                    ] as $index => $facility)
                      @if($index >= 7)
                        <li class="tr-filter-list hl-filter">
                            <label class="tr-check-box">
                                <input type="checkbox" name="{{ $facility['name'] }}" class="filter" value="{{ $facility['value'] }}">
                                {{ $facility['label'] }}
                                <span class="checkmark"></span>
                            </label>
                        </li>
                      @endif
                    @endforeach
                    <li class="tr-filter-list show-more-container">
                      <button type="button" class="tr-anchor-btn show-less-filters">Show less</button>
                    </li>
                  </ul>
                </div>
              </div>                                
                               
              <!--Neighbourhoods-->

              <div class="tr-filter-lists agencies" id="agencies">
                @if(!empty($agencyData))
                <h5>Booking Providers</h5>
                  <ul>
                      @foreach($agencyData as $agency)
                          <li class="tr-filter-list hl-filter">
                              <label class="tr-check-box">
                                  <input type="checkbox" name="agency" class="filter" value="{{ $agency }}">
                                  {{ $agency }}
                                  <span class="checkmark"></span>
                              </label>
                          </li>
                      @endforeach
                  </ul>
                @endif
              </div>
			                <div class="tr-filter-lists nearby">
    <h5>Near by</h5>
    <ul class="nearby-main-list">
        @if(!empty($nearbyPlaces))
            @php $firstFour = $nearbyPlaces->take(7) @endphp
            @foreach($firstFour as $place)
                <li class="tr-filter-list">
                    <label class="tr-check-box">
                        <input type="checkbox" name="nearby[]" class="filter" value="{{ $place->SightId }}"
                            @if(request()->has('nearby') && in_array($place->SightId, explode(',', request('nearby')))) checked @endif>
                        <span class="form-check-label">{{ $place->Title }}</span>
                        <span class="checkmark"></span>
                    </label>
                </li>
            @endforeach
            @if(count($nearbyPlaces) > 7)
                <li class="tr-filter-list show-more-container">
                    <button type="button" class="tr-anchor-btn show-more-nearby">Show more</button>
                </li>
            @endif
        @endif
    </ul>
</div>
              <!-- <div class="tr-filter-lists">
                <h5>Other</h5>
                <ul>
                  <li class="tr-filter-list"><label class="tr-check-box"><input type="checkbox" name="" id=""
                        class="filter" value="Properties without prices">Properties without prices<span
                        class="checkmark"></span></label></li>
                  <li class="tr-filter-list"><label class="tr-check-box"><input type="checkbox" name="" id=""
                        class="filter" value="Properties without photos">Properties without photos<span
                        class="checkmark"></span></label></li>
                </ul>
              </div> -->
                <div class="d-block d-sm-block d-md-none" style="margin-top: 20px;">
              <button class="tr-show-all-btn" onclick="$('.tr-filters-section').slideUp(); $('.hotel-listing-section').show(); $('body').removeClass('modal-open'); $('html, body').animate({scrollTop: $('.hotel-listing-section').offset().top - 20}, 300);" style="background-color: #FF6B00; color: white; font-family: inherit; font-size: inherit; border-radius: 5px; padding: 8px 16px; border: none; width: 100%;">
                Show all <span class="hotel_count"></span> hotels
              </button>
            </div>                     
            </div>
            @endif
            <!--Filter - END-->
            <!--ROOM - START-->
            <div class="tr-room-section-2 filter-listing responsive-container" data-withdate="{{ $pagetype == 'withdate' ? 'true' : 'false' }}">
                
              @if($pagetype=="withoutdate")
              <div class="tr-title-filter-section">
                <div class="tr-row">
                  <span class="d-none slugdata">{{$slugdata}}</span>
                  <span class="d-none slugid">{{$slgid}}</span>
                  <span class="d-none lname">{{$lname}}</span>
                  <span class="d-none filter-st">{{$st}}</span>
                  <span class="d-none filter-amenity">{{$amenity}}</span>
                  <span class="d-none filter-rs">{{$reviewscore}}</span>
                  <span class="d-none filter-price">{{$price}}</span>
@php
    $hasAmenities = !empty($amenity_ids) && isset($amenity_info) && count($amenity_info) > 0;
    $hasNeighborhoods = !empty($neighborhood_info) && count($neighborhood_info) > 0;
    $hasSights = !empty($sight_info) && isset($sight_info['sight_name']);
    $hasPropertyTypes = !empty($propertyType_ids) && isset($propertyType_info) && count($propertyType_info) > 0;
@endphp
                <h1 class="d-none d-md-block">
                Showing 
    @if($st !="") {{$st}} star @endif 
    @if($hasPropertyTypes && !$hasNeighborhoods && !$hasSights && !$hasAmenities && $amenity == "")
        {{ implode(', ', array_map(function($propertyType) { return $propertyType->type; }, $propertyType_info->toArray())) }} in {{$lname}}
    @else
        hotels in {{$lname}} 
    @endif

@if(!$getlocationexp->isEmpty())
    @if($pagetype == 'withoutdate' && ($hasAmenities || $hasNeighborhoods || $hasSights))
            @if($hasSights)
                near {{ implode(' and ', array_map(function($sight) { 
                    return $sight->name; 
                }, $sight_info->toArray())) }}
            @endif

            @if($hasNeighborhoods)
                @if($hasSights) @endif
                in {{ implode(' and ', array_map(function($neighborhood) { 
                    return $neighborhood->Name; 
                }, $neighborhood_info->toArray())) }}
            @endif

            @if($hasAmenities)
                @if($hasSights || $hasNeighborhoods) with @endif
                {{ implode(' and ', array_map(function($amenity) { 
                    return $amenity->name; 
                }, $amenity_info->toArray())) }}
            @endif
    @endif
@endif

    @if($amenity !="")
        with 
        @if($amenity == "breakfast")Free Breakfast 
        @elseif($amenity == "parking") Free Parking 
        @elseif($amenity == "free_cancellation")Free cancellation 
        @elseif($amenity =="Internet")Free internet 
        @endif
    @elseif($reviewscore !="")
        with {{$reviewscore}}+ Review Score     
    @elseif($price !="")    
        <?php 
            if (strpos($price, '-') !== false) {
                $price_range = explode("-", $price);
                $formatted_price = "$" . $price_range[0] . " and $" . $price_range[1];
            } else {                        
                $formatted_price = "$" . $price;
            } 
        ?>
        with a price range under {{$formatted_price}}        
    @endif
</h1>

                  <h1 class="d-block d-sm-block d-md-none">Top hotels</h1>
                  <div class="tr-share-section">
                    <a href="javascript:void(0);" class="tr-share" data-bs-toggle="modal"
                      data-bs-target="#shareModal">Share</a>
                  </div>
                </div>
                <!--
                <div class="tr-row">
                  <p>{{$count_result}} results found</p>
                </div>
                -->
              </div>
              @endif
              @if($pagetype=="withdate")
              <!-- Loader that shows immediately while results are being fetched -->
              <div id="loader" style="position: relative; width: 100%; z-index: 1000; display: block;">
                <!-- Repeating the placeholder structure to simulate multiple hotel listings -->
                @for($i = 0; $i < 5; $i++)
                <div class="tr-hotel-deatils" data-type="no-data">
                    <div class="tr-hotal-image">
                        <div class="tr-no-data-text animated-bg-1 w-100 h-230"></div>
                    </div>
                    <div class="tr-hotel-deatil">
                        <div class="tr-heading-with-rating">
                            <div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div>
                            <div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div>
                        </div>
                        <div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div>
                        <div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div>
                        <div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div>
                        <div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div>
                    </div>
                    <div class="tr-hotel-price-section">
                        <div class="tr-hotel-price-lists">
                            <div class="tr-hotel-price-list">
                                <div class="tr-row mb-12">
                                    <div class="tr-no-data-text animated-bg-1 w-50 h-15"></div>
                                    <div class="tr-no-data-text animated-bg-1 w-25 h-15"></div>
                                </div>
                                <div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div>
                                <div class="tr-row">
                                    <div class="tr-no-data-text animated-bg-1 w-30 h-20"></div>
                                    <div class="tr-no-data-text animated-bg-1 w-50 h-20"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endfor
              </div>
              <style>
              .animated-bg-1 {
                  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
                  background-size: 200% 100%;
                  animation: shimmer 1.5s ease-in-out infinite;
              }
              @keyframes shimmer {
                  0% { background-position: 200% 0; }
                  100% { background-position: -200% 0; }
              }
              </style>
              <!-- Hotel loader script will be loaded from external file -->
              @endif
              
              @if(!$searchresults->isEmpty())
              <?php $a = 1;?>
              @foreach($searchresults as $searchresult)
              <div class="tr-hotel-deatils" data-id="{{ $searchresult->id }}" style="{{ $pagetype == 'withdate' ? 'display: none;' : '' }}">
              @if($pagetype=="withdate")
              <?php    $ctName = $lname;
                                $cityname = str_replace(' ', '_', $ctName);
                                $CountryName = str_replace(' ', '_', $countryname);
                                $url = $cityname .'-'.$CountryName;
                                $hotel_url = url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug."?checkin={$checkin}&checkout={$checkout}") )) );
                          ?>
                <div class="tr-hotal-image">
                  <div id="roomSlider{{$a}}" class="carousel slide" data-bs-touch="false" data-bs-interval="false">
                    <!-- Indicators/dots -->
                    <div class="carousel-indicators">
                      <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="0"
                        class="active">1</button>
                      <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="1">2</button>
                      <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="2">3</button>
                    </div>
                    <!-- The slideshow/carousel -->
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}" target="_blank" title="{{$searchresult->name}}"><img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_0/520/460.jpg" alt="{{$searchresult->name}}"></a>
                      </div>
                      <div class="carousel-item">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}" target="_blank" title="{{$searchresult->name}}"><img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_1/520/460.jpg" alt="{{$searchresult->name}}"></a>
                      </div>
                      <div class="carousel-item">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}" target="_blank" title="{{$searchresult->name}}"><img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_2/520/460.jpg" alt="{{$searchresult->name}}"></a>
                      </div>
                    </div>
                    <!-- Left and right controls/icons -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                  </div>
                  <button class="tr-anchor-btn tr-save">Save</button>
                </div>

              @else
               <!-- without date--->
              <div class="tr-hotal-image">
                  <div id="roomSlider{{$a}}" class="carousel slide" data-bs-touch="false" data-bs-interval="false">
                    <!-- Indicators/dots -->
                    <div class="carousel-indicators">
                      <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="0"
                        class="active">1</button>
                      <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="1">2</button>
                      <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="2">3</button>
                    </div>
                    <!-- The slideshow/carousel -->
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}"
                      target="_blank" title="{{$searchresult->name}}"> <img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_0/520/460.jpg" alt="{{$searchresult->name}}"></a>
                      </div>
                      <div class="carousel-item">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}"
                      target="_blank" title="{{$searchresult->name}}"><img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_1/520/460.jpg" alt="{{$searchresult->name}}"></a>
                      </div>
                      <div class="carousel-item">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}"
                      target="_blank" title="{{$searchresult->name}}"><img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_2/520/460.jpg" alt="{{$searchresult->name}}"></a>
                      </div>
                    </div>
                    <!-- Left and right controls/icons -->
                    <button class="carousel-control-prev" type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide="prev"><span class="carousel-control-prev-icon"></span></button>
                    <button class="carousel-control-next" type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide="next"><span class="carousel-control-next-icon"></span></button>
                  </div>
                  <button class="tr-anchor-btn tr-save">Save</button>
                </div>
                @endif
                @if($pagetype=="withdate")
<?php    
    $ctName = $lname;
    $cityname = str_replace(' ', '_', $ctName);
    $CountryName = str_replace(' ', '_', $countryname);
    $url = $cityname .'-'.$CountryName;
    $hotel_url = url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!', $searchresult->slug."?checkin={$checkin}&checkout={$checkout}")) ));
?>
<a href="{{ $hotel_url }}" class="tr-hotel-detail-link" target="_blank" title="View details for {{ $searchresult->name }}">
  				  <div class="tr-hotel-deatil">
                  <div class="tr-heading-with-rating">
                    <h2>
                      <span>{{ $searchresult->name }}</span>
                    </h2>
                    <div class="tr-rating">
                      @for ($i = 0; $i < 5; $i++)
                        @if($i < $searchresult->stars )
                        <span class="tr-star">
                          <img src="{{asset('/frontend/hotel-detail/images/icons/star-fill-icon.svg')}}">
                        </span>
                        @endif
                      @endfor
                    </div>
                  </div>
                @if($searchresult->CityName != "")
                  <div class="tr-hotel-location">
                      <a href="{{ request()->fullUrlWithQuery(['location' => urlencode($searchresult->CityName)]) }}"
                        title="{{ $searchresult->CityName }}"
                        style="color: #333333; font-size: 14px; font-weight: 500; text-decoration: none;">
                          {{ $searchresult->CityName }}
                      </a>
                  </div>
                  @endif

                  <div class="tr-like-review">
                    @if($searchresult->rating !="" && $searchresult->rating !=0)
                    <?php

                        $rating = (float)$searchresult->rating;
                         $result = round($rating * 10);

                          if ($result > 95) {
                            $ratingtext = 'Superb';
                            $color = '#29857A';
                            $bgcolor = 'rgba(41, 133, 122, 0.11)';

                        } elseif ($result >= 91 && $result <= 95) {
                            $ratingtext = 'Excellent';
                            $color = '#29857A';
                            $bgcolor = 'rgba(41, 133, 122, 0.11)';
                        } elseif ($result >= 81 && $result <= 90) {
                            $ratingtext = 'Great';
                            $color = '#29857A';
                            $bgcolor = 'rgba(41, 133, 122, 0.11)';
                        } elseif ($result >= 71 && $result <= 80) {
                            $ratingtext = 'Good';
                            $color = '#FFE135';
                            $bgcolor = '#fafab2';
                        } elseif ($result >= 61 && $result <= 70) {
                            $ratingtext = 'Okay';
                            $color = '#FFE135';
                            $bgcolor = '#fafab2';
                        } elseif ($result >= 51 && $result <= 60) {
                            $ratingtext = 'Average';
                            $color = '#FFE135';
                            $bgcolor = '#fafab2';
                        } elseif ($result >= 41 && $result <= 50) {
                            $ratingtext = 'Poor';
                            $color = 'red';
                            $bgcolor = '#ff000026';
                        } elseif ($result >= 21 && $result <= 40) {
                            $ratingtext = 'Disappointing';
                            $color = 'red';
                            $bgcolor = '#ff000026';
                        } else {
                            $ratingtext = 'Bad';
                            $color = 'red';
                            $bgcolor = '#ff000026';
                        }

                      ?>
                    <div class="tr-heart">
                      <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z"
                          fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                    </div>

                    <div class="tr-ranting-percent">{{$result}}% </div>
                    <div class="tr-vgood" style="color:{{$color}};background: {{$bgcolor}};">{{$ratingtext}}</div>
                    @endif
                  </div>
          <div class="tr-hotel-facilities">
    <?php
        // Define an array to map each selected amenity to its specific icon path
        $amenityIconPaths = [
            'A/C' => '/frontend/hotel-detail/images/amenities/heating.svg',
            'Parking' => '/frontend/hotel-detail/images/amenities/Parking.svg',
            'Wi-Fi' => '/frontend/hotel-detail/images/amenities/Wi-Fi.svg',
            'Laundry' => '/frontend/hotel-detail/images/amenities/Laundry.svg',
            'Smoke-free' => '/frontend/hotel-detail/images/amenities/Smoke-free.svg',
            'Pool' => '/frontend/hotel-detail/images/amenities/Pool.svg',
            'Gym' => '/frontend/hotel-detail/images/amenities/Gym.svg',
            'Food' => '/frontend/hotel-detail/images/amenities/Food.svg',
            'Bar' => '/frontend/hotel-detail/images/amenities/Bar.svg',
            'Spa' => '/frontend/hotel-detail/images/amenities/Spa.svg',
            // Add additional amenities as needed
        ];

        // Define your selected amenities by name
        $selectedAmenities = array_keys($amenityIconPaths); // Fetch keys directly from icon paths

        $amenities = [];
        if ($searchresult->amenity_info != "") {
            $amenityData = explode(',', $searchresult->amenity_info);
            foreach ($amenityData as $item) {
                if (strpos($item, '|') !== false) {
                    list($name, $available) = explode('|', $item);
                    $name = trim($name);
                    $available = (int) trim($available);

                    // Only include amenities from the selected list
                    if (in_array($name, $selectedAmenities)) {
                        $amenities[] = [
                            'name' => $name,
                            'available' => $available,
                        ];
                    }
                }
            }

            // Remove duplicates and limit to 5
            $uniqueAmenities = [];
            foreach ($amenities as $amenity) {
                if (!in_array($amenity['name'], array_column($uniqueAmenities, 'name'))) {
                    $uniqueAmenities[] = $amenity;
                }
            }
            $uniqueAmenities = array_slice($uniqueAmenities, 0, 5); // Limit to the first 5 amenities
        }
    ?>

    <!-- Display Amenities on the Page -->
    @if (!empty($uniqueAmenities))
        <ul>
           @foreach ($uniqueAmenities as $mnt)
            <li>
              @php
                // Assign icon path from predefined list; if unavailable, use a default
                $iconPath = $amenityIconPaths[$mnt['name']] ?? '/frontend/hotel-detail/images/amenities/wifi.svg';
              @endphp
                    <img src="{{ asset($iconPath) }}" alt="{{ $mnt['name'] }}">
                    <span>{{ $mnt['name'] }}</span> <!-- Display the amenity name -->
            </li>
          @endforeach
        </ul>
    @endif
</div>
                <div class="tr-more-facilities">
                    @if(!empty($searchresult->short_description))
                        <ul class="short-description-content">
                            <li>{{ $searchresult->short_description }}</li>
                        </ul>

                        @if(strlen($searchresult->short_description) > 100) <!-- Show "Read More" if the description is long -->
                            <button type="button" class="tr-anchor-btn toggle-list" onclick="toggleContent(this)">Read More</button>
                        @endif
                    @endif
                </div>
                 </div>
</a>
              
                <div class="tr-hotel-price-section">
                  <!--
                  <div class="tr-deal tr-offer-alert">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path
                        d="M13.7263 8.9387L8.94634 13.7187C8.82251 13.8427 8.67546 13.941 8.5136 14.0081C8.35173 14.0752 8.17823 14.1097 8.00301 14.1097C7.82779 14.1097 7.65429 14.0752 7.49242 14.0081C7.33056 13.941 7.18351 13.8427 7.05967 13.7187L1.33301 7.9987V1.33203H7.99967L13.7263 7.0587C13.9747 7.30851 14.1141 7.64645 14.1141 7.9987C14.1141 8.35095 13.9747 8.68888 13.7263 8.9387Z"
                        stroke="#222222" stroke-linecap="round" stroke-linejoin="round" />
                      <path d="M4.66699 4.66797H4.67366" stroke="#222222" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    #1 Best value of 400 places to stay
                  </div>
                  -->
                  <div class="tr-hotel-price-lists ls">
                    @if(!empty($hotels->result))
                    @foreach ($hotels->result as $hotel_result)
                    @if($hotel_result->id == $searchresult->hotelid)
                    <?php
                            $allPrices = [];

                            foreach ($hotel_result->rooms as $room) {
                                $price = $room->total;
                                $agencyId = $room->agencyId;
                                $fullurl = $room->fullBookingURL;
                                $options = $room->options;

                                // Use a unique key to prevent duplicate entries
                                $key = $price . '_' . $agencyId;

                                if (!isset($allPrices[$key])) {
                                    $allPrices[$key] = [
                                        'price' => $price,
                                        'fullBookingURL' => $fullurl,
                                        'agencyId' => $agencyId,
                                        'options' => $options,
                                    ];
                                }
                            }

                            // Convert associative array to indexed array
                            $allPricesArray = array_values($allPrices);

                            // Sort the prices in ascending order
                            usort($allPricesArray, function($a, $b) {
                                return $a['price'] - $b['price'];
                            });

                            // Split into top two and the rest
                            $topTwoPrices = array_slice($allPricesArray, 0, 2);
                            $remainingPrices = array_slice($allPricesArray, 2);
                        ?>

                    <!-- Show the two lowest prices -->
                    @foreach ($topTwoPrices as $data)
                    <div class="tr-hotel-price-list">
                      <div class="tr-row">
                        <div class="tr-hotel-facilities">
                          <ul>
                            <?php
                                            $count = 0;
                                            $priorities = ['breakfast', 'freeWifi'];
                                            foreach ($priorities as $priority) {
                                                if (!empty($data['options']->{$priority})) {
                                                    echo "<li>" . ucfirst($priority) . " included</li>";
                                                    $count++;
                                                }
                                            }
                                            foreach ($data['options'] as $key => $value) {
                                                if ($value === true && !in_array($key, $priorities)) {
                                                    echo "<li>" . ucfirst(str_replace('_', ' ', $key)) . " included</li>";
                                                    $count++;
                                                }
                                                if ($count == 2) break;
                                            }
                                        ?>
                          </ul>
                        </div>
                        <div class="tr-site-details">
                          <img src="{{ 'https://pics.avs.io/hl_gates/100/40/' . $data['agencyId'] . '.png' }}"
                            alt="agency logo">
                        </div>
                      </div>
                      <div class="tr-row">
                        <div class="tr-action" @if($count==1 || $count==0) style="margin-top: 18px;" @endif>
                          <a href="{{ $data['fullBookingURL'] }}" class="tr-btn" target="_blank">View deal</a>
                        </div>
                        <div class="tr-hotel-price"><strong>${{ $data['price'] }}</strong></div>
                      </div>
                    </div>
                    @endforeach

                    <!-- Show remaining prices under "More Price" -->
                    @if(count($remainingPrices) > 0)
                    <div class="more-prices-containers" style="display: none;">
                      @foreach ($remainingPrices as $data)
                      <div class="tr-hotel-price-list">
                        <div class="tr-row">
                          <div class="tr-hotel-facilities">
                            <ul>
                              <?php
                                                $count = 0;
                                                $priorities = ['breakfast', 'freeWifi'];
                                                foreach ($priorities as $priority) {
                                                    if (!empty($data['options']->{$priority})) {
                                                        echo "<li>" . ucfirst($priority) . " included</li>";
                                                        $count++;
                                                    }
                                                }
                                                foreach ($data['options'] as $key => $value) {
                                                    if ($value === true && !in_array($key, $priorities)) {
                                                        echo "<li>" . ucfirst(str_replace('_', ' ', $key)) . " included</li>";
                                                        $count++;
                                                    }
                                                    if ($count == 2) break;
                                                }
                                            ?>
                            </ul>
                          </div>
                          <div class="tr-site-details">
                            <img src="{{ 'https://pics.avs.io/hl_gates/100/40/' . $data['agencyId'] . '.png' }}"
                              alt="agency logo">
                          </div>
                        </div>
                        <div class="tr-row">
                          <div class="tr-action" @if($count==1 || $count==0) style="margin-top: 18px;" @endif>
                            <a href="{{ $data['fullBookingURL'] }}" class="tr-btn" target="_blank">View deal</a>
                          </div>
                          <div class="tr-hotel-price"><strong>${{ $data['price'] }}</strong></div>
                        </div>
                      </div>
                      @endforeach
                    </div>
                    <button class="tr-more-prices ls tr-anchor-btn">View More Offers</button>
                    @endif

                    @endif
                    @endforeach
                    @endif
                  </div>
                </div>
                @else
                <!-- without date start -->
                <div class="tr-hotel-deatil">
                  <div class="tr-heading-with-rating">
                    <h2 class="hotel-name">
                      <a href="{{ url('hd-'.$searchresult->slugid .'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}"
                        target="_blank" title="{{$searchresult->name}}">{{$searchresult->name}}</a>
                    </h2>
                    <div class="tr-rating">
                      @for ($i = 0; $i < 5; $i++) @if($i < $searchresult->stars )
                        <span class="tr-star"><img
                            src="{{asset('/frontend/hotel-detail/images/icons/star-fill-icon.svg')}}"></span>

                        @endif
                      @endfor
                    </div>
                  </div>
              @if($searchresult->CityName != "")
              <div class="tr-hotel-location">
                  <a href="?location={{ urlencode($searchresult->CityName) }}" title="{{ $searchresult->CityName }}">
                      {{ $searchresult->CityName }}
                  </a>
              </div>
              @endif

                  
                  
                  
                  <div class="tr-like-review">
                  @if($searchresult->rating !="" && $searchresult->rating !=0)
                    <?php

                        $rating = (float)$searchresult->rating;
                         $result = round($rating * 10);

                          if ($result > 95) {
                            $ratingtext = 'Superb';
                            $color = '#29857A';
                            $bgcolor = 'rgba(41, 133, 122, 0.11)';

                        } elseif ($result >= 91 && $result <= 95) {
                            $ratingtext = 'Excellent';
                            $color = '#29857A';
                            $bgcolor = 'rgba(41, 133, 122, 0.11)';
                        } elseif ($result >= 81 && $result <= 90) {
                            $ratingtext = 'Great';
                            $color = '#29857A';
                            $bgcolor = 'rgba(41, 133, 122, 0.11)';
                        } elseif ($result >= 71 && $result <= 80) {
                            $ratingtext = 'Good';
                            $color = '#FFE135';
                            $bgcolor = '#fafab2';
                        } elseif ($result >= 61 && $result <= 70) {
                            $ratingtext = 'Okay';
                            $color = '#FFE135';
                            $bgcolor = '#fafab2';
                        } elseif ($result >= 51 && $result <= 60) {
                            $ratingtext = 'Average';
                            $color = '#FFE135';
                            $bgcolor = '#fafab2';
                        } elseif ($result >= 41 && $result <= 50) {
                            $ratingtext = 'Poor';
                            $color = 'red';
                            $bgcolor = '#ff000026';
                        } elseif ($result >= 21 && $result <= 40) {
                            $ratingtext = 'Disappointing';
                            $color = 'red';
                            $bgcolor = '#ff000026';
                        } else {
                            $ratingtext = 'Bad';
                            $color = 'red';
                            $bgcolor = '#ff000026';
                        }

                      ?>
                    <div class="tr-heart">
                      <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd"
                          d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z"
                          fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                      </svg>
                    </div>

                    <div class="tr-ranting-percent">{{$result}}% </div>
                    <div class="tr-vgood" style="color:{{$color}};background: {{$bgcolor}};">{{$ratingtext}}</div>
                    @endif
                  </div>

                  <div class="accordion" id="accordion{{$a}}">
                    <div class="accordion-items">
                      <div class="accordion-item">
                        <button id="headingOverview{{$a}}" class="" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapseOne{{$a}}" aria-expanded="true"
                          aria-controls="collapseOne{{$a}}">Overview</button>
                      </div>
                      <div class="accordion-item">
                        <button id="headingAmenities{{$a}}" class="" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapseTwo{{$a}}" aria-expanded="false"
                          aria-controls="collapseTwo{{$a}}">Amenities</button>
                      </div>
                      <div class="accordion-item">
                        <button id="headingThree{{$a}}" class="collapsed" type="button" data-bs-toggle="collapse"
                          data-bs-target="#collapseThree{{$a}}" aria-expanded="false"
                          aria-controls="collapseThree{{$a}}">Review</button>
                      </div>
                    </div>
                    <div class="accordion-items-content">
                      <div id="collapseOne{{$a}}" class="accordion-collapse collapse show"
                        aria-labelledby="headingOverview{{$a}}" data-bs-parent="#accordion{{$a}}">
                        <div class="tr-more-facilities list-content" style="display: block !important; visibility: visible !important;">
    @if(!empty($searchresult->OverviewShortDesc))
        <?php
            $OverviewShortDesc = explode(',', $searchresult->OverviewShortDesc);
            // Filter out any empty or whitespace-only strings from the array
            $OverviewShortDesc = array_filter($OverviewShortDesc, function($value) {
                return !empty(trim($value));
            });
            // Reindex the array after filtering
            $OverviewShortDesc = array_values($OverviewShortDesc);
        ?>
        <p class="short-description-content overviewText">
            @foreach($OverviewShortDesc as $index => $data)
                <!-- Remove any extra quotes or brackets, just display the text -->
                • {{ trim($data, " '[]") }} <!-- Trim spaces, quotes, and square brackets -->
                @if(!$loop->last)
                    <br> <!-- Line break between items to create paragraph-style bullets -->
                @endif
            @endforeach
        </p>
        <button type="button" class="custom-read-more readMoreBtn" onclick="toggleContent(this)">Read More</button>
    @endif
</div>

                      </div>
                      <div id="collapseTwo{{$a}}" class="accordion-collapse collapse"
                        aria-labelledby="headingAmenities{{$a}}" data-bs-parent="#accordion{{$a}}">
                       <div class="tr-hotel-facilities">
                          <?php
                            $amenities = [];
                            if ($searchresult->amenity_info != "") {
                              $amenityData = explode(',', $searchresult->amenity_info);
                              foreach ($amenityData as $item) {
                                if (strpos($item, '|') != false) {
                                  list($name, $available) = explode('|', $item);
                                  $amenities[] = [
                                    'name' => trim($name),
                                    'available' => (int) trim($available),
                                  ];
                                }
                              }
                              $amenities = array_slice($amenities, 0, 5);
                            }
                          ?>
                          @if (!empty($amenities))
                            <ul>
                               @foreach ($amenities as $mnt)
                                <li>
                                  @php
                                    // Assign icon path from predefined list; if unavailable, use a default
                                    $sanitizedName = str_replace(['/', '\\'], '', trim($mnt['name']));
                                    $imagePath = '/frontend/hotel-detail/images/amenities/'.$sanitizedName.'.svg';
                                    $lowerImagePath = '/frontend/hotel-detail/images/amenities/'.strtolower($sanitizedName).'.svg';
                                    $fileExists = file_exists(public_path($imagePath));
                                    $lowerFileExists = file_exists(public_path($lowerImagePath));
                                  @endphp
                                  @if($fileExists)
                                    <img src="{{ asset($imagePath) }}" class="{{ $mnt['available'] == 1 ? 'active' : 'inactive' }}">
                                  @elseif($lowerFileExists)
                                    <img src="{{ asset($lowerImagePath) }}" class="{{ $mnt['available'] == 1 ? 'active' : 'inactive' }}">
                                  @else
                                    <img src="{{ asset('/frontend/hotel-detail/images/amenities/wifi.svg') }}" class="{{ $mnt['available'] == 1 ? 'active' : 'inactive' }}">
                                  @endif
                                  <span>{{ $mnt['name'] }}</span>
                                </li>
                              @endforeach
                            </ul>
                          @endif
                        </div>
                      </div>
                                    
                                    @if($searchresults->isNotEmpty())
    @foreach($searchresults as $hotel)  {{-- Change $searchresult to $hotel to be clear --}}
        {{-- After the hotel name, before the ratings section --}}
        @if(!empty($amenity_ids) && isset($amenity_info))
           
        @endif

        @if(!empty($neighborhood_ids) && isset($neighborhood_info))
           
        @endif
		 @if(!empty($sight_hotel_ids) && isset($sight_info))
           
        @endif
    @endforeach
@else
    <div class="no-results">
        <p>No hotels found matching your selected filters.</p>
    </div>
@endif          
                                    
                                    
                      <div id="collapseThree{{$a}}" class="accordion-collapse collapse"
                        aria-labelledby="headingThree{{$a}}" data-bs-parent="#accordion{{$a}}">
                        <!--
                        <div class="tr-like-review">
                          <div class="tr-vgood">Very Good</div> (100 Review)
                        </div>
                        -->
                        <div class="tr-short-decs paragraph-content">
                          <div class="para-content">
                            <p>{{$searchresult->ReviewSummary}}</p>
                          </div>
                          <button type="button" class="tr-anchor-btn toggle-para">Read More</button>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!--
                  <div class="d-block d-sm-block d-md-none tr-mobile">
                    <div class="tr-more-facilities">
                      <ul>
                        @if($searchresult->distance !="")<li>{{$searchresult->distance}} miles to city </li>@endif
                      </ul>
                    </div>
                    <div class="tr-hotel-facilities mt-3">
                    @if (!empty($amenities))
                      <ul>
                        @foreach ($amenities as $mnt)
                          <li>
                            <span>{{ $mnt['name'] }}</span>
                          </li>
                        @endforeach
                      </ul>
                    @endif
                    </div>
                  </div>
                  -->
                  <div class="tr-view-availability">
                   <button class="tr-btn tr-view-availability-btn"><span class="d-none d-md-block">Enter dates for price</span>
                       <span class="d-block d-sm-block d-md-none">View availability</span></button>
                  </div>
                </div>
                @endif

                @if ($loop->last && $count_result > 1)
                @if (!session()->has('frontend_user'))
                <div class="tr-login-for-more-options">
                  <h2>Log in/Sign up to view all listings</h2>
                  <p>Compare prices from 70+ Hotels websites all at one place</p>
                  <div class="tr-row">
                    <a href="{{route('user_login')}}"><button type="button" class="tr-btn h-sign-up">Sign
                        up</button></a>
                  </div>
                </div>
                @endif
                @endif
              </div>
              <?php $a++;?>
              @endforeach
              @else
              <div class="spinner-border" style="margin-left: 500px;margin-top: 100px;" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              @endif
            </div>
            <!--ROOM - END-->
          </div>
          <div class="tr-map-and-filter">
            <button data-bs-toggle="modal" data-bs-target="#mapModal" class="map"><svg width="14" height="14"
                viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_2464_12970)">
                  <path
                    d="M0.583984 3.4974V12.8307L4.66732 10.4974L9.33398 12.8307L13.4173 10.4974V1.16406L9.33398 3.4974L4.66732 1.16406L0.583984 3.4974Z"
                    stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                  <path d="M4.66602 1.16406V10.4974" stroke="white" stroke-width="1.2" stroke-linecap="round"
                    stroke-linejoin="round" />
                  <path d="M9.33398 3.5V12.8333" stroke="white" stroke-width="1.2" stroke-linecap="round"
                    stroke-linejoin="round" />
                </g>
                <defs>
                  <clipPath id="clip0_2464_12970">
                    <rect width="14" height="14" fill="white" />
                  </clipPath>
                </defs>
              </svg>Map</button>
           <button id="filterModal" class="filter" onclick="$('.tr-filters-section').slideDown(); $('body').addClass('modal-open');"><svg width="14" height="14" viewBox="0 0 14 14" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path d="M12.8327 1.75H1.16602L5.83268 7.26833V11.0833L8.16602 12.25V7.26833L12.8327 1.75Z"
                  stroke="white" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
              </svg>Filter</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Map Modal With Filter & Hotel List - Start-->
  <div class="modal" id="mapModal">
    <div class="modal-dialog">
      <div class="modal-content">
         <button type="button" class="btn-close" data-bs-dismiss="modal" style="position: absolute; top: 10px; right: 10px; z-index: 1050;"></button>
        <div class="modal-body">
          <div class="tr-hotel-info-section">
            <div class="tr-filters-section" data-section="2">
              <div class="tr-filter-selected-section selected-data" data-section="2"></div>
            </div>
            <div class="tr-room-section-2"></div>
            <div class="tr-map-section">
              <button class="tr-hide-list" style="position: absolute; top: 10px; right: 10px; z-index: 9999;">Hide Hotel List</button>
              <div class="tr-hotel-on-map">
                <form>
                  <!--input type="text" class="form-control" id="" placeholder="Search on map" name="" autocomplete="off"-->
                  <div class="tr-recent-searchs-modal" id="">
                    <div class="tr-enable-location">Around Current Location</div>
                    <h5>Recent searches</h5>
                    <ul>
                      <li>
                        <div class="tr-place-info">
                          <div class="tr-location-icon"></div>
                          <div class="tr-location-info">
                            <div class="tr-hotel-name">London Hotels</div>
                            <div class="tr-hotel-city">England, United Kingdom</div>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="tr-place-info">
                          <div class="tr-location-icon"></div>
                          <div class="tr-location-info">
                            <div class="tr-hotel-name">Morocco</div>
                            <div class="tr-hotel-city">North Africa</div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <button type="button" hidden class="tr-btn">Countinue</button>
                </form>
              </div>
              <button id="onMapFilterModal" class="filter tr-mobile"><svg width="20" height="20" viewBox="0 0 20 20"
                  fill="none" xmlns="http://www.w3.org/2000/svg">
                  <path d="M18.3337 2.5H1.66699L8.33366 10.3833V15.8333L11.667 17.5V10.3833L18.3337 2.5Z" stroke="black"
                    stroke-linecap="round" stroke-linejoin="round" />
                </svg>Filter</button>
                <div id="map"></div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
  <!-- Map Modal With Filter & Hotel List - End-->
	                                               
                                              
<div class="container">
                                              
              <!--City Wise Hotel and Price - START-->
              @if($pagetype == "withoutdate" && isset($hotelAmenities) && count($hotelAmenities) > 0)
              <div class="tr-city-wise-hotels-section">
              <h3>Search for places to stay by destination</h3>
              <div class="tr-sub-title">Find Accommodation</div>
              <div class="tr-city-wise-hotel-listing">
                <!-- First Column -->
                <div class="tr-city-wise-hotel-lists">
                  <!-- Hotels with Amenities Section -->
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with Amenities</div>
                    <div class="tr-hotel-lists">
                      @foreach($hotelAmenities as $amenity)
                        @if(isset($amenity['name']) && isset($amenity['url']) && isset($amenity['code']))
                          <div class="tr-hotel-list">
                            <div class="tr-hotel-name">
                              <a href="{{ $amenity['url'] }}" title="Hotels with {{ $amenity['name'] }}" target="_blank" rel="noopener">Hotels with {{ $amenity['name'] }}</a>
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                  
                  <!-- 5-Star Hotels Section -->
                  @if(isset($fiveStarHotels) && count($fiveStarHotels) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with 5 Stars</div>
                    <div class="tr-hotel-lists">
                      @foreach($fiveStarHotels as $hotel)
                        <div class="tr-hotel-list">
                          <div class="tr-hotel-name">
                            <a href="{{ $hotel['url'] }}" title="{{ $hotel['name'] }}" target="_blank" rel="noopener">{{ $hotel['name'] }}</a>
                          </div>
                          
                        </div>
                      @endforeach
                    </div>
                  </div>
                  @endif
                  
                  <!-- 4-Star Hotels Section -->
                  @if(isset($fourStarHotels) && count($fourStarHotels) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with 4 Stars</div>
                    <div class="tr-hotel-lists">
                      @foreach($fourStarHotels as $hotel)
                        <div class="tr-hotel-list">
                          <div class="tr-hotel-name">
                            <a href="{{ $hotel['url'] }}" title="{{ $hotel['name'] }}" target="_blank" rel="noopener">{{ $hotel['name'] }}</a>
                          </div>
                          
                        </div>
                      @endforeach
                    </div>
                  </div>
                  @endif
                </div>
                
                <!-- Second Column -->
                <div class="tr-city-wise-hotel-lists">
                  <!-- Hotels with Neighborhoods Section -->
                  @if(isset($hotelNeighborhoods) && count($hotelNeighborhoods) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with Neighborhoods</div>
                    <div class="tr-hotel-lists">
                      @foreach($hotelNeighborhoods as $neighborhood)
                        @if(isset($neighborhood['name']) && isset($neighborhood['url']) && isset($neighborhood['code']))
                          <div class="tr-hotel-list">
                            <div class="tr-hotel-name">
                              <a href="{{ $neighborhood['url'] }}" title="Hotels in {{ $neighborhood['name'] }}" target="_blank" rel="noopener">Hotels in {{ $neighborhood['name'] }}</a>
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                  @endif
                  
                  <!-- 3-Star Hotels Section -->
                  @if(isset($threeStarHotels) && count($threeStarHotels) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with 3 Stars</div>
                    <div class="tr-hotel-lists">
                      @foreach($threeStarHotels as $hotel)
                        <div class="tr-hotel-list">
                          <div class="tr-hotel-name">
                            <a href="{{ $hotel['url'] }}" title="{{ $hotel['name'] }}" target="_blank" rel="noopener">{{ $hotel['name'] }}</a>
                          </div>
                          
                        </div>
                      @endforeach
                    </div>
                  </div>
                  @endif
                  
                  <!-- 2-Star Hotels Section -->
                  @if(isset($twoStarHotels) && count($twoStarHotels) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with 2 Stars</div>
                    <div class="tr-hotel-lists">
                      @foreach($twoStarHotels as $hotel)
                        <div class="tr-hotel-list">
                          <div class="tr-hotel-name">
                            <a href="{{ $hotel['url'] }}" title="{{ $hotel['name'] }}" target="_blank" rel="noopener">{{ $hotel['name'] }}</a>
                          </div>                        
                        </div>
                      @endforeach
                    </div>
                  </div>
                  @endif
                </div>
                
                <!-- Third Column -->
                <div class="tr-city-wise-hotel-lists">
                  <!-- Popular Sections -->
                  @if(isset($popularSections) && count($popularSections) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Popular Sections</div>
                    <div class="tr-hotel-lists">
                      @foreach($popularSections as $section)
                        @if(isset($section['name']) && isset($section['url']) && isset($section['code']))
                          <div class="tr-hotel-list">
                            <div class="tr-hotel-name">
                              <a href="{{ $section['url'] }}" title="Hotels in {{ $section['name'] }}" target="_blank" rel="noopener">Hotels in {{ $section['name'] }}</a>
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                  @endif
                  
                  <!-- 1-Star Hotels Section (Budget Accommodations) -->
                  @if(isset($oneStarHotels) && count($oneStarHotels) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Budget Accommodations Near You</div>
                    <div class="tr-hotel-lists">
                      @foreach($oneStarHotels as $hotel)
                        <div class="tr-hotel-list">
                          <div class="tr-hotel-name">
                            <a href="{{ $hotel['url'] }}" title="{{ $hotel['name'] }}" target="_blank" rel="noopener">{{ $hotel['name'] }}</a>
                          </div>
                          
                        </div>
                      @endforeach
                    </div>
                  </div>
                  @endif
                  
                  <!-- Hotels with Nearby Attractions -->
                  @if(isset($nearbySights) && count($nearbySights) > 0)
                  <div class="tr-city-wise-hotel-list">
                    <div class="tr-city-name">Hotels with Nearby Attractions</div>
                    <div class="tr-hotel-lists">
                      @foreach($nearbySights as $sight)
                        @if(isset($sight['name']) && isset($sight['url']) && isset($sight['code']))
                          <div class="tr-hotel-list">
                            <div class="tr-hotel-name">
                              <a href="{{ $sight['url'] }}" title="Hotels near {{ $sight['name'] }}" target="_blank" rel="noopener">Hotels near {{ $sight['name'] }}</a>
                            </div>
                          </div>
                        @endif
                      @endforeach
                    </div>
                  </div>
                  @endif
                </div>
              </div>
            </div>
            @endif                                 
                                              
    <!--Budget Hotels - START-->
    @if(isset($neabyhotelwithswimingpool) && !$neabyhotelwithswimingpool->isEmpty())
          <div class="tr-more-places tr-budget-hotels-near-you responsive-container">
              <div class="tr-heading-with-see-all">
                <h3>Budget Hotels near {{ $location_info->Name }}</h3>		
                <a href="javascript:void(0);" class="tr-see-all">See All</a>
              </div>
              <h6>Hotels with Swimming pool</h6>
              <div class="row tr-more-places-lists tr-budget-slider">
                @foreach($neabyhotelwithswimingpool as $nbs)
                <div class="tr-more-places-list">
                  <div class="tr-hotel-img">
                    <a href="{{ url('hd-'.$nbs->slugid .'-' .$nbs->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$nbs->slug) )) ) }}" target="_blank">
                      <img loading="lazy" src="https://photo.hotellook.com/image_v2/crop/h{{ $nbs->hotelid }}_0/520/460.jpg" alt="NearBy Hotel" height="222" width="319">
                    </a>
                  </div>
                  <div class="tr-hotel-deatils">
                    <div class="tr-hotel-city">{{$nbs->Lname}}</div>
                    <div class="tr-hotel-name"><a href="{{ url('hd-'.$nbs->slugid .'-' .$nbs->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$nbs->slug) )) ) }}" target="_blank">{{$nbs->name}}</a></div>
                  @if($nbs->pricefrom !="")<div class="tr-price">Start from <strong>${{$nbs->pricefrom}}</strong> <span class="tr-night">/night</span></div>@endif
                 @if(!empty($nbs->OverviewShortDesc))
                        <?php
                            $OverviewShortDesc = explode(',', $nbs->OverviewShortDesc);
                            // Filter out any empty or whitespace-only strings from the array
                            $OverviewShortDesc = array_filter($OverviewShortDesc, function($value) {
                                return !empty(trim($value));
                            });
                            // Reindex the array after filtering
                            $OverviewShortDesc = array_values($OverviewShortDesc);
                        ?>
                        <div class="tr-hotel-facilities">
                            <ul>
                            @foreach($OverviewShortDesc as $index => $datas)
                                <li>{{ trim($datas, " '[]") }}</li> 
                            @endforeach
                            </ul>
                            <button class="tr-anchor-btn toggle-list budget-more" title="Read More">Read More</button>
                        </div>
                    @endif

                    @if($nbs->rating != "" && $nbs->rating != 0) <div class="tr-likes">
                      <span class="tr-heart">
                      <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                
                      </span>@if($nbs->rating != "" && $nbs->rating != 0)
                                  <?php $ratingcount = round((float) $nbs->rating * 10); ?>
                                  {{$ratingcount}}%
                            
                              @endif

                    </div>@endif
                  </div>
                </div>
                @endforeach
              </div>
          </div>
          @endif 
            <!--Budget Hotels - END-->                                   
                                              
 @if(isset($location_info) && $location_info)
    <div class="row">
        <div class="col-sm-12">
            <div class="tr-single-page">
                <div class="tr-terms-and-conditions-section">
                    <h3 style="font-weight: bold; margin-bottom: 20px; font-size: 24px;">
                        {{ $location_info->heading}}
                    </h3>
                    <p style="margin-top: 20px;">
                        {{ $location_info->headingcontent}}
                    </p>
                </div>
            </div>
        </div>
    </div>
@endif                                      
                      
    <div class="row">
        <div class="col-sm-12">
            <div class="tr-single-page">
                <div class="tr-terms-and-conditions-section">
                    <h3 style="font-weight: bold; margin-bottom: 20px; font-size: 24px;">{{ $location_info->Name }} Hotel Pricing Guide</h3>
                    <p style="margin-top: 20px; margin-bottom: 20px;"> The average hotel cost in {{ $location_info->Name }} is ${{ $pricingStats['averageNightlyRate']['min'] }}-${{ $pricingStats['averageNightlyRate']['max'] }} per night across all categories, with significant variation by star rating, location, and season. Budget travelers can find quality options starting from ${{ number_format($pricingStats['priceRange']['min']) }}, while luxury experiences reach ${{ number_format($pricingStats['priceRange']['max']) }}+ per night.</p>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Key Market Statistics:</h4>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Total Hotels Available: Over {{ number_format($pricingStats['totalHotels']) }} hotel operators citywide</li>
                        <li>Average Nightly Rate: ${{ $pricingStats['averageNightlyRate']['min'] }}-${{ $pricingStats['averageNightlyRate']['max'] }} (varies by source and methodology)</li>
                        <li>Price Range: ${{ $pricingStats['priceRange']['min'] }}-${{ number_format($pricingStats['priceRange']['max']) }}+ per night depending on category and season</li>
                    </ul>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Hotel Pricing by Star Rating</h4>
                    <p style="margin-top: 10px; margin-bottom: 15px;">Our comprehensive analysis of 70+ booking partners reveals distinct pricing tiers:</p>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">5-Star Luxury Hotels</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Average Rate: ${{ $pricingStats['byStarRating'][5]['averageRate']['min'] }}-${{ $pricingStats['byStarRating'][5]['averageRate']['max'] }} per night</li>
                        <li>Peak Season: Can exceed $1,000/night</li>
                        @if(!empty($popularNeighborhoods))
                        <li>Popular Areas: {{ $popularNeighborhoods }}</li>
                        @endif
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">4-Star Premium Hotels</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Average Rate: ${{ $pricingStats['byStarRating'][4]['averageRate']['min'] }}-${{ $pricingStats['byStarRating'][4]['averageRate']['max'] }} per night</li>
                        <li>Notable Options: Include major chains like Hilton, Sheraton, InterContinental</li>
                        <li>Value Proposition: High-end amenities without luxury pricing</li>
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">3-Star Mid-Range Hotels</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Average Rate: ${{ $pricingStats['byStarRating'][3]['averageRate']['min'] }}-${{ $pricingStats['byStarRating'][3]['averageRate']['max'] }} per night</li>
                        <li>Sweet Spot: Best balance of comfort and value</li>
                        <li>Range: $153-$333 per night depending on location</li>
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">2-Star Budget Hotels</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Average Rate: ${{ $pricingStats['byStarRating'][2]['averageRate']['min'] }}-${{ $pricingStats['byStarRating'][2]['averageRate']['max'] }} per night</li>           
                        <li>Best Value: Significant savings while maintaining essential amenities</li>
                        <li>Popular Choices: Extended Stay, Holiday Inn Express, local motels</li>
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">1-Star/Hostel Accommodations</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Average Rate: ${{ $pricingStats['byStarRating'][1]['averageRate']['min'] }}-${{ $pricingStats['byStarRating'][1]['averageRate']['max'] }} per night</li>
                        <li>Budget Champion: Perfect for backpackers and extended stays</li>
                    </ul>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Weekly Rate Patterns</h4>
                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">Weekday Advantages:</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Sunday: Cheapest night of the week</li>
                        <li>Monday-Thursday: Consistent lower rates</li>
                        <li>Business Travel: Less competition for rooms</li>
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">Weekend Premiums:</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Friday-Saturday: 25-50% rate increases</li>
                        <li>Event Weekends: Can see 100%+ premiums</li>
                        <li>Booking Strategy: Consider Sunday-Thursday stays</li>
                    </ul>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Money-Saving Strategies</h4>
                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">Booking Optimization:</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Advance Booking: Weekday rates and shoulder seasons offer best value</li>
                        <li>Promotional Codes: Leverage seasonal discounts and limited-time offers</li>
                        <li>Group Bookings: Extended stays often include additional perks</li>
                        <li>Direct Contact: Hotels may offer unpublished rates when contacted directly</li>
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">Location Strategy:</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Transit Access: Choose neighborhoods with excellent public transport</li>
                        <li>Distance Trade-off: Save 30-50% by staying 15-20 minutes from main attractions</li>
                        <li>Emerging Areas: Consider up-and-coming neighborhoods for value</li>
                    </ul>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Additional Considerations</h4>
                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">Amenity-Based Pricing:</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Pool Hotels: Premium during summer months</li>
                        <li>Parking Included: Valuable in high-traffic areas</li>
                        <li>Airport Shuttle: Convenient for LAX travelers</li>
                        <li>Pet-Friendly: Limited options command premiums</li>
                    </ul>

                    <h5 style="font-weight: bold; margin-top: 20px; margin-bottom: 10px; font-size: 18px;">Market Trends for 2025:</h5>
                    <ul style="list-style-type: disc; margin-left: 20px; margin-bottom: 20px;">
                        <li>Eco-Friendly Practices: LEED-certified properties gaining popularity</li>
                        <li>Smart Technology: Enhanced room automation and personalized services</li>
                        <li>Experience Focus: Hotels offering curated local experiences</li>
                        <li>Flexible Cancellation: Increasingly important post-pandemic feature</li>
                    </ul>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Conclusion</h4>
                    <p style="margin-top: 10px; margin-bottom: 15px;">{{ $location_info->Name }} offers exceptional hotel diversity across all price points, from ${{ $pricingStats['priceRange']['min'] }} budget motels to ${{ $pricingStats['priceRange']['max'] }}+ luxury resorts. Smart travelers can find quality accommodations by being flexible with location, timing, and amenities. Whether seeking Hollywood glamour, beachfront relaxation, or urban exploration, LA's hotel market provides options for every traveler and budget.</p>

                    <h4 style="font-weight: bold; margin-top: 30px; margin-bottom: 15px; font-size: 20px;">Pro Tip:</h4>
                    <p style="margin-top: 10px; margin-bottom: 15px;">Book Tuesday-Thursday stays in shoulder seasons for optimal value, and consider neighborhoods like Koreatown or East Hollywood for authentic LA experiences at budget-friendly rates.</p>
                </div>
            </div>
        </div>
    </div>
                                              
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
          @if(!empty($locationPatent))
          <?php
                $locationPatents = array_reverse($locationPatent);
                ?>
          @foreach ($locationPatents as $location)
          <li>
            <a
              href="@if(!empty($location)){{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}@endif">
              {{ $location['Name'] }}</a>
          </li>
          @endforeach
          @endif
          @if(!$getlocationexp->isEmpty())
          <li><a
              href="{{ route('search.results', [$getlocationexp[0]->slugid.'-'.strtolower($getlocationexp[0]->Slug)]) }}">{{$getlocationexp[0]->Name}}</a>
          </li>
          @endif
         @if(!$getlocationexp->isEmpty())
    @if((!empty($sight_info) && count($sight_info) > 0) || 
        (!empty($neighborhood_info) && count($neighborhood_info) > 0) || 
        (!empty($amenity_info) && count($amenity_info) > 0))
        <li>
            <a href="{{ route('search.results', [$getlocationexp[0]->slugid.'-'.strtolower($getlocationexp[0]->Slug)]) }}">
                {{$getlocationexp[0]->Name}} Hotels
            </a>
        </li>
    @else
        <li>{{$getlocationexp[0]->Name}} Hotels</li>
    @endif
@endif
@if(!$getlocationexp->isEmpty())
    @if($pagetype == 'withoutdate' && 
        ((!empty($sight_info) && count($sight_info) > 0) || 
        (!empty($neighborhood_info) && count($neighborhood_info) > 0) || 
        (!empty($amenity_info) && count($amenity_info) > 0)))
        <li>          
            {{$getlocationexp[0]->Name}} Hotels
            @if(!empty($sight_info) && count($sight_info) > 0)
                near {{ $sight_info->pluck('Title')->implode(', ') }}
            @endif
            @if(!empty($neighborhood_info) && count($neighborhood_info) > 0)
                @if(!empty($sight_info) && count($sight_info) > 0)
                @endif
                in {{ $neighborhood_info->pluck('Name')->implode(', ') }}
            @endif
            @if(!empty($amenity_info) && count($amenity_info) > 0)
                @if(!empty($sight_info) || !empty($neighborhood_info))
                    with
                @endif
                {{ $amenity_info->pluck('name')->implode(', ') }}
            @endif
        </li>
    @endif
@endif
      </ul>
    </div>

      <!-- end date and breadcrumb -->
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
          <div class="tr-share-details">
            <span class="tr-hotel-name">
				<h4 class="d-none d-md-block">{{$lname}}: <span class="hotel_count"></span></h4>
                <h4 class="d-block d-sm-block d-md-none">{{$lname}}: hotels &amp; places to stay</h4>
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
<!-- Nearby Places Modal -->
<div class="tr-modal-overlay" id="nearbyModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; justify-content: center; align-items: center;">
      <div class="tr-modal" style="position: relative; max-height: 90vh; overflow-y: auto; margin: auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1); width: 90%; max-width: 900px;">
          <div class="tr-modal-header" style="padding: 15px 20px; border-bottom: 1px solid #eee; position: relative;">
              <h5 style="margin: 0;">Nearby Places</h5>
              <button type="button" class="tr-modal-close" style="position: absolute; right: 15px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; font-size: 24px; color: #666;">←</button>
          </div>
          <div class="tr-search-box" style="padding: 15px 20px; border-bottom: 1px solid #eee;">
              <div class="search-input-wrapper">
                  <input type="text" id="nearbySearch" placeholder="Search nearby places..." class="tr-search-input">
                  <i class="fas fa-search search-icon"></i>
              </div>
          </div>
          <div class="tr-modal-body" style="padding: 20px;">
              <div class="tr-nearby-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 10px;">
                  @if(!empty($nearbyPlaces))
                      @foreach($nearbyPlaces->take(36) as $place)
                          <div class="tr-nearby-item" style="padding: 5px 15px;">
                              <label class="tr-check-box">
                                  <input type="checkbox" name="nearby[]" class="filter modal-filter" value="{{ $place->SightId }}"
                                      @if(request()->has('nearby') && in_array($place->SightId, explode(',', request('nearby')))) checked @endif>
                                  <span class="form-check-label">{{ $place->Title }}</span>
                                  <span class="checkmark"></span>
                              </label>
                          </div>
                      @endforeach
                  @endif
              </div>
          </div>
          <div class="tr-modal-footer" style="padding: 15px 20px; border-top: 1px solid #eee;">
              <button type="button" class="tr-btn tr-btn-secondary tr-modal-reset" style="float: left; padding: 5px 15px; font-size: 13px; background: #ff6b00; color: white; border: none; border-radius: 4px; cursor: pointer;">Reset</button>
              <button type="button" class="tr-btn tr-btn-primary tr-modal-apply" style="float: right; padding: 5px 15px; font-size: 13px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">Apply</button>
              <div style="clear: both;"></div>
          </div>
      </div>
</div>
<!-- end of modal -->
</div>  
<script src="{{ asset('js/hotel-loader.js') }}"></script>
</body>

</html>
<script src="{{asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js')}} "></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/custom.js')}} "></script>
<script src="{{asset('/js/hotel_list.js')}} "></script>
<script src="{{ asset('/js/custom.js')}}"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
@if($pagetype !="withoutdate")
<script>
  document.addEventListener("DOMContentLoaded", function() {
    var defaultCenter = [20.5937, 78.9629]; // Default fallback center (India)
    var defaultZoom = 5;

    var mapCenter = defaultCenter;
    var mapZoom = defaultZoom;
    @if($searchresults->isNotEmpty() && $searchresults->first()->Latitude && $searchresults->first()->longnitude)
      mapCenter = [{{ $searchresults->first()->Latitude }}, {{ $searchresults->first()->longnitude }}];
      mapZoom = 12;
    @endif

    var map = L.map('map', {
      center: mapCenter,
      zoom: mapZoom
    });

    var layer = new L.TileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
      subdomains: 'abcd',
      maxZoom: 19
    });
    map.addLayer(layer);

    // Custom marker with price and arrow display
    var kayakIconWithArrow = function(price, highlight = false) {
      const bgColor = highlight ? '#ff4d01' : 'white'; // Background color change on hover
      const size = highlight ? [80, 55] : [70, 50]; // Adjust size on hover
      return L.divIcon({
        className: 'kayak-div-icon',
        html: `
          <div class="marker-wrapper" style="background: ${bgColor}; border-radius: 12px; border: 1px solid #ccc; padding: 10px; width: ${size[0]}px; height: ${size[1] - 15}px; display: flex; align-items: center; justify-content: center; box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.15); position: relative;">
            <div class="price-label" style="font-weight: bold; font-size: 16px; color: #333;">$${price !== null ? price : ''}</div>
            <div class="marker-arrow" style="content: ''; position: absolute; bottom: -10px; left: 50%; transform: translateX(-50%); width: 0; height: 0; border-left: 10px solid transparent; border-right: 10px solid transparent; border-top: 10px solid ${bgColor}; filter: drop-shadow(0px 2px 5px rgba(0, 0, 0, 0.15));"></div>
          </div>
        `,
        iconSize: size,
        popupAnchor: [0, -30],
      });
    };

    var markers = {};

    @foreach($searchresults as $searchresult)
      @if($searchresult->Latitude && $searchresult->longnitude)

        <?php
          $price = null;
          $fullBookingURL = null;
          if (!empty($hotels->result)) {
              foreach ($hotels->result as $hotel_result) {
                  if ($hotel_result->id == $searchresult->hotelid) {
                      if (!empty($hotel_result->rooms) && isset($hotel_result->rooms[0])) {
                          $price = $hotel_result->rooms[0]->total;
                          $fullBookingURL = $hotel_result->rooms[0]->fullBookingURL;
                      }
                      break;
                  }
              }
          }
          $hotel_detail_url = url('hd-'.$searchresult->slugid.'-'.$searchresult->id.'-'.strtolower(str_replace(' ', '_', str_replace('#', '!', $searchresult->slug)))."?checkin={$checkin}&checkout={$checkout}");
        ?>

        (function() {
          var price = {{ $price !== null ? $price : 'null' }};
          var fullBookingURL = "{{ $fullBookingURL }}";
          var imageUrl = "https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_0/520/460.jpg";
          var hotelName = "{{ $searchresult->name }}";
          var hotelDetailUrl = "{{ $hotel_detail_url }}";
          var city = "{{ $searchresult->CityName }}";
          var rating = "{{ $searchresult->rating }}";
          var stars = parseInt("{{ $searchresult->stars }}");

          // Calculate rating percentage
          var ratingPercentage = (stars / 5) * 100; // Now each star equals 20%
          // Add visible stars beside hotel name
          var ratingStars = '';
          if (!isNaN(stars) && stars > 0) {
            for (var i = 0; i < stars; i++) {
              ratingStars += '<img src="{{asset('/js/images/Stars.svg')}}" alt="Star" style="width: 12px; margin-right: 2px;">';
            }
          }
          var agencyId = "{{ $data['agencyId'] }}";

          var popupContent = `
  <div style="display: flex; padding: 0; width: 250px; height: 130px; align-items: flex-start;">
    <img src="${imageUrl}" alt="${hotelName}" style="width: 100px; height: 100%; object-fit: cover; margin: 0;">
    <div style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-left: 10px; display: flex; flex-direction: column; justify-content: space-between;">
     <h3 style="margin: 0; font-size: 14px; font-weight: bold; line-height: 1.2;">
        <a href="${hotelDetailUrl}" target="_blank" style="color: inherit; text-decoration: none;">${hotelName}</a> ${ratingStars}
      </h3>
      <p style="margin: 8px 0 0; font-size: 14px; color: #555; display: flex; align-items: center;">
        <img src="{{asset('/js/images/map.svg')}}" alt="Marker" style="width: 16px; height: 16px; margin-right: 8px;">
        ${city}
      </p>
      <div style="display: flex; align-items: center; justify-content: center; margin: 10px 0;">
        <img src="{{asset('/js/images/Score.svg')}}" alt="Heart" style="width: 20px; height: 20px; margin-right: 5px;">
        <span style="font-weight: bold; font-size: 14px;">${ratingPercentage.toFixed(0)}%</span>
        <span style="background-color: #e6f4f4; color: #4c8076; font-weight: bold; font-size: 12px; padding: 4px 8px; border: 2px solid white; border-radius: 4px; margin-left: 5px;">Very Good</span>
      </div>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
        <img src="https://pics.avs.io/hl_gates/100/40/${agencyId}.png" alt="agency logo" style="width: 60px; height: 20px; margin-right: 5px;">
        <a href="${fullBookingURL}" target="_blank" style="text-decoration: none;">
          <button style="background-color: white; color: #333; border: 1px solid grey; padding: 2px 5px; border-radius: 5px; font-weight: bold; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <span style="font-size: 16px;">$${price}</span>
            <img src="{{ asset('/js/images/Arro.svg') }}" alt="Arrow" style="width: 20px; margin-left: 5px;">
          </button>
        </a>
      </div>
    </div>
  </div>
`;
          var marker = L.marker([{{ $searchresult->Latitude }}, {{ $searchresult->longnitude }}], {
            icon: kayakIconWithArrow(price),
            riseOnHover: true
          }).addTo(map).bindPopup(popupContent);

          markers["{{ $searchresult->id }}"] = marker;
          markers["{{ $searchresult->id }}"].isHighlighted = false;

          // Show popup on click
          marker.on('click', function() {
            this.openPopup();
          });

          // Highlight on hover
          marker.on('mouseover', function() {
            if (!marker.isHighlighted) {
              this.setIcon(kayakIconWithArrow(price, true));
              this.isHighlighted = true;
            }
          });

          // Remove highlight on mouseout
          marker.on('mouseout', function() {
            if (marker.isHighlighted) {
              this.setIcon(kayakIconWithArrow(price));
              this.isHighlighted = false;
            }
          });
        })();

      @endif
    @endforeach

    document.querySelectorAll('.tr-hotel-deatils').forEach(function(listingItem) {
      var listingId = listingItem.dataset.id;

      listingItem.addEventListener('mouseover', function() {
        if (markers[listingId] && !markers[listingId].isHighlighted) {
          markers[listingId].setIcon(kayakIconWithArrow(markers[listingId].options.icon.options.html.match(/\$(\d+)/)?.[1], true));
          markers[listingId].isHighlighted = true;
          markers[listingId].openPopup();
        }
      });

      listingItem.addEventListener('mouseout', function() {
        if (markers[listingId] && markers[listingId].isHighlighted) {
          markers[listingId].setIcon(kayakIconWithArrow(markers[listingId].options.icon.options.html.match(/\$(\d+)/)?.[1]));
          markers[listingId].isHighlighted = false;
          markers[listingId].closePopup();
        }
      });
    });

    // Close popup when clicking outside
    map.on('click', function(event) {
      if (!event.originalEvent.target.closest('.leaflet-popup-content')) {
        map.closePopup();
      }
    });

    map.invalidateSize();
    $('#mapModal').on('shown.bs.modal', function () {
      map.invalidateSize();
    });

    setTimeout(function() {
      map.invalidateSize();
    }, 500);
  });
</script>
@endif
<script>
    // Get references to the elements
    // Get references to the elements
const minRange = document.getElementById("minRange");
const maxRange = document.getElementById("maxRange");
const minPrice = document.getElementById("minPrice");
const maxPrice = document.getElementById("maxPrice");

// Function to update the price labels' positions and values
function updatePriceLabels() {
  const minValue = parseInt(minRange.value);
  const maxValue = parseInt(maxRange.value);

  // Update label text to reflect current slider values
  minPrice.textContent = `$${minValue}`;
  maxPrice.textContent = `$${maxValue}`;

  // Calculate percentage positions within the slider
  const minPosition = (minValue / minRange.max) * minRange.offsetWidth;
  const maxPosition = (maxValue / maxRange.max) * maxRange.offsetWidth;

  // Position labels according to calculated slider thumb positions
  minPrice.style.left = `${minPosition}px`;
  maxPrice.style.left = `${maxPosition}px`;
}

// Function to update listings based on slider values (replace with your actual filter function)
function updateListings() {
  // Your existing logic to update listings based on `minRange.value` and `maxRange.value`
  console.log(`Updating listings with min price: $${minRange.value} and max price: $${maxRange.value}`);
  // Add your actual filtering logic here
}

// Combined function to update both labels and listings
function handleSliderChange() {
  updatePriceLabels();
  updateListings();
}

// Initialize label positions
updatePriceLabels();

// Update label positions and listings when the sliders move
minRange.addEventListener("input", handleSliderChange);
maxRange.addEventListener("input", handleSliderChange);


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
document.addEventListener('DOMContentLoaded', function() {
    const readMoreBtn = document.querySelector('.custom-read-more');
    const trMoreFacilities = document.querySelector('.tr-more-facilities');

    if (readMoreBtn && trMoreFacilities) {
        readMoreBtn.addEventListener('click', function() {
            trMoreFacilities.classList.toggle('expanded');

            // Optional: Change button text to "Read Less" when expanded
            if (trMoreFacilities.classList.contains('expanded')) {
                readMoreBtn.textContent = 'Read Less';
            } else {
                readMoreBtn.textContent = 'Read More';
            }
        });
    }
});
</script>
<script>
    function toggleContent(button) {
        const content = button.previousElementSibling; // Select the <p> element
        content.classList.toggle('show-more');
        button.textContent = content.classList.contains('show-more') ? 'Read Less' : 'Read More';
    }
</script>
<script>
  window.amenityIds = {!! json_encode(!empty($amenity_ids) ? $amenity_ids : []) !!};
  window.neighborhood_info = {!! json_encode(!empty($neighborhood_info) ? $neighborhood_info : []) !!};
  window.sight_info = {!! json_encode(!empty($sight_info) ? $sight_info : []) !!};
  window.propertyType_ids = {!! json_encode(!empty($propertyType_ids) ? $propertyType_ids : []) !!};
  window.propertyTypeInfo = {!! json_encode(!empty($propertyType_info) ? $propertyType_info : []) !!};
</script>
<style>
.filter-count {
    margin-left: 5px;
    color: #666;
    font-size: 0.9em;
}
</style>

<script>
$(document).ready(function() {
    setTimeout(function() {
        $.get('/hotel-filter-counts', { id: '{{ $id }}' }, function(data) {
            // Star ratings
            $.each(data.stars, function(rating, count) {
                $('input[name="rating"][value="' + rating + '"]').closest('li')
                    .append('<span class="filter-count">(' + count + ')</span>');
            });
            
            // Amenities
            $.each(data.amenities, function(amenity, count) {
                $('input.filter[value^="' + amenity + '"]').closest('li')
                    .append('<span class="filter-count">(' + count + ')</span>');
            });
            
            // Property types
            $.each(data.propertyTypes, function(type, count) {
                $('input[name="hoteltypes"]').each(function() {
                    if ($(this).closest('li').text().trim().includes(type)) {
                        $(this).closest('li').append('<span class="filter-count">(' + count + ')</span>');
                    }
                });
            });
        
            // Nearby places
			$.each(data.nearbyPlaces, function(sightId, count) {
 		 	  $('input[name="nearby[]"][value="' + sightId + '"]').closest('li')
      			  .append('<span class="filter-count">(' + count + ')</span>');
			});
        
            // Agencies
            $.each(data.agencies, function(agency, count) {
                $('input[name="agency"][value="' + agency + '"]').closest('li')
                    .append('<span class="filter-count">(' + count + ')</span>');
            });
      
           $.each(data.guestRatings, function(rating, count) {
       			 var $input = $('input[name="guest_rating"][value="' + rating + '"]');
       			 if ($input.length) {
            	$input.closest('li').append('<span class="filter-count">(' + count + ')</span>');
   		    	 }
   			 });
        });
    }, 11000); // 25 second delay after page opens
});
</script>
