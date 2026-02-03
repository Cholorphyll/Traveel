<header id="header">
  <div class="container">
    <div class="row">
      <div class="col-sm-12">
        <div class="tr-header">
          <div class="tr-hamburgers-logo">
            <div class="tr-hamburgers"></div>
            <div class="tr-logo">
              <a href="/">
                <img loading="lazy" src="{{asset('frontend/hotel-detail/images/travell-small-logo.png')}}" alt="travell-logo">
              </a>
            </div>
          </div>
          <div class="tr-logo tr-desktop">
            <a href="/">
              <img loading="lazy" src="{{asset('frontend/hotel-detail/images/travell-logo.png')}}" alt="travell-logo">
            </a>
          </div>
          <div class="tr-nav-tabs show non-clickable">
            <div class="tr-explore-tab" data-tab="exploreForm"><span><img src="{{asset('frontend/hotel-detail/images/icons/compass-icon.svg')}}" alt="Compass Icon">Explore</span></div>
            <div class="tr-hotel-tab active" data-tab="hotelForm"><span><img src="{{asset('frontend/hotel-detail/images/icons/clarity_building-line-black-icon.svg')}}" alt="Clarity Building Line Icon"/>Hotel</span></div>
          </div>
          <div class="tr-login-section">
          <!-- Unified auth check: Laravel Auth (e.g., Google) OR legacy session('frontend_user') -->
          <?php
              $isLoggedIn = false;
              $Username = '';
              $user_image = null;
              try {
                if (Auth::check()) {
                  $isLoggedIn = true;
                  $authUser = Auth::user();
                  $Username = $authUser->name ?? ($authUser->email ?? 'User');
                  // If your users table has an avatar/photo column, use it; otherwise keep null.
                  $user_image = $authUser->avatar ?? ($authUser->photo ?? null);
                } elseif (session()->has('frontend_user')) {
                  $isLoggedIn = true;
                  $userData = session('frontend_user');
                  $Username = $userData['Username'] ?? '';
                  $user_image = $userData['user_image'] ?? null;
                }
              } catch (\Throwable $e) { /* ignore */ }
            ?>
            @if ($isLoggedIn) 
          <button class="tr-logged">
            <div class="tr-username">{{$Username}}</div>
          </button>
          <div class="tr-myaccount-modal">
            
            <div class="tr-mz-myaccount-info">
              <ul>
                 <li class="tr-my-profile-link"><a href="{{route('profile')}}">My Profile</a></li>
                <li class="tr-my-settings-link"><a href="{{route('user.settings')}}">Settings</a></li>
                <li class="tr-logout-link"><a href="{{route('userlogout')}}">Logout</a></li>
              </ul>
            </div>
          </div>
          @else
          <button class="tr-login" data-bs-toggle="modal" data-bs-target="#signInModal">Sign in</button>
          @endif

          </div>

        </div>
      </div>
    </div>
  </div>
</header>
@include('frontend.login_models')