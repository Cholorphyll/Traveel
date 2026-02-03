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
                <li class="breadcrumb-item" aria-current="page">Edit Images</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Edit Image</h2>
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
                    <div class="row d-flex justify-content-end">
                      <div class="col-md-6 form-group">

                      </div>

                      <div class="col-md-3 form-group">
                        <div class="form-search form-search-icon-right">
                          <input type="text" name="search_value" id="filter_exp_imgbyid" class="form-control rounded-3"
                            aria-describedby="emailHelp" placeholder="search by Image id" required>
                          <i class="ti ti-search"></i>
                        </div>
                      </div>

                      <div class="col-md-3 form-group text-right">
                        <span type="button" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                          <h3 class="margin-l"><u>Add More Images</u></h3>
                        </span>
                      </div>

                    </div>

                    <input type="hidden" name='restname' id="expid"
                      value=" @if(!$getexp->isEmpty()){{ $getexp[0]->ExperienceId }} @endif"
                      class="form-control rounded-3">
                    <span class="getupdatedimges">
                      @if(!$getimage->isEmpty())

                      <div class="row">
                        <div class="col-xs-9 col-sm-9 col-md-9">
                          <div class="form-group mt-3">

                            <div class="col-md-6 form-group ">
                              <span id="Success"></span>
                            </div>

                            <div class="historical-place mb-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexpimg(1)">Edit</span>
                              <h5>Primary Images</h5>


                              @php
                              $primaryImageFound = false;
                              @endphp

                              @foreach($getimage as $value)
                              @if($value->IsPrimary == 1)
                              <div class="image-container"
                                style="display: inline-block; margin: 10px; text-align: center;">
                                <img src="{{ asset('/experience-images/' . $value->Image) }}" alt="Image"
                                  style="width: 160px; height: 130px; margin-top: 20px; border-radius: 10%;">
                                <br>
                                <span class="crop edit-1 d-none">
                                  <i class="material-icons-two-tone" data-id="{{ $value->ExperienceImageId}}"
                                    onclick="deleteexpImage({{ $value->ExperienceImageId}})"
                                    style="color: red;">delete</i>
                                </span>
                              </div>


                              @php
                              $primaryImageFound = true;
                              @endphp

                              @endif
                              @endforeach

                              @if (!$primaryImageFound)
                              <p class="margin-l">No primary images found.</p>
                              @endif
                              <hr>
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="editexpimg(2)">Edit</span>
                              <h5 class="mt-5">Guest Images</h5>

                              @php
                              $GuestImage = false;
                              @endphp


                              @foreach($getimage as $value)
                              @if($value->IsPrimary != 1)
                              <div class="image-container"
                                style="display: inline-block; margin: 10px; text-align: center;">
                                <img src="{{ asset('/experience-images/' . $value->Image) }}" alt="Image"
                                  style="width: 160px; height: 130px; margin-top: 20px; border-radius: 10%;">
                                <br>
                                <span class="crop edit-2 d-none">
                                  <i class="material-icons-two-tone" data-id="{{ $value->ExperienceImageId}}"
                                    onclick="deleteexpImage({{ $value->ExperienceImageId}})"
                                    style="color: red;">delete</i>
                                </span>
                              </div>

                              @php
                              $GuestImage = true;
                              @endphp
                              @endif
                              @endforeach

                              @if (!$GuestImage)
                              <p class="margin-l">No guest images found.</p>
                              @endif

                            </div>

                          </div>
                        </div>
                      </div>
                      </form>
                      @else
                      <p>Image Not Found.</p>
                      @endif

                    </span>

                    <!-- Modal -->
                    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
                      tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content" style="width: 647px;padding: 20px;">
                          <div class="modal-header">
                            <!-- <h3 class="modal-title" id="staticBackdropLabel"> -->

                            <span class=" margin-l" style="font-weight: bold; text-decoration: underline;">Add More
                              Image</span>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                          </div>
                          <div class="modal-body">
                            <!-- <h4 class="text-center">Add New FAQ</h4> -->

                            <div class="container">
                              <!-- <p class="mt-3">Add More Image</p> -->
                              <span id="inputerror" class="mb-3"></span>

                              <div class="col-md-12 form-group">
                                <strong class="form-label"> Upload New Image </strong>

                                <br>
                                <br>
                                @if ($errors->any())
                                <div class="col-md-8 alert alert-danger mt 3">
                                  <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                  </ul>
                                </div>
                                @endif
                                <form action="{{ route('upload_exp_Image',[$getexp[0]->ExperienceId]) }}" method="post"
                                  enctype="multipart/form-data">
                                  @csrf
                            
                                  <input type="file" class="form-control mb-3 img" name="image" required>

                                  <strong>Select Image type</strong>
                                  <select name="image_type" id="" class="form-control mt-3">
                                    <option value="0">Guest Image</option>
                                    <option value="1">Primary Image</option>
                                  </select>
                                  <br>
                                  <span class="mt-3  custom-faq-buttons">
                                    <button id="" type="submit" data-id="{{ $getexp[0]->ExperienceId }}"
                                      class="btn btn-dark uploadimg">Save</button>
                                </form>
                                <button id="cancel-list" type="button" class="btn btn-dark margin-l"
                                  data-bs-dismiss="modal">Cancel</button>
                                </span>

                              </div>

                              <div class="modal-footer mt-3">

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