<div class="tr-title open">
  <h4>
    <svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path
        d="M17.3335 5.05859C17.3335 6.20565 16.2305 7.30859 15.0835 7.30859C16.2305 7.30859 17.3335 8.41154 17.3335 9.55859C17.3335 8.41154 18.4364 7.30859 19.5835 7.30859C18.4364 7.30859 17.3335 6.20565 17.3335 5.05859Z"
        stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
      <path
        d="M17.3335 15.0586C17.3335 16.2056 16.2305 17.3086 15.0835 17.3086C16.2305 17.3086 17.3335 18.4115 17.3335 19.5586C17.3335 18.4115 18.4364 17.3086 19.5835 17.3086C18.4364 17.3086 17.3335 16.2056 17.3335 15.0586Z"
        stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
      <path
        d="M9.3335 8.05859C9.3335 10.2252 7.25015 12.3086 5.0835 12.3086C7.25015 12.3086 9.3335 14.3919 9.3335 16.5586C9.3335 14.3919 11.4168 12.3086 13.5835 12.3086C11.4168 12.3086 9.3335 10.2252 9.3335 8.05859Z"
        stroke="#131313" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    Top attractions
  </h4>
  <span class="d-none d-md-block">Sorted by nearest</span>
</div>

<div class="tr-tourist-place-lists">
  @if(!$nearbyatt->isEmpty())
  @foreach($nearbyatt as $nearbyatt)
  <div class="tr-tourist-place">
    <div class="tr-img">
      <a href="javascript:void(0);"><img loading="lazy"
          src="{{asset('frontend/hotel-detail/images/tourist-places-1.png')}}" alt="tourist-places"></a>
    </div>
    <div class="tr-details">
      <div class="tr-place-name"><a
          href="{{route('sight.details',[$nearbyatt->LocationId.'-'.$nearbyatt->SightId.'-'.$nearbyatt->Slug])}}">{{$nearbyatt->Title}}</a>
      </div>
      <div class="tr-place-type">{{ $nearbyatt->ctitle}}</div>
      <div class="tr-like-review">
        @if($nearbyatt->TAAggregateRating != "" && $nearbyatt->TAAggregateRating != 0)
        <?php $result = rtrim($nearbyatt->TAAggregateRating, '.0') * 20;  ?>

        <span class="tr-likes">{{$result}}%</span>
        @endif
        <span class="tr-review">. 22 reviews</span>
      </div>
      <div class="tr-distance">{{$nearbyatt->distance}} km</div>
    </div>
  </div>
  @endforeach

  @for($i = $nearbyattCount; $i < 4; $i++) <!-- Placeholder elements when less than 4 items are available -->
    <div class="tr-tourist-place">
      <div class="tr-img">
        <img loading="lazy" src="{{ asset('frontend/hotel-detail/images/no-data-explore.png') }}" alt="no-data">
      </div>
      <div class="tr-details">
        <div class="tr-no-data-text w-100 mb-12"></div>
        <div class="tr-no-data-text w-60 mb-12"></div>
        <div class="tr-no-data-text w-80 mb-12"></div>
        <div class="tr-no-data-text w-20 mb-12"></div>
      </div>
    </div>
    @endfor
    @else
   
    <!-- Display 4 placeholders when there is no data -->
    @for($i = 0; $i < 4; $i++) <div class="tr-tourist-place">
      <div class="tr-img">
        <img loading="lazy" src="{{ asset('frontend/hotel-detail/images/no-data-explore.png') }}" alt="no-data">
      </div>
      <div class="tr-details">
        <div class="tr-no-data-text w-100 mb-12"></div>
        <div class="tr-no-data-text w-60 mb-12"></div>
        <div class="tr-no-data-text w-80 mb-12"></div>
        <div class="tr-no-data-text w-20 mb-12"></div>
      </div>
</div>
@endfor
@endif
</div>