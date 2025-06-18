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
                <li class="breadcrumb-item" aria-current="page">Manage Attraction</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Attraction <a href="{{route('add_attraction')}}" target="_blank" class="btn btn-info ml-3">
                    <strong>Add New Attraction</strong></a>
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
          <div class="col-md-8 alert alert-danger mt-3">
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
              <h5>Search Attraction</h5>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-12 mt-3">
                    <!-- <form method="post" action="{{route('search_location')}}"> -->
                    @csrf
                    <div class="row">
                      <div class="col-md-3 form-group">
                        <strong class="form-label">Search Name, Id or Url </strong>
                        <div class="form-search form-search-icon-right">
                          <input type="text" name="search_value" id="search_attinput" class="form-control rounded-3"
                            aria-describedby="emailHelp" placeholder="Page_Url" required><i class="ti ti-search"></i>
                        </div>
                      </div>
                      <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-outline-secondary" id="search_attraction">Search</button>
                      </div>
                    </div>
                    <!-- </form>  -->
                  </div>
                </div>
              </div>
              <div class="getfilterDataat"></div>

            </div>
          </div>
        </div>
        <!-- [ form-element ] end -->
      </div>
    </div>
  </div>

  <!-- JavaScript to handle the Enter key submission -->
  <script>
    // Get the search button and input elements
    const searchButton = document.getElementById('search_attraction');
    const searchInput = document.getElementById('search_attinput');

    // Handle Enter key submission and trigger search
    searchInput.addEventListener('keydown', function(event) {
      if (event.key === 'Enter') {
        event.preventDefault();  // Prevent form default behavior
        searchButton.click();    // Trigger the search button click
      }
    });
  </script>
</x-app-layout>
