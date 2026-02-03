<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
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
                      <td>{{ $value->ID }}</td>
                      <td>{{ $value->Page_Name }}</td>
                      <td>   
                      <a href="{{ route('edit_exp_landing',[$value->ID])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Landing</a>                 
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