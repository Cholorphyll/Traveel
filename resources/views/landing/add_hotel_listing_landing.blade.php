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
                <h2 class="mb-0">Add Hotel Listing Landing Page</h2>
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
                    <div class="row">
                      <div class="col-xs-8 col-sm-8 col-md-8">
                        <div class="form-group mb-3">
                          <span id="Success"></span>
                          <div class="col-md-8 form-group">
                            <div class="form-search form-search-icon-right">
                              <label for="name" class="mt-3">Page Name <span class="text-danger">*</span><small id="nameError" class=" margin-left-10 text-danger"></small></label>
                              <input type="text" name="name" id="name" class="form-control rounded-3" 
                                aria-describedby="emailHelp" placeholder="Name">                              

                              <label for="slug" class="mt-3">Slug</label>
                              <input type="text" name="slug" id="slug" class="form-control rounded-3" 
                                aria-describedby="emailHelp" placeholder="Slug">

                              <label for="meta_title" class="mt-3">Meta Title</label>
                              <textarea name="meta_title" id="meta_title" class="form-control rounded-3"></textarea>

                              <label for="meta_tags" class="mt-3">Meta Description</label>
                              <textarea name="meta_tags" id="meta_tags" class="form-control rounded-3"></textarea>

                              <label for="meta_tags" class="mt-3">Keywords</label>
                              <textarea name="meta_tags" id="Keywords" class="form-control rounded-3"></textarea>

                              <label for="about" class="mt-3">About</label>
                              <textarea name="about" id="about" class="form-control rounded-3"></textarea>
								
								<label for="Collection" class="mt-3">Collection</label>
                              <input type="text" name="Collection" id="Collection"
                                class="form-control inputval rounded-3" placeholder="Search Collection">
                            </div>
                          </div>

                          <input type="hidden" value="{{request()->route('id')}}" id="locationId">
                          <input type="hidden" value="" id="locationId">
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
                                <span class="hotel-mnt-heading mt-3"><strong>Amenities:</strong></span>
                                <span class="hotel-mnt margin-l mt-3"></span>
                              </p>
                             
                            </div>
                          </div>
                          <button type="button" id="add_hotel_listing_landing" class="btn btn-outline-dark mt-3">
                            Create Page
							</button>
								
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

</x-app-layout>