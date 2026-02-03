@if(!$getbusiness->isEmpty())
<h5 class="mb-3">General information <span style="margin-left: 267px;
    font-size: medium;"> <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">edit</a></span>
</h5>
<hr class="mb-3" style="margin: auto;">
<h5>Business name</h5>
<p>{{$getbusiness[0]->bname}}</p>
<h5>Business description</h5>
<p>@if($getbusiness[0]->About !=""){{$getbusiness[0]->About}} @else <a class="nav-link" data-bs-toggle="modal"
    data-bs-target="#exampleModalss">+add description</a>
  @endif</p>
<h5>Timings</h5>
<p>@if($getbusiness[0]->Timings !=""){{$getbusiness[0]->Timings}} @else <a class="nav-link" data-bs-toggle="modal"
    data-bs-target="#exampleModalss">+add timing</a>
  @endif</p>

<h5>Price Range</h5>
<p>@if($getbusiness[0]->PriceRange !=""){{$getbusiness[0]->PriceRange}} @else <a class="nav-link" data-bs-toggle="modal"
    data-bs-target="#exampleModalss">+add Price Range</a>
  @endif</p>

<h5>Menu</h5>
<p>@if($getbusiness[0]->MenuLink !="") <a href="{{$getbusiness[0]->MenuLink}}">{{$getbusiness[0]->MenuLink}}</a> @else
  <a class="nav-link" data-bs-toggle="modal" data-bs-target="#exampleModalss">+Menu Link</a>
  @endif</p>

  @endif