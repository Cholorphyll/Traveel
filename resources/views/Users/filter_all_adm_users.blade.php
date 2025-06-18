@if(!$getuser->isEmpty())
                      <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12">

                          <table class="table">
                            <thead>
                              <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">Email</th>  
                                <th scope="col">action</th>
                              </tr>
                            </thead>
                            <tbody>  
                              <?php $x=1; ?>
                              @foreach($getuser as $value)
                              <tr @if($value->isActive == 0) class="table-danger" @elseif($value->isActive == 2) class="table-secondary" @endif>
                                <td>{{$x}}</td>
                                <td>{{$value->name}}</td>
                                <td>{{$value->email}}</td>
                                <td >
                                  <div class="row">
                                    <div class="col-md-3"> 
                                      <select name="" class="form-control adm-active"
                                        data-id="{{$value->id}}">
                                        <option value="1" @if($value->isActive == 1) selected @endif >Active</option>
                                        <option value="0" @if($value->isActive == 0) selected @endif>Inactive</option>
                                        <option value="2" @if($value->isActive == 2) selected @endif>Temp-User</option>
                                      </select>
                                    </div>
                                    <div class="col-md-3">
                                      <button  class="btn btn-danger" data-userid="{{$value->id}}" id="delete-user">Delete</button>
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
