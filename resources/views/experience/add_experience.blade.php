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
                <li class="breadcrumb-item" aria-current="page">Add Experience</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h4 class="mb-0">Add Experience</h4>
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

                    <form class="" action="{{route('store_experience')}}" method="POST">
                      @csrf

                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Experience Name</strong>
                              <input type="text" name="name" class="form-control rounded-3" placeholder="Name"
                                required>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Page Slug</strong>
                              <input type="text" name="slug" class="form-control rounded-3" placeholder="Page slug"
                                required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Meta Title</strong>
                              <input type="text" name='MetaTagTitle' class="form-control rounded-3" placeholder="">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3"
                                  placeholder=""></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>About Experience</strong>
                                <textarea type="text" name='about' class="form-control rounded-3" placeholder=""
                                  required></textarea>
                              </div>
                            </div>
                          </div>
                          <h4><u>Location Info</u></h4>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 1</strong>
                                <textarea type="text" name='addressline1' class="form-control rounded-3"
                                  placeholder="addressline1"></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 2</strong>
                                <input type="text" name='addressline2' class="form-control rounded-3"
                                  placeholder="addressline2">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Neighborhood</strong>
                                <textarea type="text" name='neighborhood' class="form-control rounded-3"
                                  placeholder="Neighborhood"></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>City</strong>
                                <div class="form-search form-search-icon-right">
                                  <input type="text" id="searchlocationcity" name="ctname" class="form-control rounded-3"
                                    placeholder=""><i class="ti ti-search"></i>
                                </div>

                                <span id="citylisth"></span>
                              </div>
                            </div>

                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                              <strong>Country</strong>
                               <input type="text"  id="country" class="form-control" name="county" >  
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pincode</strong>
                                <input type="number" name='pincode' class="form-control rounded-3"
                                  placeholder="Pincode">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Latitude</strong>
                                <input type="text" name='Latitude' class="form-control rounded-3"
                                  placeholder="Enter Latitude">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Longitude</strong>
                                <input type="text" name='Longitude' class="form-control rounded-3"
                                  placeholder="Enter Longitude">
                              </div>
                            </div>
                          </div>


                          <h4><u>Key Details</u></h4>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Mobile Tickets Accepted</strong>
                                <select name="mobile_tickets" id="" class="form-control rounded-3">
                                  <option value="">Select</option>
                                  <option value="Yes">Yes</option>
                                  <option value="No">No</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Duration (In Hours)</strong>
                                <input type="time" name='duration' class="form-control rounded-3" placeholder="Duration">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Confirmation</strong>
                                <textarea type="text" name='Confirmation' class="form-control rounded-3"
                                  placeholder="Instant Confirmation"></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Free Cancellation</strong>
                                <input type="text" name="free_cancellation" id="" class="form-control rounded-3"
                                  placeholder="If you cancel 7 days in Advance">
                              </div>
                            </div>


                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pickup</strong>
                                <select name="pickup" id="" class="form-control rounded-3">
                                  <option value="Yes">Yes</option>
                                  <option value="No">No</option>
                                </select>

                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Booking Fee</strong>
                                <select name="bookingfee" id="" class="form-control rounded-3">
                                  <option value="NO">NO</option>
                                  <option value="Yes">Yes</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Language</strong>
                                <div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="searcLanguages" class="Languages form-control rounded-3" placeholder="Search Languages"><i class="ti ti-search"></i><div id="Languageslist"></div>
                              </div>
                            </div>
                            <p class="mt-3">
                                <span class="mt-3"><strong>Languages:</strong></span>
                                <span class="Language_value margin-l mt-3"></span>
                              </p>
                          </div>

                          <h4><u>Contact Info</u></h4>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Website</strong>
                                <input type="text" name='website' class="form-control rounded-3"
                                  placeholder="Enter website url">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Phone</strong>
                                <input type="number" name='phone' class="form-control rounded-3"
                                  placeholder="Enter Phone Number">
                              </div>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Email</strong>
                              <input type="email" name='email' class="form-control rounded-3"
                                placeholder="Enter Email Address">
                            </div>
                          </div>

                          <h4><u>Ticket Pricing</u></h4>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Price per adult</strong>
                                <input type="text" name='adult_price' class="form-control rounded-3" placeholder="INR500">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Price per minor</strong>
                                <input type="text" name='price_minor' class="form-control rounded-3" placeholder="INR200">
                              </div>
                            </div>

                          </div>

                          <h4><u>Other Details</u></h4>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Inclusions</strong>

                                <textarea name="Inclusions" id="" cols="20" rows="5" class="form-control"></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Exclusions</strong>
                                <textarea name="exclusions" id="" cols="20" rows="5" class="form-control"></textarea>
                              </div>
                            </div>

                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Departure Point</strong>
                                <input type="text" class="form-control" name="departure_point">

                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Return Point</strong>
                                <input type="text" class="form-control" name="return_point">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Additional Details</strong>

                                <textarea name="additionaldetails" id="" cols="20" rows="7" class="form-control"></textarea>
                              </div>
                            </div>


                          </div>







                        </div>

                        <div class="form-group mt-3">
                          <button type="submit" class="btn btn-primary save-timing">Submit</button>
                          <a href="" class="btn btn-danger">cancel</a>
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