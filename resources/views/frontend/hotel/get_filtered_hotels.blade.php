<style>
.clickable-hotel-card {
    cursor: pointer;
}
.tr-hotel-deatils {
    position: relative;
}

.hotel-details-link {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

/* Make sure interactive elements work properly */
.tr-anchor-btn {
    position: relative;
    z-index: 2;
}

/* Ensure carousel controls work properly */
.carousel-control-prev,
.carousel-control-next {
    z-index: 2;
}
</style>
<?php
// Proper deduplication logic at the start
$uniqueHotels = [];
$displayResults = [];
$perPage = 30; // Set your desired items per page

foreach ($searchresults as $result) {
    if (!in_array($result->hotelid, $uniqueHotels)) {
        $uniqueHotels[] = $result->hotelid;
        $displayResults[] = $result;
    }
    
    // Stop when we reach the per-page limit
    if (count($displayResults) >= $perPage) {
        break;
    }
}

$displayCount = count($displayResults);
$totalCount = $searchresults->total(); // Total available results
?>
@if(!empty($displayResults))
<?php $a = 1;?>
<span class="d-none page_type">getlist</span>
@foreach($displayResults as $searchresult)
<?php
$hotel_url = url('hd-'.$searchresult->slugid.'-' .$searchresult->id .'-'.strtolower(str_replace(' ', '_', str_replace('#', '!',$searchresult->slug."?checkin={$checkinDate}&checkout={$checkoutDate}"))));
?>
<div class="tr-hotel-deatils clickable-hotel-card" data-id="{{ $searchresult->id }}" data-url="{{ $hotel_url }}">
  <div class="tr-hotal-image">
    <div id="roomSlider{{$a}}" class="carousel slide" data-bs-touch="false" data-bs-interval="false">
      <!-- Indicators/dots -->
      <div class="carousel-indicators">
        <button type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide-to="0" class="active">1</button>
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
      <button class="carousel-control-prev" type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide="prev"><span
          class="carousel-control-prev-icon"></span></button>
      <button class="carousel-control-next" type="button" data-bs-target="#roomSlider{{$a}}" data-bs-slide="next"><span
          class="carousel-control-next-icon"></span></button>
    </div>
    <button class="tr-anchor-btn tr-save">Save</button>
  </div>
<div class="tr-hotel-deatil">
    <a href="{{ $hotel_url }}" class="hotel-details-link"></a>
    <div class="tr-heading-with-rating">
      <h2>
  <a href="{{ $hotel_url }}" class="hotel-name-link" onclick="event.stopPropagation();">
    <span>{{ $searchresult->name }}</span>
  </a>
</h2>

      <div class="tr-rating">
        @for ($i = 0; $i < 5; $i++)
          @if($i < $searchresult->stars )
          <span class="tr-star">
            <img src="{{asset('frontend/hotel-detail/images/icons/star-fill-icon.svg')}}">
          </span>
          @endif
        @endfor
      </div>
    </div>
@if($searchresult->CityName != "")
<div class="tr-hotel-location">
    <?php
    // Construct the city listing link by using specific variables
    $city_slug = strtolower(str_replace(' ', '-', $searchresult->CityName));
    $country_slug = strtolower(str_replace(' ', '-', $searchresult->CountryName));
    $location_id = $searchresult->slugid; // Assuming you have the location ID in the $searchresult object
    $city_url = url("ho-{$location_id}-{$city_slug}-{$country_slug}") . "?checkin={$checkinDate}&checkout={$checkoutDate}&locationid={$location_id}&rooms=1&guest=2&location=" . urlencode($searchresult->CityName);
    ?>
    <a href="{{ $city_url }}" title="{{ $searchresult->CityName }}" style="color: black; text-decoration: none;">
        {{ $searchresult->CityName }}
    </a>
</div>
@endif





    <div class="tr-like-review">
   @if($searchresult->rating !="")
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
            'A/C' => 'frontend/hotel-detail/images/amenities/heating.svg',
            'Parking' => 'frontend/hotel-detail/images/amenities/Parking.svg',
            'Wi-Fi' => 'frontend/hotel-detail/images/amenities/Wi-Fi.svg',
            'Laundry' => 'frontend/hotel-detail/images/amenities/Laundry.svg',
            'Smoke-free' => 'frontend/hotel-detail/images/amenities/Smoke-free.svg',
            'Pool' => 'frontend/hotel-detail/images/amenities/Pool.svg',
            'Gym' => 'frontend/hotel-detail/images/amenities/Gym.svg',
            'Food' => 'frontend/hotel-detail/images/amenities/Food.svg',
            'Bar' => 'frontend/hotel-detail/images/amenities/Bar.svg',
            'Spa' => 'frontend/hotel-detail/images/amenities/Spa.svg',
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
                        $iconPath = $amenityIconPaths[$mnt['name']] ?? 'frontend/hotel-detail/images/amenities/wifi.svg';
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

  <div class="tr-hotel-price-section">
    <!--
    <div class="tr-deal tr-offer-alert">
      <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
          <path d="M13.7263 8.9387L8.94634 13.7187C8.82251 13.8427 8.67546 13.941 8.5136 14.0081C8.35173 14.0752 8.17823 14.1097 8.00301 14.1097C7.82779 14.1097 7.65429 14.0752 7.49242 14.0081C7.33056 13.941 7.18351 13.8427 7.05967 13.7187L1.33301 7.9987V1.33203H7.99967L13.7263 7.0587C13.9747 7.30851 14.1141 7.64645 14.1141 7.9987C14.1141 8.35095 13.9747 8.68888 13.7263 8.9387Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/>
          <path d="M4.66699 4.66797H4.67366" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
      #1 Best value of 400 places to stay
    </div>
    -->
    <div class="tr-hotel-price-lists">
      @if(!$hotels->isEmpty())
        <?php
          $allPrices = [];
          foreach ($hotels as $hotel_result) {
            if ($hotel_result->hotelid == $searchresult->hotelid) {
              $price = $hotel_result->price;
              $agencyId = $hotel_result->agency_id;
              $fullurl = $hotel_result->booking_link;
              $options = explode(',', $hotel_result->amenity);

              $key = $hotel_result->hotelid . '_' . $price . '_' . $agencyId;

              if (!isset($allPrices[$key])) {
                $allPrices[$key] = [
                  'price' => $price,
                  'fullBookingURL' => $fullurl,
                  'agencyId' => $agencyId,
                  'options' => $options,
                ];
              }
            }
          }

          $allPricesArray = array_values($allPrices);
          usort($allPricesArray, function($a, $b) {
              return $a['price'] - $b['price'];
          });

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
                    if (in_array($priority, $data['options'])) {
                      echo "<li>" . ucfirst($priority) . " included</li>";
                      $count++;
                    }
                  }
                  foreach ($data['options'] as $key) {
                    if (!in_array($key, $priorities)) {
                      echo "<li>" . ucfirst(str_replace('_', ' ', $key)) . " included</li>";
                      $count++;
                    }
                    if ($count == 2) break;
                  }
                ?>
              </ul>
            </div>
            <div class="tr-site-details">
              <img loading="lazy" src="{{ 'https://pics.avs.io/hl_gates/100/40/' . $data['agencyId'] . '.png' }}"
                alt="agency logo">
            </div>
          </div>
          <div class="tr-row">
            <div class="tr-action" @if($count == 1 || $count == 0) style="margin-top: 18px;" @endif>
              <a href="{{ $data['fullBookingURL'] }}" class="tr-btn" target="_blank">View deal</a>
            </div>
            <div class="tr-hotel-price"><strong>${{ $data['price'] }}</strong></div>
          </div>
        </div>
        @endforeach

        <!-- Show remaining prices under "More Price" -->
        @if(count($remainingPrices) > 0)
        <div class="more-prices-container">
          <!-- Container for more prices -->
          <div class="tr-hotel-price-lists">
            @foreach ($remainingPrices as $data)
            <div class="tr-hotel-price-list" style="display: none;">
              <div class="tr-row">
                <div class="tr-hotel-facilities">
                  <ul>
                    <?php
                      $count = 0;
                      $priorities = ['breakfast', 'freeWifi'];
                      foreach ($priorities as $priority) {
                        if (in_array($priority, $data['options'])) {
                          echo "<li>" . ucfirst($priority) . " included</li>";
                          $count++;
                        }
                      }
                      foreach ($data['options'] as $key) {
                        if (!in_array($key, $priorities)) {
                          echo "<li>" . ucfirst(str_replace('_', ' ', $key)) . " included</li>";
                          $count++;
                        }
                        if ($count == 2) break;
                      }
                    ?>
                  </ul>
                </div>
                <div class="tr-site-details">
                  <img loading="lazy" src="{{ 'https://pics.avs.io/hl_gates/100/40/' . $data['agencyId'] . '.png' }}"
                    alt="agency logo">
                </div>
              </div>
              <div class="tr-row">
                <div class="tr-action" @if($count == 1 || $count == 0) style="margin-top: 18px;" @endif>
                  <a href="{{ $data['fullBookingURL'] }}" class="tr-btn" target="_blank">View deal</a>
                </div>
                <div class="tr-hotel-price"><strong>${{ $data['price'] }}</strong></div>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        <button class="tr-more-price tr tr-anchor-btn" onclick="this.textContent = this.textContent === 'View More Offers' ? 'View Less Offers' : 'View More Offers';">View More Offers</button>
        @endif

      @endif
    </div>
  </div>

  @if ($loop->last && $displayCount > 1)

  <div class="tr-login-for-more-options">
    <h2>Log in/Sign up to view all listings</h2>
    <p>Compare prices from 70+ Hotels websites all at one place</p>
    <div class="tr-row">
      <button type="button" class="tr-btn">Sign up</button>
    </div>
  </div>

  @endif

</div>
<?php $a++;?>
@endforeach
@else
<div class="tr-hotels-not-available">Hotels not available</div>
@endif

{{-- Pagination --}}
@if ($totalCount > $perPage)
    {{ $searchresults->links('hotellist_pagg.default') }}
@endif

{{-- Update count displays to use: --}}
{{ $displayCount }} of {{ $totalCount }} hotels


<!--  Map Code -->


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
          if (!empty($hotels->result)) {
              foreach ($hotels->result as $hotel_result) {
                  if ($hotel_result->id == $searchresult->hotelid) {
                      if (!empty($hotel_result->rooms) && isset($hotel_result->rooms[0])) {
                          $price = $hotel_result->rooms[0]->total;
                      }
                      break;
                  }
              }
          }
        ?>

        (function() {
          var price = {{ $price !== null ? $price : 'null' }};
          var imageUrl = "https://photo.hotellook.com/image_v2/crop/h{{ $searchresult->hotelid }}_0/520/460.jpg";
          var hotelName = "{{ $searchresult->name }}";
          var city = "{{ $searchresult->CityName }}";
          var rating = "{{ $searchresult->rating }}";
          var stars = parseInt("{{ $searchresult->stars }}");

          // Calculate rating percentage
          var ratingPercentage = (stars / 5) * 100; // Now each star equals 20%
          // Add visible stars beside hotel name
          var ratingStars = '';
          if (!isNaN(stars) && stars > 0) {
            for (var i = 0; i < stars; i++) {
              ratingStars += '<img src="{{ asset('js/images/Stars.svg')}}" alt="Star" style="width: 12px; margin-right: 2px;">';
            }
          }
          var agencyId = "{{ $data['agencyId'] }}";

          var popupContent = `
  <div style="display: flex; padding: 0; width: 250px; height: 130px; align-items: flex-start;">
    <img src="${imageUrl}" alt="${hotelName}" style="width: 100px; height: 100%; object-fit: cover; margin: 0;">
    <div style="flex: 1; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; padding-left: 10px; display: flex; flex-direction: column; justify-content: space-between;">
      <h3 style="margin: 0; font-size: 14px; font-weight: bold; line-height: 1.2;">${hotelName} ${ratingStars}</h3>
      <p style="margin: 8px 0 0; font-size: 14px; color: #555; display: flex; align-items: center;">
        <img src="{{asset('js/images/map.svg')}}" alt="Marker" style="width: 16px; height: 16px; margin-right: 8px;">
        ${city}
      </p>
      <div style="display: flex; align-items: center; justify-content: center; margin: 10px 0;">
        <img src="{{asset('js/images/Score.svg')}}" alt="Heart" style="width: 20px; height: 20px; margin-right: 5px;">
        <span style="font-weight: bold; font-size: 14px;">${ratingPercentage.toFixed(0)}%</span>
        <span style="background-color: #e6f4f4; color: #4c8076; font-weight: bold; font-size: 12px; padding: 4px 8px; border: 2px solid white; border-radius: 4px; margin-left: 5px;">Very Good</span>
      </div>
        <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
        <img src="https://pics.avs.io/hl_gates/100/40/${agencyId}.png" alt="agency logo" style="width: 60px; height: 20px; margin-right: 5px;">
        <button style="background-color: white; color: #333; border: 1px solid grey; padding: 2px 5px; border-radius: 5px; font-weight: bold; display: flex; align-items: center; justify-content: center;">
          <span style="font-size: 16px;">$${price}</span>
          <img src="{{ asset('js/images/Arro.svg') }}" alt="Arrow" style="width: 20px; margin-left: 5px;">
        </button>
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

<script>
    function toggleContent(button) {
        const content = button.previousElementSibling;
        content.classList.toggle('show-more');
        button.textContent = content.classList.contains('show-more') ? 'Read Less' : 'Read More';
    }
</script>

<script>
  // Make hotel cards clickable
  document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing hotel card click handlers');
    const hotelCards = document.querySelectorAll('.clickable-hotel-card');
    console.log('Found ' + hotelCards.length + ' hotel cards');
    
    hotelCards.forEach(card => {
      const url = card.getAttribute('data-url');
      console.log('Card URL:', url);
      
      card.addEventListener('click', function(e) {
        console.log('Card clicked');
        // Prevent click if it was on a link inside the card
        if (e.target.closest('a')) {
          console.log('Click on internal link, not redirecting');
          return;
        }
        
        if (url) {
          console.log('Redirecting to:', url);
          window.location.href = url;
        } else {
          console.log('No URL found for this card');
        }
      });
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Function to detect mobile devices
    function isMobile() {
      return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    // Initialize all carousels
    document.querySelectorAll('.carousel').forEach(function(carousel) {
      if (isMobile()) {
        // Show controls immediately on mobile
        var carouselInstance = new bootstrap.Carousel(carousel, {
          touch: true
        });
        
        // Force show controls
        carousel.querySelectorAll('.carousel-control-prev, .carousel-control-next').forEach(function(control) {
          control.style.opacity = '1';
        });
      }
    });
  });
</script>

<style>
.tr-hotel-deatil {
    position: relative;
}

.tr-hotel-deatil a.hotel-details-link {
    display: block;
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1;
}

/* Make sure interactive elements work properly */
.tr-anchor-btn {
    position: relative;
    z-index: 2;
}

/* Ensure carousel controls work properly */
.carousel-control-prev,
.carousel-control-next {
    z-index: 2;
}
</style>
