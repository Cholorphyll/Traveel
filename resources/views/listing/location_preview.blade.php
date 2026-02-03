
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
              <li class="breadcrumb-item" aria-current="page">Manage Locations</li>
            </ul>
          </div>
          <div class="col-md-12">
            <div class="page-header-title">
              <h2 class="mb-0">Location <a href="{{route('listing')}}" class="btn btn-info ml-3"><strong>Add New Location</strong></a></h2>
            </div>
          </div>
        </div>
      </div>
    </div>
  <div class="py-12">
       <div class="">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
            <div class="row justify-content-center">
              <div class="col-md-12">
                <h2><strong>Location</strong><a href="{{route('add_sight')}}" class="btn btn-primary ml-3"><strong>Add Sight</strong></a></h2>
                  <table class="table">
                    <thead>
                      <tr>
                        <th scope="col">Location ID</th>
                        <th scope="col">Location Name</th>
                        <th scope="col">Location Lavel</th>
                        <th scope="col">Parent ID</th>
                        <th scope="col">IsActive</th>
                        <th scope="col">Country </th>
                        <th scope="col">IsState</th>
                        <th scope="col">MetaTag Title </th>
                        <th scope="col">Meta Tag Description</th>
                        <th scope="col">Uploaded Location </th>
                        <th scope="col">Create Date</th>
                        <th scope="col">About</th>
                        <th scope="col">Status</th>
                        <th scope="col">Related Tags</th>
                        <th scope="col">Search Tags</th>
                        <th scope="col">flg api</th>
                        <th scope="col">location api</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody>
                    @if($listing)
                      @foreach($listing as $value)
                        <tr>
                          <td>{{ $value->LocationId }}</td>
                          <td>{{ $value->Name }}</td>
                          <td>@if($value->LocationLevel == 0) Parent @else Child @endif</td>
                          <td> @if($value->LocationLevel != 0) {{$value->ParentId}} @else Null @endif</td>
                          <td> @if($value->IsActive == 1) Active @else Inactive @endif</td>
                          <td>{{ $value->countryname }}</td>
                          <td>{{ $value->IsState }}</td>
                          <td>{{ $value->MetaTagTitle }}</td>
                          <td>{{ $value->MetaTagDescription }}</td>
                          <td>{{ $value->UploadLocation }}</td>
                          <td>{{ $value->CreateDate }}</td>
                          <td>{{ $value->About }}</td>
                          <td>{{ $value->Status }}</td>
                          <td>{{ $value->Related_Tags }}</td>
                          <td>{{ $value->Search_Tags }}</td>
                          <td>{{ $value->flg_api }}</td>
                          <td>{{ $value->location_api }}</td>
                          <td><a href="{{ route('edit_listing',[$value->LocationId])}}" class="btn btn-primary ml-3"><strong>Edit</strong></a></td>
                          
                        </tr>
                      @endforeach
                      @else 
                      <tr><td colspan="14">Data Not available.</td></tr>
                    @endif
                    </tbody>
                  </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>   
     </div>
  </div>   
</div>
</x-app-layout>