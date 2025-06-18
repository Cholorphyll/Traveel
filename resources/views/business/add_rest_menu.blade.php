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
  <title>Business - Restaurant</title>
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
  <link rel="stylesheet" href="{{ asset('/css/business_index.css')}}">
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
    <h5 class="mt-5 "><a href="{{route('view_rest_business',[$getbusiness[0]->bid,$getbusiness[0]->bslug])}}">  <span class="l-arrow"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><!--!Font Awesome Free 6.5.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.--><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l160 160c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L109.2 288 416 288c17.7 0 32-14.3 32-32s-14.3-32-32-32l-306.7 0L214.6 118.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-160 160z"/></svg></span><span class="back">back</span> </a></h5>
      @if(!$getbusiness->isEmpty())

      <h3 style="color:#ff601f; text-align:center;">Manage {{$getbusiness[0]->Title}} Menu</h3>

      @endif
      <div class="col-md-12 mb-3">
        <form action="" method="post"  id="add-rest-menu">
          <div class="row mb-3">
            <!-- Add a row div to contain the columns -->
            <div class="mt-3 col-md-4">

              <input type="hidden" value="{{$getbusiness[0]->RestaurantId}}" name="id">
              <input type="hidden" value="{{$getbusiness[0]->bid}}" name="bid"> 
              <h4 >Add Menu</h4>
              <!-- <textarea name="menuitems" id="" class="form-control" cols="5"  rows="6"></textarea> -->
              
              <!-- <p>item 1</p>
              <input type="text"  class="form-control">
              <p>item 2</p>
              <input type="text"  class="form-control">
              <p>item 3</p>
              <input type="text"  class="form-control"> -->

              <div id="inputContainer">
              @if(!$getbusiness->isEmpty())
    @if($getbusiness[0]->Menu !="")
        <?php $Menu = json_decode($getbusiness[0]->Menu, true); ?>
        <div id="inputContainer">
            @foreach($Menu as $index => $item)
                <div class="input-group mb-3 mt-3">
                    <p class="marginr-10">item {{ $index + 1 }}</p>
                    <input type="text" class="form-control ml-3" name="menuitems[]" value="{{ $item['menuitem'] }}">
                </div>
            @endforeach
        </div>
    @endif
@else
    <div id="inputContainer">
        <div class="input-group mb-3 mt-3">
            <p class="marginr-10">item 1</p>
            <input type="text" class="form-control ml-3" name="menuitems[]">
        </div>
    </div>
@endif


              </div>
              <button id="addMoreButton" class="btn btn-dark mt-3">Add more item</button>



              <button class="btn btn-dark mt-3">Submit</button>
             
            </div>

        </form>

      </div>

      <hr>

      <div>
        <div class="row mb-3">
          <!-- Add a row div to contain the columns -->
          <h6 class="mb-3">Menu list </h6>
            <div class="show-menu_items">
            
              @if(!$getbusiness->isEmpty())
                @if($getbusiness[0]->Menu !="")
                  <?php $Menu = json_decode($getbusiness[0]->Menu, true); ?>
                    <ol>
                        @foreach($Menu as $item)
                            <li>{{ $item['menuitem'] }}</li>
                        @endforeach
                    </ol>
                @else
                  <p>Menu not found.</p>
                @endif
                  
              @else
                  <p>Menu not found.</p>
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
<script>
document.getElementById('addMoreButton').addEventListener('click', function () {
    var inputContainer = document.getElementById('inputContainer');
    var lastItem = inputContainer.querySelectorAll('.input-group').length; // Get the current number of items
    var newInputGroup = document.createElement('div');
    newInputGroup.className = 'input-group mb-3 mt-3';
    newInputGroup.innerHTML = `
        <p class="marginr-10">item ${lastItem + 1}</p>
        <input type="text" class="form-control ml-3" name="menuitems[]">
    `;
    inputContainer.appendChild(newInputGroup);
});
</script>

<style>
.marginr-10 {
    margin-right: 10px;
}
.ml-3 {
    margin-left: 10px;
}
</style>



  <!-- <script src="{{ asset('/js/custom.js')}}"></script> -->
  <!-- end nav js -->


</body>

</html>