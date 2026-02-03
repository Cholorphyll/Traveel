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
                <li class="breadcrumb-item" aria-current="page">Add Day Tours Itinerary</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h4 class="mb-0">Add Day Tours Itinerary</h4>
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
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>


</x-app-layout>