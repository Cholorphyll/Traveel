   @if(!$nearby_sight->isEmpty())
   @foreach($nearby_sight as $nearby_sight)
   <div class="col-sm-6 d-flex align-items-center my-4">
     <img src="{{ asset('/images/image1.png')}}" width="72px" height="72px" alt="">
     <div class="mx-3 small text-center">
       <p class="mb-0 small  text-111"><a href="{{route('sight.details',[$nearby_sight->LocationId.'-'.$nearby_sight->SightId.'-'.$nearby_sight->Slug])}}">{{$nearby_sight->Title}}
         </a></p>
       <p class="mb-0">----------------------------⇢</p>
       <p class="mb-0 small text-111">{{ round($nearby_sight->distance,2) }} Km</p>
     </div>
     <i class="fa fa-map-marker-alt fa-2x"></i>
   </div>
   @endforeach
   @else
   <p>Nearby Attraction not found.</p>
   @endif