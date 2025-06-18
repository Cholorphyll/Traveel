  @if(!$sightreviews->isEmpty())           
                          <div class="tr-customer-reviews ">
                            @foreach($sightreviews as $review)
                              <div class="tr-customer-review">
                                <div class="tr-customer-details">
                                  <div class="tr-customer-detail">
                                    <img loading="lazy" src="{{asset('public/frontend/hotel-detail/images/usericon-review.png')}}" alt="customer">
                                    <div class="tr-customer-name">
                                      <div>{{$review->Name}}</div>
                                      <div class="tr-recommended-title @if($review->IsRecommend == 1) tr-heart-green @elseif($review->IsRecommend == 0) tr-heart-red @endif"> Recommended
                                        <!-- <span class="tr-time">(1 week ago)</span> -->
                                      </div>
                                    </div>
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
                                <p>{{$review->ReviewDescription}}</p>
                                <div class="tr-helpful">
                                  Was this helpful? 
                                  <button class="tr-like-button">Like</button>
                                  <button class="tr-dislike-button">Dislike</button>
                                </div>
                              </div>
                            @endforeach
                          </div>                  
                          @else
                          <div class="tr-no-review-listing mt-5">
                            <div class="tr-no-review-list">
                                  <div class="tr-no-data-text w-70 tr-no-data-dark-gray mb-12"></div>
                                  <div class="tr-no-data-text w-70 mb-12"></div>
                                  <div class="tr-no-data-text w-50 tr-no-data-red mb-12"></div>
                                  <div class="tr-no-data-text w-30 tr-no-data-green mb-12"></div>
                                  <div class="tr-no-data-text w-20 mt-41 mb-12"></div>
                                  <div class="tr-no-data-text w-10 mb-0"></div>
                            </div>
                          </div>
                          @endif