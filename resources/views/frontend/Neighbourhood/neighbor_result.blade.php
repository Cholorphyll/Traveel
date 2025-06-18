  @forelse($searchresults as $searchresult)
  <div class="row mt-5">

  <div class="col-md-4">
       <div class="card-slider">

         <img src="{{('images/heart.svg')}}" alt="" class="heart" />

         <div class="hotel-listing-slider slick-initialized slick-slider">
           <img src="{{('images/Hotel lobby.svg')}}" alt="" style="max-width: -webkit-fill-available;border-radius: 10px
px
;" />
           <!-- <img src="{{('images/unsplash_7T1KOFfE1aM.png')}}" alt="" />
                                          <img src="{{('images/unsplash_7T1KOFfE1aM.png')}}" alt="" /> -->
         </div>
       </div>
     </div>
    <div class="col-md-5">
      <div class="py-3 px-2 border-toend">

        <?php    $ctName = $lname;
                                $cityname = str_replace(' ', '_', $ctName);
                                $CountryName = str_replace(' ', '_', $countryname);
                                $url = $cityname .'-'.$CountryName;  
                          ?>
        <span class="d-none ">{{$searchresult->Name}}</span>
        <div class="fs20 fs-sm-14 fw-500  mb-2 hotel-title">{{$searchresult->Name}}</div>
        <div class="d-flex justify-content-between align-items-start locationandothers ">

        </div>

        @if($searchresult->address != "")
        <div class="d-flex color707070 align-items-center fs-sm-10 fs14  mb-2">
          <div class="d-flex align-items-center">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
              <path
                d="M10 9.25C10.6904 9.25 11.25 8.69036 11.25 8C11.25 7.30964 10.6904 6.75 10 6.75C9.30964 6.75 8.75 7.30964 8.75 8C8.75 8.69036 9.30964 9.25 10 9.25Z"
                fill="#E86E2C" />
              <path
                d="M10 1.75C6.55391 1.75 3.75 4.43164 3.75 7.72656C3.75 9.2957 4.46523 11.3824 5.87578 13.9289C7.00859 15.9734 8.31914 17.8223 9.00078 18.7422C9.11596 18.8994 9.26656 19.0272 9.44037 19.1153C9.61418 19.2034 9.8063 19.2493 10.0012 19.2493C10.196 19.2493 10.3882 19.2034 10.562 19.1153C10.7358 19.0272 10.8864 18.8994 11.0016 18.7422C11.682 17.8223 12.9938 15.9734 14.1266 13.9289C15.5348 11.3832 16.25 9.29648 16.25 7.72656C16.25 4.43164 13.4461 1.75 10 1.75ZM10 10.5C9.50555 10.5 9.0222 10.3534 8.61107 10.0787C8.19995 9.80397 7.87952 9.41352 7.6903 8.95671C7.50108 8.49989 7.45157 7.99723 7.54804 7.51227C7.6445 7.02732 7.8826 6.58186 8.23223 6.23223C8.58186 5.8826 9.02732 5.6445 9.51227 5.54804C9.99723 5.45157 10.4999 5.50108 10.9567 5.6903C11.4135 5.87952 11.804 6.19995 12.0787 6.61107C12.3534 7.0222 12.5 7.50555 12.5 8C12.4993 8.66282 12.2357 9.29828 11.767 9.76697C11.2983 10.2357 10.6628 10.4993 10 10.5Z"
                fill="#707070" />
            </svg>
            <div class="ms-1">
              {{$searchresult->address}}
            </div>


          </div>

        </div>
        @endif
      </div>

    </div>

  </div>




  @empty
  <h3 class="m-3">No Hotels available for this location.</h3>
  @endforelse


  <hr class="d-block">
  @if(!$searchresults->isEmpty())
  {{ $searchresults->links('hotellist_pagg.default') }}

  @endif