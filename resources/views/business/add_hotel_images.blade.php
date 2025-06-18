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
  <title>Add Hotel Images - Travell</title>
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
  <link rel="stylesheet" href="{{ asset('/css/hotel-css/autoComplete.min.css')}}">
  <link rel="stylesheet" href="{{ asset('/css/business.css')}}">
  <script src="{{ asset('/css/hotel-css/autoComplete.js')}}"></script>

  <!-- end nav css -->
  <style>
  /* Hide the default file input */
  input[type="file"] {
    display: none;
  }

  /* Style for the custom button */
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
  </style>
</head>

<body>

  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-PTHP3JH4" height="0" width="0"
      style="display:none;visibility:hidden"></iframe></noscript>
  <!-- End Google Tag Manager (noscript) -->
  <!-- <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
       
    </div> -->



  <div class="">
    <div class="container mt-5 ">
    <h5 class="mt-5 "><a href="{{route('view_hotel_business',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}"> <span class="l-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg></span><span class="back">back</span> </a></h5>
      @if(!$getbusiness->isEmpty())

      <h3 style="color:#ff601f; text-align:center;">Manage {{$getbusiness[0]->name}} Images</h3>

      @endif
      <div class="col-md-12 mb-3">
        <form action="" method="post" enctype="multipart/form-data" id="add-hotel-img">
          <div class="row mb-3">
            <!-- Add a row div to contain the columns -->
            <div class="mt-3 col-md-12">

              <input type="hidden" value="{{$getbusiness[0]->id}}" name="id">
              <input type="hidden" value="{{$getbusiness[0]->bid}}" name="bid"> 
              <input type="file" id="fileInput" style="display:none;" name="image">
              <label for="fileInput" class="custom-button">+Add More Photos</label>
              <button type="submit" class="btn btn-dark upload-button">Upload</button>
              <p class="up-text d-none" style="color:green;">uploading...</p>
              <p id="fileName">Selected Image: </p>
              <img id="previewImage" style="max-width: 100%; max-height: 300px; display: none;">

              <!-- <p id="fileName">Selected File: </p> -->
              <h6 class="mt-3 photo-desc" style="text-align: center;">Photos appear on your {{$getbusiness[0]->name}}
                page</h5>
                <!-- <input type="file" id="fileInput" style="display: none"> -->

                <!-- <div class="box-border">
           

            <img src="{{asset('business/images-icon.svg')}}" alt="" height="35"><input type="file"> +Add More Photos
            
            </div> -->
            </div>

        </form>

      </div>

      <hr>

      <div>
        <div class="row mb-3">
          <!-- Add a row div to contain the columns -->
          <h6 class="mb-3">Custom Images </h6>
            <div class="show-img">
<?php // return print_r($getimage);?>
            @if(!$getimage->isEmpty())
              @foreach($getimage as $getimages)
                <div class="image-wrapper mt-3 mr-3" style="display: inline-block; position: relative;margin-left: 16px;">
                  <img src="https://s3-us-west-2.amazonaws.com/s3-travell/hotel-images/{{$getimages->image}}" alt="" class="image" style="width: 323px;height: 231px;">
                  <span class="delete-hotel-image" data-id="{{ $getimages->id }}" data-restid="{{ $getimages->hotelid }}" style="position: absolute; cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="30" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
                      <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                      <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                    </svg> 
                  </span>
                </div>
              @endforeach
            @else
              Images not found.
            @endif

         
            </div>

          <h6 class="mt-3">All Images </h6>

          <div class="">  

            @if(!$getbusiness->isEmpty())
            @foreach($getbusiness as $val)



            <?php
            
            $photos = json_decode($val->photos, true);
           
           $firstPhotoUrl = !empty($photos) ?  : '';
            $firstPhotoUrl ="";
            if(!empty($photos)){
              $firstPhotoUrl = $photos[0]['url']; 
            }
            
       ?>


            <div class="">
              @if($firstPhotoUrl !="")
              @foreach($photos as $photo)
              <img src="{{$photo['url']}}" alt="" class="usericon img-fluid "
                style="width: auto; margin-right: 4px; margin-top: 6px;">

              @endforeach
              @endif
             
           
              

            </div>


            @endforeach
            @endif
          </div>

         

        </div>
      </div>



    </div>
  </div>

  @include('footer')
  <!-- Button trigger modal -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>


  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <script src="{{ asset('/js/business.js')}}"></script>

  <script>
  document.getElementById('fileInput').addEventListener('change', function() {
    var fileInput = this;
    var fileName = fileInput.value.split('\\').pop(); // Get the file name without the path
    document.getElementById('fileName').innerText = 'Selected Image: ';

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



  <!-- <script src="{{ asset('/js/custom.js')}}"></script> -->
  <!-- end nav js -->


</body>

</html>