  
        <hr>
        <form action="{{route('update_map_data')}}" method="post" id="update_map">
        <h5>Location</h5>
        <div class="row">
          <div class="col-md-6 ">
            <div class="info-box general-information">
              <h5>Street Address</h5>
              <input type="text" name="address" class="form-control" value="{{$getbusiness[0]->address}}"
                placeholder="address">

              <h5 class="mt-3">Postcode</h5>
              <input type="text" name="postcode" class="form-control" value="{{$getbusiness[0]->Pincode}}"
                placeholder="pincode">

                <input type="hidden" class="form-control fname" value="{{$getbusiness[0]->hid}}"  name="hotelid">
                <input type="hidden" class="form-control " value="{{$getbusiness[0]->bus_id}}"  name="bus_id">
                <input type="hidden" class="form-control " value="{{$getbusiness[0]->bslug}}"  name="bus_slug">

              <div class="col-md-12 mt-5">
                <div class="row">
                  <h5>Geo</h5>
                  <hr>
                  <div class="col-md-6">
                    <span>longitude</span>
                    <input type="text" name="longitude" class="form-control" value="{{$getbusiness[0]->longnitude}}"
                      placeholder="pincode">
                  </div>
                  <div class="col-md-6">
                    <span>Latitude</span>
                    <input type="text" name="latitude" class="form-control" value="{{$getbusiness[0]->Latitude}}"
                      placeholder="pincode">
                  </div>


                </div>

              </div>

              <div class="col-md-6 mt-3">
              <button class="btn btn-outline-dark " >cancel</button>
                  <button class="btn btn-outline-dark update-button2" >save</button>
                   
              </div>
            </div>
         
            </form>



          </div>
          <div class="col-md-6">
            <div class="info-box getcontact">

              <!-- start map -->
              <?php   
                $latitude ="";
                $longitude=""  ;

                $latitude = $getbusiness[0]->Latitude;
                $longitude = $getbusiness[0]->longnitude;

              ?>

              <div class="map border border-1 my-5">
                @if($getbusiness[0]->Latitude != "" && $getbusiness[0]->longnitude != "")
                <div id="map1" class="" style="width: 100%; height: 400px;"></div>

                <!-- <div id="screenshotContainer"></div> -->
                @endif

              </div>

            </div>

            <!-- end map -->

          </div>
        </div>