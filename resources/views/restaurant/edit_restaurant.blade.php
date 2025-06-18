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
                <li class="breadcrumb-item" aria-current="page">Edit Restaurant</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Restaurant</h2>
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

                    <form class="" action="{{route('update_rest',[$restaurant->RestaurantId])}}" method="POST">
                      @csrf


                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Restaurant Name</strong>
                              <input type="text" name="rest_name" value="{{ $restaurant->Title}}" class="form-control rounded-3"
                                placeholder="Restaurant Name" required>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Page Slug</strong>
                              <input type="text" name="slug" class="form-control rounded-3" value="{{ $restaurant->Slug}}"
                                placeholder="Page slug" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Meta Title</strong>
                              <input type="text" name='MetaTagTitle' value="{{ $restaurant->MetaTagTitle}}" class="form-control rounded-3"
                                placeholder="">
                              <input type="hidden" value="" id="sightid">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3"
                                  placeholder="" value="">{{ $restaurant->MetaTagDescription}} </textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>About Restaurant</strong>
                                <textarea type="text" name='about' value="" class="form-control rounded-3"
                                  placeholder="">{{ $restaurant->About}}</textarea>
                              </div>
                            </div>
                          </div>
                          <h2><u>Location Info</u></h2>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 1</strong>
                                <?php
                                  $address = $restaurant->Address;
                                  $address1 = "";
                                  $address2 = "";

                                  $lastCommaPos = strrpos($address, ',');

                                  if ($lastCommaPos !== false) {
                                  
                                    $address1 = trim(substr($address, 0, $lastCommaPos));
                                    $address2 = trim(substr($address, $lastCommaPos + 1));
                                  } else {
                                    
                                    $address1 = $restaurant->Address;
                                  }
                                  ?>

                                <textarea type="text" name='addressline1' class="form-control rounded-3"
                                  placeholder="">{{$address1}}</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 2</strong>
                                <input type="text" name='addressline2' value="{{$address2}}" class="form-control rounded-3"
                                  placeholder="addressline2">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Neighborhood</strong>
                                <textarea type="text" name='neighborhood' value="" class="form-control rounded-3"
                                  placeholder="Neighborhood">{{$restaurant->neighborhood}}</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">

                                <strong>City</strong>
                                <div class="form-search form-search-icon-right">
                                  <input type="text" id="searchHotelcity" name="ctname" class="form-control rounded-3"
                                    placeholder="" value="@if(!$getcity->isEmpty()) {{$getcity[0]->Name}} @endif"><i class="ti ti-search"></i>
                                </div>

                                <span id="citylisth"></span>

                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Country</strong>
                                <input type="text" id="country" class="form-control" name="county" value="@if(!empty($country)) {{$country[0]->Name}} @endif">

                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pincode</strong>
                                <input type="number" name='pincode' class="form-control rounded-3" value="{{$restaurant->Pincode}}"
                                  placeholder="Pincode">
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Latitude</strong>
                                <input type="text" name='Latitude' value="{{$restaurant->Latitude}}" class="form-control rounded-3"
                                  placeholder="Enter Latitude">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Longitude</strong>
                                <input type="text" name='Longitude' value="{{$restaurant->Longitude}}" class="form-control rounded-3"
                                  placeholder="Enter Longitude">
                              </div>
                            </div>
                          </div>
                          @if($restaurant->Latitude !="" && $restaurant->Longitude !="")
                          <div class="col-md-12 mt-3 mb-3">
                             <div id = "map1" style = "width: 1414px; height: 250px"></div>
                          </div>
                          @endif
                          <h2><u>Timming And Pricing</u></h2>



                          <!-- start time to stay  -->
                          <div class="time-stay py-4 border-bottom">
                            <h5 class="mb-4  fs-22 ">How Long You`ll Stay</h5>
                            <div class=" datetims time b-20 border border-dark  px-3 py-3  text-center"
                              style="width: 150px;height: 120px;">
                              <img src="{{ asset('assets/images/clock.png')}}" width="55px" alt="">
                              <p class="small my-2">2 - 3 hours</p>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Pricing</strong>
                                <input type="text" name='pricing' value="{{$restaurant->PriceRange}}" class="form-control rounded-3"
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
                                <input type="text" name='website' value="{{$restaurant->Website}}" class="form-control rounded-3"
                                  placeholder="Enter website url">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Phone</strong>
                                <input type="number" name='phone' value="{{$restaurant->Phone}}" class="form-control rounded-3"
                                  placeholder="Enter Phone Number">
                              </div>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Email</strong>
                              <input type="email" name='email' value="{{$restaurant->Email}}" class="form-control rounded-3"
                                placeholder="Enter Email Address">
                            </div>
                          </div>

                          <h2><u>Nearest Stations</u></h2>
                        <?php $nearestStations = json_decode($restaurant->NearestStation); ?>
                        @if(!empty($nearestStations))
                  
                          @foreach($nearestStations as $station)

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Station Name</strong>
                                <input type="text" name='station_name[]' class="form-control rounded-3"
                                  value="@if(!empty($station)){{$station->station_name}} @endif" placeholder="Station Name">
                            
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Time</strong>
                                <input type="text" name='time[]' class="form-control rounded-3"
                                  value="@if(!empty($station)){{$station->time}} @endif" placeholder="15 minute walk">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Duration</strong>
                              <input type="text" name='duration[]' class="form-control rounded-3"
                                  value="@if(!empty($station)){{$station->duration}} @endif" placeholder="Duration">
                            
                            </div>
                          </div>
                          @endforeach
                          @else

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Station Name</strong>
                                <input type="text" name='station_name[]' class="form-control rounded-3"
                                  placeholder="Station Name">
                            
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Time</strong>
                                <input type="text" name='time[]' class="form-control rounded-3"
                                   placeholder="15 minute walk">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Duration</strong>
                              <input type="text" name='duration[]' class="form-control rounded-3"
                                  placeholder="Duration">                            
                            </div>
                          </div>

                          @endif
                          <div id="station"></div>
                          <h4 id="addstButton" class="float-right"><u>Add Nearest Station</u></h4>
                          <div class="form-check">
                            <input class="form-check-input" name="mustisee" type="checkbox" id="inlineFormCheck">
                            <label class="form-check-label" for="inlineFormCheck"> Must See</label>
                          </div>

                          <?php $Menu = json_decode($restaurant->Menu); ?>
                        @if(!empty($Menu))
               
                          @foreach($Menu as $Menu)
                          <div class="col-md-6 last-menu">
                            <div class="form-group mt-3">
                              <strong>Menu Item</strong>
                              <input type="text" name='menu[]' class="form-control rounded-3" value="{{$Menu->menuitem}}"
                                placeholder="menu item">                            
                            </div>
                          </div>
                          @endforeach
                          @else
                          <div class="col-md-6 last-menu">
                            <div class="form-group mt-3">
                              <strong>Menu Item</strong>
                              <input type="text" name='menu[]' class="form-control rounded-3" value=""
                                placeholder="menu item">                            
                            </div>
                          </div>
                          @endif
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
                                  <span class="feature-mnt">
                                    @if(!empty($RestaurantFeature))
                                        @foreach($RestaurantFeature as $RestaurantFeature)
                                        <span class="margin-l ft"><button class="btn btn-secondary featurn feature-mnt">{{$RestaurantFeature->Name}}</button><i class="fa fa-trash ml-3" id="">
                                        </i><input type="hidden" value="{{$RestaurantFeature->Name}}" name="featureitem[]"></span>
                                        @endforeach
                                    @endif
                                  </span>
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
                                  <span class="Dietary-mnt">                                  
                                  @if(!empty($RestaurantDeit))
                                        @foreach($RestaurantDeit as $RestaurantDeit)
                                        <span class="margin-l dt"><button class="btn btn-secondary Dietary Dietary-mnt">{{$RestaurantDeit->Name}}</button><i class="fa fa-trash ml-3" id="">
                                        </i><input type="hidden" value="{{$RestaurantDeit->Name}}" name="dietryitems[]"></span>
                                        @endforeach
                                    @endif
                                  </span>
                                </p>
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

                                  <?php     if(!empty($restaurant->Timings)){ $timingData = json_decode($restaurant->Timings, true);} ?>
                                  <div class="selectdays">
                                    <ul>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r1" id="r1"
                                          @if(!empty($restaurant->Timings)) <?= in_array('sun', array_keys($timingData['time'])) ? 'checked' : '';  ?> @endif >
                                          <label class="checkbox-alias" for="r1">S</label>
                                        </div>
                                      </li>

                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r2" id="r2"
                                          @if(!empty($restaurant->Timings)) <?= in_array('mon', array_keys($timingData['time'])) ? 'checked' : '';  ?> @endif >
                                          <label class="checkbox-alias" for="r2">M</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r3" id="r3"
                                          @if(!empty($restaurant->Timings)) <?= in_array('tue', array_keys($timingData['time'])) ? 'checked' : ''; ?> @endif >
                                          <label class="checkbox-alias" for="r3">T</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r4" id="r4"
                                          @if(!empty($restaurant->Timings)) <?= in_array('wed', array_keys($timingData['time'])) ? 'checked' : '';  ?>  @endif />
                                          <label class="checkbox-alias" for="r4">W</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r5" id="r5"
                                          @if(!empty($restaurant->Timings)) <?= in_array('thu', array_keys($timingData['time'])) ? 'checked' : ''; ?> @endif />
                                          <label class="checkbox-alias" for="r5">T</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r6" id="r6"
                                          @if(!empty($restaurant->Timings)) <?= in_array('fri', array_keys($timingData['time'])) ? 'checked' : ''; ?>  @endif >
                                          <label class="checkbox-alias" for="r6">F</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r7" id="r7"
                                          @if(!empty($restaurant->Timings)) <?=  in_array('sat', array_keys($timingData['time'])) ?  'checked' : ''; ?>  @endif />
                                          <label class="checkbox-alias" for="r7">S</label>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="mt-4 mb-4">
                                    <div class="form-check form-check-inline">
                                  
                                      <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        name="inlineCheckbox1"     @if(!empty($restaurant->Timings))  @if(array_key_exists('open_closed', $timingData) && $timingData['open_closed']['open24'] == 1 ) checked
                                        @endif @endif>

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
                                   
                                   @if(!empty($timingData['time']))
                                    @foreach ($timingData['time'] as $day => $times)

                                    <div class="row pls">
                                      <div class="col-md-5 col-5">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control" id="clopen"
                                            value="{{ $times['start'] }}" placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-5 col-5">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control" id="cltime"
                                            value="{{ $times['end'] }}" placeholder="Enter email" name="cltime[]">
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
                                    @endforeach
                                    @else 
                                    <div class="row pls">
                                      <div class="col-md-5 col-5">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control" id="clopen"
                                            placeholder="Enter email" name="opentime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-5 col-5">
                                        <div class="mb-2 mt-2">
                                          <input type="time" class="form-control" id="cltime"
                                           placeholder="Enter email" name="cltime[]">
                                        </div>
                                      </div>
                                      <div class="col-md-2 col-2">
                                      
                                        <div class="plusicon">+</div>
                                      
                                      </div>
                                    </div>
                                 
                                    @endif

                                  

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

  <script src="https://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
  <script>
  // Creating map options
  
  var mapOptions = {
    center: [{{$restaurant->Latitude}} ,{{$restaurant->Longitude}}],
    zoom: 10
  }

  // Creating a map object
  var map = new L.map('map1', mapOptions);

  // Creating a Layer object
  var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

  // Adding layer to the map
  map.addLayer(layer);

  let marker = new L.Marker([{{$restaurant->Latitude}} ,{{$restaurant->Longitude}}]);
 
  marker.addTo(map);
  marker.addTo(new L.Marker([{{$restaurant->Latitude}} ,{{$restaurant->Longitude}}]));
  </script>
</x-app-layout>