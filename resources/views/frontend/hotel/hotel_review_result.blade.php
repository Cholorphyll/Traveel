@if(!$review->isEmpty())
@foreach($review as $reviews)
<div class="tr-customer-review">
  <div class="tr-customer-details">
    <div class="tr-customer-detail">
      <img loading="lazy" src="{{asset('frontend/hotel-detail/images/customer.png')}}" alt="customer">
      <div class="tr-customer-name">{{$reviews->Name}}</div>
      <div class="tr-hotel-place">London</div>
    </div>
    <div class="tr-report">
      <div class="tr-report-icon"></div>
      <div class="tr-report-popup">
        <h5>Report this</h5>
        <div class="tr-follow">Follow</div>
      </div>
    </div>
  </div>
  <div class="tr-ratings">
    <span>
    @for ($i = 0; $i < 5; $i++)
     @if($i < $reviews->Rating )
    <span><i class="fa fa-star" aria-hidden="true"></i></span>
    @else
    <span><i class="fa fa-star-o" aria-hidden="true"></i></span>
    @endif
    @endfor
      <!-- <span><i class="fa fa-star" aria-hidden="true"></i></span>
      <span><i class="fa fa-star" aria-hidden="true"></i></span>
      <span><i class="fa fa-star" aria-hidden="false"></i></span> -->
    </span>
    <span class="tr-time-ago">1 week ago</span>
  </div>
  <ul>
    <li><svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M4.19922 17.5532C4.14714 17.5532 4.0918 17.5435 4.0332 17.5239C3.97461 17.5044 3.92578 17.4686 3.88672 17.4165C3.83464 17.3644 3.79883 17.3123 3.7793 17.2603C3.75977 17.2082 3.75 17.1561 3.75 17.104C3.75 17.0389 3.75977 16.9803 3.7793 16.9282C3.79883 16.8761 3.83464 16.8241 3.88672 16.772C3.92578 16.7329 3.97461 16.7004 4.0332 16.6743C4.0918 16.6483 4.14714 16.6353 4.19922 16.6353H5.37109V4.93604C5.37109 4.84489 5.39062 4.757 5.42969 4.67236C5.46875 4.58773 5.52083 4.51286 5.58594 4.44775C5.65104 4.36963 5.72591 4.31429 5.81055 4.28174C5.89518 4.24919 5.98307 4.23291 6.07422 4.23291H11.3672C11.4714 4.23291 11.5658 4.24593 11.6504 4.27197C11.735 4.29801 11.8099 4.34359 11.875 4.40869C11.9401 4.4738 11.9922 4.54541 12.0312 4.62354C12.0703 4.70166 12.0898 4.7863 12.0898 4.87744V5.03369H13.9258C14.0039 5.03369 14.0853 5.05322 14.1699 5.09229C14.2546 5.13135 14.3359 5.18343 14.4141 5.24854C14.4792 5.32666 14.5312 5.40804 14.5703 5.49268C14.6094 5.57731 14.6289 5.6652 14.6289 5.75635V16.6353H15.8008C15.8529 16.6353 15.9082 16.6483 15.9668 16.6743C16.0254 16.7004 16.0742 16.7394 16.1133 16.7915C16.1654 16.8436 16.2012 16.8957 16.2207 16.9478C16.2402 16.9998 16.25 17.0519 16.25 17.104C16.25 17.1691 16.2402 17.2277 16.2207 17.2798C16.2012 17.3319 16.1654 17.3774 16.1133 17.4165C16.0742 17.4686 16.0254 17.5044 15.9668 17.5239C15.9082 17.5435 15.8529 17.5532 15.8008 17.5532H14.4141C14.3229 17.5532 14.235 17.5369 14.1504 17.5044C14.0658 17.4718 13.9909 17.4165 13.9258 17.3384C13.8477 17.2733 13.7923 17.1984 13.7598 17.1138C13.7272 17.0291 13.7109 16.9412 13.7109 16.8501V5.95166H12.0898V16.8501C12.0898 16.9412 12.0703 17.0291 12.0312 17.1138C11.9922 17.1984 11.9336 17.2733 11.8555 17.3384C11.7904 17.4165 11.7155 17.4718 11.6309 17.5044C11.5462 17.5369 11.4583 17.5532 11.3672 17.5532H4.19922ZM10.2539 10.8931C10.2539 10.8019 10.2376 10.7173 10.2051 10.6392C10.1725 10.561 10.1237 10.4894 10.0586 10.4243C9.99349 10.3592 9.92188 10.3104 9.84375 10.2778C9.76562 10.2453 9.68099 10.229 9.58984 10.229C9.4987 10.229 9.41406 10.2453 9.33594 10.2778C9.25781 10.3104 9.1862 10.3592 9.12109 10.4243C9.05599 10.4894 9.00716 10.561 8.97461 10.6392C8.94206 10.7173 8.92578 10.8019 8.92578 10.8931C8.92578 10.9842 8.94206 11.0688 8.97461 11.147C9.00716 11.2251 9.05599 11.2967 9.12109 11.3618C9.1862 11.4269 9.25781 11.4757 9.33594 11.5083C9.41406 11.5409 9.4987 11.5571 9.58984 11.5571C9.68099 11.5571 9.76562 11.5409 9.84375 11.5083C9.92188 11.4757 9.99349 11.4269 10.0586 11.3618C10.1237 11.2967 10.1725 11.2251 10.2051 11.147C10.2376 11.0688 10.2539 10.9842 10.2539 10.8931ZM6.28906 16.6353H11.1719V5.15088H6.28906V16.6353Z"
          fill="black"></path>
      </svg>Deluxe room</li>
    <li><svg width="25" height="25" viewBox="0 0 25 25" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M5.16406 9.47949C5.16406 8.37492 6.05949 7.47949 7.16406 7.47949H17.6641C18.7686 7.47949 19.6641 8.37492 19.6641 9.47949V17.9795C19.6641 19.0841 18.7686 19.9795 17.6641 19.9795H7.16406C6.05949 19.9795 5.16406 19.0841 5.16406 17.9795V9.47949Z"
          stroke="#222222" stroke-width="1.28571" stroke-linecap="round" stroke-linejoin="round">
        </path>
        <path d="M8.41406 5.48145V8.98144" stroke="#222222" stroke-width="1.28571" stroke-linecap="round"
          stroke-linejoin="round"></path>
        <path d="M16.4141 5.48145V8.98144" stroke="#222222" stroke-width="1.28571" stroke-linecap="round"
          stroke-linejoin="round"></path>
        <path d="M8.16406 11.4814H16.6641" stroke="#222222" stroke-width="1.28571" stroke-linecap="round"
          stroke-linejoin="round"></path>
      </svg>1 night. {{ date('F j', strtotime($reviews->CreatedOn)) }}</li>
    <li><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M16.6615 17.8091V16.1424C16.6615 15.2584 16.3103 14.4105 15.6851 13.7854C15.06 13.1603 14.2122 12.8091 13.3281 12.8091H6.66146C5.7774 12.8091 4.92956 13.1603 4.30444 13.7854C3.67931 14.4105 3.32813 15.2584 3.32812 16.1424V17.8091"
          stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path>
        <path
          d="M10.0052 9.47575C11.8462 9.47575 13.3385 7.98336 13.3385 6.14242C13.3385 4.30147 11.8462 2.80908 10.0052 2.80908C8.16426 2.80908 6.67188 4.30147 6.67188 6.14242C6.67188 7.98336 8.16426 9.47575 10.0052 9.47575Z"
          stroke="#222222" stroke-linecap="round" stroke-linejoin="round"></path>
      </svg>{{$reviews->go_with}} Traveller</li>
  </ul>
  <p>{{ $reviews->Description }}</p>
  <a href="javascript:void(0);" class="tr-read-more">Read more</a>
  <div class="tr-hotel-response">
    <h5><svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M17.5 9.89195C17.5029 10.9918 17.2459 12.0769 16.75 13.0586C16.162 14.2351 15.2581 15.2246 14.1395 15.9164C13.021 16.6081 11.7319 16.9748 10.4167 16.9753C9.31678 16.9782 8.23176 16.7212 7.25 16.2253L2.5 17.8086L4.08333 13.0586C3.58744 12.0769 3.33047 10.9918 3.33333 9.89195C3.33384 8.57675 3.70051 7.28766 4.39227 6.16908C5.08402 5.05049 6.07355 4.14659 7.25 3.55862C8.23176 3.06273 9.31678 2.80575 10.4167 2.80862H10.8333C12.5703 2.90444 14.2109 3.63759 15.4409 4.86767C16.671 6.09775 17.4042 7.73833 17.5 9.47528V9.89195Z"
          stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
      </svg>Hotel response :</h5>
    <div class="tr-customer-msg">Dear guest,</div>
    <div class="tr-customer-msg">Thank you for taking the time to share your...</div>
    <a href="javascript:void(0)">Continue reading</a>
  </div>
  <div class="tr-helpful">
    Was this helpful?
    <button class="tr-like-button">Like</button>
    <button class="tr-dislike-button">Dislike</button>
  </div>
</div>
@endforeach
@else
reviews not found.
@endif