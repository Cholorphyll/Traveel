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
                <li class="breadcrumb-item" aria-current="page">Edit Experience</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h4 class="mb-0">Edit Experience</h4>
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
                    @if(!$get_exp->isEmpty())
                    <form >
                      @csrf

                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexp(1)">Edit</span>
                              <strong>Experience Name</strong>
                              <input type="text" name="name" class="form-control rounded-3 name1" value="{{$get_exp[0]->Name}}"  data-original-value="{{$get_exp[0]->Name}}" placeholder="Name"
                                required disabled>
                            </div>

                            <span id="buttonsContainer-1" class="buttons-container-dd d-none mb-3"
                              data-colid="{{ $get_exp[0]->ExperienceId }}">
                              <button type="button" value="1"
                                class=" btn btn-dark save-button px-4 updateexp"
                                data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="1">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="cancelexp(1)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">

                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexp(2)">Edit</span>
                              <strong>Page Slug</strong>
                              <input type="text" name="slug" class="form-control rounded-3 name2" value="{{$get_exp[0]->Slug}}"
                                placeholder="Page slug" data-original-value="{{$get_exp[0]->Slug}}" required disabled>
                            </div>

                            <span id="buttonsContainer-2" class="buttons-container-dd d-none mb-3"
                              data-colid="2">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updateexp"
                                data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="2">Save</button>

                              <button type="button" value="2" id="cancel-2" onclick="cancelexp(2)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexp(3)">Edit</span>
                              <strong>Meta Title</strong>
                              <input type="text" name='MetaTagTitle' class="form-control rounded-3 name3" data-original-value="{{$get_exp[0]->MetaTitle}}" value="{{$get_exp[0]->MetaTitle}}"  placeholder=""
                                disabled>
                            </div>

                            <span id="buttonsContainer-3" class="buttons-container-dd d-none mb-3"
                              data-colid="{{ $get_exp[0]->ExperienceId }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updateexp"
                                data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="3">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="cancelexp(3)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                  onclick="editexp(4)">Edit</span>
                                <strong>Meta Description</strong> 
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3 name4" data-original-value="{{$get_exp[0]->MetaDescription}}"
                                  placeholder="" disabled>{{$get_exp[0]->MetaDescription}}</textarea>
                              </div>
                              <span id="buttonsContainer-4" class="buttons-container-dd d-none mb-3"
                                data-colid="{{ $get_exp[0]->ExperienceId }}">
                                <button type="button" value="1"
                                  class="reviewField- btn btn-dark save-button px-4 updateexp"
                                  data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="4">Save</button>

                                <button type="button" value="2" id="cancel-1" onclick="cancelexp(4)"
                                  class=" btn btn-dark cancel-button px-4">Cancel</button>
                              </span>
                            </div>
                            <input type="hidden" id="exp_id" value="{{$get_exp[0]->ExperienceId}}">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                  onclick="editexp(5)">Edit</span>
                                <strong>About Experience</strong>
                                <textarea type="text" name='about' class="form-control rounded-3 name5" data-original-value="{{$get_exp[0]->about}}" 
                                  disabled>{{$get_exp[0]->about}}</textarea>
                              </div>

                              <span id="buttonsContainer-5" class="buttons-container-dd d-none mb-3"
                                data-colid="{{ $get_exp[0]->ExperienceId }}">
                                <button type="button" value="1"
                                  class="reviewField- btn btn-dark save-button px-4 updateexp"
                                  data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="5">Save</button>

                                <button type="button" value="2" id="cancel-1" onclick="cancelexp(5)"
                                  class=" btn btn-dark cancel-button px-4">Cancel</button>
                              </span>
                            </div>
                          </div>

                          <h4><u>Location Info</u></h4>
                          <div class="row">
                            <div class="col-md-12">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexp(6)">Edit</span>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 1</strong>
                                <textarea type="text" name='addressline1' class="form-control rounded-3 name6" data-original-value="@if(!$ExpContactdetail->isEmpty()){{$ExpContactdetail[0]->Address}}@endif"
                                  placeholder="addressline1" disabled>@if(!$ExpContactdetail->isEmpty()){{$ExpContactdetail[0]->Address}}@endif</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 2</strong>
                                <input type="text" name='addressline2' class="form-control rounded-3 name6"
                                  placeholder="addressline2" disabled>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Neighborhood</strong>
                                <textarea type="text" name='neighborhood' class="form-control rounded-3 name6" data-original-value="@if(!$get_neigh->isEmpty()){{$get_neigh[0]->Name}} @endif"
                                  disabled>@if(!$get_neigh->isEmpty()){{$get_neigh[0]->Name}}@endif</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>City</strong>
                                <div class="form-search form-search-icon-right">
                                  <input type="text" id="searchlocationcity" name="ctname" value="{{$get_exp[0]->Lname}}" data-original-value="{{$get_exp[0]->Lname}}"
                                    class="form-control rounded-3 name6" placeholder="" disabled><i class="ti ti-search"></i>
                                </div>

                                <span id="citylisth"></span>
                              </div>
                            </div>

                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Country</strong>
                                <input type="text" id="country" class="form-control name6" value="{{$getcountry[0]->Name}}" name="county" data-original-value="{{$get_exp[0]->Lname}}" disabled>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pincode</strong>
                                <input type="number" name='pincode' class="form-control rounded-3 name6" data-original-value="{{$get_exp[0]->Pincode}}"  value="{{$get_exp[0]->Pincode}}" placeholder="Pincode"
                                  disabled>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Latitude</strong>
                                <input type="text" name='Latitude' class="form-control rounded-3 name6"
                                  placeholder="Enter Latitude" value="{{$get_exp[0]->Latitude}}"  data-original-value="{{$get_exp[0]->Latitude}}"   disabled>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Longitude</strong>
                                <input type="text" name='Longitude' class="form-control rounded-3 name6"  data-original-value="{{$get_exp[0]->Longitude}}" value="{{$get_exp[0]->Longitude}}" 
                                  placeholder="Enter Longitude" disabled>
                              </div>
                            </div>
                          </div>

                          <span id="buttonsContainer-6" class="buttons-container-dd d-none mb-3"
                            data-colid="{{ $get_exp[0]->ExperienceId }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 updateexp"
                              data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="6">Save</button>

                            <button type="button" value="2" id="cancel-1" onclick="cancelexp(6)"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>
                          <div class="col-md-12 mt-3 mb-3">
                        <div id = "map1" style = "width: 1414px; height: 250px"></div>
                        </div>

                          <h4><u>Key Details</u></h4>
                          <div class="row">
                            <div class="col-md-12">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexp(7)">Edit</span>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Mobile Tickets Accepted</strong>
                                <select name="mobile_tickets" id="" class="form-control rounded-3 name7" disabled>
                                  <option value="">Select</option>
                                  <option value="Yes" @if($get_exp[0]->mobile_tickets == 'Yes') selected @endif >Yes</option>
                                  <option value="No" @if($get_exp[0]->mobile_tickets == 'No') selected @endif>No</option>
                                </select>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Duration (In Hours)</strong>
                                <input type="time" name='duration' class="form-control rounded-3 name7" data-original-value="{{$get_exp[0]->Duration}}" value="{{$get_exp[0]->Duration}}"
                                  placeholder="Duration" disabled>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Confirmation</strong>
                                <textarea type="text" name='Confirmation' class="form-control rounded-3 name7"
                                  placeholder="Instant Confirmation" data-original-value="{{$get_exp[0]->Confirmation}}"  disabled>{{$get_exp[0]->Confirmation}}</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Free Cancellation</strong>
                                <input type="text" name="free_cancellation" id="" class="form-control rounded-3 name7" data-original-value="{{$get_exp[0]->FreeCancellation}}"
                                  placeholder="If you cancel 7 days in Advance" value="{{$get_exp[0]->FreeCancellation}}" disabled>
                              </div>
                            </div>


                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pickup</strong>
                                <select name="pickup" id="" class="form-control rounded-3 name7" disabled>
                                  <option value="Yes" @if($get_exp[0]->Pickup == "Yes") Selected @endif>Yes</option>
                                  <option value="No" @if($get_exp[0]->Pickup == "No") Selected @endif>No</option>
                                </select>

                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Booking Fee</strong>
                                <select name="bookingfee" id="" class="form-control rounded-3 name7" disabled>
                                  <option value="NO" @if($get_exp[0]->booking_fee == "Yes") Selected @endif>NO</option>
                                  <option value="Yes" @if($get_exp[0]->booking_fee == "Yes") Selected @endif>Yes</option>
                                </select>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Language</strong>
                                <div class="form-search form-search-icon-right change-field mb-3">
                                  <input type="text" name="" id="searcLanguages"
                                    class="Languages form-control rounded-3" placeholder="Search Languages" ><i
                                    class="ti ti-search"></i>
                                  <div id="Languageslist"></div>
                                </div>
                              </div>
                              <p class="mt-3">
                                <span class="mt-3"><strong>Languages:</strong></span>
                                <span class="Language_value margin-l mt-3">
                                  @if(!$getlang->isEmpty())
                                  <span><input type="hidden" value="{{$getlang[0]->Language}}" name="language[]"><button class="btn btn-secondary margin-top ">{{$getlang[0]->Language}}</button><i class="fa fa-trash ml-3" ></i></span>
                                  @endif
                                </span>
                              </p>
                            </div>

                            <span id="buttonsContainer-7" class="buttons-container-dd d-none mb-3"
                              data-colid="{{ $get_exp[0]->ExperienceId }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updateexp"
                                data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="7">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="cancelexp(7)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <h4><u>Contact Info</u></h4>
                            <div class="row">
                              <div class="col-md-12">
                                <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                  onclick="editexp(8)">Edit</span>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Website</strong>
                                  <input type="text" name='website' class="form-control rounded-3 name8"
                                    placeholder="Enter website url" value="{{$get_exp[0]->website}}" data-original-value="{{$get_exp[0]->website}}"  disabled>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Phone</strong>
                                  <input type="number" name='phone' class="form-control rounded-3 name8"  data-original-value="@if(!$ExpContactdetail->isEmpty()){{$ExpContactdetail[0]->Phone}}@endif" value="@if(!$ExpContactdetail->isEmpty()){{$ExpContactdetail[0]->Phone}}@endif"
                                    placeholder="Enter Phone Number" disabled>
                                </div>
                              </div>

                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Email</strong>
                                <input type="email" name='email' class="form-control rounded-3 name8" data-original-value="@if(!$ExpContactdetail->isEmpty()){{$ExpContactdetail[0]->Email}}@endif" value="@if(!$ExpContactdetail->isEmpty()){{$ExpContactdetail[0]->Email}}@endif"
                                  placeholder="Enter Email Address" disabled>
                              </div>
                            </div>


                            <span id="buttonsContainer-8" class="buttons-container-dd d-none mb-3"
                              data-colid="{{ $get_exp[0]->ExperienceId }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 updateexp"
                                data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="8">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="cancelexp(8)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <h4><u>Ticket Pricing</u></h4>
                            <div class="row">
                              <div class="col-md-12">
                                <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                  onclick="editexp(9)">Edit</span>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Price per adult</strong>
                                  <input type="text" name='adult_price' class="form-control rounded-3 name9" value="{{ $get_exp[0]->adult_price }}" data-original-value="{{ $get_exp[0]->adult_price }}"
                                    placeholder="INR500" disabled>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Price per minor</strong>
                                  <input type="text" name='price_minor' data-original-value="{{ $get_exp[0]->minor_price }}" class="form-control rounded-3 name9" value="{{ $get_exp[0]->minor_price }}"
                                    placeholder="INR200"  disabled>
                                </div>
                              </div>
                              <span id="buttonsContainer-9" class="buttons-container-dd d-none mb-3"
                                data-colid="{{ $get_exp[0]->ExperienceId }}">
                                <button type="button" value="1"
                                  class="reviewField- btn btn-dark save-button px-4 updateexp"
                                  data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="9">Save</button>

                                <button type="button" value="2" id="cancel-1" onclick="cancelexp(9)"
                                  class=" btn btn-dark cancel-button px-4">Cancel</button>
                              </span>
                            </div>


                            <h4><u>Other Details</u></h4>
                            <div class="row">
                              <div class="col-md-12">
                                <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                  onclick="editexp(10)">Edit</span>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Inclusions</strong>

                                  <textarea name="Inclusions" id="" cols="20" rows="5"
                                    class="form-control name10"  data-original-value="{{ $get_exp[0]->Inclusive }}" disabled>{{ $get_exp[0]->Inclusive }}</textarea>
                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Exclusions</strong>
                                  <textarea name="exclusions" id="" cols="20" rows="5"
                                    class="form-control name10"  data-original-value="{{ $get_exp[0]->Exclusive }}" disabled>{{ $get_exp[0]->Exclusive }}</textarea>
                                </div>
                              </div>

                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Departure Point</strong>
                                  <input type="text" class="form-control name10" name="departure_point" data-original-value="{{ $get_exp[0]->DeparturePoint }}" value="{{ $get_exp[0]->DeparturePoint }}" disabled>

                                </div>
                              </div>
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Return Point</strong>
                                  <input type="text" class="form-control name10" name="return_point" value="{{ $get_exp[0]->ReturnDetails }}"  data-original-value="{{ $get_exp[0]->ReturnDetails }}" disabled>
                                </div>
                              </div>
                            </div>
                            <div class="row">
                              <div class="col-md-6">
                                <div class="form-group mt-3">
                                  <strong>Additional Details</strong>

                                  <textarea name="additionaldetails" id="" cols="20" rows="7" data-original-value="{{ $get_exp[0]->AdditionalInformation }}"
                                    class="form-control name10" disabled>{{ $get_exp[0]->AdditionalInformation }}</textarea>
                                </div>
                              </div>

                              <span id="buttonsContainer-10" class="buttons-container-dd d-none mb-3"
                                data-colid="{{ $get_exp[0]->ExperienceId }}">
                                <button type="button" value="1"
                                  class="reviewField- btn btn-dark save-button px-4 updateexp"
                                  data-id="{{ $get_exp[0]->ExperienceId }}" data-colid="10">Save</button>

                                <button type="button" value="2" id="cancel-1" onclick="cancelexp(10)"
                                  class=" btn btn-dark cancel-button px-4">Cancel</button>
                              </span>

                            </div>

                          </div>

                          
                        </div>

                    </form>
                    @else
                      <p>Data not found.</p>
                    @endif

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <script src="https://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
  <script>
  // Creating map options
  
  var mapOptions = {
    center: [{{$get_exp[0]->Latitude}} ,{{$get_exp[0]->Longitude}}],
    zoom: 10
  }

  // Creating a map object
  var map = new L.map('map1', mapOptions);

  // Creating a Layer object
  var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

  // Adding layer to the map
  map.addLayer(layer);

  let marker = new L.Marker([{{$get_exp[0]->Latitude}} ,{{$get_exp[0]->Longitude}}]);
 
  marker.addTo(map);
  marker.addTo(new L.Marker([{{$get_exp[0]->Latitude}} ,{{$get_exp[0]->Longitude}}]));
  </script>

</x-app-layout>