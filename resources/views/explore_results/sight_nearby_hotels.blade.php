@if(!$nearby_hotel->isEmpty())
@foreach($nearby_hotel as $nearby_hotels)
<div class="tr-nearby-hotel-list">
  <div class="tr-hotel-img">
    <a href="javascript:void(0):">
      <img loading="lazy" src="https://photo.hotellook.com/image_v2/limit/h{{$nearby_hotels->hotel_id}}_1/376/291.jpg"
        alt="NearBy Hotel" height="290" width="291">
    </a>
  </div>
  <div class="tr-hotel-deatils">
    <div class="tr-hotel-name"><a
        href="{{route('hotel.detail',[$nearby_hotels->location_id.'-'.$nearby_hotels->hotelid.'-'.$nearby_hotels->slug])}}">{{$nearby_hotels->name}}</a>
    </div>
    <div class="tr-rating d-block d-sm-block d-md-none">
      <span><i class="fa fa-star" aria-hidden="true"></i></span> 5.0
    </div>
    <div class="tr-like-review">
      <?php  $ratingtext ="";
                             $result ="";
                             $color =""; ?>
      <!-- rating -->
      @if($nearby_hotels->stars !="" && $nearby_hotels->stars !=0)
      <?php 
                  $rating = (float)$nearby_hotels->stars;       
                  $result = round($rating * 20); 
                  if ($result > 95) {
                      $ratingtext = 'Superb';
                      $color = 'green';
                  } elseif ($result >= 91 && $result <= 95) {
                      $ratingtext = 'Excellent';
                      $color = 'green';
                  } elseif ($result >= 81 && $result <= 90) {
                      $ratingtext = 'Great';
                      $color = 'green';
                  } elseif ($result >= 71 && $result <= 80) {
                      $ratingtext = 'Good';
                      $color = '#FFE135';
                  } elseif ($result >= 61 && $result <= 70) {
                      $ratingtext = 'Okay';
                      $color = '#FFE135';
                  } elseif ($result >= 51 && $result <= 60) {
                      $ratingtext = 'Average';
                      $color = '#FFE135';
                  } elseif ($result >= 41 && $result <= 50) {
                      $ratingtext = 'Poor';
                      $color = 'red';
                  } elseif ($result >= 21 && $result <= 40) {
                      $ratingtext = 'Disappointing';
                      $color = 'red';
                  } else {
                      $ratingtext = 'Bad';
                      $color = 'red';
                  }

                 
                ?>
      <span class="tr-likes"><svg width="21" height="21" viewBox="0 0 21 21" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M10.6655 6.37175C9.45752 5.01908 7.44319 4.65521 5.92972 5.89385C4.41626 7.13249 4.20318 9.20344 5.39172 10.6684L10.6655 15.5524L15.9392 10.6684C17.1277 9.20344 16.9407 7.11946 15.4012 5.89385C13.8617 4.66824 11.8734 5.01908 10.6655 6.37175Z"
            stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>{{$result}}%</span>
      <span class="tr-total-rating">4.2</span>
      <span class="tr-vgood" style="color:{{$color}}">{{$ratingtext}}</span>
      @endif
    </div>
    @if($nearby_hotels->pricefrom !="")
    <div class="tr-price-section"><span>${{$nearby_hotels->pricefrom}}</span> /night</div>
    @endif
  </div>
</div>
@if($nearby_hotel->count() < 4) @for($i=0; $i < 4 - $nearby_hotel->count(); $i++)
  <div class="tr-nearby-hotel-list">
    <div class="tr-hotel-img">
      <img loading="lazy" src="{{asset('frontend/hotel-detail/images/no-data-nearby-hotel.png')}}"
        alt="NearBy Hotel">
    </div>
    <div class="tr-hotel-deatils">
      <div class="tr-no-data-text w-100 mb-12"></div>
      <div class="tr-no-data-text w-70 mb-12"></div>
      <div class="tr-no-data-text w-25 mb-12"></div>
    </div>
  </div>
  @endfor
  @endif
  @endforeach
  @else
  <!-- Display 4 default hotel cards if no data is available -->
  @for($i = 0; $i < 4; $i++) <div class="tr-nearby-hotel-list">
    <div class="tr-hotel-img">
      <img loading="lazy" src="{{asset('frontend/hotel-detail/images/no-data-nearby-hotel.png')}}"
        alt="NearBy Hotel">
    </div>
    <div class="tr-hotel-deatils">
      <div class="tr-no-data-text w-100 mb-12"></div>
      <div class="tr-no-data-text w-70 mb-12"></div>
      <div class="tr-no-data-text w-25 mb-12"></div>
    </div>
    </div>
    @endfor
    @endif