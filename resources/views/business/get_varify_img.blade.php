 @if(!$getbus->isEmpty() && $getbus[0]->varify_image == 1)
            <div><img src="{{ asset('images/check.png') }}" alt="" style="width: 84px;"></div>
            @else
            <span style="margin-top: 108px;"></span>
            @endif
            <a class="form-control  btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal2"
              role="button" style="width: inherit;">Photo ID</a>