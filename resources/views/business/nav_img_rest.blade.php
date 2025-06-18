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
                        <!-- <li><a class="dropdown-item" href="{{route('edit_business_profile')}}">Edit Profile</a></li> -->
                        <li><a class="dropdown-item" href="{{route('logout')}}">Logout</a></li>
                    
                    </ul>
                </div>

                </li>
                <!-- </span> -->
             @else
            <li class="nav-item active">
        
                <a class="form-control" data-bs-toggle="modal" data-bs-target="#exampleModal1"  role="button" style="background: #CB4C14;color: white;     text-decoration: none;border: none;">Sign in</a>
                  
             </li>
             @endif
             <!-- <li class="nav-item active">
               <p> <a  href="{{route('user_login')}}" style="text-decoration: none;">dashboard</a></p>
                  
             </li> -->