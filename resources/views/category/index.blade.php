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
                <li class="breadcrumb-item" aria-current="page">Manage Category</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">


                </h2>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- [ breadcrumb ] end -->
      <div class="row">
        <div class="col-md-12">
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
          <br>
          <div class="card">
            <div class="card-header">
              <h5>Search Category</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-12 mt-3">
           
                    <div class="row">
                    <span id="Success"></span>
                      <div class="landingpagetype mb-5">
                        <h5>Select Category  Type</h5>
                        <input type="radio" id="Attraction" name="page_type" value="Attraction" class="mt-3" checked>
                        <label for="Attraction">Attraction</label>
                        <input type="radio" id="Hotel" name="page_type" value="Hotel" class="margin-left">
                        <label for="Hotel">Hotel</label>
                        <!-- <input type="radio" id="Restaurent" name="page_type" value="Restaurent" class="margin-left">
                        <label for="Restaurent">Restaurant</label> -->
                        <input type="radio" id="Experience" name="page_type" value="Experience" class="margin-left">
                        <label for="Experience">Experience</label>
                      </div>


                      <div id="searchBoxes">
                        <div id="attractionBox" class="searchBox">
                          <h5 class="">Attraction Category</h5>
                          <div class="row">
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Search Attraction Name, Id or Url </strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" name="search_value" id="searchinput"
                                  class="form-control rounded-3 searchlandingvalue"  placeholder="Search Attraction"
                                  required><i class="ti ti-search"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary " id="search_cat_attraction" data-type="attraction"
                               >Search</button>
                            </div>
                          </div>
                        </div>
                        <div id="hotelBox" class="searchBox" style="display: none;">
                        <h5 class="">Hotel Category</h5>
                          <div class="row">
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Search Hotel Name, Id or Url </strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" name="search_value" id=""
                                  class="form-control rounded-3 hotelinput"  placeholder="Search Hotel"
                                  required><i class="ti ti-search"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary search-cat-hotel"
                              data-type="hotel">Search</button>
                            </div>
                          </div>
                        </div>

                       
                        <div id="experienceBox" class="searchBox" style="display: none;">
                        <h5 class="">Experience Category</h5>
                        <div class="row">
                          <div class="col-md-3 form-group">
                            <strong class="form-label">Search Experience Name, Id or Url </strong>
                            <div class="form-search form-search-icon-right">
                              <input type="text" name="search_value" id="" class="form-control rounded-3 exp_inp"
                              placeholder="Search Experience" required><i class="ti ti-search"></i>
                            </div>
                          </div>
                          <div class="col-md-3 mt-4">
                            <button type="submit" class="btn btn-outline-secondary search-cat-experience" data-type="experience"
                           >Search</button>
                          </div>
                          </div>
                        </div>
                      </div>



                    </div>
                
                    
                  </div>
                </div>
              </div>
              <div class="getfilterData m-3"></div>

            </div>
          </div>
        </div>
  
        
      </div>
    </div>
  </div>
</x-app-layout>