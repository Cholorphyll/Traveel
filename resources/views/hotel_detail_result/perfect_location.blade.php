  @if(!$near_sight->isEmpty())
          @foreach($near_sight as $value)
              <div class="col-4 d-flex ammenity mb-3"> <img src="{{ asset('images/beach.svg')}}" alt="">{{ round($value->distance,2) }}  Km min distance to {{$value->name}}</div>
           @endforeach
        @else
        Not available.
        @endif