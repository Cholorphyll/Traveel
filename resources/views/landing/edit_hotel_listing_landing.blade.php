<x-app-layout>
  <div class="pc-container">
    <div class="pc-content">
      <div class="d-flex justify-content-end mb-3">
        <a href="{{ url('landing/show-all-hotel-landing-listings') }}" class="text-primary">
          <i class="fa fa-arrow-left"></i> Back to All Listings
        </a>
      </div>
      <!-- Rest of your content -->
      <!-- Header section remains the same -->

      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
              <div class="row justify-content-center">
                <div class="col-md-12">
                  <div class="white_shd full margin_bottom_30">
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success mt-3">{{ $message }}</div>
                    @endif

                    @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                      <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                      </ul>
                    </div>
                    @endif

                    <div class="row">
                      <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group mt-3">
                          <span id="Success"></span>
                          <input type="hidden" id="listingId" value="{{ $listing->id }}">
                          <input type="hidden" id="locationId" value="{{ $listing->location_id }}">

                          <!-- Name Section -->
                          <div class="form-group mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3"
                              onclick="editHlanding(1)">Edit</span>
                            <strong>Name</strong>
                            <textarea id="name" class="form-control rounded-3"
                              data-original-value="{{ $listing->name }}" disabled>{{ $listing->name }}</textarea>
                          </div>

                          <span id="buttonsContainer-1" class="buttons-container-dd d-none mb-3">
                            <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                              data-id="{{ $listing->id }}" data-colid="1">Save</button>
                            <button type="button" onclick="cancelHLanding(1)"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <!-- Slug Section -->
                          <div class="form-group mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3"
                              onclick="editHlanding(2)">Edit</span>
                            <strong>Slug</strong>
                            <textarea id="slug" class="form-control rounded-3"
                              data-original-value="{{ $listing->slug }}" disabled>{{ $listing->slug }}</textarea>
                          </div>

                          <span id="buttonsContainer-2" class="buttons-container-dd d-none mb-3">
                            <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                              data-id="{{ $listing->id }}" data-colid="2">Save</button>
                            <button type="button" onclick="cancelHLanding(2)"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <!-- Meta Tag Title Section -->
                          <div class="form-group mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3"
                              onclick="editHlanding(3)">Edit</span>
                            <strong>Meta Tag Title</strong>
                            <textarea id="meta_title" class="form-control rounded-3"
                              data-original-value="{{ $listing->meta_tag_title }}"
                              disabled>{{ $listing->meta_tag_title }}</textarea>
                          </div>

                          <span id="buttonsContainer-3" class="buttons-container-dd d-none mb-3">
                            <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                              data-id="{{ $listing->id }}" data-colid="3">Save</button>
                            <button type="button" onclick="cancelHLanding(3)"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <!-- Meta Tag Description Section -->
                          <div class="form-group mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3"
                              onclick="editHlanding(4)">Edit</span>
                            <strong>Meta Tag Description</strong>
                            <textarea id="meta_tags" class="form-control rounded-3"
                              data-original-value="{{ $listing->meta_tag_description }}"
                              disabled>{{ $listing->meta_tag_description }}</textarea>
                          </div>

                          <span id="buttonsContainer-4" class="buttons-container-dd d-none mb-3">
                            <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                              data-id="{{ $listing->id }}" data-colid="4">Save</button>
                            <button type="button" onclick="cancelHLanding(4)"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <!-- Keywords Section -->
                          <div class="form-group mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3"
                              onclick="editHlanding(5)">Edit</span>
                            <strong>Keywords</strong>
                            <textarea id="Keywords" class="form-control rounded-3"
                              data-original-value="{{ $listing->keyword }}" disabled>{{ $listing->keyword }}</textarea>
                          </div>

                          <span id="buttonsContainer-5" class="buttons-container-dd d-none mb-3">
                            <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                              data-id="{{ $listing->id }}" data-colid="5">Save</button>
                            <button type="button" onclick="cancelHLanding(5)"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <!-- About Section -->
                          <div class="form-group mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3"
                              onclick="editHlanding(6)">Edit</span>
                            <strong>About</strong>
                            <textarea id="about" class="form-control rounded-3"
                              data-original-value="{{ $listing->about }}" disabled>{{ $listing->about }}</textarea>
                          </div>

                          <span id="buttonsContainer-6" class="buttons-container-dd d-none mb-3">
                            <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                              data-id="{{ $listing->id }}" data-colid="6">Save</button>
                            <button type="button" onclick="cancelHLanding(6)"
                              class="btn btn-dark cancel-button px-4">Cancel</button>
                          </span>
                        </div>
                      </div>

                      <!-- Nearby and Filters Section -->
                      <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                          <br>
                          <!-- Changed edit button number from 6 to 7 to avoid conflict -->
                          <span class="badge bg-dark edit-btn fa-pull-right mt-3" onclick="editHlanding(7)">Edit</span>
                          <input type="hidden" value="{{request()->route('id')}}" id="hotelId">

                          <h5 class="mb-3 mt-3">Near By</h5>
                          <div class="nearby">
                            <input type="radio" id="Attraction" name="near_by" value="Attraction"
                              @if($listing->nearby_type == 'Attraction') checked @endif>
                            <label for="Attraction">Attraction</label>

                            <input type="radio" id="Hotel" name="near_by" value="Hotel" class="margin-left"
                              @if($listing->nearby_type == 'Hotel') checked @endif>
                            <label for="Hotel">Hotel</label>

                            <input type="radio" id="Restaurant" name="near_by" value="Restaurant" class="margin-left"
                              @if($listing->nearby_type == 'Restaurant') checked @endif>
                            <label for="Restaurant">Restaurant</label>

                            <input type="radio" id="Neighborhood" name="near_by" value="Neighborhood"
                              class="margin-left" @if($listing->nearby_type == 'Neighborhood') checked @endif>
                            <label for="Neighborhood">Neighborhood</label>

                            <input type="radio" id="Airport" name="near_by" value="Airport" class="margin-left"
                              @if($listing->nearby_type == 'Airport') checked @endif>
                            <label for="Airport">Airport</label>
                          </div>

                          <div class="col-md-8 form-group mt-3">
                            <div class="form-search form-search-icon-right change-field mb-3">
                              <input type="text" name="Attraction" id="Attraction"
                                class="form-control inputval rounded-3"
                                placeholder="Search {{ $listing->nearby_type }}">
                              <i class="ti ti-search"></i>
                            </div>
                            <span class="att-list"></span>
                          </div>

                          <span class="value mt-3">
                            @if(!empty($listing->nearby_name))
                            <button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value">
                              {{$listing->nearby_name}}
                            </button>
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
                                    <!-- <option value="Room Amenities">Room Amenities</option>
                                    <option value="Hotel Pricing">Hotel Pricing</option>
                                    <option value="Room Types">Room Types</option>
                                    <option value="Distance">Dist ance</option>
                                    <option value="Hotel Style">Hotel Style</option>
                                    <option value="On-site Restaurants">On-site Restaurants</option>
                                    <option value="Hotel Tags">Hotel Tags</option>

                                    <option value="Metro/Public Transit Access">Metro/Public Transit Access</option>
                                    <option value="Access">Access</option>-->
                                  </select>
                                </div>
                                <div class="col-md-6 change-with-filter">

                                </div>
                              </div>

                              <p class="mt-3">
                                <span class="star-heading  mt-3"><strong>Star Rating:</strong></span>
                                <span class="star-rating margin-l mt-3">
                                  @if(isset($listing->rating) && $listing->rating != "")
                                  <?php $rating = explode(',', $listing->rating); ?>
                                  @foreach($rating as $rating)
                                  <span class="margin-l">
                                    <button class="btn btn-secondary on-site-restaurants margin-top">{{$rating}}
                                      Star</button>
                                    <i class="fa fa-trash ml-3"></i>
                                  </span>
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              <p class="mt-3">
                                <span class="hotel-mnt-heading mt-3"><strong>Hotel Amenities:</strong></span>
                                <span class="hotel-mnt margin-l mt-3">
                                  @if(isset($listing->amenity) && $listing->amenity != "")
                                  <?php $HotelAmenities = explode(',', $listing->amenity); ?>
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

                              <!-- Changed button container ID from 6 to 7 -->
                              <span id="buttonsContainer-7" class="buttons-container-dd d-none mb-3"
                                data-colid="{{ $listing->id }}">
                                <button type="button" class="btn btn-dark save-button px-4 update_hotel_listing_landing"
                                  data-id="{{ $listing->id }}" data-colid="7">Save</button>
                                <button type="button" onclick="cancelHLanding(7)"
                                  class="btn btn-dark cancel-button px-4">Cancel</button>
                              </span>
                            </div>

                            <br>
                            <button type="button" id="hidepage" data-id="{{ $listing->id }}"
                              class="btn btn-outline-dark">Hide Page</button>

                            <form action="{{ route('delete.hotel.listing.landing', $listing->id) }}" method="POST"
                              style="display:inline;">
                              @csrf
                              @method('DELETE')
                              <button type="submit" class="btn btn-outline-dark"
                                onclick="return confirm('Are you sure you want to delete this listing?');">
                                <i class="ti ti-trash"></i> Delete Page
                              </button>
                            </form>

                          </div>
                        </div>
                      </div>


                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
  <script src="{{ asset('js/hotel-listing-edit.js') }}"></script>
  @endpush
</x-app-layout>