@extends('layouts.settings')

@section('content')
<nav class="navbar navbar-expand-lg bg-light py-0 desktop__Nav">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="{{ route('homepage') }}">
        <img src="{{ asset('images/logo.png') }}" style="width: 110px;"  alt="logo" />
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
          <a href="{{ route('profile.edit') }}">
            <img src="{{ $user->ProfilePicture ?? asset('images/profile.png') }}" alt="avatar" width="40" height="40" class="avatar rounded-circle" />
          </a>
        </div>
      </div>
    </div>
  </nav>
  <section class="pagewrapper">
   <div class="containertrip ">
    <div class="trip__profile">
      <div class="profile__section text-center">
              <img src="{{ $user->ProfilePicture ?? asset('images/bigprofile.jpg') }}" class="rounded-circle" width="128" height="128" alt="">
              <div class="profile__details">
                <h1>{{ $user->FirstName }} {{ $user->LastName }}</h1>
                <p>{{ $user->Bio }}</p>
                <p>Joined {{ date('Y', strtotime($user->CreatedAt ?? now())) }}</p>
                <button class="upload__profile tripprofile">Follow</button>
              </div>
            </div>
          </div>
          <div class="tabs custom  trip__profiletabs">
              <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link active"
                    id="about-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#about"
                    type="button"
                    role="tab"
                  >
                    About
                  </button>
                </li>
                <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="reviews-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#reviews"
                    type="button"
                    role="tab"
                  >
                    Reviews
                  </button>
                </li>
                     <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="saved-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#Saved"
                    type="button"
                    role="tab"
                  >
                    Saved
                  </button>
                </li>
              
                     <li class="nav-item" role="presentation">
                  <button
                    class="nav-link"
                    id="guides-tab"
                    data-bs-toggle="tab"
                    data-bs-target="#Guides"
                    type="button"
                    role="tab"
                  >
                    Guides
                  </button>
                </li>
              </ul>
            </div>
            <div class="tab-content" id="profileTabsContent">
              <div class="tab-pane fade show active" id="about" role="tabpanel">
                <div class="tabstipdetails">
                  <h4>Trips</h4>
                 
                <div class="row space_custom-12 py-3">
              <div class="col-md-4">
                <div class="card h-100 border-0">
                  <img src="{{ asset('images/trip.jpg') }}" class="card_images" alt="Rome">
                  <div class="card-body custompad">
                    <p class="custom__title">Exploring the Ancient Wonders of Rome</p>
                  </div>
                </div>
              </div>
            <div class="col-md-4">
                <div class="card h-100 border-0">
                  <img src="{{ asset('images/trip2.jpg') }}" class="card_images" alt="Rome">
                  <div class="card-body custompad">
                    <p class="custom__title">Discovering the Hidden Gems of Kyoto</p>
                  </div>
                </div>
              </div>

              <div class="col-md-4">
                <div class="card h-100 border-0">
                  <img src="{{ asset('images/trip3.jpg') }}" class="card_images" alt="Rome">
                  <div class="card-body custompad">
                    <p class="custom__title">Adventures in the Amazon Rainforest</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

              <div class="tab-pane fade" id="reviews" role="tabpanel">
                <p class="mt-4">No reviews yet.</p>
              </div>
                <div class="tab-pane fade" id="Saved" role="tabpanel">
                <p class="mt-4">Saved</p>
              </div>
               <div class="tab-pane fade" id="Guides" role="tabpanel">
                <p class="mt-4">Guides</p>
              </div>
            </div>
            <nav class="py-3" aria-label="Page navigation">
              <ul class="pagination justify-content-center">
                <li class="page-item">
                  <a class="page-link border-0" href="#" aria-label="Previous">
                  <img src="{{ asset('images/paginationleft.svg') }}" alt="">
                  </a>
                </li>
                <li class="page-item active">
                  <a class="page-link border-0 fw-bold" href="#">1</a>
                </li>
                <li class="page-item">
                  <a class="page-link border-0" href="#">2</a>
                </li>
                <li class="page-item">
                  <a class="page-link border-0" href="#">3</a>
                </li>
                <li class="page-item">
                <a class="page-link border-0" href="#" aria-label="Previous">
                  <img src="{{ asset('images/paginationright.svg') }}" alt="">
                  </a>
                </li>
              </ul>
            </nav>
        </div>
    </div>
  </section>
@endsection
