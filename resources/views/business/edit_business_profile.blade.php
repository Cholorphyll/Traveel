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
  <title>dashboard - Travell</title>
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
  <link rel="stylesheet" href="{{ asset('/css/signin.css')}}">

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
  <nav class="navbar-expand-sm static-navbar align-items-start">

    <div class="container mt-3  d-flex">

      <a class="navbar-brand" href="{{route('homepage')}}"><img src="{{ asset('images/logo.png') }}" alt=""
          class="navlogo" style="max-width: 78px;"></a>

      <nav class="navbar navbar-expand-lg">

        <div class="container-fluid">

          <div class="collapse navbar-collapse" id="navbarText">

            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal">Sign up</a>
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
                 
             }
            ?>
            @if (session()->has('business_user')) 
            <!-- <span class="getuser-nav"> -->
            <p class="" style="margin-top: 16px;
margin-right: 19px;">{{$Username}}</p>
                <li class="nav-item active ">
                <div class="dropdown">
                    <a class="nav-link p-0  dropdown-toggle" href="#"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                         <img src="@if($user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$user_image}}   @else {{ asset('public/images/Frame 61.svg') }} @endif" alt=""
                        class="usericon img-fluid rounded-circle" style="height: 49px;">
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
    <div class="container mt-3">


      <!-- <div class="d-flex flex-column-reverse flex-md-column">
        <div class="banner-container other-sections">
          <img src="{{asset('public/images/homepagebanner.png')}}" class="img-fluid d-block w-100" alt="">
          <div class="banner-text fw-500">
            Discover New Destinations
          </div>
        </div>
      </div> -->

      <div class=" " style="margin-top: 54px;">

        <div class="col-md-12 mt-3 mb-5">
          <div class="row">
            <div class="col-md-3 getimge">
              <img
                src="@if($getdata[0]->user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$getdata[0]->user_image}} @else {{ asset('public/images/User_image.jpg')  }} @endif"
                alt="Profile Picture" class="img-fluid rounded-circle" style="width: 126px;
          height: 126px;
          border-radius: 100%;" data-bs-toggle="modal" data-bs-target="#exampleModalss"
                onclick="event.preventDefault()">
              <br>
              <a href="#" id="changePictureLink">Change Profile Picture</a>
              <form id="changeimg" action="{{route('busi_changeprofileimg')}}" method="post" enctype="multipart/form-data"
                style="display: none;">
                @csrf
                <input type="file" id="fileInput" name="pimage" style="display: none;">
                <input type="hidden" class="userid" value="{{$getdata[0]->id}}" name="userid">
                <input type="submit" value="Upload" id="uploadButton" style="display: none;width: 182px;"
                  class=" form-control btn btn-primary">
                <!-- <button style="display: none;" class=" form-control btn btn-danger">cancel</button> -->
              </form>
            </div>

            <div class="col-md-7 getupdata">


              <h4 class="mb-4">Welcome, {{$getdata[0]->username}}</h4>
              <p class=""> {{$getdata[0]->busi_username}}</p>
              <p class=""> {{$getdata[0]->email}}</p>
             @if($getdata[0]->website !="") <p class="">website - <a href="{{$getdata[0]->website}}">{{$getdata[0]->website}}</a></p>@endif
              
              <p class="">Joined on {{ date('Y-m-d', strtotime($getdata[0]->CreatedOn)) }}</p>
              @if($getdata[0]->lName !="")
              <p class="">Lives in
                <br>
                <span>{{ $getdata[0]->lName }}, {{ $getdata[0]->cName }}</span>
              </p>
              @endif
              @if($getdata[0]->lName !="")
              <p class=""><b>About</b>

                <br>
                {{ $getdata[0]->Bio }}
                <br>
                <span>Lives in {{ $getdata[0]->lName }}, {{ $getdata[0]->cName }}</span>
              </p>
              @endif
            </div>

            <div class="col-md-2">
              <h6> <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss"
                  onclick="event.preventDefault()"><u>Edit Profile</u></a></h6>

              <!-- <h6> <a href="#" class="nav-link mt-3" data-bs-toggle="modal" data-bs-target="#postimage"
                  onclick="event.preventDefault()"><u>+ add post </u> </a></h6> -->

              <!-- <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal">Edit Profile</a> -->
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">

           
          </div>
          <div class="col-md-10">
            <div class="row allattractions d-none">
              <!-- <h5 class=""> All Attraction</h5> -->
              <br>
            </div>
            <!-- <div class="row getposts allposts ">
              <h5 class=""> All Posts</h5>

            </div> -->

          </div>

        </div>














      </div>




    </div>


    <!--add tip  -->

    <!-- Modal -->
    <div class="modal fade" id="exampleModalss" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" id="exampleModalLabel">Edit Profile</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" id="myForm">
            @csrf
            <div class="modal-body">
              <div class="form-group mt-3 mb-3">
                <label for="exampleInputLocation">Update Profile Image:</label>
                <input type="file" class="form-control" aria-describedby="emailHelp" accept="image/*" name="image">
                <div class="validation-msg" id="location-msg"></div>
              </div>

              <div class="col-md-12">
                <div class="row">
                  <!-- Add a row div to contain the columns -->
                  <div class="col-md-12">
                    <label for="exampleInputLocation">Name</label>
                    <input type="text" class="form-control fname" value="{{$getdata[0]->username}}"
                      placeholder="First Name" name="name">
                    <input type="hidden" class="userid" value="{{$getdata[0]->id}}" name="userid">

                    <div class="validation-msg" id="fname-msg"></div>
                  </div>
                  <div class="col-md-6">

                  </div>
                </div>
              </div>

              <div class="form-group mt-3">
                <label for="exampleInputLocation">Username</label>
                <input type="text" class="form-control bio" value="{{$getdata[0]->busi_username}}"
                  placeholder="username" name="busi_username">
                <div class="user-msg" id="email-msg"></div>
              </div>
              <div class="form-group mt-3">
                <label for="exampleInputLocation">Website</label>
                <input type="text" class="form-control" value="{{$getdata[0]->website}}"
                  placeholder="website" name="website">
                <div class="user-msg" id="email-msg"></div>
              </div>
              <div class="form-group mt-3">
                <label for="exampleInputLocation">Current Location</label>
                <input type="text" class="form-control locname" placeholder="Location" name="location" id="search_city"
                  value="{{$getdata[0]->lName}} @if($getdata[0]->lName !='') , {{ $getdata[0]->cName }}@endif"
                  autoComplete="off">
                <div class="validation-msg" id="password-msg"></div>

                <span id="citylist"></span>


              </div>
              <div class="form-group  ">
                <label for="exampleInputLocation">About</label>
                <textarea name="bio" style="height: 109px;">{{ $getdata[0]->Bio }}</textarea>

                <div class="validation-msg" id="email-msg"></div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <button type="submit" class="btn btn-primary" style=" height: 38.118px;">Save</button>
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
    <!-- end add tip  -->


  </div>
  @include('footer')


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>


  <!-- nav js -->
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>
  <script src="{{ asset('/js/business.js')}}"></script>
  <!-- end nav js -->
  <script>
  var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
    .location.port : '');
  var base_url = baseURL + '/';

  // $(document).ready(function() {
  //     $('#myForm').submit(function(event) {
  //         event.preventDefault();

  //         var fname = $('.fname').val();
  //         var lname = $('.lname').val();
  //         var bio = $('.bio').val();
  //         var locname = $('.locname').val();
  //         var userid = $('.userid').val();

  //         // If all validations pass, submit the form using AJAX
  //         $.ajax({
  //             headers: {
  //                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  //             },
  //             type: 'POST',
  //             url: base_url + 'updateprofile', // Change to your Laravel route
  //             data: {
  //                 fname: fname,
  //                 lname: lname,
  //                 bio: bio,
  //                 locname: locname,
  //                 userid: userid
  //             }, // Corrected the data format to an object
  //             success: function(response) {
  //                // $('#myForm')[0].reset();
  //                 $('#exampleModalss').modal('hide');
  //                 $('.getupdata').html(response);

  //             },
  //             error: function(xhr, textStatus, errorThrown) {
  //                 console.log(xhr.responseText);
  //             }
  //         });

  //     });
  // });


  </script>

  <script>
  $(document).on('keyup', '#search_city', function() {

    var value = $('#search_city').val();
    if (value != "") {
      $("#citylist").css("display", "block");

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'search_loc_city',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },

        success: function(response) {

          $('#citylist').html("");


          response.forEach(function(country) {
            var listItem = $('<li>').text(country.value + ',' + country.country);
            //   $('#country-list').fadeIn();
            $('#citylist').append(listItem);
          });
        }
      });
    } else {

      $('#citylist').append("");
      //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function() {
      $("#search_city").val($(this).text());
      $('#citylist').fadeOut();

    });
  })
  </script>


  <script>
  $(document).ready(function() {
    $('#addpost').submit(function(event) {
      event.preventDefault();

      // Initialize FormData with the form
      var formData = new FormData($(this)[0]);

      // Validate form fields
      var isValid = true;

      // Check if any input field is empty
      $(this).find('input[type="text"], textarea').each(function() {
        if ($(this).val().trim() === '') {
          isValid = false;
          $(this).next('.validation-msgs').text('This field is required.').css('color', 'red');
        } else {
          $(this).next('.validation-msgs').text('');
        }
      });

      var description = $('.description');
      if (description.val().trim() === '') {
        isValid = false;
        description.siblings('.description-msg').text('Description is required.').css('color', 'red');
      } else {
        description.siblings('.description-msg').text('');
      }

      // Check if at least one checkbox is checked
      var checkboxesChecked = $('input[type="checkbox"]:checked').length;
      if (checkboxesChecked == 0) {
        isValid = false;
        $('.check-error').text('Please select at least one checkbox.').css('color', 'red');
      } else {
        $('.check-error').text('');
      }

      // If form is valid, submit it using AJAX
      if (isValid) {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: base_url + 'addpost',
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            if (response == 0) {
              return $('.validation-msgs').text('Invalid location. please select again.').css('color',
                'red');

            }
            $('#postimage').modal('hide');

            $('.getposts').html(response);
          },
          error: function(xhr, textStatus, errorThrown) {
            console.log(xhr.responseText);
          }
        });
      }
    });
  });
  </script>

  <script>
  $(document).on('keyup', '#search_citys', function() {

    var value = $('#search_citys').val();
    if (value != "") {
      $("#citylists").css("display", "block");

      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'search_loc_citys',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },

        success: function(response) {

          $('#citylists').html("");


          response.forEach(function(country) {
            var listItem = $('<li>').text(country.value + ',' + country.country);
            //   $('#country-list').fadeIn();
            $('#citylists').append(listItem);
          });
        }
      });
    } else {

      $('#citylists').append("");
      //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function() {
      $("#search_citys").val($(this).text());
      $('#citylists').fadeOut();

    });
  })
  </script>
</body>

</html>