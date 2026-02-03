   @if(!$getuserdata->isEmpty())
                      <div class="row">
                        <div class="col-xs-6 col-sm-6 col-md-6">


                          <div class="form-group mt-3">
                            <span id="Success"></span>
                            <h4>User Details</h4>

                            <div class="form-group getdata-{{ $getuserdata[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="edit_user(1)">Edit</span>
                              <strong>First Name</strong>
                              <div id="questionmsg-{{ $getuserdata[0]->id }}"></div>
                              <div class="name-error"></div>
                              <input type="text" name="name" id="name" data-original-value="{{$getuserdata[0]->name}}"
                                class="form-control rounded-3 name1" value="{{$getuserdata[0]->name}}"
                                disabled>
                               
                            </div>
                            <input type="hidden" value="{{ $getuserdata[0]->id }}" id="id">

                            <span id="buttonsContainer-1" class="buttons-container-dd d-none mb-3"
                              data-colid="{{ $getuserdata[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 update_admin_user"
                                data-id="{{ $getuserdata[0]->id }}" data-colid="1">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="canceluseredit(1)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            <div class="form-group getdata-{{ $getuserdata[0]->id }} mt-3">
                              <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0"
                                onclick="edit_user(2)">Edit</span>

                              <strong>Email</strong>
                              <div class="email-error"></div>
                              <input type="email" name="email" data-original-value="{{$getuserdata[0]->email}}" 
                                class="form-control rounded-3 name2" value="{{$getuserdata[0]->email}}" disabled>
                            </div>

                            <span id="buttonsContainer-2" class="buttons-container-dd d-none mb-3 "
                              data-colid="{{ $getuserdata[0]->id }}">
                              <button type="button" value="1"
                                class="reviewField- btn btn-dark save-button px-4 update_admin_user"
                                data-id="{{ $getuserdata[0]->id }}" data-colid="2">Save</button>

                              <button type="button" value="2" id="cancel-1" onclick="canceluseredit(2)"
                                class=" btn btn-dark cancel-button px-4">Cancel</button>
                            </span>

                            
                           

                          </div>
                        </div>




                        @else
                        <p>Data not found.</p>
                        @endif