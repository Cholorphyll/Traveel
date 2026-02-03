   
        @if(!$searchresults->isEmpty())
          @foreach ($searchresults as $sresult)



          <div class="row mt-3">

            <div class="col-md-4">
              <div class="card-slider">

                <img src="{{('images/heart.svg')}}" alt="" class="heart" />

                <div class="hotel-listing-slider slick-initialized slick-slider">
                  <img src="{{('images/Hotel lobby.svg')}}" alt="" style="max-width: -webkit-fill-available;border-radius: 10px
px
;" />
                  <!-- <img src="{{('images/unsplash_7T1KOFfE1aM.png')}}" alt="" />
                                          <img src="{{('images/unsplash_7T1KOFfE1aM.png')}}" alt="" /> -->
                </div>
              </div>
            </div>
            <div class="col-md-5">
              <div class="py-3 px-2 border-toend">
                <div class="d-flex text-neutral-2 align-items-center mb-2">
                  @for ($i = 0; $i < 5; $i++) @if($i < $sresult->stars )
                    <img src="{{('images/star.svg')}}" alt="" class="stars">
                    @else
                    <i class="far fa-star text-111"></i>
                    @endif
                    @endfor

                    <!-- <img src="{{('images/star.svg')}}" alt="" class="stars">
                                          <img src="{{('images/star.svg')}}" alt="" class="stars">
                                          <img src="{{('images/star.svg')}}" alt="" class="stars">
                                          <img src="{{('images/star.svg')}}" alt="" class="stars"> -->
                </div>
                <?php  
                                          $ctName = $cityName;
				
                                          $cityname = str_replace(' ', '_', $ctName);
                                          $CountryName = str_replace(' ', '_', $countryname);
                                          $url = $cityname .'-'.$CountryName;
                                    ?>

			  
                    
                <div class="fs20 fs-sm-14 fw-500  mb-2 hotel-link"> <a href=" {{ url('hd-'.$sresult->loc_id.'-' .$sresult->id .'-'.strtolower( str_replace(' ', '_', str_replace('#', '!', $sresult->slug)))) }}">{{$sresult->name}}</a></div>
				  @if($sresult->rating != "")
                <div class="d-flex justify-content-between align-items-start locationandothers ">
                  <ul class="d-flex flex-wrap fs-sm-10 fs14">
                    <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                      <span>{{$sresult->rating}}%</span>
						
            
                    </li>
                    <!-- <li class=" d-flex align-items-center"><i class="fa  fa-circle" aria-hidden="true"></i> -->

                    <!-- <span><a href="">83 reviews</a></span> -->
                    </li>
                  </ul>
                </div>
				@endif

   @if(!empty($hotels->result))
            
            @foreach ($hotels->result as $searchresult)
            @if($searchresult->id == $sresult->hotelid)
                          <?php 
                          $i = 1;
                          $freeCancellationDisplayed = false;
                          $freeWifiDisplayed = false;
                          $depositDisplayed = false;
                          $breakfastDisplayed = false; 
                            foreach ($searchresult->rooms as $room) {
                          
                              if($i == 1){
                               echo  $room->desc.'<br>';
                              }
                              $i ++; 
                          

                              $options = $room->options;
                              $trueOptions = [];
                              $selectedOptions = "";
                              if(isset($room->options)){ 

                                  foreach ($options as $key => $value) {
                                         if (isset($value) == 1) {
                                         
                                       
                                          if (!$breakfastDisplayed && $key == "breakfast") {
                                            $breakfastDisplayed = true; // Set the flag to true
                                            echo 'Breakfast included<br>';
                                          }
                                        
                                        if (!$freeCancellationDisplayed && $key == "Free Cancellation") {
                                            $freeCancellationDisplayed = true; 
                                            echo 'Free Cancellation <br>';
                                        }
        
                                  
                                        if (!$freeWifiDisplayed && $key == "freeWifi") {
                                            $freeWifiDisplayed = true; 
                                            echo 'Free WiFi <br>';
                                        }
        
                                
                                        // if (!$depositDisplayed && $key == "deposit") {
                                        //     $depositDisplayed = true;
                                        //     echo ucfirst($key) . '<br>';
                                        // }
                                      }
                               
                              
                                 }

                              
                              } 
                         
                            ?>
                       
               
                     <?php      }
                          ?>       
                    @endif
                    @endforeach
                    @endif

                <div class="d-flex color707070 align-items-center fs-sm-10 fs14  mb-2">
                  <div class="d-flex align-items-center">
                    <svg xmlns="https://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                      <path
                        d="M10 9.25C10.6904 9.25 11.25 8.69036 11.25 8C11.25 7.30964 10.6904 6.75 10 6.75C9.30964 6.75 8.75 7.30964 8.75 8C8.75 8.69036 9.30964 9.25 10 9.25Z"
                        fill="#E86E2C" />
                      <path
                        d="M10 1.75C6.55391 1.75 3.75 4.43164 3.75 7.72656C3.75 9.2957 4.46523 11.3824 5.87578 13.9289C7.00859 15.9734 8.31914 17.8223 9.00078 18.7422C9.11596 18.8994 9.26656 19.0272 9.44037 19.1153C9.61418 19.2034 9.8063 19.2493 10.0012 19.2493C10.196 19.2493 10.3882 19.2034 10.562 19.1153C10.7358 19.0272 10.8864 18.8994 11.0016 18.7422C11.682 17.8223 12.9938 15.9734 14.1266 13.9289C15.5348 11.3832 16.25 9.29648 16.25 7.72656C16.25 4.43164 13.4461 1.75 10 1.75ZM10 10.5C9.50555 10.5 9.0222 10.3534 8.61107 10.0787C8.19995 9.80397 7.87952 9.41352 7.6903 8.95671C7.50108 8.49989 7.45157 7.99723 7.54804 7.51227C7.6445 7.02732 7.8826 6.58186 8.23223 6.23223C8.58186 5.8826 9.02732 5.6445 9.51227 5.54804C9.99723 5.45157 10.4999 5.50108 10.9567 5.6903C11.4135 5.87952 11.804 6.19995 12.0787 6.61107C12.3534 7.0222 12.5 7.50555 12.5 8C12.4993 8.66282 12.2357 9.29828 11.767 9.76697C11.2983 10.2357 10.6628 10.4993 10 10.5Z"
                        fill="#707070" />
                    </svg>
                    <div class="ms-1">
                      {{$sresult->distance}} km to City centre
                    </div>
                  </div>
                  <!-- <div class="mx-4">
                    Excellent location!
                  </div> -->
                  <!-- <div class="hotel-rating">
                    9.3
                  </div> -->
                </div>
           
                <div class="fs13 fw-500 mb-3 mb-md-2">
                  Top Amenities
                </div>
                <div class="topamenities">
                  <?php 
                $displayedAmenitiesCount = 0;
                 $amenity =explode(',',$sresult->amenities);  
                ?>
                  @if($sresult->amenities != "")
                  
                  @foreach($amenity as $mnt)
                  @if($displayedAmenitiesCount <= 10)
                  <div class="amens d-flex "> <img src="{{ asset('images/wifi.svg')}}" alt=""> {{ $mnt}}</div>

                  <?php   
                      $displayedAmenitiesCount++;

                   ?>
                   @else
                        @break  
                   @endif
                  @endforeach

                  @endif
                  <?php 
                $displayedAmenitiesCount = 0;
                 $rm =explode(',',$sresult->room_aminities);  
                ?>
                  @if($sresult->room_aminities != "")
                  
                  @foreach($rm as $roommnt)
                  @if($displayedAmenitiesCount <= 10)
                  <div class="amens d-flex "> <img src="{{ asset('images/wifi.svg')}}" alt=""> {{ $roommnt}}</div>

                  <?php   
                      $displayedAmenitiesCount++;

                   ?>
                   @else
                        @break  
                   @endif
                  @endforeach

                  @endif
                  
                  

                  <?php 
          
                 $lang =explode(',',$sresult->Languages);  
                ?>
                  @if($sresult->Languages != "")
                  
                  @foreach($lang as $lan)
               
                  <div class="amens d-flex "> <img src="{{ asset('images/wifi.svg')}}" alt=""> {{ $lan}}</div>

                 
                  
                  @endforeach

                  @endif

                </div>
              </div>
            </div>
<!--   $sresult->hotelid -->
            @if(!empty($hotels->result))
            
            @foreach ($hotels->result as $searchresult)
            @if($searchresult->id == $sresult->hotelid)
            <div class="col-md-3 d-flex align-items-end py-3 selectdatesblock">
              <?php

                      $lowestPrices = [];
                      $otherPrices = [];

                    
                          foreach ($searchresult->rooms as $room) {
                              $agencyName = $room->agencyName;
                              $price = $room->total;
                              $agencyId = $room->agencyId;
							 $fullurl = $room->fullBookingURL;
							     //room count code
                               if(isset($room->options)){ 
                            
                                  $roomcount=0;
                                  foreach ($options as $key => $value) {
                                      if (isset($value)) {
                                      
                                    
                                      if (!$breakfastDisplayed && $key == "available") {
                                          $roomcount =$value;
                                      }

                                    }
                                  }
                                }

                                  //room code
                              // Check if agencyName is already in the array or if the price is lower
                              if (!isset($lowestPrices[$agencyName]) || $price < $lowestPrices[$agencyName]['price']) {
                                  $lowestPrices[$agencyName] = ['price' => $price, 'fullBookingURL' => $fullurl, 'agencyId' => $agencyId,'roomcount'=>$roomcount];
                              }

                              // Store other prices
                              $otherPrices[$agencyName][] = ['price' => $price, 'fullBookingURL' => $fullurl,'agencyId' => $agencyId];
                          }
                      
                ?>

              <div class="w-100 px-5 px-md-0">
                <div class="price">
                  <!-- <div class="old text-decoration-line-through"> ₹700
                                          </div> -->
                  <!--  <div class="new">${{$searchresult->price}} </div>
					 First section for the first record -->
                  @foreach ($lowestPrices as $agencyName => $data)
                  @if ($loop->first)
                  <!-- First record -->
                  <div class="d-flex align-items-center justify-content-between p-api">
                    <img src="{{ 'https://pics.avs.io/hl_gates/100/40/' . $data['agencyId'] . '.png' }}"
                      alt="Agency Logo">
                    <div class="d-flex align-items-center ml-3" style="margin-left: 92px;">
                      <a href="{{ $data['fullBookingURL'] }}" target="_blank">
                        <div class="new">${{ $data['price'] }}</div>
                      </a>
                      <svg xmlns="https://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17" fill="none">
                        <g clip-path="url(#clip0_2493_66513)">
                          <path
                            d="M7.19399 5.33294L12.143 5.24941M12.143 5.24941L12.2266 10.1984M12.143 5.24941L5.88738 11.7199"
                            stroke="#222222" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </g>
                        <defs>
                          <clipPath id="clip0_2493_66513">
                            <rect width="12" height="12" fill="white"
                              transform="translate(0.53125 8.62695) rotate(-45.967)" />
                          </clipPath>
                        </defs>
                      </svg>
                    </div>
                  </div>
                  @endif
                  @endforeach
                </div>
                <div class="mb-20 fs12 color707070 text-start">
					 Price Per Night   @if($data['roomcount'] <= 5 && $data['roomcount'] != 0) <span class="ml-3" style="color:red;    margin-left: 55px;"> Only {{$data['roomcount']}} rooms left</span> @endif 
                </div>
                <button class="orangebutton">
                  <a href="{{$searchresult->fullUrl}}&currency=usd">View Deal</a>
                </button>


                @foreach ($lowestPrices as $agencyName => $data)
                @if (!$loop->first)
                <!-- Rest of the records -->
                <div class="d-flex align-items-center justify-content-between p-api">
                  <img src="{{ 'https://pics.avs.io/hl_gates/100/40/' . $data['agencyId'] . '.png' }}" alt="Agency Logo">
                  <div class="d-flex align-items-center">
                    <a href="{{ $data['fullBookingURL'] }}" target="_blank">${{ $data['price'] }}</a>
                    <svg xmlns="https://www.w3.org/2000/svg" width="18" height="17" viewBox="0 0 18 17" fill="none">
                      <g clip-path="url(#clip0_2493_66513)">
                        <path
                          d="M7.19399 5.33294L12.143 5.24941M12.143 5.24941L12.2266 10.1984M12.143 5.24941L5.88738 11.7199"
                          stroke="#222222" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                      </g>
                      <defs>
                        <clipPath id="clip0_2493_66513">
                          <rect width="12" height="12" fill="white"
                            transform="translate(0.53125 8.62695) rotate(-45.967)" />
                        </clipPath>
                      </defs>
                    </svg>
                  </div>
                </div>
                @endif
                @endforeach


                @endif
                @endforeach
                </div>
                </div>
                @endif
              </div>
         



     
          @endforeach


   

          @else
          <h3 class="m-3">No Hotels available for this location.</h3>
          @endif
          <hr class="d-block">
          @if(!$searchresults->isEmpty())
          <!-- {{ $searchresults->links() }} -->
          {{ $searchresults->links('hotellist_pagg.default') }}
          @endif