<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
        
           <table class="table">
             <thead>
               <tr>
                 <th scope="col">ID</th>
                 <th scope="col">Hotel ID</th>
                 <th scope="col">Hotel Name</th>
                 <th scope="col">Action</th>
               </tr>
             </thead>
             <tbody>
             @if(!$hotellisting->isEmpty())
               @foreach($hotellisting as $value)
                 <tr>
                   <td>{{ $value->id }}</td>
                   <td>{{ $value->hotelid }}</td>
                   <td>{{ $value->name }}</td>
                   <td> 
                   <a href="{{ route('edit_hotel',[$value->id])}}" target="_blank" > <i class="fas fa-edit"></i>Edit Hotel</a> 
                   <a href="{{ route('edit_review',[$value->hotelid])}}" target="_blank" class="margin-l"> <i class="fas fa-edit"></i>Edit Review</a>
                   <a href="{{ route('edit_hotel_faqs',[$value->hotelid])}}" target="_blank" class="margin-l"> <i class="fas fa-edit"></i>Edit Faqs</a>  
                   <a href="{{ route('edit_hotel_category',[$value->hotelid])}}" target="_blank" class="margin-l"> <i class="fas fa-edit"></i>Edit Category</a>
                      <a href="{{ route('edit_hotel_landing',[$value->hotelid])}}" target="_blank" class="margin-l"> <i class="fas fa-edit"></i>Edit Landing Page</a>   
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