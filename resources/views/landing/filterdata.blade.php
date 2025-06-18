<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
		   <div class="getfilterDataat">
			   @include('landing.filterdata_partial')
           <table class="table">
            
             <thead>
               <tr>
                 <th scope="col">Landing ID</th>
                 <th scope="col">Landing Name</th>
                 <th scope="col">Action</th>
               </tr> 
             </thead>
             <tbody>
           @if($type == 'attraction')
             @if(!$data->isEmpty())
               @foreach($data as $value)
                 <tr>
                   <td>{{ $value->ID }}</td>
                   <td>{{ $value->Page_Name }}</td>
                   <td>   
                   <a href="{{ route('edit_attraction_landing',[$value->ID])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Landing</a>                 
                  </td>
                    
                 </tr>
               @endforeach
               @else 
               <tr><td colspan="14">Data Not available.</td></tr>
             @endif
				 
				
                 @elseif($type == 'restaurant')
    @if(!$data->isEmpty())
      @foreach($data as $value)
        <tr>
          <td>{{ $value->id }}</td>
          <td>{{ $value->Name }}</td>
          <td>
        <a href="{{ route('edit_restaurant', ['id' => $restaurant->id]) }}" class="btn btn-primary">
    <i class="fas fa-edit"></i> Edit Landing
</a>
          </td>
        </tr>
      @endforeach
    @else
      <tr><td colspan="3">Data Not Available.</td></tr>
    @endif
				 
           @elseif($type == 'hotel')
              @if(!$data->isEmpty())
                  @foreach($data as $value)
                    <tr>
                      <td>{{ $value->id }}</td>
                      <td>{{ $value->Name }}</td>
                      <td>   
                      <a href="{{ route('edit_hotel_landing',[$value->id])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Landing</a>                 
                      </td>
                      
                    </tr>
                  @endforeach
                  @else 
                  <tr><td colspan="14">Data Not available.</td></tr>
                @endif
              
            @elseif($type = 'experience')
              @if(!$data->isEmpty())
                @foreach($data as $value)
                    <tr>
                      <td>{{ $value->id }}</td>
                      <td>{{ $value->Name }}</td>
                      <td>   
                      <a href="{{ route('edit_exp_landing',[$value->id])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Landing</a>                 
                      </td>
                      
                    </tr>
                  @endforeach
                  @else 
                  <tr><td colspan="14">Data Not available.</td></tr>
                @endif  
          @elseif($type = 'hotellisting')
              @if(!$data->isEmpty())
                @foreach($data as $value)
                    <tr>
                      <td>{{ $value->ID }}</td>
                      <td>{{ $value->Page_Name }}</td>
                      <td>   
                      <a href="{{ route('edit_hotel_listing_landing',[$value->id])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Landing</a>                 
                      </td>
                      
                    </tr>
                  @endforeach
                  @else 
                  <tr><td colspan="14">Data Not available.</td></tr>
                @endif 
           @endif
             </tbody>

           </table>
		   <!-- Pagination Links -->
          <div class="d-flex justify-content-center mt-4">
                              {{ $data->appends(['type' => request('type'), 'value' => request('value')])->links('pagination::bootstrap-4') }}

                            </div>
                        </div>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>   
</div>
</div>