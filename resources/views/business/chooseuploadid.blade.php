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
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Business - Travell</title>
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


  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <link href="{{ asset('/css/hotel-css/t-datepicker.min.css')}}" rel="stylesheet" type="text/css">


  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <link rel="stylesheet" href="{{ asset('/css/business_index.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/business.css')}}">
  <!-- end nav css -->
  <style>
  input[type="file"] {
    display: none;
  }

  .custom-button {
    background-color: #4CAF50;
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    margin-left: 37%;
  }

  .photo-desc {
    text-align: center;
  }

  .custom-buttons {
    background-color: lightgray;
    border: 0px;
    border-radius: 5px;
    padding: 15px;
  }
  </style>

</head>

<body>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <!-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
       
    </div> -->

  @include('Loc_nav.business_nav_bar')

  <div class="">
    <div class="container">
      <div class="aboutustext mt-5">
        <div class="row">
          @if(!$getbusiness->isEmpty())
          @foreach($getbusiness as $val)
          <?php
              $firstPhotoUrl ="";
              if(isset($val->photos)){
                $photos = json_decode($val->photos, true);
                if(!empty($photos)){
                  $firstPhotoUrl = $photos[0]['url']; 
                }
              }
            
       ?>
          <div class="col-md-2">
          
            @if($firstPhotoUrl !="")  
              <img src="{{$firstPhotoUrl}}" alt="" class="usericon img-fluid rounded-circle" style="height: 49px;">
            @else

              <img src="{{asset('images/Hotel lobby -nmustsee (4).svg')}}" class="card-img">
            @endif
          </div>
          <div class="col-md-6">
            <p style="color: #00aa6c;">{{$val->name}}</p>
            <span>{{$val->address}}</span>
            <p><b>Business: </b>@if($type == 'restaurant') Restaurant @elseif($type == 'sight') Sight @elseif($type == 'hotel') Hotel @endif</p>
          </div>



        </div>
        <hr>
        @endforeach
        @endif
        
      
       
        <div class="row">
          <h4 style="color: #00aa6c;text-align: center;">Verify Your Identity</h4>
          <p class="mb-3" style="  margin-bottom: 80px;">Identity verification ensures the highest levels of security
            and helps us to prevent fraud.
            Please choose one of the options below to confirm your identity.</p>
          <div class="col-md-6 ml-5  varify-img" style="text-align: center;">

            @if(!$getbus->isEmpty() && $getbus[0]->varify_image == 1)
            <div><img src="{{ asset('images/check.png') }}" alt="" style="width: 84px;"></div>
            @else
            <span style="margin-top: 108px;"></span>
            @endif
            <a class="form-control  btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal2"
              role="button" style="width: inherit;">Photo ID</a>
          </div>
          <div class="col-md-6" style="text-align: center;">
            @if(!$getbus->isEmpty() && $getbus[0]->varify_phone == 1)
            <div><img src="{{ asset('images/check.png') }}" alt="" style="width: 84px;"></div>
            @else
            <span style="margin-top: 108px;"></span>
            @endif
            <a class="form-control  btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal1"
              role="button" style="width: inherit;">Phone</a>

          </div>
        </div>
      </div>
    </div>

    @include('footer')
    <!-- Button trigger modal -->



    <!-- Modal -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">

            <h6 class="modal-title" id="exampleModalLabel">Photo ID Varification</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" id="imagevarification">
            @csrf
            <div class="modal-body">
              <div class="form-group mt-3">
                <label for="exampleInputLocation">Choose issuing country/region</label>
                <select class="form-select" name="country" required>
                  <option value="" selected>Choose country</option>
                  @foreach($coutnry as $cont)
                  <option value="{{$cont->Name}}">{{$cont->Name}}</option>
                  @endforeach
                </select>
			
			
			 @if(!$getbus->isEmpty()) 
				
              	  <input type="hidden" name="business_id" value=" {{$getbus[0]->business_id}}">
				 
                <input type="hidden" name="id" value="{{$getbus[0]->id}}">
				@else
				    <input type="hidden" name="business_id" value="{{$getbusiness[0]->id}}">
				 
				  @endif
                <div class="validation-msg"></div>
              </div>

              <div class="form-group mt-3">
                <label for="exampleInputLocation">Select ID type</label>
                <select class="form-select" name="idtype" required>
                  <option selected>choose id type</option>
                  <option value="1" selected>Possport</option>
                  <option value="2">Driver's license</option>
                  <option value="3">Identity card</option>
                </select>
                <div class="validation-msg"></div>
              </div>
              <div class="form-group mt-3 mb-3">
                <label for="exampleInputLocation"> Upload a clear photo of a government-issued ID:</label>
                <input type="file" id="fileInput" style="display:none;" name="image">

                <button for="fileInput" class="form-control btn btn-outline-secondary custom-buttons" type="button"
                  onclick="document.getElementById('fileInput').click();">
                  <svg style="height: 23px; margin-right: 10px;" xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 640 512">
                    <!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                    <path
                      d="M144 480C64.5 480 0 415.5 0 336c0-62.8 40.2-116.2 96.2-135.9c-.1-2.7-.2-5.4-.2-8.1c0-88.4 71.6-160 160-160c59.3 0 111 32.2 138.7 80.2C409.9 102 428.3 96 448 96c53 0 96 43 96 96c0 12.2-2.3 23.8-6.4 34.6C596 238.4 640 290.1 640 352c0 70.7-57.3 128-128 128H144zm79-217c-9.4 9.4-9.4 24.6 0 33.9s24.6 9.4 33.9 0l39-39V392c0 13.3 10.7 24 24 24s24-10.7 24-24V257.9l39 39c9.4 9.4 24.6 9.4 33.9 0s9.4-24.6 0-33.9l-80-80c-9.4-9.4-24.6-9.4-33.9 0l-80 80z"/>
                  </svg>
                  Upload file from this device
                </button>


                <p class="up-text d-none" style="color:green;">Uploading...</p>
                <p id="fileName" class="mt-3">Selected Image: </p>
                <img id="previewImage" style="max-width: 100%; max-height: 300px; display: none;">

                <div class="image-msg"></div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <button type="submit" class="btn btn-primary" style=" height: 38.118px;">Save</button>
                  </div>
                  <div class="col-md-6">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                      style=" height: 38.118px;">Close</button>
                  </div>

                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal -->
    
    <!-- Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog ">
        <div class="modal-content">
          <div class="modal-header">

            <h6 class="modal-title" id="exampleModalLabel" style="color: #00aa6c;">Confirm Phone Number</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form action="" method="post" id="varifyphone">
            @csrf
            <div class="modal-body">
              <div class="form-group mt-3">
                <label for="exampleInputLocation">Phone</label>
               <input type="number" class="form-control phone" value="" placeholder="Phone" name="phone" autoComplete="off">
                <div class="phone-msg"></div>
              </div>
            </div>
            <div class="modal-footer">
              <div class="col-md-12">
                <div class="row">
                  <div class="col-md-6">
                    <button type="submit" class="btn btn-primary" style=" height: 38.118px;">Send Sms</button>
                  </div>
                  <div class="col-md-6">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"
                      style=" height: 38.118px;">Close</button>
                  </div>

                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <!-- end model 2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <!-- <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script> -->

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
    $(document).ready(function() {
      var parent = $(".parent");
      parent.mouseenter(function() {
        $(".b-list").show();
      });
      parent.mouseleave(function() {
        $(".b-list").hide();
      });
    });
    </script>


    <script>
    document.getElementById('fileInput').addEventListener('change', function() {
      var fileInput = this;
      var fileName = fileInput.value.split('\\').pop(); // Get the file name without the path
      document.getElementById('fileName').innerText = 'Selected Image: ' + fileName;

      // Display the selected image
      var previewImage = document.getElementById('previewImage');
      if (fileInput.files && fileInput.files[0]) {
        var reader = new FileReader();

        reader.onload = function(e) {
          previewImage.src = e.target.result;
          previewImage.style.display = 'block';
        };

        reader.readAsDataURL(fileInput.files[0]);
      }
    });
    </script>

    <!-- 

    <script src = "{{ asset('/css/hotel-css/t-datepicker.min.js')}}" > -->
    <!-- </script>
    <script src="{{ asset('/js/datepicker-homepage.js')}}"></script> -->
    <script src="{{ asset('/js/custom.js')}}"></script>
    <script src="{{ asset('/js/business.js')}}"></script>
    <!-- end nav js -->


</body>

</html>