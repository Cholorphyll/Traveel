
@if(!empty($result) )

<!-- new code -->
@foreach($result as $resta)
<div class="tr-restaurant" data-restaurant-id="{{ $resta->RestaurantId }}">
                        <!-- Restaurant Information -->
                        
                        <div class="tr-row">
                            <div class="tr-image">
                                <a href="javascript:void(0);">
                                    <img loading="lazy" src="{{ asset('images/Hotel lobby-image.png') }}" alt="" width="108" height="130">
                                </a>
                            </div>
                            <div class="tr-restaurant-details">
                                <div class="tr-details">
                                    <h3>
                                        <a href="{{route('restaurant_detail', $resta->slugid.'-'.$resta->RestaurantId.'-'.str_replace(' ','-',$resta->Title) )}}" target="_blank"> {{$resta->Title}}</a>
                                    </h3>
                                    <div class="tr-location">{{$resta->Lname}}</div>
                                    <div class="tr-like-review">
                                        <div class="tr-heart">
                                            <svg width="12" height="11" fill="none">
                                                <path d="M5.996 2.29C5.03 1.21 3.42 0.92 2.207 1.91C0.997 2.9 0.826 4.55 1.777 5.73L5.996 9.63L10.215 5.73C11.166 4.55 11.016 2.89 9.785 1.91C8.553 0.93 6.962 1.21 5.996 2.29Z" fill="white" stroke="white"></path>
                                            </svg>
                                        </div>
                                        <div class="tr-ranting-percent">
                                        @if($resta->TAAggregateRating != "" &&
                                      $resta->TAAggregateRating !=
                                      0)<?php $result = rtrim($resta->TAAggregateRating, '.0') * 20;  ?>
                                      {{$result}}% @else
                                      --
                                      @endif
                                        </div>
                                    </div>

                                    <!-- Timings, Price Range, Category, and Features -->
                                    <div class="tr-more-inform">
                                        <ul>
                                            <li><span>Open</span> @if($resta->Timings != "") {{$resta->Timings}} @endif</li>
                                            <li>@if($resta->PriceRange != "") {{$resta->PriceRange}} @endif</li>
                                        </ul>
                                    </div>

                                    @if($resta->category !="")
                                    <?php $category = explode(',',$resta->category); ?>
                                        <div class="tr-more-inform">
                                            <ul>
                                              @foreach($category as $ct)
                                                    <li>{{ $ct }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if($resta->features !="")
                                    <?php $features = explode(',',$resta->features); ?>
                                        <div class="tr-delivery-type">
                                            <ul>
                                                @foreach($features as $ft)
                                                @if($ft =='delivery')
                                                  <li class="tr-yes">Delivery</li>
                                                  @elseif($ft =='Takeout')
                                                  <li class="tr-yes">Takeout</li>
                                                  @else
                                                  <li class="tr-yes">{{$ft}}</li>
                                                  @endif
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>
        @endforeach

@else
match not found.
@endif