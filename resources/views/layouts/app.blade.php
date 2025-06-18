<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>{{ config('app.name', 'Laravel') }}</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="Able Pro is trending dashboard template made using Bootstrap 5 design framework. Able Pro is available in Bootstrap, React, CodeIgniter, Angular,  and .net Technologies.">
  <meta name="keywords"
    content="Bootstrap admin template, Dashboard UI Kit, Dashboard Template, Backend Panel, react dashboard, angular dashboard">
  <meta name="csrf-token" content="{{ csrf_token() }}">


  <meta name="author" content="Phoenixcoded">
  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
  <!-- Scripts -->
  <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
  <link rel="stylesheet" href="{{ asset('/resources/css/app.css')}}"/>
        <script href="{{ asset('/resources/css/app.js')}}" ></script>
  <!-- [Favicon] icon -->
  <link rel="icon" href="{{asset('/assets/images/favicon.svg')}}" type="image/x-icon">
  <!-- [Font] Family -->
  <link rel="stylesheet" href="{{asset('/assets/fonts/inter/inter.css')}}" id="main-font-link" />

  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="{{asset('/assets/fonts/tabler-icons.min.css')}}" />
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="{{asset('/assets/fonts/feather.css')}}" />
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="{{asset('/assets/fonts/fontawesome.css')}}" />
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="{{asset('/assets/fonts/material.css')}}" />
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="{{asset('/assets/css/style.css')}}" id="main-style-link" />
  <link rel="stylesheet" href="{{asset('/assets/css/style-preset.css')}}" />
	 <link rel="stylesheet" href="{{ asset('/css/map_leaflet.css')}}">
 <link rel="stylesheet" href="{{ asset('/assets/css/custom.css')}}">

</head>
<!-- [Head] end -->
<!-- [Body] Start -->

<body>
  <div class="loadResult hide">
    <div class="loader"></div>
  </div>
  <!-- [ Pre-loader ] start -->
  <div class="loader-bg">
    <div class="loader-track">
      <div class="loader-fill"></div>
    </div>
  </div>
  <!-- [ Pre-loader ] End -->
  <!-- [ Sidebar Menu ] start -->
  <nav class="pc-sidebar">
    <div class="navbar-wrapper">
      <div class="m-header">
        <a href="#" class="b-brand text-primary">
          <!-- ========   Change your logo from here   ============ -->
          <img src="{{asset('/assets/images/logo-dark.svg')}}" />
          <span class="badge bg-light-success rounded-pill ms-2 theme-version"></span>
        </a>
      </div>
      <div class="navbar-content">
        <div class="card pc-user-card">
          <div class="card-body">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0">
                <img src="{{asset('/assets/images/user/avatar-1.jpg')}}" alt="user-image"
                  class="user-avtar wid-45 rounded-circle" />
              </div>
              <div class="flex-grow-1 ms-3 me-2">
                <h6 class="mb-0">Jonh Smith</h6>
                <small>Administrator</small>
              </div>
              <a class="btn btn-icon btn-link-secondary avtar-s" data-bs-toggle="collapse" href="#pc_sidebar_userlink">
                <svg class="pc-icon">
                  <use xlink:href="#custom-sort-outline"></use>
                </svg>
              </a>
            </div>
            <div class="collapse pc-user-links" id="pc_sidebar_userlink">
              <div class="pt-3">
                <a href="#!">
                  <i class="ti ti-user"></i>
                  <span>My Account</span>
                </a>
                <a href="#!">
                  <i class="ti ti-power"></i>
                  <span>Logout</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <ul class="pc-navbar">
          <li class="pc-item pc-caption">
            <label>Navigation</label>
            <i class="ti ti-dashboard"></i>
          </li>
          <li class="pc-item pc-hasmenu">
            <a href="{{ route('dashboard')}}" class="pc-link">
              <span class="pc-micon">
                <svg class="pc-icon">
                  <use xlink:href="#custom-status-up"></use>
                </svg>
              </span>
              <span class="pc-mtext">Dashboard</span>
              <span class="pc-arrow">
            </a>

          </li>
          <ul class="pc-submenu">
            @if(Auth::user()->isActive != 2)
            <li class="pc-item"><a class="pc-link" href="{{route('search_location')}}">Manage Location</a></li>
            @endif
            <li class="pc-item"><a class="pc-link" href="{{route('search_attraction')}}">Manage Attraction</a></li>
            <li class="pc-item"><a class="pc-link" href="{{route('hotels')}}">Manage Hotels</a></li>
 			<li class="pc-item"><a class="pc-link" href="{{route('restaurant')}}">Manage Restaurant</a></li> 
			<li class="pc-item"><a class="pc-link" href="{{route('experience')}}">Manage Experince</a></li> 
			<li class="pc-item"><a class="pc-link" href="{{route('landing')}}">Manage Landing Pages</a></li>
			<li class="pc-item"><a class="pc-link" href="{{route('reviews')}}">Manage Reviews</a></li> 
			<li class="pc-item"><a class="pc-link" href="{{route('manage_faqs')}}">Manage Faq</a></li> 
            <li class="pc-item"><a class="pc-link" href="{{route('manage_category')}}">Manage Category</a></li> 
            @if(Auth::user()->isActive != 2)
			<li class="pc-item"><a class="pc-link" href="{{route('users')}}">Manage Users</a></li> 
			<li class="pc-item"><a class="pc-link" href="{{route('user_index')}}">Manage Admin Users</a></li> 
		
			  <li class="pc-item"><a class="pc-link" href="{{route('busi_index')}}">Manage Business</a></li>  
			  <li class="pc-item"><a class="pc-link" href="{{route('all_busi_users')}}">Manage Business Users</a></li>  
      @endif
			  
          </ul>

        </ul>
      </div>
    </div>
  </nav>
  <!-- [ Sidebar Menu ] end -->
  <!-- [ Header Topbar ] start -->
  <header class="pc-header">
    <div class="header-wrapper">
      <!-- [Mobile Media Block] start -->
      <div class="me-auto pc-mob-drp">
        <ul class="list-unstyled">
          <!-- ======= Menu collapse Icon ===== -->
          <li class="pc-h-item pc-sidebar-collapse">
            <a href="#" class="pc-head-link ms-0" id="sidebar-hide">
              <i class="ti ti-menu-2"></i>
            </a>
          </li>
          <li class="pc-h-item pc-sidebar-popup">
            <a href="#" class="pc-head-link ms-0" id="mobile-collapse">
              <i class="ti ti-menu-2"></i>
            </a>
          </li>
          <li class="dropdown pc-h-item">
            <a class="pc-head-link dropdown-toggle arrow-none m-0 trig-drp-search" data-bs-toggle="dropdown" href="#"
              role="button" aria-haspopup="false" aria-expanded="false">
              <svg class="pc-icon">
                <use xlink:href="#custom-search-normal-1"></use>
              </svg>
            </a>
            <div class="dropdown-menu pc-h-dropdown drp-search">
              <form class="px-3 py-2">
                <input type="search" class="form-control border-0 shadow-none" placeholder="Search here. . ." />
              </form>
            </div>
          </li>
        </ul>
      </div>
      <!-- [Mobile Media Block end] -->
      <div class="ms-auto">
        <ul class="list-unstyled">
          <li class="dropdown pc-h-item">
            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
              aria-haspopup="false" aria-expanded="false">
              <svg class="pc-icon">
                <use xlink:href="#custom-sun-1"></use>
              </svg>
            </a>
            <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
              <a href="#!" class="dropdown-item" onclick="layout_change('dark')">
                <svg class="pc-icon">
                  <use xlink:href="#custom-moon"></use>
                </svg>
                <span>Dark</span>
              </a>
              <a href="#!" class="dropdown-item" onclick="layout_change('light')">
                <svg class="pc-icon">
                  <use xlink:href="#custom-sun-1"></use>
                </svg>
                <span>Light</span>
              </a>
              <a href="#!" class="dropdown-item" onclick="layout_change_default()">
                <svg class="pc-icon">
                  <use xlink:href="#custom-setting-2"></use>
                </svg>
                <span>Default</span>
              </a>
            </div>
          </li>
          <li class="dropdown pc-h-item">
            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
              aria-haspopup="false" aria-expanded="false">
              <svg class="pc-icon">
                <use xlink:href="#custom-setting-2"></use>
              </svg>
            </a>
            <div class="dropdown-menu dropdown-menu-end pc-h-dropdown">
              <a href="#!" class="dropdown-item">
                <i class="ti ti-user"></i>
                <span>My Account</span>
              </a>
              <a href="#!" class="dropdown-item">
                <i class="ti ti-settings"></i>
                <span>Settings</span>
              </a>
              <a href="#!" class="dropdown-item">
                <i class="ti ti-headset"></i>
                <span>Support</span>
              </a>
              <a href="#!" class="dropdown-item">
                <i class="ti ti-lock"></i>
                <span>Lock Screen</span>
              </a>
              <a href="#!" class="dropdown-item">
                <i class="ti ti-power"></i>
                <span>Logout</span>
              </a>
            </div>
          </li>
          <li class="pc-h-item">
            <a href="#" class="pc-head-link me-0" data-bs-toggle="offcanvas" data-bs-target="#announcement"
              aria-controls="announcement">
              <svg class="pc-icon">
                <use xlink:href="#custom-flash"></use>
              </svg>
            </a>
          </li>
          <li class="dropdown pc-h-item">
            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
              aria-haspopup="false" aria-expanded="false">
              <svg class="pc-icon">
                <use xlink:href="#custom-notification"></use>
              </svg>
              <span class="badge bg-success pc-h-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-notification dropdown-menu-end pc-h-dropdown">
              <div class="dropdown-header d-flex align-items-center justify-content-between">
                <h5 class="m-0">Notifications</h5>
                <a href="#!" class="btn btn-link btn-sm">Mark all read</a>
              </div>
              <div class="dropdown-body text-wrap header-notification-scroll position-relative"
                style="max-height: calc(100vh - 215px)">
                <p class="text-span">Today</p>
                <div class="card mb-2">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <svg class="pc-icon text-primary">
                          <use xlink:href="#custom-layer"></use>
                        </svg>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <span class="float-end text-sm text-muted">2 min ago</span>
                        <h5 class="text-body mb-2">UI/UX Design</h5>
                        <p class="mb-0">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                          when an unknown printer took a galley of
                          type and scrambled it to make a type</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card mb-2">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <svg class="pc-icon text-primary">
                          <use xlink:href="#custom-sms"></use>
                        </svg>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <span class="float-end text-sm text-muted">1 hour ago</span>
                        <h5 class="text-body mb-2">Message</h5>
                        <p class="mb-0">Lorem Ipsum has been the industry's standard dummy text ever since the 1500.</p>
                      </div>
                    </div>
                  </div>
                </div>
                <p class="text-span">Yesterday</p>
                <div class="card mb-2">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <svg class="pc-icon text-primary">
                          <use xlink:href="#custom-document-text"></use>
                        </svg>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <span class="float-end text-sm text-muted">2 hour ago</span>
                        <h5 class="text-body mb-2">Forms</h5>
                        <p class="mb-0">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                          when an unknown printer took a galley of
                          type and scrambled it to make a type</p>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card mb-2">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <svg class="pc-icon text-primary">
                          <use xlink:href="#custom-user-bold"></use>
                        </svg>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <span class="float-end text-sm text-muted">12 hour ago</span>
                        <h5 class="text-body mb-2">Challenge invitation</h5>
                        <p class="mb-2"><span class="text-dark">Jonny aber</span> invites to join the challenge</p>
                        <button class="btn btn-sm btn-outline-secondary me-2">Decline</button>
                        <button class="btn btn-sm btn-primary">Accept</button>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="card mb-2">
                  <div class="card-body">
                    <div class="d-flex">
                      <div class="flex-shrink-0">
                        <svg class="pc-icon text-primary">
                          <use xlink:href="#custom-security-safe"></use>
                        </svg>
                      </div>
                      <div class="flex-grow-1 ms-3">
                        <span class="float-end text-sm text-muted">5 hour ago</span>
                        <h5 class="text-body mb-2">Security</h5>
                        <p class="mb-0">Lorem Ipsum has been the industry's standard dummy text ever since the 1500s,
                          when an unknown printer took a galley of
                          type and scrambled it to make a type</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="text-center py-2">
                <a href="#!" class="link-danger">Clear all Notifications</a>
              </div>
            </div>
          </li>
          <li class="dropdown pc-h-item header-user-profile">
            <a class="pc-head-link dropdown-toggle arrow-none me-0" data-bs-toggle="dropdown" href="#" role="button"
              aria-haspopup="false" data-bs-auto-close="outside" aria-expanded="false">
              <img src="{{asset('/assets/images/user/avatar-1.jpg')}}" alt="user-image" class="user-avtar" />
            </a>
            <div class="dropdown-menu dropdown-user-profile dropdown-menu-end pc-h-dropdown">
              <div class="dropdown-header d-flex align-items-center justify-content-between">
                <h5 class="m-0">Profile</h5>
              </div>
              <div class="dropdown-body">
                <div class="profile-notification-scroll position-relative" style="max-height: calc(100vh - 225px)">
                  <div class="d-flex mb-1">
                    <div class="flex-shrink-0">
                      <img src="{{asset('/assets/images/user/avatar-1.jpg')}}" alt="user-image" class="user-avtar wid-35" />
                    </div>
                    <div class="flex-grow-1 ms-3">
                      <h6 class="mb-1">{{ Auth::user()->name }} 🖖</h6>
                      <span>{{ Auth::user()->email }}</span>
                    </div>
                  </div>
                  <hr class="border-secondary border-opacity-50" />
                  <div class="card">
                    <div class="card-body py-3">
                      <div class="d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 d-inline-flex align-items-center"><svg class="pc-icon text-muted me-2">
                            <use xlink:href="#custom-notification-outline"></use>
                          </svg>Notification</h5>
                        <div class="form-check form-switch form-check-reverse m-0">
                          <input class="form-check-input f-18" type="checkbox" role="switch" />
                        </div>
                      </div>
                    </div>
                  </div>
                  <p class="text-span">Manage</p>
                  <a href="{{route('profile.edit')}}" class="dropdown-item">
                    <span class="d-flex">
                      <svg class="pc-icon text-muted me-2">
                        <use xlink:href="#custom-setting-outline"></use>
                      </svg>
                      <span>Profile</span>
                    </span>
                  </a>
                  <hr class="border-secondary border-opacity-50" />
                  <div class="d-grid mb-3">
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf

                      <button class="btn btn-primary d-flex" type="submit">
                        <svg class="pc-icon me-2">
                          <use xlink:href="#custom-logout-1-outline"></use>
                        </svg>
                        <div>Logout</div>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
      </div>
      </li>
      </ul>
    </div>
    </div>
  </header>
  <div class="offcanvas pc-announcement-offcanvas offcanvas-end" tabindex="-1" id="announcement"
    aria-labelledby="announcementLabel">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title" id="announcementLabel">What’s new announcement?</h5>
      <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
      <p class="text-span">Today</p>
      <div class="card mb-3">
        <div class="card-body">
          <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
            <div class="badge bg-light-success f-12">Big News</div>
            <p class="mb-0 text-muted">2 min ago</p>
            <span class="badge dot bg-warning"></span>
          </div>
          <h5 class="mb-3">Able Pro is Redesigned</h5>
          <p class="text-muted">Able Pro is completely renowed with high aesthetics User Interface.</p>
          <img src="{{asset('/assets/imageslayout/img-announcement-1.png')}}" alt="img" class="img-fluid mb-3" />
          <div class="row">
            <div class="col-12">
              <div class="d-grid"><a class="btn btn-outline-secondary"
                  href="https://1.envato.market/c/1289604/275988/4415?subId1=phoenixcoded&u=https%3A%2F%2Fthemeforest.net%2Fitem%2Fable-pro-responsive-bootstrap-4-admin-template%2F19300403"
                  target="_blank">Check Now</a></div>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-3">
        <div class="card-body">
          <div class="align-items-center d-flex flex-wrap gap-2 mb-3">
            <div class="badge bg-light-warning f-12">Offer</div>
            <p class="mb-0 text-muted">2 hour ago</p>
            <span class="badge dot bg-warning"></span>
          </div>
          <h5 class="mb-3">Able Pro is in best offer price</h5>
          <p class="text-muted">Download Able Pro exclusive on themeforest with best price. </p>
          <a href="https://1.envato.market/c/1289604/275988/4415?subId1=phoenixcoded&u=https%3A%2F%2Fthemeforest.net%2Fitem%2Fable-pro-responsive-bootstrap-4-admin-template%2F19300403"
            target="_blank"><img src="{{asset('/assets/imageslayout/img-announcement-2.png')}}" alt="img" class="img-fluid" /></a>
        </div>
      </div>

    </div>
  </div>
  <!-- header ends here -->
  <main>
    {{ $slot }}
  </main>


  <div class="offcanvas border-0 pct-offcanvas offcanvas-end" tabindex="-1" id="offcanvas_pc_layout">
    <div class="offcanvas-header">
      <h5 class="offcanvas-title">Settings</h5>
      <button type="button" class="btn btn-icon btn-link-danger" data-bs-dismiss="offcanvas" aria-label="Close"><i
          class="ti ti-x"></i></button>
    </div>
    <div class="pct-body" style="height: calc(100% - 85px)">
      <div class="offcanvas-body py-0">
        <ul class="list-group list-group-flush">
          <li class="list-group-item">
            <div class="pc-dark">
              <h6 class="mb-1">Theme Mode</h6>
              <p class="text-muted text-sm">Choose light or dark mode or Auto</p>
              <div class="row theme-layout">
                <div class="col-4">
                  <div class="d-grid">
                    <button class="preset-btn btn active" data-value="true" onclick="layout_change('light');">
                      <svg class="pc-icon text-warning">
                        <use xlink:href="#custom-sun-1"></use>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="false" onclick="layout_change('dark');">
                      <svg class="pc-icon">
                        <use xlink:href="#custom-moon"></use>
                      </svg>
                    </button>
                  </div>
                </div>
                <div class="col-4">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="default" onclick="layout_change_default();">
                      <svg class="pc-icon">
                        <use xlink:href="#custom-setting-2"></use>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <h6 class="mb-1">Theme Contrast</h6>
            <p class="text-muted text-sm">Choose theme contrast</p>
            <div class="row theme-contrast">
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="true" onclick="layout_sidebar_change('true');">
                    <svg class="pc-icon">
                      <use xlink:href="#custom-mask"></use>
                    </svg>
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="false" onclick="layout_sidebar_change('false');">
                    <svg class="pc-icon">
                      <use xlink:href="#custom-mask-1-outline"></use>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <h6 class="mb-1">Custom Theme</h6>
            <p class="text-muted text-sm">Choose your Primary color</p>
            <div class="theme-color preset-color">
              <a href="#!" class="active" data-value="preset-1"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-2"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-3"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-4"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-5"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-6"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-7"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-8"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-9"><i class="ti ti-check"></i></a>
              <a href="#!" data-value="preset-10"><i class="ti ti-check"></i></a>
            </div>
          </li>
          <li class="list-group-item">
            <h6 class="mb-1">Sidebar Caption</h6>
            <p class="text-muted text-sm">Sidebar Caption Hide/Show</p>
            <div class="row theme-nav-caption">
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn active" data-value="true" onclick="layout_caption_change('true');">
                    <img src="{{asset('/assets/imagescustomizer/img-caption-1.svg')}}" alt="img" class="img-fluid" width="70%" />
                  </button>
                </div>
              </div>
              <div class="col-6">
                <div class="d-grid">
                  <button class="preset-btn btn" data-value="false" onclick="layout_caption_change('false');">
                    <img src="{{asset('/assets/imagescustomizer/img-caption-2.svg')}}" alt="img" class="img-fluid" width="70%" />
                  </button>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="pc-rtl">
              <h6 class="mb-1">Theme Layout</h6>
              <p class="text-muted text-sm">LTR/RTL</p>
              <div class="row theme-direction">
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn active" data-value="false" onclick="layout_rtl_change('false');">
                      <img src="{{asset('/assets/imagescustomizer/img-layout-1.svg')}}" alt="img" class="img-fluid" width="70%" />
                    </button>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="true" onclick="layout_rtl_change('true');">
                      <img src="{{asset('/assets/imagescustomizer/img-layout-2.svg')}}" alt="img" class="img-fluid" width="70%" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="pc-container-width">
              <h6 class="mb-1">Layout Width</h6>
              <p class="text-muted text-sm">Choose Full or Container Layout</p>
              <div class="row theme-container">
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn active" data-value="false" onclick="change_box_container('false')">
                      <img src="{{asset('/assets/imagescustomizer/img-container-1.svg')}}" alt="img" class="img-fluid"
                        width="70%" />
                    </button>
                  </div>
                </div>
                <div class="col-6">
                  <div class="d-grid">
                    <button class="preset-btn btn" data-value="true" onclick="change_box_container('true')">
                      <img src="{{asset('/assets/imagescustomizer/img-container-2.svg')}}" alt="img" class="img-fluid"
                        width="70%" />
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </li>
          <li class="list-group-item">
            <div class="d-grid">
              <button class="btn btn-light-danger" id="layoutreset">Reset Layout</button>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- [Page Specific JS] start -->
  
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.0/jquery-ui.min.js"></script>
  <script src="{{asset('/assets/js/config.js')}}"></script>
  <script src="{{asset('/assets/js/main.js')}}"></script>
<script src="{{asset('/assets/js/custom.js')}}"></script>
  <script src="{{asset('/assets/js/plugins/apexcharts.min.js')}}"></script>
  <script src="{{asset('/assets/js/pages/dashboard-default.js')}}"></script>
  @stack('scripts')
  <!-- [Page Specific JS] end -->
  <script src="{{ asset('/js/map_leaflet.js')}}"></script>
  <!-- Required Js -->
  <script src="{{asset('/assets/js/plugins/popper.min.js')}}"></script>
  <script src="{{asset('/assets/js/plugins/simplebar.min.js')}}"></script>
  <script src="{{asset('/assets/js/plugins/bootstrap.min.js')}}"></script>
  <script src="{{asset('/assets/js/fonts/custom-font.js')}}"></script>
  <script src="{{asset('/assets/js/config.js')}}"></script>
  <script src="{{asset('/assets/js/pcoded.js')}}"></script>
  <script src="{{asset('/assets/js/plugins/feather.min.js')}}"></script>

</body>
<!-- [Body] end -->

</html>