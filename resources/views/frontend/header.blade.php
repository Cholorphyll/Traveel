<header>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="tr-header @if(!isset($type) || (isset($type) && $type !='hotel')) explore-page @endif  @if(isset($type) && $type =='hotel') hotel-page @endif" >
          <div class="tr-hamburgers-logo">
            <div class="tr-hamburgers"></div>
            <div class="tr-logo">
              <a href="/">
                <img src="{{ asset('/frontend/hotel-detail/images/travell-small-logo.png')}}"
                  alt="travell-logo">
              </a>
            </div>
          </div>
          <div class="tr-logo tr-desktop">
            <a href="/">
              <img src=" {{ asset('/frontend/hotel-detail/images/travell-logo.png')}}" alt="travell-logo">
            </a>
          </div>
          <div class="tr-search-info-section" id="hotelSearchInfos">
            <div class="tr-utility-nav">
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
                $slugid = isset($sessionData['slugid']) ? $sessionData['slugid'] : ''; 
                $slug = isset($sessionData['slug']) ? $sessionData['slug'] : ''; 
              ?>
              <div class="nav-item tr-location">@if(isset($lname) && $lname !="") {{$lname}} @else Add location @endif</div>
              <div class="nav-item tr-dates">{{ $checkin }}</div>
              <div class="nav-item tr-guest">{{ $guest }} @if($guest == 1 && $guest !="Add guests" ) guest @else guests @endif</div>
              <div class="nav-item tr-search-btn-icon">
                <button class="tr-btn tr-serach-modal"></button>
              </div>
              <button class="tr-edit-btn tr-mobile"></button>
              <div id="sessionData" 
                data-checkin="{{ $checkinDate ?? '' }}" 
                data-checkout="{{ $checkoutDate ?? '' }}" 
                data-guest="{{ session('guest') ?? '' }}" 
                data-rooms="{{ session('rooms') ?? '' }}" 
                data-slug="{{ session('slug') ?? '' }}">
              </div>
            </div>
          </div>
          <div class="tr-nav-tabs">
            <div class="tr-explore-tab  @if(!isset($type) || (isset($type) && $type != 'hotel')) active @endif" data-tab="exploreForm">
              <span><img src="{{asset('/frontend/hotel-detail/images/icons/compass-icon.svg')}}" alt="Compass Icon">Explore</span>
            </div>
            <div class="tr-hotel-tab @if(isset($type) && $type =='hotel') active @endif" data-tab="hotelForm">
              <span><img src="{{asset('/frontend/hotel-detail/images/icons/clarity_building-line-black-icon.svg')}}" alt="Clarity Building Line Icon" />Hotel</span>
            </div>
          </div>
          <div class="tr-login-section">
            <!--Below Button for signin - currently it hide-->
            <?php
              if (session()->has('frontend_user')) {
                $userData = session('frontend_user');
                $Username = $userData['Username'];
                $user_image = $userData['user_image'];
              }
            ?>
            @if (session()->has('frontend_user')) 
            <button class="tr-logged">
              <div class="tr-username">{{$Username}}</div>
            </button>
            <div class="tr-myaccount-modal">
              <div class="tr-mz-myaccount-info">
                <ul>
                  <li class="tr-my-profile-link"><a href="{{route('user_dashboard')}}">Dashboard</a></li>
                  <!-- <li class="tr-my-trip-link"><a href="javascript:void(0);"></a></li> -->
                </ul>
              </div>
              <div class="tr-mz-myaccount-info">
                <ul>
               <!--   <li class="tr-my-settings-link"><a href="javascript:void(0);">Settings</a></li>-->
                  <li class="tr-logout-link"><a href="{{route('userlogout')}}">Logout</a></li>
                </ul>
              </div>
            </div>
            @else
            <button class="tr-login" data-bs-toggle="modal" data-bs-target="#signInModal">Sign in</button>
            @endif
          </div>
        </div>
      </div>    
    </div>
  </div>
  <div class="tr-find-hotels">
    <div class="tr-explore-and-hotel-form">
      <button type="button" class="btn-close" id="btnClose"></button>
      <div class="tr-nav-tabs tr-mobile">
        <div class="tr-explore-tab " data-tab="exploreForm"><span><img
              src="{{asset('/frontend/hotel-detail/images/icons/compass-icon.svg')}}"
              alt="Compass Icon">Explore</span></div>
        <div class="tr-hotel-tab @if(isset($type) && $type =='hotel') active @endif" data-tab="hotelForm"><span><img
              src="{{asset('/frontend/hotel-detail/images/icons/clarity_building-line-black-icon.svg')}}" alt="Clarity Building Line Icon" />Hotel</span>
        </div>
      </div>
      <form class="tr-explore-form  @if(!isset($type) || (isset($type) && $type !='hotel')) open @endif" id="exploreForm">
        <div class="tr-form-section">
          <div class="tr-form-fields"> 
            <div class="col tr-mobile">
              <div class="tr-mobile-where">
                <label class="tr-lable">Where to?</label>
                <div class="tr-location-label">Search location</div>
              </div>
            </div>
            <div class="col tr-form-where">
              <div class="tr-mobile tr-close-btn">Where are you going?</div>
              <input type="text" id="searchlocation" type="search" value="@if(isset($lname) && $lname !='') {{$lname}} @else Add location @endif" name="search"
                placeholder=" Search Destination" autocomplete="off">
               
                <span class="d-none loc-slugid">@if(isset($lslugid)){{$lslugid}}@endif</span>
                <span class="d-none loc-slug">@if(isset($lslugid)){{$lslug}}@endif</span>
      <!--  Search Destination -->

              <div class="recent-his search-box-info  tr-recent-searchs-modal" id="recentSearchLocation">

                <p id="loc-list" class="px-4 autoCompletewrapper" style="width: fit-content;"></p>
              </div>
              <div class="col tr-form-btn">
                <button type="button" class="tr-btn tr-mobile">Countinue</button>
              </div>
            </div>
          </div>
          <div class="col tr-form-btn">
            <button type="submit" class="tr-btn tr-popup-btn exp-search" id="hotelSearchSubmit">Search</button>
          </div>
        </div>
      </form>
      <form class="tr-hotel-form @if(isset($type) && $type =='hotel') open @endif" id="hotelForm">
        <div class="tr-form-section">
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
             <input id="searchhotel" type="text" tabindex="1" value="@if(isset($lname) && $lname !='')  {{$lname}}@else &#xF002; Search @endif" placeholder="&#xF002; Search" autocomplete="off">
              <div class="hotel_recent_his search-box-info tr-recent-searchs-modal"
                id="recentSearchsDestination">

                <p id="hotel_loc_list" class="px-4 autoCompletewrapper" style="width: max-content;"></p>
              </div> 
              <span id="slug" class="d-none">@if($slug !=""){{$slug}}@endif</span>
     <span id="location-name" class="d-none">@if($lname !=""){{$lname}}@endif</span>
              <span id="hotel" class="d-none"></span>
              <span id="location_id" class="d-none">@if($slugid !=""){{$slugid}}@endif</span>
               
              <div class="col tr-form-btn">
                <button type="button" class="tr-btn tr-mobile">Countinue</button>
              </div>
            </div>
            <?php date_default_timezone_set('Asia/Kolkata'); 
                
                  if($checkinDate =="") {
                    $checkinDate = date('Y-m-d', strtotime(' +1 day'));  
                    $checkoutDate = date('Y-m-d', strtotime(' +4 day'));  
                  }
    
      ?>
            <div class="col tr-form-booking-date">
              <div class="tr-form-checkin">
                <label for="checkIn">Check in</label>
                <input type="text"  class="form-control dateInput t-input-check-in"
                  id="checkInInput1" value="{{ $checkinDate}}" placeholder="@if($checkinDate !=''){{ $checkinDate}}@else Add dates @endif" name="" autocomplete="off" readonly>
              </div>
              <div class="tr-form-checkout">
                <label for="checkOut">Check out</label>
                <input type="text" value="@if($checkoutDate !=''){{ $checkoutDate}}@else Add dates @endif" class="form-control dateInput t-input-check-out"
                  id="checkOutInput1" placeholder="@if($checkoutDate !=''){{ $checkoutDate}}@else Add dates @endif" name="checkOut" autocomplete="off" readonly>
              </div>
               <div class="tr-calenders-modal" id="calendarsModal">
                  <div id="calendarPair1" class="calendarPair">
                    <div class="navigation">
                      <button type="button" class="prevMonth" id="prevMonth1">Previous</button>
                      <button type="button" class="nextMonth" id="nextMonth1">Next</button>
                    </div>
                    <div class="custom-calendar checkInCalendar" id="checkInCalendar1">
                      <div class="monthYear"></div>
                      <div class="calendarBody"></div>
                    </div>
                    <div class="custom-calendar checkOutCalendar" id="checkOutCalendar1">
                      <div class="monthYear"></div>
                      <div class="calendarBody"></div>
                    </div>
                    <button type="button" class="tr-clear-details" hidden id="reset1">Clear dates</button>
                  </div>
                </div>
        
              <div class="col tr-form-btn">
                <button type="button" class="tr-btn tr-mobile">Next</button>
              </div>
            </div>

            <div class="col tr-form-who">
              <label for="totalRoomAndGuest">Who</label>
              <input type="text" class="form-control " id="totalRoomAndGuest" value='@if($rooms !="") @if($rooms == 1 ) Room: @else Rooms: @endif {{ $rooms }} @endif @if($guest !="") @if($guest == 1 && $guest !="Add guests" ) Adult: @else Adults: @endif {{ $guest }}  @else Add guests @endif' placeholder='@if($rooms !="") @if($rooms == 1 ) Room:@else Rooms:@endif{{ $rooms }} @endif @if($guest !="")@if($guest == 1 && $guest !="Add guests" ) Adult: @else Adults: @endif {{ $guest }}@else Add guests @endif'  name=""
                autocomplete="off" readonly >
              <div class="tr-guests-modal" id="guestQtyModal">
                <div class="tr-add-edit-guest tr-total-num-of-rooms">

          <div class="tr-guest-type">
            <label class="tr-guest">Room</label>
          </div>
          <div class="tr-qty-box">
            <button class="minus disabled" value="minus">-</button>
            <input type="text" id="totalRoom" value="@if($rooms !=''){{ $rooms }}@else 0 @endif" id="" min="1" max="10" name="" readonly />
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
                    <input type="text" id="totalAdultsGuest" class="totalguest" value="@if($guest !=''){{ $guest }}@else 0 @endif" id="" min="1" max="10"
                      name="" readonly />
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
                    <input type="text" id="totalChildrenGuest" class="totalguest" value="0" id="" min="1" max="10"
                      name="" readonly />
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
          </div>
          <div class="col tr-form-btn">
            <button type="submit" class="tr-btn tr-popup-btn filter-chackinout" id="hotelSearchSubmit">Search</button>
          </div>
        </div>
      </form>
    </div>
  </div>
 <script src="{{ asset('/js/explore_search.js') }}"></script>
</header>
@include('frontend.login_models')