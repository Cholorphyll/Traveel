@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">📧 Email Preferences</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle"></i> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="text-muted mb-4">
                        Manage your email notification preferences. Choose what updates you'd like to receive from us.
                    </p>

                    <form method="POST" action="{{ route('user.email-preferences.update') }}">
                        @csrf

                        <div class="mb-4">
                            <h5 class="mb-3">Email Types</h5>
                            
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="location_recommendations" 
                                       name="location_recommendations" value="1"
                                       {{ $preferences->location_recommendations ? 'checked' : '' }}>
                                <label class="form-check-label" for="location_recommendations">
                                    <strong>Location Recommendations</strong>
                                    <p class="text-muted small mb-0">Get personalized recommendations for locations you've searched</p>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="hotel_deals" 
                                       name="hotel_deals" value="1"
                                       {{ $preferences->hotel_deals ? 'checked' : '' }}>
                                <label class="form-check-label" for="hotel_deals">
                                    <strong>Hotel Deals</strong>
                                    <p class="text-muted small mb-0">Receive exclusive hotel deals and offers</p>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="explore_suggestions" 
                                       name="explore_suggestions" value="1"
                                       {{ $preferences->explore_suggestions ? 'checked' : '' }}>
                                <label class="form-check-label" for="explore_suggestions">
                                    <strong>Explore Suggestions</strong>
                                    <p class="text-muted small mb-0">Discover new attractions and experiences</p>
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" id="weekly_digest" 
                                       name="weekly_digest" value="1"
                                       {{ $preferences->weekly_digest ? 'checked' : '' }}>
                                <label class="form-check-label" for="weekly_digest">
                                    <strong>Weekly Digest</strong>
                                    <p class="text-muted small mb-0">Get a weekly summary of top destinations and deals</p>
                                </label>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="mb-3">Email Frequency</h5>
                            <div class="form-group">
                                <select class="form-select" id="email_frequency" name="email_frequency">
                                    <option value="daily" {{ $preferences->email_frequency === 'daily' ? 'selected' : '' }}>
                                        Daily
                                    </option>
                                    <option value="weekly" {{ $preferences->email_frequency === 'weekly' ? 'selected' : '' }}>
                                        Weekly (Recommended)
                                    </option>
                                    <option value="monthly" {{ $preferences->email_frequency === 'monthly' ? 'selected' : '' }}>
                                        Monthly
                                    </option>
                                </select>
                                <small class="text-muted">How often would you like to receive emails?</small>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Preferences
                            </button>
                            <a href="{{ route('user.unsubscribe') }}" class="btn btn-outline-danger btn-sm">
                                Unsubscribe from all emails
                            </a>
                        </div>
                    </form>

                    @if($preferences->last_email_sent_at)
                    <div class="mt-4 pt-3 border-top">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Last email sent: {{ $preferences->last_email_sent_at->diffForHumans() }}
                        </small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-check-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}
</style>
@endsection
