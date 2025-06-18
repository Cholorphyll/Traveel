@if(!$nearby_sight->isEmpty())

@foreach($nearby_sight as $value)

<div class="tr-tourist-place">
  <div class="tr-img">
    <a href="javascript:void(0);"><img loading="lazy" src="{{ asset('/frontend/hotel-detail/images/tourist-places-1.png')}}" alt="tourist-places"></a>
  </div>
  <div class="tr-details">
    <div class="tr-place-name"><a
        href="{{ asset('at-'.$value->LocationId.'-'.$value->SightId.'-'.strtolower($value->slug)) }}">{{$value->name}}</a>
    </div>
    <div class="tr-place-type">{{$value->category}}</div>
    <div class="tr-like-review">
      <span class="tr-likes">@if($value->TAAggregateRating != "" &&
                    $value->TAAggregateRating !=
                    0)<?php $result = rtrim($value->TAAggregateRating, '.0') * 20;  ?>
                    {{$result}}% @else
                    --
                    @endif(static)</span>
      <!-- <span class="tr-review">. 22 reviews</span> -->
    </div>
    <div class="tr-distance">{{ round($value->distance,2) }} Km</div>
  </div>
</div>
@endforeach
@else
<div class="tr-tourist-place">
  <p>Nearby attraction not found.</p>
</div>
@endif