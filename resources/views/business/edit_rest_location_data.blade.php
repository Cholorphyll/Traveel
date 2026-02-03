<!doctype html>
<html lang="en">

<head>
  <!-- Google Tag Manager -->
  <script>
  (function(w, d, s, l, i) {
    w[l] = w[l] || [];
    w[l].push({
      'gtm.start': new Date().getTime(),
      event: 'gtm.js'
    });
    var f = d.getElementsByTagName(s)[0],
      j = d.createElement(s),
      dl = l != 'dataLayer' ? '&l=' + l : '';
    j.async = true;
    j.src =
      'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
    f.parentNode.insertBefore(j, f);
  })(window, document, 'script', 'dataLayer', 'GTM-PTHP3JH4');
  </script>
  <!-- End Google Tag Manager -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Business - Travell</title>
  <meta name="description"
    content="Travell is the way we seek out the happiness and beauty in our outside world, and find contentment in that experience." />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!--  fontawesome -->

  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>


  <link rel="icon" type="image/x-icon" href="{{asset('/favicon.ico')}}">
  <!-- nav css -->
  <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/business.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <link rel="stylesheet" href="{{ asset('/css/business_index.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
  <!-- end nav css -->

</head>

<body>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <!-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
       
    </div> -->




  <div class="">
    <div class="container">
		  <h5 class="mt-5 "><a href="{{route('view_rest_business',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}"> <span class="l-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg></span><span class="back">back</span></a>
      </h5>
      
      <!-- start business -->
      <div class="col-md-12 updatedata">
        
        <hr>
        <form action="{{route('update_rest_map_data')}}" method="post" id="">
          @csrf
        <h5>Location</h5>
        <div class="row">
          <div class="col-md-6 ">
            <div class="info-box general-information">
              <h5>Street Address</h5>
              <input type="text" name="address" class="form-control" value="{{$getbusiness[0]->Address}}"
                placeholder="address">

              <h5 class="mt-3">Postcode</h5>
              <input type="text" name="postcode" class="form-control" value="{{$getbusiness[0]->Pincode}}"
                placeholder="pincode">

                <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}" name="business_id">
                <input type="hidden" class="form-control" value="{{$getbusiness[0]->bus_id}}"  name="bus_id">
                <input type="hidden" class="form-control " value="{{$getbusiness[0]->bslug}}"  name="bus_slug">

              <div class="col-md-12 mt-5">
                <div class="row">
                  <h5>Geo</h5>
                  <hr>
                  <div class="col-md-6">
                    <span>longitude</span>
                    <input type="text" name="longitude" class="form-control" value="{{$getbusiness[0]->Longitude}}"
                      placeholder="longitude">
                  </div>
                  <div class="col-md-6">
                    <span>Latitude</span>
                    <input type="text" name="latitude" class="form-control" value="{{$getbusiness[0]->Latitude}}"
                      placeholder="latitude">
                  </div>


                </div>

              </div>

              <div class="col-md-6 mt-3">
              <a href="{{route('view_rest_business',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}" class="btn btn-outline-dark">cancel</a>
             
                  <button class="btn btn-outline-dark update-button2" >save</button>
                   
              </div>
            </div>
         
            </form>



          </div>
          <div class="col-md-6">
            <div class="info-box getcontact">

              <!-- start map -->
              <?php   
                $latitude ="";
                $longitude=""  ;

                $latitude = $getbusiness[0]->Latitude;
                $longitude = $getbusiness[0]->Longitude;

              ?>

              <div class="map border border-1 my-5">
                @if($getbusiness[0]->Latitude != "" && $getbusiness[0]->Longitude != "")
                <div id="map1" class="" style="width: 100%; height: 400px;"></div>

                <!-- <div id="screenshotContainer"></div> -->
                @endif

              </div>

            </div>

            <!-- end map -->

          </div>
        </div>
      </div>
<!-- end business data-->
    </div>

    <!-- end business -->

  </div>

  @include('footer')
  <!-- Button trigger modal -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>


  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>




  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}">
  </script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>
  <script src="{{ asset('/js/business.js')}}"></script>
  <!-- end nav js -->

  <!-- screen shot js-->

  <script src="{{ asset('/js/map_leaflet.js')}}"></script>


<!-- map screenshot code -->
<!-- <script src="https://unpkg.com/leaflet-simple-map-screenshoter"></script>
<script src="https://unpkg.com/file-saver/dist/FileSaver.js"></script> -->





<script>
var mapOptions = {
  center: [{{ $latitude }}, {{ $longitude }}],
  zoom: 10
};

var map = new L.map('map1', mapOptions);

var layer = new L.TileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png');
map.addLayer(layer);

var customIcon = L.icon({
  iconUrl: '{{ asset("images/map-marker-icon.png") }}',
  iconSize: [32, 32], // Adjust the size as needed
  iconAnchor: [16, 32], // Adjust the anchor point if needed
});

var marker = L.marker([{{ $latitude }}, {{ $longitude }}], {
  icon: customIcon
}).addTo(map);

// var simpleMapScreenshoter = L.simpleMapScreenshoter().addTo(map);

// function captureScreenshot() {
//   simpleMapScreenshoter.takeScreen('blob', {}).then(blob => {
//     const screenshotImage = new Image();
//     screenshotImage.src = URL.createObjectURL(blob);
//     var mapContainer = document.getElementById('map1');
//     mapContainer.classList.add('d-none');
//     const screenshotContainer = document.getElementById('screenshotContainer');
//     screenshotContainer.appendChild(screenshotImage);
//   }).catch(e => {
//     console.error(e.toString());
//   });
// }

// window.onload = captureScreenshot;
</script>


</body>

</html>