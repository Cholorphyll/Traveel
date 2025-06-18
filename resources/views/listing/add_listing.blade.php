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
                <li class="breadcrumb-item" aria-current="page">Add Listing</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Add Location</h2>
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
                    <br>

                    <form class="" action="{{route('store_listing')}}" method="POST">
                      @csrf

                      <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">
                          <!-- <div class="form-group mt-3">
                            <strong>Parent Location</strong>
                            <input type="text" name='parent_id' id="country-input" class="form-control rounded-3"
                              placeholder="Enter Parent Id">
                          </div> -->
                          <div class="form-group mt-3">
                            <strong>Parent Location</strong>
                            <input type="text" id="search_city" name="ctname" class="form-control rounded-3"
                              placeholder="Search City minimun 2 letters ">
                            <input type="hidden" name='city' id="selected_city_id" class="form-control rounded-3"
                              placeholder="City">
                            <span id="citylist"></span>
                          </div>


                          <div class="form-group">
                            <strong>Location Name</strong>
                            <input type="text" name="location_name" class="form-control rounded-3"
                              placeholder="location Name">
                          </div>
                          <div class="form-group">
                            <strong>Page slug</strong>
                            <input type="text" name="page_slug" class="form-control rounded-3"
                              placeholder="location Name">
                          </div>
                          <div class="form-group mt-3">

                            <strong>Country</strong>
                            <select name="Countryid" id="" class="form-control">
                              <option value="">Select Country</option>
                              @if(!$country->isEmpty())
                              @foreach($country as $country)
                              <option value="{{$country->CountryId}}">{{$country->Name}}</option>
                              @endforeach
                              @endif
                            </select>
                          </div>
                          <div class="form-group mt-3">
                            <strong>Meta Titile</strong>
                            <input type="text" name='meta_title' class="form-control rounded-3" placeholder="Meta Title"
                              >
                          </div>
                          <div class="form-group mt-3">
                            <strong>Meta Description</strong>
                            <textarea type="text" name='meta_desc' class="form-control rounded-3" placeholder=""
                              ></textarea>
                          </div>

                          <div class="form-group mt-3">
                            <strong>About Location</strong>
                            <textarea type="text" name='about' class="form-control rounded-3" placeholder=""
                              ></textarea>
                          </div>
                          <div class="form-group mt-3">
                            <strong>Pincode</strong>
                            <input type="text" name='upload_location' class="form-control rounded-3"
                              placeholder="Upload Location" >
                          </div>
                        </div>
                      </div>
                      <div class="form-group mt-3">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <a href="{{route('search_location')}}" class="btn btn-danger">cancel</a>
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