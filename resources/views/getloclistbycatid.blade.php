@php
    $i = 1;
    $j = 0;
    $markers = [];
@endphp

<?php $i = 1; $j = 0; ?>
            @if(!empty($searchresults))


            <!-- Now display all non-must-see items in tr-common-listing div -->
            <div class="tr-common-listing">
                @foreach($searchresults as $item)
                @php
                    // Determine item type
                    $itemType = 'attraction';
                    if (isset($item->SightId)) {
                        if (strpos($item->SightId, 'rest_') === 0) {
                            $itemType = 'restaurant';
                        } elseif (strpos($item->SightId, 'exp_') === 0) {
                            $itemType = 'experience';
                        }
                    }
                @endphp
                
                @if($itemType == 'attraction')
                    <div class="tr-list" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="{{$item->SightId}}" data-type="attraction">
                        @php
                            $attractionHasVideo = false;
                            $mediaUrl = null; // Will store video or image URL
                            $isSightImageVideo = false; // Flag to indicate if the media is a video

                            if (isset($sightImages) && !$sightImages->isEmpty()) {
                                // First, check for a video
                                foreach ($sightImages as $sImage) {
                                    if ($sImage->Sightid == $item->SightId && str_contains($sImage->Image, 'vid')) {
                                        $mediaUrl = "https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/" . $sImage->Image;
                                        $attractionHasVideo = true;
                                        $isSightImageVideo = true;
                                        break; 
                                    }
                                }
                                // If no video was found, look for an image
                                if (!$attractionHasVideo) {
                                    foreach ($sightImages as $sImage) {
                                        if ($sImage->Sightid == $item->SightId && !str_contains($sImage->Image, 'vid')) { // Ensure it's not a video path
                                            $mediaUrl = "https://image-resize-5q14d76mz-cholorphylls-projects.vercel.app/api/resize?url=https://s3-us-west-2.amazonaws.com/s3-travell/Sight-images/" . $sImage->Image . "&width=920";
                                            break;
                                        }
                                    }
                                }
                            }
                            
                            // Fallback if no specific media found from $sightImages
                            if (!$mediaUrl) {
                                $mediaUrl = asset('/images/Hotel lobby.svg');
                                $isSightImageVideo = false; // It's definitely an image now
                            }
                        @endphp

                        <div class="image-container {{ $isSightImageVideo ? 'video-media' : 'image-media' }}">
                            <a href="{{ asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug)) }}" target="_blank">
                                @if($isSightImageVideo)
                                    <video class="carousel-video w-100 h-100"
                                        autoplay loop playsinline muted
                                        onplay="hideCarouselControls({{ $loop->index }})" 
                                        onpause="showCarouselControls({{ $loop->index }})">
                                        <source src="{{ $mediaUrl }}" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                @else
                                    <img loading="lazy" src="{{ $mediaUrl }}" alt="{{ $item->Title }}">
                                @endif
                            </a>
                        </div>
                        <div class="tr-list-details">
                            <div class="tr-like-review">
                                <div class="tr-heart">
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="12" height="12" rx="6" fill="#28965A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99782 3.90601C5.36052 3.19235 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99782 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99782 3.90601Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </div>
                                <div class="tr-ranting-percent">
                                    @if(isset($item->Averagerating))
                                        {{$item->Averagerating}}%
                                    @else
                                        --%
                                    @endif
                                </div>
                            </div>
                            <h3 class="tr-title-name">
                                <a href="{{ asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug)) }}" target="_blank">{{$item->Title}}</a>                        
                            </h3>
                        </div>
                    </div>
                @elseif($itemType == 'restaurant')
                    <div class="tr-list restaurant" onmouseover="highlightRestaurantMarker(this)" onmouseout="unhighlightRestaurantMarker(this)"
                      data-restaurant-id="{{$item->RestaurantId ?? $item->SightId}}" data-type="restaurant">
                      <div class="image-container image-media">
                      <a href="{{ url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug) }}" target="_blank">
                        <img loading="lazy" src="{{ asset('/images/Group 1171275916.png') }}" alt="restaurant image">
                      </a>
                      </div>
                      <div class="tr-list-details">
                        <div class="tr-like-review">
                          <div class="tr-heart">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="12" height="12" rx="6" fill="#28965A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99782 3.90601C5.36052 3.19235 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99782 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99782 3.90601Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                          </div>
                          <div class="tr-ranting-percent">
                            @if(isset($item->Averagerating))
                              {{$item->Averagerating}}%
                            @else
                              --%
                            @endif
                          </div>
                        </div>
                        <h3> <a href="{{ url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug) }}" target="_blank">{{$item->Title}}</a>     
                        </h3>
                      </div>
                    </div>
                @elseif($itemType == 'experience')
                    <div class="tr-list experience" onmouseover="highlightExperienceMarker(this)" onmouseout="unhighlightExperienceMarker(this)"
                       data-experience-id="{{$item->SightId ?? ''}}" data-type="experience">
                      <div class="image-container image-media">
                        <a href="@if(!empty($item->viator_url)) {{$item->viator_url}} @else {{route('experince',[$item->slugid.'-'.str_replace('exp_', '', $item->SightId).'-'.$item->Slug])}} @endif" target="_blank">
                          @if(!empty($item->Img1))
                            <img loading="lazy" src="{{$item->Img1}}" alt="Experience Image">
                          @else
                            <img loading="lazy" src="{{ asset('/images/Hotel lobby.svg') }}" alt="Experience Image">
                          @endif
                        </a>
                      </div>
                      <div class="tr-list-details">
                        <div class="tr-like-review">
                          <div class="tr-heart">
                            <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="12" height="12" rx="6" fill="#28965A"/><path fill-rule="evenodd" clip-rule="evenodd" d="M5.99782 3.90601C5.36052 3.19235 4.29779 3.00038 3.49931 3.65387C2.70082 4.30737 2.58841 5.39997 3.21546 6.17285L5.99782 8.7496L8.78017 6.17285C9.40723 5.39997 9.30853 4.30049 8.49632 3.65387C7.68411 3.00726 6.63511 3.19235 5.99782 3.90601Z" fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"/></svg>
                          </div>
                          <div class="tr-ranting-percent">
                            @if(isset($item->Averagerating))
                              {{$item->Averagerating}}%
                            @else
                              --%
                            @endif
                          </div>
                        </div>
                         <h3><a href="@if(!empty($item->viator_url)) {{$item->viator_url}} @else {{route('experince',[$item->slugid.'-'.str_replace('exp_', '', $item->SightId).'-'.$item->Slug])}} @endif" target="_blank">{{$item->Title ?? $item->Name}}</a>
                        </h3>
                      </div>
                    </div>
                @endif
                @endforeach
            </div>
            @endif
    <input type="hidden" id="shown-attraction-ids" value="{{ implode(',', $searchresults->pluck('SightId')->toArray()) }}">
                </div>