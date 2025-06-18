<x-app-layout>
  <head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </head>
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
                <h2 class="mb-0">Edit Hotel Landing Page</h2>
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
                    <span type="button" class="float-right"
                      style="float:right">
                      @if($getlanding->isEmpty())
                      <h4><u><a href="{{ route('add_hotel_landing_page',[request()->route('id')]) }}">Add Landing Page</a></u></h4>
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
                                placeholder="" >
                            </div>
                            <input type="hidden" name='attrid' id="selected_att_id"
                              value="{{ $getlanding[0]->hotelid }}" class="form-control rounded-3" required>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <strong>Name</strong>
                              <div id="namemsg-{{ $getlanding[0]->id }}"></div>
                              <input type="text" name="name" id="name1" class="form-control rounded-3" 
                                value="{{ $getlanding[0]->Name }}" data-original-value="{{ $getlanding[0]->Name }}">
                            </div>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <strong>Slug</strong>
                              <div id="slugmsg-{{ $getlanding[0]->id }}"></div>
                              <input type="text" name="slug" id="name2" class="form-control rounded-3"
                                value="{{ $getlanding[0]->Slug }}" data-original-value="{{ $getlanding[0]->Slug }}">
                            </div>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <strong>Meta Title</strong>
                              <div id="titlemsg-{{ $getlanding[0]->id }}"></div>
                              <input type="text" name="meta_title" id="name3" class="form-control rounded-3"
                                value="{{ $getlanding[0]->MetaTagTitle }}" data-original-value="{{ $getlanding[0]->MetaTagTitle }}">
                            </div>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <strong>Meta Description</strong>
                              <div id="descmsg-{{ $getlanding[0]->id }}"></div>
                              <input type="text" name="meta_desc" id="name4" class="form-control rounded-3"
                                value="{{ $getlanding[0]->MetaTagDescription }}" data-original-value="{{ $getlanding[0]->MetaTagDescription }}">
                            </div>

                            <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <strong>About</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="about" id="name5" class="form-control rounded-3"
                                >{{ $getlanding[0]->About }}</textarea>
                            </div>
                      </div></div>

                      <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <br>
                            <h5>Landing Page Type</h5>
                            <div class="mt-2">
                              <input type="radio" id="Attraction" name="page_type" value="Attraction">
                              <label for="html">Attraction</label>
                              <input type="radio" id="Hotel" name="page_type" value="Hotel" class="margin-left">
                              <label for="css">Hotel</label>
                              <input type="radio" id="Restaurant" name="page_type" value="Restaurant" class="margin-left">
                              <label for="javascript">Restaurant</label>
                              <input type="radio" id="Experience" name="page_type" value="Experience" class="margin-left">
                              <label for="javascript">Experience</label>
                            </div>

                            <input type="hidden" value="{{request()->route('id')}}" id="hotelId">
                          
                            <h5 class="mb-3 mt-3">Near By</h5>
                            <div class="nearby mt-2">
                              <input type="radio" id="Attraction" name="near_by" value="Attraction" @if($getlanding[0]->Nearby_Type == 'Attraction') checked @endif>
                              <label for="html">Attraction</label>
                              <input type="radio" id="Hotel" name="near_by" value="Hotel" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Hotel') checked @endif>
                              <label for="css">Hotel</label>
                              <input type="radio" id="Restaurent" name="near_by" value="Restaurent" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Restaurent') checked @endif>
                              <label for="javascript">Restaurent</label>
                              <input type="radio" id="Neighborhood" name="near_by" value="Neighborhood" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Neighborhood') checked @endif> 
                              <label for="javascript">Neighborhood</label>
                              <input type="radio" id="Airport" name="near_by" value="Airport" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Airport') checked @endif>
                              <label for="javascript">Airport</label>
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
                              @if(!empty($getlanding[0]->NearbyId)) 
                                <button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value">{{$getlanding[0]->NearbyId}}</button>
                              @endif
                            </span>

                            <h5 class="mb-3 mt-3">Conditions</h5>
                            <div class="margin-l" style='margin-left: 39px;'>
                              <div class="col-md-8 form-group">
                                <div class="row">
                                  <div class="col-md-6">
                                    <select class="select-filters form-select">
                                      <option selected>Select</option>
                                      <option value="amenities">Amenities</option>
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
                              </div>
                            </div>

                            <p class="mt-3">
                              <span class="star-heading mt-3"><strong>Star Rating:</strong></span>
                              <span class="star-rating margin-l mt-3">                               
                                @if(isset($getlanding[0]->Rating) && $getlanding[0]->Rating !== "null")
                                  <?php 
                                  $rating = json_decode($getlanding[0]->Rating);
                                  ?>
                                  @if(!is_null($rating) && (is_array($rating) || is_object($rating)))
                                    @foreach($rating as $ratingItem)
                                      <span class="margin-l"><button class="btn btn-secondary on-site-restaurants margin-top">{{$ratingItem}} Star</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="hotel-mnt-heading mt-3"><strong>Hotel Amenities:</strong></span>
                              <span class="hotel-mnt margin-l mt-3">                                 
                                @if(isset($getlanding[0]->HotelAmenities) && $getlanding[0]->HotelAmenities !== "null") 
                                  <?php 
                                  $HotelAmenities = json_decode($getlanding[0]->HotelAmenities);
                                  ?>
                                  @if(!is_null($HotelAmenities) && (is_array($HotelAmenities) || is_object($HotelAmenities)))
                                    @foreach($HotelAmenities as $HotelAmenities)
                                      <span class="margin-l"><button class="btn btn-secondary on-site-restaurants margin-top">{{$HotelAmenities}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="mnt-heading"><strong>Room Amenities:</strong></span>
                              <span class="mnt margin-l mt-3">                                
                                @if(isset($getlanding[0]->Room_Amenities) && $getlanding[0]->Room_Amenities !== "null")
                                  <?php 
                                  $Room_Amenities = json_decode($getlanding[0]->Room_Amenities);
                                  ?>
                                  @if(!is_null($Room_Amenities) && (is_array($Room_Amenities) || is_object($Room_Amenities)))
                                    @foreach($Room_Amenities as $Room_Amenity)
                                      <span class="margin-l"><button class="btn btn-secondary on-site-restaurants margin-top">{{$Room_Amenity}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="Hotel-Pricing-heading mt-3"><strong>Hotel Pricing:</strong></span>
                              <span class="Hotel_Pricing margin-l mt-3">                                 
                                @if(isset($getlanding[0]->Hotel_Pricing) && $getlanding[0]->Hotel_Pricing !== "null")
                                  <?php 
                                  $Hotel_Pricing = json_decode($getlanding[0]->Hotel_Pricing);
                                  ?>
                                  @if(!is_null($Hotel_Pricing) && (is_array($Hotel_Pricing) || is_object($Hotel_Pricing)))
                                    @foreach($Hotel_Pricing as $Hotel_Pricing)
                                      <span class="margin-l"><button class="btn btn-secondary on-site-restaurants margin-top">{{$Hotel_Pricing}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="room-type-heading mt-3"><strong>Room Type:</strong></span>
                              <span class="room_type margin-l mt-3">                               
                                @if(isset($getlanding[0]->RoomType) && $getlanding[0]->RoomType !== "null")
                                  <?php 
                                  $RoomType = json_decode($getlanding[0]->RoomType);
                                  ?>
                                  @if(!is_null($RoomType) && (is_array($RoomType) || is_object($RoomType)))
                                    @foreach($RoomType as $RoomType)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary on-site-restaurants margin-top">{{$RoomType}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="distance-heading mt-3"><strong>Distance:</strong></span>
                              <span class="distance margin-l mt-3">
                                @if(isset($getlanding[0]->Distance) && $getlanding[0]->Distance !== "null")   
                                  <?php 
                                  $Distance = json_decode($getlanding[0]->Distance);
                                  ?>
                                  @if(!is_null($Distance) && (is_array($Distance) || is_object($Distance)))
                                    @foreach($Distance as $Distance)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary on-site-restaurants margin-top">{{$Distance}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="hotel-style-heading mt-3"><strong>Hotel Style:</strong></span>
                              <span class="hotel-style margin-l mt-3"> 
                                @if(isset($getlanding[0]->Hotel_Style) && $getlanding[0]->Hotel_Style !== "null")   
                                  <?php 
                                  $Hotel_Style = json_decode($getlanding[0]->Hotel_Style);
                                  ?>
                                  @if(!is_null($Hotel_Style) && (is_array($Hotel_Style) || is_object($Hotel_Style)))
                                    @foreach($Hotel_Style as $Hotel_Style)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary on-site-restaurants margin-top">{{$Hotel_Style}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="on-site-restaurants-heading mt-3"><strong>On-site Restaurants:</strong></span>
                              <span class="on-site-restaurants margin-l mt-3" id="onsite-restaurants">                               
                                @if(isset($getlanding[0]->OnSiteRestaurants) && $getlanding[0]->OnSiteRestaurants !== "null")   
                                  <?php 
                                  $OnSiteRestaurants = json_decode($getlanding[0]->OnSiteRestaurants);
                                  ?>
                                  @if(!is_null($OnSiteRestaurants) && (is_array($OnSiteRestaurants) || is_object($OnSiteRestaurants)))
                                    @foreach($OnSiteRestaurants as $OnSiteRestaurants)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary on-site-restaurants margin-top">{{$OnSiteRestaurants}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="Hotel-Tags-heading mt-3"><strong>Hotel Tags:</strong></span>
                              <span class="Hotel_Tags margin-l mt-3">                               
                                @if(isset($getlanding[0]->hotel_tags) && $getlanding[0]->hotel_tags !== "null")   
                                  <?php 
                                  $hotel_tags = json_decode($getlanding[0]->hotel_tags);
                                  ?>
                                  @if(!is_null($hotel_tags) && (is_array($hotel_tags) || is_object($hotel_tags)))
                                    @foreach($hotel_tags as $hotel_tags)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary on-site-restaurants margin-top">{{$hotel_tags}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="Public-heading mt-3"><strong>Metro/Public Transit Access:</strong></span>
                              <span class="Public_Transit margin-l mt-3">
                                @if(isset($getlanding[0]->PublicTransitAccess) && $getlanding[0]->PublicTransitAccess !== "null")                                
                                  <?php 
                                  $PublicTransitAccess = json_decode($getlanding[0]->PublicTransitAccess);
                                  ?>
                                  @if(!is_null($PublicTransitAccess) && (is_array($PublicTransitAccess) || is_object($PublicTransitAccess)))
                                    @foreach($PublicTransitAccess as $PublicTransitAccess)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary on-site-restaurants margin-top">{{$PublicTransitAccess}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <p class="mt-3">
                              <span class="Access-heading mt-3"><strong>Access:</strong></span>
                              <span class="Access margin-l mt-3">
                                @if(isset($getlanding[0]->Access) && $getlanding[0]->Access !== "null")                          
                                  <?php 
                                  $Access = json_decode($getlanding[0]->Access);
                                  ?>
                                  @if(!is_null($Access) && (is_array($Access) || is_object($Access)))
                                    @foreach($Access as $Access)
                                      <span class="margin-l margin-top"><button class="btn btn-secondary margin-top">{{$Access}}</button><i class="fa fa-trash ml-3"></i></span>
                                    @endforeach
                                  @endif
                                @endif
                              </span>
                            </p>

                            <!-- Add single save button at bottom -->
                            <div class="text-center mt-4 mb-4">
                              <button type="button" id="saveAllChanges" class="btn btn-primary" data-id="{{ $getlanding[0]->id }}">
                                Save All Changes
                              </button>
                            </div>

                            <button type="button" id="hidepage" data-id="{{ $getlanding[0]->id }}" class="btn btn-outline-dark">Hide Page</button>
                            <button type="button" id="delete-landing-page" data-id="{{ $getlanding[0]->id }}" class="btn btn-outline-dark">Delete Page</button>
                          </div>
                        </div>
                      </div>
                      </form>
                      @else
                      <p>FAQ Not Found.</p>
                      @endif
                    </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

@push('scripts')
<script>
$(document).ready(function() {
    console.log('Document ready');
    
    // Remove the reference to hotel-landing.js since we have the code here
    $('script[src="{{ asset("js/hotel-landing.js") }}"]').remove();
    
    $('#saveAllChanges').on('click', function(e) {
        e.preventDefault();
        console.log('Save button clicked');
        
        const id = $(this).data('id');
        console.log('ID:', id);
        
        // Show loading state
        const $saveButton = $(this);
        const originalText = $saveButton.text();
        $saveButton.prop('disabled', true).text('Saving...');

        // Get CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        
        // Collect data
        const data = {
            page_type: $('input[name="page_type"]:checked').val(),
            Nearby_Type: $('input[name="near_by"]:checked').val(),
            NearbyId: $('.nearby-value').text().trim(),
            Nearbyname: $('.inputval').val().trim(),
            Rating: $('.star-rating button').map(function() { return $(this).text().trim(); }).get().join(','),
            HotelAmenities: $('.hotel-mnt button').map(function() { return $(this).text().trim(); }).get().join(','),
            Room_Amenities: $('.mnt button').map(function() { return $(this).text().trim(); }).get().join(','),
            Hotel_Pricing: $('.Hotel_Pricing button').map(function() { return $(this).text().trim(); }).get().join(','),
            RoomType: $('.room_type button').map(function() { return $(this).text().trim(); }).get().join(','),
            Distance: $('.distance button').map(function() { return $(this).text().trim(); }).get().join(','),
            Hotel_Style: $('.hotel-style button').map(function() { return $(this).text().trim(); }).get().join(','),
            OnSiteRestaurants: $('.on-site-restaurants button').map(function() { return $(this).text().trim(); }).get().join(','),
            HotelTags: $('.Hotel_Tags button').map(function() { return $(this).text().trim(); }).get().join(','),
            PublicTransitAccess: $('.Public_Transit button').map(function() { return $(this).text().trim(); }).get().join(','),
            Access: $('.Access button').map(function() { return $(this).text().trim(); }).get().join(',')
        };

        console.log('Sending data:', data);

        // Send AJAX request
        $.ajax({
            url: '/landing/update-hotel-landing/' + id,
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Success:', response);
                if (response.status === 'success') {
                    alert('All changes saved successfully');
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                let errorMessage = 'Error saving changes';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage += ': ' + (response.message || error);
                } catch(e) {
                    errorMessage += ': ' + error;
                }
                alert(errorMessage);
            },
            complete: function() {
                $saveButton.prop('disabled', false).text(originalText);
            }
        });
    });
});
</script>
@endpush
</x-app-layout>