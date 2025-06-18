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
                <li class="breadcrumb-item" aria-current="page">Edit Day Tours Itinerary</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h4 class="mb-0">Edit Day Tours Itinerary</h4>
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
                    @if($getdata->isEmpty())<h4 style="float:right"><u style="float:right"><a
                          href="{{route('add_Itinerary',[request()->route('id')])}}" target="_blank"
                          class=" ml-3 "><strong>Add Itinerary</strong></a> </u></h4>

                     @endif     
                    <br>
                    @if(!$getdata->isEmpty())
                    <!-- <form class="" action="{{route('update_itinerary',[request()->route('id')])}}" method="POST"> -->
                    @csrf

                    <h4 class="mb-3">Itinerary Details</h4>

                    <div class="row">
                      <div class="col-md-8">
                        <div class="row mb-3 ">
                          <div class="col-md-6">
                            <div class="form-check">
                              <input class="form-check-input itenerytype-edit" type="radio" name="flexRadioDefault"
                                id="singleDayRadio" @if(!$getdata->isEmpty()) @if($getdata[0]->ItnineraryDay == 0)
                              checked @else disabled @endif @endif>
                              <label class="form-check-label" for="singleDayRadio">
                                Single Day
                              </label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-check">
                              <input class="form-check-input itenerytype-edit" type="radio" name="flexRadioDefault"
                                id="multiDayRadio" @if(!$getdata->isEmpty()) @if($getdata[0]->ItnineraryDay != 0)
                              checked  @else disabled @endif @endif>
                              <label class="form-check-label" for="multiDayRadio">
                                Multi Day
                              </label>
                            </div>
                          </div>
                        </div>

                        <input type="hidden" id="swich_day" value="0">
                        <div id="itineraryContainer">
                          @php
                          $currentDay = null;
                          $currentSequence = 0; // Initialize the sequence
                          $x = 0;
                          @endphp

                          <div class="row input-value">


                            @foreach($getdata as $itinerary)
                            @if ($itinerary->ItnineraryDay !== $currentDay)
                            @php
                            $currentDay = $itinerary->ItnineraryDay;


                            $currentSequence = 0;
                            @endphp
                          </div>

                          @if($itinerary->ItnineraryDay !=0)
                          <h5 class="inp mt-3 day" id="day{{ $itinerary->ItnineraryDay }}">Day
                            {{ $itinerary->ItnineraryDay }}</h5>
                          <input type="hidden" value="{{ $itinerary->ItnineraryDay }}"
                            id="{{ $itinerary->ItnineraryDay }}-day" class="dayvalue">
                          @else
                          <h5 class="inp mt-3 day d-none" id="day1">Day 1</h5>
                          <input type="hidden" value="0" id="0-day" class="dayvalue">
                          @endif


                          <div class="row input-value">
                            <!-- Open a new row -->
                            @endif
                            <?php   $x++;?>

                            <div class="col-md-6 inp">
                              <div class="  all-inp">

                                <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                  onclick="edititn({{$itinerary->ExperienceItnineraryId}})">Edit</span>

                                <strong class="Itinerary_Point-{{$x}} mt-3">Itinerary Point
                                  {{ ++$currentSequence }}</strong>
                                <input type="text" id="search_input" name="Itinerary[][{{ $itinerary->ItnineraryDay }}]"
                                  class="form-control rounded-3 searchAttraction name{{$itinerary->ExperienceItnineraryId}}"
                                  data-original-value="{{ $itinerary->Name }}" placeholder="Search an attraction" value="{{ $itinerary->Name }}" required disabled>


                                <span class="att_list"></span>
                                <input type="hidden" value="{{ request()->route('id') }}" name="exp_id" id="exp_id">

                              </div>

                              <div
                                class="form-group mt-3  d-none buttonsContainer-{{$itinerary->ExperienceItnineraryId}}">
                                <button class="btn btn-outline-dark updateItinery"
                                  data-id="{{$itinerary->ExperienceItnineraryId}}">Save</button>

                                <button type="button" value="2" id="cancel-1"
                                  onclick="cancelitn({{$itinerary->ExperienceItnineraryId}})"
                                  class=" btn btn-dark cancel-button px-4">Cancel</button>
                              </div>
                            </div>

                            @endforeach

                          </div>
                        </div>


                        <div class="form-group mt-3 d-none save-iten">
                        <button class="btn btn-outline-dark addnewItinery">Save New Itinerary</button>
                        <a href="" class="btn btn-outline-dark">cancel</a>
                      </div>

                        <p id="daycontainer"></p>
                        <h4 id="addnewitinerarye" class="margin-l mr-3"><u style="float:right">Add Itinerary Point</u>
                        </h4>
                        @if(!$getdata->isEmpty()) @if($getdata[0]->ItnineraryDay != 0)
                        <h4 id="adddayedit" class="mr-3 ml-3"><u style="float:right;margin-right: 20px;">Add
                            Day</u></h4>
                        @else
                        <h4 id="adddayedit" class="mr-3 ml-3 d-none"><u style="float:right;margin-right: 20px;">Add
                            Day</u></h4>

                        @endif @endif

                      </div>


                    </div>
                  </div>


                  @else
                  <form class="" action="{{route('store_itinerary')}}" method="POST">
                    @csrf
                    <h4 class="mb-3">Itinerary Details</h4>
                    <div class="row">
                      <div class="col-md-8">

                        <div class="row mb-3">
                          <div class="col-md-6">
                            <div class="form-check">
                              <input class="form-check-input itenerytype" type="radio" name="flexRadioDefault"
                                id="singleDayRadio" checked>
                              <label class="form-check-label" for="singleDayRadio">
                                Single Day
                              </label>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="form-check">
                              <input class="form-check-input itenerytype" type="radio" name="flexRadioDefault"
                                id="multiDayRadio">
                              <label class="form-check-label" for="multiDayRadio">
                                Multi Day
                              </label>
                            </div>
                          </div>
                        </div>



                        <input type="hidden" id="swich_day" value="0">
                        <div id="itineraryContainer">
                          <h5 class="inp mt-3 day d-none" id="day1">Day 1</h5>
                          <input type="hidden" value="0" id="0-day" class="dayvalue">
                          <div class="row input-value">
                            <div class="col-md-6 inp all-inp">
                              <strong>Itinerary Point 1</strong>
                              <div class="form-search form-search-icon-right">
                                <input type="text" id="search_input" name="Itinerary[][0]"
                                  class="form-control rounded-3 searchAttraction" placeholder="Search an attraction"
                                  required><i class="ti ti-search"></i>
                              </div>
                              <span class="att_list"></span>
                              <input type="hidden" value="{{request()->route('id')}}" name="exp_id">
                            </div>
                          </div>

                        </div>
                        <p id="daycontainer"></p>
                        <h4 id="addnewitinerary" class="margin-l mr-3"><u style="float:right">Add Itinerary Point</u>
                        </h4>
                        <h4 id="addday" class="mr-3 ml-3 d-none"><u style="float:right;margin-right: 20px;">Add
                            Day</u></h4>



                      </div>

                      <div class="form-group mt-3">
                        <button type="submit" class="btn btn-outline-dark">Save</button>
                        <a href="" class="btn btn-outline-dark">cancel</a>
                      </div>


                    </div>
                </div>

                </form>
                @endif

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


</x-app-layout>