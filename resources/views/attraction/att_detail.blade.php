<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PTHP3JH4');</script>
  <meta charset="UTF-8">
  @php $title = '';$lname=''; @endphp
@if(!empty($searchresult)) @php $title = $searchresult[0]->Title; $lname=$searchresult[0]->Name; @endphp @endif
  <title>{{$title}}, {{$lname}}@if(!empty($locationPatent)), @foreach($locationPatent as $location){{$location['Name']}}@if(!$loop->last),@endif @endforeach @endif– Reviews, Hours, and Photos - Travell</title>
  <meta name="description" content="Planning to visit {{$title}}? Read Reviews, check hours, and see traveler photos of {{$title}}, {{$lname}}.">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="csrf-token" content="{{ csrf_token() }}">
 <link rel="icon" type="image/x-icon" href="{{asset('/favicon.ico')}}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <link rel="stylesheet" href="{{ asset('/css/bootstrap.min.css') }}">

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> 
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


 <link rel="stylesheet" href="{{ asset('/css/all.min.css') }}"><!-- font awesome v.5--> 
 <link rel="stylesheet" href="{{ asset('/css/header.css') }}">  
 <link rel="stylesheet" href="{{ asset('/css/explore.css') }}"> 
 <link rel="stylesheet" href="{{ asset('/css/style.css')}}">



 
  <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}"><!-- map--> 

   <!-- nav css -->


     <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}"> 
        <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
      <!-- end nav css -->

   <!-- end map -->

</head>
<?php
      $locationPatents = array_reverse($locationPatent);
       $n = 2;
      ?>
<script type="application/ld+json">
{
  "@context": "https://schema.org/",
  "@type": "BreadcrumbList",
  "itemListElement": [
        {
    "@type": "ListItem",
    "position": 1,
    "name": " @if(!empty($breadcumb)) {{ $breadcumb[0]->ccName }} @endif",
    "item": " @if(!empty($breadcumb)) {{ route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])}} @endif"
  },
    {
    "@type": "ListItem",
    "position": 1,
    "name": "@if(!empty($breadcumb)) {{$breadcumb[0]->CountryName}} @endif",
    "item": " @if(!empty($breadcumb)) {{ route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])}} @endif"
  },
  @if(!empty($searchresult))
    @if(!empty($locationPatents))
      @foreach($locationPatents as $location) {
        "@type": "ListItem",
        "position": {{$n}},
       
        "name": "{{ $location['Name'] }}",
        "item": "{{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug']).'-'.str_replace(' ','_',strtolower($searchresult[0]->countryName))]) }}"
      },
      <?php $n++; ?>
      @endforeach
      @endif
    @endif
    @if(!empty($searchresult))
    {
    "@type": "ListItem",
    "position": {{$n}},
    "name": "{{$searchresult[0]->Name}}",
    "item": "{{ route('search.results',[$searchresult[0]->LocationId.'-'.strtolower($searchresult[0]->Lslug).'-'.strtolower($searchresult[0]->countryName)]) }}"
  }, {
    "@type": "ListItem",
    "position": {{$n + 1}},
    "name": "{{$searchresult[0]->Title}}",
    "item": ""
  } 
   @endif
   ]
}
</script>

<body>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	
@include('Loc_nav.loc_navbar')

  <!-- main page start -->
  <main class="main ">
    <div class="container">
      <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
      @if(!empty($searchresult))
		
        <ol class="breadcrumb">
		@if(!empty($breadcumb))
       <li class="breadcrumb-item active" aria-current="page">
          <a href="{{ route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])}}"> {{$breadcumb[0]->ccName}}</a>
          </li>              
			<li class="breadcrumb-item">
				<a
				   href="{{ route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])}}">
					@if(!empty($breadcumb)) {{$breadcumb[0]->CountryName}} @endif</a>
			</li>
			@endif
          @if(!empty($locationPatent))
          <?php
                $locationPatent = array_reverse($locationPatent);
            ?>
          @foreach ($locationPatent as $location)
          <li class="breadcrumb-item">
            <a
              href="{{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}">
              {{ $location['Name'] }}</a>
          </li>
          @endforeach
          @endif
          <li class="breadcrumb-item active" aria-current="page">
            <a
              href="{{ route('search.results',[$searchresult[0]->LocationId.'-'.strtolower($searchresult[0]->Lslug)]) }}"><span>{{$searchresult[0]->Name}}</span>
            </a>
          </li>
          <li class="breadcrumb-item active" aria-current="page"><span>{{$searchresult[0]->Title}}</span>
          </li>
        </ol>
        @endif
      </nav>
    </div>
    <div class="container">

      <div class="row">
        @if(!empty($searchresult))
        <div class="col-lg-7">

          <!-- Breadcrums -->

          <!-- <nav itemscope itemtype="https://schema.org/BreadcrumbList" style="--bs-breadcrumb-divider: '>';"
            aria-label="breadcrumb">
            <ol class="breadcrumb">
              <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                class="breadcrumb-item active" aria-current="page">
                <a itemprop="item"
                  href="{{ route('search.results',[$searchresult[0]->LocationId.'-'.strtolower($searchresult[0]->Lslug).'-'.strtolower($searchresult[0]->countryName)]) }}"><span
                    itemprop="name">{{$searchresult[0]->Name}}</span>
                </a>
                <meta itemprop="position" content="1" />
              </li>
              <li itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem"
                class="breadcrumb-item active" aria-current="page"><span
                  itemprop="name">{{$searchresult[0]->Title}}</span>
                <meta itemprop="position" content="2" />
              </li>
            </ol>
          </nav> -->

          <div class="me-4">
            <span class="text-main fs-18 m">@if (!$getcat->isEmpty())
              @php
                  $categories = [];
              @endphp

              @foreach($getcat as $catval)
                  @if (!in_array($catval->Title, $categories))
                  
                      {{ $catval->Title }}

                      {{-- Add the category to the array --}}
                      @php
                          $categories[] = $catval->Title;
                      @endphp

                      @if (!$loop->last && !$loop->remaining)
                          {{-- Add a comma if it's not the last category --}}
                          ,
                      @endif
                  @endif
              @endforeach
          @endif

          </span>
            <h3 class=" d-flex justify-content-between align-items-center">
              <span class="fs-30">{{$searchresult[0]->Title}}</span>
              <span class="d-flex align-items-center">
                <img src="{{ asset('/images/logout.png') }}" class=" me-3" width="25px" height="23px"
                  alt="logout">
                <img src="{{ asset('/images/upload.png') }}" width="25px" height="23px" alt="logout"></span>
            </h3>
            <p class="mb-0 small"><span>{{$searchresult[0]->Address}}</span></p>
            <input type="hidden" value="{{$searchresult[0]->SightId}}" id="sightId">
            <input type="hidden" id="LocationId" value="{{$searchresult[0]->LocationId}}">
            <span class="d-none sightId" >{{$searchresult[0]->SightId}}</span>
            <span class="d-none Longitude">{{$searchresult[0]->Longitude}}</span>
            <span class="d-none Latitude">{{$searchresult[0]->Latitude}}</span>
            
          </div>
        </div>
        <!-- <div class="offset-1"></div> -->
        <div class="ms-auto col-lg-4">
          <div class="d-flex align-items-center mt-3">
            <div class="d-flex align-items-center vote">
          
          
              <div
                class="border rounded-pill px-3 fa-2x py-1 me-4 d-flex align-items-center justify-content-around vote-section ">
                @if($searchresult[0]->TAAggregateRating != "" && $searchresult[0]->TAAggregateRating != 0)
                <?php $result = rtrim($searchresult[0]->TAAggregateRating, '.0') * 20;  ?> 

                <i class="fas fa-heart fs-22 text-main me-3"></i><span
                  class=" fs-22">
				     <span> {{$result}}% </span>                
                  
                   @else             
				
                   
                <i
                    class="fas fa-heart fs-22 text-c"></i><span class="fw-bold fs-22">--</span>
                @endif
              </div> 
              <div>
                <p class="mb-0  fs-18">(0 votes)</p>
                <p class="mb-0 small">Traveller Reviews</p>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <section class="col-lg-7">
          <div class="">
            <div class="">
              <!-- start carousel  -->
         <!--end if image not avalable -->
              <div class="my-4 border-bottom slider sightImages">             
              @if(!$Sight_image->isEmpty())
                <div class="b-10 border border-3 border-c mb-4 bg-e text-center py-5">               
                   <img src="{{ asset('sight-images/'. $Sight_image[0]->Image) }}" alt="" />
                </div>
              @else
                <div class="b-10 border border-3 border-c mb-4 bg-e text-center py-5 addph">
                        <img src="{{ asset('/images/drop.png')}}" alt="drop" />
                        <p class="mb-0">Enhance this page</p>
                        <a href="#" class="text-dark" >Upload photos</a>
                    </div>
              @endif                  
              
                </div>
           <!--end if image not avalable -->


            <!--  <div class="my-4 border-bottom slider">
                <div id="maincarousel" class="carousel b-20 slide overflow-hidden mt-3" data-bs-ride="carousel">
                  <ol class="carousel-indicators">

                    <li data-bs-target="#maincarousel" data-bs-slide-to="0" class="active">
                    </li>
                    <li data-bs-target="#maincarousel" data-bs-slide-to="1"></li>
                    <li data-bs-target="#maincarousel" data-bs-slide-to="2"></li>
                    <li data-bs-target="#maincarousel" data-bs-slide-to="3"></li>
                    <li data-bs-target="#maincarousel" data-bs-slide-to="4"></li>
                  </ol> 

              <div class="carousel-inner" role="listbox" aria-label="carousel">
                    <div class="carousel-item active" role="option"> -->
                      <!-- <img src="{{ asset('//images/Hotel lobby.svg')}}" class="w-100" alt="sdc"> -->
                     
                    <!-- </div> -->
                    <!-- <div class="carousel-item" role="option">
                      <img src="{{ asset('//images/Hotel lobby.svg')}}" class="w-100" alt="sd">
                    </div>
                    <div class="carousel-item" role="option">
                      <img src="{{ asset('//images/Hotel lobby.svg')}}" class="w-100" alt="dsds">
                    </div>
                    <div class="carousel-item" role="option">
                      <img src="{{ asset('//images/Hotel lobby.svg')}}" class="w-100" alt="sdsd">
                    </div>
                    <div class="carousel-item" role="option">
                       <img src="{{ asset('//images/slider.png')}}" class="w-100" alt="ddsk"> 
                      <img src="{{ asset('//images/Hotel lobby.svg')}}" class="w-100" alt="sdsd">
                    </div> -->

                    
                  </div>
                  <!-- <button class="carousel-control-prev carousel-btn" type="button" data-bs-target="#maincarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next carousel-btn" type="button" data-bs-target="#maincarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button> 

                </div>
                <div class="d-flex mb-4 justify-content-end mt-2">
                  <a href="#" class="text-111 addph"> + Add More Photos </a>

                </div>
              </div>-->
              <!-- end carousel  -->
              <div class="about-reviews d-lg-none">
                <div class="border-bottom pb-4 justify-content-between d-flex align-items-center">
                  <div class="">
                    <p class="text-main mb-0">open now</p>
                    <p class="mb-0"></p>
                  </div>
                  <i class="fas fa-angle-right"></i>
                </div>


                @forelse($sightreviews as $review)

                <div class="testmonial py-4 border-bottom">
                  <div class="d-flex ">
                    <img class="rounded-circle" src="{{asset('images/person.png')}}" width="33px" height="33px"
                      alt="person">
                    <div class="mx-2">
                      <div class="d-flex  align-items-center">
                        <h6 class="mb-0">{{$review->Name}}</h6>
                        <div class="">
                          <i class="fas fa-heart text-main mx-2  "></i>
                          <span class="text-center text-secondary">recommends</span>
                        </div>
                      </div>
                      <p class="ls-0">{{$review->ReviewDescription}}</p>
                    </div>
                  </div>
                </div>
                @empty
                <div class="testmonial py-4 border-bottom">
                  <p class="ls-0">No Reviews found. Be the first one to review. </p>
                </div>
                @endforelse


                <div class="text-center border-bottom py-4">
                  <button class="rounded-pill btn bg-main text-white px-4 ">See all Reviews</button>
                </div>
              </div>
              <!-- about  -->
              <div class="about py-4 border-bottom">
                <h5 class="mb-4  fs-22 ">About The Place <i class="fas fa-edit ms-2" id="open_edit_desc"></i></h5>   
                <p id="sight-desc">{{$searchresult[0]->About}}</p>
             

                <p>
                  For more details on visiting guidance and the measures we have in place, see our
                  FAQs.
                </p>

               
              <span id="edit_desc" class="edit-desc d-none mb-5">
                <span class="upd d-none" style="color:green; ">Updating...</span>
              <textarea name="description" id="descriptionTextarea" cols="80" rows="7" class="form-control">{{$searchresult[0]->About}}</textarea>

           
                  <br>
                  <button id="updatedesc" class="rounded-pill btn border border-dark mx-2 px-5" data-id="{{$searchresult[0]->SightId}}">Update</button>
                  <br>
              </span>

          

           

              <!-- end about  -->
              <!-- start time to stay info is avail -->

              <!-- <div class="time-stay py-4 border-bottom">
                <h5 class="mb-4  fs-22 ">How Long You`ll Stay</h5>
                <div class="time b-20 border border-dark  px-3 py-3  text-center" style="width: 150px;height: 120px;">
                  <img src="{{ asset('/images/clock.png')}}" width="55px" alt="">
                  <p class="small my-2">2 - 3 hours</p>
                </div>
              </div> -->

              <!-- not available -->
             
              <div class="time-stay py-5 border-bottom border-top">
                  <h5 class="mb-4  fs-22 ">How Long You`ll Stay</h5>
                  <img src="{{ asset('/images/clock.png')}}" width="25px" alt="">
                  <a href="#" class="text-dark fw-bold ms-3">Suggest the duration</a><i
                      class="fas fa-edit ms-2"></i>

              </div>
              <!-- end time to stay  -->

           <!-- start timming  -->
              <div class="timming py-lg-5 py-4  border-bottom" >
                <h5 class="mb-4  fs-22 ">Timings</h5>
								<div class="timmig-box">
								<div class="mx-3 mt-3 mb-3"><img src="{{asset('images/timg.png')}}" width="50"></div>
                  <table id="updtiming">
      
                    @if(!$gettiming->isEmpty())
                          <?php
                          $data = json_decode($gettiming[0]->timings, true);
                          $daysOfWeek = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
                          date_default_timezone_set('Asia/Kolkata'); // Set to Indian Standard Time

                          $currentDay = strtolower(date('D'));
                          $currentTime = date('H:i');

                          ?>

                          @foreach($daysOfWeek as $day)
                              <tr>
                                  <td>{{ ucfirst($day) }}</td>
                                  @if(isset($data['time'][$day]))
                                      <?php
                                      $startTime = $data['time'][$day]['start'];
                                      $endTime = $data['time'][$day]['end'];
                                      ?>

                                      @if($day === $currentDay)
                                  
                                      @if ($currentTime >= $startTime && $currentTime <= $endTime)
                                              @if($startTime == "00:00" && $endTime == "23:59")
                                                  <td>| Open 24 hours</td>
                                            @elseif($startTime == "00:00" && $endTime == "00:00")
                                                  <td>| closed</td>
                                              @else
                                                  <td>| {{ $startTime }} - {{ $endTime }}</td>
                                              @endif    
                                          @else
                                            @if($startTime == "00:00" && $endTime == "23:59")
                                                  <td>| Open 24 hours</td>
                                            @elseif($startTime == "00:00" && $endTime == "00:00")
                                                  <td>| closed</td>
                                              @else
                                                  <td>| {{ $startTime }} - {{ $endTime }}</td>
                                              @endif    
                                            
                                                <!--    |  <span class="clsennow">Closed now</span> -->
                                            </td>
                                          @endif
                                      @else
                                        @if($startTime == "00:00" && $endTime == "23:59")
                                              <td>| Open 24 hours</td>
                                          @elseif($startTime == "00:00" && $endTime == "00:00")
                                                  <td>| closed</td>
                                          @else
                                              <td>| {{ $startTime }} - {{ $endTime }}</td>
                                          @endif
                                        
                                      @endif
                                  @else
                                      <td>| <span class="">Not available</span></td>
                                  @endif
                              </tr>
                          @endforeach
                      @else
                        Timing not available .
                    @endif


                    </table>
                   </ul>
								  </div>
                </div>
                            <!-- end timming  -->
              <!-- start known  -->
              <div class="known py-lg-5 py-4 border-bottom">
                <h5 class="mb-4  fs-22 ">What Is It Known For</h5>
                <ul>
                  <li class="my-2">Cleanliness around the area </li>
                  <li class="my-2">Better signal coverage</li>
                  <li class="my-2">Child-freindly spaces</li>
                  <li class="my-2">Photography is prohibited</li>
                  <li class="my-2">Playground areas accessible</li>
                </ul>
              </div>
              <!-- end known  -->

              <!-- start nearby  -->
              <div class="nearby py-lg-5 py-4  border-bottom">
                <h5 class="mb-4  fs-22 ">Whats nearby</h5>      
                <div class="row" id="near_sight">
                  @if(!$nearby_sight->isEmpty())
                  @foreach($nearby_sight as $nearby_sight)
                  <div class="col-sm-6 d-flex align-items-center my-4">
                    <img src="{{ asset('/images/image1.png')}}" width="72px" height="72px" alt="">
                    <div class="mx-3 small text-center">
                      <p class="mb-0 small  text-111"><a href="{{route('sight.details',[$nearby_sight->LocationId.'-'.$nearby_sight->SightId.'-'.$nearby_sight->Slug])}}">{{$nearby_sight->Title}} </a></p>
                      <p class="mb-0">----------------------------⇢</p>
                      <p class="mb-0 small text-111">{{ round($nearby_sight->distance,2) }}  Km</p>
                    </div>
                    <i class="fa fa-map-marker-alt fa-2x"></i>
                  </div>
                  @endforeach
                @else
                <p>Nearby Attraction not found.</p>
                @endif         


                </div>
              </div>

              <!-- end nearby  -->
              <div class="py-4  border-bottom">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                  <p class="mb-0 fs-18">Would you recomended visiting<br />
                    {{$searchresult[0]->Title}} ? </p>
                  <div class="d-flex mt-2 mt-md-0">              
                    <button class="aadrev rounded-pill btn border border-dark mx-2 px-5 ">No</button>
                    <button class="aadrev rounded-pill btn border border-dark mx-2 px-5 ">Yes</button>
                  </div>
                </div>
              </div>
              <!-- start where will be  -->
              <div class=" where-be py-4 py-lg-5 border-bottom">
                <h5 class="mb-3 heading fs-26 ">Where you`ll be</h5>
                <!-- start map -->
                <!-- <img class="w-100" src="{{ asset('/images/map.png')}}" alt="map"> -->
				     @if($searchresult[0]->Longitude != "" && $searchresult[0]->Latitude != "")
                <div id="map1" class="" style="width: 100%; height: 400px;"></div>
  
                  <div id="screenshotContainer"></div>
			      	@endif
             
                <!--  end map -->
                <h6 class=" mt-4 fs-18"> {{$searchresult[0]->Title}}</h6>
                <!-- <p class="mb-4">
                  snuggly nested within central london is acadimic and leafy
                  Bloomsbury
                </p> -->
                <div class="information">
                  <div class="d-flex align-items-center mb-3">
                    <i class="fa fa-map-marker-alt fa-lg me-3"></i>
                    <p class="mb-0">@if($searchresult[0]->Address != "") {{$searchresult[0]->Address}}
                      @else
                       <a href="#" class="text-dark fw-bold">
                        Add location information</a><i class="fas fa-edit ms-2"></i>  
                      @endif</p>
                  </div>
                  <div class="d-flex align-items-center mb-3">
                    <i class="fa fa-phone-alt fa-lg me-3"></i>
                    <p class="mb-0">
                      @if($searchresult[0]->Phone != "") {{ $searchresult[0]->Phone}}
                      @else
                      <a href="#" class="text-dark fw-bold">Add phone number</a><i
                                                    class="fas fa-edit ms-2"></i>
                    @endif </p>
                  </div>
                  <div class="d-flex align-items-center mb-3">
                  @if($searchresult[0]->Website != "")  <i class="fa fa-globe fa-lg me-3"></i>
                    <p class="mb-0">{{ $searchresult[0]->Website}}</p>
                      @else  <i class="fa fa-globe me-3"></i>
                                            <p class="mb-0"><a href="#" class="text-dark fw-bold">Add website</a><i
                                                    class="fas fa-edit ms-2"></i>   @endif
                  </div>
                </div>
              </div>
              <!-- end where will be  -->
              <!-- start Nearby Attractions  -->
              <div class="attractions pb-4 pt-4 ">
                <h5 class="mb-2 mb-lg-3 heading fs-26 ">Nearby Attractions</h5>
                <div class="things-todo border-bottom">
                  <div class="row mb-5" id="nearby_att">
                    <div class="d-flex  align-items-center justify-content-between">
                      <h6 class="mb-0 fs-18">Things To Do</h6>
                      <!-- <a href="#" class="text-dark see-all">See All</a> -->
                    </div>
                    @if(!$nearbyatt->isEmpty()) <p class="mb-3 distance">50+ within {{$nearbyatt[0]->radius}} km</p> @endif



                      
                    @if(!$nearbyatt->isEmpty())
                    @foreach($nearbyatt as $nearbyatt)
                    <div class="col-6 mb-3">
                      <div class="">
                        <div class="img-box position-relative">
                          <i class="fas fa-bookmark position-absolute text-secondary"></i>
                          <img src=" {{ asset('/images/park.jpg') }}" class="w-100 b-20" alt="hol2">
                        </div>
                        <div class="d-flex justify-content-between align-items-center my-2">
                          <span class="text-secondary fs-sm-12 m--5">
                         {{ $nearbyatt->ctitle}}
                          </span>
                          <div class="border rounded-pill px-lg-3 px-1 rating  py-lg-1">
                            <!-- <i class="fas fa-heart text-main me-lg-3 me-1"></i><span class=" fs-18 fs-sm-14">98%</span> -->

                            @if($nearbyatt->TAAggregateRating != "" && $nearbyatt->TAAggregateRating != 0)
                <?php $result = rtrim($nearbyatt->TAAggregateRating, '.0') * 20;  ?> 

                <i class="fas fa-heart text-main me-lg-3 me-1"></i><span
                  class=" fs-18 fs-sm-14">
				     <span> {{$result}}% </span>                
                  
                   @else             
				
                   
                <i
                    class="fas fa-heart text-main me-lg-3 me-1"></i><span class="fs-18 fs-sm-14">--</span>
                @endif
                          </div>
                        </div>
                        <h6 class=" d-lg-flex justify-content-between align-items-center fs-18">
                          <span class=fs-sm-16>{{$nearbyatt->Title}}</span>

                        </h6>
                        <p class="fs-sm-14 mb-0">T{{$nearbyatt->Address}}</p>
                        <div class="d-lg-flex justify-content-between align-items-center">


                        @if(!empty($nearbyatt->timings))
                        <div class="text-secondary fs-sm-14"><i class="far fa-clock    "></i></div>
                            <?php 

                        $currentDay = strtolower(date('D'));
                        $schedule = json_decode($nearbyatt->timings,true);
                        if(isset($schedule['time'][$currentDay])){
                          $openingtime = $schedule['time'][$currentDay]['start'];
                          $closingTime =$schedule['time'][$currentDay]['end'];

                          if($openingtime == '00:00' && $closingTime == "23:59"){
                              $formatetime = "12:00 AM - 11:59";
                          }else{
                              $formatetime = $openingtime.' - '.$closingTime;
                          }
                          $isOpen = true;                          

                        }else{
                          $isOpen = false;
                        }
                          
                          ?>
                          @if($isOpen)
                           {{$formatetime}}
                           
                          <div class="text-main fs-sm-14">Open Now</div>

                          @else
                     
                            <div class="text-main fs-sm-14">Closed Today</div>
                          @endif
                          @endif
                        </div>
                      </div>
                    </div>              
                  @endforeach
                  @else
                  <p>Nearby Attractions not found.</p>
                  @endif   
                  </div>             
              </div>
              <!-- end Nearby Attractions  -->
  			  <!-- start nearby  -->
                <div class="nearby py-lg-5 py-4 ">
                <h5 class="mb-4  fs-22">Nearby Hotels</h5>      
                <div class="row " id="nearby_hotel">
                  @if(!$nearby_hotel->isEmpty())
                  @foreach($nearby_hotel as $nearby_hotel)
                  <div class="col-sm-6 d-flex align-items-center my-4">
                    <img src="{{ asset('/images/image1.png')}}" width="72px" height="72px" alt="">
                    <div class="mx-3 small text-center">
                      <p class="mb-0 small  text-111"><a href="{{route('hotel.detail',[$nearby_hotel->location_id.'-'.$nearby_hotel->hotelid.'-'.$nearby_hotel->slug])}}">{{$nearby_hotel->name}} </a></p>
                      <p class="mb-0">----------------------------⇢</p>
                      <p class="mb-0 small text-111">{{ round($nearby_hotel->distance,2) }}  Km</p>
                    </div>
                    <i class="fa fa-map-marker-alt fa-2x"></i>
                  </div>
                  @endforeach
                @else
                <p>Nearby Hotels not found.</p>
                @endif         


                </div>

              </div>

              <!-- end nearby  -->
            </div>
          </div>
        </section>
        <!-- <div class="offset-1"></div> -->
        <aside class="ms-auto col-lg-4">
          <div class="">

            <div class="my-4">
              <h4 class=" heading fs-22 ">Reviews</h4>
              <p class="mb-3 text-dark">What do people say about this place</p>
              <p id="msg" style="color:green;"></p>
              <div class="testimonials">
                <!-- <div class="d-flex mb-4 justify-content-end mt-2">
                  <a href="#" class="text-111 aadrev"> + Write A Review </a>

                </div> -->

                <!-- addphoto  -->
              <span class="getreviewdata">
                @forelse($sightreviews as $review)

                <div class="testmonial py-4">

                  <div class="d-flex align-items-center">
                    <img class="rounded-circle" src="{{asset('images/person.png')}}" width="50px" height="50px"
                      alt="">
                    <div class=" mx-2">
                      <div class="d-flex justify-content-between align-items-center">

                        <h6 class="mb-0">
                          <span>{{$review->Name}}</span>
                        </h6>
                        <!--	  <span itemprop="itemReviewed" itemscope itemtype="https://schema.org/LocalBusiness">
						<span itemprop="name" class="d-none">{{$searchresult[0]->Title}}</span>
							</span> -->
                        @if($review->IsRecommend =='1')
                        <div class="">
                          <i class="fas fa-heart text-main mx-2  "></i>
                          <span class="text-center text-secondary">recommends</span>
                          <!-- <span class="text-center text-secondary">Rating given = -->
                            {{$review->ReviewRating}}</span></span>
                        </div>
                        @else
                        <div class="">
                          <i class="fas fa-heartbeat text-secondary mx-2  "></i>
                          <span class="text-center text-secondary">doesn't recommend</span>
                        </div>
                        @endif
                      </div>
                      <p class="mb-0 text-secondary small"><?php
                          $createdDate = $review->CreatedDate; 

                          $dateTime = new DateTime($createdDate);
                          $formattedDate = $dateTime->format("F Y");

                          echo $formattedDate; 
                          ?>
                      </p>
                    </div>

                  </div>
                  <?php
                        $fullDescription = $review->ReviewDescription;
                        $shortDescription = substr($fullDescription, 0, 150);
                  ?>
                                  
                               <div class="review-container">
                                  <p class="my-3 ls-0 review-text">
                                      <span class="short-description @if (strlen($fullDescription) < 150)  d-none @endif">{{$shortDescription}}..</span>
                                      <span class="full-description  @if (strlen($fullDescription) > 150)  d-none @endif">{{$fullDescription}}</span>
                                  </p>
                                  @if (strlen($fullDescription) > 150) 
                                      <span class="underline text-dark view-more"><u>View More</u></span>
                                  @endif
                              </div>

                    <div class="d-flex align-items-center justify-content-between">
                    <!-- @if (strlen($fullDescription) > 100) 
                        <span class="underline text-dark view-more "><u>View More</u></span>
                       @endif  -->
                        <span>
                            <img class="me-2" src="{{asset('images/share.png')}}" width="16px" alt="share">
                            Share
                        </span>
                    </div>
                </div>
                 
                @empty
              
                    <div class="py-3 text-center">
                        <p>No User Reviews Yet</p>
                        <p class="small">Be the first one to write a review for Imambada</p>
                        <img src="{{asset('images/review.png')}}" class="img-fluid" alt="" />
                        <button class="aadrev text-white explore bg-main btn rounded-pill px-4">Write a Review</button>
                    </div>
                         
                @endforelse
                </span>
              </div>
            </div>
          </div>
        </aside>
    
        <!-- start Frequently Asked Questions about Tate Modern  -->
        <div class="asked-questions py-4 border-top" id="faqdata">
          <h5 class="mb-3  heading fs-26 ">Frequently Asked Questions about Tate Modern </h5>
          @if(!empty($faq))
          @foreach($faq as $faqs)
          <div class="question py-3">
            <h6 class="fs-18">{{$faqs->Faquestion}}?</h6>
            <p class="mb-0"><span itemprop="text">{{$faqs->Answer}}</span></p>
            <a href="#" class="text-dark">See all nearby attractions.</a>
          </div>
          @endforeach
          @else
          <p>Faq not Found.</p>
          @endif
        </div>
	      <!-- Start Select dateTime  -->
         <div  class="add-datetims d-none position-fixed d-flex align-items-center justify-content-center" >
                            <div class="add-tip-box bg-white b-10 py-5 px-md-5 px-3 position-relative">
                              <div class="container-fluid">
                                <div role="button" class="close-datetims rounded-pill px-3 position-absolute">close X
                                </div>
                                <h3 class="text-md-center mb-3 fs-25">Select Days and Time</h3>
                                <div class="">

                                  <?php     if(!$gettiming->isEmpty()){ $timingData = json_decode($gettiming[0]->timings, true);} ?>
                                  <div class="selectdays">
                                    <ul>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r1" id="r1"
                                          checked >
                                          <label class="checkbox-alias" for="r1">S</label>
                                        </div>
                                      </li>

                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r2" id="r2"
                                          checked>
                                          <label class="checkbox-alias" for="r2">M</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r3" id="r3"
                                          checked >
                                          <label class="checkbox-alias" for="r3">T</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r4" id="r4"
                                          checked >
                                          <label class="checkbox-alias" for="r4">W</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r5" id="r5"
                                          checked />
                                          <label class="checkbox-alias" for="r5">T</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r6" id="r6"
                                          checked >
                                          <label class="checkbox-alias" for="r6">F</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r7" id="r7"
                                          checked >
                                          <label class="checkbox-alias" for="r7">S</label>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="mt-4 mb-4">
                                    <div class="form-check form-check-inline">
                                  
                                      <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        name="inlineCheckbox1"    @if(!empty($gettiming[0]->timings))  @if(array_key_exists('open_closed', $timingData) && $timingData['open_closed']['open24'] == 1 ) checked
                                        @endif @endif>

                                      <label class="form-check-label" for="inlineCheckbox1">Open 24 hours</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                        name="inlineCheckbox2">
                                      <label class="form-check-label" for="inlineCheckbox2">Closed</label>
                                    </div>
                                  </div>
                                  @if(!$gettiming->isEmpty())
                                  <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mainhours" id="flexRadioDefault1" value="1" @if($gettiming[0]->main_hours == 1) checked @endif>
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Open with main hours 
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mainhours" id="flexRadioDefault2" value="0" @if($gettiming[0]->main_hours == 0) checked @endif>
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        Open with no main hours 
                                    </label>
                                </div>
                                @else
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mainhours" id="flexRadioDefault1" value="1"  checked >
                                    <label class="form-check-label" for="flexRadioDefault1">
                                        Open with main hours 
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="mainhours" id="flexRadioDefault2" value="0" >
                                    <label class="form-check-label" for="flexRadioDefault2">
                                        Open with no main hours 
                                    </label>
                                </div>
                                  @endif
                                  <div class="mt-3 mb-3">
                                    <span class="error"></span>
                                    <div class="row" style="margin-left: 109px;">
                                      <div class="col-md-5 col-5">
                                        <div class=" mt-1">
                                          <label for="email" class="form-label">Opens at</label>

                                        </div>
                                      </div>
                                      <div class="col-md-5 col-5">
                                        <div class="mt-1">
                                          <label for="email" class="form-label">Closes at</label>

                                        </div>
                                      </div>  
 
                                    </div>
                                    <?php $i=1;?>
                                    @if(!$gettiming->isEmpty())
                                   @if(!empty($timingData['time'] ))
                                    @foreach ($timingData['time'] as $day => $times)

                                    @if($day =="sun")
                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                      <span>Sun<br> 
                                          <input class="form-check-input " type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('sun', this)"  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked @endif>
                                          <label class="form-check-label" for="flexCheck">
                                              closed
                                          </label>
                                      </span>
                                  </div>

                                      <div class="col-md-3 col-5 sun  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                    
                                          <input type="time" class="form-control clopen" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-3 col-5 sun  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control cltime" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2 sun  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                @endif
                                @if($day =="mon")
                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                      <span>Mon<br> 
                                          <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('mon', this)"  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked  @endif> 
                                          <label class="form-check-label" for="flexCheck">
                                              closed
                                          </label>
                                      </span>
                                  </div>

                                      <div class="col-md-3 col-5 mon  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                    
                                          <input type="time" class="form-control clopen" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-3 col-5 mon  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control cltime" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2 mon  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                    @endif
                                 

                                    @if($day =="tue")
                                        <div class="row pls">
                                            <div class="col-md-2 col-5">                                      
                                                <span>Tue<br> 
                                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckTue" @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked @endif onchange="toggleTimeInputs('tue', this)">
                                                    <label class="form-check-label" for="flexCheckTue">
                                                        closed
                                                    </label>
                                                </span>
                                            </div>
                                            
                                            <div class="col-md-3 col-5 tue  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                                <div class="mb-2 mt-2">
                                                    <input type="time" class="form-control day clopen" id="clopenTue"
                                                        value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]" >
                                                </div>
                                            </div>
                                            <div class="col-md-3 col-5 tue  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                                <div class="mb-2 mt-2">
                                                    <input type="time" class="form-control  cltime day" id="cltimeTue"
                                                        value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]" >
                                                </div>
                                            </div>
                                            <div class="col-md-2 col-2 tue  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                                @if($i == 1)
                                                <div class="plusicon">+</div>
                                                @else
                                                <div class="closeicon">x</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif 
                                    @if($day =="wed")
                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                      <span>Wed<br> 
                                          <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('wed', this)"  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked @endif>
                                          <label class="form-check-label" for="flexCheck">
                                              closed
                                          </label>
                                      </span>
                                  </div>
                                      <div class="col-md-3 col-5 wed  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control clopen" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-3 col-5 wed  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control cltime" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2 wed  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                    @endif
                                    @if($day =="thu")
                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                      <span>Thu<br> 
                                          <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('thu', this)"  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked @endif>
                                          <label class="form-check-label" for="flexCheck">
                                              closed
                                          </label>
                                      </span>
                                  </div>
                                      <div class="col-md-3 col-5 thu  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control clopen" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-3 col-5 thu  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control cltime" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2 thu  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                    @endif
                                    @if($day =="fri")
                                    <div class="row pls ">
                                    <div class="col-md-2 col-5">                                      
                                      <span>Fri<br> 
                                          <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('fri', this)"   @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked @endif>
                                          <label class="form-check-label" for="flexCheck">
                                              closed
                                          </label>
                                      </span>
                                  </div>
                                      <div class="col-md-3 col-5 fri  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control clopen" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-3 col-5 fri  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control cltime" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2 fri  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                    @endif
                                    @if($day =="sat")
                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                      <span>Sat<br> 
                                          <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('sat', this)"  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) checked @endif>
                                          <label class="form-check-label" for="flexCheck">
                                              closed
                                          </label>
                                      </span>
                                  </div>
                                      <div class="col-md-3 col-5 sat  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control clopen" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-3 col-5 sat  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control cltime" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2 sat  @if($times['start'] =='00:00' && $times['end'] =='00:00' ) d-none @endif">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                    <?php $i++; ?>
                                    @endif
                                    @endforeach
                                    @else 


                                  
                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                        <span>Sun<br> 
                                            <input class="form-check-input " type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('sun', this)">
                                            <label class="form-check-label" for="flexCheck">
                                                closed
                                            </label>
                                        </span>
                                    </div>
                                    <div class="col-md-3 col-5 sun">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control clopen" id="clopenSun" value="09:00" placeholder="Enter email" name="opentime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-5 sun">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control cltime" id="cltimeSun" value="17:00" placeholder="Enter email" name="cltime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-2 sun">
                                        <div class="plusicon">+</div>
                                    </div>
                                </div>

                                <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                        <span>Mon<br> 
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('mon', this)">
                                            <label class="form-check-label" for="flexCheck">
                                                closed
                                            </label>
                                        </span>
                                    </div>
                                    <div class="col-md-3 col-5 mon">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control clopen" id="clopenMon" value="09:00" placeholder="Enter email" name="opentime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-5 mon">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control cltime" id="cltimeMon" value="17:00" placeholder="Enter email" name="cltime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-2 mon">
                                        <div class="plusicon">+</div>
                                    </div>
                                </div>


                                <div class="row pls">
                                <div class="col-md-2 col-5">                                      
                                    <span>Tue<br> 
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckTue" onchange="toggleTimeInputs('tue', this)">
                                        <label class="form-check-label" for="flexCheckTue">
                                            closed
                                        </label>
                                    </span>
                                </div>
                                <div class="col-md-3 col-5 tue">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control clopen" id="clopenTue" value="09:00" placeholder="Enter email" name="opentime[]">
                                    </div>
                                </div>
                                <div class="col-md-3 col-5 tue">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control cltime" id="cltimeTue" value="17:00" placeholder="Enter email" name="cltime[]">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2 tue">
                                    <div class="plusicon">+</div>
                                </div>
                            </div>

                            <div class="row pls">
                                <div class="col-md-2 col-5">                                      
                                    <span>Wed<br> 
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckWed" onchange="toggleTimeInputs('wed', this)">
                                        <label class="form-check-label" for="flexCheckWed">
                                            closed
                                        </label>
                                    </span>
                                </div>
                                <div class="col-md-3 col-5 wed">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control clopen" id="clopenWed" value="09:00" placeholder="Enter email" name="opentime[]">
                                    </div>
                                </div>
                                <div class="col-md-3 col-5 wed">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control cltime" id="cltimeWed" value="17:00" placeholder="Enter email" name="cltime[]">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2 wed">
                                    <div class="plusicon">+</div>
                                </div>
                            </div>
                            <div class="row pls">
                            <div class="col-md-2 col-5">                                      
                                <span>Thu<br> 
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckThu" onchange="toggleTimeInputs('thu', this)">
                                    <label class="form-check-label" for="flexCheckThu">
                                        closed
                                    </label>
                                </span>
                            </div>
                            <div class="col-md-3 col-5 thu">
                                <div class="mb-2 mt-2">
                                    <input type="time" class="form-control clopen" id="clopenThu" value="09:00" placeholder="Enter email" name="opentime[]">
                                </div>
                            </div>
                            <div class="col-md-3 col-5 thu">
                                <div class="mb-2 mt-2">
                                    <input type="time" class="form-control cltime" id="cltimeThu" value="17:00" placeholder="Enter email" name="cltime[]">
                                </div>
                            </div>
                            <div class="col-md-2 col-2 thu">
                                <div class="plusicon">+</div>
                            </div>
                        </div>

                        <div class="row pls">
                            <div class="col-md-2 col-5">                                      
                                <span>Fri<br> 
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckFri" onchange="toggleTimeInputs('fri', this)">
                                    <label class="form-check-label" for="flexCheckFri">
                                        closed
                                    </label>
                                </span>
                            </div>
                            <div class="col-md-3 col-5 fri">
                                <div class="mb-2 mt-2">
                                    <input type="time" class="form-control clopen" id="clopenFri" value="09:00" placeholder="Enter email" name="opentime[]">
                                </div>
                            </div>
                            <div class="col-md-3 col-5 fri">
                                <div class="mb-2 mt-2">
                                    <input type="time" class="form-control cltime" id="cltimeFri" value="17:00" placeholder="Enter email" name="cltime[]">
                                </div>
                            </div>
                            <div class="col-md-2 col-2 fri">
                                <div class="plusicon">+</div>
                            </div>
                        </div>

                        <div class="row pls">
                            <div class="col-md-2 col-5">                                      
                                <span>Sat<br> 
                                    <input class="form-check-input" type="checkbox" value="" id="flexCheckSat" onchange="toggleTimeInputs('sat', this)">
                                    <label class="form-check-label" for="flexCheckSat">
                                        closed
                                    </label>
                                </span>
                            </div>
                            <div class="col-md-3 col-5 sat">
                                <div class="mb-2 mt-2">
                                    <input type="time" class="form-control clopen" id="clopenSat" value="09:00" placeholder="Enter email" name="opentime[]">
                                </div>
                            </div>
                            <div class="col-md-3 col-5 sat">
                                <div class="mb-2 mt-2">
                                    <input type="time" class="form-control cltime" id="cltimeSat" value="17:00" placeholder="Enter email" name="cltime[]">
                                </div>
                            </div>
                            <div class="col-md-2 col-2 sat">
                                <div class="plusicon">+</div>
                            </div>
                        </div>

                            <!-- Repeat the above code for 'thu', 'fri', and 'sat' -->

                                <!-- Repeat the above code for other days -->


                                 
                                    @endif

                                    @else

                                        <div class="row pls">
                                        <div class="col-md-2 col-5">                                      
                                            <span>Sun<br> 
                                                <input class="form-check-input " type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('sun', this)">
                                                <label class="form-check-label" for="flexCheck">
                                                    closed
                                                </label>
                                            </span>
                                        </div>
                                        <div class="col-md-3 col-5 sun">
                                            <div class="mb-2 mt-2">
                                                <input type="time" class="form-control clopen" id="clopenSun" value="09:00" placeholder="Enter email" name="opentime[]">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-5 sun">
                                            <div class="mb-2 mt-2">
                                                <input type="time" class="form-control cltime" id="cltimeSun" value="17:00" placeholder="Enter email" name="cltime[]">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-2 sun">
                                            <div class="plusicon">+</div>
                                        </div>
                                    </div>

                                    <div class="row pls">
                                        <div class="col-md-2 col-5">                                      
                                            <span>Mon<br> 
                                                <input class="form-check-input" type="checkbox" value="" id="flexCheck" onchange="toggleTimeInputs('mon', this)">
                                                <label class="form-check-label" for="flexCheck">
                                                    closed
                                                </label>
                                            </span>
                                        </div>
                                        <div class="col-md-3 col-5 mon">
                                            <div class="mb-2 mt-2">
                                                <input type="time" class="form-control clopen" id="clopenMon" value="09:00" placeholder="Enter email" name="opentime[]">
                                            </div>
                                        </div>
                                        <div class="col-md-3 col-5 mon">
                                            <div class="mb-2 mt-2">
                                                <input type="time" class="form-control cltime" id="cltimeMon" value="17:00" placeholder="Enter email" name="cltime[]">
                                            </div>
                                        </div>
                                        <div class="col-md-2 col-2 mon">
                                            <div class="plusicon">+</div>
                                        </div>
                                    </div>


                                    <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                        <span>Tue<br> 
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckTue" onchange="toggleTimeInputs('tue', this)">
                                            <label class="form-check-label" for="flexCheckTue">
                                                closed
                                            </label>
                                        </span>
                                    </div>
                                    <div class="col-md-3 col-5 tue">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control clopen" id="clopenTue" value="09:00" placeholder="Enter email" name="opentime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-5 tue">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control cltime" id="cltimeTue" value="17:00" placeholder="Enter email" name="cltime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-2 tue">
                                        <div class="plusicon">+</div>
                                    </div>
                                </div>

                                <div class="row pls">
                                    <div class="col-md-2 col-5">                                      
                                        <span>Wed<br> 
                                            <input class="form-check-input" type="checkbox" value="" id="flexCheckWed" onchange="toggleTimeInputs('wed', this)">
                                            <label class="form-check-label" for="flexCheckWed">
                                                closed
                                            </label>
                                        </span>
                                    </div>
                                    <div class="col-md-3 col-5 wed">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control clopen" id="clopenWed" value="09:00" placeholder="Enter email" name="opentime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-5 wed">
                                        <div class="mb-2 mt-2">
                                            <input type="time" class="form-control cltime" id="cltimeWed" value="17:00" placeholder="Enter email" name="cltime[]">
                                        </div>
                                    </div>
                                    <div class="col-md-2 col-2 wed">
                                        <div class="plusicon">+</div>
                                    </div>
                                </div>
                                <div class="row pls">
                                <div class="col-md-2 col-5">                                      
                                    <span>Thu<br> 
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckThu" onchange="toggleTimeInputs('thu', this)">
                                        <label class="form-check-label" for="flexCheckThu">
                                            closed
                                        </label>
                                    </span>
                                </div>
                                <div class="col-md-3 col-5 thu">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control clopen" id="clopenThu" value="09:00" placeholder="Enter email" name="opentime[]">
                                    </div>
                                </div>
                                <div class="col-md-3 col-5 thu">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control cltime" id="cltimeThu" value="17:00" placeholder="Enter email" name="cltime[]">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2 thu">
                                    <div class="plusicon">+</div>
                                </div>
                            </div>

                            <div class="row pls">
                                <div class="col-md-2 col-5">                                      
                                    <span>Fri<br> 
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckFri" onchange="toggleTimeInputs('fri', this)">
                                        <label class="form-check-label" for="flexCheckFri">
                                            closed
                                        </label>
                                    </span>
                                </div>
                                <div class="col-md-3 col-5 fri">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control clopen" id="clopenFri" value="09:00" placeholder="Enter email" name="opentime[]">
                                    </div>
                                </div>
                                <div class="col-md-3 col-5 fri">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control cltime" id="cltimeFri" value="17:00" placeholder="Enter email" name="cltime[]">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2 fri">
                                    <div class="plusicon">+</div>
                                </div>
                            </div>

                            <div class="row pls">
                                <div class="col-md-2 col-5">                                      
                                    <span>Sat<br> 
                                        <input class="form-check-input" type="checkbox" value="" id="flexCheckSat" onchange="toggleTimeInputs('sat', this)">
                                        <label class="form-check-label" for="flexCheckSat">
                                            closed
                                        </label>
                                    </span>
                                </div>
                                <div class="col-md-3 col-5 sat">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control clopen" id="clopenSat" value="09:00" placeholder="Enter email" name="opentime[]">
                                    </div>
                                </div>
                                <div class="col-md-3 col-5 sat">
                                    <div class="mb-2 mt-2">
                                        <input type="time" class="form-control cltime" id="cltimeSat" value="17:00" placeholder="Enter email" name="cltime[]">
                                    </div>
                                </div>
                                <div class="col-md-2 col-2 sat">
                                    <div class="plusicon">+</div>
                                </div>
                            </div>

                                    @endif

                                  </div>


                    </form>
                  </div>

                  <a href="javascript:void(0)"
                    class="btn btn-danger bg-main text-white w-100 mt-2 py-2  rounded-pill  save-time"
                    style="color:red;">Save</a>
                </div>
              </div>
            </div>
	  
        
        @else
        <h3 class="m-3">Attraction not found.</h3>
        @endif
      </div>
    </div>
  </main>
  <!-- main page end  -->
  <!-- start footer  -->
  @include('footer')
  <!-- end footer  -->
  @if(!empty($searchresult))
  <!-- start lightbox  -->
  <div class="lightbox d-none vh-100 vw-100 py-4 px-md-5" style="top: 0;left: 0; z-index: 200000">
    <div class=" vh-100 text-white m-md-5 d-md-block d-flex flex-column justify-content-between">
      <div class="px-2">
        <div class="d-flex justify-content-between  mb-4">
          <div class="">
            <p class="mb-0">Historic Place</p>
            <h3 class=" fs-30">
              Lucknow, Imambada
            </h3>
          </div>

          <div class="close h-50 px-2 b-10" role="button">close X</div>
        </div>
      </div>
      <div class="img-box text-center">
        <img src="/images/image.png" class="img-fluid" alt="">
      </div>
      <div class="images px-3 px-md-0">
        <div class="my-3 d-flex align-items-center justify-content-between">
          <div class="d-flex align-items-center ">
            <img src="/images/user-circle.png" alt="">
            <div class="ms-2">
              <h6 class="mb-0">Al James</h6>
              <span class="small text-white-50">September 2021</span>
            </div>
          </div>
          <div class="d-flex align-items-center">
            <div class="" role="button"><i class="fas fa-share-alt me-2"></i>Share</div>
            <div class="ms-4 like" role="button"><i class="fas fa-thumbs-up me-2 "></i>Like
            </div>
            <!-- feather-thumbs-up -->
          </div>
        </div>
        <div class="overflow-hidden">
          <div class="owl-carousel owl-theme px-md-5">
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
            <div class="item">
              <img class="mx-1" src="/images/image1.png" width="72px" height="72px" alt="">
            </div>
          </div>
          <div class="text-center text-white">
            1 / 8
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- end lightbox  -->

  
  <!-- addphoto  -->
  <div class="add-photo d-none position-fixed d-flex align-items-center justify-content-center" id="add-photo">
    <div class="add-photo-box bg-white b-10 py-5 px-4 position-relative">
      <div class="container">
        <div role="button" class="close-photo rounded-pill px-3 position-absolute">close X</div>

        <h3 class=" mb-3 mt-3">Add Photos </h3>
        <span id="clonedata" >
          <span class="photo_error"></span>
       <div class="add-img-section clonesec border border-dark border-c b-10 my-3">
      <div class="d-flex align-items-md-center flex-md-nowrap flex-wrap m-3">
        <div class="add-img-section">
          <div class="field" align="left">
            <input type="file" name="files[]" class="dropzone" onchange="updateImagePreview(this)">
          </div>
          <div class="dropzone-desc" style="position: unset;margin:0; margin-top: 32px;">
            <img src="{{asset('images/Group.png')}}" width="81" height="57" alt="">
            <span class="text-decoration-underline">Upload Image</span>
          </div>
        </div>
        <input type="text" class="form-control mx-3 my-3 title" name="title[]" placeholder="Add image title">
        <span role="button" class="trash rounded-circle border p-4 d-inline-flex justify-content-center align-items-center" onclick="deleteImage(this)">
          <i class="fas fa-trash-alt"></i>
        </span>
      </div>
    </div>
        </div>

        </span>
        <button class="btn my-md-3 rounded-pill w-100 mt-2 py-2  border" onclick="addmoreimages()">+ Add another
          photo</button>
        <button class="btn btn-danger bg-main text-white w-100 mt-2 py-2  rounded-pill"
                    style="color:red;" id="save_photo" >Submit</button>
      </div>
    </div>
  </div>
  <!-- end add photo  -->


  
  <!-- add review  -->
  <div class="add_review add-photo d-none position-fixed d-flex align-items-center justify-content-center"
    id="add_review">
    <div class="add-photo-box bg-white b-10 py-5 px-4 position-relative">
      <div class="container-fluid">
        <div role="button" class="close-box close-photo rounded-pill px-3 position-absolute ">close X</div>

        <h3 class="text-md-center mb-3">Add Review</h3>
        <div class="add-img-section border border-dark border-c  b-10 my-3">
          <div class="d-flex align-items-md-center flex-md-nowrap flex-wrap m-3">
            <div class="container-fluid">
              <!-- <form > -->
              <div class="col-md-12">
                <div class="row">
                  <input type="text" class="form-control" placeholder="Enter Name" name="name" id="name">
                  <div class="error-msg" id="name-error"></div>
                </div>
                <div class="row mt-3">
                  <input type="text" class="form-control" placeholder="Enter Email" name="email" id="email">
                  <div class="error-msg" id="email-error"></div>
                </div>
                <div class="row mt-3">
                  <lable>Write your review here</lable>
                  <textarea name="review" id="review" cols="10" rows="3" class="form-control"></textarea>
                </div>
                <div class="error-msg" id="review-error"></div>
                <div class="star-rating py-4 border-bottom">
                  <p class="d-flex justify-content-between align-items-center">
                    <span>Would you recommend visiting {{$searchresult[0]->Title}}?</span>
                    <span><i class="" data-bs-toggle="collapse" href="#starcollapse" role="button" aria-expanded="false"
                        aria-controls="starcollapse"></i></span>
                  </p>
                  <div class="form-check">
                  <input class="form-check-input rating" type="radio" name="rating" value="1"  checked>
                 
                   Yes
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input rating" type="radio" name="rating" value="0"  >
              
                   No
                  </label>
                </div>
				 <div class="fs14 mb-15 fw-500">
                Add Photos
              </div>
                <section class="mb-24">
                <div class="form-group">
                  <div class="dropzone-wrapper">
                    <div class="dropzone-desc">
                      <img src="{{asset('images/Group.png')}}" width="32.727px" height=" 30px;" alt="">

                      <p><span class="text-decoration-underline">Upload a Photo</span> or drag and
                        drop</p>
                    </div>

                    <div class="field" align="left">
                      <input type="file" id="files" name="files[]" class="dropzone" multiple />
                    </div>
                  </div>
                </div>

                </section>
                  <!-- <div class="ratings d-flex collapse show" id="starcollapse">
                    @foreach(range(1, 5) as $index)
                    <span id="star{{ $index }}" class="star far fa-star"></span>
                    @endforeach

                  </div> -->
                  <div class="error-msg" id="rating-error"></div>
                </div>
              </div>
            </div>
            <!-- </form> -->
          </div>
          <button type="submit" class="btn btn-primary m-3 " id="addReview">Submit</button>
        </div>

      </div>
    </div>
  </div>
  <!-- end add photo  -->
  <!--add tip  -->
  <div class="add-tip d-none position-fixed d-flex align-items-center justify-content-center">
    <div class="add-tip-box bg-white b-10 py-5 px-4 position-relative">
      <div class="container-fluid">
        <div role="button" class="close-tip rounded-pill px-3 position-absolute">close X</div>
        <h3 class="text-md-center mb-3 fs-30">Add Tip</h3>
        <div class="">
          <p class="fs-18">Would you recommend visiting {{$searchresult[0]->Title}}?</p>
          <!-- <button class="btn bg-main rounded-pill text-white py-1 px-5">Yes</button>
                    <button class="btn border-dark rounded-pill py-1  ms-3 px-5">No</button> -->
          <!-- <form action="">
            <span> <input class="" type="radio" name="recommend" id="yes"><label role="button" class="ms-2"
                for="yes">Yes</label>
            </span> -->
            <span class="ms-2">
              <input class="" type="radio" ><label role="button" class="ms-2"
                for="no">Yes</label>
            </span>
          <!-- </form> -->
        </div>
        <div class="type-review">
          <textarea name="" id="" rows="6" class="form-control mt-3 b-10" placeholder="Write your review"></textarea>
        </div>
        <p class="mt-4 ">Add photos</p>
        <div class="add-tip-section  py-2 border-c  b-10 my-3">
          <p class="text-center">Drag & drop to upload or <a href="#" class="text-111">Browse</a></p>
          <div class="add d-flex align-items-center justify-content-center mb-2">
            <div class="img-box mx-2 position-relative">
              <img src="/images/modal.png" width="120px" height="120px" alt="modal">
              <i class="fas fa-trash-alt position-absolute rounded-circle bg-white border p-1 fa-xs" style="    top: -5px;
                            right: -5px;"></i>
            </div>
            <div class="img-box bg-white mx-2">
              <img src="/images/upload1.png" width="120px" height="120px" alt="add">
            </div>
          </div>
        </div>
        <button class="btn bg-main text-white w-100 mt-2 py-2  rounded-pill">Submit</button>
      </div>
    </div>
  </div>
 
  <!-- end add tip  -->
  @endif
  <?php 
  
  if(!empty($searchresult)){
    $longitude = $searchresult[0]->Longitude;
    $latitude = $searchresult[0]->Latitude;
  
  }else{
    $longitude = 0;
    $latitude = 0;
  }

  ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
  </script>
  <script type="application/ld+json">
  {
    @if(!empty($faq))
    "@context": "https://schema.org",
    "@type": "FAQPage",
    "mainEntity": [

      @foreach($faq as $faqItem) {
        "@type": "Question",
        "name": "{{$faqItem->Faquestion}}",
        "acceptedAnswer": {
          "@type": "Answer",
          "text": "{{$faqItem->Answer}}"
        }
      }

      @endforeach

    ]
    @endif
  }
  </script>
  <script type="application/ld+json">
  @if(!empty($searchresult))
  {
    "@context": "https://schema.org",
    "@type": "LocalBusiness",
    "name": "{{$searchresult[0]->Title}}",
    "image": [
      "{{ asset('//images/slider.png')}}"
    ],
    "url": "",
    "address": {
      "@type": "PostalAddress",
      "streetAddress": "{{$searchresult[0]->Address}}"
    },
    @if(!empty($sightreviews))
    "aggregateRating": {
      "@type": "AggregateRating",
      "ratingValue": "{{$searchresult[0]->TAAggregateRating}}  ",
      "reviewCount": "{{round($searchresult[0]->TATotalReviews)}} "
    },
    @endif "openingHours": "",
    "email": "",
    "telephone": "",
    "sameAs": ""
  }
  @endif
  </script>



  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="{{ asset('/js/jquery3.6.js')}}"></script>


  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script> 

  <script src="{{ asset('/js/explore.js')}}"></script>
  <!-- <script src="{{ asset('/js/header.js')}}"></script> -->


  
  <script src="{{ asset('/js/map_leaflet.js')}}"></script>
  <script src="https://unpkg.com/leaflet-simple-map-screenshoter"></script>
  <script src="https://unpkg.com/file-saver/dist/FileSaver.js"></script>

 


  <script src="{{ asset('/js/custom.js')}}"></script>

<script>

  var mapOptions = {
    center: [{{$latitude}}, {{$longitude}}],
    zoom: 10
  }
  var map = new L.map('map1', mapOptions);
  var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
  map.addLayer(layer);
  var customIcon = L.icon({
    iconUrl: '{{ asset("images/map-marker-icon.png")}}',
    iconSize: [32, 32], // Adjust the size as needed
    iconAnchor: [16, 32], // Adjust the anchor point if needed
  });
  var marker = L.marker([{{$latitude}}, {{$longitude}}], { icon: customIcon }).addTo(map); 
  var simpleMapScreenshoter = L.simpleMapScreenshoter().addTo(map);
  
  function captureScreenshot() { 
    simpleMapScreenshoter.takeScreen('blob', {     
    }).then(blob => { 
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
  window.onload = captureScreenshot;
</script>
<script>
$(document).ready(function() {
    if (window.File && window.FileList && window.FileReader) {
      $("#files").on("change", function(e) {
        var files = e.target.files,
          filesLength = files.length;
        for (var i = 0; i < filesLength; i++) {
          var f = files[i]
          var fileReader = new FileReader();
          fileReader.onload = (function(e) {
            var file = e.target;
            $("<span class=\"pip\">" +
              "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
              "<br/><span class=\"remove remove-image\"></span>" +
              "</span>").insertAfter("#files");
            $(".remove").click(function() {
              $(this).parent(".pip").remove();
            });

          });
          fileReader.readAsDataURL(f);
        }
        console.log(files);
      });
    } else {
      alert("Your browser doesn't support to File API")
    }
  });
  </script>
  <script>
        $('.defaultsearchvalue2').click(function (e) {
            e.preventDefault();
          
            $('#explore-tab').removeClass('active');
            $('#explore-tab-pane').removeClass('active show');


            
            $('#profile-tab').addClass('active');
            $('#profile-tab-pane').addClass('active show');

            
        });


        $('.defaultsearch').click(function (e) {
            e.preventDefault();
          
            $('#profile-tab').removeClass('active');
            $('#profile-tab-pane').removeClass('active show');
            $('#explore-tab').addClass('active');
            $('#explore-tab-pane').addClass('active show');


            

            
        });
    </script>
</body>


</html>