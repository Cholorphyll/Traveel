<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Restaurant | Travell (2025)</title> 
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!--  fontawesome -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" />




  <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
	 <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
 <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
        <!-- nav css -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> 
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}"> 
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
      <!-- end nav css -->
<link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
	<script src="{{ asset('js/cookie-consent.js') }}"></script>
</head>

<body>
@include('Loc_nav.loc_navbar')
@if(!$rest->isEmpty())

  <!-- Modal -->
  <div class="review-popup">

    <div class="modal fade" id="review_model" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <form action="" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Write a review</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <p class="mb-10"><b>Would you recommend visiting {{$rest[0]->Title}} ?</b></p>
              <span class="name-error"></span>
              <input type="text" placeholder="Name" class="form-control mb-20 name">
              <div class="email-error"></div>
              <input type="email" placeholder="Email" class="form-control mb-20 email">
              <textarea name="" id="" cols="30" rows="10" placeholder="Write a review"
                class="reviewfield mb-20 review_desc"></textarea>

              <div class="review-category">
                <div class="rev-desc">
                  Great Location
                </div>
                <div class="rev-desc active">
                  Amazing Hotel
                </div>
                <div class="rev-desc">
                  First Class
                </div>
              </div>
              <div class="fs14 mb-15 fw-500">
                Add Photos
              </div>

              <section class="mb-24">

                <div class="form-group">
                  <div class="dropzone-wrapper">
                    <div class="dropzone-desc">
                      <img src="{{asset('/images/Group.png')}}" width="32.727px" height=" 30px;" alt="">

                      <p><span class="text-decoration-underline">Upload a Photo</span> or drag and
                        drop</p>
                    </div>

                    <div class="field" align="left">
                      <input type="file" id="files" name="files[]" class="dropzone" multiple />
                    </div>
                  </div>
                </div>

              </section>
              <div class="fs14 mb-20 mt-18 fw-500 text-center">
                How likely do you recommend us to your friend?
              </div>
              <div class="form-check">
                  <input class="form-check-input rating" type="radio" name="rating" value="1"  checked>
                 
                   Yes
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input rating" type="radio" name="rating" value="0"  >
              
                   No
                  </label>
                </div>
         
              <!-- <nav aria-label="Page navigation example">
                <ul class="pagination star-rating justify-content-center">
                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item"><a class="page-link" href="#">4</a></li>
                  <li class="page-item"><a class="page-link" href="#">5</a></li>


                </ul>
              </nav> -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary add-rest-review">Submit Here</button>

            </div>
          </form>

        </div>
      </div>
    </div>

  </div>


  <div class="container pt-5">

	<!--breadcrumb--->
	    @if(!$rest->isEmpty())
	  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
  
              <ol class="breadcrumb">                
              @if(!empty($breadcumb))
              <li class="breadcrumb-item active" aria-current="page">
                  <a href="{{ route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])}}"> {{$breadcumb[0]->ccName}}</a>
                  </li>              
              <li class="breadcrumb-item">
                <a
                  href="{{ route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])}}">
                  @if(!empty($breadcumb)) {{$breadcumb[0]->CountryName}} @endif</a>
              </li>
              @endif

                @if(!empty($locationPatent))
                <?php
                $locationPatent = array_reverse($locationPatent);
                ?>
                @foreach ($locationPatent as $location)
                <li class="breadcrumb-item">
                  <a
                    href="@if(!empty($rest)){{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}@endif">
                    {{ $location['Name'] }}</a>
                </li>
                @endforeach
                @endif
                <li class="breadcrumb-item " aria-current="page">
                    <a
                    href="{{ route('search.results',[$rest[0]->slugid.'-'.strtolower($rest[0]->LSlug)]) }}"><span>{{$rest[0]->Lname}}</span>
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{$rest[0]->Title}}</li>               
              </ol>
     
            </nav>
         @endif



    <!-- Below gallery -->
    <h2 class="fs-32 my-sm-13">
      <b>{{$rest[0]->Title}}</b>
    </h2>

    <p class="color707070 mb-2">
       <!-- Finger Food, Asian, Fast Food, North Indian, Beverages -->

      @if(!$getcuisine->isEmpty())
      @foreach($getcuisine as $value)
      {{$value->Name}}
      @if(!$loop->last)
      ,
      @endif

      @endforeach

     
    @endif
    </p>
    <div class="row justify-content-center flex-md-row">
      <div class="col-md-7 pe-30">
        <div class="topper-column">

          <div class=" ">

            <div class="d-md-flex justify-content-between align-items-end locationandothers mb-15">
              <div>
                <?php
                  // Define the current day of the week (e.g., "mon", "tue", etc.)
                  $currentDay = strtolower(date("D"));

                  // Decode the JSON schedule from your database
                  $schedule = json_decode($rest[0]->Timings, true);

                  // Check if the current day exists in the schedule
                  if (isset($schedule['time'][$currentDay])) {
                      $openingTime = $schedule['time'][$currentDay]['start'];
                      $closingTime = $schedule['time'][$currentDay]['end'];
                      

                    if ($openingTime === '00:00' && $closingTime === '23:59') {
                        $formattedTime = '12:00 AM - 11:59 PM';
                    } else {
                        $formattedTime = $openingTime . ' - ' . $closingTime;
                    }
                    $isOpen = true;
                  } else {
                      $isOpen = false;
                  }
?>

                <p class="mb-2 mt-2 mt-md-0">
                  <b class="color707070 mb-0 fs18">{{$rest[0]->Address}}</b>
                </p>

                @if ($isOpen)
                <p class="mb-0">
                  <b>
                    <span>Open Now</span>
                    <span class="mx-15 d-inline-flex">-</span>
                    <span>{{$formattedTime}} (Today)</span>
                  </b>
                </p>
                @else
                <p class="mb-0">
                  <b>
                    <span>Closed Today</span>
                  </b>
                </p>
                @endif
              </div>
              <ul class="d-flex  mb-0 mt-2">
                <li class="save d-flex align-items-center"><img src="images/save.svg" alt="" class="mr-4 ">
                  <span class=""><a href="" class="text-decoration-none">Save</a></span>
                </li>
                <li class="d-flex align-items-center">
                  <img src="images/sahre.svg" alt="" class="mr-4">
                  <span class=""><a href="">Share</a></span>
                </li>
              </ul>
            </div>


          </div>


          <!-- hotel-gallery -->


          <div class="hotel-gallery responsive-container">
            <div class="row overflow-borderradius position-relative">
              <img src="{{ asset('/images/Group 1171275916.png') }}" class="h-100 p1" alt="">
            </div>
          </div>

        </div>
        <section class="overview">
          <h5 class="mb-40 fs24"><b>Overview</b></h5>
          <div class="row d-flex mb-40">
            <div class="col-md-6">
              <div class="how-to-get-there">
                <img src="{{ asset('/images/plane.svg')}}" alt="">
                <div>
                  <p>Restaurant Safety Measure</p>
                  <b>Daily Temp. Checks</b>
                </div>
              </div>
            </div>

          </div>

        </section>


        <p class="rest_id d-none">{{ $rest[0]->RestaurantId }}</p>
        <div class="d-flex ">
          <h6 class="mb-17 fs18 mb-30"><b>Menu</b></h6>
        </div>
        <div class="d-flex row">



          <div class="d-flex flex-wrap ">
            @if($rest[0]->Menu !="")
            <?php $menu = json_decode($rest[0]->Menu, true); ?>
            @foreach($menu as $menu)

            <button type="button" class="m-3 menu-btn" style="">{{$menu['menuitem']}}</button>

            @endforeach
            @else 
            not available.
                @endif
          </div>
          <!-- <div class="col-md-4">
            <img
              src="{{asset('/images/wepik-abstract-simple-burgers-free-delivery-restaurant-menu-20230724173656b7NE.png')}}"
              alt="" class="d-block w-100 mb-20">
            <b class="d-block mb-10 ">Burger Menu</b>
            <b class="d-block text-neutral-2">6 Pages</b>
          </div>
          <div class="col-md-4">
            <img
              src="{{asset('/images/wepik-abstract-simple-burgers-free-delivery-restaurant-menu-20230724173656b7NE.png')}}"
              alt="" class="d-block w-100">
          </div>
          <div class="col-md-4">
            <img
              src="{{asset('/images/wepik-abstract-simple-burgers-free-delivery-restaurant-menu-20230724173656b7NE.png')}}"
              alt="" class="d-block w-100">
          </div>-->
        </div>
        <hr class="d-block">

        <section>
          <div class="d-flex ">
            <h6 class="fs24 mb-30"><b>Cuisines</b></h6>
          </div>

          <div class="d-flex flex-wrap ">
            @if(!$getcuisine->isEmpty())
            @foreach($getcuisine as $value)
            <button type="button" class="m-3 menu-btn">{{$value->Name}}</button>
            @endforeach

            @else
            <p>Cuisines not available.</p>
            @endif
          </div>
        </section>
        <hr class="d-block">

        <section>
          <div class="row align-items-start">
            <div class="col-md-11">
              <div class="d-flex ">
                <h6 class="fs24 mb-24"><b>Features</b></h6>
              </div>
              <p class="fw-500">
                @if(!$getfetures->isEmpty())
                @foreach($getfetures as $val)
                {{$val->Name}}
                @if (!$loop->last)
                ,
                @endif
                @endforeach
                @else
              <p>Features not available.</p>
              @endif
              </p>
            </div>
          </div>
        </section>
        <hr class="d-block">
        <section>
          <div class="row align-items-start">
            <div class="col-md-11">
              <div class="d-flex ">
                <h6 class="fs24 mb-24"><b>Average Cost</b></h6>
              </div>
                <p class="fw-500"><b class="d-block"> {{$rest[0]->PriceRange}} for one order (approx.)</b>
                <b class="d-block text-neutral-2 fs14">Exclusive of applicable taxes and charges, if
                  any</b>
              </p>
            </div>
          </div>
        </section>
        <hr class="d-block">
        <section>
          <div class="max-width500">
            <div class="d-flex ">
              <h6 class="fs24 mb-24"><b>More Info</b></h6>
            </div>

            <div class="d-flex flex-wrap more-info">
            @if(!$getspecialdiet->isEmpty())
                @foreach($getspecialdiet as $diet)               
              <div class="d-flex align-items-center">
                <img src="{{ asset('/images/Check-Success.png')}}" alt="" class="check-success d-block mr-10">
                <span> {{$diet->Name}}</span>
              </div>
              @endforeach

              @else
              <p>Info not available.</p>
              @endif
              <!-- <div class="d-flex align-items-center">
                <img src="{{ asset('/images/Check-Success.png')}}" alt="" class="check-success d-block mr-10">
                <span>Desserts and Bakes</span>
              </div>
              <div class="d-flex align-items-center">
                <img src="{{ asset('/images/Check-Success.png')}}" alt="" class="check-success d-block mr-10">
                <span>4/5 Star</span>
              </div>
              <div class="d-flex align-items-center">
                <img src="{{ asset('/images/Check-Success.png')}}" alt="" class="check-success d-block mr-10">
                <span>No Seating Available</span>
              </div> -->

            </div>
          </div>
          <hr class="d-block">
          <section>
            <div class="row align-items-start">
              <div class="col-md-11">
                <div class="d-flex ">
                  <h6 class="fs24 mb-24"><b>RELATED TO {{$rest[0]->Title}} </b></h6>
                </div>
                <p class="fw-500">Restaurants in JW Marriott, Sector 35 B, Restaurants in Chandigarh,
                  Chandigarh Restaurants, Sector 35 restaurants, Best Sector 35 restaurants,
                  Chandigarh City restaurants, in Chandigarh, near me, in Chandigarh City, in Sector
                  35, in Chandigarh, near me, in Chandigarh City, in Sector 35, in Chandigarh, near
                  me, in Chandigarh City, in Sector 35, Order food online in Sector 35, Chandigarh,
                  Order food online in Chandigarh, Order food online in Chandigarh City</p>
              </div>
            </div>
          </section>
          <hr class="d-block">
          <section>
            <div class="row align-items-start">
              <div class="col-md-11">
                <div class="d-flex ">
                <?php
                  $address = $rest[0]->Address;

                  $partBeforeComma = "";
                  if (!empty($address) && strpos($address, ',') !== false) {
                      $parts = explode(',', $address, 2); 
                     
                      $partBeforeComma = trim($parts[0]);                
                     
                  } else {
                    $partBeforeComma = substr($partBeforeComma,0,100);
                  }
                  ?>
                  <h6 class="fs24 mb-24"><b>RESTAURANTS AROUND {{strtoupper( $partBeforeComma)}}</b></h6>
                </div>
                <p class="fw-500">  @if(!empty($near_restaurant))
                  @foreach($near_restaurant as $ns)
                  @if($rest[0]->Title != $ns->Title)         
                  <a href="{{ route('restaurant_detail',[$ns->slugid.'-'.$ns->RestaurantId.'-'.$ns->Slug]) }}" class="nr-rest"> {{$ns->Title}}</a>
                  @if(!$loop->last)
                  ,
                  @endif
                  @endif
                  @endforeach
                  @else
                <p>Restaurant not found.</p>

                @endif</p>
              </div>
            </div>
          </section>
          <hr class="d-block">
          <section>
            <div class="row align-items-start">
              <div class="col-md-11">
                <div class="d-flex ">
                  <h6 class="fs24 mb-24"><b>RESTAURANTS AROUND {{$rest[0]->Title}}</b></h6>
                </div>
                <p class="fw-500">

                  @if(!empty($near_restaurant))
                  @foreach($near_restaurant as $near_restaurant)
                  @if($rest[0]->Title != $near_restaurant->Title)
                 <a href="{{route('restaurant_detail',[$near_restaurant->slugid.'-'.$near_restaurant->RestaurantId.'-'.$near_restaurant->Slug])}}" class="nr-rest"> {{$near_restaurant->Title}}</a>
                
                  @if(!$loop->last)
                  ,
                  @endif
                  @endif
                  @endforeach
                  @else
                <p>Restaurant not found.</p>

                @endif

                </p>
              </div>
            </div>
          </section>
      </div>


      <div class="col-md-5 pt-2 ps-30">
        <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
          <span>89%</span>
        </li>
        <h5 class="fs24 mb-20"><b>Reviews ({{count($restreview)}})</b></h5>
        <div class="d-flex mb-24">
          <button type="button" class="btn btn-outline-dark mr-10 reviews-button" data-bs-toggle="modal"
            data-bs-target="#review_model">Write a Review</button>
          <button type="button" class="btn btn-outline-dark reviews-button">Sort By <svg
              xmlns="http://www.w3.org/2000/svg" width="11" height="7" viewBox="0 0 11 7" fill="none">
              <path d="M1 1L5.5 6L10 1" stroke="black" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" />
            </svg></button>
        </div>

        <span class="review-update">
          <div class="d-flex flex-wrap ">
            @if(!empty($restreview))
            @foreach($restreview as $rval)
            <div class="key-facts bg-F7F7F7">{{  substr($rval->Description, 0, 30)}}</div>
            @endforeach

            @endif
          </div>



          <div class="d-flex align-items-start mt-4">

            <div>

              @if(!$restreview->isEmpty())
              @foreach($restreview as $restreviews)
              <div class="d-flex ">

                <div class="mr-25">
                  <img src="{{ asset('/images/image 22.png')}}" class="w-51px " alt="">
                </div>
                <div class="">
                  <h5 class=" fs18"><b>{{$restreviews->Name}} </b></h5>
                  <?php $date = date('j F, Y', strtotime($restreviews->CreatedOn));
 ?>
                  <div class="fs14">Date of stay: {{$date}}</div>
                </div>

              </div>
              <div class=" fs18 my-20">
                <b>{{$restreviews->Description}}</b>
              </div>
              @endforeach

              @else
              <p>Reviews not found.</p>
              @endif

              <!-- <section class="overview">
                <h5 class="mb-8 fs24"><b>Overview</b></h5>


                <div class="more">
                  <span itemprop="description">

                    <p class="fs15"></p>
                  </span>
                </div>





              </section> -->


            </div>
            <!-- <img src="{{asset('/images/dots.svg')}}" alt="" class="d-none d-md-inline-block"> -->
          </div>

        </span>
        <hr class="d-block">

      </div>


    </div>

  </div>
@else
      <p>Data not Found.</p>              
@endif

 <!-- start footer  -->
 @include('footer')
  <!-- end footer  -->


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.2.0/readmore.min.js"></script>


  <script src="{{ asset('/js/restaurant.js')}}"></script>

  <script src="assets/t-datepicker.min.js"></script>

  
    <!--nav bar-->
    <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>

  <script>
  $(document).ready(function() {
    $('input').val('')
  });

  $('.bloglistingcarousel').each(function() {
    var slider = $(this);
    slider.slick({
      dots: true,
      autoplay: true,
      autoplaySpeed: 5000,
      mobileFirst: true,
      arrows: false,
      responsive: [{
        breakpoint: 480,
        settings: "unslick"
      }]
    });

  });

  //   drag drop  script

  // function readFile(input) {
  //     if (input.files && input.files[0]) {
  //         var reader = new FileReader();

  //         reader.onload = function (e) {
  //             var htmlPreview =
  //                 '<img width="200" src="' + e.target.result + '" />' +
  //                 '<p>' + input.files[0].name + '</p>';
  //             var wrapperZone = $(input).parent();
  //             var previewZone = $(input).parent().parent().find('.preview-zone');
  //             var boxZone = $(input).parent().parent().find('.preview-zone').find('.box').find('.box-body');

  //             wrapperZone.removeClass('dragover');
  //             previewZone.removeClass('hidden');
  //             boxZone.empty();
  //             boxZone.append(htmlPreview);
  //         };

  //         reader.readAsDataURL(input.files[0]);
  //     }
  // }

  // function reset(e) {
  //     e.wrap('<form>').closest('form').get(0).reset();
  //     e.unwrap();
  // }

  // $(".dropzone").change(function () {
  //     readFile(this);
  // });

  // $('.dropzone-wrapper').on('dragover', function (e) {
  //     e.preventDefault();
  //     e.stopPropagation();
  //     $(this).addClass('dragover');
  // });

  // $('.dropzone-wrapper').on('dragleave', function (e) {
  //     e.preventDefault();
  //     e.stopPropagation();
  //     $(this).removeClass('dragover');
  // });

  // $('.remove-preview').on('click', function () {
  //     var boxZone = $(this).parents('.preview-zone').find('.box-body');
  //     var previewZone = $(this).parents('.preview-zone');
  //     var dropzone = $(this).parents('.form-group').find('.dropzone');
  //     boxZone.empty();
  //     previewZone.addClass('hidden');
  //     reset(dropzone);
  // });

  $(document).ready(function() {
    $('.review-category>div').click(function() {
      $('.review-category>div').removeClass("active");
      $(this).addClass("active");
    });]]
  });




  $(document).ready(function() {
    if (window.File && window.FileList && window.FileReader) {
      $("#files").on("change", function(e) {
        var files = e.target.files,
          filesLength = files.length;
        for (var i = 0; i < filesLength; i++) {
          var f = files[i]
          var fileReader = new FileReader();
          fileReader.onload = (function(e) {
            var file = e.target;
            $("<span class=\"pip\">" +
              "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
              "<br/><span class=\"remove remove-image\"></span>" +
              "</span>").insertAfter("#files");
            $(".remove").click(function() {
              $(this).parent(".pip").remove();
            });

          });
          fileReader.readAsDataURL(f);
        }
        console.log(files);
      });
    } else {
      alert("Your browser doesn't support to File API")
    }
  });
  </script>
</body>

</html>