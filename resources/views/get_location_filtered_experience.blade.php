 
 @if(!empty($result))
 <script type="text/javascript">
  $(document).ready(function() {
     $('.tr-experience-slider').slick({
      autoplay: false,
      autoplaySpeed: 2000,
      dots: true,
      arrows: false,
      infinite: true,
      slidesToShow: 3,
      slidesToScroll: 1
    });


  });
  </script> 
        @foreach($result as $expen)
        <div class="tr-experience">
              <!-- <div class="tr-heading-with-distance">
              
                <div class="tr-distance"></div>
              </div> -->
              <div class="tr-experience-slider">
                <div class="tr-store">
                  <a href="javascript:void(0);">
                     @if($expen->Img1 !="")
                    <img src="{{$expen->Img1}}" alt="hotel image" height="185" width="155">
                    @else
                    <img src="{{asset('images/Hotel lobby.svg')}}" alt="hotel image"  height="185" width="155">
                    @endif
                  </a>
                </div>
                <div class="tr-store">
                  <a href="javascript:void(0);">
                    @if($expen->Img2 !="")
                    <img src="{{$expen->Img2}}" alt="hotel image"  height="185" width="155">
                    @else
                    <img src="{{asset('images/Hotel lobby.svg')}}" alt="hotel image"  height="185" width="155">
                    @endif
                  </a>
                </div>
                <div class="tr-store">
                  <a href="javascript:void(0);">
                    @if($expen->Img3 !="")
                    <img src="{{$expen->Img3}}" alt="hotel image"  height="185" width="155">
                    @else
                    <img src="{{asset('images/Hotel lobby.svg')}}" alt="hotel image"  height="185" width="155">
                    @endif</a>
                </div>
                <div class="tr-store">
                  <a href="javascript:void(0);">@if($expen->Img2 !="")
                    <img src="{{$expen->Img2}}" alt="hotel image"  height="185" width="155">
                    @else
                    <img src="{{asset('images/Hotel lobby.svg')}}" alt="hotel image"  height="185" width="155">
                    @endif</a>
                </div>
              </div>
              <div class="tr-details">
                <h3><a
                    href="{{route('experince',[$expen->slugid.'-'.$expen->ExperienceId.'-'.$expen->Slug])}}"
                    target="_blank">{{$expen->Name}}</a></h3>
                @if($expen->Lname !="") <div class="tr-location">{{$expen->Lname}}</div>@endif
                <div class="tr-more-inform">
                  <ul>
                    <li><span>Open</span>until 17:10</li>
                    <li>5 hours.</li>
                  </ul>
                </div>
                <div class="tr-like-review">
                  <div class="tr-heart">
                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd"
                        d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z"
                        fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                  </div>
                  <div class="tr-ranting-percent">89%</div>
                </div>
              </div>
            </div>
                
          @endforeach
		 @else
      	  match not found.
		 @endif 
 