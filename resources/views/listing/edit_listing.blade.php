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
                <li class="breadcrumb-item" aria-current="page">Edit Listing</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Location</h2>
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
                    <!-- <h2><strong>Edit Listing</strong></h2> -->
                  </div>
                </div>
                @if ($message = Session::get('success'))
                <div class="col-md-12 alert alert-success">
                  {{ $message }}
                </div>
                @endif
                @if ($errors->any())
                <div class="col-md-8 alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach 
                  </ul>
                </div>
                @endif
                <br>
             <span class="getupdata">
                <form >
                

                  <div class="row">
                    <div class="col-xs-6 col-sm-6 col-md-6">
                      <span id="Success"></span>
                    <!-- <div class="form-group mt-3">
                        <strong>Parent ID</strong>
                        <input type="number" name='parent_id'id="country-input"  class="form-control rounded-3"
                          value="{{$listing[0]->ParentId}}" placeholder="Enter Parent Id" required>
                      </div> -->
                     
                  
                      <div class="form-group mt-3">
                      <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn" value="0"
              onclick="editLocation(1)">Edit</span>
                        <strong>Parent Location</strong>
                        <div class="form-search form-search-icon-right">
                        <input type="text"  id="search_city" name="ctname" class="form-control rounded-3 name1" data-orgdata="@if(!$getLoc->isEmpty()){{$getLoc[0]->Name}}@endif" value="@if(!$getLoc->isEmpty()){{$getLoc[0]->Name}}@endif" placeholder="Search City minimun 2 letters "  disabled><i class="ti ti-search"></i> </div>
                         
                          <span id="citylist"></span>
                          
                      </div>
                      <span id="buttonsContainer-1"
                        class="buttons-container-dd d-none mb-3 " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="1">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(1)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span>    
                    
                     <input type="hidden" value="{{  $listing[0]->LocationId }}" id="locid">
                      <div class="form-group">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(2)">Edit</span>
                        <strong>Location Name</strong>
                        <input type="text" name="location_name" class="form-control rounded-3 name2"  data-orgdata="{{$listing[0]->Name}}"
                          value="{{$listing[0]->Name}}" placeholder="location Name" disabled>
                          <p class="errorname"></p>
                      </div>
                      <span id="buttonsContainer-2"
                        class="buttons-container-dd d-none mb-3 " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="2">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(2)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span>    


                  
                      <div class="form-group">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(3)">Edit</span>
                        <strong>Page slug</strong>
                        <input type="text" name="page_slug" value="{{$listing[0]->Slug}}" class="form-control rounded-3 name3" data-orgdata="{{$listing[0]->Slug}}" placeholder="location Name"
                        disabled>
                        <p class="errorslug"></p>
                      </div>
                      <span id="buttonsContainer-3"
                        class="buttons-container-dd d-none mb-3 " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="3">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(3)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span>   

                     
                      <div class="form-group mt-3">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(8)">Edit</span>
                        <strong>Country</strong>
                        <select name="Countryid" id="Countryid" class="form-control rounded-3 name8" data-orgdata="{{$listing[0]->CountryId }}"  disabled>
                          <option value="">Select Country</option>
                          @if(!$country->isEmpty())
                          @foreach($country as $country)
                          <option value="{{$country->CountryId}}" @if($listing[0]->CountryId == $country->CountryId)
                            selected @endif>{{$country->Name}}</option>
                          @endforeach
                          @endif
                        </select>
                      </div>
                      <span id="buttonsContainer-8"
                        class="buttons-container-dd d-none " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="8">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(8)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span>    
                      <!-- <div class="form-group mt-3">
                        <strong>Location Lavel</strong>
                        <select name="location_lavel" id="" class="form-control rounded-3">
                          <option value="1" @if($listing[0]->Name == 1) selected @endif>Parent</option>
                          <option value="2" @if($listing[0]->Name == 2) selected @endif>Child</option>
                        </select>
                      </div>
                      -->

                     
                      <div class="form-group mt-3">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(4)">Edit</span>
                        <strong>Meta Title</strong>
                        <input type="text" name='meta_title' class="form-control rounded-3 name4" data-orgdata="{{$listing[0]->MetaTagTitle}}"
                          value="{{$listing[0]->MetaTagTitle}}" placeholder="Meta Title"  disabled>
                      </div>
                      <span id="buttonsContainer-4"
                        class="buttons-container-dd d-none mb-3 " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="4">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(4)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span> 

                      
                      <div class="form-group mt-3">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(7)">Edit</span>
                        <strong>Meta Description</strong>
                        <textarea type="text" name='meta_desc' class="form-control rounded-3 name7" value=""  data-orgdata="{{$listing[0]->MetaTagDescription}}"
                        disabled >{{$listing[0]->MetaTagDescription}}</textarea>
                      </div>
                      <span id="buttonsContainer-7"
                        class="buttons-container-dd d-none mb-3 " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="7">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(7)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span> 
                      <!-- <div class="form-group mt-3">
                        <strong>Is State</strong>
                        <select name="isstate" id="" class="form-control rounded-3">
                          <option value="1">Yes</option>
                          <option value="0">No</option>
                        </select>
                      </div> -->
                   
                      <div class="form-group mt-3">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(5)" >Edit</span>
                        <strong>About Location</strong>
                        <textarea type="text" name='about' class="form-control rounded-3 name5" data-orgdata="{{$listing[0]->About}}"
                        disabled >{{$listing[0]->About}}</textarea>
                      </div>
                      <span id="buttonsContainer-5"
                        class="buttons-container-dd d-none mb-3 " >
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="5">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(5)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span> 


                     
              
                      <div class="form-group mt-3">
                      <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
              onclick="editLocation(6)">Edit</span>
                        <strong>Pincode</strong>
                        <input type="number" name='pincode' class="form-control rounded-3 name6 pincode" data-orgdata="{{$listing[0]->UploadLocation}}"
                          placeholder="Pincode" value="{{$listing[0]->UploadLocation}}"  disabled>
                      </div>

                      <span id="buttonsContainer-6"
                        class="buttons-container-dd d-none mb-3">
                        <button type="button" value="1"
                          class="reviewField- btn btn-dark save-button px-4 updateLocation"
                          data-id="{{ $listing[0]->LocationId }}" data-colid="6">Save</button>
                        <button type="button" value="2" id="cancel-1"
                          onclick="cancelLoc(6)"
                          class=" btn btn-dark cancel-button px-4">Cancel</button>
                      </span> 
                   

                    </div>
                  </div>
                
              </div>
              </form>
            </span>
              
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

</x-app-layout>