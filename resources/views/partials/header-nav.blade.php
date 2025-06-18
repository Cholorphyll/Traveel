 <!-- start heaader  -->
 <script src="{{ asset('/js/welcome.js')}}"></script>
 @if(!empty($searchresults[0]->fullName))
 @php $exp = explode(',',$searchresults[0]->fullName);
 $ctName = $exp[0];
 $countryname = $exp[1];
 @endphp
 @endif
 <?php
$placeholder = '';
if (!empty($searchresult[0]->cityName)) {
    $placeholder = $searchresult[0]->cityName;
} elseif (!empty($searchlocation)) {
    $placeholder = substr($searchlocation, 0, 4);
} elseif (!empty($searchresults[0]->fullName)) {
    $placeholder = $ctName;
} elseif (!empty($searchresult[0]->Name)) {
    $placeholder = $searchresult[0]->Name;
} else {
    $placeholder = 'Delhi';
}
?>

 <script>
var placeholderValue = '<?php echo $placeholder; ?>';
 </script>
 <header class=" fixed-top bg-white">

   <nav class="navbar navbar-expand navbar-light ">
     <div class="container">
       <a class="navbar-brand" href="/">
         <img src="{{asset('/images/logo.png')}}" width="30px" alt="logo">
       </a>
       <div class="collapse navbar-collapse" id="collapsibleNavId">
         <div class="search-box-click d-flex align-items-center position-relative mx-lg-0 mx-auto">
           <div
             class="ms-lg-4  search-box d-flex justify-content-center align-items-center rounded-pill position-relative">

  <input type="text" id="searchlocation" type="search" value="{{request('search')}}"  name="search" autofocus  class="rounded-pill text-capitalize form-control border-0 fs-22 "
                            placeholder=" @if(!empty($searchresult[0]->cityName)) {{$searchresult[0]->cityName}} @elseif(!empty($searchlocation)) {{substr($searchlocation, 0, 4)}} @elseif(!empty($searchresults[0]->fullName)) {{$ctName}} @elseif(!empty($searchresult[0]->Name)) {{$searchresult[0]->Name}}  @else Delhi @endif">
                        <div class="search-info  text-secondary fs-16"><span class="d-none d-lg-inline">   @if(!empty($searchresult[0]->address)) {{substr($searchresult[0]->address, 0, 10)}},
                 @elseif(!empty($searchresults[0]->Address)) {{substr($searchresults[0]->Address, 0, 10)}},
                 @elseif(!empty($searchresults[0]->address)) {{substr($searchresults[0]->address, 0, 10)}},
                 @elseif(!empty($searchresult[0]->Address)) {{substr($searchresult[0]->Address, 0, 10)}},

                 @else NIBM rd, @endif

                 @if(!empty($searchresult[0]->cityName)) {{$searchresult[0]->cityName}},
                 @elseif(!empty($searchlocation)) {{$searchlocation}},
                 @elseif(!empty($searchresults[0]->fullName)){{$ctName}},
                 @elseif(!empty($searchresult[0]->Name))
                 {{$searchresult[0]->Name}}
                 @else Delhi,
                 @endif </span>

               @if(!empty($searchresults[0]->CountryName)) {{$searchresults[0]->CountryName}}
               @elseif(!empty($searchresults[0]->fullName))
               {{$countryname}}

               @elseif(!empty($searchresult[0]->countryName))
               {{$searchresult[0]->countryName}}

               @else
               India
               @endif<i class="ms-1 fa text-main fa-angle-down"></i></div>
                        <div class="search-box-info d-none bg-white px-4 b-20 shadow-1 position-absolute">
                        <p class="small my-3" id="recent-search">@if (Session::has('lastsearch')) Recent Searches @else POPULAR DESTINATIONS @endif</p>
                            <div id="loc-list"></div>

           
           </div>
           <span class="position-absolute d-none small close bg-e rounded-circle px-2 text-dark " role="button"
             style="right: 10px; top: 20%;">x</span>
         </div>
         <ul class="navbar-nav ms-md-5 d-none d-md-flex  mt-2 mt-lg-0">
           <li class="nav-item  mx-3 active">
             <a class="nav-link" href="/">Explore</a>
           </li>
           <li class="nav-item mx-3 ">
           <?php
       $slug ="";
      
       if(!empty($tplocname)){
            $ct = $tplocname[0]->cityName;
            $cout = $tplocname[0]->countryName;
            $slug = $ct .'-'.$cout;
       } ?>
             <a class="nav-link" 
               href="@if(!empty($searchresults[0]->tp_location_mapping_id)) {{route('hotel.list',[$searchresults[0]->tp_location_mapping_id.'-'.strtolower($slug)])}} @else {{route('hotel.list',['674'.'-'.'fuegen-austria'])}}  @endif">Hotels</a>
           </li>
         </ul>
         <div class="account-btn rounded-pill ms-auto d-none d-lg-flex  border p-2  ">
           <a href="#">
             <img class="px-1" src="{{asset('/images/accountperson.png')}}" alt="account">
           </a>
           <a href="#">
             <img class="px-1" src="{{asset('/images/accountbars.png')}}" alt="account">
           </a>
         </div>
       </div>
     </div>
   </nav>
 </header>
 <!-- bottomnavbar sm screens -->
 <div class="bottom-nav fixed-bottom d-md-none">
   <div class="d-flex align-items-center p-2  bg-white mx-2 justify-content-around rounded-pill border shadow">
     <a href="explore.html"
       class="active text-decoration-none d-flex flex-column justify-content-center align-items-center">
       <img width="24px" height="24px" src="{{asset('/images/explore.png')}}" alt="explore">
       <span href="#" class="mt-2">Explore </span>
     </a>
   
     <a href="hotel.html" class="text-decoration-none d-flex flex-column justify-content-center align-items-center">
       <img width="24px" height="24px" src="{{asset('/images/hotels.png')}}" alt="hotels">
       <?php
       $slug ="";
       if(!empty($tplocname)){
        $ct = $tplocname[0]->cityName;
        $cout = $tplocname[0]->countryName;
        $slug = $ct .'-'.$cout;
       } ?>
       <span href="@if(!empty($searchresults[0]->tp_location_mapping_id)) {{route('hotel.list',[$searchresults[0]->tp_location_mapping_id.'-'.strtolower($slug)])}} @endif" class="mt-2">Hotels</span>
     </a>
     <a href="#" class="text-decoration-none d-flex flex-column justify-content-center align-items-center">
       <img width="24px" height="24px" src="{{asset('/images/tips.png')}}" alt="tips">
       <span href="#" class="mt-2">Trips </span>
     </a>
     <a href="#" class="text-decoration-none d-flex flex-column justify-content-center align-items-center">
       <img width="24px" height="24px" src="{{asset('/images/profile.png')}}" alt="profile">
       <span href="#" class="mt-2">Profile </span>
     </a>
   </div>
 </div>