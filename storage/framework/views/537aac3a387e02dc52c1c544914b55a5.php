<header>
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="tr-header <?php if(!isset($type) || (isset($type) && $type !='hotel')): ?> explore-page <?php endif; ?>  <?php if(isset($type) && $type =='hotel'): ?> hotel-page <?php endif; ?>" >
          <div class="tr-hamburgers-logo">
            <div class="tr-hamburgers"></div>
            <div class="tr-logo">
              <a href="/">
                <img src="<?php echo e(asset('/frontend/hotel-detail/images/travell-small-logo.png')); ?>"
                  alt="travell-logo">
              </a>
            </div>
          </div>
          <div class="tr-logo tr-desktop">
            <a href="/">
              <picture>
                <source srcset="<?php echo e(asset('/frontend/hotel-detail/images/travell-logo.webp')); ?>" type="image/webp">
                <img src=" <?php echo e(asset('/frontend/hotel-detail/images/travell-logo.webp')); ?>" alt="travell-logo" loading="lazy" class="lcp-image">
              </picture>
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
              <?php
                  $locationText = 'Add location';
                  if (isset($searchresult) && is_array($searchresult) && !empty($searchresult[0]) && isset($searchresult[0]->name) && $searchresult[0]->name != '') {
                      $locationText = $searchresult[0]->name;
                  } elseif (isset($lname) && $lname != '') {
                      $locationText = $lname;
                  }
              ?>
              <div class="nav-item tr-location"><?php echo e($locationText); ?></div>
              <div class="nav-item tr-dates"><?php echo e($checkin); ?></div>
              <div class="nav-item tr-guest"><?php echo e($guest); ?> <?php if($guest == 1 && $guest !="Add guests" ): ?> guest <?php else: ?> guests <?php endif; ?></div>
              <div class="nav-item tr-search-btn-icon">
                <button class="tr-btn tr-serach-modal"></button>
              </div>
              <button class="tr-edit-btn tr-mobile"></button>
              <div id="sessionData" 
                data-checkin="<?php echo e($checkinDate ?? ''); ?>" 
                data-checkout="<?php echo e($checkoutDate ?? ''); ?>" 
                data-guest="<?php echo e(session('guest') ?? ''); ?>" 
                data-rooms="<?php echo e(session('rooms') ?? ''); ?>" 
                data-slug="<?php echo e(session('slug') ?? ''); ?>">
              </div>
            </div>
          </div>
          <div class="tr-nav-tabs">
            <div class="tr-explore-tab  <?php if(!isset($type) || (isset($type) && $type != 'hotel')): ?> active <?php endif; ?>" data-tab="exploreForm">
              <span><img src="<?php echo e(asset('/frontend/hotel-detail/images/icons/compass-icon.svg')); ?>" alt="Compass Icon">Explore</span>
            </div>
            <div class="tr-hotel-tab <?php if(isset($type) && $type =='hotel'): ?> active <?php endif; ?>" data-tab="hotelForm">
              <span><img src="<?php echo e(asset('/frontend/hotel-detail/images/icons/clarity_building-line-black-icon.svg')); ?>" alt="Clarity Building Line Icon" />Hotel</span>
            </div>
          </div>
          <div class="tr-login-section">
            <!-- Unified auth check: Laravel Auth (e.g., Google) OR legacy session('frontend_user') -->
            <?php
              $isLoggedIn = false;
              $Username = '';
              $user_image = null;
              try {
                if (Auth::check()) {
                  $isLoggedIn = true;
                  $authUser = Auth::user();
                  $Username = $authUser->name ?? ($authUser->email ?? 'User');
                  // If your users table has an avatar/photo column, use it; otherwise keep null.
                  $user_image = $authUser->avatar ?? ($authUser->photo ?? null);
                } elseif (session()->has('frontend_user')) {
                  $isLoggedIn = true;
                  $userData = session('frontend_user');
                  $Username = $userData['Username'] ?? '';
                  $user_image = $userData['user_image'] ?? null;
                }
              } catch (\Throwable $e) { /* ignore */ }
            ?>
            <?php if($isLoggedIn): ?>
            <button class="tr-logged">
              <div class="tr-username"><?php echo e($Username); ?></div>
            </button>
            <div class="tr-myaccount-modal">
              <div class="tr-mz-myaccount-info">
                <ul>
                 <li class="tr-my-profile-link"><a href="<?php echo e(route('profile.trips')); ?>">My Profile</a></li>
                  <li class="tr-my-settings-link"><a href="<?php echo e(route('user.settings')); ?>">Settings</a></li>
                  <li class="tr-logout-link"><a href="<?php echo e(route('userlogout')); ?>">Logout</a></li>
                </ul>
              </div>
            </div>
            <?php else: ?>
            <button class="tr-login" data-bs-toggle="modal" data-bs-target="#signInModal">Sign in</button>
            <?php endif; ?>
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
              src="<?php echo e(asset('/frontend/hotel-detail/images/icons/compass-icon.svg')); ?>"
              alt="Compass Icon">Explore</span></div>
        <div class="tr-hotel-tab <?php if(isset($type) && $type =='hotel'): ?> active <?php endif; ?>" data-tab="hotelForm"><span><img
              src="<?php echo e(asset('/frontend/hotel-detail/images/icons/clarity_building-line-black-icon.svg')); ?>" alt="Clarity Building Line Icon" />Hotel</span>
        </div>
      </div>
      <form class="tr-explore-form  <?php if(!isset($type) || (isset($type) && $type !='hotel')): ?> open <?php endif; ?>" id="exploreForm">
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
              <input type="text" id="searchlocation" type="search" value="<?php if(isset($lname) && $lname !=''): ?> <?php echo e($lname); ?> <?php else: ?> Add location <?php endif; ?>" name="search"
                placeholder=" Search Destination" autocomplete="off">
               
                <span class="d-none loc-slugid"><?php if(isset($lslugid)): ?><?php echo e($lslugid); ?><?php endif; ?></span>
                <span class="d-none loc-slug"><?php if(isset($lslugid)): ?><?php echo e($lslug); ?><?php endif; ?></span>
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
      <form class="tr-hotel-form <?php if(isset($type) && $type =='hotel'): ?> open <?php endif; ?>" id="hotelForm">
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
             <?php
                 $hotelSearchValue = '&#xF002; Search';
                 if (isset($searchresult) && is_array($searchresult) && !empty($searchresult[0]) && isset($searchresult[0]->name) && $searchresult[0]->name != '') {
                     $hotelSearchValue = $searchresult[0]->name;
                 } elseif (isset($lname) && $lname != '') {
                     $hotelSearchValue = $lname;
                 }
             ?>
             <input id="searchhotel" type="text" tabindex="1" value="<?php echo e($hotelSearchValue); ?>" placeholder="&#xF002; Search" autocomplete="off">
              <div class="hotel_recent_his search-box-info tr-recent-searchs-modal"
                id="recentSearchsDestination">

                <p id="hotel_loc_list" class="px-4 autoCompletewrapper" style="width: max-content;"></p>
              </div> 
              <span id="slug" class="d-none"><?php if($slug !=""): ?><?php echo e($slug); ?><?php endif; ?></span>
     <span id="location-name" class="d-none"><?php if(isset($lname) && $lname != ""): ?><?php echo e($lname); ?><?php endif; ?></span>
              <span id="hotel" class="d-none"></span>
              <span id="location_id" class="d-none"><?php if($slugid !=""): ?><?php echo e($slugid); ?><?php endif; ?></span>
               
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
                  id="checkInInput1" value="<?php echo e($checkinDate); ?>" placeholder="<?php if($checkinDate !=''): ?><?php echo e($checkinDate); ?><?php else: ?> Add dates <?php endif; ?>" name="" autocomplete="off" readonly>
              </div>
              <div class="tr-form-checkout">
                <label for="checkOut">Check out</label>
                <input type="text" value="<?php if($checkoutDate !=''): ?><?php echo e($checkoutDate); ?><?php else: ?> Add dates <?php endif; ?>" class="form-control dateInput t-input-check-out"
                  id="checkOutInput1" placeholder="<?php if($checkoutDate !=''): ?><?php echo e($checkoutDate); ?><?php else: ?> Add dates <?php endif; ?>" name="checkOut" autocomplete="off" readonly>
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
              <input type="text" class="form-control " id="totalRoomAndGuest" value='<?php if($rooms !=""): ?> <?php if($rooms == 1 ): ?> Room: <?php else: ?> Rooms: <?php endif; ?> <?php echo e($rooms); ?> <?php endif; ?> <?php if($guest !=""): ?> <?php if($guest == 1 && $guest !="Add guests" ): ?> Adult: <?php else: ?> Adults: <?php endif; ?> <?php echo e($guest); ?>  <?php else: ?> Add guests <?php endif; ?>' placeholder='<?php if($rooms !=""): ?> <?php if($rooms == 1 ): ?> Room:<?php else: ?> Rooms:<?php endif; ?><?php echo e($rooms); ?> <?php endif; ?> <?php if($guest !=""): ?><?php if($guest == 1 && $guest !="Add guests" ): ?> Adult: <?php else: ?> Adults: <?php endif; ?> <?php echo e($guest); ?><?php else: ?> Add guests <?php endif; ?>'  name=""
                autocomplete="off" readonly >
              <div class="tr-guests-modal" id="guestQtyModal">
                <div class="tr-add-edit-guest tr-total-num-of-rooms">

          <div class="tr-guest-type">
            <label class="tr-guest">Room</label>
          </div>
          <div class="tr-qty-box">
            <button class="minus disabled" value="minus">-</button>
            <input type="text" id="totalRoom" value="<?php if($rooms !=''): ?><?php echo e($rooms); ?><?php else: ?> 0 <?php endif; ?>" id="" min="1" max="10" name="" readonly />
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
                    <input type="text" id="totalAdultsGuest" class="totalguest" value="<?php if($guest !=''): ?><?php echo e($guest); ?><?php else: ?> 0 <?php endif; ?>" id="" min="1" max="10"
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
 <script src="<?php echo e(asset('/js/explore_search.js')); ?>"></script>
 <script src="<?php echo e(asset('/js/sign_in.js')); ?>"></script>
<script src="<?php echo e(asset('/js/custom.js')); ?>?v=<?php echo e(filemtime(public_path('js/custom.js'))); ?>"></script>
</header>
<?php echo $__env->make('frontend.login_models', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\tavelll\resources\views/frontend/header.blade.php ENDPATH**/ ?>