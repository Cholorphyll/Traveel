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
                <li class="breadcrumb-item" aria-current="page">Add Hotel Landing Page</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Add Hotel Landing Page</h2>
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
                    @if($result->isEmpty())
                    <div class="row">
                      <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group mb-3">
                          <span id="Success"></span>
                          <div class="col-md-8 form-group">
                            <div class="form-search form-search-icon-right">
                              <label for="html" class="mt-3">Page Name</label>
                              <input type="text" name="search_value" id="name" class="form-control rounded-3"
                                aria-describedby="emailHelp" placeholder="Name">

                              <label for="html" class="mt-3">Slug</label>
                              <input type="text" name="search_value" id="slug"
                                class="form-control rounded-3 " aria-describedby="emailHelp" placeholder="Slug">

                              <label for="html" class="mt-3">Meta Title</label>
                              <textarea name="" id="meta_title" class=" form-control rounded-3"></textarea>

                              <label for="html" class="mt-3">Meta Description</label>
                              <textarea name="" id="meta_tags" class="form-control rounded-3"></textarea>

                              <label for="html" class="mt-3">About</label>
                              <textarea name="about" id="about" class=" form-control rounded-3"></textarea>
                            </div>

                          </div>
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
                          <input type="hidden" value="{{request()->route('id')}}" id="hotelId">
                          <h5 class="mb-3 mt-3">Near By</h5>
                          <div class="nearby">
                            <input type="radio" id="Attraction" name="near_by" value="Attraction" checked>
                            <label for="html">Attraction</label>
                            <input type="radio" id="Hotel" name="near_by" value="Hotel" class="margin-left">
                            <label for="css">Hotel</label>
                            <input type="radio" id="Restaurent" name="near_by" value="Restaurent" class="margin-left">
                            <label for="javascript">Restaurent</label>
                            <input type="radio" id="Neighborhood" name="near_by" value="Neighborhood" class="margin-left">
                            <label for="javascript">Neighborhood</label>
                            <input type="radio" id="Airport" name="near_by" value="Airport" class="margin-left">
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
                          </span>




                          <h5 class="mb-3">With</h5>
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
                                <span class="star-rating margin-l mt-3"></span>
                              </p>
                              <p class="mt-3">
                                <span class="hotel-mnt-heading mt-3"><strong>Hotel Amenities:</strong></span>
                                <span class="hotel-mnt margin-l mt-3"></span>
                              </p>
                              <p class="mt-3 ">
                                <span class="mnt-heading "><strong>Room Amenities:</strong></span>
                                <span class="mnt margin-l mt-3"></span>
                              </p>     
                              <p class="mt-3">
                                <span class="Hotel-Pricing-heading mt-3"><strong>Hotel Pricing:</strong></span>
                                <span class="Hotel_Pricing margin-l mt-3"></span>
                              </p>
                              <p class="mt-3">
                                <span class="room-type-heading  mt-3"><strong>Room Type:</strong></span>
                                <span class="room_type margin-l mt-3"></span>
                              </p>
                              <p class="mt-3">
                                <span class="distance-heading  mt-3"><strong>Distance:</strong></span>
                                <span class="distance margin-l mt-3"></span>
                              </p>
                              <p class="mt-3">
                                <span class="hotel-style-heading  mt-3"><strong>Hotel Style:</strong></span>
                                <span class="hotel-style margin-l mt-3"></span>
                              </p>
                              <p class="mt-3">
                                <span class="on-site-restaurants-heading  mt-3"><strong>On-site Restaurants:</strong></span>
                                <span class="on-site-restaurants margin-l mt-3" id="onsite-restaurants"></span>
                              </p>
                                <p class="mt-3">
                                <span class="Hotel-Tags-heading  mt-3"><strong>Hotel Tags:</strong></span>
                                <span class="Hotel_Tags margin-l mt-3" ></span>
                              </p>
                               <p class="mt-3">
                                <span class="Public-heading  mt-3"><strong>Metro/Public Transit Access:</strong></span>
                                <span class="Public_Transit margin-l mt-3" ></span>
                              </p>
                              <p class="mt-3">
                                <span class="Access-heading  mt-3"><strong>Access:</strong></span>
                                <span class="Access margin-l mt-3" ></span>
                              </p>





                              <!-- <h4 class="mt-3 add-new-filter"><u>Add New Filter</u></h4> -->
                            </div>

                        
                          </div>
                          <button type="button" id="add_landing" data-id="" class="btn btn-outline-dark ">Create
                            Page</button>

                        </div>
                      </div>
                    </div>
                    @else
                      <p>Landing Page already Added.</p>
                    @endif
                  </div>
                </div>
              </div>

</x-app-layout>