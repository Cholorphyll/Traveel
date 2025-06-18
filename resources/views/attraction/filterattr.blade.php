<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
           <table class="table">
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
					     <a href="{{ route('att_detail',[$value->LocationId.'-'.$value->SightId.'-'.$value->Slug])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i>short - Edit Attraction</a>
					   <a href="{{ route('edit_attraction',[$value->SightId])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Attraction</a>
					 <a href="{{ route('edit_attfaq',[$value->SightId])}}" target="_blank" class=" ml-3 margin-l"> <i class="fas fa-edit"></i>Edit FAQ</a>
                <a href="{{ route('edit_category',[$value->SightId])}}" target="_blank" class="ml-3 margin-l"> <i class="fas fa-edit"></i>Edit Category</a>
                   <a href="{{ route('edit_reviewbyid',[$value->SightId])}}" target="_blank"  class=" breadcrumb-item ml-3 margin-l"> <i class="fas fa-edit"></i>Edit review</a> 
                   <a href="{{ route('edit_landing',[$value->SightId])}}" target="_blank"  class=" breadcrumb-item ml-3 margin-l"> <i class="fas fa-edit"></i>Edit landing Page</a>
                  <a href="{{ route('edit_sight_img',[$value->SightId])}}" target="_blank"  class=" breadcrumb-item ml-3 margin-l"> <i class="fas fa-edit"></i>Edit Images</a>
                  </td>
                   
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