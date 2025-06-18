<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
           <table class="table">
           @if($val == "attraction")
             <thead>
               <tr>
                 <th scope="col">Attraction ID</th>
                 <th scope="col">Attraction Name</th>
                 <th scope="col">Action</th>
               </tr> 
             </thead>
             <tbody>
             @if(!$attraction->isEmpty())
               @foreach($attraction as $value)
                 <tr>
                   <td>{{ $value->SightId }}</td>
                   <td>{{ $value->Title }}</td>
                   <td>
                   <a href="{{ route('edit_review',[$value->SightId])}}" target="_blank"  class=" breadcrumb-item ml-3 margin-l"> <i class="fas fa-edit"></i>Edit review</a> 
                 
                   
                  </td>
                   
                 </tr>
               @endforeach
               @else 
               <tr><td colspan="14">Data Not available.</td></tr>
             @endif
             </tbody>
            @elseif($val == 'hotel')

            <thead>
               <tr>
                 <th scope="col">Hotel ID</th>
                 <th scope="col">Hotel Name</th>
                 <th scope="col">Action</th>
               </tr>
             </thead>
             <tbody>
             @if(!$hotellisting->isEmpty())
               @foreach($hotellisting as $value)
                 <tr>
                   <td>{{ $value->hotelid }}</td>
                   <td>{{ $value->name }}</td>
                   <td>                 
                   <a href="{{ route('edithotel_review',[$value->hotelid])}}" target="_blank" class="margin-l"> <i class="fas fa-edit"></i>Edit Review</a>                    
                  </td>
                   
                 </tr>
               @endforeach
               @else 
               <tr><td colspan="14">Data Not available.</td></tr>
             @endif
             </tbody>
           
            @endif
           </table>
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>   
</div>
</div>