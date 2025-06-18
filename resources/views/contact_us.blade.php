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
  <title>Contact Us - Travell</title>
  <meta name="description" content="Reach out to us at Travell. We're always here to provide you the best solutions." />
  
  <!-- Favicon -->
  <link rel="icon" type="image/x-icon" href="{{asset('/favicon.ico')}}">
  
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  
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

  <!-- FontAwesome -->
  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>

  <!-- Custom CSS -->
  <link rel="stylesheet" href="{{ asset('/css/style.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css') }}">
  <link rel="stylesheet" href="{{ asset('/css/contctform.css') }}">
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
    .recent-his {
        margin-top: 53px;
    }

    /* Styles for the thank you message */
    #thankYouMessage {
        display: none;
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
   <script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
	{
  	"@type": "WebPage",
  	"@id": "https://www.travell.co/contact-us",
  	"name": "Contact Us | Travell",
  	"url": "https://www.travell.co/contact-us",
  	"inLanguage": "en",
  	"isPartOf": {
    	"@id": "https://www.travell.co/#website"
  	},
  	"mainEntity": {
    	"@id": "https://www.travell.co/#organization"
  	},
  	"description": "Get in touch with the Travell team for support, partnerships, or general inquiries. We'd love to hear from you."
	},
	{
  	"@type": "Organization",
  	"@id": "https://www.travell.co/#organization",
  	"name": "Travell",
  	"url": "https://www.travell.co",
  	"logo": {
    	"@type": "ImageObject",
    	"url": "https://www.travell.co/logo.png",
    	"width": 200,
    	"height": 60
  	},
  	"contactPoint": {
    	"@type": "ContactPoint",
    	"contactType": "Customer Support",
    	"email": "Travell.co@outlook.in",
    	"url": "https://www.travell.co/contact-us"
  	},
  	"sameAs": [
    	"https://www.facebook.com/mytravellco",
	"https://x.com/wwwTravellco",
	"https://www.instagram.com/wwwtravellco",
  	"https://www.linkedin.com/company/mytravell",
	"https://www.f6s.com/company/travell.co",
	"https://twitter.com/wwwTravellco",
	"https://www.crunchbase.com/organization/tripalong"
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
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@include('frontend.header')
<div class="contatform">
        <div class="container">
            
            <div class="row">
                
                <div class="col-md-6">
                    <div class="contact-left-column">
                        <p>Couldn’t find what you were looking for?</p>
                        <div>We’re here for you!</div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="contact-form">
                     <form id="contactForm" action="{{ route('contact.send') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-6">
                                    <input type="text" name="name" placeholder="Enter Your Name*" required>
                                </div>
                                <div class="col-6">
                                    <input type="text" name="phone" placeholder="Enter your Phone number*" required>
                                </div>
                                <div class="col-6">
                                    <input type="email" name="email" placeholder="Enter your mail ID*" required>
                                </div>
                                <div class="col-6">
                                    <input type="text" name="location" placeholder="Select your location*" required>
                                </div>
                                <div class="col-12">
                                    <textarea name="message" placeholder="How can we help you get your good out there?" required></textarea>
                                </div>
                            </div>
                             <button type="submit" onclick="showThankYouMessage(event)">Submit <img src="images/arrow-right.svg" alt=""></button>
                        </form>
						 <div id="thankYouMessage" class="mt-4" style="display: none;">
                            <div class="alert alert-success" role="alert">
                                Thank you! Your message has been sent successfully. We will get back to you shortly.
                            </div>
                        </div>
                    </div>                     
                </div>
            </div>
           <!--BREADCRUMB - START-->
        <div class="tr-breadcrumb-section" style="margin-top: 0;">
        <ul class="tr-breadcrumb">
            <li><a href="https://www.travell.co">Travell</a></li>
            <li>Contact Us</li>
        </ul>
    </div>
        <!--BREADCRUMB - END-->              
        </div>	 		
    </div>
    @include('footer')

<script>
    function showThankYouMessage(event) {
        event.preventDefault(); // Prevent the default form submission behavior
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Submit the form data via AJAX
        $.ajax({
            url: $('#contactForm').attr('action'),
            type: 'POST',
            data: $('#contactForm').serialize(),
            success: function(response) {
                // Show thank you message
                document.getElementById('thankYouMessage').style.display = 'block';
                document.getElementById('contactForm').reset(); // Reset the form fields
            },
            error: function(xhr) {
                // Handle error
                alert('There was an error sending your message. Please try again later.');
            }
        });
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

<!-- Scripts - Consolidated -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('/css/hotel-css/t-datepicker.min.js') }}"></script>
<script src="{{ asset('/js/datepicker-homepage.js') }}"></script>
<script src="{{ asset('/js/custom.js') }}"></script>
<script src="{{ asset('/js/header.js')}}"></script>
<script src="{{ asset('/js/restaurant.js')}}"></script>
<script src="{{ asset('/frontend/hotel-detail/js/common.js')}}"></script>
<script src="{{ asset('/frontend/hotel-detail/js/custom.js')}}"></script>

</body>

</html>
