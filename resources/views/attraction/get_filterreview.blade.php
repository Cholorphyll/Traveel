@if(!$get_attreview->isEmpty())
@foreach($get_attreview as $value)

@csrf
<div class="form-group form-group-editable disabled">
  <div class="form-group">
    <strong><span><i class="fas fa-user-circle"></i> {{$value->Name}}<i @if($value->IsRecommend == 0) class="fas
          fa-thumbs-down"
          @else class="fas fa-thumbs-up" @endif></i></span></strong>Recommended <br>
    <?php   $date = date("M, Y", strtotime($value->CreatedDate)); ?>
    {{$date}}
    <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn" @if(!empty($val)) value=" {{$val}} " @endif
      onclick="editReview({{$value->Id}})">Edit</span>
    <textarea name="review" rows="7" class="form-control rounded-3 att_review" required
      disabled>{{$value->ReviewDescription}}</textarea>
    <input type="hidden" name="" id="sightid" value="{{$value->SightId}}">
  </div>

  <span id="buttonsContainer-{{$value->Id}}-1" class="buttons-container-dd d-none">
    <button type="button" value="1" class="reviewField-{{$value->Id}} btn btn-dark save-button px-4"
      onclick="saveReview({{$value->Id}}, this)">Approve</button>
  </span>

  <span id="buttonsContainer-{{$value->Id}}-2" class="buttons-container-dd d-none">
    <button type="button" value="2" class="reviewField-{{$value->Id}} btn btn-dark cancel-button px-4"
      onclick="saveReview({{$value->Id}}, this)">Disapprove</button>
  </span>

  <span id="buttonsContainer-{{$value->Id}}-5" class="buttons-container-dd " style="margin-left: 530px;"><span
      data-value="5" value="5" class="reviewField-{{$value->Id}} approve-button  mark-spam px-4 fload-right"
      onclick="markattReviewspam({{$value->Id}}, this)"><u>mark spam</u></span></span>


  <div id="buttonsContainer-{{$value->Id}}-3" class="d-flex flex-wrap gap-2 pt-3 form-edit-buttons d-none">
    <button type="button" value="3" class="reviewField-{{$value->Id}} btn btn-dark save-button px-4"
      onclick="saveReview({{$value->Id}}, this)">mark not spam</button>
    <button type="button" value="1" class="reviewField-{{$value->Id}} btn btn-dark save-button px-4"
      onclick="saveReview({{$value->Id}}, this)">delete</button>
  </div>
</div>

@endforeach

@else
<p>Review not found.</p>
@endif