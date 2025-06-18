@if(!$get_experience->isEmpty())
@foreach($get_experience as $get_experiences)
<div class="tr-top-ways-list">
  <div class="tr-img">
    <a
      href="{{route('experince',[$get_experiences->slugid.'-'.$get_experiences->ExperienceId.'-'.$get_experiences->Slug])}}"><img
        loading="lazy"
        src='  @if($get_experiences->Img1 !=""){{$get_experiences->Img1}}  @else {{asset("images/Hotel lobby.svg")}} @endif'
        alt="top-places"></a>
  </div>
  <div class="tr-details">
    <div class="tr-place-type">Classes</div>
    <div class="tr-place-name"><a
        href='@if($get_experiences->viator_url != ""){{$get_experiences->viator_url}}  @endif'>{{$get_experiences->Name}}</a>
    </div>
    <div class="tr-like-review">
      <span class="tr-likes"><svg width="14" height="13" viewBox="0 0 14 13" fill="none"
          xmlns="http://www.w3.org/2000/svg">
          <path fill-rule="evenodd" clip-rule="evenodd"
            d="M6.99847 2.82194C5.79052 1.46927 3.7762 1.10541 2.26273 2.34405C0.749264 3.58269 0.536189 5.65364 1.72472 7.11858L6.99847 12.0026L12.2722 7.11858C13.4607 5.65364 13.2737 3.56966 11.7342 2.34405C10.1947 1.11844 8.20641 1.46927 6.99847 2.82194Z"
            stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>{{ $get_experiences->TAAggregationRating }}%</span>
      <span class="tr-review">({{ $get_experiences->review_count }} reviews)</span>
    </div>
    <div class="tr-price-section">{{ $get_experiences->Cost }}</div>
    <!-- <div class="tr-price-section"><span> {{ $get_experiences->Cost }}</span> /adult</div> -->
  </div>
</div>

@endforeach
@else
<p>Experience not found.</p>
@endif