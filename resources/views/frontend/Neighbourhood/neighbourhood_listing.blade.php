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
  <title>BEST Hotels in @if(!empty($countryname)){{$countryname}} , {{$lname}} @endif - Travell</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!--  fontawesome -->

  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" />

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <!-- <script src="assets/autoComplete.js"></script>
    <link rel="stylesheet" href="assets/autoComplete.min.css"> -->




  <link rel="stylesheet"
    href="https://htmlstream.com/preview/front-v2.1/assets/vendor/ion-rangeslider/css/ion.rangeSlider.css">
  <link rel="stylesheet" href="{{asset('/css/datarange.css')}}">

  <script src="{{asset('/js/datepicker.js')}}"></script>
  <link rel="stylesheet" href="{{asset('/css/renge.css')}}">

  <link rel="stylesheet" href="{{ asset('/css/hotel_listing.css')}}">

  <!-- <link rel="stylesheet" href="{{ asset('/css/hotel-css/style.css')}}"> -->
  <link rel="stylesheet" href="{{ asset('/css/hotel-css/chart.css')}}">
  <link rel="stylesheet" href="{{asset('/css/header.css')}}">

  <!-- nav css -->
  <!-- <link rel="stylesheet" href="{{ asset('/css/style.css')}}"> -->
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">


  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <style>
  .recent-his {
    margin-top: 53px;
  }
  </style>
  <!-- end nav css -->

</head>

<body>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->

  @include('Loc_nav.loc_navbar')

  <div class="hotel-listing-page">
    <div class="container">

      <!-- breadcrums -->



      <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
        <ol class="breadcrumb mt-3">
          <!-- <li class="breadcrumb-item"><a href="{{ url('/') }}">Location</a></li> -->
          <input type="hidden" value="{{$id}}" id="cityid">
          <li class="breadcrumb-item active" aria-current="page"><a
              href=" {{ route('hotel.list',[$id.'-'.strtolower($lname).'-'.strtolower($countryname)]) }} ">

              {{ucfirst($countryname)}}

            </a></li>
          @if(!empty($locationPatent))
          <?php
                $locationPatents = array_reverse($locationPatent);
                ?>
          @foreach ($locationPatents as $location)
          <li class="breadcrumb-item">
            <a
              href="@if(!empty($location)){{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}@endif">
              {{ $location['Name'] }}</a>
          </li>
          @endforeach
          @endif
          <li class="breadcrumb-item active" aria-current="page">


            {{ $lname}}

          </li>

        </ol>
      </nav>

      <!-- end date and breadcrumb -->

      <h1 class="fs24 fw-500 mb-16">Neighborhood in {{$lname}}</h1>
      <hr>

      <span class="d-none loc_id">
        {{$id}}
      </span>
      <span class="d-none hnid">
    5
      </span>
      <span class="d-none slug">
        {{$slug}}
      </span>
      <div class="hotel-listing-card  filter-listing">

        <div class="spinner-border text-secondary" style="margin-left: 537px;" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>


      </div>





    </div>
  </div>


  @include('footer')


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>
  <!-- <script src="assets/t-datepicker.min.js"></script> -->
  <!-- chart -->
  <script src="https://htmlstream.com/preview/front-v2.1/assets/vendor/jquery-migrate/dist/jquery-migrate.min.js">
  </script>

  <script src="https://htmlstream.com/preview/front-v2.1/assets/vendor/ion-rangeslider/js/ion.rangeSlider.min.js">
  </script>
  <script src="https://htmlstream.com/preview/front-v2.1/assets/vendor/chartist/dist/chartist.min.js"></script>

  <!-- JS Front -->
  <script src="https://htmlstream.com/preview/front-v2.1/assets/js/hs.core.js"></script>
  <script src="https://htmlstream.com/preview/front-v2.1/assets/js/components/hs.range-slider.js"></script>
  <script src="https://htmlstream.com/preview/front-v2.1/assets/js/components/hs.chartist-bar-chart.js"></script>
  <script src="https://htmlstream.com/preview/front-v2.1/assets/js/components/hs.chartist-area-chart.js"></script>

  <!-- <script src="{{ asset('/js/index.js')}}"></script> -->

  <!-- JS Plugins Init. -->
  <script>
  $(document).on('ready', function() {
    // initialization of header


    // initialization of forms
    $.HSCore.components.HSRangeSlider.init('.js-range-slider');

    // initialization of chartist bar chart
    $.HSCore.components.HSChartistBarChart.init('.js-bar-chart');

  });

  $(window).on('resize', function() {
    setTimeout(function() {
      $.HSCore.components.HSChartistBarChart.init('.js-bar-chart');
      $.HSCore.components.HSChartistAreaChart.init('.js-area-chart');
    }, 800);
  });
  </script>
  <script>
  var $sliderValue = $("#form_slider1").val(),
    $sliderCounter = $('.rangevalue'),
    $rangeSlider = $("#form_slider1");

  // set value initially
  $sliderCounter.html($sliderValue)

  // update value on scrub
  $rangeSlider.on("input", function() {
    $sliderValue = $(this).val();
    $sliderCounter.html($sliderValue)
  });

  var $sliderValue = $("#form_slider2").val(),
    $sliderCounter = $('.rangevalue'),
    $rangeSlider = $("#form_slider2");

  // set value initially
  $sliderCounter.html($sliderValue)

  // update value on scrub
  $rangeSlider.on("input", function() {
    $sliderValue = $(this).val();
    $sliderCounter.html($sliderValue)
  });
  </script>

  <script>
  var $slider = $('.hotel-listing-slider');

  if ($slider.length) {
    var currentSlide;
    var slidesCount;
    var sliderCounter = document.createElement('div');
    sliderCounter.classList.add('slider__counter');

    var updateSliderCounter = function(slick, currentIndex) {
      currentSlide = slick.slickCurrentSlide() + 1;
      slidesCount = slick.slideCount;
      $(sliderCounter).text(currentSlide + '/' + slidesCount)
    };

    $slider.on('init', function(event, slick) {
      $slider.append(sliderCounter);
      updateSliderCounter(slick);
    });

    $slider.on('afterChange', function(event, slick, currentSlide) {
      updateSliderCounter(slick, currentSlide);
    });

    $slider.slick();
  }





  if ($('.firstspan').hasClass('current-page')) {
    $('.chevron_back').hide();
  } else {
    $('.chevron_back').show();
  }
  </script>
 
  <script>
  var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
    .location.port : '');
  var base_url = baseURL + '/';

  var loc_id = $('.loc_id').text();
  var slug = $('.slug').text();

  function updateSearchResults(page) {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: base_url + 'fetch_neighb_listing',
      data: {
        'locationid': loc_id,
        'slug':slug,
        'page':page,
        '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        // Update the search results
        $('.filter-listing').html(response);
      }
    });
  }

  // Initial request for the first page
  updateSearchResults(1);

  // Event listener for pagination links
  $(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    var page = $(this).attr('href').split('page=')[1];
    updateSearchResults(page);
  });
  </script>


  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="{{asset('js/hotellisting.js')}} "></script>
  <script src="{{asset('js/header.js')}}"></script>

  <!-- nav js -->
  <script src="{{ asset('css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>

  <!-- end nav js -->


</body>

</html>