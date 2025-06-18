<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Travel Urls!</title>
  </head>
  <body>
  <div class="container-sm" style="max-width: 950px;">
    <h6 class="m-3" style="float: right;"><a href="{{route('add_url')}}">Add url+</a></h6>
    <div class="col-md-6 mt-3">
      Search URL
      <input type="text" class="form-control " style="float: right;" id="searchdata">
    </div>
	  <div class="tr-spotlight">
    <div class="tr-heading">
        <svg width="24" height="7" viewBox="0 0 24 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M23.3868 3.5L20.5 0.613249L17.6132 3.5L20.5 6.38675L23.3868 3.5ZM0.5 4H20.5V3H0.5V4Z" fill="#09707A" />
        </svg>
        IN THE SPOTLIGHT
        <svg width="24" height="7" viewBox="0 0 24 7" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0.613249 3.5L3.5 0.613249L6.38675 3.5L3.5 6.38675L0.613249 3.5ZM23.5 4H3.5V3H23.5V4Z" fill="#09707A" />
        </svg>
    </div>

    @if ($spotlightHotels->isNotEmpty())
        @foreach ($spotlightHotels as $hotel)
            <div>
                <h3>{{ $hotel->name }}</h3> <!-- Display hotel name -->
                <div>{{ $hotel->Spotlights }}</div> <!-- Spotlight content from the Spotlights column -->
                <div>{{ $hotel->feature1 }}</div> <!-- Additional features if available -->
                <div>{{ $hotel->feature2 }}</div>
                <div>{{ $hotel->feature3 }}</div>
            </div>
        @endforeach
    @else
        <div>No spotlight information available at the moment.</div>
    @endif
</div>


  <table class="table getdata">
  <thead>
    <tr>
      <th scope="col">Url</th>
      <th scope="col">FAQ</th>
      <th scope="col">Descriptions</th>
      <th scope="col">Data Entry</th>
    </tr>
  </thead>
  <tbody>
  
  @if(!$getweb->isEmpty())   
      @foreach($getweb as $getweb)  
    <tr>
    
      <td>{{$getweb->url}}</td>
      <!-- <td><input type="checkbox" class="update-checkbox" data-url-id="{{ $getweb->id }}" data-column="faq" {{ $getweb->faq == 1 ? 'checked' : '' }}></td>
            <td><input type="checkbox" class="update-checkbox" data-url-id="{{ $getweb->id }}" data-column="description" {{ $getweb->description == 1 ? 'checked' : '' }}></td>
            <td><input type="checkbox" class="update-checkbox" data-url-id="{{ $getweb->id }}" data-column="dataEntry" {{ $getweb->dataEntry == 1 ? 'checked' : '' }}></td>  -->
            
            <td>
                <input type="checkbox" class="update-checkbox" data-url-id="{{ $getweb->id }}" data-column="faq" {{ $getweb->faq == 1 ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" class="update-checkbox" data-url-id="{{ $getweb->id }}" data-column="description" {{ $getweb->description == 1 ? 'checked' : '' }}>
            </td>
            <td>
                <input type="checkbox" class="update-checkbox" data-url-id="{{ $getweb->id }}" data-column="dataEntry" {{ $getweb->dataEntry == 1 ? 'checked' : '' }}>
            </td>
         
    </tr>
    @endforeach
    @else
    <td>Not available</td>
    @endif
 
  </tbody>
</table>
  </div>

 

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

 
  </body>
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
		<script>
$(document).on('keyup', '#searchdata', function () {
    var value = jQuery('#searchdata').val();
  alert(value);
  
})

</script>
  <script>
(function($) {
   
    $(document).ready(function() {
      
        // ('.update-checkbox').on('change', function() {
          $(document).on('change', '.update-checkbox', function() {
            var urlId = $(this).data('url-id');
            var column = $(this).data('column');
            var value = this.checked ? 1 : 0;

            // AJAX request
            $.ajax({
                url: '{{ route('url_udate') }}',
                method: 'POST',
                data: {
                    '_token': '{{ csrf_token() }}',
                    'url_id': urlId,
                    'column': column,
                    'value': value
                },
                success: function(response) {
                    
                    $('.getdata').html(response);
                },
                error: function(error) {
                    console.error('Update failed');
                }
            });
        });
    });
})(jQuery.noConflict());

</script>


</html>