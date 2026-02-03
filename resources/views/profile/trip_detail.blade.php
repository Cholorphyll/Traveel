@extends('layouts.settings')

@section('content')
<nav class="navbar navbar-expand-lg bg-light py-0 desktop__Nav">
    <div class="container">
        <a class="navbar-brand customlogo d-flex align-items-center" href="{{ route('homepage') }}">
            <img src="{{ asset('images/logo.png') }}" style="width:110px" alt="logo" />
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>
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
                <img src="{{ $user->ProfilePicture ?? asset('images/profile.png') }}" 
                alt="avatar" class="avatar rounded-circle" width="40" height="40" />
            </div>
        </div>
    </div>
</nav>

<section class="pagewrapper">
    <div class="containertrip">
        <div class="trip__profile">
            <div class="profile__section text-center">
            <img src="{{ $user->ProfilePicture ?? asset('images/bigprofile.jpg') }}" 
            class="rounded-circle" width="128" height="128" alt="" />
                <div class="profile__details">
                    <h1>{{ $user->FirstName ?? null }} {{ $user->LastName ?? null }}</h1>
                    <p>Joined in {{ date('Y', strtotime($user->CreatedAt ?? now())) }}</p>
                    <a href="{{ route('profile.edit') }}" class="upload__profile tripprofile">Edit Profile</a>
                </div>
            </div>
        </div>
        
        <div class="tabs custom trip__profiletabs">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'about' ? 'active' : '' }}" 
                            id="about-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#about" 
                            type="button" 
                            role="tab">
                        About
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'saved' ? 'active' : '' }}" 
                            id="saved-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#saved" 
                            type="button" 
                            role="tab">
                        Saved
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link {{ $activeTab === 'reviews' ? 'active' : '' }}" 
                            id="reviews-tab" 
                            data-bs-toggle="tab" 
                            data-bs-target="#reviews" 
                            type="button" 
                            role="tab">
                        Reviews
                    </button>
                </li>
            </ul>
            
            <div class="tab-content" id="profileTabsContent">
                <div class="tab-pane fade {{ $activeTab === 'about' ? 'show active' : '' }}" id="about" role="tabpanel">
                    <div class="tabstipdetails">
                        <div class="row space_custom-12 py-3 justify-content-center">
                            <div class="col-md-4">
                                <div class="card h-100 border-0">
                                    <img src="{{ asset('images/trip4.jpg') }}" class="card_images" alt="Rome">
                                    <div class="card-body text-center custom__titletrip">
                                        <h5>No trips yet</h5>
                                        <p>Exploring the Ancient Wonders of Rome</p>
                                    </div>
                                    <button class="upload__profile" style="background-color: #E8EDF2; width: fit-content; margin: auto;">
                                        Create a trip
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="tab-pane fade {{ $activeTab === 'saved' ? 'show active' : '' }}" id="saved" role="tabpanel">
                    <p class="mt-4">No saved items yet.</p>
                </div>
                
                {{-- In the reviews tab --}}
                <div class="tab-pane fade {{ $activeTab === 'reviews' ? 'show active' : '' }}" id="reviews" role="tabpanel">
                    <div class="reviews-container mt-4">
                        @forelse($reviews as $review)
                            <div class="card mb-4">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="card-title mb-0">{{ $review->ReviewTitle }}</h5>
                                        <div class="rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $review->ReviewRating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                            <span class="ms-1">{{ number_format($review->ReviewRating, 1) }}</span>
                                        </div>
                                    </div>
                                    <p class="card-text">{{ $review->ReviewDescription }}</p>
                                    <div class="text-muted small">
                                        <span>Posted on {{ \Carbon\Carbon::parse($review->CreatedDate)->format('M d, Y') }}</span>
                                        @if($review->gowith)
                                            <span class="mx-2">•</span>
                                            <span>Went with {{ $review->gowith }}</span>
                                        @endif
                                    </div>
                                    @if($review->IsRecommend)
                                        <div class="recommended-badge mt-2">
                                            <i class="fas fa-thumbs-up"></i> Recommended
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5">
                                <i class="far fa-comment-dots fa-3x text-muted mb-3"></i>
                                <p class="mb-0">No reviews yet. Be the first to review!</p>
                            </div>
                        @endforelse

                        @if($reviews->hasPages())
                            <div class="d-flex justify-content-center mt-4">
                                {{ $reviews->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.rating {
    color: #ffc107;
}
.rating i {
    font-size: 1.2rem;
}
.rating-input {
    font-size: 1.5rem;
    cursor: pointer;
}
.rating-input .rating-star {
    color: #dee2e6;
    transition: color 0.2s;
}
.rating-input .rating-star:hover,
.rating-input .rating-star.active {
    color: #ffc107;
}
.recommended-badge {
    display: inline-block;
    background-color: #e8f5e9;
    color: #2e7d32;
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}
.recommended-badge i {
    margin-right: 0.25rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Rating stars interaction
    const stars = document.querySelectorAll('.rating-star');
    const ratingInput = document.getElementById('ReviewRating');
    
    stars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            ratingInput.value = rating;
            
            stars.forEach(s => {
                if (s.getAttribute('data-rating') <= rating) {
                    s.classList.add('fas', 'active');
                    s.classList.remove('far');
                } else {
                    s.classList.add('far');
                    s.classList.remove('fas', 'active');
                }
            });
        });
        
        // Hover effect
        star.addEventListener('mouseover', function() {
            const rating = this.getAttribute('data-rating');
            
            stars.forEach(s => {
                if (s.getAttribute('data-rating') <= rating) {
                    s.classList.add('fas');
                    s.classList.remove('far');
                }
            });
        });
        
        star.addEventListener('mouseout', function() {
            const currentRating = ratingInput.value;
            
            stars.forEach(s => {
                s.classList.remove('fas');
                s.classList.add('far');
                
                if (s.getAttribute('data-rating') <= currentRating) {
                    s.classList.add('fas', 'active');
                    s.classList.remove('far');
                }
            });
        });
    });
    
    // Initialize with 5 stars
    document.querySelector('.rating-star[data-rating="5"]').click();
    
    // Form validation
    const form = document.getElementById('reviewForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }
});
</script>
@endpush
