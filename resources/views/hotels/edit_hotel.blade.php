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
                <li class="breadcrumb-item" aria-current="page">Edit Hotel</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Hotel</h2>
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

                    <form class="" action="{{ route('updateHotel',[$gethotel[0]->id])}}" method="POST">
                      @csrf
                      <div class="row">
                        <div class="col-md-12">

                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Hotel Name</strong>
                              <input type="text" name="hotel_name" value="{{$gethotel[0]->name}}"
                                class="form-control rounded-3" placeholder="Hotel Name" required>
                            </div>

                          </div>
                          <div class="col-md-6">
                            <div class="form-group">
                              <strong>Page Slug</strong>
                              <input type="text" name="slug" class="form-control rounded-3"
                                value="{{$gethotel[0]->slug}}" placeholder="Page slug" required>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Meta Title</strong>
                              <input type="text" name='MetaTagTitle' value="{{$gethotel[0]->metaTagTitle}}"
                                class="form-control rounded-3" placeholder="">
                              <input type="hidden" value="" id="sightid">
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-md-6">  
                              <div class="form-group mt-3">
                                <strong>Meta Description</strong>
                                <textarea type="text" name='MetaTagDescription' class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->MetaTagDescription}} </textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>About Hotel</strong>
                                <textarea type="text" name='about' value="" class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->about}}</textarea>
                              </div>
                            </div>
                          </div>

                          <div class="row">
                           
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Short Description</strong>
                                <textarea type="text" name='short_description' value="" class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->short_description}}</textarea>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Spotlight</strong>
                                <textarea type="text" name='Spotlights' value="" class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->Spotlights}}</textarea>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Things to Know</strong>
                                <textarea type="text" name='ThingstoKnow' value="" class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->ThingstoKnow}}</textarea>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>checkIn</strong>
                                <textarea type="text" name='checkIn' value="" class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->checkIn}}</textarea>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>checkOut</strong>
                                <textarea type="text" name='checkOut' value="" class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->checkOut}}</textarea>
                              </div>
                            </div>

                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Highlights</strong>
                                <textarea name="Highlights" class="form-control rounded-3" placeholder="">
  {{ is_array(json_decode($gethotel[0]->Highlights, true)) 
      ? implode(', ', json_decode($gethotel[0]->Highlights, true)) 
      : $gethotel[0]->Highlights }}
</textarea>
                              </div>
                            </div>

                            <div class="col-md-6">
    <div class="form-group mt-3">
        <strong>Reviews</strong>
        <br>

        <div id="reviews-container">
            @if (!$getreviews->isEmpty())
                @foreach ($getreviews as $review)
                    <div class="col-md-12 form-group mt-3">
                        <strong>Review</strong>
                        <textarea name="review[]" class="form-control rounded-3">{{ $review->Description }}</textarea>
                        <input type="hidden" name="reviewId[]" value="{{ $review->HotelReviewId }}">
                        <strong>Rating (out of 5)</strong>
                        <input type="number" name="rating[]" min="1" max="5" value="{{ $review->Rating ?? 5 }}" class="form-control rounded-3">
                    </div>
                    <hr>
                @endforeach
            @else
                <p>No reviews found. Add a new review.</p>
            @endif
        </div>

        <!-- Add Review Button to open the modal -->
        <button type="button" class="btn btn-dark mt-3" data-bs-toggle="modal" data-bs-target="#addReviewModal">
            Add Review
        </button>

        <!-- Add Review Modal -->
        <div class="modal fade" id="addReviewModal" tabindex="-1" aria-labelledby="addReviewModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addReviewModalLabel">Add New Review</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mt-3">
                            <strong>Review</strong>
                            <textarea id="newReviewContent" class="form-control rounded-3"></textarea>
                            <strong>Rating (out of 5)</strong>
                            <input id="newReviewRating" type="number" min="1" max="5" class="form-control rounded-3">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="addReviewButton" class="btn btn-primary">Add Review</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

                          </div>
                            <h3><u>FAQ Section</u></h3>
                            <div class="row">
                            <div class="col-md-6">
    <div class="form-group mt-3">
        <strong>FAQ</strong>
        <br>

        @if (!$getfaq->isEmpty())
        <div class="row" id="faq-container">
            @foreach ($getfaq as $faq)
            <div class="col-md-12 form-group mt-3">
                <strong>Question</strong>
                <textarea name="question[]" class="form-control rounded-3">{{ $faq->Question }}</textarea>
                <input type="hidden" name="faqId[]" value="{{ $faq->hotelQuestionId }}">
            </div>
            <div class="col-md-12 form-group mt-3">
                <strong>Answer</strong>
                <textarea name="answer[]" class="form-control rounded-3">{{ $faq->Answer }}</textarea>
            </div>
            <hr>
            @endforeach
        </div>
        @else
        <div id="faq-container"></div>
        <p>No FAQs found. Add a new FAQ.</p>
        @endif

        <!-- Add FAQ Button to open a modal -->
        <button type="button" class="btn btn-dark mt-3" data-bs-toggle="modal" data-bs-target="#addFaqModal">
            Add FAQ
        </button>

        <!-- Add FAQ Modal -->
        <div class="modal fade" id="addFaqModal" tabindex="-1" aria-labelledby="addFaqModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addFaqModalLabel">Add New FAQ</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group mt-3">
                            <strong>Question</strong>
                            <textarea id="newFaqQuestion" class="form-control rounded-3"></textarea>
                        </div>
                        <div class="form-group mt-3">
                            <strong>Answer</strong>
                            <textarea id="newFaqAnswer" class="form-control rounded-3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" id="addFaqButton" class="btn btn-primary">Add FAQ</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>



                          </div>
                          <h3><u>Location Info</u></h3>
                          <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 1</strong>
                                <?php  $address = explode(',',$gethotel[0]->address);
                              //  print_r($address);
                                ?>
                                <textarea type="text" name='addressline1' class="form-control rounded-3"
                                  placeholder="">@if(isset($address[0])) {{$address[0]}} @else $gethotel[0]->address @endif</textarea>
                              </div>
                            </div>
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Address Line 2</strong>
                                <input type="text" name='addressline2' value="@if(isset($address[1])) {{$address[1]}} @endif" class="form-control rounded-3"
                                  placeholder="addressline2">
                              </div>
                            </div>
                          </div>
                          <div class="row">                        
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                            <strong>City</strong>
							                <div class="form-search form-search-icon-right">
                              <input type="text" id="searchHotelcity" name="ctname" class="form-control rounded-3"
                                value="{{$gethotel[0]->Lname}}" placeholder=""><i class="ti ti-search"></i> </div>
                          
                              <span id="citylisth"></span>
                               
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">                             
                              <strong>Country</strong>
                               <input type="text" value="{{$gethotel[0]->countryName}}" id="country" class="form-control" name="country">
                            </div>
                          </div>

                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Pincode</strong>
                              <input type="number" name='pincode' class="form-control rounded-3"
                                value="{{$gethotel[0]->Pincode}}" placeholder="Pincode">
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Latitude</strong>
                              <input type="text" name='Latitude' value="{{$gethotel[0]->Latitude}}"
                                class="form-control rounded-3" placeholder="Enter Latitude">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Longitude</strong>
                              <input type="text" name='Longitude' value="{{$gethotel[0]->longnitude}}"
                                class="form-control rounded-3" placeholder="Enter Longitude">
                            </div>
                          </div>                        
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>stars</strong>
                              <input type="text" name='stars' value="{{$gethotel[0]->stars}}"
                                class="form-control rounded-3" placeholder="Enter stars">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Price</strong>
                              <input type="text" name='pricefrom' value="{{$gethotel[0]->pricefrom}}"
                                class="form-control rounded-3" placeholder="Enter pricefrom">
                            </div>
                          </div>                        
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Property Type</strong>
                              <!-- <input type="text" name='propertyType' value="{{$gethotel[0]->propertyType}}"
                                class="form-control rounded-3" placeholder="Enter Property Type"> -->
                                  <?php $propertyType = $gethotel[0]->propertyType ?>

                                <select class="form-select" aria-label="Default select example" name='propertyType'>
                                  <option value="" selected>Select Property Type</option>
                                    @foreach($TPHotel_types as $tp)                                   
                                    <option value="{{$tp->hid}}" @if($tp->hid == $propertyType) selected @endif >{{$tp->type}}</option>
                                    
                                    @endforeach                                   
                                  </select>
                            </div>
                          </div>
                          <div class="col-md-6">
  <div class="form-group mt-3">
    <strong>Amenities</strong>
    <input
      type="text"
      id="amenitySearch"
      class="form-control rounded-3"
      placeholder="Search for amenities..."
      oninput="filterAmenities()"
    />
    <div id="amenityDropdown" class="dropdown-menu"></div>
    <textarea
      id="amenitiesInput"
      name="amenities"
      class="form-control mt-2"
      value="{{$gethotel[0]->facilities}}"                          
      placeholder="Selected amenities will appear here..."
      readonly
    ></textarea>
  </div>
</div>

    <!-- Hidden Data for Amenities -->
<div id="amenitiesData" data-amenities='[
{"id":6,"name":"Business center","groupName":"Services"},
  {"id":9,"name":"Restaurant","groupName":"General"},
  {"id":13,"name":"Laundry service","groupName":"Services"},
  {"id":14,"name":"Bar","groupName":"General"},
  {"id":15,"name":"Sauna","groupName":"Activities"},
  {"id":36,"name":"Garden","groupName":"General"},
  {"id":37,"name":"Outdoor pool","groupName":"Activities"},
  {"id":38,"name":"Swimming Pool","groupName":"Activities"},
  {"id":40,"name":"Gym / Fitness Centre","groupName":"Activities"},
  {"id":41,"name":"Cafe","groupName":"General"},
  {"id":43,"name":"Reception","groupName":"General"},
  {"id":45,"name":"Tours","groupName":"Activities"},
  {"id":46,"name":"Conference Facilities","groupName":"General"},
  {"id":48,"name":"Massage","groupName":"Activities"},
  {"id":49,"name":"Hotel/airport transfer","groupName":"Services"},
  {"id":50,"name":"24h. Reception","groupName":"General"},
  {"id":52,"name":"Lobby","groupName":"General"},
  {"id":57,"name":"Jacuzzi","groupName":"Activities"},
  {"id":58,"name":"Bicycle rental","groupName":"Activities"},
  {"id":59,"name":"Wheelchair accessible","groupName":"General"},
  {"id":65,"name":"Spa","groupName":"Activities"},
  {"id":68,"name":"Indoor pool","groupName":"Activities"},
  {"id":70,"name":"Golf course (on site)","groupName":"Activities"},
  {"id":72,"name":"Solarium","groupName":"Activities"},
  {"id":73,"name":"Tennis courts","groupName":"Activities"},
  {"id":75,"name":"Multilingual staff","groupName":"General"},
  {"id":76,"name":"Parasols","groupName":"General"},
  {"id":77,"name":"Luggage room","groupName":"General"},
  {"id":78,"name":"Doctor on call","groupName":"Services"},
  {"id":79,"name":"Water sports (non-motorized)","groupName":"Activities"},
  {"id":80,"name":"Playground","groupName":"Activities"},
  {"id":81,"name":"Library","groupName":"General"},
  {"id":82,"name":"Wellness","groupName":"Activities"},
  {"id":88,"name":"Breakfast to go","groupName":"Services"},
  {"id":89,"name":"Launderette","groupName":"General"},
  {"id":91,"name":"Washing machine","groupName":"General"},
  {"id":92,"name":"Table tennis","groupName":"Activities"},
  {"id":93,"name":"Casino","groupName":"Activities"},
  {"id":94,"name":"Beauty Salon","groupName":"Services"},
  {"id":95,"name":"Steam Room","groupName":"General"},
  {"id":96,"name":"Rent a car in the hotel","groupName":"Parking"},
  {"id":97,"name":"Barbecue Area","groupName":"General"},
  {"id":98,"name":"Games Room","groupName":"Activities"},
  {"id":100,"name":"Animation","groupName":"Activities"},
  {"id":101,"name":"Billiards","groupName":"Activities"},
  {"id":105,"name":"Nightclub","groupName":"Activities"},
  {"id":106,"name":"Welcome drink","groupName":"General"},
  {"id":107,"name":"LGBT friendly","groupName":"General"},
  {"id":108,"name":"Water sports (motorized)","groupName":"Activities"},
  {"id":109,"name":"Garage","groupName":"Parking"},
  {"id":112,"name":"Horse Riding","groupName":"Activities"},
  {"id":113,"name":"Diving","groupName":"Activities"},
  {"id":114,"name":"Mini-Supermarket","groupName":"General"},
  {"id":115,"name":"Mini Golf","groupName":"Activities"},
  {"id":116,"name":"Bowling","groupName":"Activities"},
  {"id":117,"name":"Ski room","groupName":"General"},
  {"id":118,"name":"Gift Shop","groupName":"General"},
  {"id":119,"name":"Eco Friendly","groupName":"General"},
  {"id":122,"name":"Children care/activities","groupName":"Activities"},
  {"id":124,"name":"Free local telephone calls","groupName":"Services"},
  {"id":126,"name":"Luggage Service","groupName":"Services"},
  {"id":128,"name":"Porters","groupName":"Services"},
  {"id":129,"name":"Water Sports","groupName":"Activities"},
  {"id":130,"name":"Tour Desk","groupName":"General"},
  {"id":131,"name":"Wi-Fi in public areas","groupName":"General"},
  {"id":133,"name":"Wi-Fi in room","groupName":"Room"},
  {"id":134,"name":"Daily Housekeeping","groupName":"Room"},
  {"id":148,"name":"Adults only","groupName":"General"},
   {"id":2,"name":"Hairdryer","groupName":"Room"},
  {"id":3,"name":"Safe","groupName":"Room"},
  {"id":4,"name":"TV","groupName":"Room"},
  {"id":5,"name":"Telephone","groupName":"Room"},
  {"id":7,"name":"Shower","groupName":"Room"},
  {"id":8,"name":"Non-smoking rooms","groupName":"Room"},
  {"id":10,"name":"Separate shower and tub","groupName":"Room"},
  {"id":11,"name":"Air conditioning","groupName":"Room"},
  {"id":16,"name":"Mini bar","groupName":"Room"},
  {"id":18,"name":"Radio","groupName":"Room"},
  {"id":19,"name":"Desk","groupName":"Room"},
  {"id":22,"name":"Bathroom","groupName":"Room"},
  {"id":26,"name":"Bathtub","groupName":"Room"},
  {"id":27,"name":"Coffee/tea maker","groupName":"Room"},
  {"id":31,"name":"Daily newspaper","groupName":"Room"},
  {"id":32,"name":"In-room safe","groupName":"Room"},
  {"id":33,"name":"Balcony/terrace","groupName":"Room"},
  {"id":35,"name":"Ironing board","groupName":"Room"},
  {"id":51,"name":"Voice mail","groupName":"Room"},
  {"id":53,"name":"Kitchenette","groupName":"Room"},
  {"id":60,"name":"Microwave","groupName":"Room"},
  {"id":61,"name":"Bathrobes","groupName":"Room"},
  {"id":62,"name":"Inhouse movies","groupName":"Room"},
  {"id":66,"name":"Refrigerator","groupName":"Room"},
  {"id":67,"name":"Crib available","groupName":"Room"},
  {"id":71,"name":"Electronic room keys","groupName":"Room"},
  {"id":99,"name":"Video/DVD Player","groupName":"Room"},
  {"id":110,"name":"Slippers","groupName":"Room"},
  {"id":125,"name":"Handicapped Room","groupName":"Room"},
  {"id":133,"name":"Wi-Fi in room","groupName":"Room"},
  {"id":134,"name":"Daily Housekeeping","groupName":"Room"}
]'></div>                
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Short Facilities</strong>                          
                                <textarea type="text" name='shortFacilities' class="form-control rounded-3"
                                  placeholder="">{{$gethotel[0]->shortFacilities}} </textarea>
                            </div>
                          </div>
                          <div class="col-md-6">
  <div class="form-group mt-3">
    <strong>Language</strong>
    <input
      type="text"
      id="languageSearch"
      class="form-control rounded-3"
      placeholder="Search for languages..."
      oninput="filterLanguages()"
    />
    <div id="languageDropdown" class="dropdown-menu"></div>
    <textarea
      id="languagesInput"
      name="Languages"
      class="form-control mt-2"
      placeholder="Selected languages will appear here..."
      readonly
    ></textarea>
  </div>
</div>

<!-- Hidden Data for Languages -->
<div id="languagesData" data-languages='[
  {"id":136,"name":"English","groupName":"Staff languages"},
  {"id":137,"name":"French","groupName":"Staff languages"},
  {"id":138,"name":"Deutsch","groupName":"Staff languages"},
  {"id":139,"name":"Spanish","groupName":"Staff languages"},
  {"id":140,"name":"Arabic","groupName":"Staff languages"},
  {"id":141,"name":"Italian","groupName":"Staff languages"},
  {"id":142,"name":"Chinese","groupName":"Staff languages"},
  {"id":143,"name":"Russian","groupName":"Staff languages"}
]'></div>                      
                        </div>
                        <div class="row">
                        <div class="col-md-6">
  <div class="form-group mt-3">
    <strong>Room Amenity</strong>
    <input
      type="text"
      id="roomAmenitySearch"
      class="form-control rounded-3"
      placeholder="Search for room amenities..."
      oninput="filterRoomAmenities()"
    />
    <div id="roomAmenityDropdown" class="dropdown-menu"></div>
    <textarea
      id="roomAmenitiesInput"
      name="room_aminities"
      class="form-control mt-2"
	  value="{{$gethotel[0]->room_aminities}}"
      placeholder="Selected room amenities will appear here..."
      readonly
    ></textarea>
  </div>
</div>

<!-- Hidden Data for Room Amenities -->
<div id="roomAmenitiesData" data-room-amenities='[
  {"id":2,"name":"Hairdryer","groupName":"Room"},
  {"id":3,"name":"Safe","groupName":"Room"},
  {"id":4,"name":"TV","groupName":"Room"},
  {"id":5,"name":"Telephone","groupName":"Room"},
  {"id":7,"name":"Shower","groupName":"Room"},
  {"id":8,"name":"Non-smoking rooms","groupName":"Room"},
  {"id":10,"name":"Separate shower and tub","groupName":"Room"},
  {"id":11,"name":"Air conditioning","groupName":"Room"},
  {"id":16,"name":"Mini bar","groupName":"Room"},
  {"id":18,"name":"Radio","groupName":"Room"},
  {"id":19,"name":"Desk","groupName":"Room"},
  {"id":22,"name":"Bathroom","groupName":"Room"},
  {"id":26,"name":"Bathtub","groupName":"Room"},
  {"id":27,"name":"Coffee/tea maker","groupName":"Room"},
  {"id":31,"name":"Daily newspaper","groupName":"Room"},
  {"id":32,"name":"In-room safe","groupName":"Room"},
  {"id":33,"name":"Balcony/terrace","groupName":"Room"},
  {"id":35,"name":"Ironing board","groupName":"Room"},
  {"id":51,"name":"Voice mail","groupName":"Room"},
  {"id":53,"name":"Kitchenette","groupName":"Room"},
  {"id":60,"name":"Microwave","groupName":"Room"},
  {"id":61,"name":"Bathrobes","groupName":"Room"},
  {"id":62,"name":"Inhouse movies","groupName":"Room"},
  {"id":66,"name":"Refrigerator","groupName":"Room"},
  {"id":67,"name":"Crib available","groupName":"Room"},
  {"id":71,"name":"Electronic room keys","groupName":"Room"},
  {"id":99,"name":"Video/DVD Player","groupName":"Room"},
  {"id":110,"name":"Slippers","groupName":"Room"},
  {"id":125,"name":"Handicapped Room","groupName":"Room"},
  {"id":133,"name":"Wi-Fi in room","groupName":"Room"},
  {"id":134,"name":"Daily Housekeeping","groupName":"Room"}
]'></div>

                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Location Score</strong>
                              <input type="text" name='location_score' value="{{$gethotel[0]->location_score}}"
                                class="form-control rounded-3" placeholder="Enter Location Score">
                            
                            </div>
                          </div>                        
                        </div>

                        @if($gethotel[0]->Latitude != "" && $gethotel[0]->longnitude !="")
                        <div class="col-md-12 mt-3 mb-3">
                        <div id = "map1" style = "width: 1634px; height: 300px"></div>
                        </div>
                        @endif
                        <h3 class="mt-5"><u>Contact Info</u></h3>

                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Website</strong>
                              <input type="text" name='website' value="{{$gethotel[0]->Website}}"
                                class="form-control rounded-3" placeholder="Enter website url">
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Phone</strong> 
                             <input type="text" name="phone" 
       value="{{ old('phone', $gethotel[0]->Phone ?? '') }}" value="{{$gethotel[0]->Phone}}"
       class="form-control rounded-3" 
       placeholder="Enter Phone Number"
       pattern="^\+?[0-9\s\(\)-]{5,20}$"
       title="Please enter a valid phone number">

                            </div>
                          </div>

                        </div>
                        <div class="col-md-6">
                          <div class="form-group mt-3">
                            <strong>Email</strong>
                            <input type="email" name='email' value="{{$gethotel[0]->Email}}"
                              class="form-control rounded-3" placeholder="Enter Email Address">
                          </div>
                        </div>
                        @php
                        $nearestStations = json_decode($gethotel[0]->NearestStations);
                        @endphp
                     
                        <h3 class="mt-3"><u>Nearest Stations</u></h3>
                        @if(!empty($nearestStations ))
                        @foreach($nearestStations as $station)

                        <div class="row">
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Station Name</strong>
                              <input type="text" name='station_name[]' class="form-control rounded-3"
                                value="{{$station->station_name}}" placeholder="Station Name">
                            
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-group mt-3">
                              <strong>Time</strong>  
                              <input type="text" name='time[]' class="form-control rounded-3" value="{{$station->time}}"
                                placeholder="15 minute walk">
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="form-group mt-3">
                            <strong>Duration</strong>
                            <input type="number" name='duration[]' class="form-control rounded-3"
                              value="{{$station->duration}}" placeholder="Duration">
                          </div>
                        </div>
                        @endforeach
                      @else

                        <div class="row">
                            <div class="col-md-6">
                              <div class="form-group mt-3">
                                <strong>Station Name</strong>
                                <input type="text" name='station_name[]' class="form-control rounded-3"
                                  value="" placeholder="Station Name">                         
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
                              <input type="number" name='duration[]' class="form-control rounded-3"
                              placeholder="Duration">
                            </div>
                          </div>

                        @endif
                        <div id="station"></div>
                        <h4 id="addstButton" class="float-right"><u>Add Nearest Station</u></h4>
                      </div>



                      



                                <div class="form-group mt-3">
                      <button type="submit" class="btn btn-primary">Submit</button>
                      <a href="{{route('hotels')}}" class="btn btn-danger">cancel</a>
                    </div>

                    </form>
                  </div>


                </div>
              </div>
            </div>
            <!-- Start Select dateTime  -->
          </div>
       
        </div>

       
      </div>
    </div>
  </div>
  </div>
  </div> 
  </div>
	    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <script src="{{ asset('/js/map_leaflet.js')}}"></script>
  <script src="https://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
  <script>
  // Creating map options
  
  var mapOptions = {
    center: [{{$gethotel[0]->Latitude}} ,{{$gethotel[0]->longnitude}}],
    zoom: 8
  }

  // Creating a map object
  var map = new L.map('map1', mapOptions);

  // Creating a Layer object
  var layer = new L.TileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png');

  // Adding layer to the map
  map.addLayer(layer);

  let marker = new L.Marker([{{$gethotel[0]->Latitude}} ,{{$gethotel[0]->longnitude}}]);
 
  marker.addTo(map);
  marker.addTo(new L.Marker([{{$gethotel[0]->Latitude}} ,{{$gethotel[0]->longnitude}}]));
  </script>

  <script>
    $(document).ready(function() {
        $('#searchHotelcity').on('input', function() {
            let query = $(this).val();
            $.ajax({
                url: "{{ route('searchHotel') }}", // Define a route for searching hotels
                method: 'GET',
                data: { query: query },
                success: function(data) {
                    $('#citylisth').html(data); // Update the city list with results
                }
            });
        });
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const addFaqButton = document.getElementById('addFaqButton');
    const faqContainer = document.getElementById('faq-container');
    const newFaqQuestion = document.getElementById('newFaqQuestion');
    const newFaqAnswer = document.getElementById('newFaqAnswer');

    addFaqButton.addEventListener('click', function () {
        const question = newFaqQuestion.value.trim();
        const answer = newFaqAnswer.value.trim();

        if (!question || !answer) {
            alert('Please provide both Question and Answer.');
            return;
        }

        // Create FAQ elements dynamically
        const questionDiv = document.createElement('div');
        questionDiv.classList.add('col-md-12', 'form-group', 'mt-3');
        questionDiv.innerHTML = `
            <strong>Question</strong>
            <textarea name="new_question[]" class="form-control rounded-3">${question}</textarea>
        `;

        const answerDiv = document.createElement('div');
        answerDiv.classList.add('col-md-12', 'form-group', 'mt-3');
        answerDiv.innerHTML = `
            <strong>Answer</strong>
            <textarea name="new_answer[]" class="form-control rounded-3">${answer}</textarea>
        `;

        // Append new elements to the container
        faqContainer.appendChild(questionDiv);
        faqContainer.appendChild(answerDiv);

        // Clear modal inputs
        newFaqQuestion.value = '';
        newFaqAnswer.value = '';

        // Close the modal
        const addFaqModal = document.getElementById('addFaqModal');
        const modalInstance = bootstrap.Modal.getInstance(addFaqModal);
        modalInstance.hide();
    });
});


</script>

<script>
    // Handle adding reviews dynamically
    document.getElementById('addReviewButton').addEventListener('click', function () {
        const reviewContent = document.getElementById('newReviewContent').value.trim();
        const reviewRating = document.getElementById('newReviewRating').value.trim();

        // Validate input
        if (!reviewContent || !reviewRating || reviewRating < 1 || reviewRating > 5) {
            alert('Please provide a valid review and a rating between 1 and 5.');
            return;
        }

        // Append the review to the reviews container
        const reviewsContainer = document.getElementById('reviews-container');
        const newReviewDiv = document.createElement('div');
        newReviewDiv.classList.add('col-md-12', 'form-group', 'mt-3');
        newReviewDiv.innerHTML = `
            <strong>Review</strong>
            <textarea name="new_review[]" class="form-control rounded-3">${reviewContent}</textarea>
            <strong>Rating (out of 5)</strong>
            <input type="number" name="new_rating[]" value="${reviewRating}" class="form-control rounded-3" min="1" max="5">
            <hr>
        `;

        reviewsContainer.appendChild(newReviewDiv);

        // Clear modal inputs
        document.getElementById('newReviewContent').value = '';
        document.getElementById('newReviewRating').value = '';

        // Close the modal
        const addReviewModal = bootstrap.Modal.getInstance(document.getElementById('addReviewModal'));
        addReviewModal.hide();
    });
</script>
<script>
// Get amenities from the hidden data attribute
const amenitiesData = JSON.parse(
  document.getElementById("amenitiesData").dataset.amenities
);

// Filter out amenities where the groupName is "Staff languages" or "Room"
const amenities = amenitiesData.filter(
  (amenity) =>
    amenity.groupName !== "Staff languages" && amenity.groupName !== "Room"
);

// DOM Elements
const searchInput = document.getElementById("amenitySearch");
const dropdown = document.getElementById("amenityDropdown");
const amenitiesInput = document.getElementById("amenitiesInput");

let selectedAmenities = [];

// Function to filter amenities based on search input
function filterAmenities() {
  const query = searchInput.value.toLowerCase();
  dropdown.innerHTML = "";

  if (query.length > 0) {
    const filtered = amenities.filter((amenity) =>
      amenity.name.toLowerCase().includes(query)
    );

    if (filtered.length > 0) {
      filtered.forEach((amenity) => {
        const option = document.createElement("button");
        option.className = "dropdown-item";
        option.textContent = amenity.name;
        option.onclick = (e) => {
          e.preventDefault();
          selectAmenity(amenity);
        };
        dropdown.appendChild(option);
      });
      dropdown.classList.add("show");
    } else {
      dropdown.classList.remove("show");
    }
  } else {
    dropdown.classList.remove("show");
  }
}

// Function to handle selecting an amenity
function selectAmenity(amenity) {
  const alreadySelected = selectedAmenities.some((a) => a.id === amenity.id);
  if (!alreadySelected) {
    selectedAmenities.push({ id: amenity.id, name: amenity.name });
    updateAmenitiesInput();
  }
  dropdown.classList.remove("show");
  searchInput.value = "";
}

function updateAmenitiesInput() {
  // Show names in the textarea for user display only
  amenitiesInput.value = selectedAmenities.map((a) => a.name).join(", ");

  // Create a hidden input to store ID string for backend
  let hiddenInput = document.getElementById("amenitiesHiddenInput");
  if (!hiddenInput) {
    hiddenInput = document.createElement("input");
    hiddenInput.type = "hidden";
    hiddenInput.name = "amenities"; // This will go to request('amenities')
    hiddenInput.id = "amenitiesHiddenInput";
    amenitiesInput.parentNode.appendChild(hiddenInput);
  }

  // Save the comma-separated ID string
  hiddenInput.value = selectedAmenities.map((a) => a.id).join(",");
}
// Function to remove dropdown when clicking outside
document.addEventListener("click", (event) => {
  if (!dropdown.contains(event.target) && event.target !== searchInput) {
    dropdown.classList.remove("show");
  }
});

</script>
<style>
  #amenityDropdown {
  position: absolute;
  z-index: 1000;
  background-color: white;
  border: 1px solid #ccc;
  width: 100%;
  max-height: 200px;
  overflow-y: auto;
  display: none;
}

#amenityDropdown.show {
  display: block;
}

.badge {
  padding: 0.5em 0.75em;
  display: inline-flex;
  align-items: center;
}

.badge .btn-close {
  margin-left: 0.5em;
  font-size: 0.75rem;
}

</style>
<script>
// Get room amenities from the hidden data attribute
const roomAmenitiesData = JSON.parse(
  document.getElementById("roomAmenitiesData").dataset.roomAmenities
);

// Filter to include only Room amenities
const roomAmenities = roomAmenitiesData.filter(
  (amenity) => amenity.groupName === "Room"
);

// DOM Elements
const roomAmenitySearch = document.getElementById("roomAmenitySearch");
const roomAmenityDropdown = document.getElementById("roomAmenityDropdown");
const roomAmenitiesInput = document.getElementById("roomAmenitiesInput");

let selectedRoomAmenities = [];

// Function to filter room amenities based on search input
function filterRoomAmenities() {
  const query = roomAmenitySearch.value.toLowerCase();
  roomAmenityDropdown.innerHTML = "";

  if (query.length > 0) {
    const filtered = roomAmenities.filter((amenity) =>
      amenity.name.toLowerCase().includes(query)
    );

    if (filtered.length > 0) {
      filtered.forEach((amenity) => {
        const option = document.createElement("button");
        option.className = "dropdown-item";
        option.textContent = amenity.name;
        option.onclick = (e) => {
          e.preventDefault();
          selectRoomAmenity(amenity);
        };
        roomAmenityDropdown.appendChild(option);
      });
      roomAmenityDropdown.classList.add("show");
    } else {
      roomAmenityDropdown.classList.remove("show");
    }
  } else {
    roomAmenityDropdown.classList.remove("show");
  }
}

// Function to handle selecting a room amenity
function selectRoomAmenity(amenity) {
  if (!selectedRoomAmenities.includes(amenity.name)) {
    selectedRoomAmenities.push(amenity.name);
    updateRoomAmenitiesInput();
  }
  roomAmenityDropdown.classList.remove("show");
  roomAmenitySearch.value = ""; // Clear search input for new entry
}

// Function to update the room amenities input field
function updateRoomAmenitiesInput() {
  roomAmenitiesInput.value = selectedRoomAmenities.join(", ");
}

// Function to remove dropdown when clicking outside
document.addEventListener("click", (event) => {
  if (
    !roomAmenityDropdown.contains(event.target) &&
    event.target !== roomAmenitySearch
  ) {
    roomAmenityDropdown.classList.remove("show");
  }
});
</script>
<script>
// Get languages from the hidden data attribute
const languagesData = JSON.parse(
  document.getElementById("languagesData").dataset.languages
);

// Filter to include only Staff languages
const languages = languagesData.filter(
  (language) => language.groupName === "Staff languages"
);

// DOM Elements
const languageSearch = document.getElementById("languageSearch");
const languageDropdown = document.getElementById("languageDropdown");
const languagesInput = document.getElementById("languagesInput");

let selectedLanguages = [];

// Function to filter languages based on search input
function filterLanguages() {
  const query = languageSearch.value.toLowerCase();
  languageDropdown.innerHTML = "";

  if (query.length > 0) {
    const filtered = languages.filter((language) =>
      language.name.toLowerCase().includes(query)
    );

    if (filtered.length > 0) {
      filtered.forEach((language) => {
        const option = document.createElement("button");
        option.className = "dropdown-item";
        option.textContent = language.name;
        option.onclick = (e) => {
          e.preventDefault();
          selectLanguage(language);
        };
        languageDropdown.appendChild(option);
      });
      languageDropdown.classList.add("show");
    } else {
      languageDropdown.classList.remove("show");
    }
  } else {
    languageDropdown.classList.remove("show");
  }
}

// Function to handle selecting a language
function selectLanguage(language) {
  if (!selectedLanguages.includes(language.name)) {
    selectedLanguages.push(language.name);
    updateLanguagesInput();
  }
  languageDropdown.classList.remove("show");
  languageSearch.value = ""; // Clear search input for new entry
}

// Function to update the languages input field
function updateLanguagesInput() {
  languagesInput.value = selectedLanguages.join(", ");
}

// Function to remove dropdown when clicking outside
document.addEventListener("click", (event) => {
  if (!languageDropdown.contains(event.target) && event.target !== languageSearch) {
    languageDropdown.classList.remove("show");
  }
});
</script>
</x-app-layout>