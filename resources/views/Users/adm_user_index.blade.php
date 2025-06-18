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
                <li class="breadcrumb-item" aria-current="page">Manage Admin Users</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Admin Users 
                <a href="{{route('add_admin_user')}}" target="_blank" class="btn btn-info ml-3"><strong>Add New User</strong></a> 
               
                 <a href="{{route('all_admin_user')}}" target="_blank" class="btn btn-info ml-3"><strong>All Users</strong></a> 
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
              <h5>Search Admin User</h5>
             
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-12">
                  <div class="col-md-12 mt-3">
                    <!-- <form method="post" action="{{route('search_location')}}"> -->
                    @csrf
                    <div class="row">
                      <div class="col-md-3 form-group">
                        <strong class="form-label">Search Name, Id or Email </strong>
                        <div class="form-search form-search-icon-right">
                          <input type="text"  id="search_input" class="form-control rounded-3"
                            aria-describedby="emailHelp" placeholder="Search User" required><i class="ti ti-search"></i>
                        </div>
                      </div>
                      <div class="col-md-3 mt-4">
                        <button type="submit" class="btn btn-outline-secondary" id="search_admin_users">Search</button>
                      </div>
                    </div>
                    <!-- </form>  -->
                  </div>
                </div>
              </div>
              <div class="getfilterData"></div>

            </div>
          </div>
        </div>
        <!-- [ form-element ] end -->
      </div>
    </div>
  </div>
</x-app-layout>