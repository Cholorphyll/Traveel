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