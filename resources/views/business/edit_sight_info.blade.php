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
  <title>Business - Sight</title>
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
  <style>
  .recent-his {
    margin-top: 53px;
  }
  </style>
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
     <h5 class="mt-5 "><a href="{{route('view_bussines',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}"> <span class="l-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg></span><span class="back">back</span> </a></h5>

      <!-- start business -->
      <div class="col-md-12  bus-info">
        <hr>
        <h3>Business Info</h3>
        <div class="row">
          <div class="col-md-6">
            <div class="info-box general-information">
              <h5 class="mb-3">General information <span style="margin-left: 267px;font-size: medium;">
                  @if($getbusiness[0]->varify_business != 0)
                  <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">edit</a>
                  @endif
                </span>
              </h5>
              <hr class="mb-3" style="margin: auto;">
              <h5>Business name</h5>
              <p>{{$getbusiness[0]->bname}}</p>
              <h5>Business description</h5>
              <p>@if($getbusiness[0]->About !=""){{$getbusiness[0]->About}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+add description</a>
                @endif</p>
              <h5>Phone</h5>
              <p>@if($getbusiness[0]->Phone !=""){{$getbusiness[0]->Phone}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+add phone</a>
                @endif</p>
              <h5>Must See</h5>
              <p>@if($getbusiness[0]->IsMustSee !="") @if($getbusiness[0]->IsMustSee == 1) Yes @else No @endif @else <a
                  class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">+Must See</a>
                @endif</p>
              <h5>Category</h5>
              <p>@if($getbusiness[0]->cname !="") {{$getbusiness[0]->cname}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+Category</a>
                @endif</p>
            </div>

          </div>
          <div class="col-md-6">
            <div class="info-box">
              <h5 class="mb-3">Location
            
                @if($getbusiness[0]->varify_business != 0)
                <span style="margin-left: 354px;
    font-size: medium;    text-decoration: none;"> <a href="{{route('edit_sight_location',[$getbusiness[0]->bid])}}">edit</a></span>
                @else
                <span style="margin-left: 354px;
    font-size: medium; text-decoration: none;"> <a href="{{route('choose_uploadid',[$getbusiness[0]->id,'sight'])}}">edit</a></span>
                @endif
              </h5>
              <hr class="mb-3" style="margin: auto;">
              <h5>Address</h5>
              <p>@if($getbusiness[0]->Address !=""){{$getbusiness[0]->Address}} @else <a
                  href="{{route('edit_brestaurant_location',[$getbusiness[0]->bid])}}" class="nav-link">+add address</a>
                @endif</p>
              <h5>Postcode</h5>
              <p>@if($getbusiness[0]->Pincode !=""){{$getbusiness[0]->Pincode}} @else <a
                  href="{{route('edit_brestaurant_location',[$getbusiness[0]->bid])}}" class="nav-link">+add pincode</a>
                @endif</p>

              <?php   
                $latitude ="";
                $longitude="" ;

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
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 ">

          </div>
        </div>

        <!-- end business -->
        <!-- start photos and videos -->

        <div class=" mt-5 photos d-none">
          <div class="row">
            <h4>Restaurant Photos</h4>

            <hr>




            <!-- start custom image  -->
            <!-- <h6 class="mt-3">Custom Images </h6> -->


            <!-- end custom image  -->


          </div>
        </div>
        <!-- end photos and videos -->
        <!-- start Reviews  -->


        <!-- end Reviews-->


      </div>


    </div>
    @include('footer')
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="exampleModalss" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog " style="margin-top: 118;margin-left: 644px;">
        <div class="modal-content" style="width: 843px;">
          <div class="modal-header">
            <h6 class="modal-title" id="exampleModalLabel">General information</h6>

            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" id="edit_sight_info">
            @csrf
            <div class="modal-body">
              <div class="form-group mt-3 mb-3">
                <label for="exampleInputLocation">Business name:</label>
                <input type="text" class="form-control" name="bname" value="{{$getbusiness[0]->bname}}">
                <div class="validation-msg" id="location-msg"></div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <!-- Add a row div to contain the columns -->
                  <div class="col-md-12">


                    <input type="hidden" class="form-control " value="{{$getbusiness[0]->bus_id}}" name="bus_id">
                    <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}"
                      name="business_id">

                    <input type="hidden" class="userid" value="" name="userid">

                    <div class="validation-msg" id="fname-msg"></div>
                  </div>
                  <div class="col-md-6">

                  </div>
                </div>


                <div class="form-group mt-3">
                  <label for="exampleInputLocation">Business description</label>
                  <textarea name="about" style="height: 215px;">{{$getbusiness[0]->About}}</textarea>

                  <div class="validation-msg" id="email-msg"></div>
                </div>
              </div>
              <div class="form-group mt-3">
                <label for="exampleInputLocation">Phone</label>

                <input type="number" placeholder="phone" name="phone" class="form-control" value="{{$getbusiness[0]->Phone}}">

                <div class="validation-msg" id="email-msg"></div>
              </div>
              <div class="form-group mt-3">
                <label for="exampleInputLocation">Must see</label>

                <select name="mustsee" id="" class="form-control">
                  <option value="0" @if($getbusiness[0]->IsMustSee == 0) selected @endif>NO</option>
                  <option value="1" @if($getbusiness[0]->IsMustSee == 1) selected @endif>Yes</option>
                </select>

                <div class="validation-msg" id="email-msg"></div>
              </div>

              <div class="form-group mt-3">
                <label for="exampleInputLocation">Category</label>

                <select name="categoryid" id="" class="form-control">
                  @foreach($category as $catval)
                  <option value="{{$catval->CategoryId}}" @if($catval->CategoryId == $getbusiness[0]->CategoryId)
                    selected @endif>{{$catval->Title}}</option>
                  @endforeach

                </select>

                <div class="validation-msg" id="email-msg"></div>
              </div>
              <div class="modal-footer">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-6">
                      <button type="submit" class="btn btn-primary update-button"
                        style=" height: 38.118px;">Save</button>
                    </div>
                    <div class="col-md-6">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                        style=" height: 38.118px;">Close</button>
                    </div>
                  </div>
                </div>
              </div>

          </form>

        </div>
      </div>
    </div>

    <!-- Modal -->
  </div>



  <!-- Modal3 -->
  <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog " style="margin-top: 118;margin-left: 644px;">
      <div class="modal-content" style="width: 843px;">
        <div class="modal-header">
          <h6 class="modal-title" id="exampleModalLabel">Contact</h6>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="" method="post" id="update-restaurant-contact">
          @csrf
          <div class="modal-body">
            <div class="mt-3 mb-3 col-md-6">
              <label>Phone</label>
              <input type="text" class="form-control fname" value="{{$getbusiness[0]->Phone}}" name="Phone">

            </div>


            <div class="col-md-6">
              <label>Email</label>
              <input type="email" class="form-control fname" value="{{$getbusiness[0]->Email}}" name="Email">
              <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}" name="business_id">
              <input type="hidden" class="form-control " value="{{$getbusiness[0]->bus_id}}" name="bus_id">
            </div>

            <div class="mt-3 col-md-6">
              <label>Website</label>
              <input type="text" class="form-control fname" value="{{$getbusiness[0]->Website}}" name="Website">
            </div>
          </div>
          <div class="modal-footer">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-3">
                  <button type="submit" class="btn btn-primary update-button2" style=" height: 38.118px;">Save</button>
                </div>
                <div class="col-md-3">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                    style=" height: 38.118px;">Close</button>
                </div>

              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal -->


  <!-- end model 2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>


  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>



  <script>
  $(document).ready(function() {
    var parent = $(".parent");
    parent.mouseenter(function() {
      $(".b-list").show();
    });
    parent.mouseleave(function() {
      $(".b-list").hide();
    });
  });
  </script>

  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}">
  </script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>
  <script src="{{ asset('/js/business.js')}}"></script>
  <!-- end nav js -->


  <!-- screen shot js-->

  <script src="{{ asset('/js/map_leaflet.js')}}"></script>




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

  </script>


  <!-- end screen shot js -->


</body>

</html>