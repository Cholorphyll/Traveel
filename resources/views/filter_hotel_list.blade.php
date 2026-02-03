
          @forelse($searchresults as $searchresult)
          <div class="row mt-5">

            <div class="col-md-4">
              <div class="card-slider">

                <img src="{{('/images/heart.svg')}}" alt="" class="heart" />

                <div class="hotel-listing-slider fltr-listing-page">
                  <img src="{{('/images/Hotel lobby.svg')}}" alt="" />
              
                </div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="py-3 px-2 border-toend">
                <div class="d-flex text-neutral-2 align-items-center mb-2">
                  @for ($i = 0; $i < 5; $i++) @if($i < $searchresult->stars )
                    <img src="{{('/images/star.svg')}}" alt="" class="stars">
                    @else
                    <i class="far fa-star text-111"></i>
                    @endif
                    @endfor

                    <!-- <img src="{{('/images/star.svg')}}" alt="" class="stars">
                                <img src="{{('/images/star.svg')}}" alt="" class="stars">
                                <img src="{{('/images/star.svg')}}" alt="" class="stars">
                                <img src="{{('/images/star.svg')}}" alt="" class="stars"> -->
                </div>
                   <?php    $ctName = $lname;
                                $cityname = str_replace(' ', '_', $ctName);
                                $CountryName = str_replace(' ', '_', $countryname);
                                $url = $cityname .'-'.$CountryName;
                          ?>
                <span class="d-none "><a href=" {{ url('hd-' .$searchresult->hotelid .'-'.strtolower( str_replace(' ', '_', $searchresult->slug))) }}"> {{$searchresult->name}}</a></span>
                <div class="fs20 fs-sm-14 fw-500  mb-2 hotel-title"><a href=" {{ url('hd-'.$searchresults[0]->loc_id.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_', $searchresult->slug))) }}"> {{$searchresult->name}}</a></div>
                <div class="d-flex justify-content-between align-items-start locationandothers ">
                  <ul class="mb-2 mb-md-4 d-flex flex-wrap fs-sm-10 fs14">
                    <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                      <span>{{$searchresult->rating}}%</span>
                    </li>
                  
                  </ul>
                </div>


                <div class="d-flex color707070 align-items-center fs-sm-10 fs14  mb-2">
                  <div class="d-flex align-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                      <path
                        d="M10 9.25C10.6904 9.25 11.25 8.69036 11.25 8C11.25 7.30964 10.6904 6.75 10 6.75C9.30964 6.75 8.75 7.30964 8.75 8C8.75 8.69036 9.30964 9.25 10 9.25Z"
                        fill="#E86E2C" />
                      <path
                        d="M10 1.75C6.55391 1.75 3.75 4.43164 3.75 7.72656C3.75 9.2957 4.46523 11.3824 5.87578 13.9289C7.00859 15.9734 8.31914 17.8223 9.00078 18.7422C9.11596 18.8994 9.26656 19.0272 9.44037 19.1153C9.61418 19.2034 9.8063 19.2493 10.0012 19.2493C10.196 19.2493 10.3882 19.2034 10.562 19.1153C10.7358 19.0272 10.8864 18.8994 11.0016 18.7422C11.682 17.8223 12.9938 15.9734 14.1266 13.9289C15.5348 11.3832 16.25 9.29648 16.25 7.72656C16.25 4.43164 13.4461 1.75 10 1.75ZM10 10.5C9.50555 10.5 9.0222 10.3534 8.61107 10.0787C8.19995 9.80397 7.87952 9.41352 7.6903 8.95671C7.50108 8.49989 7.45157 7.99723 7.54804 7.51227C7.6445 7.02732 7.8826 6.58186 8.23223 6.23223C8.58186 5.8826 9.02732 5.6445 9.51227 5.54804C9.99723 5.45157 10.4999 5.50108 10.9567 5.6903C11.4135 5.87952 11.804 6.19995 12.0787 6.61107C12.3534 7.0222 12.5 7.50555 12.5 8C12.4993 8.66282 12.2357 9.29828 11.767 9.76697C11.2983 10.2357 10.6628 10.4993 10 10.5Z"
                        fill="#707070" />
                    </svg>
                    <div class="ms-1">
                      {{$searchresult->distance}} km to City centre
                    </div>
                  </div>
                  <!-- <div class="mx-4">
                    Excellent location!
                  </div>
                  <div class="hotel-rating">
                    9.3
                  </div> -->
                </div>
                <!-- <div class="color707070 fs14 mb-12">
                                Deluxe Double Room (2Adults + 1Child)
                            </div> -->
                <div class="color707070 fs14 mb-12">
                  {{$searchresult->propertyType}}
                </div>
                <div class="fs13 fw-500 mb-2">
                  Top Amenities
                </div>
                <div class="topamenities">
                  <!-- <div class="amens d-flex "> <img src="{{ asset('/images/wifi.svg')}}" alt="">Free Wifi</div>
                                <div class="amens d-flex "> <img src="{{ asset('/images/coffee.svg')}}" alt="">Coffee Shop</div>
                                <div class="amens d-flex "> <img src="{{ asset('/images/beer.svg')}}" alt="">Bar/lounge</div> -->

                  @php
                  $providedAmenities = [
                  "2 swimming pools",
                  "Beachfront",
                  "Free parking",
                  "Free WiFi",
                  "Non-smoking rooms",
                  "Family rooms",
                  "Restaurant",
                  "Fitness centre",
                  "Daily housekeeping",
                  "Bar",
                  "Lift",
                  "Breakfast",
                  "Air conditioning",
                  "Heating",
                  "Good breakfast",
                  "Laundry",
                  "Luggage storage",
                  "Tea/coffee maker in all rooms",
                  "Facilities for disabled guests",
                  "Airport shuttle (free)",
                  "Private beach area",
                  "Exceptional breakfast",
                  "Room service",
                  "Terrace",
                  "Spa and wellness centre",
                  "Skiing",
                  "3 swimming pools",
                  "Fabulous breakfast",
                  "Private parking",
                  "BBQ facilities",
                  "Water park",
                  "4 swimming pools",
                  "5 swimming pools",
                  "Garden",
                  "Designated smoking area",
                  "Indoor swimming pool",
                  "Very good breakfast",
                  "Outdoor swimming pool",
                  "Parking on site",
                  "Hot spring bath",
                  "Parking",
                  "Parking (free)",
                  "Parking on site",
                  "Tennis court",
                  "WiFi available in all areas",
                  "WiFi",
                  "Swimming Pool",
                  "Superb breakfast",
                  "Parking on site (free)",
                  "Parking on site"
                  ];

                  $amenitiesList = explode(',', $searchresult->amenities);
                  $matchingAmenities = [];
                  $remainingAmenities = [];

                  foreach ($providedAmenities as $amenity) {
                  $cleanedAmenity = trim($amenity);
                  if (in_array($cleanedAmenity, $amenitiesList)) {
                  $matchingAmenities[] = $cleanedAmenity;
                  }
                  }

                  @endphp

                  @php
                  $remainingCount = 10 - count($matchingAmenities);
                  @endphp

                  @foreach ($matchingAmenities as $amenity)
                  <div class="amens d-flex">
                    <img src="{{ asset('/images/wifi.svg')}}" alt="{{ $cleanedAmenity }}">
                    {{ $amenity }}
                  </div>
                  @endforeach

                  @php
                  $remainingAmenities = array_diff($amenitiesList, $matchingAmenities);
                  $remainingAmenities = array_slice($remainingAmenities, 0, $remainingCount);
                  @endphp

                  @foreach ($remainingAmenities as $amenity)
                  <div class="amens d-flex">
                    <img src="{{ asset('/images/wifi.svg')}}" alt="{{ $cleanedAmenity }}">
                    {{ $amenity }}
                  </div>
                  @endforeach

                </div>

              </div>
            </div>
            <div class="col-md-3 d-flex align-items-end py-3 selectdatesblock">
              <div>
                <div class="price">
                  <!-- <div class="old"> ₹700 </div> -->
                  <div class="new">${{$searchresult->pricefrom}}</div>
                </div>
                <!-- <div class="mb-20 fs12 color707070">
                                Includes taxes and Charges
                            </div> -->

                <div class="selectdates">
                  <div>
                    Get Price
                  </div> <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16" fill="none">
                    <path
                      d="M6.25586 11.6094L10.2029 8.11691C10.2195 8.10218 10.2329 8.08408 10.242 8.06382C10.2511 8.04355 10.2559 8.02157 10.2559 7.99934C10.2559 7.97711 10.2511 7.95514 10.242 7.93487C10.2329 7.9146 10.2195 7.89651 10.2029 7.88178L6.25586 4.389"
                      stroke="#262626" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </div>
              </div>
            </div>
          </div>

          @empty
          <h3 class="m-3">No Hotels available for this location.</h3>
          @endforelse


          <hr class="d-block">