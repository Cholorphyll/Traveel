<style>
  .custom-link {
    text-decoration: none; /* Remove underline */
    color: inherit; /* Inherit the color from the parent element */
  }
</style>

@if (!empty($searchresults))
    @foreach ($searchresults as $searchresult)
        <div class="d-flex text-dark align-items-center my-2">
            <div class="bg-e p-2 me-3 border b-10"><i class="fa fa-map-marker-alt"></i></div>
            <p class="mb-0"><a class="custom-link" href="{{ route('restaurant_listing',$searchresult['id'] ) }}">{{ $searchresult['value'] }}</a></p>
        </div>
    @endforeach
@else
    <p>Location not found</p>
@endif


