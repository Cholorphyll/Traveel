   @if($getbusiness[0]->MenuLink !="")    
              <p> <a href="{{$getbusiness[0]->MenuLink}}">{{$getbusiness[0]->MenuLink}}</a>
                </p> 
                @endif
                <h5 class="mt-5">add, edit menu link</h5>
                <div class="col-md-10">
                 <form action="" id="update_menu_link">
                  <input type="text" class="form-control" name="menulink"  value="">
                  <input type="hidden" class="form-control " value="{{$getbusiness[0]->bus_id}}" name="bus_id">
                  <input type="hidden" class="form-control " value="{{$getbusiness[0]->business_id}}" name="business_id">
                
     
                  <button type="submit"  class="btn btn-dark mt-3"  >Submit</button>
         
            </form>
               </div>