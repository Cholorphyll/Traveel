<!doctype html>
<html lang="en">

<head>
    <title>About Travell</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('/favicon.ico') }}">

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
        /* Custom styles applied only to the main content, excluding navbar */
        .main-content h2,
        .main-content h4  {
            font-size: 26px;
            margin-top: 40px;
            margin-bottom: 20px;
            font-weight: bold;
            color: #000000;
            text-align: left;
        }


        .main-content p,{
            font-size: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
            line-height: 1.9;
            color: #000000;
        }

        .main-content ul {

    margin-top: 20px;
    margin-bottom: 20px;
    list-style-type: disc;
    margin-left: 20px;
    color: #000000;
}


.main-content li {
    list-style-position: inside;
    padding-left: 0;
    text-indent: -1.5em;
    font-size: 1.1em;
    margin: 0; /* Remove margin to eliminate space between bullet points */
    line-height: 1.6;
}


.main-content li:before {
    content: '• ';
    margin-left: 0;
}
.main-content h2 {
    text-align: left !important;
    margin-left: 0;
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
.tr-terms-and-conditions-section h2 {
    text-align: left;
    margin-left: 0;
    padding-left: 0;
}

.tr-terms-and-conditions-section {
    padding-left: 0;
    padding-right: 0;
}

    </style>
    <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
	{
  	"@type": "AboutPage",
  	"@id": "https://www.travell.co/about-us",
  	"name": "About Travell",
  	"url": "https://www.travell.co/about-us",
  	"isPartOf": {
    	"@id": "https://www.travell.co/#website"
  	},
  	"mainEntity": {
    	"@id": "https://www.travell.co/#organization"
  	},
  	"description": "Learn more about Travell, your ultimate travel planning companion. We help travelers find top-rated hotels, attractions, restaurants, and create customized itineraries effortlessly."
	},
	{
  	"@type": "Organization",
  	"@id": "https://www.travell.co/#organization",
  	"name": "Travell",
  	"url": "https://www.travell.co",
  	"logo": {
    	"@type": "ImageObject",
    	"url": "https://www.travell.co/images/logo.png",
    	"width": 200,
    	"height": 60
  	},
  	"sameAs": [
    	"https://www.facebook.com/mytravellco",
	"https://x.com/wwwTravellco",
	"https://www.instagram.com/wwwtravellco",
  	"https://www.linkedin.com/company/mytravell",
	"https://www.f6s.com/company/travell.co",
	"https://twitter.com/wwwTravellco",
	"https://www.crunchbase.com/organization/travell"
  	]
	},
	{
  	"@type": "WebSite",
  	"@id": "https://www.travell.co/#website",
  	"name": "Travell",
  	"url": "https://www.travell.co",
  	"publisher": {
    	"@id": "https://www.travell.co/#organization"
  	}
	}
  ]
}
</script>
</head>

<body>
    <!-- Include Navbar -->
    @include('frontend.header')

    <!-- Mobile Navigation-->
    <div class="tr-mobile-nav-section">
      <div class="tr-mobile-nav-content">
        <button type="button" class="btn-nav-close" id=""></button>
        <div class="tr-nav-header">
          <div class="tr-logo">
            <img src="images/travell-small-logo.png" alt="travell small logo">
          </div>
          <div class="tr-location">London</div>
        </div>
        <div class="tr-mobile-nav-lists">
          <ul>
            <li><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 7.49984L10 1.6665L17.5 7.49984V16.6665C17.5 17.1085 17.3244 17.5325 17.0118 17.845C16.6993 18.1576 16.2754 18.3332 15.8333 18.3332H4.16667C3.72464 18.3332 3.30072 18.1576 2.98816 17.845C2.67559 17.5325 2.5 17.1085 2.5 16.6665V7.49984Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 18.3333V10H12.5V18.3333" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/></svg>Explore</li>
            <li><svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M2.5 7.49984L10 1.6665L17.5 7.49984V16.6665C17.5 17.1085 17.3244 17.5325 17.0118 17.845C16.6993 18.1576 16.2754 18.3332 15.8333 18.3332H4.16667C3.72464 18.3332 3.30072 18.1576 2.98816 17.845C2.67559 17.5325 2.5 17.1085 2.5 16.6665V7.49984Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 18.3333V10H12.5V18.3333" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/></svg>Hotels</li>
            <li><svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.6677 8.21686L11.4782 8.25293L11.4075 8.43241L8.58835 15.5894L7.6097 15.7757L8.3748 9.30726L8.4309 8.83302L7.96178 8.92232L3.5739 9.75759L3.37156 9.79611L3.30699 9.99171L2.47522 12.5116L1.87823 12.6253L1.98228 9.2217L1.98466 9.14395L1.95388 9.07252L0.606627 5.94522L1.20367 5.83157L2.90392 7.86957L3.03583 8.02769L3.23812 7.98919L7.626 7.15392L8.09517 7.0646L7.86869 6.64412L4.77982 0.909331L5.75841 0.723048L11.0099 6.34373L11.1416 6.48469L11.3311 6.44861L15.7902 5.59979C16.0247 5.55515 16.2673 5.60549 16.4647 5.73973L16.6615 5.45033L16.4647 5.73973C16.6621 5.87398 16.798 6.08113 16.8426 6.31561C16.8873 6.55009 16.8369 6.79271 16.7027 6.99007L16.9921 7.18692L16.7027 6.99007C16.5685 7.18744 16.3613 7.3234 16.1268 7.36803L11.6677 8.21686Z" stroke="black" stroke-width="0.7"></path></svg>Flights</li>
            <li><svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.0835 9.43359H14.8335M3.58349 9.43359H5.83349M17.4116 5.68359L15.9485 1.78191C15.7289 1.19645 15.1693 0.808594 14.544 0.808594H6.12299C5.49773 0.808594 4.93804 1.19645 4.7185 1.78191L3.25537 5.68359M17.4116 5.68359L17.6299 6.26568C17.7524 6.59225 18.0646 6.80859 18.4133 6.80859C18.9068 6.80859 19.293 7.23341 19.2463 7.72462L18.8335 12.0586M17.4116 5.68359H18.9585M3.25537 5.68359L3.03708 6.26568C2.91462 6.59225 2.60243 6.80859 2.25366 6.80859C1.76023 6.80859 1.37395 7.23341 1.42073 7.72462L1.83349 12.0586M3.25537 5.68359H1.70849M1.83349 12.0586L1.95418 13.3258C2.0275 14.0957 2.67408 14.6836 3.44742 14.6836H3.5835M1.83349 12.0586V12.0586C1.55735 12.0586 1.3335 12.2824 1.3335 12.5586V15.0586C1.3335 15.4728 1.66928 15.8086 2.0835 15.8086H2.8335C3.24771 15.8086 3.5835 15.4728 3.5835 15.0586V14.6836M3.5835 14.6836H17.0835M17.0835 14.6836H17.2196C17.9929 14.6836 18.6395 14.0957 18.7128 13.3258L18.8335 12.0586M17.0835 14.6836V15.0586C17.0835 15.4728 17.4193 15.8086 17.8335 15.8086H18.5835C18.9977 15.8086 19.3335 15.4728 19.3335 15.0586V12.5586C19.3335 12.2825 19.1096 12.0586 18.8335 12.0586V12.0586M6.24161 3.33425L5.41255 5.82142C5.25067 6.30707 5.61214 6.80859 6.12406 6.80859H14.544C15.0548 6.80859 15.4163 6.30707 15.2544 5.82142L14.4254 3.33425C14.2212 2.72174 13.648 2.68359 13.0024 2.68359H7.66463C7.01899 2.68359 6.44578 2.72174 6.24161 3.33425Z" stroke="black" stroke-width="0.7" stroke-linecap="round" stroke-linejoin="round"></path></svg>Cars</li>
          </ul>
        </div>
        <div class="tr-mobile-nav-lists">
          <ul>
            <li><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6719 6.14307H3.33854C2.41807 6.14307 1.67188 6.88926 1.67188 7.80973V16.1431C1.67188 17.0635 2.41807 17.8097 3.33854 17.8097H16.6719C17.5923 17.8097 18.3385 17.0635 18.3385 16.1431V7.80973C18.3385 6.88926 17.5923 6.14307 16.6719 6.14307Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.3385 17.8091V4.47575C13.3385 4.03372 13.1629 3.6098 12.8504 3.29724C12.5378 2.98468 12.1139 2.80908 11.6719 2.80908H8.33854C7.89651 2.80908 7.47259 2.98468 7.16003 3.29724C6.84747 3.6098 6.67188 4.03372 6.67188 4.47575V17.8091" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path></svg>Write a review</li>
            <li><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M16.6719 6.14307H3.33854C2.41807 6.14307 1.67188 6.88926 1.67188 7.80973V16.1431C1.67188 17.0635 2.41807 17.8097 3.33854 17.8097H16.6719C17.5923 17.8097 18.3385 17.0635 18.3385 16.1431V7.80973C18.3385 6.88926 17.5923 6.14307 16.6719 6.14307Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path><path d="M13.3385 17.8091V4.47575C13.3385 4.03372 13.1629 3.6098 12.8504 3.29724C12.5378 2.98468 12.1139 2.80908 11.6719 2.80908H8.33854C7.89651 2.80908 7.47259 2.98468 7.16003 3.29724C6.84747 3.6098 6.67188 4.03372 6.67188 4.47575V17.8091" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path></svg>Trips</li>
          </ul>
        </div>
        <div class="tr-mobile-nav-lists">
          <h4>Company</h4>
          <ul>
            <li><a href="javascript:void(0);">About us</a></li>
            <li><a href="javascript:void(0);">Contact us</a></li>
            <li><a href="javascript:void(0);">Traveller’s Choice</a></li>
            <li><a href="javascript:void(0);">Travel stories</a></li>
            <li><a href="javascript:void(0);">Help</a></li>
          </ul>
        </div>
        <div class="tr-actions">
          <button class="tr-btn tr-write-review">Sign up / Log in</button>
        </div>
      </div>
    </div>

    <div class="container">
      <div class="row">
        <div class="col-sm-12">
          <!--Terms and Conditions - START-->
          <div class="tr-single-page">
            <div class="tr-terms-and-conditions-section">
            <h1>About Us - Travell</h1>

<p><strong>Hey there!</strong></p>

<p>Ok so picture this - it's 2:37 AM, you've got like 16 tabs open, your coffee's gone cold for the third time, and you're <span class="highlight">THIS close</span> to giving up on finding a decent hotel deal. Been there? Yup, same. That's literally how Travell happened.</p>

<h2>The Whole Messy Story:</h2>

<p>Not gonna lie, we never actually planned to start a company. Seriously. We were just a bunch of travel-obsessed friends who got <span class="highlight">REALLY</span> annoyed with the whole hotel booking nightmare. You know what I'm talking about - that thing where you find what seems like a great deal, then your friend books the exact same room for way less? Yeah. That.</p>

<p>&#x27A3; <strong>Fun fact:</strong> The first version of Travell was sketched out on the back of a pizza box. Pretty sure there were grease stains on our initial code... but that's another story for another day.</p>

<h2>The Breaking Point:</h2>

<p>So there we were in Bali (sounds fancy, but we got cheap tickets). Five of us, same hotel, same rooms, FIVE different prices. Like... how?! Our tech guru just lost it. Started ranting about algorithms and price aggregation right there in the lobby. The poor receptionist had no idea what was happening. That's where the real work started.</p>

<h2>How We Actually Fix This Mess:</h2>

<p><strong>Warning:</strong> This part might sound techy but I promise it's worth reading!</p>

<p>We built this crazy-huge system that basically stalks hotel prices (in a totally legal way, don't worry). It's like having thousands of friends constantly checking every booking site, deal forum, and hotel chain but way less annoying.</p>

<p>Our price-finding machine does this:</p>
<ul>
    <li>Stalks <span class="highlight">ALL</span> the booking sites (like, literally all of them)</li>
    <li>Finds every single price for every room</li>
    <li>Catches those sneaky extra fees they try to hide</li>
    <li>Spots those random 3AM flash deals you'd usually miss</li>
    <li>Double checks everything cause trust issues, ya know?</li>
</ul>

<p>Here's the thing tho - we don't play favorites <em>‘At All’</em>. Other sites are like "oh this hotel pays us more so let's show it first" - nope. Not here. Cheapest price wins, period. Even if it means we make less money.</p>

<h2>Numbers & Stuff:</h2>

<p><strong>Real talk</strong> - people save an average of 4-5 hours NOT having to compare prices everywhere. That's like an entire Lord of the Rings movie! Or like 5 episodes of whatever you're binging right now.</p>

<p>Some wild stuff we've seen:</p>
<ul>
    <li>Most people save around 15-30% (sometimes way more if you're lucky)</li>
    <li>The biggest price difference we've found was $400... FOR THE SAME ROOM. Still not over it.</li>
    <li>This one couple saved $1,247 on their honeymoon suite in the Maldives (they sent us chocolates after)</li>
</ul>

<h2>Money Stuff- Keeping it Real</h2>

<p>Look, we gotta keep the lights on. We make small commissions when you book. But here's the deal - if we find you a cheaper price on a site that pays us zero commission we're still gonna show you that price. Our financial advisor says this is dumb. We say this is honest.</p>

<h2>Talk To Us!</h2>

<p>Seriously, we love hearing from you guys. Even if it's just to share your hotel booking horror stories (we collect those like trading cards now). Our support team is actually just real humans who happen to know their stuff. Fair warning tho - they tell <span class="highlight">TERRIBLE</span> travel jokes. Like, dad-joke level bad.</p>

<h2>Bottom Line?</h2>

<p>Nobody should need a PhD in tab management just to book a hotel room. We're here to fix that. Try us out - worst case scenario, you spend 30 seconds searching. Best case? You save enough money for an extra day of vacation. Or lots of room service. We don't judge.</p>

<p>Welcome to Travell - because somebody had to fix this mess, might as well be us!</p>

<p><strong>P.S.</strong> - Still looking for that pizza box with our original sketches. Pretty sure it's behind a filing cabinet somewhere...</p>
            </div>
          </div>
          <!--Terms and Conditions - END-->
 		<!--BREADCRUMB - START-->
        <div class="tr-breadcrumb-section">
          <ul class="tr-breadcrumb">
            <li><a href="https://www.travell.co">Travell</a></li>
            <li>About Us</a></li>
          </ul>
        </div>
        <!--BREADCRUMB - END-->
        </div>
      </div>
    </div>

    <!--FOOTER-->
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
