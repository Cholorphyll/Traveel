<div class="tr-title">
    <h4>
        <svg width="24" height="20" viewBox="0 0 19 19" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- SVG path here -->
        </svg>
        Restaurants
    </h4>
    <span class="d-none d-md-block">Within 1 KM</span>
</div>
<div class="tr-tourist-place-lists">
<?php if(!$get_nearby_rest->isEmpty()): ?>

        <?php $__currentLoopData = $get_nearby_rest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="tr-tourist-place">
                <div class="tr-img">
                    <a href="<?php echo e(asset('rd-'.$val->slugid.'-'.$val->RestaurantId.'-'.strtolower($val->Slug))); ?>">
                        <img loading="lazy" src="<?php echo e(asset('images/Hotel lobby-image.png')); ?>" alt="tourist-places">
                    </a>
                </div>
                <div class="tr-details">
                    <div class="tr-place-name">
                        <a href="<?php echo e(asset('rd-'.$val->slugid.'-'.$val->RestaurantId.'-'.strtolower($val->Slug))); ?>"><?php echo e($val->Title); ?></a>
                    </div>
                    <div class="tr-like-review">
                        <?php if(!empty($val->TATrendingScore) && $val->TATrendingScore != 0): ?>
                            <span class="tr-likes">
                                <?php echo e(rtrim($val->TATrendingScore, '.0') * 10); ?>%
                            </span>
                        <?php endif; ?>
                        <!-- <span class="tr-review"> reviews</span> -->
                    </div>
                    <div class="tr-distance"><?php echo e($val->distance); ?> km</div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php for($i = $nearbyattrest; $i < 4; $i++): ?> <!-- Placeholder elements when less than 4 items are available -->
        <div class="tr-tourist-place">
          <div class="tr-img">
            <img loading="lazy" src="<?php echo e(asset('frontend/hotel-detail/images/no-data-explore.png')); ?>" alt="no-data">
          </div>
          <div class="tr-details">
            <div class="tr-no-data-text w-100 mb-12"></div>
            <div class="tr-no-data-text w-60 mb-12"></div>
            <div class="tr-no-data-text w-80 mb-12"></div>
            <div class="tr-no-data-text w-20 mb-12"></div>
          </div>
        </div>
        <?php endfor; ?>

<?php else: ?>
    <!-- Fallback layout if no data -->
            <?php for($i = 0; $i < 4; $i++): ?>
                <div class="tr-tourist-place">
                    <div class="tr-img">
                        <img loading="lazy" src="<?php echo e(asset('frontend/hotel-detail/images/no-data-explore.png')); ?>" alt="no-data">
                    </div>
                    <div class="tr-details">
                        <div class="tr-no-data-text w-100 mb-12"></div>
                        <div class="tr-no-data-text w-60 mb-12"></div>
                        <div class="tr-no-data-text w-80 mb-12"></div>
                        <div class="tr-no-data-text w-20 mb-12"></div>
                    </div>
                </div>
            <?php endfor; ?>
<?php endif; ?>
</div><?php /**PATH C:\wamp64\www\tavelll\resources\views/explore_results/nearby_restaurant.blade.php ENDPATH**/ ?>