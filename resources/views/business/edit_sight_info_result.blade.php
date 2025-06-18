<h5 class="mb-3">General information <span style="margin-left: 267px;font-size: medium;">
                  @if($getbusiness[0]->varify_business != 0)
                  <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">edit</a>
                  @endif
                </span>
              </h5>
              <hr class="mb-3" style="margin: auto;">
              <h5>Business name</h5>
              <p>{{$getbusiness[0]->bname}}</p>
              <h5>Business description</h5>
              <p>@if($getbusiness[0]->About !=""){{$getbusiness[0]->About}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+add description'</a>
                @endif</p>
              <h5>Phone</h5>
              <p>@if($getbusiness[0]->Phone !=""){{$getbusiness[0]->Phone}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+add phone'</a>
                @endif</p>
              <h5>Must See</h5>
              <p>@if($getbusiness[0]->IsMustSee !="") @if($getbusiness[0]->IsMustSee == 1) Yes @else No @endif @else <a
                  class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">+Must See</a>
                @endif</p>
              <h5>Category</h5>
              <p>@if($getbusiness[0]->cname !="") {{$getbusiness[0]->cname}} @else <a class="nav-link"
                  data-bs-toggle="modal" data-bs-target="#exampleModalss">+Category</a>
                @endif</p>
            </div>