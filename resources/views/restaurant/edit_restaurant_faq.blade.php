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
                <li class="breadcrumb-item" aria-current="page">Edit Faq</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">EDIT FAQ</h2>
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
                    <span type="button" class="float-right" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                      style="float:right">
                     <h4><u>ADD FAQ</u></h4> 
                    </span>
                    <span class="getupdatedfaq">
                    @if(!$getfaq->isEmpty())

                    <!-- <form class="" action="{{route('update_faq')}}" method="POST"> -->
                    @csrf

                    <div class="row">
                      <div class="col-xs-6 col-sm-6 col-md-6">
                        <div class="form-group mt-3">
                          <span id="Success"></span>
                          <strong>Restaurant</strong>
                          <div class="form-search form-search-icon-right">
                            <input type="text" id="search_attractionfaq" name="sightname" value="{{ $getfaq[0]->Title }}"
                              class="search_attractionfaqs form-control rounded-3" placeholder="" disabled>
                          </div>
                          <input type="hidden" name='attrid' id="selected_att_id" value="{{ $getfaq[0]->RestaurantId }}"
                            class="form-control rounded-3" required>

                          @foreach($getfaq as $getfaq)

                          <div class="form-group getdata-{{ $getfaq->RestaurantQuestionId }} mt-3">
                            <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                              onclick="edit_Hotelfaq({{ $getfaq->RestaurantQuestionId }})">Edit</span>
                            <strong>Question</strong>
                            <div id="questionmsg-{{ $getfaq->RestaurantQuestionId }}"></div>
                            <textarea type="text" name="question" data-original-value="{{ $getfaq->Question }}"
                              id="question{{ $getfaq->RestaurantQuestionId }}" class="form-control rounded-3"
                              disabled>{{ $getfaq->Question }}</textarea>
                          </div>

                          <div class="form-group">
                            <strong>Answer</strong>
                            <div id="answer-{{ $getfaq->RestaurantQuestionId }}"></div>
                            <textarea type="text" name="answer" data-original-value="{{ $getfaq->Answer }}"
                              id="Answer{{ $getfaq->RestaurantQuestionId }}" class="form-control rounded-3" placeholder=""
                              disabled>{{ $getfaq->Answer }}</textarea>
                          </div>

                          <span id="buttonsContainer-{{ $getfaq->RestaurantQuestionId }}"
                            class="buttons-container-dd d-none mb-3 " data-colid="{{ $getfaq->RestaurantQuestionId }}">
                            <input type="hidden" value="{{$getfaq->RestaurantId}}" id="restid">
                            <button type="button" value="1"
                              class="reviewField- btn btn-dark save-button px-4 updaterestfaq"
                              data-id="{{ $getfaq->RestaurantQuestionId }}" >Save</button>
                            <button type="button" value="2" id="cancel-1"
                              onclick="cancel_hotelfaq({{ $getfaq->RestaurantQuestionId }})"
                              class=" btn btn-dark cancel-button px-4">Cancel</button>
                          </span>

                          @endforeach

                        </div>
                      </div>
                    </div>
                    </form>
                    @else
                    <p>FAQ Not Found.</p>
                    @endif
                  </span>


                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                      tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content" style="width: 647px;padding: 20px;">
                          <div class="modal-header">
                            <!-- <h3 class="modal-title" id="staticBackdropLabel"> -->
                            <span class="list-faq" style="font-weight: bold; text-decoration: underline;">
                            Add from list</span> 
                            <span class="custom-faq margin-l">Add
                              Custom</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <h4 class="text-center">Add New FAQ</h4>

                            <div class="container">
                              <p class="mt-3">Add From List</p>
                              <p id="errorcheck"></p>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" value="" id="checkbox1">
                                <span>What are some of the best places to go out to eat near the Restaurant?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox2">
                                <span>Are there any waterfalls within a short drive of the Restaurant?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox3">
                                <span>What are some of the nearest parks to the Restaurant?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox4">
                                <span>What are some of the best places to go hiking or biking near the Restaurant?</span>
                              </label>
                              <br>
                              <label>
                                <input class="form-check-input checkbox" type="checkbox" id="checkbox4">
                                <span>What are some of the best places to go shopping near the Restaurant?</span>
                              </label>
                              <br>
                              <br>
                              <span class="mt-3  list-buttons">
                                <button id="saveButton-faq" data-id="{{request()->route('id') }}" class="btn btn-dark">Save</button>
                                <button id="cancel-list" type="button" class="btn btn-dark margin-l" data-bs-dismiss="modal">Cancel</button>
                              </span>
                            
                            <hr>
                            <p id="errorcustfaq"></p>
                              <label for="">Question</label>
                              <textarea type="text" name="question" 
                               id="addques" class="form-control rounded-3"
                              ></textarea>
                              <!-- <label for="">Answer</label>
                              <textarea type="text" name="Answer" 
                              data-id="" id="addans" class="form-control rounded-3"
                              ></textarea> -->
                              <br>
                              <br>
                              <span class="mt-3  custom-faq-buttons">
                                <button id="savecustom-faq" data-id="{{request()->route('id')}}" class="btn btn-dark" >Save</button>
                                <button id="cancel-list" type="button" class="btn btn-dark margin-l" data-bs-dismiss="modal" >Cancel</button>
                              </span>

                            </div>

                            <div class="modal-footer mt-3">
                              <!-- <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button> -->

                            </div>
                          </div>
                        </div>
                      </div>


                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

</x-app-layout>