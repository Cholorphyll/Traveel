 <div class="tr-galleries-section" id="galleryOutdoor">
         <h3>Indoor and Outdoor</h3>
         <!-- start -->
        <div class="tr-gallery-category">
          <div class="tr-galleries-left-column">
            <ul>
             @if($hotelid !="")
            <?php $hoteid = $hotelid; 
   $imgcount = count($images[$hoteid]);

    ?>
            <ul>
              @if(isset($images[$hoteid][0]) && $images[$hoteid][0] != "")
              <li data-bs-toggle="modal" data-bs-target="#gallerySliderModal">
                <img 
                  src="https://photo.hotellook.com/image_v2/limit/{{ $images[$hoteid][0] }}/350/250.auto"
                  alt="Outdoor Pictures 1">
              </li>
              @endif
              @php
              $remainingImages = array_slice($images[$hoteid], 1);
              @endphp

              @for ($i = 0; $i <$imgcount; $i++) @if ($i < count($remainingImages)) @php $image=$remainingImages[$i];
                @endphp @if(!empty($image)) <li data-bs-toggle="modal" data-bs-target="#gallerySliderModal">
                <img  src="https://photo.hotellook.com/image_v2/limit/{{ $image }}/350/250.auto"
                  alt="Outdoor Pictures 2">
                </li>
                @endif
                @endif
                @endfor
            </ul>
            @endif

            </ul>
          </div>
       
        </div>

      <!-- end -->
      </div>