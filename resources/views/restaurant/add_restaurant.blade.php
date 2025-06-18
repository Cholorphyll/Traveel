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
                <li class="breadcrumb-item" aria-current="page">Add Restaurant</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Add Restaurant</h2>
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

                    <form class="" action="{{route('store_rest')}}" method="POST">
                      @csrf

                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Restaurant Name</strong>
                              <input type="text" name="rest_name" value="" class="form-control rounded-3"
                                placeholder="Restaurant Name" required>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Page Slug</strong>
                              <input type="text" name="slug" class="form-control rounded-3" value=""
                                placeholder="Page slug" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Meta Title</strong>
                              <input type="text" name='MetaTagTitle' value="" class="form-control rounded-3"
                                placeholder="">
                              <input type="hidden" value="" id="sightid">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3"
                                  placeholder=""> </textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>About Restaurant</strong>
                                <textarea type="text" name='about' value="" class="form-control rounded-3"
                                  placeholder=""></textarea>
                              </div>
                            </div>
                          </div>
                          <h2><u>Location Info</u></h2>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 1</strong>
                                <?php // $address = explode(',',$getatt[0]->Address);
                              //  print_r($address);
                                ?>
                                <textarea type="text" name='addressline1' class="form-control rounded-3"
                                  placeholder=""></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 2</strong>
                                <input type="text" name='addressline2' value="" class="form-control rounded-3"
                                  placeholder="addressline2">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Neighborhood</strong>
                                <textarea type="text" name='neighborhood' value="" class="form-control rounded-3"
                                  placeholder="Neighborhood"></textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">

                                <strong>City</strong>
                                <div class="form-search form-search-icon-right">
                                  <input type="text" id="searchHotelcity" name="ctname" class="form-control rounded-3"
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
                                <input type="text" id="country" class="form-control" name="county">

                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pincode</strong>
                                <input type="number" name='pincode' class="form-control rounded-3" value=""
                                  placeholder="Pincode">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Latitude</strong>
                                <input type="text" name='Latitude' value="" class="form-control rounded-3"
                                  placeholder="Enter Latitude">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Longitude</strong>
                                <input type="text" name='Longitude' value="" class="form-control rounded-3"
                                  placeholder="Enter Longitude">
                              </div>
                            </div>
                          </div>
                          <h2><u>Timming And Pricing</u></h2>



                          <!-- start time to stay  -->
                          <div class="time-stay py-4 border-bottom">
                            <h5 class="mb-4  fs-22 ">How Long You`ll Stay</h5>
                            <div class=" datetims time b-20 border border-dark  px-3 py-3  text-center"
                              style="width: 150px;height: 120px;">
                              <img src="{{ asset('public/assets/images/clock.png')}}" width="55px" alt="">
                              <p class="small my-2">2 - 3 hours</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pricing</strong>
                                <input type="text" name='pricing' value="" class="form-control rounded-3"
                                  placeholder="INR100 – INR450">
                              </div>
                            </div>

                          </div>
                          <!-- end time to stay  -->
                          <h2><u>Contact Info</u></h2>

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Website</strong>
                                <input type="text" name='website' value="" class="form-control rounded-3"
                                  placeholder="Enter website url">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Phone</strong>
                                <input type="number" name='phone' value="" class="form-control rounded-3"
                                  placeholder="Enter Phone Number">
                              </div>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Email</strong>
                              <input type="email" name='email' value="" class="form-control rounded-3"
                                placeholder="Enter Email Address">
                            </div>
                          </div>

                          <h2><u>Nearest Stations</u></h2>



                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Station Name</strong>
                                <input type="text" name='station_name[]' class="form-control rounded-3" value=""
                                  placeholder="Station Name">
                                <input type="hidden" name='nearbyid[]' class="form-control rounded-3" value=""
                                  placeholder="Station Name">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Time</strong>
                                <input type="text" name='time[]' class="form-control rounded-3" value=""
                                  placeholder="15 minute walk">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Duration</strong>
                              <input type="number" name='duration[]' class="form-control rounded-3" value=""
                                placeholder="Duration">
                            </div>
                          </div>

                          <div id="station"></div>
                          <h4 id="addstButton" class="float-right"><u>Add Nearest Station</u></h4>
                          <div class="form-check">
                            <input class="form-check-input" name="mustisee" type="checkbox" id="inlineFormCheck">
                            <label class="form-check-label" for="inlineFormCheck"> Must See</label>
                          </div>

                          <div class="col-md-6 last-menu">
                            <div class="form-group mt-3">
                              <strong>Menu Item</strong>
                              <input type="text" name='menu[]' class="form-control rounded-3" value=""
                                placeholder="menu item">                            
                            </div>
                          </div>
                          <div id="menuitem" class="row"></div>                          
                          <h4 id="addmenu" class="float-right" style="float:right"><u>Add Menu</u></h4>
                          <h2><u>Features and Dietary Info</u></h2>
                          <div class="col-md-6">
                              <div class="form-group mt-3">

                                <strong>Features</strong>
                                <div class="form-search form-search-icon-right">
                                  <input type="text" id="searchfeature" name="" class="form-control rounded-3"
                                    placeholder=""><i class="ti ti-search"></i>
                                </div>

                                <span id="featurelist"></span>

                                <p class="mt-3">
                                  <span class="featurelist-heading d-none">Features</span>
                                  <span class="feature-mnt"></span>
                                </p>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">

                                <strong>Dietary</strong>
                                <div class="form-search form-search-icon-right">
                              
                                  <input type="text" id="searchDietary" name="" class="form-control rounded-3"
                                    placeholder=""><i class="ti ti-search"></i>
                                </div>

                                <span id="Dietarylist"></span>

                                <p class="mt-3">
                                  <span class="Dietarylist-heading d-none">Dietary</span>
                                  <span class="Dietary-mnt"></span>
                                </p>
                              </div>
                            </div>

                          <!-- Start Select dateTime  -->
                          <div
                            class="add-datetims d-none position-fixed d-flex align-items-center justify-content-center">
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
                                          <input type="checkbox" name="Group[]" value="r1" id="r1">
                                          <label class="checkbox-alias" for="r1">S</label>
                                        </div>
                                      </li>

                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r2" id="r2">
                                          <label class="checkbox-alias" for="r2">M</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r3" id="r3">
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
                                          <input type="checkbox" name="Group[]" value="r6" id="r6">
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

                                      <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        name="inlineCheckbox1">

                                      <label class="form-check-label" for="inlineCheckbox1">Open 24 hours</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                      <input class="form-check-input" type="checkbox" id="inlineCheckbox2"
                                        name="inlineCheckbox2">
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
                                    <?php $i=1;?>


                                    <div class="row pls">
                                      <div class="col-md-5 col-5">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control" id="clopen" value=""
                                            placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-5 col-5">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control" id="cltime" value=""
                                            placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2">
                                        @if($i == 1)
                                        <div class="plusicon">+</div>
                                        @else
                                        <div class="closeicon">x</div>
                                        @endif
                                      </div>
                                    </div>
                                    <?php $i++; ?>

                                  </div>

                    </form>
                  </div>

                  <a href="javascript:void(0)"
                    class="btn btn-danger bg-main text-white w-100 mt-2 py-2  rounded-pill  save-time"
                    style="color:red;">Save</a>
                </div>
              </div>
            </div>
            <!-- Start Select dateTime  -->
          </div>
          <div class="form-group mt-3">
            <button type="submit" class="btn btn-primary">Submit</button>
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