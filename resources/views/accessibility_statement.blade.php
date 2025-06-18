<!doctype html>
<html lang="en">

<head>
	<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PTHP3JH4');</script>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Accessibility Statement - Travell</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!--  fontawesome -->
  <link rel="icon" type="image/x-icon" href="{{asset('/favicon.ico')}}">
  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>

  <!-- nav css -->
  <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
   <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">

 
   <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> 
   <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


     <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}"> 
        <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
   
      <style>
        .recent-his{
          margin-top: 53px;
        }
           
      </style>
   <!-- end nav css -->

</head>

<body>
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@include('Loc_nav.loc_navbar')




  <div class="py-5">
    <div class="container">


      <div class="d-flex flex-column-reverse flex-md-column">
        <div class="banner-container other-sections">
          <img src="{{asset('/images/homepagebanner.png')}}" class="img-fluid d-block w-100" alt="">
          <div class="banner-text fw-500">
            Discover New Destinations
          </div>
        </div>
      </div>

      <!--<div class="aboutustext">
        <div class="tnctext d-flex justify-content-center">
          <div class="tnc"><a href="#" class="text-decoration-none"> About Travell</a></div>
          <div class="tnc"><a href="#" class="text-decoration-none"> Investor Relations</a></div>
          <div class="tnc"><a href="#" class="text-decoration-none"> Resources</a></div>
          <div class="tnc"><a href="#" class="text-decoration-none"> Contact Us</a></div>
        </div>
      </div>-->



      <div class="row justify-content-between mt-md-64px">
        <!--<div class="col-md-3">
          <div class="tnc-left">
            <a href="#">Logo and Guidelines</a>
            <a href="#">Equity, Diversity + Inclusion</a>
            <a href="#">Social Impact</a>
            <a href="{{ route('trust_and_safety') }}">Trust & Safety</a>
            <a href="#">Case Studies</a>
            <a href="#">Business Marketing Tool</a>
          </div>
        </div>-->
        <div class="col-md-12">
          <div class="fs-32 mb-32"><b>Accessibility Statement
            </b></div>

          <p>At Travell, inclusivity is a top priority when it comes to our platform. Our team strives to incorporate
            accessibility into every aspect of our design and development process. Your input is valuable to us, so
			  please don't hesitate to reach out via email at <a href="mailto:info@travell.co">info@travell.co</a> with any feedback or issues you may have. Our unwavering commitment is to ensure that you have the most exceptional experience possible.
          </p>
                  

        </div>

      </div>
    </div>

  </div>

  @include('footer')

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- nav js -->
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script> 
  <script src="{{ asset('/js/custom.js')}}"></script>
 <!-- end nav js -->

</body>

</html>