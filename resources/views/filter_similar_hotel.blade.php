   


     @if(!empty($gethotel['result']))
    @foreach($gethotel['result'] as $index => $Shotle)
  
    <div class="col-md-6 @if($index >= 2) d-none hidden-list @endif">




      
         <!-- Card START -->
         <div class="card card-img-scale border-0 overflow-hidden bg-transparent">
           <!-- Image and overlay -->
           <div class="card-img-scale-wrapper rounded-3">
             <img src="{{ asset('/images/hotel/hotel.png')}}" class="card-img" alt="hotel image">

           </div>

           <!-- Card body -->
           <div class="">
             <!-- Title -->
             <div class="d-flex align-items-center justify-content-between">
               <a href="{{$Shotle['fullUrl']}}"
                 class="stretched-link text-decoration-none fs18 "><b>{{$Shotle['name']}}</b></a>

             </div>
             <div class="d-flex justify-content-between align-items-start locationandothers">
               <ul class="d-flex my-3 flex-wrap">
                 <!-- <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                   <span>{{$Shotle['guestScore']}}</span>
                 </li> -->
                 <li class=" d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>

                   <span><a href="" class="acme">{{$Shotle['guestScore']}} Guest Score</a></span>
                 </li>
               </ul>


             </div>
             <a href="" class="mb-12 d-block acme">{{$Shotle['address']}} </a>

             <button type="button"
               class="btn btn-outline-secondary btn-dark-outline mt-44">${{$Shotle['price']}}</button>

           </div>
         </div>
         <!-- Card END -->
       </div>

       @endforeach
       @endif