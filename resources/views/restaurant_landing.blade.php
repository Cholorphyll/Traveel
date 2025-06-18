<!doctype html>
<html lang="en">

<head>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-3G0VLZYF7R"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-3G0VLZYF7R');
</script>
	
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  @if(!empty($locationPatent)) @php $locationPatent = array_reverse($locationPatent); @endphp @endif
@php $month = date('F'); $year = date('Y'); $lname ="";  @endphp
@if(!empty($searchresults)) @php  $lname = $lname @endphp  @endif  
<!-- $location_name -->
  <title>Travell</title>

<meta name="description" 
 content="Planning to visit {{$lname}}? Yaan will find you the top things to do in {{$lname}}@if(!empty($locationPatent)), @foreach ($locationPatent as $location){{$location['Name']}}@if(!$loop->last),@endif @endforeach @endif and plan your trip for you when visiting {{$lname}}.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">
  <!--  fontawesome -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://kit.fontawesome.com/b73881b7c2.js" crossorigin="anonymous"></script> 


  <link rel="stylesheet" href="{{ asset('/css/style.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/custom.css')}}">
	 <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">

        <!-- nav css -->
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" /> 
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">
  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}"> 
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>
      <!-- end nav css -->


  <!--<link rel="stylesheet" href="https://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />-->

  <?php
      $locationPatents = $locationPatent;
       $n = 2;
      ?>
  <script type="application/ld+json">
  {
    "@context": "https://schema.org/",
    "@type": "BreadcrumbList",
    "itemListElement": [{
        "@type": "ListItem",
        "position": 1,
        "name": "@if(!empty($breadcumb)) {{$breadcumb[0]->CountryName}} @endif",
        "item": "@if(!empty($breadcumb)){{ route('search.results',[$breadcumb[0]->LocationId.'-'.strtolower($breadcumb[0]->Lslug).'-'.str_replace(' ','_',strtolower($breadcumb[0]->CountryName))]) }} @endif"
      },
    
      @if(!empty($locationPatents))
      @foreach($locationPatents as $location) {
        "@type": "ListItem",
        "position": {{$n}},
       
        "name": "{{ $location['Name'] }}",
        "item": "@if(!empty($searchresults)){{ route('search.results',[ $location['LocationId'] .'-'.strtolower($location['slug'])]) }} @endif" 
      },
      <?php $n++; ?>
      @endforeach
      @endif

      {
        "@type": "ListItem",
        "position": {{$n}},
        "name": "@if(!empty($breadcumb)) {{$breadcumb[0]->LName}} @endif",
        "item": ""
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
 
    @include('Loc_nav.loc_navbar')
    <div class="container mt-3"> 
  <div class="container">
  <nav style="--bs-breadcrumb-divider: '>';" aria-label="breadcrumb">
            @if(!empty($breadcumb))
              <ol class="breadcrumb">    
				    @if($breadcumb[0]->ccName !="")
              <li class="breadcrumb-item active" aria-current="page">
          <a href="{{ route('explore_continent_list',[$breadcumb[0]->contid,$breadcumb[0]->ccName])}}" target="_blank"> {{$breadcumb[0]->ccName}}</a>
          </li> 
          @endif
          
                <li class="breadcrumb-item">
                  <a
                    href="{{ route('explore_country_list',[$breadcumb[0]->CountryId,$breadcumb[0]->cslug])}}">
                    @if(!empty($breadcumb)) {{$breadcumb[0]->CountryName}} @endif</a>
                </li>
                @endif
                @if(!empty($locationPatent))
                <?php
                $locationPatent = $locationPatent;
                ?>
                @foreach ($locationPatent as $location)
                <li class="breadcrumb-item">
                <a
                    href="@if(!empty($searchresults)){{ route('search.results',[$location['LocationId'].'-'.strtolower($location['slug'])]) }}@endif">
                    {{ $location['Name'] }}</a>
                </li>
                @endforeach
                @endif
                @if(!empty($breadcumb))    <li class="breadcrumb-item active" aria-current="page">{{$breadcumb[0]->LName}}</li>    @endif          
              </ol>
            
            </nav>
           
    <div class="row justify-content-center flex-md-row">
      <div class="col-md-5">
		  <h1 class="fs24 fw-500 mb-16">   Restaurants matching "{{$category}}"
      </h1>
        <div class="body mx-auto" align="center">
          <div class="explore-search" style="z-index: 2;">
            <div class="search-box-icon">
              <img src="{{asset('/images/search.svg')}}" class="search-icon" alt="">
            </div>
            <!-- <input type="text" id="autocompleteFive" placeholder="Search a Restaurant" class="searchrest">
                        <ul id="results" class="autoCompletewrapper"></ul> -->
            <!-- <input id="autoCompletetwo" type="text" tabindex="1" placeholder="&#xF002; Search"> -->
            <input type="text" 
              placeholder="Where are you goining?" autocomplete="off" class="serch_sights" id="serch_sights">
           
          </div>
        </div>

       
      


      


<span id="getcatfilterdata">

<!-- new category session data -->

 <span class="get_result">

          <div class="nearby-restaurant mb-50 mt-5">
            <div class="attraction mb-15">
              <img src="{{asset('/images/forks.svg')}}" alt="">
              <span>
                Restaurant
              </span>
            </div>


            <div class="row align-items-center">
              @if(!$searchresults->isEmpty())
              @foreach($searchresults as $rest)
              <div class="attraction mb-8">
            <svg xmlns="https://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
            <g clip-path="url(#clip0_1563_10259)">
              <path
                d="M0.4375 7C0.4375 8.74048 1.1289 10.4097 2.35961 11.6404C3.59032 12.8711 5.25952 13.5625 7 13.5625C8.74048 13.5625 10.4097 12.8711 11.6404 11.6404C12.8711 10.4097 13.5625 8.74048 13.5625 7C13.5625 5.25952 12.8711 3.59032 11.6404 2.35961C10.4097 1.1289 8.74048 0.4375 7 0.4375C5.25952 0.4375 3.59032 1.1289 2.35961 2.35961C1.1289 3.59032 0.4375 5.25952 0.4375 7Z"
                stroke="#6A6A6A" stroke-linecap="round" stroke-linejoin="round" />
              <path
                d="M7.39422 3.6015L8.25432 5.37383H9.92856C10.013 5.37087 10.0963 5.39397 10.1672 5.44001C10.2381 5.48604 10.293 5.55278 10.3246 5.63114C10.3563 5.70951 10.363 5.79571 10.3439 5.87803C10.3248 5.96035 10.2809 6.0348 10.218 6.09127L8.76461 7.60983L9.56984 9.46172C9.60493 9.54638 9.61264 9.6399 9.59189 9.72916C9.57114 9.81843 9.52298 9.89896 9.45415 9.95947C9.38532 10.02 9.29928 10.0574 9.2081 10.0666C9.11691 10.0757 9.02515 10.0561 8.94569 10.0104L6.99915 8.91507L5.0533 10.0125C4.97382 10.0584 4.88196 10.0782 4.79062 10.0692C4.69929 10.0602 4.61307 10.0228 4.54411 9.9622C4.47515 9.90164 4.4269 9.82099 4.40616 9.73158C4.38541 9.64218 4.39321 9.54852 4.42846 9.46378L5.23369 7.61188L3.78098 6.09127C3.71828 6.03492 3.67439 5.96066 3.65526 5.87854C3.63613 5.79643 3.64269 5.71042 3.67404 5.63216C3.70539 5.55389 3.76003 5.48714 3.83055 5.44094C3.90108 5.39474 3.9841 5.37131 4.06837 5.37383H5.74261L6.60477 3.6015C6.64222 3.52913 6.69885 3.46845 6.76847 3.42609C6.83808 3.38373 6.918 3.36133 6.99949 3.36133C7.08098 3.36133 7.1609 3.38373 7.23052 3.42609C7.30013 3.46845 7.35676 3.52913 7.39422 3.6015Z"
                stroke="#6A6A6A" stroke-linecap="round" stroke-linejoin="round" />
            </g>
            <defs>
              <clipPath id="clip0_1563_10259">
                <rect width="14" height="14" fill="white" />
              </clipPath>
            </defs>
          </svg>


          <span>
          Restaurant
          </span>
        
        </div>
              <div class="col-4 mb-3"> <a href="{{route('restaurant_detail',[$rest->slugid.'-'.$rest->RestaurantId.'-'.$rest->Slug])}}"
                  class=" text-decoration-none  "> <img src="{{asset('/images/unsplash_QGPmWrclELg.png')}}" alt=""
                    class="restaurant-img w-100"></a></div>

              <div class="col-8 ps-2 d-flex align-items-start justify-content-between">


                <div>
                  <div class="mb-4px fw-700">
                    <b> <a href="{{route('restaurant_detail',[$rest->slugid.'-'.$rest->RestaurantId.'-'.$rest->Slug])}}"
                  class=" text-decoration-none  ">
                        {{$rest->Title}}</a></b>
                       
                  
                      </div>
                  <div class="text-neutral-2 mb-4px">{{$rest->Address}}</div>
                  <div class="text-neutral-2">{{$rest->PriceRange}} </div>
                </div>


                <li class=" d-flex align-items-center fs12"><i class="fa fa-heart" aria-hidden="true"
                    style="margin-right: 6px;"></i>
                  <span>89%</span>
                </li>
              </div>
              @endforeach
              @else
              <p>Restaurant not available.</p>
              @endif

            </div>



          </div>
<!-- end category sesstion data -->
   
 

        </span>
        <div id="loading" style="display: none;">
          <!-- Loading... -->
      </div>

  
			
		          <!-- start about -->
           
       

          <!-- end nearby hotel -->
       
      </div>
      </span>
      <div class="col-md-7">
      @if(!empty($searchresults))
        <div class="mapsticky">
          <div id="map1" class="listmap" style="width: 100%; height: 1000px"></div>
        </div>
       @endif 
      </div>




    </div>
  </div>
  </div>
 <!-- start footer  -->
   @include('footer')
  <!-- end footer  -->



  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>




  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="{{ asset('/js/jquery.min.js')}}"></script> 

  <script src="{{ asset('/js/restaurant.js')}}"></script> 
  <script src="{{ asset('/js/header.js')}}"></script>  
  <!--map-->
  <script src="{{ asset('/js/map_leaflet.js')}}"></script>
  <!--nav bar-->
  <script src="{{ asset('/js/index.js')}}"></script>
  <script src="{{ asset('/css/hotel-css/t-datepicker.min.js')}}"></script>
  <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
  
  <script src="{{ asset('/js/custom.js')}}"></script>

  <script>
var locations = [];

<?php foreach ($searchresults as $result): ?>
  <?php if (!empty($result->Latitude) && !empty($result->Longitude)): ?>
    locations.push([<?php echo $result->Latitude; ?>, <?php echo $result->Longitude; ?>]);
  <?php endif; ?>
<?php endforeach; ?>

// Set default center to a location in Europe
var defaultCenter = [48.8566, 2.3522];
var center = locations.length > 0 ? locations[0] : defaultCenter;

var mapOptions = {
  center: center,
  zoom: 9
};

var map = new L.map('map1', mapOptions);
var layer = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');
map.addLayer(layer);

var markers = {};

<?php for ($i = 0; $i < count($searchresults); $i++): ?>
  <?php if (!empty($searchresults[$i]->Latitude) && !empty($searchresults[$i]->Longitude)): ?>
    var name<?php echo $i; ?> = '<?php echo addslashes($searchresults[$i]->Title); ?>'; 
    var isRecommend<?php echo $i; ?> = document.querySelector('.isrecomd_<?php echo $i; ?>').textContent;
    var category<?php echo $i; ?> = document.querySelector('.catname_<?php echo $i; ?>').textContent.trim();

    if (category<?php echo $i;?> === 'notavail') {
      category<?php echo $i;?> = ''; // Assign an empty string
    }

    var timing<?php echo $i;?> = document.querySelector('.timing_<?php echo $i;?>').textContent;

    var marker<?php echo $i; ?> = new L.Marker([<?php echo $searchresults[$i]->Latitude; ?>, <?php echo $searchresults[$i]->Longitude; ?>]);
    marker<?php echo $i; ?>.addTo(map);
    marker<?php echo $i; ?>.on('mouseover', function(e) {
      showTestName(e, name<?php echo $i; ?>, isRecommend<?php echo $i; ?>, category<?php echo $i; ?>, timing<?php echo $i; ?>);
    });
    marker<?php echo $i; ?>.on('mouseout', function(e) {
      map.closePopup(); 
    });
    markers[<?php echo $searchresults[$i]->RestaurantId; ?>] = marker<?php echo $i; ?>;
  <?php endif; ?>
<?php endfor; ?>

function showTestName(e, name, isRecommend, category, timing) {
  var marker = e.target;
  var popupContent = '<strong>' + name + '</strong>' + "<br>Recommend: " + isRecommend + "<br>" + category + "<br>" + timing;
  marker.bindPopup(popupContent, { offset: L.point(0, -20) }).openPopup(); 
}

// Function to find the parent element with a specific class
function findParentWithClass(element, className) {
  while ((element = element.parentElement) && !element.classList.contains(className));
  return element;
}

<?php
	   $lolat = null;
	   $loLongitude = null;
	   if(!$searchresults->isEmpty()){
	    $lolat = $searchresults[0]->loc_latitude;  
        $loLongitude = $searchresults[0]->loc_longitude; 
	  }
	 
?>
// Define the specific location
var specificLocation = [<?php echo $lolat; ?>, <?php echo $loLongitude; ?>];

// Create bounds array for markers
var bounds = [];
for (var i = 0; i < locations.length; i++) {
    bounds.push([locations[i][0], locations[i][1]]);
}

// Check if bounds is empty
if (bounds.length === 0) {
    // Set the specific location as the map view
    map.setView(specificLocation,10);
} else {
    // Zoom the map to fit all markers
    map.fitBounds(bounds);
}

  </script>

  <script>
 function highlightMarker(element) {
  var sid = element.querySelector(".sid").textContent;

  var attractionElements = document.querySelectorAll('.attraction');
  attractionElements.forEach(function(element) {
    var sid = element.getAttribute('data-sid');
    var marker = markers[sid];

    if (marker) {
      marker.setIcon(
        L.icon({
          iconUrl: '{{asset('/js/images/marker-icon.png')}}',
          iconSize: [25, 41],
        })
      );
    }
  });

  var marker = markers[sid];

  if (marker) {
    marker.setIcon(
      L.icon({
        iconUrl: '{{asset('/js/images/highlight-loc.png')}}',
        iconSize: [52, 53],
      })
    );
  } 
}

function unhighlightMarker(element) {
  var sid = element.querySelector(".sid").textContent;
  var marker = markers[sid];

  if (marker) {
    marker.setIcon(
      L.icon({
        iconUrl: '{{asset('/js/images/marker-icon.png')}}', 
        iconSize: [25, 41], 
      })
    );
  } 
  updateMarkerIcons();
 
}
function updateMarkerIcons() {

  var attractionElements = document.querySelectorAll('.attraction');
  attractionElements.forEach(function(element) {
    var sid = element.getAttribute('data-sid');
    var isMustSee = element.getAttribute('data-ismustsee');

    if (isMustSee === "1") {
      var marker = markers[sid];
      if (marker) {
        marker.setIcon(
          L.icon({
            iconUrl: '{{asset('/js/images/highlight-loc.png')}}',
            iconSize: [52, 53],
          })
        );
      } 
    }
  });

}


window.onload = function () {
  updateMarkerIcons();
};
//[28, 53]



  </script>




  <script>
  $(document).ready(function() {
    $('input').val('')
  });

  $('.bloglistingcarousel').each(function() {
    var slider = $(this);
    slider.slick({
      dots: true,
      autoplay: true,
      autoplaySpeed: 5000,
      mobileFirst: true,
      arrows: false,
      responsive: [{
        breakpoint: 480,
        settings: "unslick"
      }]
    });

  });


  $(document).ready(function() {
    $('.review-category>div').click(function() {
      $('.review-category>div').removeClass("active");
      $(this).addClass("active");
    });
  });

  $(document).ready(function() {
    if (window.File && window.FileList && window.FileReader) {
      $("#files").on("change", function(e) {
        var files = e.target.files,
          filesLength = files.length;
        for (var i = 0; i < filesLength; i++) {
          var f = files[i]
          var fileReader = new FileReader();
          fileReader.onload = (function(e) {
            var file = e.target;
            $("<span class=\"pip\">" +
              "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
              "<br/><span class=\"remove remove-image\"></span>" +
              "</span>").insertAfter("#files");
            $(".remove").click(function() {
              $(this).parent(".pip").remove();
            });

          });
          fileReader.readAsDataURL(f);
        }
        console.log(files);
      });
    } else {
      alert("Your browser doesn't support to File API")
    }
  });


  var a = document.querySelectorAll(".quick-search");
  for (var i = 0, length = a.length; i < length; i++) {
    a[i].onclick = function() {
      var b = document.querySelector(".quick-search.active");
      if (b) b.classList.remove("active");
      this.classList.add('active');
    };
  }
  </script>

  <script>
  /* 
  this is an implementation of Wes Bos click & drag scroll algorythm. In his video, he shows how to do the horizontal scroll. I have implemented the vertical scroll for those wondering how to make it as well.
  
  Wes Bos video:
    https://www.youtube.com/watch?v=C9EWifQ5xqA
*/
  const container = document.querySelector('#items-container');

  let startY;
  let startX;
  let scrollLeft;
  let scrollTop;
  let isDown;

  container.addEventListener('mousedown', e => mouseIsDown(e));
  container.addEventListener('mouseup', e => mouseUp(e))
  container.addEventListener('mouseleave', e => mouseLeave(e));
  container.addEventListener('mousemove', e => mouseMove(e));

  function mouseIsDown(e) {
    isDown = true;
    startY = e.pageY - container.offsetTop;
    startX = e.pageX - container.offsetLeft;
    scrollLeft = container.scrollLeft;
    scrollTop = container.scrollTop;
  }

  function mouseUp(e) {
    isDown = false;
  }

  function mouseLeave(e) {
    isDown = false;
  }

  function mouseMove(e) {
    if (isDown) {
      e.preventDefault();
      //Move vertcally
      const y = e.pageY - container.offsetTop;
      const walkY = y - startY;
      container.scrollTop = scrollTop - walkY;

      //Move Horizontally
      const x = e.pageX - container.offsetLeft;
      const walkX = x - startX;
      container.scrollLeft = scrollLeft - walkX;

    }
  }
  </script>
     <script>
   // $('.clickingexplore').click(function (e) { 
    //    e.preventDefault();
    //  $('.navbar .nav-tabs').addClass('nav-tabsactive');
   // });
</script>
<script>
        $('.defaultsearchvalue2').click(function (e) {
            e.preventDefault();
          
            $('#explore-tab').removeClass('active');
            $('#explore-tab-pane').removeClass('active show');


            
            $('#profile-tab').addClass('active');
            $('#profile-tab-pane').addClass('active show');

            
        });


        $('.defaultsearch').click(function (e) {
            e.preventDefault();
          
            $('#profile-tab').removeClass('active');
            $('#profile-tab-pane').removeClass('active show');
            $('#explore-tab').addClass('active');
            $('#explore-tab-pane').addClass('active show');


            

            
        });
    </script>
</body>

</html>