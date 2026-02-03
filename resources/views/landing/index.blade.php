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
                <li class="breadcrumb-item" aria-current="page">Manage Landing</li>
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
        <script>
            // Test if jQuery is loaded
            if (typeof jQuery === 'undefined') {
                console.error('jQuery is not loaded!');
            } else {
                console.log('jQuery is loaded, version:', jQuery.fn.jquery);
            }

            // Use vanilla JavaScript first
            document.addEventListener('DOMContentLoaded', function() {
                console.log('DOM loaded');
                
                // Find all filter buttons
                var filterButtons = document.querySelectorAll('.filter-btn');
                console.log('Found filter buttons:', filterButtons.length);
                
                // Add click handlers using vanilla JS
                filterButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        console.log('Button clicked via vanilla JS');
                        
                        var type = this.getAttribute('data-type');
                        var row = this.closest('.row');
                        var filterInput = row.querySelector('.filter-input');
                        var filterValue = filterInput.value.toLowerCase().trim();
                        
                        console.log('Type:', type);
                        console.log('Filter value:', filterValue);
                        
                        // Get search parameters
                        var searchType = document.getElementById('searchType').value;
                        var searchValue = document.getElementById('searchValue').value;
                        
                        console.log('Search type:', searchType);
                        console.log('Search value:', searchValue);
                        
                        // Make AJAX request
                        var xhr = new XMLHttpRequest();
                        xhr.open('GET', '{{ route("landing.filter") }}' + 
                            '?type=' + encodeURIComponent(searchType) + 
                            '&value=' + encodeURIComponent(searchValue) + 
                            '&filter=' + encodeURIComponent(filterValue), true);
                            
                        xhr.onload = function() {
                            if (xhr.status === 200) {
                                console.log('Got response');
                                document.querySelector('.search-container').innerHTML = xhr.responseText;
                            } else {
                                console.error('Error:', xhr.statusText);
                            }
                        };
                        
                        xhr.onerror = function() {
                            console.error('Request failed');
                        };
                        
                        console.log('Sending request...');
                        xhr.send();
                    });
                });
                
                // Add enter key handler
                var filterInputs = document.querySelectorAll('.filter-input');
                filterInputs.forEach(function(input) {
                    input.addEventListener('keypress', function(e) {
                        if (e.key === 'Enter') {
                            var button = this.closest('.row').querySelector('.filter-btn');
                            if (button) {
                                button.click();
                            }
                        }
                    });
                });
            });
        </script>
        
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
              <h5>Search Landing</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-12 mt-3">
                    <!-- <form method="post" action="{{route('search_location')}}"> -->
                    
                    <span type="button" class="float-right" style="float:right">
                      <h4><u><a href="{{ route('addlandingpage_search') }}" target="_blank">Add Landing Page</a></u>
                      </h4>
                    </span>
                    @csrf
                    <div class="row">
                    <span id="Success"></span>
                      <div class="landingpagetype mb-5">
                        <h5>Select Landing Page Type</h5>
						<input type="radio" id="Hotel_listing" name="page_type" value="hotellisting" class="mt-3" checked>
                        <label for="Hotel_listing">Hotel Listing</label>
                        <input type="radio" id="Attraction" name="page_type" value="Attraction" class="mt-3" checked>
                        <label for="Attraction">Attraction</label>
                        <input type="radio" id="Hotel" name="page_type" value="Hotel" class="mt-3" checked>
                        <label for="Hotel">Hotel</label>
                        <input type="radio" id="Restaurent" name="page_type" value="Restaurent" class="mt-3" checked>
                        <label for="Restaurent">Restaurant</label>
                        <input type="radio" id="Experience" name="page_type" value="Experience" class="mt-3" checked>
                        <label for="Experience">Experience</label>
                      </div>


                      <div id="searchBoxes">
                        <div id="attractionBox" class="searchBox">
                          <h5 class="">Attraction Landing</h5>
                          <div class="row m-3">
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Search Landing Name, Id or Url </strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" name="search_value" id="search_attinput"
                                  class="form-control rounded-3 searchlandingvalue"  placeholder="Search Attraction Landing"
                                  required><i class="ti ti-search"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary searchlandingpage" data-type="attraction">Search</button>            
                            </div>
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Filter Results</strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" class="form-control rounded-3 filter-input" 
                                  placeholder="Filter results">
                                <i class="ti ti-filter"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="button" class="btn btn-outline-secondary filter-btn" data-type="attraction">
                                <i class="ti ti-filter"></i> Filter Results
                              </button>
                            </div>
                          </div>
                        </div>
                        <div id="hotelBox" class="searchBox" style="display: none;">
                        <h5 class="">Hotel Landing</h5>
                          <div class="row m-3">
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Search Landing Name, Id or Url </strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" name="search_value" id="search_attinput"
                                  class="form-control rounded-3 searchlandingvalue"  placeholder="Search Hotel Landing "
                                  required><i class="ti ti-search"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary searchlandingpage" data-type="hotel">Search</button>            
                            </div>
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Filter Results</strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" class="form-control rounded-3 filter-input" id="filter-hotel"
                                  placeholder="Filter results" data-type="hotel">
                                <i class="ti ti-filter"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="button" class="btn btn-outline-secondary filter-btn" data-type="hotel">
                                Filter <i class="ti ti-search"></i>
                              </button>
                            </div>
                          </div>
                        </div>
                        <div id="restaurentBox" class="searchBox" style="display: none;">
                        <h5 class="">Restaurant Landing</h5>
                          <div class="row m-3">
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Search Landing Name, Id or Url </strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" name="search_value" id="search_attinput"
                                  class="form-control rounded-3 searchlandingvalue"  placeholder="Search restaurant landing"
                                  required><i class="ti ti-search"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary searchlandingpage" data-type="restaurant">Search</button>            
                            </div>
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Filter Results</strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" class="form-control rounded-3 filter-input" id="filter-restaurant"
                                  placeholder="Filter results" data-type="restaurant">
                                <i class="ti ti-filter"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="button" class="btn btn-outline-secondary filter-btn" data-type="restaurant">
                                Filter <i class="ti ti-search"></i>
                              </button>
                            </div>
                          </div>
                        </div>
						  
						   <div id="hotellistingBox" class="searchBox" style="display: none;">
                          <h5 class="m-3">Add Hotel Listing Landing</h5>
                          <div class="row m-3">
                            <div class="col-md-3 form-group">
                              <strong class="form-label">Search Name, Id or Url</strong>
                              <div class="form-search form-search-icon-right">
                               <input type="text" name="search_value" id="search_attinput"
                                  class="form-control rounded-3 searchlandingvalue"  placeholder="Search hotel_listing landing"
                                  required><i class="ti ti-search"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary searchlandingpage" data-type="hotellisting">Search</button>            
                            </div>
                          <div class="col-md-3 form-group">
                              <strong class="form-label">Filter Results</strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" class="form-control rounded-3 filter-input" id="filter-restaurant"
                                  placeholder="Filter results" data-type="restaurant">
                                <i class="ti ti-filter"></i>
                              </div>
                            </div>
                            <div class="col-md-3 mt-4">
                              <button type="button" class="btn btn-outline-secondary filter-btn" data-type="restaurant">
                                Filter <i class="ti ti-search"></i>
                              </button>
                            </div>
                          </div>
                        </div>
						  
                        <div id="experienceBox" class="searchBox" style="display: none;">
                        <h5 class="">Experience Landing</h5>
                        <div class="row">
                          <div class="col-md-3 form-group">
                            <strong class="form-label">Search Landing Name, Id or Url </strong>
                            <div class="form-search form-search-icon-right">
                              <input type="text" name="search_value" id="search_attinput" class="form-control rounded-3 searchlandingvalue"
                              placeholder="Search Experience Landing" required><i class="ti ti-search"></i>
                            </div>
                          </div>
                          <div class="col-md-3 mt-4">
                              <button type="submit" class="btn btn-outline-secondary searchlandingpage" data-type="experience">Search</button>            
                            </div>
                          <div class="col-md-3 form-group">
                            <strong class="form-label">Filter Results</strong>
                            <div class="form-search form-search-icon-right">
                              <input type="text" class="form-control rounded-3 filter-input" id="filter-experience"
                                placeholder="Filter results" data-type="experience">
                              <i class="ti ti-filter"></i>
                            </div>
                          </div>
                          <div class="col-md-3 mt-4">
                            <button type="button" class="btn btn-outline-secondary filter-btn" data-type="experience">
                              Filter <i class="ti ti-search"></i>
                            </button>
                          </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <!-- </form>  -->
                  </div>
                </div>
              </div>
              <div class="getfilterDataat m-3"></div>
            </div>
          </div>
        </div>
        <!-- [ form-element ] end -->
      </div>
    </div>
  </div>
</x-app-layout>