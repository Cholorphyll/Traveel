 @foreach($faq as $faq)
 <?php $timing = json_decode($faq->timing, true); ?>
    <div class="question py-3">
      <h6 class="fs-18">{{$faq->Faquestion}}?</h6>
      <p class="mb-0">{{$faq->Answer}}</p>
      @if(!empty($timing))
                <ul>
                    @foreach($timing['time'] as $day => $times)
                       
                        <li>{{$day .'-'.$times['start'] .'-'. $times['end']}}</li>
                    @endforeach
                </ul>
            @endif
      <a href="#" class="text-dark">See all nearby attractions.</a>
    </div>
    @endforeach  