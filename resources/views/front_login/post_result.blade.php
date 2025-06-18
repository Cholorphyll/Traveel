  @if(!$getpost->isEmpty())
              @foreach($getpost as $val)
              <div class="col-md-5">
                <!-- Card START -->
                <div class="card card-img-scale border-0 overflow-hidden bg-transparent">
                  <!-- Image and overlay -->
                  <div class="card-img-scale-wrapper rounded-3">
                    <!-- Image -->
                    <?php $a= 1 ; ?>
                    @if(!$getpostimages->isEmpty())
                    @foreach($getpostimages as $img)
                    @if($img->postid == $val->Id && $a == 1)

                    <?php $a = 2; ?>
                    <img src="https://s3-us-west-2.amazonaws.com/s3-travell/post-images/{{$img->postimge}}"
                      class="card-img br-10 mb-12" alt="hotel image" style="max-height: 287px;">
                    @endif
                    @endforeach
                    @endif
                  </div>

                  <!-- Card body -->
                  <div class="">
                    <!-- Title -->
                    <?php  
                          
                                  $locationParts = explode(',', $val->LocationName);
                                  $city = isset($locationParts[0]) ? trim($locationParts[0]) : '';
                                  $country = isset($locationParts[1]) ? trim($locationParts[1]) : '';
                          
                          ?>

                    <div class="d-flex align-items-center justify-content-between">
                      <h5>{{ $city }}</h5>

                    </div>
                    <p>{{ $country }}</p>
                    {{$val->Description}}

                  </div>
                </div>
                <!-- Card END -->
              </div>


              @endforeach
              @endif