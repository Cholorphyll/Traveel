 <h5 class="mb-3">Contact Link <span style="margin-left: 354px;
    font-size: medium;"> <a  class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal2">edit</a></span></h5>
              
              <hr class="mb-3" style="margin: auto;">
              <h5>Phone</h5>
              <p>@if($getbusiness[0]->Phone !=""){{$getbusiness[0]->Phone}} @else <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal2">+add phone number</a>  @endif</p>
              <h5>Email</h5>
              <p> @if($getbusiness[0]->Email !=""){{$getbusiness[0]->Email}} @else  <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal2">+add Email</a>  @endif</p>

              <h5>Website</h5>
              <p> @if($getbusiness[0]->Website !=""){{$getbusiness[0]->Website}} @else  <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModal2">+add website</a>  @endif</p>