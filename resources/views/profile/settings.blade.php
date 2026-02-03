@extends('layouts.settings')

@section('content')
<nav class="navbar navbar-expand-lg bg-light py-0 desktop__Nav">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="{{ route('homepage') }}">
        <img src="{{ asset('images/logo.png') }}" style="width: 100px;" alt="logo" />
      </a>
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link" href="#">Trips</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Explore</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#">Saved</a>
          </li>
        </ul>
        <div class="d-flex align-items-center customgap">
          <div class="position-relative search-container">
            <span class="search__icon"><img src="{{ asset('images/Search.svg') }}" alt="" /></span>
            <input type="text" class="form-control search__input" placeholder="Search" />
          </div>

          <div class="bell__button">
            <img src="{{ asset('images/info.svg') }}" alt="" />
          </div>

          <a href="{{ route('profile.edit') }}" class="profile-image-link">
            @if(session()->has('frontend_user') && isset(session('frontend_user')['user_image']))
              <img src="{{ asset(session('frontend_user')['user_image']) }}" 
                   alt="{{ session('frontend_user')['Username'] }}" 
                   class="avatar rounded-circle" />
            @else
              <img src="{{ asset('images/lobby-image.png') }}" 
                   alt="avatar" width="40" height="40"
                   class="avatar rounded-circle" />
            @endif
          </a>
        </div>
      </div>
    </div>
  </nav>
  <section class="pagewrapper">
    <div class="container">
      <div class="page__section pageGap">
        <div class="left__section">
          <div class="custom__Nav">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="account-tab" data-bs-toggle="tab" data-bs-target="#account"
                  type="button" role="tab">
                  Account
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" type="button"
                  role="tab">
                  Reviews
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="mail-tab" data-bs-toggle="tab" data-bs-target="#mail" type="button"
                  role="tab">
                  Linked Mailboxes
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="user-tab" data-bs-toggle="tab" data-bs-target="#usersetting" type="button"
                  role="tab">
                  User Settings
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="notifications-tab" data-bs-toggle="tab" data-bs-target="#notifications"
                  type="button" role="tab">
                  Notifications
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="preferences-tab" data-bs-toggle="tab" data-bs-target="#preferences"
                  type="button" role="tab">
                  Preferences
                </button>
              </li>
            </ul>
          </div>
        </div>
        <div class="right__section">
          <div class="d-flex justify-content-between align-items-start">
            <h2 class="profile__heading">Settings</h2>
          </div>
          <div class="tab-content" id="profileTabsContent">
            <div class="tab-pane fade show active" id="account" role="tabpanel">
              <div class="Notifications">
                <h4>Account</h4>
              </div>
              @if(session('success') || session('error') || $errors->any())
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                  {{ session('success') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  {{ session('error') }}
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                
                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                  <ul class="mb-0">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
              @endif
              
              <form class="form__Section" action="{{ route('user.settings.account') }}" method="POST" id="accountForm">
                @csrf
                
                <div class="form__group">
                  <label for="FirstName" class="form-label">First Name</label>
                  <input type="text" class="form-control" id="FirstName" name="FirstName" value="{{ $user->FirstName ?? '' }}" placeholder="First Name" required>
                </div>
                <div class="form__group">
                  <label for="LastName" class="form-label">Last Name</label>
                  <input type="text" class="form-control" id="LastName" name="LastName" value="{{ $user->LastName ?? '' }}" placeholder="Last Name" required>
                </div>
                <div class="form__group">
                  <label for="Username" class="form-label">Username</label>
                  <input type="text" class="form-control" id="Username" name="Username" value="{{ $user->Username ?? '' }}" placeholder="Username" required>
                </div>
                <div class="form__group">
                  <label for="Email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="Email" name="Email" value="{{ $user->Email ?? '' }}" placeholder="Email" required>
                </div>
                <button type="submit" class="upload__profile change">Save Changes</button>
              </form>

            </div>
            <div class="tab-pane fade" id="reviews" role="tabpanel">
              Reviews
            </div>
            <div class="tab-pane fade" id="mail" role="tabpanel">
              <div class="Mail__boxes">
                <h3>Linked Mailboxes</h3>
                <p>Connect your email to automatically import flight and accommodation reservations. This feature
                  supports Gmail, Outlook, and Yahoo Mail.</p>
                <button class="upload__profile">Connect to Gmail</button>
              </div>
            </div>
            <div class="tab-pane fade" id="usersetting" role="tabpanel">
              <div class="Mail__boxes">
                <h3>User Settings</h3>

              </div>
              <form class="form__Section dateSection" method="POST" action="{{ route('user.settings.preferences') }}">
                @csrf
                <div class="group__Section">
                  <label class="label__heading" for="">Date Format</label>
                  <div class="form-check p-0">
                    <input class="form-check-input d-none" type="radio" name="date_format" value="mm/dd/yyyy" id="circleCheck1" {{ session('user_preferences.date_format') == 'mm/dd/yyyy' || !session('user_preferences.date_format') ? 'checked' : '' }}>
                    <label class="form-check-label w-100 p-3" for="circleCheck1">mm/dd/yyyy</label>
                  </div>

                  <div class="form-check p-0">
                    <input class="form-check-input d-none" type="radio" name="date_format" value="dd/mm/yyyy" id="circleCheck2" {{ session('user_preferences.date_format') == 'dd/mm/yyyy' ? 'checked' : '' }}>
                    <label class="form-check-label w-100 p-3" for="circleCheck2">dd/mm/yyyy</label>
                  </div>
                </div>
                <div class="group__Section">
                  <label class="label__heading" for="">Distance Format</label>
                  <div class="form-check p-0">
                    <input class="form-check-input d-none" type="radio" name="distance_format" value="miles" id="circleCheck3" {{ session('user_preferences.distance_format') == 'miles' || !session('user_preferences.distance_format') ? 'checked' : '' }}>
                    <label class="form-check-label w-100 p-3" for="circleCheck3">Miles <span>(e.g. 5mi)</span></label>
                  </div>

                  <div class="form-check p-0">
                    <input class="form-check-input d-none" type="radio" name="distance_format" value="kilometers" id="circleCheck4" {{ session('user_preferences.distance_format') == 'kilometers' ? 'checked' : '' }}>
                    <label class="form-check-label w-100 p-3" for="circleCheck4">Kilometers <span>(e.g.
                        80km)</span></label>
                  </div>
                </div>
                <div class="group__Section">
                  <label class="label__heading" for="">Time Format</label>
                  <div class="form-check p-0">
                    <input class="form-check-input d-none" type="radio" name="time_format" value="12hour" id="circleCheck5" {{ session('user_preferences.time_format') == '12hour' || !session('user_preferences.time_format') ? 'checked' : '' }}>
                    <label class="form-check-label w-100 p-3" for="circleCheck5">12-hour <span>(e.g.
                        2:00pm)</span></label>
                  </div>

                  <div class="form-check p-0">
                    <input class="form-check-input d-none" type="radio" name="time_format" value="24hour" id="circleCheck6" {{ session('user_preferences.time_format') == '24hour' ? 'checked' : '' }}>
                    <label class="form-check-label w-100 p-3" for="circleCheck6">24-hour <span>(e.g.
                        14:00)</span></label>
                  </div>
                </div>
                
                <div class="mt-2">
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>Place Descriptions</h5>
                      <p>Enable place descriptions from the web to enhance your trip details.</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="place_descriptions" id="place_descriptions" {{ session('user_preferences.place_descriptions') ? 'checked' : '' }}>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>Expert Travel Tips</h5>
                      <p>Receive expert travel tips and recommendations to improve your travel planning.</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="travel_tips" id="travel_tips" {{ session('user_preferences.travel_tips') ? 'checked' : '' }}>
                    </div>
                  </div>
                </div>
                
                <button type="submit" class="upload__profile change mt-3">Save Preferences</button>
              </form>
            </div>
            <div class="tab-pane fade" id="notifications" role="tabpanel">
              <form method="POST" action="{{ route('user.settings.notifications') }}">
                @csrf
                <div class="Notifications">
                  <h4>Notifications</h4>
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>Email Notifications</h5>
                      <p>Receive email notifications for trip updates, reminders, and important information.</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="email_notifications" id="email_notifications" {{ session('notification_settings.email_notifications') ? 'checked' : '' }}>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>Trip updates</h5>
                      <p>Receive emails when collaborators make changes to the trip</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="trip_updates" id="trip_updates" {{ session('notification_settings.trip_updates') ? 'checked' : '' }}>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>New messages</h5>
                      <p>Receive emails for new messages</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="new_messages" id="new_messages" {{ session('notification_settings.new_messages') ? 'checked' : '' }}>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>New activities</h5>
                      <p>Receive emails when new activities are added to the trip</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="new_activities" id="new_activities" {{ session('notification_settings.new_activities') ? 'checked' : '' }}>
                    </div>
                  </div>
                  <div class="d-flex justify-content-between gap-3 align-items-center py-2">
                    <div class="details">
                      <h5>Push Notifications</h5>
                      <p>Get push notifications for real-time updates, flight deals, and app improvements.</p>
                    </div>
                    <div class="form-check mb-0 form-switch toggle-setting">
                      <input class="form-check-input" type="checkbox" name="push_notifications" id="push_notifications" {{ session('notification_settings.push_notifications') ? 'checked' : '' }}>
                    </div>
                  </div>
                  <button type="submit" class="upload__profile change mt-3">Save Notification Settings</button>
                </div>
              </form>
            </div>
            <div class="tab-pane fade" id="preferences" role="tabpanel">
              <div class="Notifications">
                <h4>Preferences</h4>
              </div>
              <form class="form__Section custom__form" method="POST" action="{{ route('user.settings.preferences') }}">
                @csrf
                <div class="form__group">
                  <label for="language" class="form-label">Language</label>
                  <select class="form-control" id="language" name="language">
                    <option value="en" {{ session('user_preferences.language') == 'en' ? 'selected' : '' }}>English</option>
                    <option value="es" {{ session('user_preferences.language') == 'es' ? 'selected' : '' }}>Spanish</option>
                    <option value="fr" {{ session('user_preferences.language') == 'fr' ? 'selected' : '' }}>French</option>
                    <option value="de" {{ session('user_preferences.language') == 'de' ? 'selected' : '' }}>German</option>
                  </select>
                </div>
                <div class="form__group">
                  <label for="currency" class="form-label">Currency</label>
                  <select class="form-control" id="currency" name="currency">
                    <option value="USD" {{ session('user_preferences.currency') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                    <option value="EUR" {{ session('user_preferences.currency') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                    <option value="GBP" {{ session('user_preferences.currency') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                    <option value="INR" {{ session('user_preferences.currency') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                  </select>
                </div>
                <div class="form__group">
                  <label for="theme" class="form-label">Theme</label>
                  <select class="form-control" id="theme" name="theme">
                    <option value="light" {{ session('user_preferences.theme') == 'light' ? 'selected' : '' }}>Light</option>
                    <option value="dark" {{ session('user_preferences.theme') == 'dark' ? 'selected' : '' }}>Dark</option>
                    <option value="system" {{ session('user_preferences.theme') == 'system' ? 'selected' : '' }}>System Default</option>
                  </select>
                </div>
                <button type="submit" class="upload__profile change">Save Preferences</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    </div>
  </section>
@endsection