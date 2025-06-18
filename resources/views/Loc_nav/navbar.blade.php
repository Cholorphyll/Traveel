<header id="header">
    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <div class="tr-header">
            <div class="tr-hamburgers-logo">
              <div class="tr-hamburgers"></div>
              <div class="tr-logo">
                <a href="/">
                  <img loading="lazy" src="images/travell-small-logo.png" alt="travell-logo">
                </a>
              </div>
            </div>
            <div class="tr-logo tr-desktop">
              <a href="/">
                <img loading="lazy" src="{{ asset('images/logo.png') }}" alt="travell-logo">
              </a>
            </div>
            <div class="tr-search-info-section" id="hotelSearchInfos">
              <div class="tr-utility-nav" >
                <div class="nav-item tr-location">Add location</div>
                <div class="nav-item tr-dates">Add date</div>
                <div class="nav-item tr-guest">Add guests</div>
                <div class="nav-item tr-search-btn-icon">
                  <button class="tr-btn tr-serach-modal"></button>
                </div>
                <button class="tr-edit-btn tr-mobile"></button>
              </div>
            </div>
            <div class="tr-nav-tabs">
              <div class="tr-explore-tab" data-tab="exploreForm"><span><img src="images/icons/compass-icon.svg" alt="Compass Icon">Explore</span></div>
              <div class="tr-hotel-tab active" data-tab="hotelForm"><span><img src="images/icons/clarity_building-line-black-icon.svg" alt="Clarity Building Line Icon"/>Hotel</span></div>
            </div>
            @if (session()->has('frontend_user'))
            <!-- <span class="getuser-nav"> -->
            <p class="" style="margin-top: 16px;
margin-right: 19px;">{{$Username}}</p>
                <li class="nav-item active ">
                <div class="dropdown">
                    <a class="nav-link p-0  dropdown-toggle" href="#"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                         <img src="@if($user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$user_image}}   @else {{ asset('images/Frame 61.svg') }} @endif" alt=""
                        class="usericon img-fluid rounded-circle" style="height: 49px;">
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                        <li><a class="dropdown-item" href="{{route('user_dashboard')}}">Dashboard</a></li>
                        <li><a class="dropdown-item" href="{{route('userlogout')}}">Logout</a></li>

                    </ul>
                </div>

                </li>
                <!-- </span> -->
             @else
            <li class="nav-item active">
                <a class="form-control" href="{{route('user_login')}}" role="button" style="background: #CB4C14;color: white;     text-decoration: none;">Sign in</a>

             </li>
             @endif
             <!-- <li class="nav-item active">
               <p> <a  href="{{route('user_login')}}" style="text-decoration: none;">dashboard</a></p>

             </li> -->
            </ul>
        </div>
          </div>
        </div>
      </div>
    </div>

    <div class="tr-find-hotels">
      <div class="tr-explore-and-hotel-form">
        <button type="button" class="btn-close" id="btnClose"></button>
        <div class="tr-nav-tabs tr-mobile">
          <div class="tr-explore-tab" data-tab="exploreForm"><span><img src="images/icons/compass-icon.svg" alt="Compass Icon">Explore</span></div>
          <div class="tr-hotel-tab active" data-tab="hotelForm"><span><img src="images/icons/clarity_building-line-black-icon.svg" alt="Clarity Building Line Icon"/>Hotel</span></div>
        </div>
        <form class="tr-explore-form" id="exploreForm">
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
                <input type="text" class="form-control" id="searchLocation" placeholder="Search location" name="where" autocomplete="off">
                <div class="tr-recent-searchs-modal" id="recentSearchLocation">
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
                <div class="col tr-form-btn">
                  <button type="button" class="tr-btn tr-mobile">Countinue</button>
                </div>
              </div>
            </div>
            <div class="col tr-form-btn">
              <button type="submit" class="tr-btn tr-popup-btn" id="hotelSearchSubmit">Search</button>
            </div>
          </div>
        </form>
        <form class="tr-hotel-form open" id="hotelForm">
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
                <input type="text" class="form-control" id="searchDestinations" placeholder="Search destinations" name="" autocomplete="off">
                <div class="tr-recent-searchs-modal" id="recentSearchsDestination">
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
                <div class="col tr-form-btn">
                  <button type="button" class="tr-btn tr-mobile">Countinue</button>
                </div>
              </div>
              <div class="col tr-form-booking-date">
                <div class="tr-form-checkin">
                  <label for="checkInInput1">Check in</label>
                  <input type="text" value="" class="form-control checkIn" id="checkInInput1" placeholder="Add dates" name="" autocomplete="off" readonly>
                </div>
                <div class="tr-form-checkout">
                  <label for="checkOutInput1">Check out</label>
                  <input type="text" value=""class="form-control checkOut" id="checkOutInput1" placeholder="Add dates" name="checkOut" autocomplete="off" readonly>
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
                <input type="text" class="form-control" id="totalRoomAndGuest" placeholder="Add guests" name="" autocomplete="off" readonly>
                <div class="tr-guests-modal" id="guestQtyModal">
                  <div class="tr-add-edit-guest tr-total-num-of-rooms">
                    <div class="tr-guest-type">
                      <label class="tr-guest">Room</label>
                    </div>
                    <div class="tr-qty-box">
                      <button class="minus disabled" value="minus">-</button>
                      <input type="text" id="totalRoom" value="0" id="" min="1" max="10" name="" readonly/>
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
                      <input type="text" id="totalAdultsGuest" value="0" id="" min="1" max="10" name="" readonly/>
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
                      <input type="text" id="totalChildrenGuest" value="0" id="" min="1" max="10" name="" readonly/>
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
                      <input type="text" id="totalChildrenInfants" value="0" id="" min="1" max="10" name="" readonly/>
                      <button class="plus" value="plus">+</button>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col tr-form-btn">
              <button type="submit" class="tr-btn tr-popup-btn" id="hotelSearchSubmit">Search</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </header>

  <!-- Mobile Navigation-->
  <div class="tr-mobile-nav-section">
    <div class="tr-mobile-nav-content">
      <button type="button" class="btn-nav-close" id=""></button>
      <div class="tr-nav-header">
        <div class="tr-logo">
          <img src="images/travell-small-logo.png" alt="travell small logo">
        </div>
        <div class="tr-location">London</div>
      </div>
      <div class="tr-mobile-nav-lists">
        <ul>
          <li><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 7.49984L10 1.6665L17.5 7.49984V16.6665C17.5 17.1085 17.3244 17.5325 17.0118 17.845C16.6993 18.1576 16.2754 18.3332 15.8333 18.3332H4.16667C3.72464 18.3332 3.30072 18.1576 2.98816 17.845C2.67559 17.5325 2.5 17.1085 2.5 16.6665V7.49984Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 18.3333V10H12.5V18.3333" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/></svg>Explore</li>
          <li><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 7.49984L10 1.6665L17.5 7.49984V16.6665C17.5 17.1085 17.3244 17.5325 17.0118 17.845C16.6993 18.1576 16.2754 18.3332 15.8333 18.3332H4.16667C3.72464 18.3332 3.30072 18.1576 2.98816 17.845C2.67559 17.5325 2.5 17.1085 2.5 16.6665V7.49984Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 18.3333V10H12.5V18.3333" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/></svg>Hotels</li>
          <li><svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.6677 8.21686L11.4782 8.25293L11.4075 8.43241L8.58835 15.5894L7.6097 15.7757L8.3748 9.30726L8.4309 8.83302L7.96178 8.92232L3.5739 9.75759L3.37156 9.79611L3.30699 9.99171L2.47522 12.5116L1.87823 12.6253L1.98228 9.2217L1.98466 9.14395L1.95388 9.07252L0.606627 5.94522L1.20367 5.83157L2.90392 7.86957L3.03583 8.02769L3.23812 7.98919L7.626 7.15392L8.09517 7.0646L7.86869 6.64412L4.77982 0.909331L5.75841 0.723048L11.0099 6.34373L11.1416 6.48469L11.3311 6.44861L15.7902 5.59979C16.0247 5.55515 16.2673 5.60549 16.4647 5.73973L16.6615 5.45033L16.4647 5.73973C16.6621 5.87398 16.798 6.08113 16.8426 6.31561C16.8873 6.55009 16.8369 6.79271 16.7027 6.99007L16.9921 7.18692L16.7027 6.99007C16.5685 7.18744 16.3613 7.3234 16.1268 7.36803L11.6677 8.21686Z" stroke="black" stroke-width="0.7"></path></svg>Flights</li>
          <li><svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.0835 9.43359H14.8335M3.58349 9.43359H5.83349M17.4116 5.68359L15.9485 1.78191C15.7289 1.19645 15.1693 0.808594 14.544 0.808594H6.12299C5.49773 0.808594 4.93804 1.19645 4.7185 1.78191L3.25537 5.68359M17.4116 5.68359L17.6299 6.26568C17.7524 6.59225 18.0646 6.80859 18.4133 6.80859C18.9068 6.80859 19.293 7.23341 19.2463 7.72462L18.8335 12.0586M17.4116 5.68359H18.9585M3.25537 5.68359L3.03708 6.26568C2.91462 6.59225 2.60243 6.80859 2.25366 6.80859C1.76023 6.80859 1.37395 7.23341 1.42073 7.72462L1.83349 12.0586M3.25537 5.68359H1.70849M1.83349 12.0586L1.95418 13.3258C2.0275 14.0957 2.67408 14.6836 3.44742 14.6836H3.5835M1.83349 12.0586V12.0586C1.55735 12.0586 1.3335 12.2824 1.3335 12.5586V15.0586C1.3335 15.4728 1.66928 15.8086 2.0835 15.8086H2.8335C3.24771 15.8086 3.5835 15.4728 3.5835 15.0586V14.6836M3.5835 14.6836H17.0835M17.0835 14.6836H17.2196C17.9929 14.6836 18.6395 14.0957 18.7128 13.3258L18.8335 12.0586M17.0835 14.6836V15.0586C17.0835 15.4728 17.4193 15.8086 17.8335 15.8086H18.5835C18.9977 15.8086 19.3335 15.4728 19.3335 15.0586V12.5586C19.3335 12.2825 19.1096 12.0586 18.8335 12.0586V12.0586M6.24161 3.33425L5.41255 5.82142C5.25067 6.30707 5.61214 6.80859 6.12406 6.80859H14.5429C15.0548 6.80859 15.4163 6.30707 15.2544 5.82142L14.4254 3.33425C14.2212 2.72174 13.648 2.68359 13.0024 2.68359H7.66463C7.01899 2.68359 6.44578 2.72174 6.24161 3.33425Z" stroke="black" stroke-width="0.7" stroke-linecap="round" stroke-linejoin="round"></path></svg>Cars</li>
        </ul>
      </div>
      <div class="tr-mobile-nav-lists">
        <ul>
          <li><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6719 6.14307H3.33854C2.41807 6.14307 1.67188 6.88926 1.67188 7.80973V16.1431C1.67188 17.0635 2.41807 17.8097 3.33854 17.8097H16.6719C17.5923 17.8097 18.3385 17.0635 18.3385 16.1431V7.80973C18.3385 6.88926 17.5923 6.14307 16.6719 6.14307Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.3385 17.8091V4.47575C13.3385 4.03372 13.1629 3.6098 12.8504 3.29724C12.5378 2.98468 12.1139 2.80908 11.6719 2.80908H8.33854C7.89651 2.80908 7.47259 2.98468 7.16003 3.29724C6.84747 3.6098 6.67188 4.03372 6.67188 4.47575V17.8091" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path></svg>Write a review</li>
          <li><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6719 6.14307H3.33854C2.41807 6.14307 1.67188 6.88926 1.67188 7.80973V16.1431C1.67188 17.0635 2.41807 17.8097 3.33854 17.8097H16.6719C17.5923 17.8097 18.3385 17.0635 18.3385 16.1431V7.80973C18.3385 6.88926 17.5923 6.14307 16.6719 6.14307Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.3385 17.8091V4.47575C13.3385 4.03372 13.1629 3.6098 12.8504 3.29724C12.5378 2.98468 12.1139 2.80908 11.6719 2.80908H8.33854C7.89651 2.80908 7.47259 2.98468 7.16003 3.29724C6.84747 3.6098 6.67188 4.03372 6.67188 4.47575V17.8091" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path></svg>Trips</li>
        </ul>
      </div>
      <div class="tr-mobile-nav-lists">
        <h4>Company</h4>
        <ul>
          <li><a href="javascript:void(0);">About us</a></li>
          <li><a href="javascript:void(0);">Contact us</a></li>
          <li><a href="javascript:void(0);">Traveller’s Choice</a></li>
          <li><a href="javascript:void(0);">Travel stories</a></li>
          <li><a href="javascript:void(0);">Help</a></li>
        </ul>
      </div>
      <div class="tr-actions">
        <button class="tr-btn tr-write-review">Sign up / Log in</button>
      </div>
    </div>
  </div>
