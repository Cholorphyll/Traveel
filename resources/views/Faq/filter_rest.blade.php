<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
        
           <table class="table">
             <thead>
               <tr>
                 <th scope="col">Restaurant ID</th>
                 <th scope="col">Restaurant Name</th>
                 <th scope="col">Action</th>
               </tr>
             </thead>
             <tbody>
             @if(!$hotellisting->isEmpty())
               @foreach($hotellisting as $value)
                 <tr>
                   <td>{{ $value->RestaurantId }}</td>
                   <td>{{ $value->Title }}</td>
                   <td>  
                   <a href="{{ route('edit_restaurant_faq',[$value->RestaurantId])}}" target="_blank" class="margin-l"> <i class="fas fa-edit"></i>Edit Restaurant Faq</a> 
                
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