<!doctype html>
<html lang="en">

<head>
<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-PTHP3JH4');</script>
<!-- End Google Tag Manager -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Hotels - Travell (2025)</title>
	<meta name="description"
        content="Find popular hotels at the cheapest prices at your favorite destinations. Discover and create amazing travel experiences with Travell with a comforting stay.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!--  fontawesome -->

  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick-theme.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.css" rel="stylesheet" />

  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />


  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">

  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">

  <link rel="stylesheet" href="{{ asset('/css/hotel_listing.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
</head>

<body>
	<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
  @include('navigation', ['disabled' => true])
  <div class="banner-container other-sections mt-4">
    <div class="container position-relative">
      <img src="{{ asset('/images/homepagebanner.png') }}" class="img-fluid d-none d-md-block w-100" alt="">
      <img src="{{ asset('/images/bannermobile.pn') }}g" class="img-fluid d-md-none w-100" alt="">

      <div class="banner-text">
        Find and compare hotel <br> deals.
      </div>
    </div>
  </div>
  <div class="body typeahed mx-auto" align="center">
    <div class="d-flex justify-content-center mb-20">


      <ul class="nav nav-tabs border-0 w-100 justify-content-center w-50" id="myTab" role="tablist">
        <li class="nav-item border-0 w-50" role="presentation">
          <button class="nav-link  tab-switch" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane"
            type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true"><span class="tab-switch ">
              Explore
            </span></button>
        </li>
        <li class="nav-item border-0 w-50" role="presentation">
          <button class="nav-link active tab-switch" id="profile-tab" data-bs-toggle="tab"
            data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane"
            aria-selected="false">Hotels</button>
        </li>
      </ul>



    </div>
    <div class="tab-content" id="myTabContent">
      <div class="tab-pane fade " id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
        <div class="explore-search">
          <div class="search-box-icon">
            <img src="{{ asset('/images/search.svg') }}" class="search-icon" alt="">
          </div>

          <input type="text" id="searchlocation" type="search" value="{{request('search')}}" name="search"
            placeholder="Where are you goining?" autocomplete="off">
          <div class="recent-his search-box-info  d-none bg-white px-0 px-md-4 b-20 shadow-1 position-absolute">
            <!-- <p class="small my-3" id="recent-search">@if (Session::has('lastsearch')) RECENTLY VIEWED @else POPULAR
                  DESTINATIONS @endif</p> -->
            <p id="loc-list" class="px-0 px-md-4 autoCompletewrapper"></p>
          </div>
        </div>

      </div>






      <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab"
        tabindex="0">
        <div class="search-filter remove-highlight d-md-flex">

          <div class="search-locations">
            <img src="{{ asset('/images/search.svg') }}" class="search-iconformobile d-md-none" alt="">
            <label for="checkout" class="label">Where</label>
            <div class="autoComplete_wrapper">
              <input id="searchhotel" type="text" tabindex="1" placeholder="&#xF002; Search">
              <div class="hotel_recent_his search-box-info  d-none bg-white px-0 px-md-4 b-20 shadow-1 position-absolute">
                <!-- <p class="small my-3" id="recent-search">@if (Session::has('lastsearch')) RECENTLY VIEWED @else POPULAR
                  DESTINATIONS @endif</p> -->
                <p id="hotel_loc_list" class="px-0 px-md-4 autoCompletewrapper"></p>
              </div>
              <span  id="slug" class="d-none"></span> 
               <span  id="location_id" class="d-none"></span>
            </div>
          </div>

          <div class="t-datepicker">
            <div class="t-check-in"></div>
            <div class="t-check-out"></div>
          </div>

          <div class="dropdown-custom">

            <div class="dropdown-custom-toggle ">
              <img src="{{ asset('/images/usergrey.png')}}" class="user-guests d-md-none" alt="">
              <label for="checkout" class="label">Who</label>
              <input type="number" id="totalguests" class="border-0 totalguests" placeholder="2"
                readonly>
            </div>
            <ul class="dropdown-menu p-0 border-0">
              <div class="rooms-num room-info b-20 border bg-white">
                <div class="p-24">
                  <div class="adults counter mb-30 d-flex justify-content-between align-items-center">
                    <div>
                      <p class="person">Adult</p>
                      <p class="age"> Ages 13 or above</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span role="button" class="decrement dec">-</span>
                      <input type="number"
                        class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                        placeholder="0" readonly>
                      <span role="button" class="adultincrement incdec">+</span>
                    </div>
                  </div>

                  <div class="adults counter mb-30 d-flex justify-content-between align-items-center">
                    <div>
                      <p class="person">Children</p>
                      <p class="age"> Ages 2-12</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span role="button" id="childrenSubtract" class="decrement dec">-</span>
                      <input type="number"
                        class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                        placeholder="0" readonly>
                      <span role="button" id="childrenAdd" class="adultincrement incdec">+</span>
                    </div>
                  </div>

                  <div class="adults counter d-flex justify-content-between align-items-center">
                    <div>
                      <p class="person">Infants</p>
                      <p class="age"> Under 2</p>
                    </div>
                    <div class="d-flex align-items-center">
                      <span role="button" class="decrement dec">-</span>
                      <input type="number"
                        class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                        placeholder="0" readonly>
                      <span role="button" class="adultincrement incdec">+</span>
                    </div>
                  </div>
                </div>
                <div id="childrenDetails">

                </div>

              </div>
            </ul>
          </div>

           <div class="search-icon d-none d-md-flex filter-chackinout" id="filterchackinout">
					   <div class="search" >
						   <i class="fa fa-search" aria-hidden="true" ></i>
					   </div>
					</div>
        </div>
      </div>
    </div>
    <div class="selection d-none"></div>
    <div class="selection2 d-none"></div>

  </div>









  <section class="popular-cities other-sections text-start  mt-md-4 mb-md-5">
    <!-- <div class="container d-flex justify-content-between align-items-center">
      <h5 class="text-start my-md-5">Popular Choice</h5>
      <a href="" class="fs24 fs-sm-14 cursor-pointer color707070">See all</a>
    </div> -->

    <!-- <div class="hotelhomepagecontaienr mb-5 mb-md-0">
      <div class="container">
        <div class="row mb-md-5 bloglistingcarousel2">
          <div class="col-md-4">
            <div class="hotel-listing-card">
              <div class="">
                <div class="card-slider">



                  <div class="hotel-listing-slider hotelhomepageslideimage slick-initialized slick-slider"><img
                      src="{{asset('/images/unsplashsss_7T1KOFfE1aM.png') }}" alt=""
                      style="width: 100%; display: inline-block;">
                  </div>
                </div>
                <div class="py-3 px-2 ">
                  <div class="fs20 fs-sm-14 fw-500  mb-2">Taj Palace</div>
                  <div class="d-flex justify-content-between align-items-start locationandothers ">
                    <ul class="mb-2 mb-md-2 d-flex flex-wrap fs-sm-11 fs14">
                      <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                        <span>89%</span>
                      </li>
                      <li class=" d-flex align-items-center"><i class="fa  fa-circle" aria-hidden="true"></i>

                        <span><a href="">83 reviews</a></span>
                      </li>
                    </ul>
                  </div>

                  <div class="color707070 fs-sm-11 fs14 mb-4">
                    New Delhi, India
                  </div>
                  <button type="button" class="btn flex-sm-1 btn-outline-secondary px-4 py-2">₹7,999</button>

                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="hotel-listing-card">
              <div class="">
                <div class="card-slider">



                  <div class="hotel-listing-slider hotelhomepageslideimage slick-initialized slick-slider"><img
                      src="{{asset('/images/unsplashsss_7T1KOFfE1aM.png') }}" alt=""
                      style="width: 100%; display: inline-block;">
                  </div>
                </div>
                <div class="py-3 px-2 ">
                  <div class="fs20 fs-sm-14 fw-500  mb-2">Taj Palace</div>
                  <div class="d-flex justify-content-between align-items-start locationandothers ">
                    <ul class="mb-2 mb-md-2 d-flex flex-wrap fs-sm-11 fs14">
                      <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                        <span>89%</span>
                      </li>
                      <li class=" d-flex align-items-center"><i class="fa  fa-circle" aria-hidden="true"></i>

                        <span><a href="">83 reviews</a></span>
                      </li>
                    </ul>
                  </div>

                  <div class="color707070 fs-sm-11 fs14 mb-4">
                    New Delhi, India
                  </div>
                  <button type="button" class="btn flex-sm-1 btn-outline-secondary px-4 py-2">₹7,999</button>

                </div>
              </div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="hotel-listing-card">
              <div class="">
                <div class="card-slider">



                  <div class="hotel-listing-slider hotelhomepageslideimage slick-initialized slick-slider"><img
                      src="{{asset('/images/unsplashsss_7T1KOFfE1aM.png') }}" alt=""
                      style="width: 100%; display: inline-block;">
                  </div>
                </div>
                <div class="py-3 px-2 ">
                  <div class="fs20 fs-sm-14 fw-500  mb-2">Taj Palace</div>
                  <div class="d-flex justify-content-between align-items-start locationandothers ">
                    <ul class="mb-2 mb-md-2 d-flex flex-wrap fs-sm-11 fs14">
                      <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                        <span>89%</span>
                      </li>
                      <li class=" d-flex align-items-center"><i class="fa  fa-circle" aria-hidden="true"></i>

                        <span><a href="">83 reviews</a></span>
                      </li>
                    </ul>
                  </div>

                  <div class="color707070 fs-sm-11 fs14 mb-4">
                    New Delhi, India
                  </div>
                  <button type="button" class="btn flex-sm-1 btn-outline-secondary px-4 py-2">₹7,999</button>

                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div> -->



    <div class="container">
      <h5 class="text-start">Search cheap hotels by destination</h5>
      <div class="fs-sm-11 fs14 my-md-4" style="color: #90919A;">Can I really save on hotels near me and accommodation
        in
        other popular destinations by
        using KAYAK? Yes! KAYAK searches for hotel deals on hundreds of hotel comparison sites to help you find
        cheap hotels, holiday lettings, bed and breakfasts, motels, inns, resorts and more. Whether you are
        looking for a last-minute hotel or a cheap hotel room at a later date, you can find the best deals
        faster at KAYAK.</div>



      <div class="accordion accordion-flush mb-4" id="accordionFlushExample">
        <div class="row">

          <div class="accordion-item col-md-4 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                Mumbai hotels
              </button>
            </h2>
            <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">
                <div class="d-flex justify-content-between color707070 mb-2">
                  <div> Hotel Ajanta</div>
                  <div> ₹2,074+</div>
                </div>
                <div class="d-flex justify-content-between color707070 mb-2">
                  <div> Hotel City Empire</div>
                  <div> ₹2,474+</div>
                </div>
              </div>
            </div>
          </div>
          <div class="accordion-item col-md-4 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                New Delhi hotels
              </button>
            </h2>
            <div id="flush-collapseTwo" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">Placeholder content for this accordion, which is intended to
                demonstrate the <code>.accordion-flush</code> class. This is the second item's
                accordion
                body. Let's imagine this being filled with some actual content.</div>
            </div>
          </div>
          <div class="accordion-item col-md-4 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                Mumbai hotels
              </button>
            </h2>
            <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">Placeholder content for this accordion, which is intended to
                demonstrate the <code>.accordion-flush</code> class. This is the third item's
                accordion
                body. Nothing more exciting happening here in terms of content, but just filling up
                the
                space to make it look, at least at first glance, a bit more representative of how
                this would
                look in a real-world application.</div>
            </div>
          </div>

          <div class="accordion-item col-md-4 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapseFour" aria-expanded="false" aria-controls="flush-collapseFour">
                Mumbai hotels
              </button>
            </h2>
            <div id="flush-collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">Placeholder content for this accordion, which is intended to
                demonstrate the <code>.accordion-flush</code> class. This is the third item's
                accordion body. Nothing more exciting happening here in terms of content, but just filling up
                the space to make it look, at least at first glance, a bit more representative of how
                this would look in a real-world application.</div>
            </div>
          </div>
        </div>
      </div>



      <h5 class="text-start mb-4">Frequently asked questions about hotels on Travell</h5>

      <div class="accordion accordion-flush mb-4" id="accordionFlushExample">
        <div class="row">

          <div class="accordion-item col-md-6 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapse5" aria-expanded="false" aria-controls="flush-collapse5">
                How does Travell find such low hotel prices?
              </button>
            </h2>
            <div id="flush-collapse5" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">
                <!-- <div class="d-flex justify-content-between color707070 mb-2">
                  <div> Hotel Ajanta</div>
                  <div> ₹2,074+</div>
                </div>
                <div class="d-flex justify-content-between color707070 mb-2">
                  <div> Hotel City Empire</div>
                  <div> ₹2,474+</div>
                </div> -->
                Travell employs a multifaceted approach to secure low hotel prices. Firstly, it leverages advanced
                algorithms that scour numerous online travel platforms, including booking websites, travel agencies, and
                even direct hotel listings. This extensive search ensures comprehensive coverage of available options.

                Secondly, Travell capitalizes on partnerships and collaborations with hotels and travel providers, granting
                them access to exclusive deals and negotiated rates. This strategic networking allows Travell to offer
                prices that may not be readily accessible to individual consumers. Additionally, the platform may employ
                dynamic pricing strategies, adjusting rates based on factors like demand, location, and availability.
                Combined, these methods enable Travell to consistently provide competitive and cost-effective hotel options
                to its users.
              </div>
            </div>
          </div>

          <div class="accordion-item col-md-6 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapse6" aria-expanded="false" aria-controls="flush-collapse6">
                How do I find the best hotel deals on Travell?
              </button>
            </h2>
            <div id="flush-collapse6" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">To unearth the finest hotel deals on Travell, employ these savvy tactics.
                Firstly, utilize Travell's robust search filters to narrow down preferences like locations and places to
                visit. Next, keep an eye out for limited-time promotions and special offers featured on the platform.

                Additionally, consider subscribing to Travell's newsletter or notifications for exclusive access to updates
                on year-round offers and deals. It's prudent to book in advance, as early reservations often yield
                better rates. Finally, don't underestimate the power of customer reviews; they provide valuable insights
                into a hotel's quality and value. By amalgamating these strategies, you'll be poised to snag the
                ultimate hotel bargains on Travell.</div>
            </div>
          </div>

          <!-- new faq -->
          <div class="accordion-item col-md-6 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapse7" aria-expanded="false" aria-controls="flush-collapse5">
                What is special about hotel reviews on Travell?
              </button>
            </h2>
            <div id="flush-collapse7" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">
                Hotel reviews on Travell stand out for their authenticity and comprehensiveness. Users are encouraged to
                provide detailed and specific insights, covering aspects like cleanliness, service quality, amenities,
                and overall experience. This wealth of information empowers prospective travelers with a well-rounded
                understanding of what to expect.

                Moreover, unlike many platforms, Travell ensures that reviews are only submitted by verified guests who
                have actually stayed at the respective hotels. This minimizes the risk of fake or biased feedback.
                Additionally, Travell employs advanced algorithms to detect and remove any suspicious or fraudulent
                reviews, further enhancing the reliability and trustworthiness of the feedback.
              </div>
            </div>
          </div>
          <div class="accordion-item col-md-6 border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                data-bs-target="#flush-collapse8" aria-expanded="false" aria-controls="flush-collapse5">
                How do I create a Price Alert to track hotel prices on Travell?
              </button>
            </h2>
            <div id="flush-collapse8" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
              <div class="accordion-body">
                <div class="d-flex justify-content-between color707070 mb-2">
                  <div> Hotel Ajanta</div>
                  <div> ₹2,074+</div>
                </div>
                <div class="d-flex justify-content-between color707070 mb-2">
                  <div> Hotel City Empire</div>
                  <div> ₹2,474+</div>
                </div>
              </div>
            </div>
          </div>

          <!-- end faq -->
        </div>
      </div>
    </div>

  </section>

  <!-- start footer  -->
  @include('footer')
  <!-- end footer  -->


  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>
  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
  <!-- <script src="{{ asset('/css/hotel-css/index.js')}}"></script> -->
  <script src="{{ asset('/js/index.js')}}"></script>
  <!-- <script src="{{ asset('/js/autocomplete1.js')}}"></script>
  <script src="{{ asset('/js/autocomplete2.js')}}"></script> -->
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  <script src="{{ asset('/js/datepicker-hotelpage.js')}}"></script>
  <script src="{{ asset('/js/custom.js')}}"></script>

  <script>
  $('.bloglistingcarousel2').each(function() {
    var slider = $(this);
    slider.slick({
      dots: true,
      autoplay: false,
      autoplaySpeed: 5000,
      mobileFirst: true,
      arrows: false,
      slidesToShow: 1,
      responsive: [{
        breakpoint: 480,
        settings: "unslick"
      }]
    });

  });
  </script>



</body>

</html>