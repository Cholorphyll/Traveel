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
      <h5 class="mt-5"><a href="{{route('view_rest_business',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}">back</a>
      </h5>
      <!-- start business -->
      <div class="col-md-12 updatedata">

        <hr>
        <form action="{{route('update_rest_cuisine')}}" method="post" id="">
          @csrf
          <h5>Restaurant Cousine</h5>
          <div class="row">
            <div class="col-md-12 ">
              <div class="info-box2 general-information">
                <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}" name="business_id">
                <input type="hidden" class="form-control" value="{{$getbusiness[0]->bus_id}}" name="bus_id">
                <input type="hidden" class="form-control " value="{{$getbusiness[0]->bslug}}" name="bus_slug">

                <div class="col-md-12">
                  <div class="row">
                    <h5>Select Cuisine</h5>
                    <hr>
                    @if(!$allrest->isEmpty())
                        <div class="restaurant-list">
                            @foreach($allrest as $val)
                                @php
                                    $isChecked = $getcuisen->contains('RestaurantCuisineId', $val->RestaurantCuisineId);
                                @endphp
                                <div class="form-check-inline rl-margin">
                                    <input class="form-check-input" type="checkbox" name="cui_name[]" value="{{ $val->RestaurantCuisineId }}"
                                        id="flexCheckDefault{{ $val->RestaurantCuisineId }}" @if($isChecked) checked @endif>
                                    <label class="form-check-label" for="flexCheckDefault{{ $val->RestaurantCuisineId }}">
                                        {{ $val->Name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif
   
                    </div>
                    </div>



                 
                  <div class="row mt-5">
                 
                    <h5>Select Special Diet</h5>
                    <hr>
                    @if(!$allspecialdiet->isEmpty())
                        <div class="restaurant-list">
                            @foreach($allspecialdiet as $val)
                                @php
                                    $isChecked = $getSpecialDiet->contains('RestaurantSpecialDietId', $val->RestaurantSpecialDietId);
                                @endphp
                                <div class="form-check-inline rl-margin">
                                    <input class="form-check-input" type="checkbox" name="special_diet[]" value="{{ $val->RestaurantSpecialDietId }}"
                                        id="flexCheckDefault{{ $val->RestaurantSpecialDietId }}" @if($isChecked) checked @endif>
                                    <label class="form-check-label" for="flexCheckDefault{{ $val->RestaurantSpecialDietId }}">
                                        {{ $val->Name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endif
   





                  </div>

                  <!-- edit features -->

                  <div class="row mt-5">
                 
                 <h5>Select Features</h5>
                 <hr>
                 @if(!$getallFeatures->isEmpty())
                     <div class="restaurant-list">
                         @foreach($getallFeatures as $val)
                             @php
                                 $isChecked = $getfeature->contains('RestaurantFeatureId', $val->RestaurantFeatureId);
                             @endphp
                             <div class="form-check-inline rl-margin">
                                 <input class="form-check-input" type="checkbox" name="features[]" value="{{ $val->RestaurantFeatureId }}"
                                     id="flexCheckDefault{{ $val->RestaurantFeatureId }}" @if($isChecked) checked @endif>
                                 <label class="form-check-label" for="flexCheckDefault{{ $val->RestaurantFeatureId }}">
                                     {{ $val->Name }}
                                 </label>
                             </div>
                         @endforeach
                     </div>
                 @endif






               </div>


                  <!-- end edit deatured -->
                </div>

                <div class="col-md-6 mt-3">
                  <a href="{{route('view_rest_business',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}"
                    class="btn btn-outline-dark">cancel</a>

                  <button class="btn btn-outline-dark update-button2">save</button>

                </div>
              </div>

        </form>



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



</body>

</html>