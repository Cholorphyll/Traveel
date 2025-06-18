<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
           <table class="table">
             <thead>
               <tr>
                 <th scope="col">Location ID</th>
                 <th scope="col">Location Name</th>
                 <th scope="col">SEO Index</th>
                 <th scope="col">Action</th>
               </tr>
             </thead>
             <tbody>
             @if(!$listing->isEmpty())
               @foreach($listing as $value)
                 <tr>
                   <td>{{ $value->LocationId }}</td>
                   <td>{{ $value->Name }}, {{$value->countryname}}</td>
                   <td>
                     <div class="form-check">
                       <input type="checkbox" class="form-check-input seo-checkbox" 
                              data-id="{{ $value->LocationId }}"
                              {{ isset($value->show_in_index) && $value->show_in_index ? 'checked' : '' }}
                              onchange="updateSeoStatus(this)">
                     </div>
                   </td>
                   <td>                  
                     <a href="{{ route('edit_listing',[$value->LocationId])}}" target="_blank">
                       <i class="fas fa-edit"></i>Edit Location
                     </a> 
                     <a href="{{ route('edit_faq',[$value->LocationId])}}" target="_blank" class="margin-l">
                       <i class="fas fa-edit"></i>Edit FAQs
                     </a>
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

<script>
function updateSeoStatus(checkbox) {
    const locationId = checkbox.dataset.id;
    const isChecked = checkbox.checked;
    
    fetch('/admin/location-seo/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            location_id: locationId,
            show_in_index: isChecked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optional: Show success message
            console.log('SEO status updated successfully');
        } else {
            // Revert checkbox if update failed
            checkbox.checked = !isChecked;
            console.error('Failed to update SEO status');
        }
    })
    .catch(error => {
        // Revert checkbox if request failed
        checkbox.checked = !isChecked;
        console.error('Error updating SEO status:', error);
    });
}
</script>