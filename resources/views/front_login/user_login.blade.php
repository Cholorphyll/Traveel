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
  <title>Login	 - Travell</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
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
  @include('Loc_nav.loc_navbar')


  <div class="">
    <div class="container">


      <div class="d-flex flex-column-reverse flex-md-column">
        <div class="banner-container other-sections">
          <img src="{{asset('images/homepagebanner.png')}}" class="img-fluid d-block w-100" alt="">
          <div class="banner-text fw-500">
            Discover New Destinations
          </div>
        </div>
      </div>

      <div class="aboutustext " style="margin-top: -209px;">
        
      
        @if(session()->has('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="subtext" style="text-align: center;">
          Login 
        </div>
        <form action="{{ route('userlongin') }}" method="post" class="center-form mb-5">
          @csrf
          
          <div class="col-md-10">

       
            <div class="col-md-6 center-col mt-3">  
              <span><b>Email</b></span>
              <input type="text" name="email" class="form-control" placeholder="Email" autocomplete="off" required>
              @error('email')
              <span class="text-danger">{{ $message }}</span>
              @enderror
            </div>
            <div class="col-md-6 center-col mt-3">
              <span><b>Password</b></span>
         
              <div style="position:relative;">
                 <input type="password" name="password" class="form-control" placeholder="Password" autocomplete="off" required id="myInput">
                 <i class="far fa-eye" id="togglePassword" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); cursor:pointer;" onclick="myFunction()"></i>
               </div>
              @error('password')
              <span class="text-danger">{{ $message }}</span>
              @enderror
              <h6 class=" mt-3"><a href="{{route('forgot_password')}}">Forgot Password?</a></h6></span>
            </div>
            <div class="col-md-2 mt-3 center-col">
              <button type="submit" class="form-control" style="background: #CB4C14;color: white;width: 190px;
        margin-left: 164px;">Submit</button>
            </div>
          </div>
        </form>



      </div>



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
    <script src="{{ asset('/js/sign_in.js')}}"></script>
    <!-- end nav js -->
   

</body>

</html>