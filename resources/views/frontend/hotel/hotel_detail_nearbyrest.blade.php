<div class="tr-title">
  <h4><svg width="24" height="20" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path
        d="M1.63492 1.90625L15.6158 15.8871C15.8811 16.1524 16.0301 16.5122 16.0301 16.8874C16.0301 17.2626 15.8811 17.6224 15.6158 17.8877C15.3504 18.1529 14.9906 18.3018 14.6155 18.3018C14.2404 18.3018 13.8806 18.1529 13.6152 17.8877L10.2 14.4136C9.97647 14.1867 9.85109 13.881 9.85086 13.5625V13.3526C9.85089 13.1918 9.81897 13.0326 9.75695 12.8842C9.69494 12.7358 9.60406 12.6012 9.4896 12.4882L9.04865 12.081C8.89896 11.9429 8.71694 11.8447 8.51933 11.7953C8.32172 11.746 8.11487 11.7471 7.91783 11.7987C7.6071 11.8798 7.28058 11.8782 6.97065 11.7942C6.66071 11.7101 6.37816 11.5464 6.15101 11.3194L2.90918 8.07722C0.986021 6.15405 0.278307 3.24996 1.63492 1.90625Z"
        stroke="#131313" stroke-linejoin="round"></path>
      <path
        d="M14.6312 1.30859L11.6998 4.24001C11.4742 4.46554 11.2953 4.73329 11.1732 5.02798C11.0511 5.32267 10.9883 5.63852 10.9883 5.9575V6.52139C10.9883 6.60118 10.9726 6.68018 10.9421 6.75389C10.9115 6.82759 10.8668 6.89456 10.8103 6.95096L10.3811 7.38014M11.5954 8.59445L12.0246 8.16526C12.081 8.10883 12.148 8.06407 12.2217 8.03353C12.2954 8.00299 12.3744 7.98728 12.4542 7.98729H13.0181C13.3371 7.98729 13.6529 7.92446 13.9476 7.80238C14.2423 7.6803 14.51 7.50136 14.7356 7.27578L17.667 4.34437M16.1491 2.82648L13.1133 5.86225M7.04179 14.0588L3.2577 17.8642C2.97305 18.1487 2.58704 18.3086 2.18455 18.3086C1.78207 18.3086 1.39605 18.1487 1.11141 17.8642C0.826849 17.5795 0.666992 17.1935 0.666992 16.791C0.666992 16.3885 0.826849 16.0025 1.11141 15.7179L4.30959 12.541"
        stroke="#131313" stroke-linecap="round" stroke-linejoin="round"></path>
    </svg>Restaurants</h4>
  <span class="d-none d-md-block">@if(!$nearby_rest->isEmpty()) within {{ $nearby_rest[0]->radius}} km @endif</span>
</div>
<div class="tr-tourist-place-lists">
@if(!$nearby_rest->isEmpty())
@foreach($nearby_rest as $value)
  <div class="tr-tourist-place">
    <div class="tr-img">
      <a href="javascript:void(0);"><img loading="lazy" src="{{ asset('/frontend/hotel-detail/images/tourist-places-4.png')}}" alt="tourist-places"></a>
    </div>
    <div class="tr-details">
      <div class="tr-place-name"><a
          href="{{ asset('rd-'.$value->slugid.'-'.$value->RestaurantId.'-'.strtolower($value->Slug)) }}">{{$value->Title}}</a>
      </div>
      <div class="tr-place-type">Shopping area</div>
      <div class="tr-like-review">
        <span class="tr-likes">{{$value->TATrendingScore}}% </span>
        <span class="tr-review">.{{$value->review_count}} reviews</span>
      </div>
      <div class="tr-distance">{{ round($value->distance,2) }} km</div>
    </div>
  </div>
  @endforeach
  @else
  <p>Nearby restaurant not found.</p>
  @endif

</div>