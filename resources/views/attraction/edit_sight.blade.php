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
                <li class="breadcrumb-item" aria-current="page">Edit Attraction</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Attraction</h2>
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

                    <form class="" action="{{route('update_att',[$getatt[0]->sid])}}" method="POST">
                      @csrf

                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Attraction Name</strong>
                              <input type="text" name="sight_name" value="{{$getatt[0]->Title}}"
                                class="form-control rounded-3" placeholder="Sight Name" required>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Page Slug</strong>
                              <input type="text" name="slug" class="form-control rounded-3" value="{{$getatt[0]->Slug}}"
                                placeholder="Page slug" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Meta Title</strong>
                              <input type="text" name='MetaTagTitle' value="{{$getatt[0]->MetaTagTitle}}"
                                class="form-control rounded-3" placeholder="">
                              <input type="hidden" value="{{$getatt[0]->sid}}" id="sightid">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3"
                                  placeholder="">{{$getatt[0]->MetaTagDescription}} </textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>About Attraction</strong>
                                <textarea type="text" name='about' value="" class="form-control rounded-3"
                                  placeholder="">{{$getatt[0]->About}}</textarea>
                              </div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Short Description</strong>
                                <textarea type="text" name='short_description' class="form-control rounded-3"
                                  placeholder="">{{$getatt[0]->short_description}} </textarea>
                              </div>
                            </div>                       
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Get In</strong>
                                <textarea type="text" name='GetIn' class="form-control rounded-3"
                                  placeholder="">{{$getatt[0]->GetIn}} </textarea>
                              </div>
                            </div>					                       

                          </div>

						<h2><u>Key Features</u></h2>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>What Users Like</strong>
                                <textarea name="likes" class="form-control rounded-3" rows="4" 
                                  placeholder="Enter features that users like">{{$getatt[0]->Likes ?? ''}}</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>What Users Dislike</strong>
                                <textarea name="dislikes" class="form-control rounded-3" rows="4"
                                  placeholder="Enter features that users dislike">{{$getatt[0]->Dislikes ?? ''}}</textarea>
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
                                  placeholder="">{{$getatt[0]->Address}}</textarea>
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
                                  placeholder="Neighborhood">{{$getatt[0]->Neighbourhood}}</textarea>
                              </div>
                            </div>
                          
							    <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>City</strong>
                                <input type="text" id="search_city" name="ctname" class="form-control rounded-3"
                                  value="{{$getatt[0]->Name}}" placeholder="Search City minimun 2 letters ">
                                <input type="hidden" name='city' id="selected_city_id" class="form-control rounded-3"
                                  placeholder="City">
                                <span id="citylist"></span>
                              </div>
                            </div>
							  
                          </div>
                          <div class="row">
                          

							    <div class="col-md-6">
                              <div class="form-group mt-3"  id="country">
                                <strong>Country</strong>
                                <select name="county"  class="form-control" >
                                  <option value="">Select Country with City</option>
                                  @if(!$country->isEmpty())
                                  @foreach($country as $country)
                                  <option value="{{$country->CountryId}}" @if($country->CountryId ==
                                    $getatt[0]->CountryId) selected @endif >{{$country->Name}}</option>
                                  @endforeach
                                  @endif
                                </select>
                              </div>
                            </div>
							  
                            <div class="col-md-6">
                               <div class="form-group mt-3">
                                <strong>Pincode</strong>
                                <input type="text" name='pincode' class="form-control rounded-3 @error('pincode') is-invalid @enderror"
                                  value="{{$getatt[0]->Pincode}}" placeholder="Pincode">
                                @error('pincode')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                              </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Latitude</strong>
                                <input type="text" name='Latitude' value="{{$getatt[0]->Latitude}}"
                                  class="form-control rounded-3" placeholder="Enter Latitude">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Longitude</strong>
                                <input type="text" name='Longitude' value="{{$getatt[0]->Longitude}}"
                                  class="form-control rounded-3" placeholder="Enter Longitude">
                              </div>
                            </div>
                          </div>
							
						<div class="col-md-12 mt-3 mb-3">
                          <div id = "map1" style = "width: 1414px; height: 250px"></div>
                        </div>
                          <h2><u>Timing And Duration</u></h2>

                   

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
                                <input type="text" name='website' value="{{$getatt[0]->Website}}"
                                  class="form-control rounded-3" placeholder="Enter website url">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Phone</strong>
                                <input type="text" name='phone' value="{{$getatt[0]->Phone}}"
                                  class="form-control rounded-3" placeholder="Enter Phone Number">
                              </div>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Email</strong>
                               <input type="text" name='email' value="{{$getatt[0]->Email}}"
                                class="form-control rounded-3" placeholder="Enter Email Address">
                            </div>
                          </div>

                          <h2><u>Nearest Stations</u></h2>

                          @foreach($nearestStations as $station)

                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Station Name</strong>
                                <input type="text" name='station_name[]' class="form-control rounded-3"
                                  value="@if(!empty($station->NearBy)){{$station->NearBy}} @endif" placeholder="Station Name">
                                <input type="hidden" name='nearbyid[]' class="form-control rounded-3"
                                  value="{{$station->NearById}}" placeholder="Station Name">
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Time</strong>
                                <input type="text" name='time[]' class="form-control rounded-3"
                                  value="@if(!empty($station->Time)){{$station->Time}} @endif" placeholder="15 minute walk">
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Duration</strong>
                              <input type="number" name='duration[]' class="form-control rounded-3"
                                @if(!empty($station->Duration)) value="{{$station->Duration}}" @else placeholder="10" @endif>
                            </div>
                          </div>
                          @endforeach
                          <div id="station"></div>
                          <h4 id="addstButton" class="float-right"><u>Add Nearest Station</u></h4>
                          <div class="form-check">
                            <input class="form-check-input" name="mustisee" type="checkbox" id="inlineFormCheck">
                            <label class="form-check-label" for="inlineFormCheck"> Must See</label>
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

                                  <?php     if(!$SightTiming->isEmpty()){ $timingData = json_decode($SightTiming[0]->timings, true);} ?>
                                  <div class="selectdays">
                                    <ul>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r1" id="r1"
                                          @if(!$SightTiming->isEmpty()) <?= in_array('sun', array_keys($timingData['time'])) ? 'checked' : '';  ?> @endif >
                                          <label class="checkbox-alias" for="r1">S</label>
                                        </div>
                                      </li>

                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r2" id="r2"
                                          @if(!$SightTiming->isEmpty()) <?= in_array('mon', array_keys($timingData['time'])) ? 'checked' : '';  ?> @endif >
                                          <label class="checkbox-alias" for="r2">M</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r3" id="r3"
                                          @if(!$SightTiming->isEmpty()) <?= in_array('tue', array_keys($timingData['time'])) ? 'checked' : ''; ?> @endif >
                                          <label class="checkbox-alias" for="r3">T</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r4" id="r4"
                                          @if(!$SightTiming->isEmpty()) <?= in_array('wed', array_keys($timingData['time'])) ? 'checked' : '';  ?>  @endif />
                                          <label class="checkbox-alias" for="r4">W</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r5" id="r5"
                                          @if(!$SightTiming->isEmpty()) <?= in_array('thu', array_keys($timingData['time'])) ? 'checked' : ''; ?> @endif />
                                          <label class="checkbox-alias" for="r5">T</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r6" id="r6"
                                             @if(!$SightTiming->isEmpty()) <?= in_array('fri', array_keys($timingData['time'])) ? 'checked' : ''; ?>  @endif >
                                          <label class="checkbox-alias" for="r6">F</label>
                                        </div>
                                      </li>
                                      <li>
                                        <div class="invisible-checkboxes">
                                          <input type="checkbox" name="Group[]" value="r7" id="r7"
                                          @if(!$SightTiming->isEmpty()) <?=  in_array('sat', array_keys($timingData['time'])) ?  'checked' : ''; ?>  @endif />
                                          <label class="checkbox-alias" for="r7">S</label>
                                        </div>
                                      </li>
                                    </ul>
                                  </div>
                                  <div class="mt-4 mb-4">
                                    <div class="form-check form-check-inline">
                                    
                                      <input class="form-check-input" type="checkbox" id="inlineCheckbox1"
                                        name="inlineCheckbox1"    @if(!empty($SightTiming[0]->timings))  @if($timingData['open_closed']['open24'] == 1) checked
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
                                    @if(!$SightTiming->isEmpty())
                                   @if(!empty($timingData['time'] ))
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
		 <script src="https://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
  <script>
  // Creating map options
  
  var mapOptions = {
    center: [ {{$getatt[0]->Latitude}} ,{{$getatt[0]->Longitude}}],
    zoom: 7
  }

  // Creating a map object
  var map = new L.map('map1', mapOptions);

  // Creating a Layer object
  var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

  // Adding layer to the map
  map.addLayer(layer);

  let marker = new L.Marker([ {{$getatt[0]->Latitude}},{{$getatt[0]->Longitude}}]);
 
  marker.addTo(map);
  marker.addTo(new L.Marker([ {{$getatt[0]->Latitude}} ,{{$getatt[0]->Longitude}}]));
  </script>
</x-app-layout>