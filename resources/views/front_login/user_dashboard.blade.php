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

  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">

  <link rel="icon" type="image/x-icon" href="{{asset('/favicon.ico')}}">
  <!-- nav css -->
  <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/signin.css')}}">

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
	
	    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Custom CSS and JS -->
    <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">

    <!-- Custom JavaScript -->
    <script type="text/javascript" src="{{ asset('/js/custom.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js')}}"></script>

    <!-- Slick Carousel CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" />

    <!-- Datepicker CSS -->
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />

    <!-- Additional Hotel Styles -->
    <link href="{{ asset('/css/hotel-css/autoComplete.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/hotel-css/t-datepicker.min.css') }}" rel="stylesheet" type="text/css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
<link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
<link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
<link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
<link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}">
<link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">
<link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/slick.min.js')}}"></script>
<script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js')}}"></script>
<script src="{{ asset('/js/header.js')}}"></script>
<script src="{{ asset('/js/restaurant.js')}}"></script>
<script src="{{ asset('/js/custom.js')}}"></script>
	
	
	
	
  <!-- end nav css -->
  <style>


  .page-content-wrapper {
    flex: 1 0 auto;
    display: flex;
    flex-direction: column;
  }

  footer {
    flex-shrink: 0;
    margin-top: auto;
  }

  .recent-his {
    margin-top: 53px;
  }

  .tr-calenders-modal.show,
  .tr-guests-modal.show {
    display: block;
  }

  .tr-calenders-modal,
  .tr-guests-modal {
    display: none;
    position: absolute;
    background: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    z-index: 1000;
  }

  .tr-qty-box .disabled {
    opacity: 0.5;
    cursor: not-allowed;
  }
  </style>
</head>

<body>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  @include('frontend.header')


  <div class="page-content-wrapper">
    <div class="container mt-3">


      <!-- <div class="d-flex flex-column-reverse flex-md-column">
        <div class="banner-container other-sections">
          <img src="{{asset('images/homepagebanner.png')}}" class="img-fluid d-block w-100" alt="">
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
                src="@if($getdata[0]->st_profilelink !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$getdata[0]->st_profilelink}} @else {{ asset('images/User_image.jpg')  }} @endif"
                alt="Profile Picture" class="img-fluid rounded-circle" style="width: 126px;
          height: 126px;
          border-radius: 100%;" data-bs-toggle="modal" data-bs-target="#exampleModalss"
                onclick="event.preventDefault()">
              <br>
              <a href="#" id="changePictureLink">Change Profile Picture</a>
              <form id="changeimg" action="upload.php" method="post" enctype="multipart/form-data" style="display: none;" >
                  <input type="file" id="fileInput" name="pimage" style="display: none;">
                     <input type="hidden" class="userid" value="{{$getdata[0]->UserId}}" name="userid">
                  <input type="submit" value="Upload" id="uploadButton" style="display: none;width: 182px; " class=" form-control btn btn-primary">
                  <!-- <button style="display: none;" class=" form-control btn btn-danger">cancel</button> -->
              </form>
            </div>
                 
            <div class="col-md-7 getupdata">


              <h4 class="mb-4">Welcome, {{$getdata[0]->FirstName}} {{$getdata[0]->LastName}}</h4>
              <p class=""> {{$getdata[0]->Username}}</p>
			  <p class=""> {{$getdata[0]->Email}}</p>
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

              <h6> <a href="#" class="nav-link mt-3" data-bs-toggle="modal" data-bs-target="#postimage"
                  onclick="event.preventDefault()"><u>+ add post </u> </a></h6>

              <!-- <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal">Edit Profile</a> -->
            </div>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-2">

            <div class="ml-5" style="margin-left: 39px;">
              <h6>Activity</h6>
              <h6 class="mt-3"> Post</h6>
              <h6 class="mt-3"><a href="{{ route('userlogout') }}"> Logout</a></h6>
            </div>
          </div>
          <div class="col-md-10">
            <h5 class="" > All Posts</h5>
            <br>
            <div class="row getposts">
              @if(!$getpost->isEmpty())
              @foreach($getpost as $val)
              <div class="col-md-5 mt-3">
                <!-- Card START -->
                <div class="card card-img-scale border-0 overflow-hidden bg-transparent">
                  <!-- Image and overlay -->
                  <div class="card-img-scale-wrapper rounded-3">
                    <!-- Image -->
                    <?php $a= 1 ; ?>
                    @if(!$getpostimages->isEmpty())
                    @foreach($getpostimages as $img)
                    @if($img->postid == $val->Id && $a == 1)

                    <?php $a = 2; ?>
                    <img src="https://s3-us-west-2.amazonaws.com/s3-travell/post-images/{{$img->postimge}}"
                      class="card-img br-10 mb-12" alt="hotel image" style="max-height: 287px;">
                    @endif
                    @endforeach
                    @endif
                  </div>

                  <!-- Card body -->
                  <div class="">
                    <!-- Title -->
                    <?php  
                          
                                  $locationParts = explode(',', $val->LocationName);
                                  $city = isset($locationParts[0]) ? trim($locationParts[0]) : '';
                                  $country = isset($locationParts[1]) ? trim($locationParts[1]) : '';
                          
                          ?>

                    <div class="d-flex align-items-center justify-content-between">
                      <h5>{{ $city }}</h5>

                    </div>
                    <p>{{ $country }}</p>
                    {{$val->Description}}

                  </div>
                </div>
                <!-- Card END -->
              </div>


              @endforeach
              @endif
            </div>

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
          <form action="{{route('updateprofile')}}" method="post" id="myForm">
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
                  <div class="col-md-6">
                    <input type="text" class="form-control fname" value="{{$getdata[0]->FirstName}}"
                      placeholder="First Name" name="fname">
                    <input type="hidden" class="userid" value="{{$getdata[0]->UserId}}" name="userid">

                    <div class="validation-msg" id="fname-msg"></div>
                  </div>
                  <div class="col-md-6">
                    <input type="text" class="form-control lname" placeholder="Last Name" name="lname"
                      value="{{$getdata[0]->LastName}}">
                    <div class="validation-msg" id="lname-msg"></div>
                  </div>
                </div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control bio" value="{{$getdata[0]->Username}}" placeholder="username" name="Username">
                <div class="user-msg" id="email-msg"></div>
              </div>
              <div class="form-group mt-3">
                <input type="text" class="form-control bio" value="{{$getdata[0]->Bio}}" placeholder="Bio" name="bio">
                <div class="validation-msg" id="email-msg"></div>
              </div>
              <div class="form-group mt-3 ">

                <input type="text" class="form-control locname" placeholder="Location" name="location" id="search_city"
                  value="{{$getdata[0]->lName}} @if($getdata[0]->lName !='') , {{ $getdata[0]->cName }}@endif">
                <div class="validation-msg" id="password-msg"></div>

                <span id="citylist"></span>
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


    <!-- post image -->
    <div class="modal fade" id="postimage" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title" id="exampleModalLabel">Add New Post</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="{{route('updateprofile')}}" method="post" id="addpost">
            @csrf
            <div class="modal-body">
              <div class="form-group mt-3 mb-3">
                <label for="exampleInputLocation">Update Image:</label>
                <input type="file" class="form-control" accept="image/*" id="imageInput" name="images[]" multiple>
                <div class="image-msgs"></div>
              </div>

              <div class="col-md-12">
                <div class="form-group mt-3 ">
                  <label for="">Location</label>
                  <input type="text" class="form-control locname" placeholder="Location" name="location"
                    id="search_citys">
                  <div class="validation-msgs" id="password-msg"></div>

                  <span id="citylists"></span>
                </div>
              </div>
              <div class="form-group mt-3">
                <label for="">Description</label>
                <textarea name="description" class="description" rows="10" cols="10"></textarea>
                <input type="hidden" class="userid" value="{{$getdata[0]->UserId}}" name="userid">
                <div class="description-msg" id="email-msg"></div>
              </div>

              <div class="form-group mt-5 mb-5">
                <input class="form-check-input ml-5" type="checkbox" name="preferences[]" id="flexCheckAdventure"
                  value="Adventure">
                <label class="form-check-label" for="flexCheckAdventure">
                  Adventure
                </label>
                <input class="form-check-input ml-5" type="checkbox" name="preferences[]" id="flexCheckExplore"
                  value="Explore">
                <label class="form-check-label" for="flexCheckExplore">
                  Explore
                </label>
                <input class="form-check-input ml-5" type="checkbox" name="preferences[]" id="flexCheckRomance"
                  value="Romance">
                <label class="form-check-label" for="flexCheckRomance">
                  Romance
                </label>
                <input class="form-check-input ml-5" type="checkbox" name="preferences[]" id="flexCheckLuxury"
                  value="Luxury">
                <label class="form-check-label" for="flexCheckLuxury">
                  Luxury
                </label>

                <div class="check-error"></div>
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

    <!-- post image -->
  </div>
	  
	  
  @include('frontend.footer')


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
  <script src="{{ asset('/js/sign_in.js')}}"></script>
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
	
	<script>
$(document).ready(function() {
    // Initialize t-datepicker for hotel search
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    var checkout = new Date();
    checkout.setDate(checkout.getDate() + 4);

    // Initialize the date picker
    $('.t-datepicker').tDatePicker({
        autoClose: true,
        durationArrowTop: 200,
        limitDateRanges: 31,
        numCalendar: 2,
        titleCheckIn: 'Check In',
        titleCheckOut: 'Check Out',
        titleDateRange: 'night',
        titleDateRanges: 'nights',
        titleToday: 'Today',
        titleDays: ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su'],
        titleMonths: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        dateCheckIn: tomorrow,
        dateCheckOut: checkout,
        toDayShowTitle: true,
        dateRangesShowTitle: true,
        dateRangesHover: true,
        toDayHighlighted: true,
        showFullDateRanges: true
    }).on('afterCheckOut', function(e, dataDate) {
        $('#checkInInput1').val(formatDate(dataDate[0]));
        $('#checkOutInput1').val(formatDate(dataDate[1]));
    });

    // Handle tab switching
    $('.tr-nav-tabs .tr-hotel-tab, .tr-nav-tabs .tr-explore-tab').on('click', function() {
        var tab = $(this).data('tab');
        $('.tr-nav-tabs .tr-hotel-tab, .tr-nav-tabs .tr-explore-tab').removeClass('active');
        $(this).addClass('active');
        $('.tr-hotel-form, .tr-explore-form').removeClass('open');
        $('#' + tab).addClass('open');
    });

 // Handle Who button and guest selection
 $('#totalRoomAndGuest').on('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.tr-guests-modal').toggleClass('show');
    });

    // Close guest modal when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.tr-guests-modal, #totalRoomAndGuest').length) {
            $('.tr-guests-modal').removeClass('show');
        }
    });

    // Handle room and guest count
    $('.tr-qty-box .minus').on('click', function() {
        var input = $(this).siblings('input');
        var currentVal = parseInt(input.val());
        if (currentVal > 1) {
            input.val(currentVal - 1);
            updateTotalGuests();
        }
        if (currentVal <= 2) {
            $(this).addClass('disabled');
        }
    });

    $('.tr-qty-box .plus').on('click', function() {
        var input = $(this).siblings('input');
        var currentVal = parseInt(input.val());
        input.val(currentVal + 1);
        $(this).siblings('.minus').removeClass('disabled');
        updateTotalGuests();
    });

    function updateTotalGuests() {
        var rooms = parseInt($('.tr-total-num-of-rooms input').val());
        var adults = parseInt($('#totalAdultsGuest').val());
        
        var text = '';
        if (rooms > 0) {
            text += (rooms === 1 ? 'Room: ' : 'Rooms: ') + rooms + ' ';
        }
        if (adults > 0) {
            text += (adults === 1 ? 'Adult: ' : 'Adults: ') + adults;
        }
        
        $('#totalRoomAndGuest').val(text);
    }

    // Initialize values
    updateTotalGuests();

    // Handle hotel search
    $('#searchhotel').on('input', function() {
        var query = $(this).val();
        if(query.length >= 2) {
            $.ajax({
                url: '/search-locations',
                method: 'GET',
                data: { query: query },
                success: function(response) {
                    $('#hotel_loc_list').html(response);
                    $('#hotel_loc_list').show();
                }
            });
        } else {
            $('#hotel_loc_list').hide();
        }
    });

    // Handle location selection
    $(document).on('click', '.location-item', function() {
        var locationId = $(this).data('id');
        var locationSlug = $(this).data('slug');
        var locationName = $(this).text();
        
        $('#searchhotel').val(locationName);
        $('#location_id').text(locationId);
        $('#slug').text(locationSlug);
        $('#location-name').text(locationName);
        $('#hotel_loc_list').hide();
    });

    // Handle form submission
    $('#hotelForm').on('submit', function(e) {
    e.preventDefault();
    
    // Get form values
    var locationId = $('#location_id').text().trim();
    var locationName = $('#searchhotel').val().trim();
    var checkin = $('#checkInInput1').val();
    var checkout = $('#checkOutInput1').val();
    var rooms = $('.tr-total-num-of-rooms input').val();
    var guests = $('.tr-total-num-of-adults input').val();

    // Reset previous error states
    $('.error').removeClass('error');
    
    // Validate all fields are filled
    if (!locationName || !locationId || !checkin || !checkout || !rooms || !guests) {
        if (!locationName || !locationId) {
            $('#searchhotel').addClass('error');
        }
        if (!checkin || !checkout) {
            $('.t-datepicker').addClass('error');
        }
        if (!rooms || !guests) {
            $('#totalRoomAndGuest').addClass('error');
        }
        alert('Please fill in all required fields');
        return false;
    }

  

        // Store search data in session
        $.ajax({
            url: '/store-hotel-search',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                slugid: locationId,
                slug: locationSlug,
                checkin: checkin + '_' + checkout,
                rooms: rooms,
                guest: guests
            },
            success: function(response) {
                window.location.href = '/hotel-listing/' + locationSlug;
            },
            error: function(xhr) {
                console.error('Error storing search data:', xhr);
            }
        });
    });
});

// Helper function to format date
function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}
</script>
<script>
$(document).ready(function() {
    // Use mousedown instead of click for better interaction handling
    $(document).on('mousedown', function(e) {
        // Get all the elements we want to check
        var $target = $(e.target);
        var excludedSelectors = [
            '.tr-find-hotels',
            '.tr-search-btn-icon',
            '.tr-search-info-section',
            '.tr-guests-modal',
            '.tr-calenders-modal',
            '.t-datepicker',
            '.t-dates',
            '.tr-btn',
            '.tr-nav-tabs'
        ];
        
        // Check if click is outside all excluded elements
        var clickedOutside = !excludedSelectors.some(function(selector) {
            return $target.closest(selector).length > 0;
        });
        
        // If clicked outside, close everything
        if (clickedOutside) {
            $('.tr-find-hotels').removeClass('open show');
            $('.tr-guests-modal').removeClass('show');
            $('.tr-calenders-modal').removeClass('show');
            $('.tr-search-info-section').removeClass('hide');
            $('.tr-nav-tabs').removeClass('show');
        }
    });
});
</script>
