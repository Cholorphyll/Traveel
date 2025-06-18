@if(!$nearby_hotel->isEmpty())
@foreach($nearby_hotel as $index =>$nearbhotel)

<div class="tr-nearby-hotel-list">
  <div class="tr-hotel-img">
    <a href="javascript:void(0):">
      @if($nearbhotel->hotel_id !="")
      <img loading="lazy" src="https://photo.hotellook.com/image_v2/limit/h{{$nearbhotel->hotel_id}}_1/290/291.jpg"
        alt="NearBy Hotel" width="290" height="291">
      @else
      <img loading="lazy" src="{{asset('images/Hotel lobby-image.png')}}" alt="NearBy Hotel" width="290" height="291">
      @endif
    </a>
  </div>
  <div class="tr-hotel-deatils">
    <div class="tr-hotel-name"><a 
      <div class="tr-hotel-name"><a 
      href="{{route('hotel.detail',[$nearbhotel->LocationId.'-'.$nearbhotel->hotelid.'-'.strtolower( str_replace(' ', '_', str_replace('#', '!', $nearbhotel->slug))) ])}}?checkin={{ $checkin ?? ($_GET['checkin'] ?? '') }}&checkout={{ $checkout ?? ($_GET['checkout'] ?? '') }}">{{$nearbhotel->name}}</a>
    </div>
    <!-- {{$nearbhotel->address}} -->
    <div class="tr-rating d-block d-sm-block d-md-none">
      <span><i class="fa fa-star" aria-hidden="true"></i></span>{{$nearbhotel->stars}}
    </div>
    <div class="tr-like-review">
      <span class="tr-likes"><svg width="21" height="21" viewBox="0 0 21 21" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M10.6655 6.37175C9.45752 5.01908 7.44319 4.65521 5.92972 5.89385C4.41626 7.13249 4.20318 9.20344 5.39172 10.6684L10.6655 15.5524L15.9392 10.6684C17.1277 9.20344 16.9407 7.11946 15.4012 5.89385C13.8617 4.66824 11.8734 5.01908 10.6655 6.37175Z"
            stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>94%</span>
      <span class="tr-total-rating">4.2</span>
      <span class="tr-vgood">Very Good</span>
    </div>
    @if($nearbhotel->pricefrom !="")
    <div class="tr-price-section"><span>${{$nearbhotel->pricefrom}}</span> /night</div>
    @endif
  </div>
</div>

@endforeach

@else
<p>Hotel not found.</p>
@endif