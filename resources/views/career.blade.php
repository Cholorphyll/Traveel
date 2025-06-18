<!doctype html>
<html lang="en">

<head>
	<!-- Google Tag Manager -->
  <meta name="csrf-token" content="{{ csrf_token() }}">
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PTHP3JH4');</script>
<!-- End Google Tag Manager -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Find Your Dream Career - Travell</title>
  <meta name="description" content="Looking for your dream job in travel? Learn how you can find it at Travell and be a part of an amazing team." />
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{asset('/favicon.ico')}}">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  
  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>
  
  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Slick Carousel CSS -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" />

  <!-- Datepicker CSS -->
  <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}" media="screen">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}">
  <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}">

  <!-- Additional Hotel Styles -->
  <link href="{{ asset('/css/hotel-css/autoComplete.min.css') }}" rel="stylesheet">
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css') }}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/cookie-consent.css') }}">
  <script src="{{ asset('js/cookie-consent.js') }}"></script>
  <!-- jQuery UI and Bootstrap JS -->
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}"></script>
  <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/slick.min.js')}}"></script>

  <style>
    .recent-his{
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
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@include('frontend.header')

  <div class="">
    <div class="container">


      <!--<div class="d-flex flex-column-reverse flex-md-column">
        <div class="banner-container other-sections">
          <img src="{{asset('/images/homepagebanner.png')}}" class="img-fluid d-block w-100" alt="">
          <div class="banner-text fw-500">
            Discover New Destinations
          </div>
        </div>
      </div>

     <!-- <div class="aboutustext">
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
        <div class="col-md-12" style="margin-top: 50px;">
          <div class="fs-32 mb-32"><b>TRAVEL MADE -EASY, FUN & MEMORABLE @Travell
            </b></div>

          <p>At Travell, we strive to connect individuals with experiences that bring them joy. Whether you're planning a
            staycation, weekend getaway, or dream vacation, we are dedicated to assisting you. Our team works tirelessly
            to bring travellers, businesses, and communities together from all corners of the globe.
          </p>
          <p class="mt-3">
            The best part of working at Travell is the freedom to explore your career. You can mould your role and push
            boundaries, making a significant impact on our company and the industry as a whole. We believe in change and
            remain committed to connecting people with unforgettable experiences.
          </p>

          <p class="mt-3">
            Ready to join us? At Travell, we hold diversity and inclusivity in high regard. We are committed to providing
            equal employment opportunity and non-discrimination to all employees and qualified applicants.
          </p>
          <p class="mt-3">
            Your race, colour, gender identity or expression, age, religion, national origin, ancestry, ethnicity,
            disability, veteran status, genetic information, sexual orientation, marital status, or any other
            characteristic protected under applicable law is of no concern to us. We believe that everyone deserves an
            equal opportunity to achieve their dreams and make a difference.

          </p>
          <p class="mt-3">
            Travell is proud to be an equal-opportunity employer and committed to offering unwavering support and
            accommodations to all applicants throughout the recruitment process. If, due to a medical condition or
            disability, you require reasonable accommodation, please contact your recruiter or send an email to <a href="mailto:Travell.co@outlook.in">Travell.co@outlook.in</a>
            with the job requisition number mentioned in your message.

          </p>
          <p class="mt-3">
            At Travell, we are on a mission to bring people, passions, and places together to create exceptional
            travellers. We aim to empower millions of travellers with the confidence to explore and make a positive
            impact on the world.

          </p>
          <p class="mt-3">
            Working at Travell is an experience like no other, offering exposure to diverse operations, work methods,
            cultures, and limitless opportunities for professional and personal growth. Each day presents a fresh chance
            to expand your skill set and make a meaningful difference.

          </p>


          <p class="mt-3">
            Travell is seeking individuals who share their passion for travel and empowering others to explore the world.
            Our team is made up of accessible leaders and passionate travellers from all over the globe, whether they
            work in one of our international offices or from home.


          </p>
          <p class="mt-3">
            If you're interested in being part of building the future of travel and getting more people out into the
            world, then joining us might be the perfect opportunity for you. At Travell, we recognize that hiring people
            from diverse backgrounds, with unique experiences, perspectives, and ideas, is essential to innovation and
            delivering great experiences for our users and partners.

          </p>
          <p class="mt-3">
            We firmly believe that travel should be accessible to everyone. Our platform is designed to empower people
            from all corners of the globe to share their unique spaces, talents, and passions with travellers seeking
            the ultimate trip.
          </p>
          <p class="mt-3">
            As we continue to expand into new territories and technologies, we remain steadfast in our commitment to our
            core values and our aim to create a stunning and all-encompassing experience for everyone.

          </p>
          

          <p class="mt-3">
          We constantly strive to overcome challenges in areas such as search relevance, payments, fraud prevention, and discrimination, while contributing to the community through various projects such as our work on testing React components and the development of our new Android framework. 
          </p>
          <p class="mt-3">
          If you share our enthusiasm for travel and our dedication to diversity and inclusion, we encourage you to join us in revolutionizing the travel industry. Get in touch with us today to discover more. At Travell, we value a workplace that fosters inclusivity and encourages individuals to showcase their best work. 
          </p>
          <p class="mt-3">
          We acknowledge the significance of diversity and welcome applicants from underrepresented backgrounds in the tech industry. Our dedicated team focuses on engineering and product development, which includes engaging with users, prioritizing tasks, designing, and continuously building and delivering. Our founders' experience at esteemed companies enables us to incorporate the best practices from each of these organizations into Travell. 
          </p>

          <p class="mt-3">
          Join our team and be part of revolutionizing the travel industry. I couldn't agree more that work-life balance is crucial for a workplace to thrive. It's incredibly refreshing to hear that Travell is prioritizing the well-being of its employees by implementing regular hours, team lunches, exercise, and quality time with loved ones.
          </p>
          <p class="mt-3">
          As someone who shares a passion for travel, I find it fantastic that Travell organizes semiannual offsites to explore new places and enjoy the fruits of their labour. Travell is fully committed to fostering a positive and inclusive work environment that promotes both personal and professional growth. 



          </p>

        </div>
 		<!--BREADCRUMB - START-->
        <div class="tr-breadcrumb-section">
          <ul class="tr-breadcrumb">
            <li><a href="https://www.travell.co">Travell</a></li>
            <li>Careers</a></li>
          </ul>
        </div>
        <!--BREADCRUMB - END-->
      </div>
    </div>

  </div>

  @include('footer')

</body>

</html>

<!-- Scripts - Consolidated -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('/css/hotel-css/t-datepicker.min.js') }}"></script>
<script src="{{ asset('/js/datepicker-homepage.js') }}"></script>
<script src="{{ asset('/js/custom.js') }}"></script>
<script src="{{ asset('/js/header.js')}}"></script>
<script src="{{ asset('/js/restaurant.js')}}"></script>
<script src="{{ asset('/frontend/hotel-detail/js/common.js')}}"></script>
<script src="{{ asset('/frontend/hotel-detail/js/custom.js')}}"></script>

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
        
        // If clicked outside, close everything but don't prevent default behavior
        if (clickedOutside) {
            // Close elements without affecting page scrolling
            setTimeout(function() {
                $('.tr-find-hotels').removeClass('open show');
                $('.tr-guests-modal').removeClass('show');
                $('.tr-calenders-modal').removeClass('show');
                $('.tr-search-info-section').removeClass('hide');
                $('.tr-nav-tabs').removeClass('show');
                
                // Ensure body is scrollable
                $('body').css({
                    'overflow': 'auto',
                    'position': 'static'
                });
            }, 0);
        }
    });
    
    // Ensure body is always scrollable when document is ready
    $('body').css({
        'overflow': 'auto',
        'position': 'static'
    });
});
</script>
