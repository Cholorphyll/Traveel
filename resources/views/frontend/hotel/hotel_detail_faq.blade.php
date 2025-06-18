@if(!$faq->isEmpty())
@foreach($faq as $faqs)

@if(!empty($faqs->Listing))
<div class="tr-faqs-ques-ans">

  <div class="tr-faqs-ques">{{$faqs->Question}}</div>

  <div class="d-flex align-items-start">

    <div>

      <div class="tr-faqs-ans">{{$faqs->Answer}}</div>
      <?php $listing = json_decode($faqs->Listing, true); ?>
      @if(!empty($listing ))
      <ol style=" margin-left: 29px;">
        @foreach ($listing as $index => $item)
        <?php $name = $item['name']; $url = $item['url']; ?>
        <li>
          <a href="{{route('sight.details',[$url])}}" class=" fs18 " style=" font-size: 15px;">
            {{$name}}</a>@if($index < count($listing)-1)<span class="d-none">,
            </span>@endif
        </li>
        @endforeach
      </ol>
      @endif

    </div>
  </div>


</div>
@else
<div class="tr-faqs-ques-ans">
  <div class="tr-faqs-ques">{{$faqs->Question}}</div>
  <div class="tr-faqs-ans">{{$faqs->Answer}}</div>
</div>

@endif
@endforeach

@else
<p>No Faq found.</p>
@endif