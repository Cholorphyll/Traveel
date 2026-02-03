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
  .recent-his {
    margin-top: 53px;
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
            <p>{{$val->name}}</p>
            <span>{{$val->address}}</span>
            <p><b>Business: </b>@if($type == 'restaurant') Restaurant @elseif($type == 'sight') Sight @elseif($type == 'hotel') Hotel @endif</p>
          </div>



        </div>

        @endforeach
        @endif
        <hr>
        <div class="row">
          <div class="col-md-6 ml-5" style="text-align: center;">
            <a href="{{route('business_dashboard')}}"> <span>Not your business? Search again</span></a>
          </div>
          <div class="col-md-3" style="text-align: center;">
            <a class="form-control  btn btn-dark" data-bs-toggle="modal" data-bs-target="#exampleModal1"
              role="button">claim your listing</a>

          </div>
        </div>
      </div>
    </div>

    @include('footer')
    <!-- Button trigger modal -->





    <!-- Modal -->
    <div class="modal fade" id="exampleModal1" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog " style="margin-top: 118;margin-left: 644px;">
        <div class="modal-content" style="width: 843px;">
          <div class="col-md-12 mt-3">
            <div class="row">
              @if(!$getbusiness->isEmpty())
              @foreach($getbusiness as $val)
              <?php
               $firstPhotoUrl ="";
            if(isset($val->photos)){
              $photos = json_decode($val->photos, true);
          //   $firstPhotoUrl = !empty($photos) ?  : '';
             
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
              <div class="col-md-9">
                <p>{{$val->name}}</p>
                <span>{{$val->address}}</span>
              </div>

              <div class="col-md-1">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

            </div>

            @endforeach
            @endif

          </div>
          <hr>

          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-12">
                  <h5 class="modal-title" id="exampleModalLabel">Claim Your Listing</h5>

                </div>

              </div>
              <span class="mt-3">In order to increase security levels, prevent instances of fraud and ensure the best
                experience for all business representatives, Travell requires business registration and
                verification.</span>


            </div>
          </div>

          <form id="claim-list">
            @csrf
            <div class="modal-body">
              <div class="col-md-12 mt-3">
                <div class="row">
                  <div class="col-md-6">
                    <span>First Name *</span>
                    <input type="text" class="form-control" placeholder="First Name" name="name"
                      value="{{$user->username}}" required>
                      
                    <input type="hidden" class="form-control" placeholder="First Name" name="userid"
                      value="{{$user->id}}">
                      <input type="hidden" class="form-control"  name="business_id"
                      value="{{$getbusiness[0]->id}}">
                      <input type="hidden" class="form-control"  name="type"
                     value="{{$type}}"> 
                    <div class="validation-msg" id="fname-msg"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 mt-3">
                <div class="row">
                  <div class="col-md-6">
                    <span>Business Email*</span>
                    <input type="email" class="form-control" placeholder="Business Email" name="email"
                      value="{{$user->email}}" required>
                    <div class="validation-msg" id="email-msg"></div>
                  </div>
                  <div class="col-md-6">
                    <span>Business Phone *</span>
                    <input type="text" class="form-control" id="exampleInputEmail2" aria-describedby="emailHelp"
                      placeholder="Business Phone" name="business_phone" required>
                    <div class="validation-msg" id="business-phone-msg"></div>
                  </div>
                </div>
              </div>
              <div class="col-md-12 mt-3 mb-3">
                <div class="row">
                  <div class="col-md-6">
                    <span>Role at Business *</span>
                    <select class="form-select" name="role" required>
                      <option value="-1" selected="">Select one</option>
                      <option value="1">Owner</option>
                      <option value="0">General Manager</option>
                      <option value="2">Agency / Consultant</option>
                      <option value="7">Accounting / Finance</option>
                      <option value="6">Guest Services / Front Office</option>
                      <option value="8">Marketing</option>
                      <option value="9">Revenue Management</option>
                      <option value="10">Sales</option>
                      <option value="4">Other</option>
                    </select>
                    <div class="validation-msg" id="role-msg"></div>
                  </div>
                  <div class="col-md-6">
                    <span>Preferred Email Language *</span>
                    <select name="lang" class="form-control" required>
                      <option value="-1" selected="">Select one</option>
                      @foreach($language as $lang)
                      <option value="{{$lang->code}}">{{$lang->name}}</option>
                      @endforeach
                    </select>
                    <div class="validation-msg" id="lang-msg"></div>
                  </div>
                </div>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault1" required>
                <label class="form-check-label" for="flexCheckDefault1">
                  Get notified by email about new reviews, best practices, and more to help you improve your online
                  reputation and build your business.
                </label>
                <div class="validation-msg" id="check1-msg"></div>
              </div>
              <span class="mt-3 mb-3"><b>Please click the statements below to indicate you understand and accept these
                  terms.</b></span>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault2" >
                <label class="form-check-label" for="flexCheckDefault2">
                  I certify that I am an authorised representative or affiliate of this establishment and have the
                  authority to register as a business representative. The information I have entered into this form is
                  neither false nor fraudulent. I also understand that Tripadvisor may disclose my name and affiliation
                  to other verified representatives of this establishment.
                </label>
                <div class="validation-msg" id="check2-msg"></div>
              </div>
              <div class="form-check mt-3">
                <input class="form-check-input" type="checkbox" value="" id="flexCheckDefault3" required>
                <label class="form-check-label" for="flexCheckDefault3">
                  I have read and accept Tripadvisor's Terms of Use and Privacy Policy.
                </label>
                <div class="validation-msg" id="check3-msg"></div>
              </div>
              <div class="modal-footer">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary" id="loginform" style="height: 38px;">Submit</button>
                    </div>
                  </div>
                </div>
              </div>
          </form>

        </div>
      </div>
    </div>

    <!-- end model -->

    <!-- start model 2 -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog " style="margin-top: 118;margin-left: 644px;">
        <div class="modal-content" style="width: 843px;">
          <div class="col-md-12 mt-3">
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
                <img src="{{$firstPhotoUrl}}" alt="" class="usericon img-fluid rounded-circle" style="height: 69px;">
              </div>
              <div class="col-md-9">
                <p>{{$val->name}}</p>
                <span>{{$val->address}}</span>
              </div>

              <div class="col-md-1">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

            </div>

            @endforeach
            @endif

          </div>
          <hr>

          <div class="row">
            <div class="col-md-12">
              <div class="row">
                <div class="col-md-12">
                  <h5 class="modal-title" id="exampleModalLabel">Varify Your Identity</h5>

                </div>

              </div>
              <span class="mt-3">Identity verification ensures the highest levels of security and helps us to prevent fraud. Please choose one of the options below to confirm your identity.</span>


            </div>
          </div>

          <form id="claim-list">
            @csrf
            <div class="modal-body">
              <div class="col-md-12 mt-3">
                <div class="row">
                  <div class="col-md-6">
                  <a href="#"  class="btn btn-outline-success" tabindex="-1" role="button" aria-disabled="true">Primary link</a>
                  </div>
                </div>
              </div>
           
              <div class="modal-footer">
                <div class="col-md-12">
                  <div class="row">
                    <div class="col-md-2">
                      <button type="submit" class="btn btn-primary" id="loginform" style="height: 38px;">Submit</button>
                    </div>
                  </div>
                </div>
              </div>
          </form>

        </div>
      </div>
    </div>


    <!-- end model 2 -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.9.0/slick.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-lightbox/0.2.12/slick-lightbox.min.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
   

   





    <script src = "{{ asset('/css/hotel-css/t-datepicker.min.js')}}" >
    </script>
    <script src="{{ asset('/js/datepicker-homepage.js')}}"></script>
    <script src="{{ asset('/js/custom.js')}}"></script>
    <script src="{{ asset('/js/business.js')}}"></script>
    <!-- end nav js -->


</body>

		</html>