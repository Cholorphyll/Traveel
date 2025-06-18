@if(!$hotelreview->isEmpty())
@foreach($hotelreview as $value)

<form class="" action="{{route('update_review',[$value->HotelReviewId])}}" method="POST" autocomplete="on">
  @csrf
  <div class="form-group form-group-editable disabled">
    <div class="form-group">

      <strong><span><i class="fas fa-user-circle"></i> {{$value->Name}} <i @if($value->IsRecommend == 0) class="fas
            fa-thumbs-down
            mr-3"
            @else class="fas fa-thumbs-up mr-3" @endif></i></span></strong>Recommended <br>
      <?php   $date = date("M, Y", strtotime($value->CreatedOn)); ?>
      {{$date}}
      <!-- edit button -->

      <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn"
        value="@isset($val) {{ $val }} @else  0 @endisset"
        onclick="edithotelReview({{$value->HotelReviewId}})">Edit</span>

      <textarea name="review" rows="7" class="form-control rounded-3 att_review" required
        disabled>{{$value->Description}}</textarea>
      <input type="hidden" name="" id="hotelid" value="{{$value->HotelId}}">
    </div>

    <span id="buttonsContainer-{{$value->HotelReviewId}}-1" class="buttons-container-dd d-none">
      <button type="button" value="1" class="reviewField-{{$value->HotelReviewId}} btn btn-dark approve-button px-4"
        onclick="aproveReview({{$value->HotelReviewId}}, this)">Approve</button>
    </span>

    <span id="buttonsContainer-{{$value->HotelReviewId}}-2" class="buttons-container-dd d-none">
      <button type="button" value="2" class="reviewField-{{$value->HotelReviewId}} btn btn-dark cancel-button px-4"
        onclick="aproveReview({{$value->HotelReviewId}}, this)">Disapprove</button>
    </span>

    <span id="buttonsContainer-{{$value->HotelReviewId}}-5" class="buttons-container-dd d-none"
      style="margin-left: 277px;"><span data-value="5" value="5"
        class="reviewField-{{$value->HotelReviewId}} approve-button  mark-spam px-4 fload-right"
        onclick="markReviewspam({{$value->HotelReviewId}}, this)"><u>mark spam</u></span></span>

    <div id="buttonsContainer-{{$value->HotelReviewId}}-3" class="d-flex flex-wrap gap-2 pt-3 form-edit-buttons d-none">
      <button type="button" value="3" class="reviewField-{{$value->HotelReviewId}} btn btn-dark approve-button px-4"
        onclick="aproveReview({{$value->HotelReviewId}}, this)">mark not spam</button>
      <button type="button" value="4" class="reviewField-{{$value->HotelReviewId}} btn btn-dark approve-button px-4"
        onclick="aproveReview({{$value->HotelReviewId}}, this)">delete</button>
    </div>

  </div>
  @endforeach

  @else
  <p>No Reviews</p>
  @endif