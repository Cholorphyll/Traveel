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

  <title>Hello, world!</title>
  <style>
 

  .carousel-indicators li {
    background-color: #282828;
    width: 7px;
    height: 7px;
    border-radius: 50%;
  }

  .text-center {
    text-align: center;
  }

  /* abc */
  #weather-widget {
    font-family: Arial, sans-serif;
  }

  #weather-link {
    color: #000;
    text-decoration: none;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px 20px;
    display: inline-block;
  }

  #weather-link:hover {
    background-color: #f0f0f0;
  }
</style>

</head>

<body>
  <?php 

 
  ?>




 
  


@if($theme == 'basic-white')
  <style>
     .gradient-custom {     
       background: #ffffff;         
      background: -webkit-linear-gradient(to right, rgba(255, 255, 255, 1), rgba(255, 236, 210, 1));     
      background: linear-gradient(to right, rgba(255, 255, 255, 1), rgba(255, 236, 210, 1)) 
    }
    .text-color{
      color:black;
    }
  </style>
@elseif($theme == 'blue')
  <style>
     .gradient-custom {     
      background: rgb(31, 86, 124);     
      }
      .text-color{
      color:white;
    }
  </style>
  @elseif($theme == 'dark-grey')
  <style>
     .gradient-custom {   
      background: rgb(89, 99, 103);
     
      }
      .text-color{
      color:white;
    }
  </style>
   @elseif($theme == 'yellow')
  <style>
     .gradient-custom {   
      background:rgb(255, 202,58);
     
      }
      .text-color{
      color:white;
    }
  </style>
   @elseif($theme == 'light-grey')
  <style>
     .gradient-custom {   
      background: rgb(179, 168, 152);     
      }
      .text-color{
      color:white;
    }
  </style>
  @elseif($theme == 'off-white')
  <style>
     .gradient-custom {   
      background:rgb(241, 238, 229); 
      }
      .text-color{
      color:black;
    }
  </style>
  @endif
  <div class="container">
    <div class="row d-flex justify-content-center align-items-center h-100" style="color: #282828;">
  
<?php 
$days = $weatherdata->days;
$firstDay = $days[0];

$tempmax = $firstDay->feelslikemax;
?>

      <div class="col-md-9 col-lg-7 col-xl-5 mt-3">

        <div class="card mb-4 gradient-custom" style="border-radius: 25px;">
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
                    <?php $tempCelsius = ($tempmax - 32) * 5/9 ?>
                      <h2 class="display-2 text-color"><strong>{{round($tempCelsius)}}°C</strong></h2>
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


        <div class="card gradient-custom" style="border-radius: 25px;">
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
						
                     <?php  
                    $tempCelsius_max="";
                    $tempCelsius_max = ($val->feelslikemax - 32) * 5/9 ?>
                      <p class="small text-color"><strong>{{round($tempCelsius_max)}}°C</strong></p>
                      <i class="fas fa-sun fa-2x mb-3" style="color: #ddd;"></i>
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

  </div>

</body>

</html>