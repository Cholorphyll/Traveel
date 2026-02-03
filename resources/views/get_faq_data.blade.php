   <div class="asked-questions py-4 border-top">

         
          <h5 class="mb-3 heading fs-26">Frequently Asked Questions about {{$lname}}</h5>

          @foreach($faq as $value)
          <?php $listing = json_decode($value->listing, true); ?>
          <div class="question py-3">
            <h6 class="fs-18">
              <span>Q.{{$value->Question}}?</span>
            </h6>

            <div class="mb-0">
              <div>
                <p>A.{{$value->Answer}}</p>

                @if(!empty($listing ))
                <ul style=" margin-left: 29px;">
                  @foreach ($listing as $index => $item)
                  <?php $name = $item['name'];
          $url = $item['url']; ?>

                  <li>
                  <a href="{{ asset('at-'.$value->slugid.'-'.$url) }}" target="_blank"> {{$name}}</a>@if($index < count($listing)-1)<span
                      class="d-none">,</span>@endif
                  </li>
                  @endforeach
                </ul>
                @endif
              </div>
            </div>

        
            <!-- <a href="#" class="text-dark">See all nearby attractions.</a> -->
          </div>
          @endforeach
        </div>
      