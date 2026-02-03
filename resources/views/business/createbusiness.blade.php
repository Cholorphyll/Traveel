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
  <title>About Us - Travell</title>
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
                <a class="nav-link" href="{{route('createbusiness')}}">Create business</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal">Sign Out</a>
              </li>
            </ul>

          </div>
        </div>
      </nav>
    </div>
  </nav>


  <div class="">
    <div class="container mt-3">
      <form  action="{{ url('/save_business') }}" method="post" id="submitForm">
        @csrf


        <div class="col-md-6  mb-3">
          <div class="row mb-3">
            <!-- Add a row div to contain the columns -->
            <div class="mt-3">
              <label for="" class="mb-3 mt-3">Name</label>
              <input type="text" class="form-control name" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="First Name" name="name">
              <div class="validation-msg" id="fname-msg"></div>
            </div>

          </div>

          <hr>
          <div class="col-md-12">
            <div class="row">
              <div class="form-group mt-3">
                <label for="" class="mb-3 mt-3">Which category best describes this place?</label>
                <span>
                  <button type="button" class="btn btn-outline-secondary tab-1 t"
                    onclick="showTab('t-1')">Accommodation</button>
                  <button type="button" class="btn btn-outline-secondary tab-2 t"
                    onclick="showTab('t-2')">Restaurant</button>
                  <button type="button" class="btn btn-outline-secondary tab-3 t" onclick="showTab('t-3')">Things to do</button>
                </span>

                <span class="d-none t-1 mt-3">Which accommodation type best describes this property?
          
                  @foreach($getdata as $getdata)
                  <button type="button" class="btn btn-outline-secondary ml-3 mr-3 mt-3 allbtn {{$getdata->id}}"
                    onclick="selecttab('{{$getdata->id}}')"> {{$getdata->type}}</button>
                  @endforeach

                </span>
              </div>
            </div>
          </div>


          <div class="col-md-8">
          <div class="row">

          <span class="d-none t-2 mt-3">
            What kind of place is this?
            <input type="text" class="form-control restval" id="exampleInputEmail1" aria-describedby="emailHelp"
              placeholder="e.g. American, Baker, Italian, Chinese" name="email">
          </span>
          <span class="d-none t-3  mt-3">
            What kind of place is this?
            <input type="text" class="form-control thingval" id="exampleInputEmail1" aria-describedby="emailHelp"
              placeholder="e.g Museum, Park, Aquarium" name="email">
          </span>
          
          </div>
        </div>
        
          

        </div>


        <div class="col-md-6">
          <div class="row">
            <span class="mt-3 address_section">
              <label for="">Address</label>
              <input type="text" class="form-control address1" id="searchaddress"  value="" aria-describedby="emailHelp"
                placeholder="Type address" name="address">
              <div class="business search-box-info  d-none bg-white  shadow-1 position-absolute">
                <p id="address_result" class="autoCompletewrapper" style="width: 684px;"></p>

                <p id="addManually" class="autoCompletewrapper">
                 <div id="addm" style="width: 620px; margin-left: 9px;"> <span  class="m-3" style="text-overflow: ellipsis; white-space: nowrap; overflow: hidden;color: #4A4A4A;font-size: 14px;font-weight: 600;margin-left: 28px;">+add manually</span></div>
                </p>

              </div>
          </div>
        </div>

        </span>
        <div class="col-md-6 adds d-none">
          <div class="row">

            <span class="mt-3">
              <label for="">Address</label>
              <input type="text" class="form-control address" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="Address" name="address">
            </span>
          
          </div>
        </div>
        <div class="col-md-6 adds d-none">
          <div class="row">

            <span class="mt-3">
              <label for="">Address line 2</label>
              <input type="text" class="form-control address2" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="Address line 2" name="address">
            </span>
            <span class="mt-3">
              <label for="">City,state/ Province / Region</label>
              <input type="text" class="form-control city" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="City" name="address">
            </span>

          </div>
        </div>
        <div class="col-md-6 adds d-none">
          <div class="row">


            <span class="mt-3">
              <label for="">Country</label>
              <input type="text" class="form-control country" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="Country" name="address">
            </span>
            <span class="mt-3">
              <label for="">Postal code (optional)</label>
              <input type="nember" class="form-control pincode" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="Postal code" name="address">
            </span>

          </div>
          <hr>
        </div>


        <!-- <span class=" t-3 mb-3 mt-3">

          <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp"
            placeholder="Type address" name="address">
        </span> -->

        <div class="form-check mt-3 mb-3">
          <label for="">Are you an owner, employee, or official representative of this place?</label>
          <br>
          <input class="form-check-input checkedval" type="checkbox" value="1" id="flexCheckDefault">
  <label class="form-check-label" for="flexCheckDefault">
      Yes! I am a representative of this place.
  </label>

        </div>
    <h5 class="mt-3">Contact Detail</h5>

        <div class="col-md-6">
          <div class="row">


            <span class="mt-3">
              <label for="">Website(optional)</label>
              <input type="text" class="form-control website" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="http:\\" name="website">
            </span>
            <span class="mt-3">
              <label for="">Official phone (optional)</label>
              <input type="nember" class="form-control phone" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="000-000-0000" name="phone">
            </span>

          </div>

        </div>
        <div class="col-md-6">
          <div class="row">


            <span class="mt-3">
              <label for="">Email(optional)</label>
              <input type="email" class="form-control email" id="exampleInputEmail1" aria-describedby="emailHelp"
                placeholder="Official email address" name="email">
            </span>

          </div>
        </div>
    </div>
    <button class="btn btn-secondary" style="    margin-top: 20px;
    margin-left: 687px;">Submit</button>




    </form>


  </div>
  </div>
  </div>
  @include('footer')
  <!-- Button trigger modal -->

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
  
  </script>

  <script>

  function showTab(tabClass) {
    
    $('*[class^="t-"]').addClass('d-none');

    // Remove the 'selectedTab' class from all buttons
    $('*[class^="tab-"]').removeClass('selectedTab');
    $('.t').removeClass('selectedTab');
    // Show the specific tab based on the clicked button
    $('.' + tabClass).removeClass('d-none');
    $('.allbtn').addClass('tt');

    $('.' + tabClass).addClass('tt');

    // Add the 'selectedTab' class to the clicked button
    var tabButton = $('.' + tabClass.replace('t-', 'tab-'));
    tabButton.addClass('selectedTab');

    var tabButton = $('.' + tabClass.replace('t-', 'tab-'));
    tabButton.addClass('selectedTab');
 
  }
  </script>
  <script>
function selecttab(tabClass) {
    $('.allbtn').removeClass('selectedTab-btn');
    $('.' + tabClass).addClass('selectedTab-btn');
}

  </script>
  <script>
  $(document).on('keyup', '#searchaddress', function() {
    var value = $('#searchaddress').val();


    $('#address_result').html("");
    $('.business').removeClass('d-none');
    $('#addManually').removeClass('d-none');
    //   $("#business").css("display", "block");

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'searchaddress',
      data: {
        'val': value,
        '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('.business').removeClass('d-none');
        $('#address_result').html(response);
      }
    });

  })
  $(document).on('click', '#address_result li div span', function() {
    var selectedValue = $(this).text();
    $('#searchaddress').val(selectedValue);
    $('#address_result').html(""); 
  });
  </script>
  <script>
  $(document).ready(function() {
    $(document).on('click', '#searchaddress', function() {
      var value = $('#searchaddress').val();

      // Clear existing content
      $('#address_result').html("");
      $('.business').removeClass('d-none');
      $('#addManually').removeClass('d-none');
    });
  }); 
  </script>
  <script>
  $(document).on('click', '#addm', function() {
    $('#addm').html("");
    $('.adds').removeClass('d-none');    
    $('.address_section').addClass('d-none');
  })
  </script>
  <script>
  $(document).ready(function() {
    $('#submitForm').submit(function(event) {
      event.preventDefault();


     

      var address =  $('#searchaddress').val();
   

      var name = $('.name').val();
      if(name == ""){      
        $('#fname-msg').text('Name is required').css('color', 'red');
      }
    
      var address2 = $('.address2').val();
      if (address2 !== "") {        
      var city = $('.city').val();
      var pincode = $('.pincode').val();
      var country = $('.country').val(); 
      var fulladdress = address + ', ' + address2 + ', ' + pincode + ', ' + country;
      alert(fulladdress);
    }
    if(address == "" || address2 ==""){      
        $('#address-msg').text('Address is required').css('color', 'red');
    }
    var email = $('.email').val();
    var phone = $('.phone').val();
    var website = $('.website').val();
 
    var checkedval = $('.checkedval:checked').val();


    var selectedTab = $('.selectedTab').text();

    if (selectedTab === 'Accommodation') {
        var catVal = $('.selectedTab-btn').text();
    } else if (selectedTab == 'Things to do') {
       var catVal = $('.thingval').val();
    } else if (selectedTab == 'Restaurant') {
       var catVal = $('.restval').val();
    } 

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'save_business',
      data: {'name':name,'address':address,'email':email,'phone':phone,'website':website,'cattype':selectedTab,'catVal':catVal,'repreval':checkedval},
      success: function(response) {
   
      },
      error: function(xhr, status, error) {
        // Handle the error response from the server
        console.error(xhr.responseText);
      }
    });
    });

  });
  </script>
  <script>
  $(document).ready(function() {
    // Function to hide address_result on click outside
    function hideAddressResultOnClickOutside() {
        $(document).mousedown(function(e) {
            var addressResultContainer = $('#address_result');

            addressResultContainer.addClass('d-none')
       
        });

        // Stop event propagation when clicking inside address_result
        addressResultContainer.mousedown(function(e) {
            e.stopPropagation();
        });
    }

    // Call the function
    hideAddressResultOnClickOutside();
    
    // Example: Show address_result when #searchaddress is clicked
    $('#searchaddress').click(function() {
        $('#address_result').removeClass('d-none');
    });
});


  </script>
  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <!-- <script src="{{ asset('/js/custom.js')}}"></script> -->
  <!-- end nav js -->


</body>

</html>