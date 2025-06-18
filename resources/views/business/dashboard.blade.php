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
  <title>Business dashboard - Travell</title>
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


  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <link rel="stylesheet" href="{{ asset('/css/business_index.css')}}">
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

          <li class="nav-item">
            <a href="{{route('createbusiness')}}" class="nav-link" >List Business</a>
          </li>
          <!-- <li class="nav-item">
            <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal1">Sign in</a>
          </li> -->

          <li class="nav-item">
            <a class="nav-link" href="#">Claim your listing</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Product</a>
          </li>
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
  <ul class="navbar-nav  mt-lg-0 content-end" >
            <?php
             if (session()->has('business_user')) {
                $userData = session('business_user');
                $Username = $userData['Username'];
                $user_image = $userData['user_image'];
                $name = $userData['name'];
                
                 
             }
            ?>
            @if (session()->has('business_user')) 
            <!-- <span class="getuser-nav"> -->
            <p class="" style="margin-top: 16px;
margin-right: 19px;">{{$Username}}</p>
                <li class="nav-item active ">
                <div class="dropdown">
                    <a class="nav-link p-0  dropdown-toggle" href="#"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                         <img src="@if($user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$user_image}}   @else {{ asset('images/Frame 61.svg') }} @endif" alt=""
                        class="usericon img-fluid rounded-circle" style="height: 49px;">
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                      
                        <li><a class="dropdown-item" href="{{route('edit_business_profile')}}">View Profile</a></li>
                        <li><a class="dropdown-item" href="{{route('logout')}}">Logout</a></li>
                    
                    </ul>
                </div>

                </li>
                <!-- </span> -->
             @else
            <li class="nav-item active">
        
                <a class="form-control" data-bs-toggle="modal" data-bs-target="#exampleModal1"  role="button" style="background: #CB4C14;color: white;     text-decoration: none;border: none;">Sign in</a>
                  
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


      <div class="d-flex flex-column-reverse flex-md-column">
        <div class="banner-container other-sections">
          <img src="{{asset('images/homepagebanner.png')}}" class="img-fluid d-block w-100" alt="">
          <div class="banner-text fw-500">
            Discover New business
          </div>
        </div>
      </div>

      <div class="aboutustext">
      <div class="col-md-9" style="margin: auto;">

         <!-- <h3 style="text-align: center;" class="border:bottom;">Welcome back ,{{ $Username }}</h3>
         <hr> -->
                @if(!$getbusiness->isEmpty())
                 
                  
              <table class="table">
              <tr>
                    <td colspan="2" style="text-align: center;"> <h3 >Welcome back , {{$name}}</h3></td>
             </tr>
                @foreach($getbusiness as $bus_val)  
                  <tr>
                <td colspan="2">  @if($bus_val->category == "Accommodation") <a href="{{route('view_hotel_business',[$bus_val->id,$bus_val->slug])}}"  class="back"> 
                @elseif($bus_val->category == "Restaurant") 
                <a href="{{route('view_rest_business',[$bus_val->id,$bus_val->slug])}}"  class="back"> 
                @elseif($bus_val->category == "Things to do")
                <a href="{{route('view_bussines',[$bus_val->id,$bus_val->slug])}}"  class="back"> 
                @endif
  <h5 > <span style="margin-right: 16px;"> 
   <!-- <img src="{{asset('images/forks.svg')}}" alt="" > -->
                      @if($bus_val->category == "Accommodation")
                        <img src="{{asset('business/hotel.png')}}" height="20" width="20" alt="">
                        @elseif($bus_val->category == "Restaurant")
                        <img src="{{asset('business/restaurant.png')}}" height="20" width="20" alt="">
                        @elseif($bus_val->category == "Things to do")
                        <img src="{{asset('business/sunrise.png')}}" height="20" width="20" alt="">
                        @endif
</span>{{$bus_val->name}}</h5>
  <span style="margin-left: 32px;"> {{$bus_val->address}}</span>
  </a> </td>
                  </tr>
                  @endforeach
              </table>
                @endif


               
        </div>


        <div class="mt-5">
         <h4>Travell Business</h4> 
        </div>
        <!--<div class="subtext">
          YAAN or YANA- derived from DHARMASASTRA Literature means- ‘JOURNEY’ or ‘MARCHING’.
        </div>-->
        <div class="row">
          <div class="col-md-4"><input type="text" class="form-control" placeholder="Location" id="Location">

            <div class="recent-his search-box-info  d-none bg-white px-4 b-20 shadow-1 position-absolute">
              <!-- <p class="small my-3" id="recent-search">@if (Session::has('lastsearch')) RECENTLY VIEWED @else POPULAR
                  DESTINATIONS @endif</p> -->
              <p id="getresult" class="px-4 autoCompletewrapper" style="margin-top: -35px;
    width: auto;
    margin-left: -48px;">
           
            </p>
            </div>
          </div>
          <div class="col-md-4"> <input type="text" class="form-control" placeholder="Business Name" id="businame">
          </div>
          <div class="business search-box-info  d-none bg-white px-4 b-20 shadow-1 position-absolute">
            <p id="business_result" class="px-4 autoCompletewrapper" style="margin-left: 310px;margin-top: 38px;width: auto;">           
                </p>
          </div>
          <div class="col-md-4"> <button class="btn btn-secondary" id="searchBtn">Search</button></div>

        </div>



      </div>
      <section class=" other-sections my-md-64px">
        <div class="secttitle">How we Work</div>


        <div class=" howwework">
          <div class="text-center card flex-1">

            <img src="{{asset('images/globe.svg')}}" alt="" class="mb-20 d-flex justify-content-center mx-auto">
            <p class="color707070 mt-0"> At Travell, we're the leading travel search engine, dedicated to
              making your trip planning hassle-free. Our powerful search engine scans hundreds of travel sites to find
              you the best deals on flights, hotels, car rentals, and more, all in one place.</p>

          </div>
          <div class="text-center card flex-1 centercard">

            <img src="{{asset('images/searchglobe.svg')}}" alt=""
              class="mb-20 d-flex justify-content-center mx-auto">
            <p class="color707070 mt-0"> You'll always get the lowest prices directly from the travel
              providers, with no added fees. We're compensated by the providers when you book through us, so our service
              is free for you.</p>
          </div>
          <div class="text-center card flex-1">

            <img src="{{asset('images/Planning.svg')}}" alt=""
              class="mb-20 d-flex justify-content-center mx-auto">
            <p class="color707070 mt-0"> Plus, we display travel-related ads that may interest you, and some
              companies pay us when you click on them. Whether you're an experienced traveler or planning
              your first trip, make Travell your go-to travel companion for seamless planning and booking.</p>
          </div>
        </div>
      </section>
    </div>


    @include('footer')
    <!-- Button trigger modal -->


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Sign In</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{ url('/save_user') }}" method="post" id="myForm">
            @csrf
            <div class="modal-body">

              <div class="col-md-12">
                <div class="row">
                  <!-- Add a row div to contain the columns -->
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                      placeholder="First Name" name="fname">
                    <div class="validation-msg" id="fname-msg"></div>
                  </div>
                  <div class="col-md-6">
                    <input type="text" class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp"
                      placeholder="Last Name" name="lname">
                    <div class="validation-msg" id="lname-msg"></div>
                  </div>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
                  placeholder="Email Address" name="email">
                <div class="validation-msg" id="email-msg"></div>
              </div>
              <div class="form-group mt-3 ">

                <input type="password" class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp"
                  placeholder="Password" name="password">
                <div class="validation-msg" id="password-msg"></div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <button type="submit" class="btn btn-primary">Submit</button>
                  </div>
                  <div class="col-md-6">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>

                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Sign In</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form>
            @csrf
            <div class="modal-body">

              <div class="col-md-12">
                <div class="row">
                  <!-- Add a row div to contain the columns -->
                  <div class="form-group mt-3">
                    <input type="email" class="form-control email" id="exampleInputEmail1" aria-describedby="emailHelp"
                      placeholder="Email Address" name="email">
                    <div class="validation-msg" id="email-msg"></div>
                  </div>

                </div>
              </div>

              <div class="form-group mt-3 ">

                <input type="password" class="form-control password" id="exampleInputEmail2"
                  aria-describedby="emailHelp" placeholder="Password" name="password">
                <div class="validation-msg" id="password-msg"></div>
              </div>

              <div class="login_error" id=""></div>

            </div>

            <div class="modal-footer">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <button type="submit" class="btn btn-primary" id="loginform">Submit</button>
                  </div>
                  <div class="col-md-6">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  </div>

                </div>


              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


    <script>
    var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
      .location.port : '');
    var base_url = baseURL + '/';
    $(document).ready(function() {
      $('#myForm').submit(function(event) {
        event.preventDefault();

        // Reset previous validation messages
        clearValidationMessages();

        // Validate form fields
        var valid = true;

        valid = valid && validateField('fname', 'fname-msg', 'Please enter a First Name');
        valid = valid && validateField('lname', 'lname-msg', 'Please enter a Last Name');
        valid = valid && validateField('email', 'email-msg', 'Please enter an Email Address');
        valid = valid && validateField('password', 'password-msg', 'Please enter a Password');
        // Add more fields as needed

        // Check unique email asynchronously
        valid = valid && checkUniqueEmail();

        // If all validations pass, submit the form
        if (valid) {
          this.submit();
        } else {
          // Display a general error message if there are empty fields
          $('#general-msg').text('Please fill in all required fields').css('color', 'red');
        }
      });

      function validateField(fieldName, msgId, errorMessage) {
        var value = $('input[name="' + fieldName + '"]').val().trim();
        if (value === '') {
          $('#' + msgId).text(errorMessage).css('color', 'red');
          return false;
        }
        return true;
      }

      function clearValidationMessages() {
        // Clear validation messages for each field
        $('#fname-msg, #lname-msg, #email-msg, #password-msg, #general-msg').empty();
      }

      function checkUniqueEmail() {
        var email = $('input[name="email"]').val().trim();
        var fname = $('input[name="fname"]').val().trim();
        var lname = $('input[name="lname"]').val().trim();
        var password = $('input[name="password"]').val().trim();
        if (email === '') {
          return false; // Skip validation if email is empty
        }


        // Make an AJAX request to check unique email
        var unique = true;
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: base_url + 'save_user', // Change this to your server-side validation endpoint
          method: 'POST',
          data: {
            'email': email,
            'fname': fname,
            'lname': lname,
            'password': password
          },

          async: false, // Use synchronous request for simplicity (asynchronous may be preferred in production)
          success: function(response) {
            if (!response.unique) {
              $('#email-msg').text('Email address already exists. Please choose a different email.').css(
                'color', 'red');
              unique = false;
            }
          },
          error: function() {
            $('#general-msg').text('Error checking email uniqueness.').css('color', 'red');
            unique = false;
          }
        });

        return unique;
      }
    });
    </script>
    <script>
    $(document).on('keyup', '#Location', function() {
      var value = $('#Location').val();


      $('#getresult').html("");

      $("#Location").css("display", "block");

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'searchLocation',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('.recent-his').removeClass('d-none');
          $('.recent-his #getresult').html(response);
        }
      });

    })
    $(document).on('click', '#getresult li div span', function() {
      var selectedValue = $(this).text();
      $('#Location').val(selectedValue);
      $('#getresult').html(""); // Assuming you want to clear the results after selecting a location
    });
    </script>
    <script>
    $(document).on('keyup', '#businame', function() {
      var value = $('#businame').val();


      $('#business_result').html("");

      $("#business").css("display", "block");

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'searchbusiness',
        data: {
          'val': value
         
        },
        success: function(response) {
          $('.business').removeClass('d-none');
          $('#business_result').html(response);
        }
      });

    })
    $(document).on('click', '#business_result li div span', function() {
      var selectedValue = $(this).text();
      $('#businame').val(selectedValue);
      $('#business_result').html(""); // Assuming you want to clear the results after selecting a location
    });
    </script>
    <script>
    $(document).ready(function() {
      // Add a click event handler for the "Search" button
      $('#searchBtn').on('click', function() {
        // Focus on the "Business Name" input field
        $('#businame').focus();
      });
    });
    </script>

    <script>
    $(document).ready(function() {
      $(document).on('click', '#loginform', function(event) {
        event.preventDefault(); // Prevent the default form submission

        var email = $('.email').val();
        var pass = $('.password').val();

        // Clear previous error messages
        $('.validation-msg').text('');
        $('.login_error').html('');
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          url: base_url + 'loginbusiness',
          method: 'POST',
          data: {
            'email': email,
            'password': pass,
            '_token': $('meta[name="csrf-token"]').attr('content')
          }, // Fix: Use an object for data
          success: function(response) {
            // Redirect to a new page or handle success accordingly
            // window.location.href = '/dashboard';
            if (response == 0) {
              $('.login_error').text('Invalid credentials').css('color', 'red');
            }
            if (response !== 0) {

              window.location.href = '/dashboard';
            }
          },
          error: function(xhr, status, error) {
            console.error(xhr.responseText); // Log any errors to the console
          }
        });
      });
    });
    </script>


    <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
    <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
    <!-- <script src="{{ asset('/js/custom.js')}}"></script> -->
    <!-- end nav js -->


</body>

</html>