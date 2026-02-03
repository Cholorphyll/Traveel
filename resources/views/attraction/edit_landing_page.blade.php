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
                <li class="breadcrumb-item" aria-current="page">Edit Attraction Landing Page</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Attraction Landing Page</h2>
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
                    <!-- Button trigger modal -->
                    @if($get_landing->isEmpty())
                    <span type="button" class="float-right" style="float:right">
                      <h4><u><a href="{{ route('add_landing_page',[request()->route('id')]) }}">Add Landing Page</a></u>
                      </h4>
                    </span>
                    @endif
                   


                    @if(!$get_landing->isEmpty())
                    <div class="row">
                      <div class="col-xs-6 col-sm-6 col-md-6">


                        <div class="form-group mt-3">
                          <span id="Success"></span>
                          <strong>Attraction</strong>

                          <div class="form-group getdata-{{ $get_landing[0]->ID }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editattlanding(1)">Edit</span>
                            <strong>Landing Page Name</strong>
                            <div id="questionmsg-{{ $get_landing[0]->ID }}"></div>
                            <textarea type="text" name="question" data-original-value="{{$get_landing[0]->Page_Name}}"
                              id="name" class="form-control rounded-3 name1"
                              disabled>{{$get_landing[0]->Page_Name}}</textarea>
                          </div>
                          <input type="hidden" value="{{ $get_landing[0]->ID }}" id="id">
                          
                          <span id="buttonsContainer-1" class="buttons-container-dd d-none mb-3"
                            data-colid="{{ $get_landing[0]->ID }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 UpdateAttractionLanding"
                              data-id="{{ $get_landing[0]->ID }}" data-colid="1">Save</button>

                            <button type="button" value="2" id="cancel-1" onclick="cancelAttLanding(1)"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <div class="form-group getdata-{{ $get_landing[0]->ID }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editattlanding(2)">Edit</span>
                            <strong>Page Slug</strong>
                            <div id="questionmsg-{{ $get_landing[0]->ID }}"></div>
                            <textarea type="text" name="" data-original-value="{{$get_landing[0]->Slug}}"
                              id="slug" class="form-control rounded-3 name2" disabled>{{$get_landing[0]->Slug}}</textarea>
                          </div>

                          <span id="buttonsContainer-2" class="buttons-container-dd d-none mb-3 "
                            data-colid="{{ $get_landing[0]->ID }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 UpdateAttractionLanding"
                              data-id="{{ $get_landing[0]->ID }}" data-colid="2">Save</button>

                            <button type="button" value="2" id="cancel-1" onclick="cancelAttLanding(2)"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <div class="form-group getdata-{{ $get_landing[0]->ID }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editattlanding(3)">Edit</span>
                            <strong>Meta Title</strong>
                            <div id="questionmsg-{{ $get_landing[0]->ID }}"></div>
                            <textarea type="text" name="question" data-original-value="{{$get_landing[0]->Meta_Title}}"
                              id="metatitle" class="form-control rounded-3 name3"
                              disabled>{{$get_landing[0]->Meta_Title}}</textarea>
                          </div>

                          <span id="buttonsContainer-3" class="buttons-container-dd d-none mb-3 "
                            data-colid="{{ $get_landing[0]->ID }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 UpdateAttractionLanding"
                              data-id="{{ $get_landing[0]->ID }}" data-colid="3">Save</button>

                            <button type="button" value="3" id="cancel-1" onclick="cancelAttLanding(3)"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <div class="form-group getdata-{{ $get_landing[0]->ID }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editattlanding(4)">Edit</span>
                            <strong>Meta Description</strong>
                            <div id="questionmsg-{{ $get_landing[0]->ID }}"></div>
                            <textarea type="text" name="question"
                              data-original-value="{{$get_landing[0]->Meta_Description}}" id="metadesc"
                              class="form-control rounded-3 name4" disabled>{{$get_landing[0]->Meta_Description}}</textarea>
                          </div>

                          <span id="buttonsContainer-4" class="buttons-container-dd d-none mb-3 "
                            data-colid="{{ $get_landing[0]->ID }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 UpdateAttractionLanding"
                              data-id="{{ $get_landing[0]->ID }}" data-colid="4">Save</button>

                            <button type="button" value="3" id="cancel-1" onclick="cancelAttLanding(4)"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          <div class="form-group getdata-{{ $get_landing[0]->ID }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editattlanding(5)">Edit</span>
                            <strong>About Landing Page</strong>
                            <div id="questionmsg-{{ $get_landing[0]->ID }}"></div>
                            <textarea type="text" name="question" data-original-value="{{$get_landing[0]->About}}"
                              id="about" class="form-control rounded-3 name5" disabled>{{$get_landing[0]->About}}</textarea>

                          </div>
                          <span id="buttonsContainer-5" class="buttons-container-dd d-none mb-3 "
                            data-colid="{{ $get_landing[0]->ID }}">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 UpdateAttractionLanding"
                              data-id="{{ $get_landing[0]->ID }}" data-colid="5">Save</button>

                            <button type="button" value="3" id="cancel-1" onclick="cancelAttLanding(5)"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>
                        </div>
                      </div>


                      <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="editattlanding(6)">Edit</span>
                          <h5>Select Landing Page Type</h5>
                          <input type="radio" id="Attraction" name="page_type" value="Attraction" class="mt-3" checked>
                          <label for="html">Attraction</label>
                          <input type="radio" id="Hotel" name="page_type" value="Hotel" class="margin-left" disabled>
                          <label for="css">Hotel</label>
                          <input type="radio" id="Restaurent" name="page_type" value="Restaurent" class="margin-left"
                            disabled>
                          <label for="javascript">Restaurent</label>
                          <input type="radio" id="Experience" name="page_type" value="Experience" class="margin-left"
                            disabled>
                          <label for="javascript">Experience</label>



                          <input type="hidden" value="{{request()->route('id')}}" id="sightid">
                          <h5 class="mb-3 mt-3">Near By</h5>
                          <div class="nearby">
                            <input type="radio" id="Attraction" name="near_by" value="Attraction"
                              @if($get_landing[0]->Near_Type == 'Attraction') checked @endif>
                            <label for="html">Attraction</label>
                            <input type="radio" id="Hotel" name="near_by" value="Hotel" class="margin-left"
                              @if($get_landing[0]->Near_Type == 'Hotel') checked @endif>
                            <label for="css">Hotel</label>
                            <input type="radio" id="Restaurent" name="near_by" value="Restaurent" class="margin-left"
                              @if($get_landing[0]->Near_Type == 'Restaurent') checked @endif>
                            <label for="javascript">Restaurent</label>
                            <input type="radio" id="Neighborhood" name="near_by" value="Neighborhood"
                              class="margin-left">
                            <label for="javascript">Neighborhood</label>
                            <input type="radio" id="Airport" name="near_by" value="Airport" class="margin-left"
                              @if($get_landing[0]->Near_Type == 'Airport') checked @endif>
                            <label for="javascript">Airport</label><br>
                          </div>
                          <div class="col-md-8 form-group mt-3">
                            <div class="form-search form-search-icon-right change-field mb-3">
                              <input type="text" name="Attraction" id="Attraction"
                                class="form-control inputval rounded-3" placeholder="Search Attraction">
                              <i class="ti ti-search"></i>
                            </div>
                            <span class="att-list"></span>
                          </div>

                          <span class="nb-value value mt-3">
                            @if(!empty($get_landing[0]->Nearby))
                            <button
                              class="btn btn-secondary nearby-value">{{$get_landing[0]->Nearby}}</button>
                            @endif
                          </span>


                          <h5 class="mb-3">With</h5>
                          <div class="margin-l" style='margin-left: 39px;'>


                            <div class="col-md-8 form-group">
                              <div class="row">
                                <div class="col-md-6 ">
                                  <select class="attraction-select-filters form-select ">
                                    <option selected>Select</option>
                                    <option value="Hotel Class Ratings">Ratings</option>
                                    <option value="Category">Category</option>
                                    <option value="Duration">Duration</option>
                                    <option value="traveler Types">Traveler Types</option>
                                    <option value="Distance">Distance</option>
                                    <option value="Attraction Tags">Attraction Tags</option>
                                  </select>
                                </div>
                                <div class="col-md-6 change-with-filter">

                                </div>
                              </div>

                              <p class="mt-3">
                                <span class="star-heading  mt-3"><strong>Star Rating:</strong></span>
                                <span class="star-rating margin-l mt-3">
                                  <?php $Ratings = json_decode($get_landing[0]->Ratings); ?>
                                  @if(isset($get_landing[0]->Ratings) && $get_landing[0]->Ratings !== "null")    
                                  @foreach($Ratings as $Ratings) 
                                  <span class="margin-l"> <button
                                    class="btn btn-secondary ml-3 margin-top nearby-value">{{$Ratings}}</button><i class="fa fa-trash ml-3" id=""></i></span>
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              <p class="mt-3">
                                <span class="hotel-mnt-heading mt-3"><strong>Category:</strong></span>
                                <span class="category-value margin-l mt-3">
                                  <?php $Category = json_decode($get_landing[0]->Category); ?>
                                  @if(isset($get_landing[0]->Category) && $get_landing[0]->Category !== "null")  
                                  @foreach($Category as $Category)
                                  <span class="margin-l"> <button
                                    class="btn btn-secondary margin-top nearby-value">{{$Category}}</button><i class="fa fa-trash ml-3" id=""></i></span>
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              <p class="mt-3 ">
                                <span class="mnt-heading "><strong>Duration:</strong></span>
                                <span class="duration_value margin-l mt-3">
                                  <?php $Duration = json_decode($get_landing[0]->Duration); ?>
                                  @if(isset($get_landing[0]->Duration) && $get_landing[0]->Duration !== "null")  
                                  @foreach($Duration as $Duration)
                                  <span class="margin-l"> <button
                                    class="btn btn-secondary margin-top nearby-value">{{$Duration}}</button><i class="fa fa-trash ml-3" id=""></i></span>
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              <p class="mt-3">
                                <span class="Hotel-Pricing-heading mt-3"><strong>Traveler Types:</strong></span>
                                <span class="Traveler_Types_value margin-l mt-3">
                                  <?php $Traveler_Types = json_decode($get_landing[0]->Traveler_Types); ?>
                                  @if(isset($get_landing[0]->Traveler_Types) && $get_landing[0]->Traveler_Types !== "null")  
                                  @foreach($Traveler_Types as $Traveler_Types)
                                  <span class="margin-l">   <button
                                    class="btn btn-secondary margin-top nearby-value">{{$Traveler_Types}}</button><i class="fa fa-trash ml-3" id=""></i></span>
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              <p class="mt-3">
                                <span class="distance-heading  mt-3"><strong>Distance:</strong></span>
                                <span class="distance margin-l mt-3">
                                  <?php $Distance = json_decode($get_landing[0]->Distance); ?>
                                 
                                  @if(isset($get_landing[0]->Distance) && $get_landing[0]->Distance !== "null")
                                  @foreach($Distance as $Distance)
                                  <span class="margin-l"> <button
                                    class="btn btn-secondary margin-top nearby-value">{{$Distance}}</button><i class="fa fa-trash ml-3" id=""></i></span>
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              <p class="mt-3">
                                <span class="hotel-style-heading  mt-3"><strong>Attraction Tags:</strong></span>
                                <span class="Attraction_Tags_value margin-l mt-3">
                                  <?php $Attraction_Tags = json_decode($get_landing[0]->Attraction_Tags); ?>
                                  @if(isset($get_landing[0]->Attraction_Tags) && $get_landing[0]->Attraction_Tags !== "null")
                                  @foreach($Attraction_Tags as $Attraction_Tags)
                                  <span class="margin-l"><button
                                    class="btn btn-secondary margin-top nearby-value">{{$Attraction_Tags}}</button><i class="fa fa-trash ml-3" id=""></i></span>
                               
                                  @endforeach
                                  @endif
                                </span>
                              </p>
                              </div>
                            <span id="buttonsContainer-6" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $get_landing[0]->ID }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 UpdateAttractionLanding"
                                data-id="{{ $get_landing[0]->ID }}" data-colid="6">Save</button>

                                <a href="" class="btn btn-dark cancel-button px-4">Cancel</a>
                            </span>
                            </div>
                            <br>
                            <button type="button" id="att-hidepage" data-id="{{ $get_landing[0]->ID }}" class="btn btn-outline-dark">Hide Page</button>
                            <button type="button" id="delete-att-landing-page" data-id="{{ $get_landing[0]->ID }}"  class="btn btn-outline-dark">Delete Page</button>

                          </div>
                       
                        </div>
                      </div>
                    </div>


                    @else
                    <p>Data not found.</p>
                    @endif
                  </div>
                </div>



              </div>
            </div>
          </div>

</x-app-layout>