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
                <li class="breadcrumb-item" aria-current="page">Add Faq</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0"> Manage Attraction Reviews </h2>
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
                    <span class="filter-review-option" data-value="0">Pending |</span>
                    <span class="filter-review-option" data-value="1">Approved |</span>
                    <span class="filter-review-option" data-value="2">Disapproved |</span>
                    <span class="filter-review-option" data-value="3">Spam </span>
                    <div class="row float-right" style="float:right">
                      <div class="col-md-5 form-group">
                        <select name="" id="sort_att_review" class="form-control">
                          <option value="">Sort Reviews</option>
                          <option value="desc">Newest</option>
                          <option value="asc">Oldest</option>
                        </select>
                      </div>
                      <div class="col-md-7 form-group ">
                        <div class="form-search form-search-icon-right">
                          <input type="text" name="search_value" id="filter_reviews" class="form-control rounded-3"
                            aria-describedby="emailHelp" placeholder="search by review id" required><i
                            class="ti ti-search"></i>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-12">
                      <div class="row">
                        <div class="col-xs-8 col-sm-8 col-md-8">
                          <div class="form-group mt-3 list-container">
                            @if(!$get_attreview->isEmpty())
                            @foreach($get_attreview as $value)
                            <form class="" action="{{route('update_review',[$value->Id])}}" method="POST"
                              autocomplete="on">
                              @csrf
                              <div class="form-group form-group-editable disabled">
                                <div class="form-group">
                                  <strong><span><i class="fas fa-user-circle"></i> {{$value->FirstName}}
                                      {{$value->LastName}} <i @if($value->IsRecommend == 0) class="fas fa-thumbs-down"
                                        @else class="fas fa-thumbs-up" @endif></i></span></strong>Recommended <br>
                                  <?php   $date = date("M, Y", strtotime($value->CreatedDate)); ?>
                                  {{$date}}
                                  <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn" value="0"
                                    onclick="editReview({{$value->Id}})">Edit</span>
                                  <textarea name="review" rows="7" class="form-control rounded-3 att_review" required
                                    disabled>{{$value->ReviewDescription}}</textarea>
                                  <input type="hidden" name="" id="sightid" value="{{$value->SightId}}">
                                </div>

                                <span id="buttonsContainer-{{$value->Id}}-1" class="buttons-container-dd d-none">
                                  <button type="button" value="1"
                                    class="reviewField-{{$value->Id}} btn btn-dark save-button px-4"
                                    onclick="saveReview({{$value->Id}}, this)">Approve</button>
                                </span>

                                <span id="buttonsContainer-{{$value->Id}}-2" class="buttons-container-dd d-none">
                                  <button type="button" value="2"
                                    class="reviewField-{{$value->Id}} btn btn-dark cancel-button px-4"
                                    onclick="saveReview({{$value->Id}}, this)">Disapprove</button>
                                </span>

                                <div id="buttonsContainer-{{$value->Id}}-2"
                                  class="d-flex flex-wrap gap-2 pt-3 form-edit-buttons d-none">
                                  <button type="button" value="3"
                                    class="reviewField-{{$value->Id}} btn btn-dark save-button px-4"
                                    onclick="saveReview({{$value->Id}}, this)">mark not spam</button>
                                  <button type="button" value="1"
                                    class="reviewField-{{$value->Id}} btn btn-dark save-button px-4"
                                    onclick="saveReview({{$value->Id}}, this)">delete</button>
                                </div>
                              </div>
                              @endforeach
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