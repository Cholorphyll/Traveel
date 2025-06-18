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


  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <link rel="stylesheet" href="{{ asset('/css/business_index.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/business.css')}}">
  <!-- end nav css -->
  <style>
  input[type="file"] {
    display: none;
  }

  .custom-button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 37%;
  }

  .photo-desc {
    text-align: center;
  }

  .custom-buttons {
    background-color: lightgray;
    border: 0px;
    border-radius: 5px;
    padding: 15px;
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

  @include('Loc_nav.business_nav_bar')

  <div class="">
    <div class="container">
      <div class="aboutustext mt-5">
        <div class="row">
        <h4 style="color: #00aa6c;text-align: center;">Verify Phone</h4>
        <hr>
              <p style="color: #00aa6c;text-align: center;">Phone: {{$phone}}</p>
              <p style="color: #00aa6c;text-align: center;">OTP 
              <div class="col-md-3">
                <form action="" id="submit_otp">
                <input type="text" class="form-control otp" placeholder="OTP" name="otp"  style=" margin-left: 371px;" >
                <div class="otp-msg" style=" margin-left: 371px;" ></div>
                <button class="form-control btn btn-success mt-3" style=" margin-left: 371px;">Submit</button>
                </form>
              </p></div>
            
          </div>
          


        </div>

        <hr>
        
      </div>
    </div>

    @include('footer')
    <!-- Button trigger modal -->



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script> -->

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


    <script>
    document.getElementById('fileInput').addEventListener('change', function() {
      var fileInput = this;
      var fileName = fileInput.value.split('\\').pop(); // Get the file name without the path
      document.getElementById('fileName').innerText = 'Selected Image: ' + fileName;

      // Display the selected image
      var previewImage = document.getElementById('previewImage');
      if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          previewImage.src = e.target.result;
          previewImage.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
      }
    });
    </script>

    <!-- 

    <script src = "{{ asset('/css/hotel-css/t-datepicker.min.js')}}" > -->
    <!-- </script>
    <script src="{{ asset('/js/datepicker-homepage.js')}}"></script> -->
    <script src="{{ asset('/js/custom.js')}}"></script>
    <script src="{{ asset('/js/business.js')}}"></script>
    <!-- end nav js -->


</body>

</html>