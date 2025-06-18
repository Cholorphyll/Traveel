<x-app-layout>
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Edit Hotel Landing Page</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Hotels Landing Page</h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
              <div class="row justify-content-center">
                <div class="col-md-12">
                  <div class="white_shd full margin_bottom_30">
                    <div class="full graph_head">
                      <div class="heading1 margin_0">
                      </div>
                    </div>
                    @if ($message = Session::get('success'))
                    <div class="col-md-8 alert alert-success mt-3">
                      {{ $message }}
                    </div>
                    @endif
                    @if ($errors->any())
                    <div class="col-md-8 alert alert-danger mt 3">
                      <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                    @endif
                    @if ($message = Session::get('error'))
                    <div class="col-md-8 alert alert-danger mt-3">
                      {{ $message }}
                    </div>
                    @endif
                    <br>
                    <!-- Button trigger modal -->
                    <span type="button" class="float-right" style="float:right">
                      @if($getlanding->isEmpty())
                      <h4><u><a href="{{ route('add_landing_page',[request()->route('id')]) }}">Add Landing
                            Pages</a></u></h4>
                      @endif
                    </span>
                    <span class="getupdatedfaq">
                      @if(!$getlanding->isEmpty())

                      <!-- <form class="" action="{{route('update_faq')}}" method="POST"> -->
                      @csrf

                      <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">


                          <div class="form-group mt-3">
                            <span id="Success"></span>
                            <strong>Hotel</strong>
                            <div class="form-search form-search-icon-right">
                              <input type="text" id="search_attractionfaq" name="sightname"
                                value="{{ $getlanding[0]->name }}" class="search_attractionfaqs form-control rounded-3"
                                placeholder="" disabled>
                            </div>
                            <input type="hidden" name='attrid' id="selected_att_id"
                              value="{{ $getlanding[0]->hotelid }}" class="form-control rounded-3" required>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editHlanding(1)">Edit</span>
                              <strong>Landing Page Name</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question" data-original-value="{{ $getlanding[0]->Name }}"
                                id="name1" class="form-control rounded-3" disabled>{{ $getlanding[0]->Name }}</textarea>
                            </div>

                            <span id="buttonsContainer-1" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $getlanding[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updatelanding"
                                data-id="{{ $getlanding[0]->id }}" data-colid="1">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="cancelHLanding(1)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editHlanding(2)">Edit</span>
                              <strong>Page Slug</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question" data-original-value="{{ $getlanding[0]->Slug }}"
                                id="name2" class="form-control rounded-3" disabled>{{ $getlanding[0]->Slug }}</textarea>
                            </div>

                            <span id="buttonsContainer-2" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $getlanding[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updatelanding"
                                data-id="{{ $getlanding[0]->id }}" data-colid="2">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="cancelHLanding(2)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editHlanding(3)">Edit</span>
                              <strong>Meta Title</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question"
                                data-original-value="{{ $getlanding[0]->MetaTagTitle }}" id="name3"
                                class="form-control rounded-3" disabled>{{ $getlanding[0]->MetaTagTitle }}</textarea>
                            </div>

                            <span id="buttonsContainer-3" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $getlanding[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updatelanding"
                                data-id="{{ $getlanding[0]->id }}" data-colid="3">Save</button>

                              <button type="button" value="3" id="cancel-1" onclick="cancelHLanding(3)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editHlanding(4)">Edit</span>
                              <strong>Meta Description</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question"
                                data-original-value="{{ $getlanding[0]->MetaTagDescription }}" id="name4"
                                class="form-control rounded-3"
                                disabled>{{ $getlanding[0]->MetaTagDescription }}</textarea>
                            </div>

                            <span id="buttonsContainer-4" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $getlanding[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updatelanding"
                                data-id="{{ $getlanding[0]->id }}" data-colid="4">Save</button>

                              <button type="button" value="3" id="cancel-1" onclick="cancelHLanding(4)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editHlanding(5)">Edit</span>
                              <strong>About Landing Page</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question" data-original-value="{{ $getlanding[0]->About }}"
                                id="name5" class="form-control rounded-3"
                                disabled>{{ $getlanding[0]->About }}</textarea>
                            </div>
                            <span id="buttonsContainer-5" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $getlanding[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updatelanding"
                                data-id="{{ $getlanding[0]->id }}" data-colid="5">Save</button>

                              <button type="button" value="3" id="cancel-1" onclick="cancelHLanding(5)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>
                          </div>
                        </div>

                        <div class="row">
                          <div class="col-xs-8 col-sm-8 col-md-8">
                            <br>
                            <h5>Select Landing Page Type</h5>
                            <input type="radio" id="Attraction" name="page_type" value="Attraction" class="mt-3">
                            <label for="html">Attraction</label>
                            <input type="radio" id="Hotel" name="page_type" value="Hotel" class="margin-left" checked>
                            <label for="css">Hotel</label>
                            <input type="radio" id="Restaurent" name="page_type" value="Restaurent" class="margin-left">
                            <label for="javascript">Restaurent</label>
                            <input type="radio" id="Experience" name="page_type" value="Experience" class="margin-left">
                            <label for="javascript">Experience</label>


                            <!-- <div class="col-md-8 form-group mt-3">
                            <input type="radio" id="javascript" name="category" value="JavaScript" class="margin-left"
                              checked>
                            <label for="javascript">Category</label>
                            <div class="form-search form-search-icon-right mt-3">
                              <input type="text" name="search_value" id="search_hotelcategory"
                                class="form-control rounded-3" aria-describedby="emailHelp"
                                placeholder="Search Category">
                              <i class="ti ti-search"></i>
                            </div>
                            <span id="catlist"></span>
                          </div> -->
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editHlanding(6)">Edit</span>
                            <input type="hidden" value="{{request()->route('id')}}" id="hotelId">
                            <h5 class="mb-3 mt-3">Near By</h5>
                            <div class="nearby">
                              <input type="radio" id="Attraction" name="near_by" value="Attraction"
                                @if($getlanding[0]->Nearby_Type == 'Attraction') checked @endif>
                              <label for="html">Attraction</label>
                              <input type="radio" id="Hotel" name="near_by" value="Hotel" class="margin-left"
                                @if($getlanding[0]->Nearby_Type == 'Hotel') checked @endif>
                              <label for="css">Hotel</label>
                              <input type="radio" id="Restaurent" name="near_by" value="Restaurent" class="margin-left"
                                @if($getlanding[0]->Nearby_Type == 'Restaurent') checked @endif>
                              <label for="javascript">Restaurent</label>
                              <input type="radio" id="Neighborhood" name="near_by" value="Neighborhood"
                                class="margin-left" @if($getlanding[0]->Nearby_Type == 'Neighborhood') checked @endif>
                              <label for="javascript">Neighborhood</label>
                              <input type="radio" id="Airport" name="near_by" value="Airport" class="margin-left"
                                @if($getlanding[0]->Nearby_Type == 'Airport') checked @endif>
                              <label for="javascript">Airport</label><br>
                            </div>
                            <div class="col-md-8 form-group mt-3">
                              <div class="form-search form-search-icon-right change-field mb-3">
                                <input type="text" name="Attraction" id="Attraction"
                                  class="form-control inputval rounded-3" placeholder="Search Attraction">
                                <i class="ti ti-search"></i>
                              </div>
                              <span class="att-list"></span>
                            </div>

                            <span class="value mt-3">
                              @if(!empty($getlanding[0]->Nearbyname))
                              <button
                                class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value">{{$getlanding[0]->Nearbyname}}</button>
                              @endif
                            </span>




                            <h5 class="mb-3 mt-3">With</h5>
                            <div class="margin-l" style='margin-left: 39px;'>


                              <div class="col-md-8 form-group">
                                <div class="row">
                                  <div class="col-md-6 ">
                                    <select class="select-filters form-select ">
                                      <option selected>Select</option>
                                      <option value="Hotel Class Ratings">Hotel Class Ratings</option>
                                      <option value="Hotel Amenities">Hotel Amenities</option>
                                      <option value="Room Amenities">Room Amenities</option>
                                      <option value="Hotel Pricing">Hotel Pricing</option>
                                      <option value="Room Types">Room Types</option>
                                      <option value="Distance">Distance</option>
                                      <option value="Hotel Style">Hotel Style</option>
                                      <option value="On-site Restaurants">On-site Restaurants</option>
                                      <option value="Hotel Tags">Hotel Tags</option>

                                      <option value="Metro/Public Transit Access">Metro/Public Transit Access</option>
                                      <option value="Access">Access</option>
                                    </select>
                                  </div>
                                  <div class="col-md-6 change-with-filter">

                                  </div>
                                </div>

                                <p class="mt-3">
                                  <span class="star-heading  mt-3"><strong>Star Rating:</strong></span>
                                  <span class="star-rating margin-l mt-3">
                                    @if(isset($getlanding[0]->Rating) && $getlanding[0]->Rating != "")
                                    <?php $rating = explode(',', $getlanding[0]->Rating); ?>
                                    @foreach($rating as $rating)
                                    <span class="margin-l">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$rating}} Star</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="hotel-mnt-heading mt-3"><strong>Hotel Amenities:</strong></span>
                                  <span class="hotel-mnt margin-l mt-3">
                                    @if(isset($getlanding[0]->HotelAmenities) && $getlanding[0]->HotelAmenities != "")
                                    <?php $HotelAmenities = explode(',', $getlanding[0]->HotelAmenities); ?>
                                    @foreach($HotelAmenities as $amenity)
                                    <span class="margin-l">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$amenity}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3 ">
                                  <span class="mnt-heading "><strong>Room Amenities:</strong></span>
                                  <span class="mnt margin-l mt-3">
                                    @if(isset($getlanding[0]->Room_Amenities) && $getlanding[0]->Room_Amenities != "")
                                    <?php $Room_Amenities = explode(',', $getlanding[0]->Room_Amenities); ?>
                                    @foreach($Room_Amenities as $amenity)
                                    <span class="margin-l">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$amenity}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="Hotel-Pricing-heading mt-3"><strong>Hotel Pricing:</strong></span>
                                  <span class="Hotel_Pricing margin-l mt-3">
                                    @if(isset($getlanding[0]->Hotel_Pricing) && $getlanding[0]->Hotel_Pricing != "")
                                    <?php $Hotel_Pricing = explode(',', $getlanding[0]->Hotel_Pricing); ?>
                                    @foreach($Hotel_Pricing as $price)
                                    <span class="margin-l">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$price}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="room-type-heading  mt-3"><strong>Room Type:</strong></span>
                                  <span class="room_type margin-l mt-3">
                                    @if(isset($getlanding[0]->RoomType) && $getlanding[0]->RoomType != "")
                                    <?php $RoomType = explode(',', $getlanding[0]->RoomType); ?>
                                    @foreach($RoomType as $type)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$type}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="distance-heading  mt-3"><strong>Distance:</strong></span>
                                  <span class="distance margin-l mt-3">
                                    @if(isset($getlanding[0]->Distance) && $getlanding[0]->Distance != "")
                                    <?php $Distance = explode(',', $getlanding[0]->Distance); ?>
                                    @foreach($Distance as $dist)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$dist}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="hotel-style-heading  mt-3"><strong>Hotel Style:</strong></span>
                                  <span class="hotel-style margin-l mt-3">
                                    @if(isset($getlanding[0]->Hotel_Style) && $getlanding[0]->Hotel_Style != "")
                                    <?php $Hotel_Style = explode(',', $getlanding[0]->Hotel_Style); ?>
                                    @foreach($Hotel_Style as $style)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$style}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="on-site-restaurants-heading  mt-3"><strong>On-site
                                      Restaurants:</strong></span>
                                  <span class="on-site-restaurants margin-l mt-3" id="onsite-restaurants">
                                    @if(isset($getlanding[0]->OnSiteRestaurants) && $getlanding[0]->OnSiteRestaurants
                                    != "")
                                    <?php $OnSiteRestaurants = explode(',', $getlanding[0]->OnSiteRestaurants); ?>
                                    @foreach($OnSiteRestaurants as $restaurant)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$restaurant}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="Hotel-Tags-heading  mt-3"><strong>Hotel Tags:</strong></span>
                                  <span class="Hotel_Tags margin-l mt-3">
                                    @if(isset($getlanding[0]->hotel_tags) && $getlanding[0]->hotel_tags != "")
                                    <?php $hotel_tags = explode(',', $getlanding[0]->hotel_tags); ?>
                                    @foreach($hotel_tags as $tag)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$tag}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="Public-heading  mt-3"><strong>Metro/Public Transit
                                      Access:</strong></span>
                                  <span class="Public_Transit margin-l mt-3">
                                    @if(isset($getlanding[0]->PublicTransitAccess) &&
                                    $getlanding[0]->PublicTransitAccess != "")
                                    <?php $PublicTransitAccess = explode(',', $getlanding[0]->PublicTransitAccess); ?>
                                    @foreach($PublicTransitAccess as $transit)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary on-site-restaurants margin-top">{{$transit}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <p class="mt-3">
                                  <span class="Access-heading  mt-3"><strong>Access:</strong></span>
                                  <span class="Access margin-l mt-3">
                                    @if(isset($getlanding[0]->Access) && $getlanding[0]->Access != "")
                                    <?php $Access = explode(',', $getlanding[0]->Access); ?>
                                    @foreach($Access as $access)
                                    <span class="margin-l margin-top">
                                      <button
                                        class="btn btn-secondary margin-top">{{$access}}</button>
                                      <i class="fa fa-trash ml-3"></i>
                                    </span>
                                    @endforeach
                                    @endif
                                  </span>
                                </p>
                                <span id="buttonsContainer-6" class="buttons-container-dd d-none mb-3 "
                                  data-colid="{{ $getlanding[0]->id }}">
                                  <button type="button" value="1"
                                    class="reviewField- btn btn-dark save-button px-4 updatelandigfilters"
                                    data-id="{{ $getlanding[0]->id }}" data-colid="5">Save</button>

                                  <a href="" class=" btn btn-dark cancel-button px-4">Cancel</a>
                                </span>




                                <!-- <h4 class="mt-3 add-new-filter"><u>Add New Filter</u></h4> -->
                              </div>
                              <br>
                              <button type="button" id="hidepage" data-id="{{ $getlanding[0]->id }}"
                                class="btn btn-outline-dark">Hide Page</button>
                              <button type="button" id="delete-landing-page" data-id="{{ $getlanding[0]->id }}"
                                class="btn btn-outline-dark">Delete Page</button>
                            </div>
                          </div>
                        </div>
                        </form>
                        @else
                        <p>Landing page Not Found.</p>
                        @endif
                    </span>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

</x-app-layout>