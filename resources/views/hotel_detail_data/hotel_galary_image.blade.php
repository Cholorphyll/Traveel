 @if($hotelid !="")
              <?php $hoteid =$hotelid ;?>


              <div class="tr-main-image">
                @if(isset($images[$hoteid][0] ) && $images[$hoteid][0] !="")
                <img loading="lazy"
                  src="https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/628/567.auto" alt="Room Image" style="height: 400px;">
                @else
                <img src="{{asset('images/Hotel lobby.svg')}}" alt="">
                @endif
              </div>
              <div class="tr-thumb-images">
                <ul>
                  @php
                  $remainingImages = array_slice($images[$hoteid], 1);
                  @endphp

                  @for ($i = 0; $i < 6; $i++)
                   @if ($i < count($remainingImages)) @php $image=$remainingImages[$i];
                    @endphp <li>
                    @if(!empty($image))
                    <img src="https://photo.hotellook.com/image_v2/limit/{{ $image }}/312/234.auto" alt="">
                    @else
                    <img src="{{ asset('images/Hotel lobby.svg') }}" alt="">
                    @endif
                    </li>

                    @else
                    <li>
                      <img loading="lazy" src="{{ asset('images/Hotel lobby.svg') }}" alt="Room Image">
                    </li>
                    @endif
                    @endfor
                </ul>
                <a href="javascript:void(0);" class="tr-show-all-photos">Show all photos</a>
              </div>

              @endif