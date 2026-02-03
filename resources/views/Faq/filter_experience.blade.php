<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
           <table class="table">
             <thead>
               <tr>
                 <th scope="col">Experience ID</th>
                 <th scope="col">Experience Name</th>
                 <th scope="col">Action</th>
               </tr> 
             </thead>
             <tbody>
             @if(!$data->isEmpty())
               @foreach($data as $value)
                 <tr>
                   <td>{{ $value->ExperienceId }}</td>
                   <td>{{ $value->Name }}</td>
                   <td>
                   <a href="{{ route('edit_experience_faq',[$value->ExperienceId])}}" target="_blank" class="ml-3 margin-l"><i class="fas fa-edit"></i> Edit Experience Faq</a>
                
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