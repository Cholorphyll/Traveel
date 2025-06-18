
<h4 class="mb-4">Welcome, {{$getdata[0]->FirstName}} {{$getdata[0]->LastName}}</h4>
              <p class=""> {{$getdata[0]->Username}}</p>
              <p class=""> {{$getdata[0]->Email}}</p>
              <p class="">Joined on {{ date('Y-m-d', strtotime($getdata[0]->CreatedOn)) }}</p>
              @if($getdata[0]->lName !="")
              <p class="">Lives in 
                <br>
               <span>{{ $getdata[0]->lName }}, {{ $getdata[0]->cName }}</span> 
              </p>
              @endif
              @if($getdata[0]->lName !="")
              <p class=""><b>About</b> 
              <br>
                {{ $getdata[0]->Bio }}
                <br>
                <span>Lives in {{ $getdata[0]->lName }}, {{ $getdata[0]->cName }}</span>
              </p>
              @endif