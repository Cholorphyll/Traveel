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
  <title>Business - Restaurant</title>
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
              <li class="nav-item">
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
              </li>
              <li class="nav-item">
                <a class="nav-link" href="#" id="menu">Menu</a>
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
        <p class="" style="margin-top: 16px;
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


  <div class="">
    <div class="container">
      <div class=" mt-5 bus-list ">
        <div class="row">
          @if(!$getbusiness->isEmpty())
          @foreach($getbusiness as $val)
          <?php
            
        
       ?>
          <div class="col-md-2">


            <img src="{{asset('images/Hotel lobby -nmustsee (4).svg')}}" class="card-img">

          </div>
          <div class="col-md-6">
            <p>{{$val->Title}}</p>
            <div class="d-flex text-neutral-2 align-items-center mb-2">


            </div>
            <span>#Restaurant ID: {{$val->RestaurantId}}</span>
            <br>
            <?php
            if($val->slgid !=''){
             $locid = $val->slgid;
            }else{
              $locid = $val->LocationId ;
             }
                       
            ?>
            <!-- <span>{{$val->Address}}</span> -->
            <span><a
                href="{{ url('rd-'.$locid.'-' .$val->RestaurantId .'-'.strtolower( str_replace(' ', '_',  str_replace('#', '!',$val->Slug) )) ) }}"
                target="_blank">View your listing</a></span>
          </div>



        </div>

        @endforeach
        @endif
        <hr>


        <h3>Good evening,</h3>
        <p class="mb-3">Welcome to the new Management Centre! Discover new features, tools and more to grow your
          business.</p>
        <hr>


      </div>
      <!-- start business -->
      <div class="col-md-12  bus-info d-none">
        <hr>
        <h3>Business Info</h3>
        <div class="row">
          <div class="col-md-6 ">
            <div class="info-box general-information">

              <h5 class="mb-3">General information <span style="margin-left: 267px;
    font-size: medium;">
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
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+add description'</a>
                @endif</p>
              <h5>Timings</h5>
              <p>@if($getbusiness[0]->Timings !=""){{$getbusiness[0]->Timings}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+add timing</a>
                @endif</p>

              <h5>Price Range</h5>
              <p>@if($getbusiness[0]->PriceRange !=""){{$getbusiness[0]->PriceRange}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+PriceRange</a>
                @endif</p>

              <h5>Menu</h5>
              <p>@if($getbusiness[0]->MenuLink !="") <a
                  href="{{$getbusiness[0]->MenuLink}}">{{$getbusiness[0]->MenuLink}}</a> @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+Menu Link</a>
                @endif</p>
            </div>

          </div>
          <div class="col-md-6">
          <div class="info-box">
              <h5 class="mb-3">Location 
              @if($getbusiness[0]->varify_business != 0)
                <span style="margin-left: 354px;
    font-size: medium;"> <a href="{{route('edit_brestaurant_location',[$getbusiness[0]->bid])}}">edit</a></span>
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
                @if($getbusiness[0]->Latitude != "" && $getbusiness[0]->Latitude != "")
                <div id="map1" class="" style="width: 100%; height: 400px;"></div>

                <!-- <div id="screenshotContainer"></div> -->
                @endif

              </div>

            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 ">
          <div class="info-box ">
              <h5 class="mb-3">Restaurant Cuisine & Restaurant SpecialDiet
              @if($getbusiness[0]->varify_business != 0)
                <span style="margin-left: 47px;
    font-size: medium;"> <a class="nav-link" href="{{route('edit_rest_cuisine',[$getbusiness[0]->bid])}}" >edit</a></span>
          @endif
              </h5>

              <hr class="mb-3" style="margin: auto;">
              <h5>Cuisine</h5>

              <p>
                @if(!$getcuisen->isEmpty())
                  @foreach($getcuisen as $val)
                  {{$val->Name}}@if(!$loop->last), @endif
                  @endforeach
                 @else <a class="nav-link"
                 href="{{route('edit_rest_cuisine',[$getbusiness[0]->bid])}}" >+add cuisine</a>
                @endif
              </p>

             <h5>Special Diet</h5>

              <p>
                @if(!$getSpecialDiet->isEmpty())
                  @foreach($getSpecialDiet as $gets)
                  {{$gets->Name}}@if(!$loop->last), @endif
                  @endforeach
                 @else 
                 <a class="nav-link"
                 href="{{route('edit_rest_cuisine',[$getbusiness[0]->bid])}}" >+add Special Diet</a>
                @endif
              </p>

              <h5>Feature</h5>

                <p>
                  @if(!$getfeature->isEmpty())
                    @foreach($getfeature as $gets)
                    {{$gets->Name}} @if(!$loop->last), @endif
                    @endforeach
                  @else 
                  <a class="nav-link"
                  href="{{route('edit_rest_cuisine',[$getbusiness[0]->bid])}}" >+add Features</a>
                  @endif
                </p>

              
            </div>

          </div>
          <div class="col-md-6">
         
          <div class="info-box getcontact">
              <h5 class="mb-3">Contact Link
              @if($getbusiness[0]->varify_business != 0)
                <span style="margin-left: 354px;
    font-size: medium;"> <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal2">edit</a></span>
             @endif
              </h5>

              <hr class="mb-3" style="margin: auto;">
              <h5>Phone</h5>
              <p>@if($getbusiness[0]->Phone !=""){{$getbusiness[0]->Phone}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModal2">+add phone number</a> @endif</p>
              <h5>Email</h5>
              <p> @if($getbusiness[0]->Email !=""){{$getbusiness[0]->Email}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModal2">+add Email</a> @endif</p>

              <h5>Website</h5>
              <p> @if($getbusiness[0]->Website !=""){{$getbusiness[0]->Website}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModal2">+add website</a> @endif</p>

            </div>
          </div>
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
          <div class="">
          <!-- <h4>Primary photo</h4>
          <img src="" alt="" class="usericon img-fluid" style="width: auto;"> -->

          <h4 class="mt-5 mb-3"> All Photos <span class="float-right"><a href="{{route('add_rest_photos',[$getbusiness[0]->bid])}}"
                class="btn btn-dark">Add photos</a></span></h4>
                @if(!$getimage->isEmpty())
              @foreach($getimage as $getimages)
                <div class="image-wrapper mt-3 mr-3" style="display: inline-block; position: relative;margin-left: 16px;">
                  <img src="https://s3-us-west-2.amazonaws.com/s3-travell/business-images/{{$getimages->Image}}" alt="" class="image" style="width: 323px;height: 231px;">
               
                  
                  </span>
                </div>
              @endforeach
            @else
              Images not found.
            @endif
          </div>

          <!-- end custom image  -->


        </div>
      </div>
      <!-- end photos and videos -->
      <!-- start Reviews  -->

      <div class=" mt-5 reviews d-none">
        <div class="row">
          <h4>Reviews </h4>
          <h5 class="mlr">Latest Reviews<span style="font-size: 18px;
    margin-left: 804px;"><a href="{{route('rest_all_reviews',[$getbusiness[0]->bid])}}" target="_blank">All
                reviews</a></span> </h5>

          @if(!$getreview->isEmpty())
          @foreach($getreview as $val)
          <div class="col-md-10 review-outline">
            <p><b>{{$val->Name}}</b></p>
            <p>{{$val->CreatedOn}}</p>

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
			
			 <p  style="text-align: center;">Reviews not found</p>
          @endif







        </div>
      </div>
      <!-- end Reviews-->

      <!-- start -->
        
      <div class="menu-list d-none">
             <h4 class="mt-5" style="text-align: center;
    color: #7abc7a;"> Manage your menu </h4>
             <hr>
             <h4 class="mt-5 mb-3"  style="
    color: #7abc7a;"> Menu Link </h4>

                    
            
             <div class="updated-menulink">
				   @if($getbusiness[0]->MenuLink !="")    
              <p> <a href="{{$getbusiness[0]->MenuLink}}">{{$getbusiness[0]->MenuLink}}</a>
                </p> 
                @endif
                <h5 class="mt-5">add, edit menu link</h5>
                <div class="col-md-10">
                 <form action="" id="update_menu_link">
                  <input type="text" class="form-control" name="menulink"  value="">
                  <input type="hidden" class="form-control " value="{{$getbusiness[0]->bus_id}}" name="bus_id">
                  <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}" name="business_id">
                
     
                  <button type="submit"  class="btn btn-dark mt-3"  @if($getbusiness[0]->varify_business == 0)   disabled   @endif>Submit</button>
         
            </form>
               </div>
              
                </div>
		  <hr>
            <h4  style="
    color: #7abc7a;">Menu List
   
     <span class="float-right">
      <a   href= "{{route('add_rest_menu',[$getbusiness[0]->bid])}} "
            class="btn btn-dark">Add new menu</a> </span>
   
        </h4>
       
              @if(!$getbusiness->isEmpty())
                @if($getbusiness[0]->Menu !="")
                  <?php $Menu = json_decode($getbusiness[0]->Menu, true); ?>
                    <ol>
                        @foreach($Menu as $item)
                            <li>{{ $item['menuitem'] }}</li>
                        @endforeach
                    </ol>
                @else
                  <p>Menu not found.</p>
                @endif
                  
              @else
                  <p>Menu not found.</p>
              @endif
       
      </div>
      <!-- end menu -->

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
        <form action="" method="post" id="edit-gi-rest">
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
                  <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}" name="business_id">

                  <input type="hidden" class="userid" value="" name="userid">

                  <div class="validation-msg" id="fname-msg"></div>
                </div>
                <div class="col-md-6">

                </div>
              </div>


              <div class="form-group mt-3">
                <label for="exampleInputLocation">Business description(Optional)</label>
                <textarea name="about" style="height: 215px;">{{$getbusiness[0]->About}}</textarea>

                <div class="validation-msg" id="email-msg"></div>
              </div>
            </div>
            <div class="form-group mt-3">
              <label for="exampleInputLocation">Timming</label>

              <input type="text" placeholder="12:00 AM - 11:59 PM" class="form-control " name="Timings"
                value="{{$getbusiness[0]->Timings}}">

              <div class="validation-msg" id="email-msg"></div>
            </div>
          
          <div class="form-group mt-3">
            <label for="exampleInputLocation">Price Range</label>
            <input type="text" placeholder="£10 - £30" name="PriceRange" class="form-control "
              value="{{$getbusiness[0]->PriceRange}}">
            <div class="validation-msg" id="email-msg"></div>
          </div>
   
      <div class="form-group mt-3">
        <label for="exampleInputLocation">Menu Link</label>
        <input type="text" placeholder="" name="MenuLink" class="form-control "
          value="{{$getbusiness[0]->MenuLink}}">
        <div class="validation-msg" id="email-msg"></div>
      </div>
    
    <div class="modal-footer">
      <div class="col-md-12">
        <div class="row">
          <div class="col-md-6">
            <button type="submit" class="btn btn-primary update-button" style=" height: 38.118px;">Save</button>
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


  <!-- map screenshot code -->
  <!-- <script src="https://unpkg.com/leaflet-simple-map-screenshoter"></script>
<script src="https://unpkg.com/file-saver/dist/FileSaver.js"></script> -->




  <!-- 
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
</script> -->




  <script>
  document.addEventListener("DOMContentLoaded", function() {
    var businessListingLink = document.getElementById("business-Listing");
    var businessInfoLink = document.getElementById("Business-info");
    var photosVideosLink = document.getElementById("Photos-&-videos");
    var reviewsheading = document.getElementById("reviews-heading");
    var menu = document.getElementById("menu");

    var busList = document.querySelector(".bus-list");
    var busInfo = document.querySelector(".bus-info");
    var photos = document.querySelector(".photos");
    var reviews = document.querySelector(".reviews");
    var menulist = document.querySelector(".menu-list");

    var tabLinks = [businessListingLink, businessInfoLink, photosVideosLink, reviewsheading,menu];

    function resetTabColors() {
      tabLinks.forEach(function(link) {
        link.style.color = "";
      });
    }

    businessListingLink.addEventListener("click", function(event) {
      event.preventDefault();
      busList.classList.remove("d-none");
      busInfo.classList.add("d-none");
      photos.classList.add("d-none");
      reviews.classList.add("d-none");
      menulist.classList.add("d-none");
      resetTabColors();
      businessListingLink.style.color = "blue";
    });

    businessInfoLink.addEventListener("click", function(event) {
      event.preventDefault();
      busList.classList.add("d-none");
      busInfo.classList.remove("d-none");
      photos.classList.add("d-none");
      reviews.classList.add("d-none");
      menulist.classList.add("d-none");
      resetTabColors();
      businessInfoLink.style.color = "blue";

      // Initialize the map after making the container visible
      initializeMap();
    });

    photosVideosLink.addEventListener("click", function(event) {
      event.preventDefault();
      busList.classList.add("d-none");
      busInfo.classList.add("d-none");
      reviews.classList.add("d-none");
      menulist.classList.add("d-none");
      photos.classList.remove("d-none");

      resetTabColors();
      photosVideosLink.style.color = "blue";
    });

    reviewsheading.addEventListener("click", function(event) {
      event.preventDefault();
      busList.classList.add("d-none");
      busInfo.classList.add("d-none");
      photos.classList.add("d-none");
      menulist.classList.add("d-none");
      reviews.classList.remove("d-none");
      resetTabColors();
      reviewsheading.style.color = "blue";
    });
    menu.addEventListener("click", function(event) {
      event.preventDefault();
      busList.classList.add("d-none");
      busInfo.classList.add("d-none");
      photos.classList.add("d-none");
      reviews.classList.add("d-none");
      menulist.classList.remove("d-none");
      
      resetTabColors();
      menu.style.color = "blue";
    });
    




    function initializeMap() {
      var latitude = "{{ $getbusiness[0]->Latitude }}";
      var longitude = "{{ $getbusiness[0]->Longitude }}";

      if (latitude !== "" && longitude !== "") {
        var mapOptions = {
          center: [parseFloat(latitude), parseFloat(longitude)],
          zoom: 10
        };

        var map = new L.map('map1', mapOptions);

        var layer = new L.TileLayer('https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png');
        map.addLayer(layer);

        var customIcon = L.icon({
          iconUrl: '{{ asset("images/map-marker-icon.png") }}',
          iconSize: [32, 32], // Adjust the size as needed
          iconAnchor: [16, 32] // Adjust the anchor point if needed
        });

        var marker = L.marker([parseFloat(latitude), parseFloat(longitude)], {
          icon: customIcon
        }).addTo(map);


        setTimeout(function() {
          map.invalidateSize();
        }, 100);
      }
    }
  });
  </script>


  <!-- end screen shot js -->


</body>

</html>