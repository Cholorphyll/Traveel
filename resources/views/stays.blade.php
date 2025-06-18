<!DOCTYPE html>
<html lang="en-US">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
 <title>Compare Hotel Prices Worldwide - Find the Best Deals on Your Stay | Travell (2025)</title>
  <meta name="description"
    content="Discover the perfect stay at the best price. Compare hotel prices from top travel websites in one place and book your ideal accommodation with ease. Save time and money on your next trip!">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}">
  </script>

  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
<link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
	<script src="{{ asset('js/cookie-consent.js') }}"></script>
</head>

<body>
  <!--HEADER-->
    @include('frontend.header_without_search')

	<!-- Mobile Navigation-->
	@include('frontend.mobile_nav')
	
  <div class="tr-listing-without-dates-2">
  <div class="container">
    <div class="row">
      <div class="col-sm-12 ">
        <div class="tr-heading-section">
          <h1>Stay somewhere Great</h1>
          <h2>Compare prices from 70+ Hotels websites in just a single click</h2>
        </div>

        <!--HOTEL SEARCHES FORM- START-->
        <div class="tr-search-hotel">
          <form class="tr-hotel-form" id="hotelForm3">
            <div class="tr-form-section">
              <div class="tr-date-section"> 
              <input type="text" class="tr-room-guest" placeholder="1 room, 2 guests" id="totalRoomAndGuest" value="" name="" readonly="">  
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
                  <input type="text" class="form-control" id="searchhotel" placeholder="Search Location" value="" name="" autocomplete="off">

                  <div class="tr-recent-searchs-modal tr-custom-scrollbar" id="recentSearchsDestination">
                    <p id="hotel_loc_list" class="autoCompletewrapper"></p>
                  </div>
                  <span id="slug" class="d-none"></span>
                  <span id="hotel" class="d-none"></span>
                  <span id="location_id" class="d-none"></span>

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

        <!--RECENT SEARCHES - START-->
          @if(session('recent_searches'))
        <div class="tr-recent-searches-hotel-section">
          <h3>Recent searches</h3>
          <div class="tr-recent-searches-hotel-listing">
            <div class="tr-recent-searches-hotel-lists">
              @if(session('recent_searches'))
              @foreach(session('recent_searches') as $search)
              <?php
                // Format check-in and check-out dates
                $checkinFormatted = date('d-M-Y', strtotime($search['checkin']));
                $checkoutFormatted = date('d-M-Y', strtotime($search['checkout']));
                
                // Construct the URL
                 $url = url('ho-' .$search['slug']) . 
                            '?checkin=' . $checkinFormatted .
                '&checkout=' . $checkoutFormatted .
                '&locationid=' . $search['locationid'] .
                '&lid=' .
                '&rooms=' . $search['rooms'] .
                '&guest=' . $search['guest'];

            ?>
         
              <div class="tr-recent-searches-hotel-list">
                <a href="{{ $url }}">
                  <div class="tr-city-name">{{ $search['fullname'] }}</div>
                  <div class="tr-booking-dates">
                    <?php
                              // Format check-in date
                              $checkinDate = new DateTime($search['checkin']);
                              $checkinFormatted = $checkinDate->format('D, d M');
                              
                              // Format check-out date
                              $checkoutDate = new DateTime($search['checkout']);
                              $checkoutFormatted = $checkoutDate->format('D, d M');
                          ?>
                    {{ $checkinFormatted }} - {{ $checkoutFormatted }}
                  </div>
                </a>
              </div>
              @endforeach

              @endif

            </div>
          </div>
        </div>
       @endif
        <!--RECENT SEARCHES - END-->

        <!--PARTNERS - START-->
        <div class="tr-partners-section">
          <div class="tr-partners-title">70+ Partners :</div>
          <div class="tr-partners-lists">
            <div class="tr-partners-list">
              <img src="{{ asset('frontend/hotel-detail/images/booking.png')}}" alt="Booking" />
            </div>
            <div class="tr-partners-list">
              <img src="{{ asset('frontend/hotel-detail/images/expedia.png')}}" alt="expedia" />
            </div>
            <div class="tr-partners-list">
              <img src="{{ asset('frontend/hotel-detail/images/agoda.png')}}" alt="agoda" />
            </div>
            <div class="tr-partners-list">
              <img src="{{ asset('frontend/hotel-detail/images/trip.png')}}" alt="trip" />
            </div>
          </div>
        </div>
        <!--PARTNERS - END-->

        <!--More Places - START-->
        <div class="tr-more-places responsive-container">
          <h3 class="d-none d-md-block">You might like these</h3>
          <h3 class="d-block d-sm-block d-md-none">Top hotels</h3>
          <div class="tr-sub-title d-none d-md-block">More places to stay</div>
          <div class="row tr-more-places-lists filter-listing">
          @forelse($searchresults as $searchresult)
          <div class="tr-more-places-list">
            <div class="tr-hotel-img">
              <a href="javascript:void(0):">
                @if ($searchresult->image == 1)
                <a href="{{ url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}" target="_blank" title="{{$searchresult->name}}"><img src="https://s3-us-west-2.amazonaws.com/s3-travell/hotel-images/{{$searchresult->id}}.jpg" alt=""
                  style="width: 100%; height: auto; border-radius: 10px;"></a>
                @else
               <a href="{{ url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}" target="_blank" title="{{$searchresult->name}}"> <img src="https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_1/580/443.jpg"
                  alt="{{$searchresult->name}}" style="width: 100%; height: auto; border-radius: 10px;"></a>
                @endif
              </a>
            </div>
            <div class="tr-hotel-deatils">
              <div class="tr-hotel-city">{{$searchresult->cityName}}</div>
              <div class="tr-hotel-name">
                <a href="{{ url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}" target="_blank" title="{{$searchresult->name}}">{{$searchresult->name}}</a>
              </div>
              <div class="tr-hotel-facilities">
                <ul>                   
                  @if($searchresult->distance !="") <li>{{$searchresult->distance}}  miles to city </li>@endif 
                </ul>
                <ul>
                  @if(!empty($amenities))
                  @foreach(explode(',', $amenities) as $amenity)
                  <li>{{ trim($amenity) }}</li>
                  @endforeach
                  @endif
                  @if(!empty($room_aminities))
                  @foreach(explode(',', $room_aminities) as $roommnt)
                  <li>{{ trim($roommnt) }}</li>
                  @endforeach
                  @endif
                </ul>
              </div>
              <div class="tr-likes">
                <span class="tr-heart">
                  <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z"
                      fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                  </svg>
                </span>89%
              </div>
            </div>
          </div>
          @endforeach
          </div>
        </div>
        <!--More Places - END-->

        <!--City Wise Hotel and Price - START-->   
        <!--span class="locids d-none">{{$locationIds}}</span>
        <div class="tr-city-wise-hotels-section">
          <h3>Search for places to stay by destination</h3>
          <div class="tr-sub-title">Find Accommodation</div>
          <div class="getdata"></div>
        </div-->
        <!--City Wise Hotel and Price - END-->   
       
        <!--TOP TIPS FOR FINDING LONDON HOTEL DEALS - START-->
        <!--div class="col-sm-12">
          <div class="tr-tips-finding-hotel">
            <h3>Here are some fun hotel trivia facts for you</h3>
            <ul>
              <li>World’s Oldest Hotel: The Nishiyama Onsen Keiunkan in Japan is the world’s oldest hotel, dating back to 705 AD. It’s been operated by the same family for over 50 generations!</li>
              <li>Underwater Rooms: The Manta Resort in Zanzibar offers an underwater room where guests can sleep surrounded by ocean life, 4 meters below the surface.</li>
              <li>Ice Hotels: Sweden’s ICEHOTEL, built every winter from ice and snow, melts away each spring. It’s completely rebuilt with new designs every year.</li>
              <li>The Most Expensive Suite: The Royal Penthouse Suite at the Hotel President Wilson in Geneva, Switzerland, costs around $80,000 per night. It includes 12 bedrooms, panoramic views of Lake Geneva, and a private staff.</li>
              <li>Larger Than Life: The First World Hotel in Malaysia holds the Guinness World Record for the largest hotel, with over 7,000 rooms!</li>
              <li>Floating Hotels: There are several floating hotels around the world, such as the Sunborn Yacht Hotel in London, which offers luxury accommodation aboard a superyacht.</li>
              <li>Treehouse Hotels: You can stay in treehouses in unique places like the Treehotel in Sweden, where rooms are suspended in the treetops with views of the forest and sky.</li>
              <li>Hogwarts-Like Hotel: The Georgian House Hotel in London offers Harry Potter-themed rooms, allowing guests to feel like they’re staying at Hogwarts.</li>
              <li>Room with a View: At the Burj Al Arab in Dubai, a sail-shaped luxury hotel, guests can take in views of the Arabian Gulf from their room – some of which even come with their own butlers!</li>
              <li>Themed Rooms: In Tokyo, Japan, the Hotel Gracery Shinjuku has a Godzilla-themed room, complete with a giant Godzilla statue peering into your window!</li>
            </ul>
          </div>
        </div-->
        <!--TOP TIPS FOR FINDING LONDON HOTEL DEALS - END-->

        <!--BREADCRUMB - START-->
        <div class="tr-breadcrumb-section">
          <ul class="tr-breadcrumb">
            <li><a href="{{route('homepage')}}">Travell</a></li>             
            <li>Stays</li>
          </ul>
        </div>
        <!--BREADCRUMB - END-->

        <!--FAQS - START-->
        <!--
        <div class="tr-faqs-section">
          <h3 class="d-none d-md-block">FAQ’s</h3>
          <h3 class="d-block d-sm-block d-md-none">FAQ’s</h3>
          <div class="tr-faqs-ques-ans">
            <div class="tr-faqs-ques">Which facilities are available in the hotel?</div>
            <div class="tr-faqs-ans">Let’s embody your beautiful ideas together, simplify the way you visualize your
              next big things.</div>
          </div>
          <div class="tr-faqs-ques-ans">
            <div class="tr-faqs-ques">Is there a parking facility at the Arlington House Apartments?</div>
            <div class="tr-faqs-ans">Yes, the 5 star property, Arlington House Apartments provides parking facilities.
            </div>
          </div>
          <div class="tr-faqs-ques-ans">
            <div class="tr-faqs-ques">Can more than two adults stay in one room?</div>
            <div class="tr-faqs-ans">Yes, two adults can stay in single room.</div>
          </div>
          <div class="tr-faqs-ques-ans">
            <div class="tr-faqs-ques">Try using our Will my credit card be charged when I book my reservation?</div>
            <div class="tr-faqs-ans">Let’s embody your beautiful ideas together.</div>
          </div>
          <div class="tr-faqs-ques-ans">
            <div class="tr-faqs-ques">How will I get my money back after cancelling a hotel booking?</div>
            <div class="tr-faqs-ans">You will receive an automatic refund within 24 hrs.</div>
          </div>
        </div>
        -->
        <!--FAQS - END-->
      </div>
    </div>
      
  </div>
  
   </div>

  <!--FOOTER-->
  @include('frontend.footer') 
  <div class="overlay" id="overLay"></div>
</body>

</html>
<script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "BreadcrumbList",
      "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "Home",
        "item": "{{route('homepage')}}"
      },{
        "@type": "ListItem",
        "position": 2,
        "name": "Stays"
      }]
    }
</script>
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "Stays",
  "description": "Stays",
  "itemListOrder": "https://schema.org/ItemListOrderAscending",
  "itemListElement": [
    @if(!empty($searchresults))
      <?php $z = 1; $totalItems = count($searchresults);  ?>
      @foreach($searchresults as $val)
	     <?php $li =1; ?>
        {
          "@type": "ListItem",
          "position": {{$z}},
          "name": "{{$val->name}}",
          "url": "{{ url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$searchresult->slug) )) ) }}",       
                "image": "https://photo.hotellook.com/image_v2/crop/h{{ $val->hotelid }}_1/580/443.jpg"				
        }

        <?php if ($z < $totalItems): ?>
          ,
        <?php endif; ?>

        <?php $z++; ?>
      @endforeach
    @endif
  ]
}
</script>
<script src="{{asset('frontend/hotel-detail/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js')}} "></script>

<script src="{{ asset('/js/custom.js')}}"></script>
<script src="{{ asset('/js/stays.js')}}"></script>