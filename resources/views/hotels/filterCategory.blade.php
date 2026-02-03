 @if (!empty($hotel_category))
 <!-- <form class="" action="{{route('update_faq')}}" method="POST"> -->
 @csrf

 <div class="row">
   <div class="col-xs-6 col-sm-6 col-md-6">
     <div class="form-group mt-3">
       <span id="Success"></span>
       <strong>Hotel</strong>
       <div class="form-search form-search-icon-right">
         <input type="text" id="search_attractionfaq" name="sightname" value="{{ $hname[0]->name }}"
           class="search_attractionfaqs form-control rounded-3" placeholder="" disabled>
       </div>
       <input type="hidden" name='hotelid' id="hotelid" value="{{ $hname[0]->hotelid }}" class="form-control rounded-3"
         required>
         <h4 class="mt-5">Categories</h4>
       <div class="historical-place mt-3 mb-5">
         @foreach($hotel_category as $value)

         <button class="btn btn-secondary btn-lg border-0 margin-l ml-3">{{ $value->type }} </button>
         <span class="crop"> <i class="material-icons-two-tone delete-category" data-id="{{ $value->id }}">
             delete</i></span>

         @endforeach
       </div>

     </div>
   </div>
 </div>
 </form>
 @else
 <p>Category Not Found.</p>
 @endif
 <!-- Modal -->
 <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
   aria-labelledby="staticBackdropLabel" aria-hidden="true">
   <div class="modal-dialog">
     <div class="modal-content" style="width: 647px;padding: 20px;">
       <div class="modal-header">
         <!-- <h3 class="modal-title" id="staticBackdropLabel"> -->
         <span style="font-weight: bold; text-decoration: underline;">
           Existing Category</span>
         <span class=" margin-l">Add
           New</span>
         <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
       </div>
       <div class="modal-body">
         <!-- <h4 class="text-center">Add New FAQ</h4> -->

         <div class="container">
           <p class="mt-3">Listed Categories in {{ $hname[0]->name }}</p>
           <p id="errorcheck"></p>
           @foreach($hotel_category as $value)
           <div class="historical-place mt-3">
             <button class="btn btn-secondary btn-lg border-0 margin-l ml-3">{{ $value->type }}
             </button>
             <span class="crop"> <i class="material-icons-two-tone delete-category" data-id="{{ $value->id }}">
                 delete</i></span>
           </div>
           @endforeach

           <hr>
           <h4 class="mb-3">Add New Category</h4>
           <p id="inputerror"></p>

           <p id="add_cat"></p>
           <div class="col-md-6 form-group">
             <strong class="form-label">Search Category Name </strong>
             <div class="form-search form-search-icon-right">
               <input type="text" name="" id="search_category" class="form-control rounded-3"
                 aria-describedby="emailHelp" placeholder="Name" required>
               <i class="ti ti-search"></i>
             </div>
             <span id="catlist"></span>
           </div>

           <br>
           <span class="mt-3  custom-faq-buttons">
             <button id="addnewcat" data-id="@if(request()->route('id') !='') {{request()->route('id')}} @else  {{$hname[0]->hotelid}} @endif " class="btn btn-dark">Save</button>
             <button id="cancel-list" type="button" class="btn btn-dark margin-l"
               data-bs-dismiss="modal">Cancel</button>
           </span>

         </div>

         <div class="modal-footer mt-3">
           <!-- <button type="button" class="btn btn-dark" data-bs-dismiss="modal">Close</button> -->

         </div>
       </div>
     </div>
   </div>



 </div>