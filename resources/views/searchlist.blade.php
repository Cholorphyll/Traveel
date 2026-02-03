<!DOCTYPE html>
<html lang="en">
  <head>
    <title>Travell - Explore Listing</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/bootstrap.bundle.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/jquery-ui-datepicker.min.js')}}"></script>
    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/slick.min.js')}}"></script>

    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/bootstrap.min.css')}}?v={{ filemtime(public_path('frontend/hotel-detail/css/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/style.css')}}?v={{ filemtime(public_path('frontend/hotel-detail/css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/calendar.css')}}?v={{ filemtime(public_path('frontend/hotel-detail/css/calendar.css')) }}" media="screen">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/slick.css')}}?v={{ filemtime(public_path('frontend/hotel-detail/css/slick.css')) }}">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/responsive.css')}}?v={{ filemtime(public_path('frontend/hotel-detail/css/responsive.css')) }}">
    <link rel="stylesheet" href="{{ asset('/frontend/hotel-detail/css/custom.css')}}?v={{ filemtime(public_path('frontend/hotel-detail/css/custom.css')) }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css">

    <link rel="stylesheet" href="{{ asset('/explore/css/bootstrap.min.css')}}?v={{ filemtime(public_path('explore/css/bootstrap.min.css')) }}">
    <link rel="stylesheet" href="{{ asset('/explore/css/style.css')}}?v={{ filemtime(public_path('explore/css/style.css')) }}">
    <link rel="stylesheet" href="{{ asset('/explore/css/calendar.css')}}?v={{ filemtime(public_path('explore/css/calendar.css')) }}" media="screen">
    <link rel="stylesheet" href="{{ asset('/explore/css/slick.css')}}?v={{ filemtime(public_path('explore/css/slick.css')) }}">
    <link rel="stylesheet" href="{{ asset('/explore/css/responsive.css')}}?v={{ filemtime(public_path('explore/css/responsive.css')) }}">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
      html, body { height: 100%; }
      body.searchlist-page { font-family: 'Inter', sans-serif; }
      body.searchlist-page .tr-explore-listing-section { padding-top: 12px; }
      body.searchlist-page .tr-explore-listing-section > .container { max-width: 100% !important; }
      body.searchlist-page .tr-explore-listing-section > .container-fluid { max-width: 100% !important; }

      body.searchlist-page .search__Section { position: relative; margin-bottom: 16px; }
      body.searchlist-page .search__Section form { position: relative; }
      body.searchlist-page .search__Section .tr-search-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-size: 14px;
        pointer-events: none;
      }
      body.searchlist-page .search__Section input#search {
        padding-left: 38px !important;
        border-radius: 12px;
      }

      body.searchlist-page .tr-explore-listing {
        display: flex !important;
        flex-wrap: nowrap !important;
        gap: 18px !important;
        align-items: flex-start !important;
      }

      body.searchlist-page .tr-explore-left-section {
        flex: 0 0 640px !important;
        max-width: 640px !important;
        width: 640px !important;
      }

      body.searchlist-page .tr-explore-right-section {
        flex: 1 1 auto !important;
        min-width: 420px !important;
        display: block !important;
        height: calc(100vh - 140px);
      }

      body.searchlist-page .tr-map-section {
        position: sticky;
        top: 92px;
        height: calc(100vh - 140px);
      }

      body.searchlist-page #searchlistMap {
        height: 100% !important;
        min-height: 520px;
        border-radius: 14px;
        overflow: hidden;
      }

      body.searchlist-page .card__Container { margin-bottom: 18px; }
      body.searchlist-page .card__Box {
        border: 1px solid rgba(0,0,0,0.10);
        border-radius: 16px;
        padding: 16px;
        background: #fff;
      }

      body.searchlist-page .card__title h6 {
        display: inline-block;
        font-size: 14px;
        font-weight: 500;
        padding-bottom: 6px;
        border-bottom: 3px solid #ff5a1f;
        margin-bottom: 14px;
      }

      body.searchlist-page .card__Subtitle h5 {
        font-size: 14px;
        font-weight: 500;
        line-height: 1.25;
        margin-bottom: 10px;
      }

      body.searchlist-page .card__listSection { margin-bottom: 0; }

      body.searchlist-page .card__details {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-right: 22px;
        font-size: 18px;
        line-height: 1.35;
        vertical-align: middle;
      }
      body.searchlist-page .card__details.update_details { margin-right: 22px; }
      body.searchlist-page .card__details img { width: 18px; height: 18px; display: inline-block; }
      body.searchlist-page .card__details span:last-child { white-space: nowrap; }
      body.searchlist-page .tagName { color: #169B50; font-weight: 600; }

      body.searchlist-page .amenity-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #111827;
        display: inline-block;
      }

      @media (max-width: 992px) {
        body.searchlist-page .tr-explore-listing { display: block !important; }
        body.searchlist-page .tr-explore-left-section { max-width: 100% !important; width: 100% !important; }
        body.searchlist-page .tr-explore-right-section { display: none !important; }
      }
    </style>
  </head>
  <body class="searchlist-page">
    @php
      $term = $term ?? '';
      $location_name = $location_name ?? '';
      $restaurants = $restaurants ?? collect();
      $suggestions = $suggestions ?? [];
    @endphp

    @include('frontend.header')

    <!-- Mobile Navigation-->
    @include('frontend.mobile_nav')

    <div class="tr-explore-listing-section">
      <div class="container-fluid">
        <div class="tr-explore-listing">  
          <div class="tr-explore-left-section">
            <span id="locid" class="d-none">{{ $location_id ?? '' }}</span>

            <div class="search__Section">
              <form method="GET" action="{{ route('searchlist') }}">
                <span class="tr-search-icon"><i class="fa fa-search" aria-hidden="true"></i></span>
                <input type="hidden" name="locId" value="{{ $location_id ?? '' }}"/>
                <input type="hidden" name="slugid" value="{{ $location_slugid ?? '' }}"/>
                <input type="text" id="search" name="q" required placeholder="Search restaurants" value="{{ $search_query ?? '' }}"/>
              </form>
            </div>

            @if(($restaurants instanceof \Illuminate\Support\Collection ? $restaurants->count() : count($restaurants)) > 0)
              @foreach($restaurants as $r)
                @php
                  $rTitle = $r->Title ?? '';
                  $rSlugId = $r->slugid ?? ($location_slugid ?? '');
                  $rSlug = $r->slug ?? '';
                  $rId = $r->RestaurantId ?? null;
                @endphp
                <div class="card__Container">
                  <div class="card__Box">
                    <div class="card__title">
                      <h6>Restaurant</h6>
                    </div>
                    <div class="Container__Section space8">
                      <div class="card__gallerySection">
                        <div class="card__thumb">
                          @if(!empty($r->Img1))
                            <img src="{{ $r->Img1 }}" alt="">
                          @else
                            <img src="{{ asset('explore/images/London/SukhnaLake.jpg') }}" alt="">
                          @endif
                          <span><img src="{{ asset('explore/images/icons/bookmark.svg') }}" alt=""></span>
                        </div>
                      </div>
                      <div class="left__sideSection ad__Space">
                        <div class="card__Subtitle">
                          @if(!empty($rId) && !empty($rSlugId))
                            <h5><a href="{{ url('/rd-' . $rSlugId . '-' . $rId . '-' . ($rSlug ?: \Illuminate\Support\Str::slug($rTitle))) }}">{{ $rTitle }}</a></h5>
                          @else
                            <h5>{{ $rTitle }}</h5>
                          @endif
                        </div>
                        @if(!empty($r->Address))
                        <div class="card__listSection">
                          <div class="card__details update_details">
                            <span>{{ $r->Address }}</span>
                          </div>
                        </div>
                        @endif
                        @if(!empty($r->Cuisines))
                        <div class="card__listSection">
                          <div class="card__details">
                            <span>{{ $r->Cuisines }}</span>
                          </div>
                        </div>
                        @endif
                        @if(!empty($r->Cost))
                        <div class="card__listSection">
                          <div class="card__details">
                            <span>{{ $r->Cost }}</span>
                          </div>
                        </div>
                        @endif
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            @else
              @php
                $dummyRestaurants = [
                  'Boating at Sukhna Lake',
                  'Cafe Delight',
                  'Royal Street Kitchen',
                ];
              @endphp
              @foreach($dummyRestaurants as $dummyTitle)
                <div class="card__Container">
                  <div class="card__Box">
                    <div class="card__title">
                      <h6>Restaurant</h6>
                    </div>
                    <div class="Container__Section space8">
                      <div class="card__gallerySection">
                        <div class="card__thumb">
                          <img src="{{ asset('explore/images/London/SukhnaLake.jpg') }}" alt="">
                          <span><img src="{{ asset('explore/images/icons/bookmark.svg') }}" alt=""></span>
                        </div>
                      </div>
                      <div class="left__sideSection ad__Space">
                        <div class="card__Subtitle">
                          <h5>{{ $dummyTitle }}</h5>
                        </div>
                        <div class="card__listSection">
                          <div class="card__details">
                            <span><img src="{{ asset('explore/images/icons/heart.svg') }}" alt=""></span>
                            <span>89%</span>
                          </div>
                        </div>
                        <div class="card__listSection">
                          <div class="card__details update_details">
                            <span class="tagName">Open</span>
                            <span>till about 5 PM</span>
                          </div>
                          <div class="card__details update_details">
                            <span><img src="{{ asset('explore/images/icons/point.svg') }}" alt=""></span>
                            <span>&bull; 5 hours</span>
                          </div>
                        </div>
                        <div class="card__listSection">
                          <div class="card__details">
                            <span><img src="{{ asset('explore/images/icons/globel.svg') }}" alt=""></span>
                            <span>Speaks English</span>
                          </div>
                          <div class="card__details">
                            <span><img src="{{ asset('explore/images/icons/wifi.svg') }}" alt=""></span>
                            <span> Free WIFI </span>
                          </div>
                        </div>
                        <div class="card__listSection">
                          <div class="card__details">
                            <span><img src="{{ asset('explore/images/icons/close.svg') }}" alt=""></span>
                            <span>Takeout</span>
                          </div>
                          <div class="card__details">
                            <span><img src="{{ asset('explore/images/icons/tick.svg') }}" alt=""></span>
                            <span> Seating </span>
                          </div>
                          <div class="card__details">
                            <span><img src="{{ asset('explore/images/icons/tick.svg') }}" alt=""></span>
                            <span>Delivery</span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            @endif
         
          </div>

          <div class="tr-explore-right-section">
            <div class="tr-map-section">
              <button type="button" class="btn-close"></button>
              <div class="tr-hotel-on-map">
                <form>
                  <input type="text" class="form-control" id="" placeholder="Search on map" name="" autocomplete="off">
                  <div class="tr-recent-searchs-modal" id="">
                    <div class="tr-enable-location">Around Current Location</div>
                    <h5>Recent searches</h5>
                    <ul>
                      <li>
                        <div class="tr-place-info">
                          <div class="tr-location-icon"></div>
                          <div class="tr-location-info">
                            <div class="tr-hotel-name">London Hotels</div>
                            <div class="tr-hotel-city">England, United Kingdom</div>
                          </div>
                        </div>
                      </li>
                      <li>
                        <div class="tr-place-info">
                          <div class="tr-location-icon"></div>
                          <div class="tr-location-info">
                            <div class="tr-hotel-name">Morocco</div>
                            <div class="tr-hotel-city">North Africa</div>
                          </div>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <button type="button" hidden="" class="tr-btn">Countinue</button>                        
                </form>
              </div>
              <div class="tr-map-tooltip tr-explore-listing">
                <div class="tr-historical-monument">
                  <div class="tr-heading-with-distance">
                    <div class="tr-category">Shopping Area</div>
                    <div class="tr-distance">1.2 km from Tower of London</div>
                  </div>
                  <div class="tr-image">
                    <a href="javascript:void(0);">
                      <img loading="lazy" src="images/hotel-explore-2.png" alt="">
                    </a>
                  </div>
                  <div class="tr-details">
                    <h3><a href="javascript:void(0);" target="_blank">Oxford Street</a></h3>
                    <div class="tr-location">City of London</div>
                    <div class="tr-like-review">
                      <div class="tr-heart">
                        <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path></svg>
                      </div>
                      <div class="tr-ranting-percent">89%</div>
                    </div>
                  </div>
                  <div class="tr-more-inform">
                    <ul>
                      <li><span>Open</span>until 5 PM</li>
                      <li>5 hours.</li>
                    </ul>
                  </div>
                </div>
              </div>
              <div id="searchlistMap" style="width: 100%; height: 100%;"></div>
            </div>
            <div class="tr-explore-overlay"></div>
          </div>
        </div>       
      </div>
    </div>

    <!-- Map Modal With Filter & Hotel List - Start-->
      
    <!-- Map Modal With Filter & Hotel List - End-->
    
    @include('frontend.footer')
    <div class="overlay" id="overLay"></div>

    <!-- Share Modal -->
    <div class="modal" id="shareModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          <h3>Share this experience</h3>
          <div class="tr-share-infos">
            <div class="tr-hotel-img">
              <img loading="lazy" src="images/room-image-1.png" alt="Room Image">
            </div>
            <div class="tr-share-details">
              <span class="tr-hotel-name">Things to do in London</span>
              <p>3590 Attractions found in London</p>
              <!--
              <span class="tr-rating">4.83</span>
              <span class="tr-bedrooms">
                <span>2 bedrooms</span>
                <span>3 beds</span>
                <span>2 bathrooms</span>
              </span>
              -->
            </div>
          </div>
          <div class="tr-share-options">
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-copy">Copy link</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-email">Email</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-messages">Messages</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-whatsapp">Whatsapp</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-messenger">Messenger</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-facebook">Facebook</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-twitter">Twitter</a>
            </div>
            <div class="tr-share-option">
              <a href="javascript:void(0);" class="tr-embed">Embed</a>
            </div>
          </div>
          <div class="tr-alert tr-copy-alert">Link copied</div>
        </div>
      </div>
    </div>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script type="text/javascript" src="{{ asset('/frontend/hotel-detail/js/common.js') }}"></script>
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        try {
          if (!document.getElementById('searchlistMap') || typeof L === 'undefined') {
            return;
          }

          var map = L.map('searchlistMap', { zoomControl: true });
          var center = [51.5074, -0.1278];
          map.setView(center, 12);

          L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap contributors'
          }).addTo(map);

          L.marker(center).addTo(map).bindPopup('Test location').openPopup();

          setTimeout(function() {
            try { map.invalidateSize(true); } catch (e) {}
          }, 150);

          window.addEventListener('resize', function() {
            try { map.invalidateSize(true); } catch (e) {}
          });

          try { window.TR_SEARCHLIST_MAP = map; } catch (e) {}
        } catch (e) {
          try { console.log('[Searchlist] map init failed', e); } catch (e2) {}
        }
      });
    </script>
  </body>
</html>