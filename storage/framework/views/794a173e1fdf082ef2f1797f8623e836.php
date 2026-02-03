 <?php $__currentLoopData = $faq; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $faq): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
 <?php $timing = json_decode($faq->timing, true); ?>
    <div class="question py-3">
      <h6 class="fs-18"><?php echo e($faq->Faquestion); ?>?</h6>
      <p class="mb-0"><?php echo e($faq->Answer); ?></p>
      <?php if(!empty($timing)): ?>
                <ul>
                    <?php $__currentLoopData = $timing['time']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $day => $times): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                       
                        <li><?php echo e($day .'-'.$times['start'] .'-'. $times['end']); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
      <a href="#" class="text-dark">See all nearby attractions.</a>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>  <?php /**PATH C:\wamp64\www\tavelll\resources\views/add_explorefaq.blade.php ENDPATH**/ ?>