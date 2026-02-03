  @if(!$getbusiness->isEmpty())
  <h5 class="mb-3">General information <span style="margin-left: 267px;
    font-size: medium;"> <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">edit</a></span></h5>
  <hr class="mb-3" style="margin: auto;">
  <h5>Business name</h5>
  <p>{{$getbusiness[0]->bname}}</p>
  <h5>Brand Name</h5>
  <p> <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">+add a brand</a></p>
  <h5>Total number of rooms and suites</h5>
  <p>@if($getbusiness[0]->cntRooms !=""){{$getbusiness[0]->cntRooms}} @else <a href="">+add rooms</a> @endif</p>
  <h5>Business description</h5>
  <p>@if($getbusiness[0]->about !=""){{$getbusiness[0]->about}} @else <a href="">+add description'</a> @endif</p>
  @endif