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
                <li class="breadcrumb-item" aria-current="page">All Admin User</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">All Admin User</h2>
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

                    <span type="button" class="float-right" style="float:right">
                      <h4><u><a href="{{ route('add_admin_user') }}">Add New User</a></u>
                      </h4>
                    </span>



                    <span class="getupdateddata">
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

                      <span>
                  </div>
                </div>
              </div>
            </div>
          </div>

</x-app-layout>