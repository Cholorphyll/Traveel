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
                <li class="breadcrumb-item" aria-current="page">Edit Category</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">EDIT CATEGORY</h2>
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

                    <br>
                    <!-- Button trigger modal -->
                    <span type="button" class="float-right" data-bs-toggle="modal" data-bs-target="#staticBackdrop"
                      style="float:right">
                      <h4><u>ADD CATEGORY</u></h4>
                    </span>
                    <h4>Attraction Categories</h4>
                    <span class="getupdatedfaq">
                      @if(!$get_cat->isEmpty())

                      <div class="row">
                         <div class="row">
                            <div class=" col-md-6">
                              <span id="Success"></span>
                            </div>
                            <div class=" col-md-6">
                           
                            </div>
                          </div>

                        <div class="col-xs-8 col-sm-8 col-md-8">

                          
                          <div class="form-group mt-3">
                            <input type="hidden" id="sightid" value="{{ $get_cat[0]->SightId }}"
                              class="form-control rounded-3" required>
                              <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn" value="0"
                                    onclick="editattcat(1)">Edit</span>
                            <div class="historical-place mt-3 mb-5">
                              @foreach($get_cat as $value)

                              <button class="btn btn-secondary btn-lg border-0 margin-l ml-3 "
                                style="margin-left: 40px;margin-top: 20px;">{{ $value->ctitle }} </button>
                              <span class="crop d-none editattcat-1" style="margin-top: 20px;"> <i
                                  class="material-icons-two-tone delete_att_cat"
                                  data-id="{{ $value->SightCategoryId }}"> delete</i></span>

                              @endforeach
                            </div>

                          </div>
                        </div>
                      </div>
                      </form>
                      @else
                      <p>Category Not Found.</p>
                      @endif



                      <!-- Modal -->
                      <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                        tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="get_cattrue">
                        <div class="modal-dialog">
                          <div class="modal-content" style="width: 647px;padding: 20px;">
                            <div class="modal-header">
                              <!-- <h3 class="modal-title" id="staticBackdropLabel"> -->
                              <span style="font-weight: bold; text-decoration: underline;">
                                Existing Category</span>
                              <span class=" margin-l">Add
                                New</span>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <!-- <h4 class="text-center">Add New FAQ</h4> -->

                              <div class="container">
                                <p class="mt-3">Listed Categories </p>
                                <p id="errorcheck"></p>
                                @foreach($get_cat as $value)
                                <div class="historical-place mt-3">
                                  <button class="btn btn-secondary btn-lg border-0 margin-l ml-3">{{ $value->ctitle }}
                                  </button>
                                  <span class="crop"> <i class="material-icons-two-tone delete_att_cat"
                                      data-id="{{ $value->SightCategoryId }}"> delete</i></span>
                                </div>
                                @endforeach

                                <hr>
                                <h4 class="mb-3">Add New Category</h4>
                                <p id="inputerror"></p>
                                <div class="col-md-6 form-group">
                                  <strong class="form-label">Search Category Name </strong>
                                  <div class="form-search form-search-icon-right sp-category">
                                    <input type="text" name="" id="search_cat"
                                      class="getcate_resultval form-control rounded-3" aria-describedby="emailHelp"
                                      placeholder="Name" required>
                                    <i class="ti ti-search"></i>
                                  </div>
                                  <ul id="cat-list"></ul>
                                </div>

                                <br>
                                <span class="mt-3  custom-faq-buttons">
                                  <button id="saveatt-cat" data-id="{{request()->route('id')}}"
                                    class="btn btn-dark">Save</button>
                                  <button id="cancel-list" type="button" class="btn btn-dark margin-l"
                                    data-bs-dismiss="modal">Cancel</button>
                                </span>

                              </div>

                              <div class="modal-footer mt-3">
                                <!-- <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button> -->

                              </div>
                            </div>
                          </div>
                        </div>


                      </div>


                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

</x-app-layout>