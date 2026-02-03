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
                <li class="breadcrumb-item" aria-current="page">Add Attraction</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Add Attraction</h2>
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

                    <form class="" action="{{route('store_attraction')}}" method="POST">
                      @csrf

                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Attraction Name</strong>
                              <input type="text" name="sight_name" class="form-control rounded-3"
                                placeholder="Sight Name" required>
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
                              <input type="text" name='MetaTagTitle' class="form-control rounded-3" placeholder=""
                                >
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3"
                                  placeholder="" ></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>About Attraction</strong> 
                                <textarea type="text" name='about' class="form-control rounded-3" placeholder=""
                                  ></textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Short Description</strong>
                                <textarea type="text" name='short_description' class="form-control rounded-3"
                                  placeholder=""></textarea>
                              </div>
                            </div>
                          
                          </div>


                          <h2><u>Location Info</u></h2>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 1</strong>
                                <textarea type="text" name='addressline1' class="form-control rounded-3" placeholder=""
                                  ></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 2</strong>
                                <input type="text" name='addressline2' class="form-control rounded-3"
                                  placeholder="Nearest Station" >
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Neighborhood</strong>
                                <textarea type="text" name='neighborhood' class="form-control rounded-3"
                                  placeholder="Neighborhood" ></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Country</strong>
                                <select name="county" id="" class="form-control">
                                  <option value="">Select Country</option>
                                  @if(!$country->isEmpty())
                                  @foreach($country as $country)
                                  <option value="{{$country->CountryId}}" >{{$country->Name}}</option>
                                  @endforeach
                                  @endif
                                </select>
                              </div>
                            </div>

                           
                          </div>
                          <div class="row">
                          <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>City</strong>
                                <input type="text"  id="search_city" name="ctname" class="form-control rounded-3" placeholder="Search City minimun 2 letters " autocomplete="off" >
                                  <input type="hidden" name='city' id="selected_city_id" class="form-control rounded-3" placeholder="City"
                                  >
                                  <span id="citylist"></span>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pincode</strong>
                                <input type="number" name='pincode' class="form-control rounded-3" placeholder="Pincode"
                                  >
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Latitude</strong>
                                <input type="text" name='Latitude' class="form-control rounded-3"
                                  placeholder="Enter Latitude" >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Longitude</strong>
                                <input type="text" name='Longitude' class="form-control rounded-3"
                                  placeholder="Enter Longitude" >
                              </div>
                            </div>
                          </div>
                          <h2><u>Timming And Duration</u></h2>

                          <!-- <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Timmings</strong>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Mon</strong><input type="time" name="monopn" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="monclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Tues</strong><input type="time" name="tueopn" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="tueclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Wed</strong><input type="time" name="wedopn" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="wedclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Thur</strong><input type="time" name="thur" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="thurclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Frid</strong><input type="time" name="friopn" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="friclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Sat</strong><input type="time" name="satopn" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="satclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col-md-6">
                                    <strong>Sun</strong><input type="time" name="sunopn" class="form-control rounded-3">
                                  </div>
                                  <div class="col-md-6">
                                  <strong>To
                                    </strong><input type="time" name="sunclo" class="form-control rounded-3">
                                  </div>
                                </div>
                                
                               
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Duration</strong>(In Hours)
                                <input type="text" name='duration' class="form-control rounded-3" placeholder="02:00"
                                  >
                              </div>
                            </div>
                          </div> -->
                            <!-- start time to stay  -->
                            <div class="time-stay py-4 border-bottom">
                            <h5 class="mb-4  fs-22 ">How Long You`ll Stay</h5>
                            <div class=" datetims time b-20 border border-dark  px-3 py-3  text-center"
                              style="width: 150px;height: 120px;">
                              <img src="{{ asset('assets/images/clock.png')}}" width="55px" alt="">
                              <p class="small my-2">2 - 3 hours</p>
                            </div>
                          </div>
                          <!-- end time to stay  -->

                          <h2><u>Contact Info</u></h2>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Website</strong>
                                <input type="text" name='website' class="form-control rounded-3"
                                  placeholder="Enter website url" >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Phone</strong>
                                <input type="number" name='phone' class="form-control rounded-3"
                                  placeholder="Enter Phone Number" >
                              </div>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Email</strong>
                              <input type="email" name='email' class="form-control rounded-3"
                                placeholder="Enter Email Address" >
                            </div>
                          </div>

                          <h2><u>Nearest Stations</u></h2>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Station Name</strong>
                                <input type="text" name='station_name[]' class="form-control rounded-3" 
                                  placeholder="Station Name" >
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Time</strong>
                                <input type="text" name='time[]' class="form-control rounded-3"
                                  placeholder="15 minute walk" >
                              </div>
                            </div> 
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Duration</strong>
                                <input type="number" name='duration[]' class="form-control rounded-3"
                                  placeholder="Duration" >
                              </div>
                            </div>
                          </div>
                          <div id="station"></div>
                          <h4 id="addstButton" class="float-right"><u>Add Nearest Station</u></h4>
                          <div class="form-check">
                            <input class="form-check-input" name="mustisee" type="checkbox" id="inlineFormCheck">
                            <label class="form-check-label" for="inlineFormCheck"> Must See</label>
                          </div>

                        </div>
                        <!-- Start Select dateTime  -->
<div class="add-datetims d-none position-fixed d-flex align-items-center justify-content-center">
          <div class="add-tip-box bg-white b-10 py-5 px-md-5 px-3 position-relative">
            <div class="container">
              <div role="button" class="close-datetims rounded-pill px-3 position-absolute">close X
              </div>
              <h3 class="text-md-center mb-3 fs-25">Select Days and Time</h3>
              <div class="">


                  <div class="selectdays">
                    <ul>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r1" id="r1" checked>
                          <label class="checkbox-alias" for="r1">S</label>
                        </div>
                      </li>

                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r2" id="r2" checked />
                          <label class="checkbox-alias" for="r2">M</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r3" id="r3" checked />
                          <label class="checkbox-alias" for="r3">T</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r4" id="r4" />
                          <label class="checkbox-alias" for="r4">W</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r5" id="r5" />
                          <label class="checkbox-alias" for="r5">T</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r6" id="r6" />
                          <label class="checkbox-alias" for="r6">F</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="r7" id="r7" />
                          <label class="checkbox-alias" for="r7">S</label>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="mt-4 mb-4">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="inlineCheckbox1"  name="inlineCheckbox1">
                      <label class="form-check-label" for="inlineCheckbox1">Open 24 hours</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="inlineCheckbox2"  name="inlineCheckbox2">
                      <label class="form-check-label" for="inlineCheckbox2">Closed</label>
                    </div>
                  </div>
                  <div class="mt-3 mb-3">
                    <span class="error"></span>
                    <div class="row">
                      <div class="col-md-5 col-5">
                        <div class=" mt-1">
                          <label for="email" class="form-label">Opens at</label>

                        </div>
                      </div>
                      <div class="col-md-5 col-5">
                        <div class="mt-1">
                          <label for="email" class="form-label">Closes at</label>

                        </div>
                      </div>

                    </div>
                    <div class="row pls">
                      <div class="col-md-5 col-5">
                        <div class="mb-2 mt-2">

                          <input type="time" class="form-control" id="clopen" placeholder="Enter email"
                            name="opentime[]">
                        </div>
                      </div>
                      <div class="col-md-5 col-5">
                        <div class="mb-2 mt-2">

                          <input type="time" class="form-control" id="cltime" placeholder="Enter email" name="cltime[]">
                        </div>
                      </div>
                      <div class="col-md-2 col-2">
                        <div class="plusicon">+</div>
                      </div>
                    </div>


                  </div>


                </form>
              </div>

                <a href="javascript:void(0)"  class="btn btn-danger bg-main text-white w-100 mt-2 py-2  rounded-pill  save-time" style="color:red;">Save</a>
            </div>
          </div>
        </div>
        <!-- Start Select dateTime  -->
                        <div class="form-group mt-3">
                          <button type="submit" class="btn btn-primary save-timing" >Submit</button>
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
<!-- Start Select dateTime  -->
<!-- <div class="add-datetims d-none position-fixed d-flex align-items-center justify-content-center">
          <div class="add-tip-box bg-white b-10 py-5 px-md-5 px-3 position-relative">
            <div class="container">
              <div role="button" class="close-datetims rounded-pill px-3 position-absolute">close X
              </div>
              <h3 class="text-md-center mb-3 fs-25">Select Days and Time</h3>
              <div class="">


                  <div class="selectdays">
                    <ul>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="1" id="r1" checked>
                          <label class="checkbox-alias" for="r1">S</label>
                        </div>
                      </li>

                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="rGroup[]" value="1" id="r2" checked />
                          <label class="checkbox-alias" for="r2">M</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="1" id="r3" checked />
                          <label class="checkbox-alias" for="r3">T</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="1" id="r4" />
                          <label class="checkbox-alias" for="r4">W</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="1" id="r5" />
                          <label class="checkbox-alias" for="r5">T</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="1" id="r6" />
                          <label class="checkbox-alias" for="r6">F</label>
                        </div>
                      </li>
                      <li>
                        <div class="invisible-checkboxes">
                          <input type="checkbox" name="Group[]" value="1" id="r7" />
                          <label class="checkbox-alias" for="r7">S</label>
                        </div>
                      </li>
                    </ul>
                  </div>
                  <div class="mt-4 mb-4">
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="inlineCheckbox1" value="option1">
                      <label class="form-check-label" for="inlineCheckbox1">Open 24 hours</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input class="form-check-input" type="checkbox" id="inlineCheckbox2" value="option2">
                      <label class="form-check-label" for="inlineCheckbox2">Closed</label>
                    </div>
                  </div>
                  <div class="mt-3 mb-3">
                    <span class="error"></span>
                    <div class="row">
                      <div class="col-md-5 col-5">
                        <div class=" mt-1">
                          <label for="email" class="form-label">Opens at</label>

                        </div>
                      </div>
                      <div class="col-md-5 col-5">
                        <div class="mt-1">
                          <label for="email" class="form-label">Closes at</label>

                        </div>
                      </div>

                    </div>
                    <div class="row pls">
                      <div class="col-md-5 col-5">
                        <div class="mb-2 mt-2">

                          <input type="time" class="form-control" id="clopen" placeholder="Enter email"
                            name="opentime[]">
                        </div>
                      </div>
                      <div class="col-md-5 col-5">
                        <div class="mb-2 mt-2">

                          <input type="time" class="form-control" id="cltime" placeholder="Enter email" name="cltime[]">
                        </div>
                      </div>
                      <div class="col-md-2 col-2">
                        <div class="plusicon">+</div>
                      </div>
                    </div>


                  </div>


                </form>
              </div>


              <button class="btn bg-main text-white w-100 mt-2 py-2  rounded-pill  save-time"
                style="background-color: red;">Submit</button>
            </div>
          </div>
        </div> -->
        <!-- Start Select dateTime  -->
</x-app-layout>