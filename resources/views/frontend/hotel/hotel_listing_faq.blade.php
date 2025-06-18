          <div class="asked-questions py-4">

            @if(!empty($faq))
            <h5 class="mb-3 heading fs-26">Frequently Asked Questions about {{$lname}}</h5>

            @foreach($faq as $value)
            @if(Str::startsWith($value->Question, "What is the average price of a hotel room in "))
            <?php $listing = json_decode($value->Listing, true); ?>

            <div class="question py-3">
              <h6 class="fs-18">
                <span>Q.{{$value->Question}}</span>
              </h6>

              <div class="mb-0">
                @if(!empty($listing))
                <?php $hotelLinks = []; ?>
                @foreach ($listing as $hotelInfo)
                @foreach ($hotelInfo as $hotelName => $details)
                <?php
                                  $escapedHotelName = preg_quote($hotelName, '/');
                                  $hotelLink = '<a href="' . route("hotel.detail", [$details]) . '">' . $hotelName . '</a>';
                                  $hotelLinks[$hotelName] = $hotelLink;
                              ?>
                @endforeach
                @endforeach

                <?php
                          // Create a link for each hotel name
                          foreach ($hotelLinks as $hotelName => $hotelLink) {
                              $value->Answer = str_replace($hotelName, $hotelLink, $value->Answer);
                          }
                      ?>

                <ul style="margin-left: 29px;">
                  <li>{!! $value->Answer !!}</li>
                </ul>
                @endif
              </div>
            </div>

            @elseif(Str::startsWith($value->Question, "What are the top cheap hotels in ") || Str::startsWith($value->Question, "What are the best hotels in $lname with outdoor pools?"))

            <div class="question py-3">
              <h6 class="fs-18">
                <span>Q.{{$value->Question}}</span>
              </h6>

              <?php $listing = json_decode($value->Listing, true); ?>
              @if(!empty($listing))
              <?php $hotelLinks = []; 
                    
                      $hotelLinksNb = []; // Correct variable name
                      ?>
              @foreach ($listing as $index => $item)

              <?php $hotelName = $item['name'];
                            $details = $item['url']; 
                            $nb_name = $item['nb_name'];
                            $nburl = $item['nburl']; 
                            $nburlParts = explode('-', $nburl);
                            $lid = $nburlParts[0]; 
                            $neiborhoodid = $nburlParts[1]; 
                            $slug = implode('-', array_slice($nburlParts, 2));

                    
                                  $escapedHotelName = preg_quote($hotelName, '/');
                                  $hotelLink = '<a href="' . route("hotel.detail", [$details]) . '">' . $hotelName . '</a>';
                                  $hotelLinks[$hotelName] = $hotelLink;

                                  $escapedHotelName1 = preg_quote($nb_name, '/');
                                  $hotelLinkNb = '<a href="' . route("hotel_neibor_list", [$lid,$neiborhoodid, $slug]) . '">' . $nb_name . '</a>';
                                  $hotelLinksNb[$nb_name] = $hotelLinkNb; // Correct variable name
                              
                              ?>

              @endforeach

              <?php
                          // Create a link for each hotel name
                          foreach ($hotelLinks as $hotelName => $hotelLink) {
                              $value->Answer = str_replace($hotelName, $hotelLink, $value->Answer);
                          }


                      

                        foreach ($hotelLinksNb as $nb_name => $nburl) {
                          $value->Answer = str_replace($nb_name, $nburl, $value->Answer);
                        }
                      ?>

              <div class="mb-0">
                <div>
                  A.{!! $value->Answer !!}


                </div>
              </div>

              @endif

                @elseif(Str::startsWith($value->Question, "Best Hotel in ") )

            <div class="question py-3">
              <h6 class="fs-18">
                <span>Q.{{$value->Question}}</span>
              </h6>

              <?php $listing = json_decode($value->Listing, true); ?>
              @if(!empty($listing))
              <?php $hotelLinks = [];                     
                    $hotelLinksNb = []; // Correct variable name
                      ?>
              @foreach ($listing as $index => $item)

              <?php $hotelName = $item['name'];
                            $details = $item['url']; 
                            $nb_name = $item['nb_name'];
                            $nburl = $item['nburl']; 
                            $nburlParts = explode('-', $nburl);
                            $lid = $nburlParts[0]; 
                            $neiborhoodid = $nburlParts[1]; 
                            $slug = implode('-', array_slice($nburlParts, 2));

                    
                                  $escapedHotelName = preg_quote($hotelName, '/');
                                  $hotelLink = '<a href="' . route("hotel.detail", [$details]) . '">' . $hotelName . '</a>';
                                  $hotelLinks[$hotelName] = $hotelLink;

                                  $escapedHotelName1 = preg_quote($nb_name, '/');
                                  $hotelLinkNb = '<a href="' . route("hotel_neibor_list", [$lid,$neiborhoodid, $slug]) . '">' . $nb_name . '</a>';
                                  $hotelLinksNb[$nb_name] = $hotelLinkNb; // Correct variable name
                              
                              ?>

              @endforeach

              <?php
                          // Create a link for each hotel name
                          foreach ($hotelLinks as $hotelName => $hotelLink) {
                              $value->Answer = str_replace($hotelName, $hotelLink, $value->Answer);
                          }


                      

                        foreach ($hotelLinksNb as $nb_name => $nburl) {
                          $value->Answer = str_replace($nb_name, $nburl, $value->Answer);
                        }
                      ?>

              <div class="mb-0">
                <div>
                  A.{!! $value->Answer !!}


                </div>
              </div>

              @endif
        
            @else
            <?php $listing = json_decode($value->Listing, true); ?>
            <div class="question py-3">
              <h6 class="fs-18">
                <span>Q.{{$value->Question}}</span>
              </h6>

              <div class="mb-0">
                <div>
                  <p>A.{{$value->Answer}}</p>

                  @if(!empty($listing ))
                  <ul style=" margin-left: 29px;">
                    @foreach ($listing as $index => $item)
                    <?php $name = $item['name'];
                        $url = $item['url']; 
                        $desc ="";
                        if(isset($item['about'])) {   
                          $desc = $item['about'] ;
                        } 
                  ?>

                    <li>
                      <a href="{{route('hotel.detail',[$url])}}" class=" fs18 ">
                        {{$name}}</a> @if($desc != "")- {{$desc}} @endif @if($index < count($listing)-1)<span
                        class="d-none">,</span>@endif

                    </li>
                    @endforeach
                  </ul>
                  @endif
                </div>
              </div>

              <!-- <a href="#" class="text-dark">See all nearby attractions.</a> -->
            </div>
            @endif
            <!-- <a href="#" class="text-dark">See all nearby attractions.</a> -->
          
          @endforeach


          @endif
          </div>