<div class="nearby-restaurant mb-50">
                    <div class="attraction mb-15">
                        <img src="{{asset('/images/forks.svg')}}" alt="">
                        <span>
                            Restaurant
                        </span>
                    </div>
                    

                    <div class="row align-items-center">
                    @if(!$getrest->isEmpty())
                            @foreach($getrest as $rest)
                         <div class="col-4 mb-3">   <a href="{{route('restaurant_detail',[$rest->RestaurantId])}}" class=" text-decoration-none  "> <img src="{{asset('/images/unsplash_QGPmWrclELg.png')}}" alt=""
                                class="restaurant-img w-100"></a></div>
                            
                        <div class="col-8 ps-2 d-flex align-items-start justify-content-between"  >

                          
                            <div>
                                <div class="mb-4px fw-700" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)"><b>  <a href="{{route('restaurant_detail',[$rest->RestaurantId])}}" class=" text-decoration-none  ">  {{$rest->Title}}</a></b></div>
                                <div class="text-neutral-2 mb-4px">{{$rest->Address}}</div>
                                <div class="text-neutral-2">{{$rest->PriceRange}} </div>
                            </div>
                        

                            <li class=" d-flex align-items-center fs12"><i class="fa fa-heart" aria-hidden="true"
                                    style="margin-right: 6px;"></i>
                                <span>89%</span>
                            </li>
                        </div>
                        @endforeach
                        @else
                             <p>Restaurant not available.</p>
                        @endif

                    </div>



                </div>


                <div class="attraction mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                        <path
                            d="M5.25 10.9375H8.75V12.9792C8.75 13.1339 8.68854 13.2822 8.57915 13.3916C8.46975 13.501 8.32138 13.5625 8.16667 13.5625H5.83333C5.67862 13.5625 5.53025 13.501 5.42085 13.3916C5.31146 13.2822 5.25 13.1339 5.25 12.9792V10.9375Z"
                            stroke="#6A6A6A" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.375 10.9375H9.625" stroke="#6A6A6A" stroke-width="0.875" stroke-linecap="round"
                            stroke-linejoin="round" />
                        <path d="M5.68677 10.9382L4.86719 8.8125" stroke="#6A6A6A" stroke-width="0.875"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.3125 10.9382L9.13208 8.8125" stroke="#6A6A6A" stroke-width="0.875"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M1.75 4.8125C1.75 5.38703 1.8858 5.95594 2.14963 6.48674C2.41347 7.01754 2.80018 7.49984 3.28769 7.90609C3.7752 8.31235 4.35395 8.63461 4.99091 8.85447C5.62787 9.07434 6.31056 9.1875 7 9.1875C7.68944 9.1875 8.37213 9.07434 9.00909 8.85447C9.64605 8.63461 10.2248 8.31235 10.7123 7.90609C11.1998 7.49984 11.5865 7.01754 11.8504 6.48674C12.1142 5.95594 12.25 5.38703 12.25 4.8125C12.25 4.23797 12.1142 3.66906 11.8504 3.13826C11.5865 2.60746 11.1998 2.12516 10.7123 1.71891C10.2248 1.31265 9.64605 0.990391 9.00909 0.770527C8.37213 0.550663 7.68944 0.4375 7 0.4375C6.31056 0.4375 5.62787 0.550663 4.99091 0.770527C4.35395 0.990391 3.7752 1.31265 3.28769 1.71891C2.80018 2.12516 2.41347 2.60746 2.14963 3.13826C1.8858 3.66906 1.75 4.23797 1.75 4.8125Z"
                            stroke="#6A6A6A" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round" />
                        <path
                            d="M4.375 4.8125C4.375 5.97282 4.65156 7.08562 5.14384 7.90609C5.63613 8.72656 6.30381 9.1875 7 9.1875C7.69619 9.1875 8.36387 8.72656 8.85615 7.90609C9.34844 7.08562 9.625 5.97282 9.625 4.8125C9.625 3.65218 9.34844 2.53938 8.85615 1.71891C8.36387 0.898436 7.69619 0.4375 7 0.4375C6.30381 0.4375 5.63613 0.898436 5.14384 1.71891C4.65156 2.53938 4.375 3.65218 4.375 4.8125Z"
                            stroke="#6A6A6A" stroke-width="0.875" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <span>
                        Experience
                    </span>
                </div>

                @if(!$experience->isEmpty())
                <div class="card mb-50 card-img-scale border-0 overflow-hidden bg-transparent">
                    <!-- Image and overlay -->
                    <div class="card-img-scale-wrapper rounded-3">

                        <img src="{{ asset('/images/pinned.svg') }}" alt="" class="pinned">
                        <!-- Image -->
                        <img src="{{ asset('/images/image 24.png') }}" class="card-img br-10 mb-12" alt="hotel image">

                    </div>

                    <!-- Card body -->
                    <div class="">
                        <!-- Title -->
                        <div class="d-flex align-items-center justify-content-between">
                            <a href="hotel-detail.html" class="stretched-link text-decoration-none fs18 "><b>{{$experience[0]->Name}}</b></a>
                            <li class="d-flex align-items-center"><i class="fa  fa-heart" aria-hidden="true"></i>
                                <span>89%</span>
                            </li>
                        </div>

                        <a href="" class=" d-block text-decoration-none text-neutral-2">7 Hours</a>

                        <a href="" class="mb-12 d-block text-decoration-none text-neutral-2"> From {{$experience[0]->adult_price}} per person</a>


                    </div>

                </div>
                @else
                <p>Experience not available.</p>
                @endif


            </div>

        