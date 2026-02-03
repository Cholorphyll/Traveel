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

  <!-- end nav css -->
  <style>
  .recent-his {
    margin-top: 53px;
  }

  /* Add some CSS to style the selected tab */
  .btn.selectedTab {
    background-color: #6c757d;
    /* You can customize the color */
    color: #fff;
  }

  .btn.selectedTab-btn {
    background-color: #6c757d;
    /* You can customize the color */
    color: #fff;
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



    <nav class="navbar-expand-sm static-navbar align-items-start">

<div class="container mt-3  d-flex">

  <a class="navbar-brand" href="{{route('homepage')}}"><img src="{{ asset('images/logo.png') }}" alt=""
      class="navlogo" style="max-width: 78px;"></a>

  <nav class="navbar navbar-expand-lg">

    <div class="container-fluid">

      <div class="collapse navbar-collapse" id="navbarText">

        <ul class="navbar-nav me-auto mb-2 mb-lg-0">

          <span class="parent">
            <li class="nav-item">
              <a class="nav-link " href="#" id="">Business Listing</a>
            </li>

            <div class="b-list">
              @if(!$getallbusiness->isEmpty())
              @foreach($getallbusiness as $getbusi)

              <tr class="business-row" style="">


                <td class="business-info">

                  @if($getbusi->category == "Accommodation")
                  <a href="{{ route('view_hotel_business', [$getbusi->id,$getbusi->slug]) }}" class="link-business">
                    <img src="{{asset('business/hotel.png')}}" height="20" width="20" alt="">
                    @elseif($getbusi->category == "Restaurant")
                    <a href="{{ route('view_bussines', [$getbusi->id,$getbusi->slug]) }}" class="link-business">
                      <img src="{{asset('business/restaurant.png')}}" height="20" width="20" alt="">
                      @elseif($getbusi->category == "Things to do")
                      <a href="{{ route('view_bussines', [$getbusi->id,$getbusi->slug]) }}" class="link-business">
                        <img src="{{asset('business/sunrise.png')}}" height="20" width="20" alt="">
                        @endif
                        {{$getbusi->name}}
                        <br>
                        <span style="font-size: small; margin-left: 26px;">{{$getbusi->address}}</span>
                      </a>
                </td>

              </tr>
              <br>
              @endforeach
              @endif
            </div>
          </span>
      <!--    <li class="nav-item">
            <a class="nav-link" id="business-Listing">Overview</a>
          </li>
          <li class="nav-item">
            <a class="nav-link Business-info" href="#" id="Business-info">Business info</a>
          </li>

          <li class="nav-item">
            <a class="nav-link" href="#" id="Photos-&-videos">Photos & videos</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" id="reviews-heading">Reviews</a>
          </li>-->
          <li class="nav-item">
            <a class="nav-link" href="#">Marketing Tools
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Help</a>
          </li>
        </ul>



      </div>
    </div>


  </nav>
  <ul class="navbar-nav  mt-lg-0 content-end">
    <?php
         if (session()->has('business_user')) {
            $userData = session('business_user');
            $Username = $userData['Username'];
            $user_image = $userData['user_image'];
             
         }
        ?>
    @if (session()->has('business_user'))
    <!-- <span class="getuser-nav"> -->
    <p class="" style=" margin-left: 481px;margin-top: 16px;
margin-right: 19px;">{{$Username}}</p>
    <li class="nav-item active ">

      <div class="dropdown">
        <a class="nav-link p-0  dropdown-toggle" href="#" id="dropdownMenuButton1" data-bs-toggle="dropdown"
          aria-expanded="false">
          <img
            src="@if($user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$user_image}}   @else {{ asset('images/Frame 61.svg') }} @endif"
            alt="" class="usericon img-fluid rounded-circle" style="height: 49px;">
        </a>
        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
          <li><a class="dropdown-item" href="{{route('business_dashboard')}}">Dashboard</a></li>
          <li><a class="dropdown-item" href="{{route('logout')}}">Logout</a></li>

        </ul>
      </div>

    </li>
    <!-- </span> -->
    @else
    <li class="nav-item active">

      <a class="form-control" data-bs-toggle="modal" data-bs-target="#exampleModal1" role="button"
        style="background: #CB4C14;color: white;text-decoration: none;border: none;">Sign in</a>

    </li>
    @endif
    <!-- <li class="nav-item active">
               <p> <a  href="{{route('user_login')}}" style="text-decoration: none;">dashboard</a></p>
                  
             </li> -->
  </ul>
</div>
</nav>


    <div class="container mt-3 " style="max-width: 837px;">
      @if(!$getbusiness->isEmpty())

      <h3 style="color:#ff601f; text-align:center;"> {{$getbusiness[0]->name}} </h3>

      @endif



      @if(!$getbusiness->isEmpty())
       
        
       <div class="col-md-12 mt-3 ">
        <div class="row">
        <div class="col-md-2">
            @if($getbusiness[0]->Image !="")
            <img src="https://s3-us-west-2.amazonaws.com/s3-travell/business-images/{{$getbusiness[0]->Image}}" alt="" class="usericon img-fluid" style="height: 109px;">
            @else

            <img src="{{asset('images/Hotel lobby -nmustsee (4).svg')}}" class="card-img">
            @endif
          </div>
          <div class="col-md-6">
            <p>{{$getbusiness[0]->name}}</p>
            <div class="d-flex text-neutral-2 align-items-center mb-2">
              @for ($i = 0; $i < 5; $i++) @if($i < $getbusiness[0]->TAAggregateRating )
                <img src="{{('images/star.svg')}}" alt="" class="stars">
                @else
                <i class="far fa-star text-111"></i>
                @endif
                @endfor


            </div>
            <span>#Sight ID: {{$getbusiness[0]->business_id}}</span>
            <br>
            <?php
            if($getbusiness[0]->slugid !=''){
             $locid = $getbusiness[0]->slugid;
            }else{
              $locid = $getbusiness[0]->LocationId ;
             }
           
          
            
            ?>
            <!-- <span>{{$getbusiness[0]->address}}</span> -->
            <span><a
                href="{{ url('at-'.$locid.'-' .$getbusiness[0]->business_id .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$getbusiness[0]->slug) )) ) }}"
                target="_blank">View your listing</a></span>
          </div>

        </div>
       </div>
         




      
        @endif
        <hr>






      <div class="col-md-12 mb-3">
        <div class="row mb-3">
          <!-- Add a row div to contain the columns -->
          <div class="mt-3 col-md-6">
            <div class="box-border"><a href="{{ route('add_photos',[$getbusiness[0]->bid,$getbusiness[0]->slug])}}"  style="text-decoration: none;"><img src="{{asset('business/images-icon.svg')}}" alt="" height="35"> Add More Photos</a>
              <span style="font-size: small; margin-left: 40px;">PhotosCapture the attention of potential customers —
                add 10+ Management Photos</span>
            </div>
          </div>
          <div class="mt-3 col-md-6">
        
            <div class="box-border" style="padding: 30px;"><img src="{{asset('business/images-icon.svg')}}" alt="" height="35"><a href="{{ route('edit_sight_info',[$getbusiness[0]->bid])}}" style="text-decoration: none;"> View
              Business Info </a>
              <span></span>
              <!-- <span style="font-size: small; margin-left: 40px;">PhotosCapture the attention of potential customers —
                add 10+ Management Photos</span> -->
            </div>
          </div>

        </div>

        <hr>
        <div class="row mb-3">
          <!-- Add a row div to contain the columns -->
          <div class="mt-3  col-md-6"> 
            <div class="box-border" style="padding: 30px;"><img src="{{asset('business/images-icon.svg')}}" alt="" height="35"><a href="{{ route('sightreviews',[$getbusiness[0]->bid]) }}" style="text-decoration: none;"> Reviews</a>
              <span style="font-size: small; margin-left: 40px;"></span>
            </div>
          </div>
          <!-- <div class="mt-3 col-md-6"> 

            <div class="box-border"><img src="{{asset('business/images-icon.svg')}}" alt="" height="35"> Review Express
              <span style="font-size: small; margin-left: 40px;">PhotosCapture the attention of potential customers —
                add 10+ Management Photos</span>
            </div>
          </div> -->

        </div>
        <!-- <hr> -->
<!-- 
        <div class="row mb-3">
        
          <div class="mt-3 col-md-6">
            <div class="box-border"><img src="{{asset('business/images-icon.svg')}}" alt="" height="35"> Manage Listing
              <span style="font-size: small; margin-left: 40px;">PhotosCapture the attention of potential customers —
                add 10+ Management Photos</span>
            </div>
          </div>
          <div class="mt-3 col-md-6">

            <div class="box-border"><img src="{{asset('business/images-icon.svg')}}" alt="" height="35"> Guides and Articles
              <span style="font-size: small; margin-left: 40px;">PhotosCapture the attention of potential customers —
                add 10+ Management Photos</span>
            </div>
          </div>

        </div>
 -->




      </div>
    </div>

    @include('footer')
    <!-- Button trigger modal -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    <script>
    var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
      .location.port : '');
    var base_url = baseURL + '/trip/';
    </script>
    <script>
    // jQuery code to show/hide b-list on hover
    // $(document).ready(function() {
    //     // When mouse enters .abc, show .b-list
    //     $(".abc").mouseenter(function() {
    //         $(".b-list").slideDown(); // You can use .show() or other animations as well
    //     });
    //     // When mouse leaves .abc, hide .b-list
    //     $(".abc").mouseleave(function() {
    //         $(".b-list").slideUp(); // You can use .hide() or other animations as well
    //     });
    // });

    $(document).ready(function() {
      var parent = $(".parent");

      // When mouse enters parent, show .b-list
      parent.mouseenter(function() {
        $(".b-list").show();
      });

      // When mouse leaves parent, hide .b-list
      parent.mouseleave(function() {
        $(".b-list").hide();
      });
    });
    </script>




    <!-- <script src="{{ asset('/js/custom.js')}}"></script> -->
    <!-- end nav js -->


</body>

</html>