@if(!$getfaq->isEmpty())

<!-- <form class="" action="{{route('update_faq')}}" method="POST"> -->
@csrf

<div class="row">
  <div class="col-xs-6 col-sm-6 col-md-6">
    <div class="form-group mt-3">
      <span id="Success"></span>
      <strong>Location</strong>
      <input type="text" id="search_city" value="{{ $getfaq[0]->Name }}" name="ctname"
        class="form-control rounded-3" placeholder="Search City minimun 2 letters" disabled>

      <input type="hidden" name='attrid' id="locationid" value="{{ $getfaq[0]->LocationId }}"
        class="form-control rounded-3" required>

      @foreach($getfaq as $getfaq)

      <div class="form-group getdata-{{ $getfaq->LocationQuestionId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right" id="edit-btn" value="0"
          onclick="editlp({{ $getfaq->LocationQuestionId }})">Edit</span>

        <div id="questionmsg-{{ $getfaq->LocationQuestionId }}"></div>
        <textarea type="text" name="question" data-original-value="{{ $getfaq->Question }}"
          id="question{{ $getfaq->LocationQuestionId }}" class="form-control rounded-3"
          disabled>{{ $getfaq->Question }}</textarea>
      </div>

      <div class="form-group">
        <strong>Answer</strong>
        <div id="answer-{{ $getfaq->LocationQuestionId }}"></div>
        @php $i = 0; @endphp
        <textarea type="text" name="answer" data-original-value="{{ $getfaq->Answer }}"
          id="Answer{{ $getfaq->LocationQuestionId }}" rows="5" class="form-control rounded-3"
          placeholder="" disabled>{{ $getfaq->Answer }} @if($getfaq->listing != "")<?php $listings = json_decode($getfaq->listing, true); ?>@if($listings !== null) 
@foreach($listings as $listing)   @php $i ++; @endphp
{{$i}}. {{ $listing['name'] }}
@endforeach @endif @endif</textarea>
      </div>



      <span id="buttonsContainer-{{ $getfaq->LocationQuestionId }}"
        class="buttons-container-dd d-none mb-3 " data-colid="{{ $getfaq->LocationQuestionId }}">
        <button type="button" value="1"
          class="reviewField- btn btn-dark save-button px-4 updatefaq"
          data-id="{{ $getfaq->LocationQuestionId }}">Save</button>
        <button type="button" value="2" id="cancel-1"
          onclick="cancellp({{ $getfaq->LocationQuestionId }})"
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
        <p id="waitmsg"></p>
        <p id="errorcheck"></p>
        <label>
          <input class="form-check-input checkbox" type="checkbox" value="" id="checkbox1">
          <span>What are the top attractions to visit in {{ $getfaq->Name }}?</span>
        </label>
        <br>
        <label>
          <input class="form-check-input checkbox" type="checkbox" id="checkbox2">
          <span>What are the best outdoor activities in {{ $getfaq->Name }}?</span>
        </label>
        <br>
      
        <br>
        <span class="mt-3  list-buttons">
          <button id="savelocfaq" data-id="{{request()->route('id') }}"
            class="btn btn-dark">Save</button>
          <button id="cancel-list" type="button" class="btn btn-dark margin-l"
            data-bs-dismiss="modal">Cancel</button>
        </span>

        <hr>
        <p id="errorcustfaq"></p>
        <label for="">Question</label>
        <textarea type="text" name="question" id="addques"
          class="form-control rounded-3"></textarea>
        <!-- <label for="">Answer</label>
        <textarea type="text" name="Answer" 
        data-id="" id="addans" class="form-control rounded-3"
        ></textarea> -->
        <br>
        <br>
        <span class="mt-3  custom-faq-buttons">
          <button id="savecuslocfaq" data-id="{{request()->route('id')}}" class="btn btn-dark"
            disabled>Save</button>
          <button id="cancel-list" type="button" class="btn btn-dark margin-l"
            data-bs-dismiss="modal" disabled>Cancel</button>
        </span>

      </div>

      <div class="modal-footer mt-3">
        <!-- <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button> -->

      </div>
    </div>
  </div>
</div>


</div>