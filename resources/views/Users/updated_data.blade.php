@if(!$getuserdata->isEmpty())
<div class="row">
  <div class="col-xs-6 col-sm-6 col-md-6">


    <div class="form-group mt-3">
      <span id="Success"></span>
      <h4>User Details</h4>

      <div class="form-group getdata-{{ $getuserdata[0]->UserId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
          onclick="edit_user(1)">Edit</span>
        <strong>First Name</strong>
        <div id="questionmsg-{{ $getuserdata[0]->UserId }}"></div>
        <input type="text" name="question" data-original-value="{{$getuserdata[0]->FirstName}}" id="fname"
          class="form-control rounded-3 name1" value="{{$getuserdata[0]->FirstName}}" disabled>
      </div>
      <input type="hidden" value="{{ $getuserdata[0]->UserId }}" id="id">

      <span id="buttonsContainer-1" class="buttons-container-dd d-none mb-3" data-colid="{{ $getuserdata[0]->UserId }}">
        <button type="button" value="1" class="reviewField- btn btn-dark save-button px-4 update_user"
          data-id="{{ $getuserdata[0]->UserId }}" data-colid="1">Save</button>

        <button type="button" value="2" id="cancel-1" onclick="canceluseredit(1)"
          class=" btn btn-dark cancel-button px-4">Cancel</button>
      </span>

      <div class="form-group getdata-{{ $getuserdata[0]->UserId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
          onclick="edit_user(2)">Edit</span>

        <strong>Last Name</strong>

        <input type="text" name="" data-original-value="{{$getuserdata[0]->LastName}}" id="lname"
          class="form-control rounded-3 name2" value="{{$getuserdata[0]->LastName}}" disabled>
      </div>

      <span id="buttonsContainer-2" class="buttons-container-dd d-none mb-3 "
        data-colid="{{ $getuserdata[0]->UserId }}">
        <button type="button" value="1" class="reviewField- btn btn-dark save-button px-4 update_user"
          data-id="{{ $getuserdata[0]->UserId }}" data-colid="2">Save</button>

        <button type="button" value="2" id="cancel-1" onclick="canceluseredit(2)"
          class=" btn btn-dark cancel-button px-4">Cancel</button>
      </span>

      <div class="form-group getdata-{{ $getuserdata[0]->UserId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
          onclick="edit_user(3)">Edit</span>
        <strong>Username</strong>

        <input type="text" name="question" data-original-value="{{$getuserdata[0]->Username}}" id="username"
          class="form-control rounded-3 name3" value="{{$getuserdata[0]->Username}}" disabled>
      </div>

      <span id="buttonsContainer-3" class="buttons-container-dd d-none mb-3 "
        data-colid="{{ $getuserdata[0]->UserId }}">
        <button type="button" value="1" class="reviewField- btn btn-dark save-button px-4 update_user"
          data-id="{{ $getuserdata[0]->UserId }}" data-colid="3">Save</button>

        <button type="button" value="3" id="cancel-1" onclick="canceluseredit(3)"
          class=" btn btn-dark cancel-button px-4">Cancel</button>
      </span>

      <div class="form-group getdata-{{ $getuserdata[0]->UserId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
          onclick="edit_user(4)">Edit</span>
        <strong>Email</strong>

        <div class="email-error"></div>
        <input type="text" name="question" data-original-value="{{$getuserdata[0]->Email}}" id="Email"
          class="form-control rounded-3 name4" value="{{$getuserdata[0]->Email}}" disabled>
      </div>

      <span id="buttonsContainer-4" class="buttons-container-dd d-none mb-3 "
        data-colid="{{ $getuserdata[0]->UserId }}">
        <button type="button" value="1" class="reviewField- btn btn-dark save-button px-4 update_user"
          data-id="{{ $getuserdata[0]->UserId }}" data-colid="4">Save</button>

        <button type="button" value="3" id="cancel-1" onclick="canceluseredit(4)"
          class=" btn btn-dark cancel-button px-4">Cancel</button>
      </span>

      <div class="form-group getdata-{{ $getuserdata[0]->UserId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
          onclick="edit_user(5)">Edit</span>
        <strong>Phone</strong>

        <input type="text" name="question" data-original-value="{{$getuserdata[0]->PhoneNumber}}" id="phone"
          class="form-control rounded-3 name5" value="{{$getuserdata[0]->PhoneNumber}}" disabled>

      </div>
      <span id="buttonsContainer-5" class="buttons-container-dd d-none mb-3 "
        data-colid="{{ $getuserdata[0]->UserId }}">
        <button type="button" value="1" class="reviewField- btn btn-dark save-button px-4 update_user"
          data-id="{{ $getuserdata[0]->UserId }}" data-colid="5">Save</button>

        <button type="button" value="3" id="cancel-1" onclick="canceluseredit(5)"
          class=" btn btn-dark cancel-button px-4">Cancel</button>
      </span>


      <div class="form-group getdata-{{ $getuserdata[0]->UserId }} mt-3">
        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
          onclick="edit_user(6)">Edit</span>
        <br>
        <strong>City</strong>
        <div class="city-error"></div>
        <div class="form-search form-search-icon-right">
          <input type="text" id="search_usercity" name="ctname" data-original-value="{{$getuserdata[0]->Name}}"
            class="form-control rounded-3 name6" value="{{$getuserdata[0]->Name}}"
            placeholder="Search City minimun 2 letters " disabled><i class="ti ti-search"></i>
        </div>

        <span id="citylist"></span>

      </div>



      <span id="buttonsContainer-6" class="buttons-container-dd d-none mb-3 "
        data-colid="{{ $getuserdata[0]->UserId }}">
        <button type="button" value="1" class="reviewField- btn btn-dark save-button px-4 update_user"
          data-id="{{ $getuserdata[0]->UserId }}" data-colid="6">Save</button>

        <button type="button" value="3" id="cancel-1" onclick="canceluseredit(6)"
          class=" btn btn-dark cancel-button px-4">Cancel</button>
      </span>


    </div>
  </div>




  @else
  <p>Data not found.</p>
  @endif