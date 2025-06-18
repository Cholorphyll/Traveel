<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Experience</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
    <!--  fontawesome -->

    <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" />


    <!-- <script src="assets/autoComplete.js"></script>
    <link rel="stylesheet" href="assets/autoComplete.min.css"> -->

    <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
	 <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
    <!-- nav css -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
	  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
   <!-- end nav css -->
<link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
	<script src="{{ asset('js/cookie-consent.js') }}"></script>
</head>

<body>
@include('Loc_nav.loc_navbar')




    @if(!$getexp->isEmpty())
    <div class="responsive-container container pt-5">

    <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
    @if(!$getexp->isEmpty())
    <ol class="breadcrumb">
                @if(!empty($breadcumb))
                     <li class="breadcrumb-item active" aria-current="page">
                         <a href="{{ route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])}}"> {{$breadcumb[0]->ccName}}</a>
                    </li>              
                    <li class="breadcrumb-item">
                        <a href="{{ route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])}}">
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
                    href="@if(!empty($getexp)){{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}@endif">
                    {{ $location['Name'] }}</a>
                </li>
                @endforeach
                @endif
                <li class="breadcrumb-item active" aria-current="page">
                    <a
                    href="{{ route('search.results',[$getexp[0]->slugid.'-'.strtolower($getexp[0]->LSlug)]) }}"><span>{{$getexp[0]->Lname}}</span>  
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{$getexp[0]->Name}}</li>               
              </ol>
              @endif
            </nav>

        <div class="topper-column">
            <div class=" ">
                <h2 class="fs-32 my-sm-13">
                    <b>{{$getexp[0]->Name}}</b>
                </h2>

                <div class="d-flex justify-content-between align-items-start locationandothers">
                    <ul class="d-flex flex-wrap">
                        <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                            <span>89%</span>
                        </li>
                        <li class=" d-flex align-items-center"><i class="fa  fa-circle" aria-hidden="true"></i>

                            <span>{{count($getexprv)}} reviews</span>
                        </li>
                        <li class=" d-flex align-items-center sm-mt-12"><i class="fa d-none d-md-inline-block fa-circle"
                                aria-hidden="true"></i>
                            <a href="" class="nowrap-small">{{$getexp[0]->Address}}</a>
                        </li>
                    </ul>

                    <ul class="d-flex savashare">
                        <li class="save d-flex align-items-center"><img src="{{asset('/images/save.svg')}}" alt="" class="mr-4 ">
                            <span class=""><a href="" class="text-decoration-none">Save</a></span>
                        </li>
                        <li class="d-flex align-items-center">
                            <img src="{{asset('/images/sahre.svg')}}" alt="" class="mr-4">
                            <span class=""><a href="">Share</a></span>
                        </li>
                    </ul>
                </div>

            </div>


            <!-- hotel-gallery -->


            <div class="hotel-gallery">
                <div class="row overflow-borderradius position-relative">
                    <div class="col-lg-6 ">
                      @if($getexp[0]->Img1 !="")
                      <img src="{{$getexp[0]->Img1}}" class="h-100 p1"
              alt="hotel image">  
                      @else
                      <img src="{{asset('/images/Hotel lobby.svg')}}" class="h-100 p1"
              alt="hotel image">  
                      @endif   
                    </div>

                    <div class="col-lg-6 d-none d-md-inline-block"> 
                        <div class="row">
                            <div class="col-md-6 p1">
                            @if($getexp[0]->Img2 !="")                               
                            <img src="{{$getexp[0]->Img2}}" 
              alt="hotel image">   
                            @else
                            <img src="{{asset('/images/Hotel lobby.svg')}}" 
              alt="hotel image">   
                            @endif
                            </div>

                            <div class="col-md-6 p1">
                            @if($getexp[0]->Img3 !="")   
                                <!-- <img src="{{ asset('/images/image 3.png') }}" alt=""> -->
                                <img src="{{$getexp[0]->Img3}}" 
              alt="hotel image">  
                             @else   
                             <img src="{{asset('/images/Hotel lobby.svg')}}" 
              alt="hotel image">
                             @endif
                            </div>

                            <div class="col-md-6 p1">
                                <!-- <img src="{{ asset('/images/image 4.png') }}" alt=""> -->
                                <img src="{{asset('/images/Hotel lobby.svg')}}" 
              alt="hotel image">     
                            </div>

                            <div class="col-md-6 p1">
                                <!-- <img src="{{ asset('/images/image 5.png') }}" alt=""> -->
                                <img src="{{asset('/images/Hotel lobby.svg')}}" class="card-img br-10 mb-12"
              alt="hotel image">     
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-dark d-flex view-more-photos"><img src="{{ asset('/images/grid.png') }}" alt=""
                            class="">View all photos</button>
                </div>
            </div>

        </div>


        <!-- Below gallery -->

        <div class="row flex-column-reverse flex-md-row">
            <div class="col-md-8">



                <section class="overview mb-30">
                    <h5 class="mb-8 fs24"><b>Overview</b></h5>


                    <div class="more">
                        <span itemprop="description">

                            <p class="fs15">{{$getexp[0]->about}}</p>
                        </span>
                    </div>





                </section>

                @if($getexp[0]->Duration != "")
                <div class="d-flex align-items-center mb-10">
                    <img src="{{ asset('/images/clock.svg') }}" alt="" class="mr-10">
                    <div class="fs14">{{$getexp[0]->Duration}} (approx.)</div>
                </div>
                @endif
                @if($getexp[0]->TicketType != "")
                  <div class="d-flex align-items-center mb-10">
                      <img src="{{ asset('/images/phone.svg') }}" alt="" class="mr-10">
                      <div class="fs14">{{$getexp[0]->TicketType}}</div>
                  </div>
               
                @endif
                <?php// return print_r($languageData) ?>
                @if(!$languageData->isEmpty())

                <div class="d-flex align-items-center">
                    <img src="{{ asset('/images/message.svg') }}" alt="" class="mr-10">
                    <div class="fs14">Offered in:
                    @foreach ($languageData as $langItem)
                       {{$langItem->Language}} @if(!$loop->last) , @endif @endforeach
                     
                    </div>
                </div>

                @endif

                <hr class="d-block">
                <div class="d-flex ">
                    <h6 class="mb-17 fs24"><b>Why Travelers Choose This Tour?</b></h6>
                </div>
                <div class="d-flex flex-wrap ">
                  <?php   $exclusiveArray = explode(',', $getexp[0]->Exclusive); ?>
                  @if(!$getexprv->isEmpty())
                  @foreach($getexprv as $getreview)
                    <div class="key-facts "><b>{{ substr($getreview->Description, 0, 70)}}</b></div>                  
                  @endforeach
                  @else
                        <p>Reviews not available.</p>
                    @endif
                </div>
                @if($getexp[0]->Inclusive != "")
                <hr class="d-block">

                <section>
                    <div class="d-flex ">
                        <h6 class="mb-20 fs24"><b>What’s Included</b></h6>
                    </div>

              
                    <?php $explode_inc = explode(',',$getexp[0]->Inclusive); ?>
                    @foreach($explode_inc as $explode_inc)
                    <div class="d-flex ammenity fw-500 mb-20"> <img src="{{ asset('/images/Check-Success.svg')}}" alt=""
                            class="mr-10">{{$explode_inc}}</div>
                    @endforeach
              
                </section>
                @endif
                <hr class="d-block">

                <section>
                    <div class="row align-items-start">

                        <div class="col-10 col-md-11">
                            <h6 class="mb-20 fs24"><b>Meeting And Pickup</b></h6>
                            <div class="d-flex ">
                                <h6 class="fs18 mb-24"><b>Meeting point</b></h6>
                            </div>
                            @if(!empty($iteneryday))
                                @php
                                   
                                    $sortedIteneryday = $iteneryday[$getexp[0]->ExperienceId]->sortBy('ItnineraryDay')->values()->all();
                                @endphp
                                
                                @foreach ($sortedIteneryday as $langItem)
                                    <div class="fw-500 mb-20"><a href="{{ asset('at-'.$langItem->slugid.'-'.$langItem->SightId.'-'.strtolower($langItem->Slug)) }}" target="_blank">Day: {{$langItem->ItnineraryDay}} - <span class="ml-5">{{$langItem->Name}}</span></a></div>
                                @endforeach
                            @endif



                            <!-- @if(!empty($iteneryday))
                            @foreach ($iteneryday[$getexp[0]->ExperienceId] as $langItem)

                            <div class="fw-500 mb-20 ">Day:  {{$langItem->ItnineraryDay}} - <span class="ml-5">  {{$langItem->Name}}</span>  </div>

                            @endforeach
                            @endif -->
                                <!-- Evan Evans Tours <br>
                                258 Vauxhall Bridge Rd, London SW1V 1BS, UK -->

                            <!-- <div class="fw-500 mb-42">
                                Boarding from 12.45pm / Departs at 1pm
                            </div> -->


                            <div class="d-flex ">
                                <h6 class="fs18 mb-24"><b>Start time</b> - <b>End Point</b></h6>
                            </div>

                            @if(!$itenerytime[$getexp[0]->ExperienceId]->isEmpty())
                            @foreach ($itenerytime[$getexp[0]->ExperienceId] as $itenerytime)

                            <div class="fw-500 mb-42">  {{$itenerytime->Start}}  -   {{$itenerytime->End}}</div>

                            @endforeach
                            @else
                            <p>Not available.</p>
                            @endif
                            <!-- <div class="fw-500 mb-42">01:00PM</div>

                            <div class="d-flex ">
                                <h6 class="fs18 mb-24"><b>End Point</b></h6>
                            </div>
                            <div class="fw-500 mb-20">Westminster Pier <br>
                                London SW1A 2JH, UK</div> -->

                            <!-- <div class="text-neutral-2">
                                <div class="fw500">
                                    Finishes approx. 5:30pm
                                </div>
                            </div> -->
                        </div>
                    </div>
                </section>
                <hr class="d-block">

                <section>
                    <h5 class="mb-51 fs24"><b>Similar Experiences</b></h5>
                    <div class="row bloglistingcarousel">
                        <!-- Hotel item -->

                        @if(!$nearby_exp->isEmpty())
                        @foreach($nearby_exp as $nearby_exp)
                        <div class="col-md-6 ">
                            <!-- Card START -->
                            <div class="card card-img-scale border-0 overflow-hidden bg-transparent">
                                <!-- Image and overlay -->
                                <div class="card-img-scale-wrapper rounded-3">
                                    <!-- Image -->
                                    @if($nearby_exp->Img1 !="")
                                    <img src="{{$nearby_exp->Img1}}" class="card-img br-10 mb-12" alt="hotel image">
                                    @else
                                    <img src="{{asset('/images/image 210.png')}}" class="card-img br-10 mb-12" alt="hotel image">
                                    @endif
                                </div>

                                <!-- Card body -->
                                <div class="">
                                    <!-- Title -->
                                    <div class="d-flex align-items-center justify-content-between mb-10">
                                        <a href="{{route('experince',[$nearby_exp->slugid.'-'.$nearby_exp->ExperienceId.'-'.$nearby_exp->Slug])}}"><b>{{$nearby_exp->Name}}</b></a>
                                        <li class="d-flex align-items-center"><i class="fa fa-heart"
                                                aria-hidden="true"></i>
                                            <span>89%</span>
                                        </li>
                                    </div>

                                    <a href="" class="mb-10 d-block text-decoration-none text-neutral-2 fw-500">Bus
                                        Tours</a>

                                    @if($nearby_exp->adult_price != "")
                                    <div class="mt-12">
                                        <b>${{$nearby_exp->adult_price}} per adult</b>
                                    </div>
                                 @endif
                                </div>
                            </div>
                            <!-- Card END -->
                        </div>
                        @endforeach
                        @endif
                    </div>




                </section>
                <hr class="d-block">
                <section class="mb-32">
                    <h5 class="mb-20 fs24"><b>More about Experience</b></h5>

                    <ul class="ps-4 fw-500 mb-20">
                        <li><b>Website</b> - @if($getexp[0]->website != "" ) <a href="{{$getexp[0]->website}}">{{$getexp[0]->website}}</a> @else Unavailable @endif </li>
                        <li><b>Free cancellation</b> - @if($getexp[0]->FreeCancellation != "")
                             @if($getexp[0]->FreeCancellation == "Free Cancellation")
                                Available
                             @else
                                 {{$getexp[0]->FreeCancellation}}       
                             @endif   
                              @endif </li>
                        <li><b>Pickup</b> - @if($getexp[0]->Pickup != "" && $getexp[0]->Pickup == "Yes") Available @else Unavailable @endif</li>
                        <li><b>Confirmation</b> - @if($getexp[0]->Confirmation != "" ) {{$getexp[0]->Confirmation}} @else Unavailable @endif</li>
                        <li><b>Duration</b> - @if($getexp[0]->Duration != "" ) {{$getexp[0]->Duration}} @else Unavailable @endif</li>
                        <li><b>Mobile Tickets</b> - @if($getexp[0]->mobile_tickets != "" ) {{$getexp[0]->mobile_tickets}} @else Unavailable  @endif
                    
                        </li>
                        @if($getexp[0]->WhatToExpect != "" ) 
                        <li><b>What To Expect</b> - @if($getexp[0]->WhatToExpect != "" ) {{$getexp[0]->WhatToExpect}}  </li>
                        @endif
                        @endif
                        
                    </ul>

                    </section>
                    
                    @if($getexp[0]->CancellationPolicy != "") 
                    <hr class="d-block">
                   <section class="mb-32">
                    <h5 class="mb-20 fs24"><b>Cancellation Policy</b></h5>                  
                      <p> {{$getexp[0]->CancellationPolicy}}</p>
                    </section>
                   @endif

                   <!-- @if($getexp[0]->AdditionalInformation != "") 
                    <hr class="d-block">
                   <section class="mb-32">
                    <h5 class="mb-20 fs24"><b>Additional Information</b></h5>                  
                      <p> {{$getexp[0]->AdditionalInformation}}</p>
                    </section>
                   @endif -->
                   
                   @if($getexp[0]->AllInformation != "") 
                    <hr class="d-block">
                   <section class="mb-32">
                    <h5 class="mb-20 fs24"><b>More Information</b></h5>                  
                      <p> {{$getexp[0]->AllInformation}}</p>
                    </section>
                   @endif
                <hr class="d-block">
                @if($getexp[0]->AdditionalInformation != "") 
                <section class="mb-32">
                    <h5 class="mb-20 fs24"><b>Accessibility</b></h5>

                    <p> {{$getexp[0]->AdditionalInformation}}</p>
                </section>
               
                <hr class="d-block">
                @endif
                <!-- <h5 class="mb-20 fs24"><b>Accessibility</b></h5>

                <ul class="ps-4 fw-500 mb-20 line-height-35">
                    <li>Not wheelchair accessible</li>
                    <li>Stroller accessible</li>
                    <li>Near  tConfirmation will be received at time of booking</li>
                    <li>The State Apartments at Windsor Castle are occasionally closed; on such occasions, your tour may
                        visit the castle precincts, St George's Chapel (except on Sundays) or Queen Mary's Dolls' House
                        instead</li>
                    <li>The Stonehenge Audio Guide is available to download in 12 different languages prior to the visit
                        or while you are on site. Please search for the ‘Stonehenge Audio Tour’ in your app store.</li>
                    <li>Lunch will be taken late in the afternoon (around 3pm).</li>
                    <li>The order of the visit may change and lunch may be replaced by supper.</li>
                    <li>When Windsor Castle is closed (currently closed on Tuesdays and Wednesdays) we will have free
                        time in the town of Windsor.</li>
                    <li>Travellers should have a moderate physical fitness level</li>
                    <li>St. George's Chapel is closed on Sundays. There will be extra time to explore the castle
                        precincts.</li>
                    <li>This experience requires good weather. If it’s cancelled due to poor weather, you’ll be offered
                        a different date or a full refund</li>
                    <li>This experience requires a minimum number of travellers. If it’s cancelled because the minimum
                        isn’t met, you’ll be offered a different date/experience or a full refund</li>
                    <li>This tour/activity will have a maximum of 75 travellersransportation</li>
                    <li>Infants must sit on laps</li>
                </ul>

                <hr class="d-block"> -->

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Ask a Question</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="your-question">
                                    <h6>Your Question</h6>
                                    <textarea name="question" id="" cols="30" rows="10"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="">Submit Question</button>
                            </div>
                        </div>
                    </div>
                </div>

            <!--     <hr class="d-block"> -->

                <section>
                    <div class="row align-items-start mb-74">
                        <div class="col-md-6">
                            <h5 class="fs24 mb-42"><b>Reviews ({{count($getexprv)}})</b></h5>
                        </div>
                        @if(!$getexprv->isEmpty())
                        <div class="col-md-6 d-flex align-items-start  justify-content-center justify-content-md-end">

                            <div class="dropdown flex-sm-1 mb-42 d-md-none mr-15">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    Sort By
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#">Action</a></li>
                                    <li><a class="dropdown-item" href="#">Another action</a></li>
                                    <li><a class="dropdown-item" href="#">Something else here</a></li>
                                </ul>
                            </div>
                            <button type="button" class="btn flex-sm-1 btn-outline-secondary">Write a Review</button>
                        </div>
                    </div>

                    <p class=""><b>Select topics to read reviews:</b></p>
                    <div class="d-flex flex-wrap mb-65">

                   
						@foreach($getexprv as $getrv)
						<div class="key-facts">  {{ substr($getrv->Description, 0, 70)}}</div>
						@endforeach
                       
					
                      
                    </div>
                    @endif
                </section>

                <section>
                @if(!$getexprv->isEmpty())
                    <div class="d-flex justify-content-end ">
                        <div class="dropdown mb-42 d-none d-md-inline-block">
                            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                Sort By
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Action</a></li>
                                <li><a class="dropdown-item" href="#">Another action</a></li>
                                <li><a class="dropdown-item" href="#">Something else here</a></li>
                            </ul>
                        </div>
                    </div>

                    <div class="row d-flex align-items-start">
                 
               
                        @foreach($getexprv as $review)
                        <div class="col-6 col-md-1 mt-3">
                            <img src="{{asset('/images/image 22.png')}}" class="w-51px " alt="">
                        </div>
                        <div class="col-md-3">
                            <h5 class=" fs18"><b>{{$review->Name}} </b></h5>
                            <div class="fs14">
                                <?php $rvdate = date('j F,Y',strtotime($review->CreatedOn)); ?>
                                asked a question on {{$rvdate}}
                            </div>
                        </div>
                        <div class="col-md-8 d-flex align-items-start">

                            <div>
                                <div class=" fs18 mb-10">
                                    <b> {{ substr($review->Description, 0, 70)}}</b>
                                </div>
                                <p>{{$review->Description}}</p>


                            </div>
                            <img src="{{asset('/images/dots.svg')}}" alt="" class="d-none d-md-inline-block" style="margin-left: 243px;"> 
                        </div>
                        @endforeach
                      
                    </div>
                    @else
                        Reviews not available.
                       
                        @endif
                </section>
                <!-- <hr class="d-block"> -->

             
            </div>


            <div class="col-md-4">

                <div class="check-in-checkout ">
                    <form action="">
                        <div class="dropdown-custom">

                            @if($getexp[0]->Cost !="")
                            <b class="d-block mb-20"><span class="fs14">From</span> <span
                                    class="fs24">${{$getexp[0]->Cost}}</span></b>
                            @endif
                            <div class="fs14 text-decoration-underline mb-26">Lowest Price Guarantee</div>
                            <div class="d-inline-flex dropdown-custom-toggle mb-13" style="font-weight: 600;">
                                <div class="form-field">
                                    <span class="roomcount"></span>
                                    <span class="rooms">Rooms </span>
                                </div>,
                                <div class="guest  ms-1"></div>
                                <span class=" ms-1"> Guest</span>
                                <img src="{{asset('/images/checron-bol-ddown.svg')}}" class="ms-12" alt="">
                            </div>
                            <ul class="custom-dropdown-menu p-0 border-0">
                                <div class="rooms-num room-info b-20 border bg-white">
                                    <div class="p-24">
                                        <div
                                            class="rooms counter mb-30 d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="person">Rooms</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span role="button" class="roomsdecrement roomsdec">-</span>
                                                <input type="number"
                                                    class="border d-flex align-items-center rounded-3 border-1 border-dark roominputfield"
                                                    placeholder="0" readonly>
                                                <span role="button" class="roomincrement roomsincdec">+</span>
                                            </div>
                                        </div>
                                        <div
                                            class="adults counter mb-30 d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="person">Adult</p>
                                                <p class="age"> Ages 13 or above</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span role="button" class="decrement dec">-</span>
                                                <input type="number"
                                                    class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                                                    placeholder="0" readonly>
                                                <span role="button" class="adultincrement incdec">+</span>
                                            </div>
                                        </div>

                                        <div
                                            class="adults counter mb-30 d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="person">Children</p>
                                                <p class="age"> Ages 2-12</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span role="button" id="childrenSubtract" class="decrement dec">-</span>
                                                <input type="number"
                                                    class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                                                    placeholder="0" readonly>
                                                <span role="button" id="childrenAdd"
                                                    class="adultincrement incdec">+</span>
                                            </div>
                                        </div>

                                        <div class="adults counter d-flex justify-content-between align-items-center">
                                            <div>
                                                <p class="person">Infants</p>
                                                <p class="age"> Under 2</p>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span role="button" class="decrement dec">-</span>
                                                <input type="number"
                                                    class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                                                    placeholder="0" readonly>
                                                <span role="button" class="adultincrement incdec">+</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="childrenDetails">

                                    </div>

                                </div>
                            </ul>
                        </div>

                        <div class="t-datepicker experiencepagedatepicker mb-32">
                            <div class="t-check-in"></div>
                            <div class="t-check-out d-none"></div>
                            <div class="ticme-picker d-flex"> <svg xmlns="http://www.w3.org/2000/svg" width="23"
                                    height="24" viewBox="0 0 23 24" fill="none">
                                    <path d="M16 14H13V9" stroke="#6A6A6A" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M13 21C17.4183 21 21 17.6421 21 13.5C21 9.35786 17.4183 6 13 6C8.58172 6 5 9.35786 5 13.5C5 17.6421 8.58172 21 13 21Z"
                                        stroke="#717171" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>

                                <span>
                                    Select Time
                                </span>
                            </div>


                        </div>



                        <div class="text-neutral-2 mb-20"> 3 options available for 21 July</div>


                        <div class="options-available">
                            <label class="card">
                                <input name="plan" class="radio" type="radio" checked>
                                <span class="plan-details">
                                    <div class="fs14 mb-4px"><b> Tour with All Entry and Lunch</b></div>
                                    <div class="fs12 text-decoration-underline mb-18">Reserve Now & Pay Later Eligible
                                    </div>
                                    <div class="text-neutral-2 fs14 mb-32"> Tour including entry to Windsor Castle,
                                        Stonehenge and Pub lunch</div>
                                    <div class="fs12 mb-10"><b>Total ₹32,322.43</b></div>
                                    <div class="fs12 fw-500 text-neutral-2"> (No additional taxes or booking fees)</div>
                                </span>
                            </label>
                            <label class="card">
                                <input name="plan" class="radio" type="radio">
                                <span class="plan-details" aria-hidden="true">
                                    <div class="fs14 mb-4px"><b> Tour with All Entry and Lunch</b></div>
                                    <div class="fs12 text-decoration-underline mb-18">Reserve Now & Pay Later Eligible
                                    </div>
                                    <div class="text-neutral-2 fs14 mb-32"> Tour including entry to Windsor Castle,
                                        Stonehenge and Pub lunch</div>
                                    <div class="fs12 mb-10"><b>Total ₹32,322.43</b></div>
                                    <div class="fs12 fw-500 text-neutral-2"> (No additional taxes or booking fees)</div>
                                </span>
                            </label>
                        </div>


                        <div class="text-decoration-underline fw-500 mb-30">See all 3 tour options</div>


                        <div class="fs12 text-neutral-2">
                            *Likely to sell out: Based on Viator’s booking data and information from the provider from
                            the
                            past 30 days, it seems likely this experience will sell out through Viator, a Tripadvisor
                            company. Reserve Now & Pay Later Eligible - Unsure of your plans? You can reserve a spot and
                            pay
                            for it later. Just click "Reserve Now" to see more payment options.Learn more
                        </div>

                        <button class="mb-32">Check Availiblity</button>

                        <div class="fs14 color707070">Free cancellation. Cancel anytime before 24 Aug for full refund</div>
                    </form>
                </div>
            </div>
        </div>

    </div>
    @else
      <p>Data not Found.</p>
@endif



 <!-- start footer  -->
 @include('footer')
  <!-- end footer  -->

<!--
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.2.0/readmore.min.js"></script>
    <script src="{{ asset('/js/exp/t-datepicker.min.js') }}"></script> -->

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/t-datepicker.min.js"></script>
<!--nav bar-->
<script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
<script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
<script src="{{ asset('/js/custom.js')}}"></script>

    <script src="{{ asset('/js/exp/exp.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('input').val('')
        });

        $('.bloglistingcarousel').each(function () {
            var slider = $(this);
            slider.slick({
                dots: true,
                autoplay: true,
                autoplaySpeed: 5000,
                mobileFirst: true,
                arrows: false,
                responsive: [{
                    breakpoint: 480,
                    settings: "unslick"
                }]
            });

        });




    </script>
</body>

</html>