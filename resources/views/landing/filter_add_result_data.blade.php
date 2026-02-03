<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
           <table class="table">
            
             <thead>
               <tr>
                 <th scope="col"> ID</th>
                 <th scope="col"> Name</th>
                 <th scope="col">Action</th>
               </tr> 
             </thead>
             <tbody>
           @if($type == 'Attraction')
             @if(!$data->isEmpty())
               @foreach($data as $value)
                 <tr>
                   <td>{{ $value->SightId }}</td>
                   <td>{{ $value->Title }}</td>
                   <td>   
                   <a href="{{ route('add_sight_landing_page',[$value->SightId])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Add Landing</a>                 
                  </td>
                   
                 </tr>
               @endforeach
               @else 
               <tr><td colspan="14">Data Not available.</td></tr>
             @endif
           @elseif($type == 'Hotel')
              @if(!$data->isEmpty())
                  @foreach($data as $value)
                    <tr>
                      <td>{{ $value->hotelid }}</td>
                      <td>{{ $value->name }}</td>
                      <td>   
                      <a href="{{ route('add_hotel_landing',[$value->hotelid])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Add Landing</a>                 
                      </td>
                      
                    </tr>
                  @endforeach
                  @else 
                  <tr><td colspan="14">Data Not available.</td></tr>
                @endif
   
             @elseif($type == 'Restaurant')
               @if(!$data->isEmpty())
                   @foreach($data as $value)
                     <tr>
                       <td>{{ $value->RestaurantId }}</td>
                       <td>{{ $value->Title }}</td>
                       <td>   
                       <a href="{{ route('add_rest_landing',[$value->RestaurantId])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Add Landing</a>                 
                       </td>
                     </tr>
                   @endforeach
                   @else 
                   <tr><td colspan="14">Data Not available.</td></tr>
                 @endif

             @elseif($type == 'Experience')
              @if(!$data->isEmpty())
                  @foreach($data as $value)
                    <tr>
                      <td>{{ $value->ExperienceId }}</td>
                      <td>{{ $value->Name }}</td>
                      <td>   
                      <a href="{{ route('add_exp_landing',[$value->ExperienceId])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Add Landing</a>                 
                      </td>
                      
                    </tr>
                  @endforeach
                  @else 
                  <tr><td colspan="14">Data Not available.</td></tr>
                @endif   
               @elseif($type == 'hotellisting')
                  @if(!$data->isEmpty())
                    @foreach($data as $value)
                      <tr>
                        <td>{{ $value->LocationId }}</td>
                        <td>{{ $value->Name }}</td>
                        <td>   
                        <a href="{{ route('add_hotel_listing_landing',[$value->LocationId])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Add Landing</a>                 
                        </td>
                        
                      </tr>
                    @endforeach
                    @else 
                    <tr><td colspan="14">Data Not available.</td></tr>
                  @endif   
            @elseif($type == '')
              @if(!$data->isEmpty())
                  @foreach($data as $value)
                    <tr>
                      <td>{{ $value->ID }}</td>
                      <td>{{ $value->Page_Name }}</td>
                      <td>                   
                      </td>
                      
                    </tr>
                  @endforeach
                  @else 
                  <tr><td colspan="14">Data Not available.</td></tr>
                @endif  
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