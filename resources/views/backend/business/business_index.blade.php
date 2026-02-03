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
                <li class="breadcrumb-item" aria-current="page">Manage Business</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Manage Business </h2>
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

                    <br>
                    <!-- Button trigger modal -->

                    <!-- <span type="button" class="float-right" style="float:right">
                      <h4><u><a href="{{ route('add_admin_user') }}">Add New User</a></u>
                      </h4>
                    </span> -->



                    <span class="getupdateddata">
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

                      <span>
                  </div>
                </div>
              </div>
            </div>
          </div>

</x-app-layout>