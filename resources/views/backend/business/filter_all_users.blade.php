  @if(!$getuser->isEmpty())
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">

                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Username</th>
                                <th scope="col">Email</th>  
                                <th scope="col">action</th>
                              </tr>
                            </thead>
                            <tbody>  
                              <?php $x=1; ?>
                              @foreach($getuser as $value)
                              <tr @if($value->IsEmailVerify == 0) class="table-danger" @elseif($value->IsEmailVerify == 1) class="table-success" @endif>
                                <td>{{$x}}</td>
                                <td>{{$value->username}}</td>
                                <td>{{$value->busi_username}}</td>
                                <td>{{$value->email}}</td>
                                <td >
                                  <div class="row">
                                    <div class="col-md-3"> 
                                      <select name="" class="form-control buss-active"
                                        data-id="{{$value->id}}">
                                        <option value="1" @if($value->IsEmailVerify == 1) selected @endif >Active</option>
                                        <option value="0" @if($value->IsEmailVerify == 0) selected @endif>Inactive</option>
                                     
                                      </select>
                                    </div>
                                    <div class="col-md-3">
                                      <button  class="btn btn-danger" data-userid="{{$value->id}}" id="delete-business-user">Delete</button>
                                      </div>
                                  </div>


                                </td>
                              </tr>
                              @php $x++ @endphp
                              @endforeach
                            </tbody>
                          </table>


                        </div>


                      </div>



                      @else
                      <p>Data not found.</p>
                      @endif