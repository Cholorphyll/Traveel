<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-fullwidth w-100">
            <div class="modal-content p-0">
                <div class="modal-body">
                    <nav class="navbar navbar-expand-sm align-items-start">

                        <div class="container my-3 d-flex position-relative align-items-start">
                            <a class="navbar-brand m-0" href="{{route('homepage')}}"><img
                                    src="{{ asset('images/logo.png') }}" alt="Travell" class="navlogo"></a>
                           
                            <div class=" navbar-collapse d-flex align-items-start" id="collapsibleNavId">
                                <div class="mx-auto smalldevfullwidth">
                                    <ul class="nav nav-tabs border-0 w-100 justify-content-center w-50 align-items-center"
                                        id="myTab" role="tablist">
                                        <li class="nav-item border-0 border-to-right " id="explore-tabser"
                                            role="presentation">
                                            <button class="nav-link tab-switch active" id="explore-tab"
                                                data-bs-toggle="tab" data-bs-target="#explore-tab-pane" type="button"
                                                role="tab" aria-controls="explore-tab-pane" aria-selected="true"><span
                                                    class="tab-switch ">
                                                    Explore
                                                </span></button>
                                        </li>
                                        <li class="nav-item border-0" role="presentation">
                                            <button class="nav-link tab-switch" id="profile-tab" data-bs-toggle="tab"
                                                data-bs-target="#profile-tab-pane" type="button" role="tab"
                                                aria-controls="profile-tab-pane" aria-selected="false"
                                                tabindex="-1">Hotels</button>
                                        </li>
                                    </ul>
                                    <div class="tab-content typeahed mb-0" id="myTabContent">
                                        <div class="tab-pane active " id="explore-tab-pane" role="tabpanel"
                                            aria-labelledby="explore-tab" tabindex="0">
                                            <div class="explore-search d-flex">
                                                <img src="{{ asset('images/search.svg') }}" alt="">
                                                <input type="text" id="searchlocation" type="search"
                                                    value="{{request('search')}}" name="search" placeholder="Search Destination" autocomplete="off">
                                                <div
                                                    class="recent-his search-box-info  d-none bg-white px-4 b-20 shadow-1 position-absolute">
                                                   
                                                    <p id="loc-list" class="px-4 autoCompletewrapper"></p>
                                                </div>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade " id="profile-tab-pane" role="tabpanel"
                                            aria-labelledby="profile-tab" tabindex="0">
                                            <div class="search-filter remove-highlight d-md-flex">

                                                <div class="search-locations">
                                                    <img src="{{ asset('images/search.svg') }}"
                                                        class="search-iconformobile d-md-none" alt="">
                                                    <label for="checkout" class="label">Where</label>
                                                    <div class="autoComplete_wrapper">
                                                        <div class="autoComplete_wrapper" role="combobox"
                                                            aria-owns="autoComplete_list_1" aria-haspopup="true"
                                                            aria-expanded="false">
                         <input id="searchhotel" type="text" tabindex="1" placeholder="&#xF002; Search"  autocomplete="off">
              <div class="hotel_recent_his search-box-info  d-none bg-white px-4 b-20 shadow-1 position-absolute">
                <!-- <p class="small my-3" id="recent-search">@if (Session::has('lastsearch')) RECENTLY VIEWED @else POPULAR
                  DESTINATIONS @endif</p> -->
                <p id="hotel_loc_list" class="px-4 autoCompletewrapper"></p>
              </div>
															  <span  id="slug" class="d-none"></span> 
													      <span  id="hotel" class="d-none"></span> 
                                                            <span  id="location_id" class="d-none">@if(isset($gethotellistiid) && !$gethotellistiid->isEmpty()){{$gethotellistiid[0]->locationId}} @elseif(isset($hlid) && $hlid !="") {{$hlid}} @elseif(isset($getloclink) && !$getloclink->isEmpty()) {{$getloclink[0]->LocationId }}@else 373 @endif</span>
                                                        </div>
                                                    </div>
                                                </div>
											<?php date_default_timezone_set('Asia/Kolkata'); 
											
												  $checkinDate = date('Y-m-d', strtotime(' +1 day'));  
      										     $checkoutDate = date('Y-m-d', strtotime(' +4 day'));  
												?>
                                                <div class="t-datepicker">
                                                    <div class="t-check-in">
                                                        <div class="t-dates t-date-check-in">➜<label
                                                                class="t-date-info-title">Add
                                                                Dates</label></div><input type="hidden"
                                                            class="t-input-check-in"  
                        value="{{ $checkinDate}}" name="t-start" id="checkindate">
                                                    </div>
                                                    <div class="t-check-out">
                                                        <div class="t-dates t-date-check-out">➜<label
                                                                class="t-date-info-title">Add
                                                                Dates</label></div><input type="hidden"
                                                            class="t-input-check-out" value="{{ $checkoutDate}}" name="t-end" id="checkoutdate">
                                                    </div>
                                                </div>

                                                <div class="dropdown-custom">

                                                    <div class="dropdown-custom-toggle ">
                                                        <img src="images/usergrey.png" class="user-guests d-md-none"
                                                            alt="">
                                                        <label for="checkout" class="label">Who</label>
                                                        <input type="number" id="totalguests"
                                                            class="border-0 totalguests" placeholder="2" value="2"
                                                            readonly="">
                                                    </div>
                                                    <ul class="dropdown-menu p-0 border-0">
                                                        <div class="rooms-num room-info b-20 border bg-white">
                                                            <div class="p-24">
                                                                <div
                                                                    class="adults counter mb-30 d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <p class="person">Adult</p>
                                                                        <p class="age"> Ages 13 or above</p>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <span role="button"
                                                                            class="decrement dec">-</span>
                                                                        <input type="number"
                                                                            class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                                                                            placeholder="0" readonly="">
                                                                        <span role="button"
                                                                            class="adultincrement incdec">+</span>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="adults counter mb-30 d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <p class="person">Children</p>
                                                                        <p class="age"> Ages 2-12</p>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <span role="button" id="childrenSubtract"
                                                                            class="decrement dec">-</span>
                                                                        <input type="number"
                                                                            class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                                                                            placeholder="0" readonly="">
                                                                        <span role="button" id="childrenAdd"
                                                                            class="adultincrement incdec">+</span>
                                                                    </div>
                                                                </div>

                                                                <div
                                                                    class="adults counter d-flex justify-content-between align-items-center">
                                                                    <div>
                                                                        <p class="person">Infants</p>
                                                                        <p class="age"> Under 2</p>
                                                                    </div>
                                                                    <div class="d-flex align-items-center">
                                                                        <span role="button"
                                                                            class="decrement dec">-</span>
                                                                        <input type="number"
                                                                            class="border d-flex align-items-center rounded-3 border-1 border-dark inputfield"
                                                                            placeholder="0" readonly="">
                                                                        <span role="button"
                                                                            class="adultincrement incdec">+</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div id="childrenDetails">

                                                            </div>

                                                        </div>
                                                    </ul>
                                                </div>

                                                <div class="search-icon d-none d-md-flex filter-chackinout" id="filterchackinout">
                                                    <div class="search" >
                                                        <i class="fa fa-search" aria-hidden="true" ></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                               
                            </div>
							 <ul class="navbar-nav mt-2 mt-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link p-0" href="#"> <img src="{{ asset('images/Frame 61.svg') }}" alt=""
                            class="usericon"></a>
                    </li>
                </ul>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>


    <nav class="navbar border-bottom-1 navbar-expand-sm static-navbar align-items-start">
        <div class="container my-3 d-flex">
            <a class="navbar-brand" href="{{route('homepage')}}"><img src="{{ asset('images/logo.png') }}"
                   alt="Travell" class="navlogo"></a>
          
            <div class="navbar-collapse d-flex align-items-center" id="collapsibleNavId">
                <div class="mx-auto">
                    <ul class="nav nav-tabs nav-tabser w-100 justify-content-center w-50 align-items-center" id="myTab"
                        role="tablist">

                        <li class="nav-item border-0 border-to-right smalldevloctaetrigger" role="presentation">
                            <div class="tab-content my-0">
                                <div class="tab-pane active show" id="explorer-tab-pane" role="tabpanel"
                                    aria-labelledby="explorer-tab" tabindex="0">
                                    <div class="defaultsearchvalue defaultsearch d-flex align-items-center"
                                        id="explore-tab" type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                        <img src="{{ asset('images/location.svg') }}" alt="">
										<div class="text4A4A4A fw-500">@if(isset($searchresult) && count($searchresult) > 0 && isset($searchresult[0]->Title) &&  $searchresult[0]->Title !="" )
										
											{{$searchresult[0]->Name}}
									@elseif(isset($searchresult) && count($searchresult) > 0 && isset($searchresult[0]->fullname) &&  $searchresult[0]->name !="fullname")
											{{$searchresult[0]->fullname}}

										@elseif(isset($breadcumb) && count($breadcumb) > 0 && isset($breadcumb[0]->LName))
											{{$breadcumb[0]->LName}}  
										@elseif(isset($lname) && $lname !="" )
											{{$lname}}
										@elseif(isset($neibhood_name) && $neibhood_name !="" )
                                        {{$neibhood_name}}
										@elseif(isset($fullname) && $fullname !="" )
											{{$fullname}}
										@else
											Hotels
										@endif
										</div> 
                                    </div>
                                </div>
                                <div class="tab-pane" id="hotels-tab-pane" role="tabpanel" aria-labelledby="hotels-tab"
                                    tabindex="0">
                                    <div class="defaultsearchvalue defaultsearchvalue2 d-flex align-items-center"
                                        id="explore-tab" type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#exampleModal">
                                        <img src="{{ asset('images/plave.svg') }}" alt="">
                                        <div class="hotelstabs"><span>@if(isset($searchresult) && count($searchresult) > 0 && isset($searchresult[0]->Title) &&  $searchresult[0]->Title !="")
											{{$searchresult[0]->Title}}
									@elseif(isset($searchresult) && count($searchresult) > 0 && isset($searchresult[0]->name) &&  $searchresult[0]->name !="")
											{{$searchresult[0]->name}}

										@elseif(isset($searchresults) && count($searchresults) > 0 && isset($searchresults[0]->LName))
											{{$searchresults[0]->LName}}  
										@elseif(isset($lname) && $lname !="" )
											{{$lname}}
										@elseif(isset($neibhood_name) && $neibhood_name !="" )
                                        	{{$neibhood_name}}
										@elseif(isset($fullname) && $fullname !="" )
											{{$fullname}}
										@else
											Hotels
										@endif</span> <span class="separator"></span>
                                            <span>{{$checkinDate}}-{{$checkoutDate}}</span>
                                            <span class="separator"></span> <span>2 guest</span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                        </li>
                        <li class="nav-item border-0 border-to-right " role="presentation">
                            <button class="nav-link tab-switch @if(!isset($type)) active @endif " id="explorer-tab" data-bs-toggle="tab"
                                data-bs-target="#explorer-tab-pane" type="button" role="tab"
                                aria-controls="explorer-tab-pane" aria-selected="true"><span class="tab-switch ">
                                    Explore
                                </span></button>
                        </li>
                        <li class="nav-item border-0" role="presentation">
                            <button class="nav-link @if(isset($type) && $type == 'hotel') active @endif tab-switch @if(!isset($hotelpage)) filter-chackinout @endif" id="hotels-tab" data-bs-toggle="tab"
                                data-bs-target="#hotels-tab-pane" type="button" role="tab"
                                aria-controls="hotels-tab-pane" aria-selected="false" tabindex="-1">
                                Hotels
							</button>
                        </li>
                    </ul>

                </div>

               
                
                <ul class="navbar-nav  mt-lg-0">
                <?php
                 if (session()->has('frontend_user')) {
                    $userData = session('frontend_user');
                    $Username = $userData['Username'];
                    $user_image = $userData['user_image'];
                     
                 }
                ?>
                @if (session()->has('frontend_user')) 
                <!-- <span class="getuser-nav"> -->
                <p class="" style="margin-top: 16px;
    margin-right: 19px;">{{$Username}}</p>
                    <li class="nav-item active ">
                    <div class="dropdown">
                        <a class="nav-link p-0  dropdown-toggle" href="#"  id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                             <img src="@if($user_image !='') https://s3-us-west-2.amazonaws.com/s3-travell/user-images/{{$user_image}}   @else {{ asset('images/Frame 61.svg') }} @endif" alt=""
                            class="usericon img-fluid rounded-circle" style="height: 49px;">
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                            <li><a class="dropdown-item" href="{{route('user_dashboard')}}">Dashboard</a></li>
                            <li><a class="dropdown-item" href="{{route('userlogout')}}">Logout</a></li>
                        
                        </ul>
                    </div>

                    </li>
                    <!-- </span> -->
                 @else
                <li class="nav-item active">
                    <a class="form-control" href="{{route('user_login')}}" role="button" style="background: #CB4C14;color: white;     text-decoration: none;">Sign in</a>
                      
                 </li>
                 @endif
                 <!-- <li class="nav-item active">
                   <p> <a  href="{{route('user_login')}}" style="text-decoration: none;">dashboard</a></p>
                      
                 </li> -->
                </ul>
            </div>
        </div>
    </nav>