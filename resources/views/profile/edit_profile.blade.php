@extends('layouts.settings')

@section('content')
<nav class="navbar navbar-expand-lg bg-light py-0 desktop__Nav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
      <img src="{{ asset('images/logo.png') }}" style="width: 110px;" alt="logo" />
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

        @php
            // Use HTTP for local development and ensure the path is correct
            $default_pic = url('/images/profile.png');
            
            if ($user && !empty($user->st_profilelink)) {
                  $clean_path = ltrim(str_replace('\\', '/', $user->st_profilelink), '/');
                
                if (str_starts_with($clean_path, 'http')) {
                    $profile_picture = $clean_path;
                } 
                
                else {
                    $storage_path = 'public/' . ltrim($clean_path, '/');
                    $full_path = storage_path('app/' . $storage_path);
                    
                    if (file_exists($full_path)) {
                        
                        $profile_picture = asset('storage/' . ltrim($clean_path, '/'));
                    } else {
                        $profile_picture = $default_pic;
                    }
                }
            } else {
                $profile_picture = $default_pic;
            }
            
            // Only force HTTPS in production
            if (app()->environment('production')) {
                $profile_picture = str_replace('http://', 'https://', $profile_picture);
            }
        @endphp
        <a href="{{ route('profile.edit') }}">
        <img src="{{ $profile_picture }}" 
             onerror="this.onerror=null; this.src='{{ $default_pic }}'"
             alt="Profile Picture" 
             class="avatar rounded-circle" 
             style="width: 40px; height: 40px; object-fit: cover;" />
        </a>
      </div>
    </div>
  </div>
</nav>
<section class="pagewrapper">
  <div class="container">
    <div class="page__section">
      <div class="left__section">
        <div class="d-none">
          <div class="profile__section text-center">
            <a href="{{ route('profile.edit') }}">
            <img src="{{ $profile_picture }}" 
                 class="rounded-circle" alt="" />
            </a>
            <div class="profile__details">
              <h1>{{ $user->FirstName ?? null }} {{ $user->LastName ?? null }}</h1>
              <p>Joined in {{ date('Y', strtotime($user->CreatedAt ?? now())) }}</p>
              <p>123 followers · 45 following</p>
            </div>
          </div>
          <div class="tabs custom">
            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="about-tab" data-bs-toggle="tab" data-bs-target="#about" 
                        type="button" role="tab">
                  About
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews" 
                        type="button" role="tab">
                  Reviews
                </button>
              </li>
            </ul>
          </div>
          <div class="tab-content" id="profileTabsContent">
            <div class="tab-pane fade show active" id="about" role="tabpanel">
              <div class="tab__details">
                <h4>About</h4>
                <p>{{ $user->Bio ?? "I'm a travel enthusiast who loves exploring new cultures and cuisines. I've visited over 30 countries and always looking for my next adventure." }}</p>
                <div class="trip__section">
                  <h5 class="mt-4">Trips</h5>
                  <div class="tip__Box">
                    <div class="box__List">
                      <img src="{{ asset('images/Hiking.jpg') }}" alt="" />
                      <h3>Hiking in the Alps</h3>
                      <p>2022</p>
                    </div>
                    <div class="box__List">
                      <img src="{{ asset('images/Hiking.jpg') }}" alt="" />
                      <h3>Relaxing in Bali</h3>
                      <p>2022</p>
                    </div>
                    <div class="box__List">
                      <img src="{{ asset('images/Hiking.jpg') }}" alt="" />
                      <h3>Hiking in the Alps</h3>
                      <p>2022</p>
                    </div>
                  </div>
                </div>
                <div class="trip__section">
                  <h5 class="mt-4">Places</h5>
                  <div class="tip__Box">
                    <div class="box__List">
                      <img src="{{ asset('images/Hiking.jpg') }}" alt="" />
                      <h3>Paris</h3>
                      <p>Visited in 2022</p>
                    </div>
                    <div class="box__List">
                      <img src="{{ asset('images/Hiking.jpg') }}" alt="" />
                      <h3>Hiking in the Alps</h3>
                      <p>2022</p>
                    </div>
                    <div class="box__List">
                      <img src="{{ asset('images/Hiking.jpg') }}" alt="" />
                      <h3>Hiking in the Alps</h3>
                      <p>2022</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="reviews" role="tabpanel">
              <p class="mt-4">No reviews yet.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="right__section">
        <div class="d-flex justify-content-between align-items-start">
          <h2 class="profile__heading">Edit profile</h2>
        </div>
        <div class="profile__rightSide">
          <h4>Profile Picture</h4>
          @php
              $profile_pictures = $user && !empty($user->st_profilelink)
                  ? Storage::disk('s3')->url($user->st_profilelink)
                  : asset('images/profileright.jpg');
          @endphp
          <img src="{{ $profile_pictures }}" alt="Profile" class="img-fluid mb-3" />
          <form action="{{ route('profile.update.picture') }}" method="POST" enctype="multipart/form-data" id="profile-picture-form">
            @csrf
            <input type="file" name="profile_picture" id="profile_picture" class="d-none" accept="image/*">
            <button type="button" class="upload__profile" onclick="document.getElementById('profile_picture').click()">Upload New Picture</button>
          </form>
        </div>
        <form class="form__Section" id="profile-form" action="{{ route('profile.update') }}" method="POST">
          @csrf
          <div class="form__group">
            <label for="name" class="form-label">Name</label>
            @php
                $fullName = old('name');
                if (!$fullName && $user) {
                    $fullName = $user->Name ?? ($user->FirstName ?? '') . ' ' . ($user->LastName ?? '');
                }
            @endphp
            <input type="text" class="form-control @error('FirstName') is-invalid @enderror" id="FirstName" name="FirstName" 
                   value="{{ old('FirstName', $user->FirstName ?? '') }}" required />
            @error('FirstName')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label for="bio" class="form-label">Bio</label>
            <input type="text" class="form-control @error('Bio') is-invalid @enderror" id="Bio" name="Bio" 
                   value="{{ old('Bio', $user->Bio ?? '') }}" />
            @error('Bio')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label for="LocationId" class="form-label">Location</label>
            <select class="form-select select2-location @error('LocationId') is-invalid @enderror" 
                    id="LocationId" 
                    name="LocationId"
                    data-placeholder="Search for a location...">
              @if($currentLocation)
                <option value="{{ $currentLocation->LocationId }}" selected>{{ $currentLocation->Name }}</option>
              @endif
            </select>
            @error('LocationId')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label for="website" class="form-label">Website</label>
            <input type="url" class="form-control @error('website') is-invalid @enderror" id="website" name="website" 
                   value="{{ old('website', $user->website ?? '') }}" />
            @error('website')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label for="instagram" class="form-label">Instagram</label>
            <input type="text" class="form-control @error('Instagram') is-invalid @enderror" id="Instagram" name="Instagram" 
                   value="{{ old('Instagram', $user->Instagram ?? '') }}" />
            @error('Instagram')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label for="twitter" class="form-label">Twitter</label>
            <input type="text" class="form-control @error('Twitter') is-invalid @enderror" id="Twitter" name="Twitter" 
                   value="{{ old('Twitter', $user->Twitter ?? '') }}" />
            @error('Twitter')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group mt-4">
            <button type="submit" class="upload__profile">Save Changes</button>
          </div>
        </form>

        <h5 class="mt-3 mb-2 change__password">Change Password</h5>
        <form class="form__Section" action="{{ route('profile.update.password') }}" method="POST">
          @csrf
          <div class="form__group">
            <label class="form-label">Current Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                   name="password" placeholder="Current Password" />
            @error('password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label class="form-label">New Password</label>
            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                   name="password" placeholder="New Password" />
            @error('new_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="form__group">
            <label class="form-label">Confirm New Password</label>
            <input type="password" class="form-control @error('confirm_new_password') is-invalid @enderror" 
                   name="confirm_new_password" placeholder="Confirm New Password" />
            @error('confirm_new_password')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <button type="submit" class="upload__profile">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.css" rel="stylesheet">
<style>
.select2-container--default .select2-selection--single {
    height: 38px;
    padding: 5px 10px;
    border: 1px solid #ced4da;
    border-radius: 0.25rem;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 36px;
}
</style>
<script src="https://unpkg.com/cropperjs@1.6.2/dist/cropper.min.js"></script>

<!-- Modal for Image Cropping -->
<div class="modal fade" id="cropImageModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Crop Profile Picture</h5>
                
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="imageToCrop" src="" alt="">
                </div>
            </div>
            <div class="modal-footer d-flex justify-content-between align-items-center">
                <div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="cropImageBtn">Save</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cropper;
let currentFile;

$(document).ready(function() {
    // Initialize Select2 for location search
    $('.select2-location').select2({
        ajax: {
            url: '{{ route("api.locations.search") }}',
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page || 1
                };
            },
            processResults: function (data, params) {
                params.page = params.page || 1;
                return {
                    results: data.data,
                    pagination: {
                        more: (params.page * 10) < data.total
                    }
                };
            },
            cache: true
        },
        placeholder: 'Search for a location...',
        minimumInputLength: 2,
        templateResult: formatLocation,
        templateSelection: formatLocationSelection
    });

    // Zoom controls
    $('.zoom-in').off('click').on('click', function() {
        if (cropper) cropper.zoom(0.1);
    });
    $('.zoom-out').off('click').on('click', function() {
        if (cropper) cropper.zoom(-0.1);
    });

    // Manual selection only; removed auto "Select Area" control

    function formatLocation(location) {
        if (location.loading) {
            return location.text;
        }
        return location.name || location.text;
    }

    function formatLocationSelection(location) {
        return location.name || location.text;
    }

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Show success/error messages
    @if(session('success'))
        showToast('success', '{{ session('success') }}');
    @endif
    
    @if(session('error'))
        showToast('danger', '{{ session('error') }}');
    @endif
    // Handle profile picture selection
    $('#profile_picture').on('change', function(e) {
        if (this.files && this.files.length > 0) {
            currentFile = this.files[0];
            
            // Check file type
            if (!currentFile.type.match('image.*')) {
                showToast('danger', 'Please select a valid image file (JPEG, PNG, GIF)');
                return;
            }
            
            // Check file size (max 5MB)
            if (currentFile.size > 5 * 1024 * 1024) {
                showToast('danger', 'File size should be less than 5MB');
                return;
            }
            
            const reader = new FileReader();
            
            reader.onload = function(event) {
                // Show the cropper modal with the selected image
                const image = document.getElementById('imageToCrop');
                image.src = event.target.result;
                
                // Show modal and initialize cropper once the modal is visible
                $('#cropImageModal').one('shown.bs.modal', function() {
                    // Disable save until user creates a crop box (visual + logical)
                    $('#cropImageBtn').prop('disabled', true).addClass('disabled');
                    // Clean up existing instance
                    if (cropper) {
                        cropper.destroy();
                    }
                    
                    const init = () => {
                      // Initialize cropper for MANUAL selection (no auto-crop)
                      cropper = new Cropper(image, {
                        viewMode: 1,        // Restrict to container
                        dragMode: 'crop',   // User draws the box
                        aspectRatio: NaN,   // Free-form (no forced ratio)
                        autoCrop: false,    // Do NOT auto create crop box
                        responsive: true,
                        restore: false,
                        checkCrossOrigin: false,
                        modal: false,
                        guides: true,
                        center: true,
                        highlight: false,
                        background: false,
                        movable: true,
                        zoomable: true,
                        zoomOnTouch: true,
                        zoomOnWheel: true,
                        cropBoxMovable: true,
                        cropBoxResizable: true,
                        minContainerWidth: 300,
                        minContainerHeight: 300,
                        ready() {
                            // Ensure in crop mode; no initial box
                            this.cropper.setDragMode('crop');
                            this.cropper.clear();
                            // Cursor hint
                            $('#imageToCrop').css('cursor', 'crosshair');
                            // Add instruction hint once
                            const $img = $('#imageToCrop');
                            const $wrap = $img.parent();
                            if ($wrap.css('position') === 'static') {
                                $wrap.css('position', 'relative');
                            }
                            if (!$wrap.find('.crop-hint').length) {
                                $wrap.append('<div class="crop-hint">Drag on the image to select an area</div>');
                            }
                        },
                        crop: () => {
                            // Enable save only when a crop box exists
                            const hasCrop = !!(cropper && cropper.cropped);
                            $('#cropImageBtn').prop('disabled', !hasCrop).toggleClass('disabled', !hasCrop);
                            // Toggle hint visibility
                            const $img = $('#imageToCrop');
                            const $wrap = $img.parent();
                            $wrap.find('.crop-hint').toggle(!hasCrop);
                        }
                      });
                    };

                    // Wait for the image to fully load before initializing Cropper
                    if (image.complete) {
                        init();
                    } else {
                        image.onload = () => {
                            init();
                            // Prevent duplicate init on cached load
                            image.onload = null;
                        };
                    }
                }).modal('show');
            };
            
            reader.readAsDataURL(currentFile);
        }
    });
    
    // Clean up cropper when modal is hidden
    $('#cropImageModal').on('hidden.bs.modal', function() {
        if (cropper) {
            cropper.destroy();
            cropper = null;
        }
        // Remove hint overlay
        $('.crop-hint').remove();
    });
    
    // Reset crop button handler
    $('#resetCrop').on('click', function() {
        if (cropper) {
            cropper.reset();      // Reset transforms
            cropper.clear();      // Remove crop box
            cropper.setDragMode('crop');
            $('#cropImageBtn').prop('disabled', true).addClass('disabled');
        }
    });
    
    // Handle crop and save button click
    $('#cropImageBtn').click(function() {
        const $btn = $(this);
        // Block if not enabled
        if ($btn.prop('disabled')) return;
        $btn.html('<span class="spinner-border spinner-border-sm"></span> Saving...').prop('disabled', true).addClass('disabled');
        
        // Require the user to select an area first
        if (!cropper || !cropper.cropped) {
            showToast('danger', 'Please drag to select the area you want to crop.');
            $btn.html('Crop & Save').prop('disabled', true).addClass('disabled');
            return;
        }
        
        // Validate crop area
        const cropBoxData = cropper.getCropBoxData();
        if (!cropBoxData || cropBoxData.width < 50 || cropBoxData.height < 50) {
            showToast('danger', 'Selected area is too small (min 50x50px).');
            $btn.html('Crop & Save').prop('disabled', true).addClass('disabled');
            return;
        }
        
        // Get cropped canvas using the user's selection size
        const canvas = cropper.getCroppedCanvas({
            width: Math.round(cropBoxData.width),
            height: Math.round(cropBoxData.height),
            minWidth: 50,
            minHeight: 50,
            maxWidth: 2000,
            maxHeight: 2000,
            fillColor: '#fff',
            imageSmoothingEnabled: true,
            imageSmoothingQuality: 'high',
        });
        
        // Convert canvas to blob
        canvas.toBlob(function(blob) {
            const formData = new FormData();
            // Match controller expectation: ProfileController::updatePicture() validates 'profile_picture'
            formData.append('profile_picture', blob, 'profile.jpg');
            formData.append('_token', '{{ csrf_token() }}');
            
            // AJAX request to upload the cropped image
            $.ajax({
                url: '{{ route("profile.update.picture") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function() {
                    // Controller returns a redirect/HTML; on 2xx refresh UI to reflect new image
                    showToast('success', 'Profile picture updated successfully.');
                    window.location.reload();
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred while uploading the image.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMessage = Object.values(xhr.responseJSON.errors).join('\n');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    showToast('danger', errorMessage);
                },
                complete: function() {
                    $btn.html('Crop & Save').prop('disabled', false);
                }
            });
        }, 'image/jpeg', 0.9);
    });
    
    // (Removed duplicate reset and modal close handlers to avoid conflicts)
    
    // Show toast notification
    function showToast(type, message) {
        // Remove any existing toasts
        $('.toast-container').remove();
        
        // Create toast element
        const toast = `
            <div class="toast-container position-fixed top-0 end-0 p-3">
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        `;
        
        // Append to body and show
        $('body').append(toast);
        const toastEl = $('.toast');
        const toastBootstrap = new bootstrap.Toast(toastEl[0], { autohide: true, delay: 5000 });
        toastBootstrap.show();
        
        // Remove toast after it's hidden
        toastEl.on('hidden.bs.toast', function () {
            $(this).closest('.toast-container').remove();
        });
    }
});
</script>

<style>
/* Cropper Modal Styles */
.modal-dialog {
    max-width: 600px;
    margin: 1.75rem auto;
}

.modal-content {
    border: none;
    border-radius: 8px;
    overflow: hidden;
}

.img-container {
    max-height: 70vh;
    margin: 0 auto;
    overflow: hidden;
    background: #f8f9fa;
    position: relative;
}

#imageToCrop {
    max-width: 100%;
    max-height: 70vh;
    display: block;
    margin: 0 auto;
}

/* Crop box styling */
.cropper-view-box {
    outline: 2px solid #39f;
    outline-color: rgba(51, 153, 255, 0.75);
}

.cropper-face {
    background-color: #fff;
}

/* Handles */
.cropper-point {
    background-color: #39f;
    width: 10px;
    height: 10px;
    border-radius: 50%;
    opacity: 0.75;
}

.cropper-line {
    background-color: #39f;
    opacity: 0.5;
}

/* Zoom controls */
.zoom-controls {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    background: white;
    padding: 5px 10px;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.zoom-controls button {
    margin: 0 5px;
    border: 1px solid #ddd;
    background: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.zoom-controls button:hover {
    background: #f8f9fa;
}

/* Instruction hint overlay (does not block clicks) */
.crop-hint {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 1001;
    background: rgba(0,0,0,0.55);
    color: #fff;
    padding: 6px 10px;
    border-radius: 14px;
    font-size: 12px;
    pointer-events: none;
}

/* Cropper container styles */
.cropper-container {
    direction: ltr;
    font-size: 0;
    line-height: 0;
    position: relative;
    touch-action: none;
    -ms-touch-action: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}

/* Remove rounded corners for free-form cropping */
.cropper-view-box,
.cropper-face {
    border-radius: 0;
}

/* Crop box styling */
.cropper-view-box {
    outline: 2px dashed #39f;
    outline-color: rgba(51, 153, 255, 0.9);
}

/* Crop box handles */
.cropper-line,
.cropper-point {
    background-color: #39f;
}

.cropper-point {
    width: 10px;
    height: 10px;
    opacity: 1;
}

/* Grid overlay for better visibility */
.cropper-modal {
    background: transparent;
    opacity: 0.5;
}

/* Zoom controls */
.zoom-controls {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1000;
    background: white;
    padding: 5px 10px;
    border-radius: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.zoom-controls button {
    margin: 0 5px;
    border: 1px solid #ddd;
    background: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}

.zoom-controls button:hover {
    background: #f8f9fa;
}
</style>
@endsection
