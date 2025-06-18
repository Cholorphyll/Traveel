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
                <li class="breadcrumb-item" aria-current="page">Add Category</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Add Category</h2>
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

                    <form class="" action="{{route('save_category')}}" method="POST" autocomplete="on">
                      @csrf

                      <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                          <div class="form-group mt-3">
                            <strong>Attraction Name</strong>
                            <div class="form-search form-search-icon-right">
                            <input type="text"  id="search_attractionfaq" name="sightname" value="{{ old('sightname') }}" class="search_attractionfaqs form-control rounded-3" placeholder="Search Attraction"
                                  required><i class="ti ti-search"></i> </div>
                            <ul id="country-list"></ul>
                           
                          <div class="form-group">
                            <strong>Category</strong>
                            <div class="form-search form-search-icon-right sp-category">
                          <input type="text" name="category" value="{{ old('category') }}" id="search_cat" class=" form-control" placeholder="Category">
                          <i class="ti ti-search"></i> </div></div>
                         <ul id="cat-list"></ul>
                          
                      </div>
                      <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary" >Submit</button>
                        <a href="{{route('search_attraction')}}" class="btn btn-danger">cancel</a>
                      </div>
                  </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

</x-app-layout>