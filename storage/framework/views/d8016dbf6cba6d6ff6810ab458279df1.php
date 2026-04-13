   <div class="asked-questions py-4 border-top">

         
          <h5 class="mb-3 heading fs-26">Frequently Asked Questions about <?php echo e($lname); ?></h5>

          <?php $__currentLoopData = $faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <?php $listing = json_decode($value->listing, true); ?>
          <div class="question py-3">
            <h6 class="fs-18">
              <span>Q.<?php echo e($value->Question); ?>?</span>
            </h6>

            <div class="mb-0">
              <div>
                <p>A.<?php echo e($value->Answer); ?></p>

                <?php if(!empty($listing )): ?>
                <ul style=" margin-left: 29px;">
                  <?php $__currentLoopData = $listing; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php $name = $item['name'];
          $url = $item['url']; ?>

                  <li>
                  <a href="<?php echo e(asset('at-'.$value->slugid.'-'.$url)); ?>" target="_blank"> <?php echo e($name); ?></a><?php if($index < count($listing)-1): ?><span
                      class="d-none">,</span><?php endif; ?>
                  </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
                <?php endif; ?>
              </div>
            </div>

        
            <!-- <a href="#" class="text-dark">See all nearby attractions.</a> -->
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      <?php /**PATH C:\wamp64\www\tavelll\resources\views/get_faq_data.blade.php ENDPATH**/ ?>