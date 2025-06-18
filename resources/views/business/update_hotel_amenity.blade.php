
              <h5 class="mb-3">AMENITIES <span style="margin-left: 354px;
    font-size: medium;"> <a  class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal1">edit</a></span></h5>
              <hr class="mb-3" style="margin: auto;">
              <h5>Property amenities</h5>
              <p>@if($getbusiness[0]->amenities !=""){{$getbusiness[0]->amenities}} @else
             
                 <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal1">
                  +add amenities</a>  @endif</p>
              <!-- <h5>Accessibility</h5>
              <p>+add a brand</p> -->
              <h5>Languages</h5>
              <p>@if($getbusiness[0]->Languages !=""){{$getbusiness[0]->Languages}} @else  <a  class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal1">+add Languages</a>  @endif</p>
              <h5>Room features</h5>
              <p>@if($getbusiness[0]->room_aminities !=""){{$getbusiness[0]->room_aminities}} @else  <a  class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal1">+add Room aminities</a>  @endif</p>