   @if(!$getuser->isEmpty())
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">

                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Business</th>
                                <th scope="col">User</th>
                                <th scope="col">Photo Id</th>
                                <th scope="col">Phone</th>
                                <th scope="col">Status</th>
                                <th scope="col">action</th>
                              </tr>
                            </thead>
                            <tbody>  
                              <?php $x=1; ?>
                              @foreach($getuser as $value)
                              <tr @if($value->varify_business == 0) class="table-danger" @elseif($value->varify_business == 1) class="table-success" @endif>
                                <td>{{$x}}</td>
                                <td>{{$value->name}}
                                <p> <b>Address: </b>{{$value->address}}</p>
                                <p> <b>Business: </b>{{ strtoupper($value->business_type) }}</p>
                                </td>
                                <td><p><b>Name:</b>{{$value->username}}</p>
                                  <p><b>Username:</b>{{$value->busi_username}}</p>
                                  <p><b>Email:</b>{{$value->email}}</p>
                                </td>
                                <td>  
                                      <img src="https://s3-us-west-2.amazonaws.com/s3-travell/business-images/{{$value->image_id}}" alt="" class="usericon img-fluid "
                                style="width: 102px; margin-right: 4px; margin-top: 6px;">
                                @if ($value->varify_image == 1)
                                            <p style="text-align: center;">Varified</p>
                                @else
                                    <p style="text-align: center;">Not varified</p>
                                @endif
                              </td>
                                <td>{{$value->phone}}
                                  <br>
                                @if ($value->varify_phone == 1)
                                    <p style="text-align: center;">Varified </p>
                                @else
                                    <p style="text-align: center;">Not Varified</p>
                                @endif
                                </td>
                                <td >
                                  <div class="">
                                   
                                      <select name="" class="form-control buss-status-active"
                                        data-id="{{$value->id}}" style="width: 98px;">
                                        <option value="1" @if($value->varify_business == 1) selected @endif >Active</option>
                                        <option value="0" @if($value->varify_business == 0) selected @endif>Inactive</option>
                                      </select>
                                  
                                  
                                  </div>


                                </td>
                                <td>  <div class="col-md-3">
                                      <button  class="btn btn-danger" data-userid="{{$value->id}}" id="delete-business">Delete</button>
                                      </div></td>
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