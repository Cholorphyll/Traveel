<?php
    $i = 1;
    $j = 0;
    $markers = [];
?>

<?php
$i = 1;
$j = 0;
?>
            <?php if(!empty($searchresults)): ?>


            <!-- Now display all non-must-see items in tr-common-listing div -->
            <div class="tr-commo n-listing">
                <?php $__currentLoopData = $searchresults; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // Determine item type
                    $itemType = 'attraction';
                    if (isset($item->SightId)) {
                        if (strpos($item->SightId, 'rest_') === 0) {
                            $itemType = 'restaurant';
                        } elseif (strpos($item->SightId, 'exp_') === 0) {
                            $itemType = 'experience';
                        }
                    }
                ?>
                
                <?php if($itemType == 'attraction'): ?>
                    <div class="card__Container" onmouseover="highlightMarker(this)" onmouseout="unhighlightMarker(this)" data-sid="<?php echo e($item->SightId); ?>" data-type="attraction">
                        <div class="card__Box">
                			<div class="card__title">
                                <h6><?php echo e(ucfirst($itemType)); ?></h6>
                            </div>
						<?php
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
                        ?>
						      <div class="card__Subtitle">
                     
                            <h5>
                                <a href="<?php echo e(asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug))); ?>" target="_blank"><?php echo e($item->Title); ?></a>                        
                            </h5>
						                                    <button class="bookmarkbtn" onclick="toggleBookmark(this)">
  <img src="https://www.travell.co/explore/images/icons/plusnew.svg" alt="" fetchpriority="high">
</button>
                        </div>
											 	    <div class="card__listSection">
                      <div class="card__details">
                        <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" fetchpriority="high"></span>
                        <span><?php echo e($item->Averagerating ?? '--'); ?>%</span>
                      </div>
                      <?php if(!empty($item->timing)): ?>
                        <?php
                          $currentDay = strtolower(date('D'));
                          $timingDisplayed = false;
                        ?>
                        <?php $__currentLoopData = $item->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($tm->day == $currentDay && !$timingDisplayed): ?>
                            <div class="card__details">
                              <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" fetchpriority="high"></span>
                              <span>Open Until <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></span>
                            </div>
                            <?php $timingDisplayed = true; ?>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                      <div class="card__details">
                        <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" fetchpriority="high"></span>
                        <span>
                        <?php
                            // Get the location name based on the item's specific data
                            $locationName = null;
                            $locationDistance = null;
                            $locationCoordinates = null;
                            
                            // First priority: Get neighborhood name by slugid
                            if (isset($item->slugid)) {
                                $neighborhood = \App\Models\Neighborhood::where('slug', $item->slugid)->first();
                                if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                    $locationName = $neighborhood->Name;
                                    $locationCoordinates = [
                                        'lat' => $neighborhood->Latitude,
                                        'lng' => $neighborhood->Longitude
                                    ];
                                }
                            }
                            
                            // Second priority: Use cityName from controller
                            if (empty($locationName) && isset($cityName) && !empty($cityName)) {
                                $locationName = $cityName;
                            }
                            
                            // Third priority: Get neighborhood by LocationId
                            if (empty($locationCoordinates) && isset($item->LocationId)) {
                                $neighborhood = \App\Models\Neighborhood::where('LocationId', $item->LocationId)->first();
                                if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                    if (empty($locationName)) {
                                        $locationName = $neighborhood->Name;
                                    }
                                    $locationCoordinates = [
                                        'lat' => $neighborhood->Latitude,
                                        'lng' => $neighborhood->Longitude
                                    ];
                                }
                            }
                            
                            // Fourth priority: Use item's City or Area
                            if (empty($locationName) && isset($item->City) && !empty($item->City)) {
                                $locationName = $item->City;
                            } elseif (empty($locationName) && isset($item->Area) && !empty($item->Area)) {
                                $locationName = $item->Area;
                            }
                            
                            // Last resort: Use location.Name
                            if (empty($locationName) && isset($location) && isset($location->Name)) {
                                $locationName = $location->Name;
                                if (empty($locationCoordinates) && isset($location->loc_latitude) && isset($location->loc_longitude)) {
                                    $locationCoordinates = [
                                        'lat' => $location->loc_latitude,
                                        'lng' => $location->loc_longitude
                                    ];
                                }
                            }
                            
                            // Calculate distance if we have coordinates for both the item and location
                            if (!empty($locationCoordinates) && isset($item->Latitude) && isset($item->Longitude)) {
                                // Haversine formula to calculate distance
                                $earthRadius = 6371; // Earth's radius in kilometers
                                $latFrom = deg2rad((float)$item->Latitude);
                                $lngFrom = deg2rad((float)$item->Longitude);
                                $latTo = deg2rad((float)$locationCoordinates['lat']);
                                $lngTo = deg2rad((float)$locationCoordinates['lng']);
                                
                                $latDelta = $latTo - $latFrom;
                                $lngDelta = $lngTo - $lngFrom;
                                
                                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
                                $distance = $angle * $earthRadius;
                                
                                // Format the distance
                                $locationDistance = round($distance, 1) . ' km from ' . $locationName;
                                echo $locationDistance;
                            } else {
                                echo $locationName;
                            }
                        ?>
                        </span>
                      </div>
                    </div>
                        <div class="card__gallerySection <?php echo e($isSightImageVideo ? 'video-media' : 'image-media'); ?>">
					                <div class="card__thumb" fetchpriority="high">
                            <a href="<?php echo e(asset('at-'.$item->slugid.'-'.$item->SightId.'-'.strtolower($item->Slug))); ?>" target="_blank">
                                <?php if($isSightImageVideo): ?>
                                    <video class="carousel-video w-100 h-100  round"
                                        autoplay loop playsinline muted
                                        onplay="hideCarouselControls(<?php echo e($loop->index); ?>)" 
                                        onpause="showCarouselControls(<?php echo e($loop->index); ?>)">
                                        <source src="<?php echo e($mediaUrl); ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php else: ?>
                                    <img class="round"
                                        src="<?php echo e($mediaUrl); ?>"
                                        srcset="
                                            <?php echo e(str_replace('.jpg', '_small.jpg', $mediaUrl)); ?> 320w,
                                            <?php echo e(str_replace('.jpg', '_medium.jpg', $mediaUrl)); ?> 640w,
                                            <?php echo e($mediaUrl); ?> 1280w
                                        "
                                        sizes="(max-width: 768px) 100vw, 50vw"
                                        alt="<?php echo e($item->Title); ?>"
                                        fetchpriority="high"
                                    >
                                <?php endif; ?>
                            </a>
							          </div>
                        </div>
				
						          </div>
                    </div>
                <?php elseif($itemType == 'restaurant'): ?>
                    <div class="card__Container" onmouseover="highlightRestaurantMarker(this)" onmouseout="unhighlightRestaurantMarker(this)"
                      data-restaurant-id="<?php echo e($item->RestaurantId ?? $item->SightId); ?>" data-type="restaurant">

                      <div class="card__Box">
					  		<div class="card__title">
                                <h6><?php echo e(ucfirst($itemType)); ?></h6>
                            </div>
					  	      <div class="card__Subtitle">
                     
                            <h5>
                               <a href="<?php echo e(url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug)); ?>" target="_blank"><?php echo e($item->Title); ?></a>                       
                            </h5>
						                                                  <button class="bookmarkbtn" onclick="toggleBookmark(this)">
  <img src="https://www.travell.co/explore/images/icons/plusnew.svg" alt="" fetchpriority="high">
</button>
                        </div>
								    <div class="card__listSection">
                      <div class="card__details">
                        <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" fetchpriority="high"></span>
                        <span><?php echo e($item->Averagerating ?? '--'); ?>%</span>
                      </div>
                      <?php if(!empty($item->timing)): ?>
                        <?php
                          $currentDay = strtolower(date('D'));
                          $timingDisplayed = false;
                        ?>
                        <?php $__currentLoopData = $item->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($tm->day == $currentDay && !$timingDisplayed): ?>
                            <div class="card__details">
                              <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" fetchpriority="high"></span>
                              <span>Open Until <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></span>
                            </div>
                            <?php $timingDisplayed = true; ?>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                      <div class="card__details">
                        <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" fetchpriority="high"></span>
                        <span>
                        <?php
                            // Get the location name based on the item's specific data
                            $locationName = null;
                            $locationDistance = null;
                            $locationCoordinates = null;
                            
                            // First priority: Get neighborhood name by slugid
                            if (isset($item->slugid)) {
                                $neighborhood = \App\Models\Neighborhood::where('slug', $item->slugid)->first();
                                if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                    $locationName = $neighborhood->Name;
                                    $locationCoordinates = [
                                        'lat' => $neighborhood->Latitude,
                                        'lng' => $neighborhood->Longitude
                                    ];
                                }
                            }
                            
                            // Second priority: Use cityName from controller
                            if (empty($locationName) && isset($cityName) && !empty($cityName)) {
                                $locationName = $cityName;
                            }
                            
                            // Third priority: Get neighborhood by LocationId
                            if (empty($locationCoordinates) && isset($item->LocationId)) {
                                $neighborhood = \App\Models\Neighborhood::where('LocationId', $item->LocationId)->first();
                                if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                    if (empty($locationName)) {
                                        $locationName = $neighborhood->Name;
                                    }
                                    $locationCoordinates = [
                                        'lat' => $neighborhood->Latitude,
                                        'lng' => $neighborhood->Longitude
                                    ];
                                }
                            }
                            
                            // Fourth priority: Use item's City or Area
                            if (empty($locationName) && isset($item->City) && !empty($item->City)) {
                                $locationName = $item->City;
                            } elseif (empty($locationName) && isset($item->Area) && !empty($item->Area)) {
                                $locationName = $item->Area;
                            }
                            
                            // Last resort: Use location.Name
                            if (empty($locationName) && isset($location) && isset($location->Name)) {
                                $locationName = $location->Name;
                                if (empty($locationCoordinates) && isset($location->loc_latitude) && isset($location->loc_longitude)) {
                                    $locationCoordinates = [
                                        'lat' => $location->loc_latitude,
                                        'lng' => $location->loc_longitude
                                    ];
                                }
                            }
                            
                            // Calculate distance if we have coordinates for both the item and location
                            if (!empty($locationCoordinates) && isset($item->Latitude) && isset($item->Longitude)) {
                                // Haversine formula to calculate distance
                                $earthRadius = 6371; // Earth's radius in kilometers
                                $latFrom = deg2rad((float)$item->Latitude);
                                $lngFrom = deg2rad((float)$item->Longitude);
                                $latTo = deg2rad((float)$locationCoordinates['lat']);
                                $lngTo = deg2rad((float)$locationCoordinates['lng']);
                                
                                $latDelta = $latTo - $latFrom;
                                $lngDelta = $lngTo - $lngFrom;
                                
                                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
                                $distance = $angle * $earthRadius;
                                
                                // Format the distance
                                $locationDistance = round($distance, 1) . ' km from ' . $locationName;
                                echo $locationDistance;
                            } else {
                                echo $locationName;
                            }
                        ?>
                        </span>
                      </div>
                    </div>
					  
					    <div class="card__gallerySection">
						<div class="card__thumb">
					  
                      <a href="<?php echo e(url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug)); ?>" target="_blank">
                            <img
                                src="<?php echo e(asset('/images/Group 1171275916.png')); ?>"
                                srcset="
                                    <?php echo e(asset('/images/Group 1171275916_small.png')); ?> 320w,
                                    <?php echo e(asset('/images/Group 1171275916_medium.png')); ?> 640w,
                                    <?php echo e(asset('/images/Group 1171275916.png')); ?> 1280w
                                "
                                sizes="(max-width: 768px) 100vw, 50vw"
                                alt="restaurant image"
                                fetchpriority="high"
                            >
                      </a>
					  
					  </div>
					  </div>
					  
                      </div>
     
                    </div>
                <?php elseif($itemType == 'experience'): ?>
                    <div class="card__Container" onmouseover="highlightExperienceMarker(this)" onmouseout="unhighlightExperienceMarker(this)"
                       data-experience-id="<?php echo e($item->SightId ?? ''); ?>" data-type="experience">
					    <div class="card__Box">
                        	<div class="card__title">
                                <h6><?php echo e(ucfirst($itemType)); ?></h6>
                            </div>
						    <div class="card__Subtitle">
                     
                            <h5>
                               <a href="<?php echo e(url('/rd-'.$item->slugid.'-'.preg_replace('/[^0-9]/', '', $item->SightId).'-'.$item->Slug)); ?>" target="_blank"><?php echo e($item->Title); ?></a>                       
                            </h5>
						                                    <button class="bookmarkbtn" onclick="toggleBookmark(this)">
  <img src="https://www.travell.co/explore/images/icons/plusnew.svg" alt="" fetchpriority="high">
</button>
                        </div>
					    <div class="card__listSection">
                      <div class="card__details">
                        <span><img src="<?php echo e(asset('explore/images/icons/heart.svg')); ?>" alt="" fetchpriority="high"></span>
                        <span><?php echo e($item->Averagerating ?? '--'); ?>%</span>
                      </div>
                      <?php if(!empty($item->timing)): ?>
                        <?php
                          $currentDay = strtolower(date('D'));
                          $timingDisplayed = false;
                        ?>
                        <?php $__currentLoopData = $item->timing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <?php if($tm->day == $currentDay && !$timingDisplayed): ?>
                            <div class="card__details">
                              <span><img src="<?php echo e(asset('explore/images/icons/clock.svg')); ?>" alt="" fetchpriority="high"></span>
                              <span>Open Until <?php echo e(date('g:i A', strtotime($tm->endTime))); ?></span>
                            </div>
                            <?php $timingDisplayed = true; ?>
                          <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      <?php endif; ?>
                      <div class="card__details">
                        <span><img src="<?php echo e(asset('explore/images/icons/location.svg')); ?>" alt="" fetchpriority="high"></span>
                        <span>
                        <?php
                            // Get the location name based on the item's specific data
                            $locationName = null;
                            $locationDistance = null;
                            $locationCoordinates = null;
                            
                            // First priority: Get neighborhood name by slugid
                            if (isset($item->slugid)) {
                                $neighborhood = \App\Models\Neighborhood::where('slug', $item->slugid)->first();
                                if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                    $locationName = $neighborhood->Name;
                                    $locationCoordinates = [
                                        'lat' => $neighborhood->Latitude,
                                        'lng' => $neighborhood->Longitude
                                    ];
                                }
                            }
                            
                            // Second priority: Use cityName from controller
                            if (empty($locationName) && isset($cityName) && !empty($cityName)) {
                                $locationName = $cityName;
                            }
                            
                            // Third priority: Get neighborhood by LocationId
                            if (empty($locationCoordinates) && isset($item->LocationId)) {
                                $neighborhood = \App\Models\Neighborhood::where('LocationId', $item->LocationId)->first();
                                if ($neighborhood && !empty($neighborhood->Latitude) && !empty($neighborhood->Longitude)) {
                                    if (empty($locationName)) {
                                        $locationName = $neighborhood->Name;
                                    }
                                    $locationCoordinates = [
                                        'lat' => $neighborhood->Latitude,
                                        'lng' => $neighborhood->Longitude
                                    ];
                                }
                            }
                            
                            // Fourth priority: Use item's City or Area
                            if (empty($locationName) && isset($item->City) && !empty($item->City)) {
                                $locationName = $item->City;
                            } elseif (empty($locationName) && isset($item->Area) && !empty($item->Area)) {
                                $locationName = $item->Area;
                            }
                            
                            // Last resort: Use location.Name
                            if (empty($locationName) && isset($location) && isset($location->Name)) {
                                $locationName = $location->Name;
                                if (empty($locationCoordinates) && isset($location->loc_latitude) && isset($location->loc_longitude)) {
                                    $locationCoordinates = [
                                        'lat' => $location->loc_latitude,
                                        'lng' => $location->loc_longitude
                                    ];
                                }
                            }
                            
                            // Calculate distance if we have coordinates for both the item and location
                            if (!empty($locationCoordinates) && isset($item->Latitude) && isset($item->Longitude)) {
                                // Haversine formula to calculate distance
                                $earthRadius = 6371; // Earth's radius in kilometers
                                $latFrom = deg2rad((float)$item->Latitude);
                                $lngFrom = deg2rad((float)$item->Longitude);
                                $latTo = deg2rad((float)$locationCoordinates['lat']);
                                $lngTo = deg2rad((float)$locationCoordinates['lng']);
                                
                                $latDelta = $latTo - $latFrom;
                                $lngDelta = $lngTo - $lngFrom;
                                
                                $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lngDelta / 2), 2)));
                                $distance = $angle * $earthRadius;
                                
                                // Format the distance
                                $locationDistance = round($distance, 1) . ' km from ' . $locationName;
                                echo $locationDistance;
                            } else {
                                echo $locationName;
                            }
                        ?>
                        </span>
                      </div>
                    </div>
						
                        <div class="card__gallerySection">
						<div class="card__thumb">
                        <a href="<?php if(!empty($item->viator_url)): ?> <?php echo e($item->viator_url); ?> <?php else: ?> <?php echo e(route('experince',[$item->slugid.'-'.str_replace('exp_', '', $item->SightId).'-'.$item->Slug])); ?> <?php endif; ?>" target="_blank">
                          <?php if(!empty($item->Img1)): ?>
                                <img
                                    src="<?php echo e($item->Img1); ?>"
                                    srcset="
                                        <?php echo e(str_replace('.jpg', '_small.jpg', $item->Img1)); ?> 320w,
                                        <?php echo e(str_replace('.jpg', '_medium.jpg', $item->Img1)); ?> 640w,
                                        <?php echo e($item->Img1); ?> 1280w
                                    "
                                    sizes="(max-width: 768px) 100vw, 50vw"
                                    alt="Experience Image"
                                    fetchpriority="high"
                                >
                          <?php else: ?>
                            <img loading="lazy" src="<?php echo e(asset('/images/Hotel lobby.svg')); ?>" alt="Experience Image" fetchpriority="high">
                          <?php endif; ?>
                        </a>
						</div>
                      </div>
              
					  </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php endif; ?>
              <input type="hidden" id="shown-attraction-ids" value="<?php echo e(implode(',', $searchresults->pluck('SightId')->toArray())); ?>">
            </div>
<?php /**PATH C:\wamp64\www\tavelll\resources\views/getloclistbycatid.blade.php ENDPATH**/ ?>