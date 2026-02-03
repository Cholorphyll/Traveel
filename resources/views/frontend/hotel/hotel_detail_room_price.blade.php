@if(!$TPRoomtype->isEmpty()) <p>Room Types</p> @endif
          <div class="row">
            <?php 
      
                if(!$TPRoomtype->isEmpty()){
                      $Roomdesc = $TPRoomtype[0]->Roomdesc;
                      $Roomdesc = json_decode($Roomdesc);
                }

                if(!empty($searchresult)){
                      $hotelid =  $searchresult[0]->hotelid; 
                      $photoCount =  $searchresult[0]->photoCount;
                }
              ?>

            @if(!$TPRoomtype->isEmpty())

            @foreach ($Roomdesc as $key=>$value)
            <div class="col-md-6">
              <div class="row mb-3">
                <div class="col-6">

                  <?php
                        $matchedRoom = collect($getroomtype)->first(function ($room) use ($key) {
                            return stripos($key, $room->type) !== false;
                        });

                        $matchedImageId = $matchedRoom ? $matchedRoom->imageid : 0;
                        ?>

                  @if($matchedImageId <= $photoCount) <img
                    src="https://photo.hotellook.com/image_v2/limit/h{{$hotelid}}_{{$matchedImageId}}/170.jpg" alt="">
                  @else
                    <img src="https://photo.hotellook.com/image_v2/limit/h{{$hotelid}}_{{$rtp->imageid}}/170.jpg"
                      alt="">
                  @endif


                </div>

                <div class="col-6">
                  <h6 class="mb-3"><b>{{ $key }}</b></h6>

                  @foreach($value as $key=>$val)
                  @if($key == 'breakfast' && $val != '')
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Breakfast included
                    </div>
                  </div>
                  @endif
                  @if($key == 'Free Cancellation' && $val != '')
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Free Cancellation
                    </div>
                  </div>
                  @endif
                  @if($key == 'freeWifi' && $val != '')
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Free Wifi </div>
                  </div>
                  @endif
                  @if (isset($val) && $key == 'beds' && is_object($val))
                  @foreach ($val as $bedType => $bedCount)
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14">
                      {{ $bedCount }} {{ ucfirst($bedType) }} @if($bedCount == 1) Bed @elseif($bedCount == 0) @else beds
                      @endif
                    </div>
                  </div>
                  @endforeach
                  @endif
                  @if($key == 'refundable' && $val != '')
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Refundable</div>
                  </div> @endif

                  @if($key == 'deposit' && $val != '' && $val == 1 )

                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Payment directly on the site </div>
                  </div>

                  @endif

                  @if($key == 'deposit' && $val == '' && $val != 1)

                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Reserve Now, Pay Later </div>
                  </div>

                  @endif
                  @if($key == 'viewSentence')

                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14">{{$val}}</div>
                  </div>

                  @endif
                  @if($key == 'available')
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> {{$val}} @if($val == 1) Room @else Rooms @endif Available</div>
                  </div>
                  @endif
                  @if($key == 'balcony' && $val ==1)
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Balcony </div>
                  </div>
                  @endif

                  @if($key == 'refundable' && $val ==1)
                  <div class="d-flex mb-10">
                    <img src="{{ asset('images/wifi.svg') }}" class="me-2" width="25px" alt="">
                    <div class="fs14"> Card Required </div>
                  </div>
                  @endif

                  @endforeach

                </div>

                @if($rooms['desc'] == $key)

          <img src="{{ 'http://pics.avs.io/hl_gates/100/100/' . $rooms['agencyId'] . '.png' }}" alt="Agency Logo">
          <button class="form-control btn btn-outline-secondary">
            <a href="{{ $rooms['fullBookingURL'] }}" target="_blank" style="text-decoration: none;">
              ${{ $rooms['price'] }}/night
            </a>
          </button>
          @endif

          
              </div>
            </div>
            @endforeach
            @else
            <p>Not available.</p>
            @endif