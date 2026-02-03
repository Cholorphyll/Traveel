    <div class="d-flex flex-wrap ">
            @if(!empty($restreview))
            @foreach($restreview as $rval)
            <div class="key-facts bg-F7F7F7">{{  substr($rval->Description, 0, 30)}}</div>
            @endforeach

            @endif
          </div>



          <div class="d-flex align-items-start mt-4">

            <div>

               @if(!$restreview->isEmpty())
              @foreach($restreview as $restreviews)
              <div class="d-flex ">

                <div class="mr-25">
                  <img src="{{ asset('/images/image 22.png')}}" class="w-51px " alt="">
                </div>
                <div class="">
                  <h5 class=" fs18"><b>{{$restreviews->Name}} </b></h5>
                  <?php $date = date('j F, Y', strtotime($restreviews->CreatedOn));
 ?>
                  <div class="fs14">Date of stay: {{$date}}</div>
                </div>

              </div>
              <div class=" fs18 my-20">
                <b>{{$restreviews->Description}}</b>
              </div>
              @endforeach
              @else
              <p>Reviews not found.</p>
              @endif

              <!-- <section class="overview">
                <h5 class="mb-8 fs24"><b>Overview</b></h5>


                <div class="more">
                  <span itemprop="description">

                    <p class="fs15"></p>
                  </span>
                </div>





              </section> -->


            </div>
            <!-- <img src="{{asset('/images/dots.svg')}}" alt="" class="d-none d-md-inline-block"> -->
          </div>