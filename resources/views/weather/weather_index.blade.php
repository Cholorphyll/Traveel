<!doctype html>
<html lang="en">

<head>
  <!-- Required meta tags -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <link rel="stylesheet" href="{{ asset('/public/css/weather.css')}}">

  <title>Weather Forcast</title>
</head>

<body>
  <div class="col-md-12 mb-3 mt-3 hading-forcast">
    <h4 class="text-center mt-3 mb-3">Weather Forcast</h4>
  </div>
  <div class="container mt-3">


    <div class="col-md-12">
      <div class="row">
        <div class="col-md-2">
          <span class="setting" onclick="toggleSetting()">Setting</span> | <span class="Theme"
            onclick="toggleTheme()">Theme</span>
          <hr>
        </div>
        <!-- <div class="col-md-2">
          THeme
        </div> -->

      </div>
    </div>

    <div class="">

      <form method="post" action="">
        <div class="abc">
          <div class="col-md-12">
            <div class="row">
              <div class="col-md-3">

                <div class="form-search form-search-icon-right">
                  <p>Location</p>
                  <input type="text" id="searchHotelcity" name="ctname" class="form-control rounded-3"
                    placeholder="Select Location"><i class="ti ti-search"></i>
                </div>

                <span id="citylisth"></span>
              </div>
              <div class="col-md-3">
                <p>Country</p>
                <input type="text" class="form-control select-cont Country" id="Country" placeholder="Location lavel 1"
                  value="NEW YORK">
              </div>

              <div class="col-md-3">
                <p>width</p>
                <input type="text" class="form-control width" placeholder="width" value="500">
              </div>
              <div class="col-md-3">
                <p>Height</p>
                <input type="text" class="form-control height" placeholder="Height" value="400">
              </div>

            </div>


          </div>
          </div>
         
      </form>
      <div>

      </div>

  
 <!-- start theme code -->
 <div class="container theme-val d-none">
      <section class="mt-3" style="background-color: #3fbbc0d4;">
        <div class="container py-5 h-100 ">
          <!-- start code -->
          <div class="row d-flex justify-content-center align-items-center h-100" style="color: #282828;">



            <div class="col-md-12">

              <div class="row">
                <div class="col-md-2">
                  <!-- First card section -->
                  <div class="card mb-4 gradient-custom theme-1"  style="border-radius: 25px; background: rgb(31, 86, 124);
    color: white;" onclick="applyStyleToWeather(this)" data-theme="blue" data-textcol="white">
                    <div class="card-body p-4">
                      <div id="demo1" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->
                        
                        <!-- Carousel inner -->
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <div class="d-flex justify-content-between mb-4 pb-2">
                              <div style="color: white;">
                                <h2 class="display-2"  style="font-size: 1.5rem;color: white;"><strong>20°C</strong></h2>
                                <p class="text-muted mb-0" style="color: white !important;">NEW YORK</p>
                              </div>
                              <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp" width="100px">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- end First card section -->
                </div>
                <div class="col-md-2">
                  <!-- Second card section -->
                  <div class="card mb-4 gradient-custom"
                    style="border-radius: 25px; background: rgb(89, 99, 103);color: white;"  onclick="applyStyleToWeather(this)" data-theme="dark-grey" data-textcol="white">
                    <div class="card-body p-4">
                      <div id="demo1" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->

                        <!-- Carousel inner -->
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <div class="d-flex justify-content-between mb-4 pb-2">
                              <div style="color: white;color: white;">
                                <h2 class="display-2" style="font-size: 1.5rem;"><strong>20°C</strong></h2>
                                <p class="text-muted mb-0" style="color: white !important;">NEW YORK</p>
                              </div>
                              <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp" width="100px">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!-- end Second card section -->
                </div>

                <div class="col-md-2">
                  <!-- Second card section -->
                  <div class="card mb-4 gradient-custom"
                    style="border-radius: 25px;background:rgb(255, 202,58);color: white;" onclick="applyStyleToWeather(this)" data-theme="yellow" data-textcol="white">
                    <div class="card-body p-4">
                      <div id="demo1" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->

                        <!-- Carousel inner -->
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <div class="d-flex justify-content-between mb-4 pb-2">
                              <div style="color: white;">
                                <h2 class="display-2" style="font-size: 1.5rem;"><strong>20°C</strong></h2>
                                <p class="text-muted mb-0" style="color: white !important;">NEW YORK</p>
                              </div>
                              <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp" width="100px">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!-- end Second card section -->
                </div>
                <div class="col-md-2">
                  <!-- Second card section -->
                  <div class="card mb-4 gradient-custom" style="border-radius: 25px;"  onclick="applyStyleToWeather(this)" data-theme="basic-white" data-textcol="black">
                    <div class="card-body p-4">
                      <div id="demo1" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->

                        <!-- Carousel inner -->
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <div class="d-flex justify-content-between mb-4 pb-2">
                              <div>
                                <h2 class="display-2" style="font-size: 1.5rem;"><strong>20°C</strong></h2>
                                <p class="text-muted mb-0">NEW YORK</p>
                              </div>
                              <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp" width="100px">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!-- end Second card section -->
                </div>
                <div class="col-md-2">
                  <!-- Second card section -->
                  <div class="card mb-4 gradient-custom"
                    style="border-radius: 25px;background: rgb(179, 168, 152);color:white;" onclick="applyStyleToWeather(this)" data-theme="light-grey" data-textcol="white">
                    <div class="card-body p-4">
                      <div id="demo1" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->

                        <!-- Carousel inner -->
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <div class="d-flex justify-content-between mb-4 pb-2">
                              <div>
                                <h2 class="display-2" style="font-size: 1.5rem;color: white;"><strong>20°C</strong></h2>
                                <p class="text-muted mb-0" style="color: white !important;">NEW YORK</p>
                              </div>
                              <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp" width="100px">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!-- end Second card section -->
                </div>
                <div class="col-md-2">
                  <!-- Second card section -->
                  <div class="card mb-4 gradient-custom" style="border-radius: 25px;background:rgb(241, 238, 229);" onclick="applyStyleToWeather(this)" data-theme="off-white" data-textcol="black">
                    <div class="card-body p-4">
                      <div id="demo1" class="carousel slide" data-ride="carousel">
                        <!-- Indicators -->

                        <!-- Carousel inner -->
                        <div class="carousel-inner">
                          <div class="carousel-item active">
                            <div class="d-flex justify-content-between mb-4 pb-2">
                              <div>
                                <h2 class="display-2" style="font-size: 1.5rem;"><strong>20°C</strong></h2>
                                <p class="text-muted mb-0">NEW YORK</p>
                              </div>
                              <div>
                                <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp" width="100px">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                  </div>
                  <!-- end Second card section -->
                </div>


                <!-- end row -->
              </div>





              <!-- aa -->
            </div>
          </div>
          <!-- end code -->
        </div>
      </section>
    </div>
    <!-- end theme code -->
    <span class="theme-name d-none">basic-white</span>
    <button id="generate-code-button" type="button" class="btn btn-primary mt-3 " data-toggle="modal"
        data-target="#weather-widget-modal" style="background-color: #3fbbc0;border: #3fbbc0;">Generate</button>
      <button id="search-weather" type="button" class="btn btn-primary mt-3 ml-3"
        style="background-color: #3fbbc0;border: #3fbbc0;">Search</button>

      <!-- Hidden input field to hold the code -->

      <section class="vh-100 mt-3" style="background-color: #3fbbc0d4;">
        <div class="container py-5 h-100 getresult weather-widget" id="weather-widget">
          <!-- start code -->
          <div class="row d-flex justify-content-center align-items-center h-100" style="color: #282828;">

            <?php 
              $days = $weatherdata->days;
              $firstDay = $days[0];

              // Correct way to access a specific property

              $tempmax = $firstDay->tempmax;
              ?>

            <div class="col-md-9 col-lg-7 col-xl-5">

              <div class="card mb-4 gradient-custom mail-weather"  style="border-radius: 25px;">
                <div class="card-body p-4">

                  <div id="demo1" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ul class="carousel-indicators mb-0">
                      <li data-target="#demo1" data-slide-to="0" class="active"></li>
                      <li data-target="#demo1" data-slide-to="1"></li>
                      <li data-target="#demo1" data-slide-to="2"></li>
                    </ul>
                    <!-- Carousel inner -->
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                        <div class="d-flex justify-content-between mb-4 pb-2">
                          <div>
                            <h2 class="display-2 text-color"><strong>{{round($tempmax)}}°C</strong></h2>
                            <p class="mb-0 text-color">{{$lname}}</p>
                          </div>
                          <div>
                            <img src="https://mdbcdn.b-cdn.net/img/Photos/new-templates/bootstrap-weather/ilu3.webp"
                              width="150px">
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>


              <div class="card mail-weather" style="border-radius: 25px;">
                <div class="card-body p-4">

                  <div id="demo3" class="carousel slide" data-ride="carousel">
                    <!-- Indicators -->
                    <ul class="carousel-indicators mb-0">
                      <li data-target="#demo3" data-slide-to="0"></li>
                      <li data-target="#demo3" data-slide-to="1"></li>
                      <li data-target="#demo3" data-slide-to="2" class="active"></li>
                    </ul>
                    <!-- Carousel inner -->
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                        <div class="d-flex justify-content-around text-center mb-4 pb-3 pt-2">
                          @php $count = 0; @endphp
                          @foreach($days as $val)
                          <?php $date = $val->datetime; ?>
                          <div class="flex-column">
                            <p class="small text-color"><strong>{{round($val->tempmax)}}°C</strong></p>
                            <i class="fas fa-sun fa-2x mb-3 text-color" style="color: #ddd;"></i>
                            <p class="mb-0 text-color"><strong> {{date('D', strtotime($date))}}</strong></p>
                          </div>
                          @php $count++; @endphp
                          @if($count == 7)
                          @break
                          @endif
                          @endforeach

                        </div>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>  
          <!-- end code -->
        </div>
      </section>
      <!-- star model -->

      <div class="modal fade" id="weather-widget-modal" tabindex="-1" role="dialog"
        aria-labelledby="weather-widget-modal-label" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="weather-widget-modal-label">Weather Forcast Widget</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div id="weather-widget">

                <textarea id="widget-code" rows="11" cols="90" readonly></textarea>
              </div>
            </div>
            <div class="modal-footer">
              <button id="copy-code-button" type="button" class="btn btn-primary">Copy</button>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
          </div>
        </div>
      </div>
      <!-- end model -->


    </div>



   
    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
      integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.8/clipboard.min.js"></script>

    <script src="{{asset('public/js/weather.js')}} "></script>

    <script>
    $(document).ready(function() {
      $(document).on('click', '[data-dismiss="modal"]', function() {
        $(this).closest('.modal').modal('hide');
      });
      document.getElementById("generate-code-button").addEventListener("click", function() {
        // Trigger the modal to show
        $('#weather-widget-modal').modal('show');

        var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' +
          window.location.port : '');
        var base_url = baseURL + '/';
        var country = $('#Country').val();
        var cont = $('#searchHotelcity').val();

        var theme = $('.theme-name').text();
        if (cont != "" && country != "") {
          var locname = cont + ',' + country;
        } else if (country != "") {
          var locname = country;
        }

        var width = $('.width').val();
        var height = $('.height').val();
       // alert(width)

        // Fetch the widget code from the API endpoint
        fetch(base_url + 'createwidget?locname=' + encodeURIComponent(locname) + '&width=' + encodeURIComponent(
            width) + '&height=' + encodeURIComponent(height)+ '&theme=' + encodeURIComponent(theme))
          .then(response => response.json())
          .then(data => {
            if (data.widget_code) {
              document.getElementById("widget-code").value = data.widget_code;

            } else {
              alert('Failed to fetch widget code from the API');
            }
          })
          .catch(error => {
            console.error('Error fetching widget code:', error);
            alert('Failed to fetch widget code from the API');
          });
      });

      document.getElementById("copy-code-button").addEventListener("click", function() {
        var widgetCodeTextarea = document.getElementById("widget-code");
        widgetCodeTextarea.select();
        try {
          var successful = document.execCommand('copy');

        } catch (err) {
          console.error('Unable to copy widget code:', err);
          alert('Unable to copy widget code!');
        }
      });
    });
    </script>


    <script>
    // $(document).ready(function() {
    //     document.getElementById("generate-code-button").addEventListener("click", function() {
    //       var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
    //       var base_url = baseURL + '/trip/';
    //         var cont = $('#searchHotelcity').val();
    //         var country = $('#country').val();
    //         var locname = cont + ',' + country;

    //         // Fetch the widget code from the API endpoint
    //         fetch(base_url + 'createwidget?locname=' + encodeURIComponent(locname))
    //             .then(response => response.json())
    //             .then(data => {
    //                 if (data.widget_code) {
    //                     document.getElementById("widget-code").value = data.widget_code;
    //                 } else {
    //                     alert('Failed to fetch widget code from the API');
    //                 }
    //             })
    //             .catch(error => {
    //                 console.error('Error fetching widget code:', error);
    //                 alert('Failed to fetch widget code from the API');
    //             });
    //     });

    //     document.getElementById("copy-code-button").addEventListener("click", function() {
    //         var widgetCodeTextarea = document.getElementById("widget-code");
    //         widgetCodeTextarea.select();
    //         try {
    //             var successful = document.execCommand('copy');
    //             var message = successful ? 'Widget code copied to clipboard!' : 'Unable to copy widget code!';
    //             alert(message);
    //         } catch (err) {
    //             console.error('Unable to copy widget code:', err);
    //             alert('Unable to copy widget code!');
    //         }
    //     });
    // });
    </script>

</body>

</html>