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
                <li class="breadcrumb-item" aria-current="page">View reviews</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h3 class="mb-0"> Restaurant Reviews </h3>
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
                    <h3 class="mb-0 mt-3">@if($getrest[0]->Title != "") {{$getrest[0]->Title}} @else Manage Restaurant
                      Reviews @endif</h3>
                    <br>
                    <span class="rest-review-option opt-0" data-value="0" style="font-weight: bold;
      text-decoration: underline;">Pending |</span>
                    <span class="rest-review-option opt-1" data-value="1">Approved |</span>
                    <span class="rest-review-option opt-2" data-value="2">Disapproved |</span>
                    <span class="rest-review-option opt-3" data-value="3,4,5">Spam </span>
                    <input type="hidden" name="" id="restid" value=" @if(!$getrest->isEmpty()) {{$getrest[0]->RestaurantId}} @endif">
                    <div class="row float-right" style="float:right">
                      <div class="col-md-5 form-group">
                        <select name="" id="sort_rest_review" class="form-control">
                          <option value="asc">Sort Reviews</option>
                          <option value="desc">Newest</option>
                          <option value="asc">Oldest</option>
                        </select>
                      </div>
                      <div class="col-md-7 form-group ">
                        <div class="form-search form-search-icon-right">
                          <input type="text" name="search_value" id="filRestReviewbyid" class="form-control rounded-3"
                            aria-describedby="emailHelp" placeholder="search by review id" required><i
                            class="ti ti-search"></i>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                          <div class="form-group mt-3 list-container">
                          <p id="Success"></p>
                            @if(!$restreview->isEmpty())
                            @foreach($restreview as $value)
                            <form class="" action="{{route('update_review',[$value->RestaurantReviewId])}}" method="POST"
                              autocomplete="on">
                              @csrf
                              <div class="form-group form-group-editable disabled">
                                <div class="form-group">
                                
                                  <strong><span><i class="fas fa-user-circle"></i> {{$value->Name}} <i @if($value->IsRecommend == 0) class="fas fa-thumbs-down
                                        mr-3"
                                        @else class="fas fa-thumbs-up mr-3" @endif></i></span></strong>Recommended <br>
                                  <?php   $date = date("M, Y", strtotime($value->CreatedOn)); ?>
                                  {{$date}}
                                  <!-- edit button -->
                                  <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn" value="0"
                                    onclick="editrestReview({{$value->RestaurantReviewId}})">Edit</span>

                                  <textarea name="review" rows="7" class="form-control rounded-3 att_review" required
                                    disabled>{{$value->Description}}</textarea>
                                  <input type="hidden" name="" id="restid" value="{{$value->RestaurantId}}">
                                </div>

                                <span id="buttonsContainer-{{$value->RestaurantReviewId}}-1" class="buttons-container-dd d-none">
                                  <button type="button" value="1"
                                    class="reviewField-{{$value->RestaurantReviewId}} btn btn-dark approve-button px-4"
                                    onclick="aproverestReview({{$value->RestaurantReviewId}}, this)">Approve</button>
                                   
                                </span>

                                <span id="buttonsContainer-{{$value->RestaurantReviewId}}-2" class="buttons-container-dd d-none">
                                  <button type="button" value="2"
                                    class="reviewField-{{$value->RestaurantReviewId}} btn btn-dark cancel-button px-4"
                                    onclick="aproverestReview({{$value->RestaurantReviewId}}, this)">Disapprove</button>
                                </span>

                                <span id="buttonsContainer-{{$value->RestaurantReviewId}}-5" class="buttons-container-dd d-none" style="margin-left: 277px;"><span data-value="5" value="5"
                                    class="reviewField-{{$value->RestaurantReviewId}} approve-button  mark-spam px-4 fload-right"
                                    onclick="markRestReviewspam({{$value->RestaurantReviewId}}, this)"><u>mark spam</u></span></span>

                                <div id="buttonsContainer-{{$value->RestaurantReviewId}}-3"
                                  class="d-flex flex-wrap gap-2 pt-3 form-edit-buttons d-none">
                                  <button type="button" value="3"
                                    class="reviewField-{{$value->RestaurantReviewId}} btn btn-dark approve-button px-4"
                                    onclick="aproverestReview({{$value->RestaurantReviewId}}, this)">mark not spam</button>
                                  <button type="button" value="4"
                                    class="reviewField-{{$value->RestaurantReviewId}} btn btn-dark approve-button px-4"
                                    onclick="deleterestReview({{$value->RestaurantReviewId}}, this)">delete</button>
                                </div>

                              </div>
                              @endforeach
                              @else
                              <p>No Pending Reviews</p>
                              @endif
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