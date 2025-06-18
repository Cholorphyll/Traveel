<x-app-layout>
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Edit Restaurant Landing Page</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Restaurant Landing Page</h2>
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
                    @if ($message = Session::get('success'))
                      <div class="col-md-8 alert alert-success mt-3">
                        {{ $message }}
                      </div>
                    @endif

                    @if ($errors->any())
                      <div class="col-md-8 alert alert-danger mt-3">
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

                    @if(!$getlanding->isEmpty())
                      <form action="{{ route('update_restaurant_landing') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id" value="{{ $landing->id ?? '' }}">

                        <div class="row">
                          <div class="col-xs-6 col-sm-6 col-md-6">
                            <!-- Restaurant Section -->
                            <div class="form-group mt-3">
                              <span id="Success"></span>
                              <strong>Restaurant</strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" id="search_restaurant" name="restaurant_name"
                                  value="{{ $getlanding[0]->Name }}" class="search_restaurant form-control rounded-3"
                                  placeholder="" disabled>
                              </div>
                              <input type="hidden" name="restaurantid" id="selected_restaurant_id"
                                value="{{ $getlanding[0]->restaurantid }}" class="form-control rounded-3" required>

                              <!-- Landing Page Name -->
                              <div class="form-group mt-3">
                                <strong>Landing Page Name</strong>
                                <textarea name="Name" id="name1" class="form-control rounded-3" disabled>{{ $getlanding[0]->Name }}</textarea>
                              </div>

								
								 <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              
                              <strong>Page Slug</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question" data-original-value="{{ $getlanding[0]->Slug }}"
                                id="name2" class="form-control rounded-3" disabled>{{ $getlanding[0]->Slug }}</textarea>
                            </div>
								
                              <!-- Meta Details -->
                              <div class="form-group mt-3">
                                <strong>Meta Title</strong>
                                <textarea name="MetaTagTitle" id="meta_title" class="form-control rounded-3" disabled>{{ $getlanding[0]->MetaTagTitle }}</textarea>
                              </div>
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea name="MetaTagDescription" id="meta_description" class="form-control rounded-3" disabled>{{ $getlanding[0]->MetaTagDescription }}</textarea>
                              </div>

								
								 <div class="form-group getdata-{{ $getlanding[0]->id }} mt-3">
                              <strong>About Landing Page</strong>
                              <div id="questionmsg-{{ $getlanding[0]->id }}"></div>
                              <textarea type="text" name="question" data-original-value="{{ $getlanding[0]->About }}"
                                id="name5" class="form-control rounded-3"
                                disabled>{{ $getlanding[0]->About }}</textarea>
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
 <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editHlanding(6)">Edit</span>
                          <input type="hidden" value="{{request()->route('id')}}" id="hotelId">
                          <h5 class="mb-3 mt-3">Near By</h5>
                          <div class="nearby">
                            <input type="radio" id="Attraction" name="near_by" value="Attraction" @if($getlanding[0]->Nearby_Type == 'Attraction') checked @endif>
                            <label for="html">Attraction</label>
                            <input type="radio" id="Hotel" name="near_by" value="Hotel" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Hotel') checked @endif>
                            <label for="css">Hotel</label>
                            <input type="radio" id="Restaurent" name="near_by" value="Restaurent" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Restaurent') checked @endif>
                            <label for="javascript">Restaurent</label>
                            <input type="radio" id="Neighborhood" name="near_by" value="Neighborhood" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Neighborhood') checked @endif> 
                            <label for="javascript">Neighborhood</label>
                            <input type="radio" id="Airport" name="near_by" value="Airport" class="margin-left" @if($getlanding[0]->Nearby_Type == 'Airport') checked @endif>
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
                          @if(!empty($getlanding[0]->NearbyId)) 
                            <button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value">{{$getlanding[0]->NearbyId}}</button>
                           @endif
                          </span>
								 </div>
                             
                             <!-- Amenities -->
<h5 class="mt-3">Amenities</h5>
<p>
  <span><strong>Restaurant Amenities:</strong></span>
  @if(!empty($getlanding[0]->Amenities))
    <?php 
      $amenities = json_decode($getlanding[0]->Amenities, true); 
    ?>
    @if(is_array($amenities))
      @foreach($amenities as $amenity)
        <span class="badge bg-secondary">{{ $amenity }}</span>
      @endforeach
    @else
      <span>No valid amenities found.</span>
    @endif
  @else
    <span>No amenities provided.</span>
  @endif
</p>


                              <button type="submit" class="btn btn-dark mt-3">Save</button>
                            </div>
                          </div>
                        </div>
                      </form>
                    @else
                      <p>No landing data found.</p>
                    @endif
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
