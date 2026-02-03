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
      @if(!$getbusiness->isEmpty())

      <h3 style="color:#ff601f; text-align:center;">{{$getbusiness[0]->Title}} </h3>

      @endif
      <div class="col-md-12 updatedata">

        <hr>
     
        @if(!$getreview->isEmpty())
        <h5 class="mlr">{{count($getreview)}} Reviews</h5>

        @foreach($getreview as $val)
        <div class="col-md-10 review-outline">
          <p><b>{{$val->Name}}</b></p>
          <?php
           
              $dateString = $val->CreatedOn;
              $timestamp = strtotime($dateString);
              $formattedDate = date('Y-m-d', $timestamp);
            ?>

          <p>{{$formattedDate}}</p>

          <div class="d-flex text-neutral-2 align-items-center mb-2">
            @for ($i = 0; $i < 5; $i++) @if($i < $val->Rating )
              <img src="{{('images/star.svg')}}" alt="" class="stars">
              @else
              <i class="far fa-star text-111"></i>
              @endif
              @endfor
          </div>
          <p>{{$val->Description}}</p>
        </div>
        <br>
        @endforeach

        @else
        <p>Reviews not found</p>
        @endif


    <div class="">
    {{ $getreview->links('pagination::bootstrap-5') }}
    </div>
      
        
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




</body>

</html>