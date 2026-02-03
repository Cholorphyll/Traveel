@extends('layouts.settings')

@section('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
@endsection

@section('content')
    <nav class="navbar navbar-expand-lg bg-light py-0 desktop__Nav">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('homepage') }}">
                <img src="{{ asset('images/logo.png') }}" style="width: 100px;" alt="logo" />
            </a>
            <div class="collapse navbar-collapse" id="mainNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('trip.index') }}">Trips</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Explore</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('recenthistory') }}">Saved</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center customgap">
                    <div class="position-relative search-container">
                        <span class="search__icon"><img src="{{ asset('settings/images/Search.svg') }}"
                                alt="" /></span>
                        <input type="text" class="form-control search__input" placeholder="Search" />
                    </div>

                    <div class="bell__button">
                        <img src="{{ asset('settings/images/trips/info.svg') }}" alt="" />
                    </div>

                   <a href="{{ route('profile.edit') }}" class="profile-image-link">
                        @php
                            // Prefer Auth (e.g., Google login), fallback to legacy session
                            $imgPath = null;
                            $altName = 'avatar';
                            try {
                                if (Auth::check()) {
                                    $authUser = Auth::user();
                                    $altName = $authUser->name ?? ($authUser->email ?? 'avatar');
                                    $imgPath = $authUser->avatar ?? ($authUser->photo ?? null);
                                } elseif (session()->has('frontend_user')) {
                                    $user = session('frontend_user');
                                    if (is_array($user)) {
                                        $imgPath = $user['user_image'] ?? null;
                                        $altName = $user['Username'] ?? 'avatar';
                                    }
                                }
                            } catch (\Throwable $e) { /* ignore */ }
                            $resolved = null;
                            if (!empty($imgPath)) {
                                if (\Illuminate\Support\Str::startsWith($imgPath, ['http://', 'https://'])) {
                                    $resolved = $imgPath;
                                } else {
                                    // try public path directly
                                    if (file_exists(public_path(ltrim($imgPath, '/')))) {
                                        $resolved = asset(ltrim($imgPath, '/'));
                                    } else {
                                        // try storage public disk
                                        $relative = ltrim($imgPath, '/');
                                        try {
                                            if (\Illuminate\Support\Facades\Storage::disk('public')->exists($relative)) {
                                                $resolved = asset('storage/' . $relative);
                                            }
                                        } catch (\Throwable $e) {
                                            // ignore
                                        }
                                    }
                                }
                            }
                            $finalSrc = $resolved ?: asset('images/Hotel lobby-image.png');
                        @endphp
                        <img src="{{ $finalSrc }}"
                             alt="{{ $altName }}" width="40" height="40"
                             class="avatar rounded-circle"
                             onerror="this.onerror=null;this.src='{{ asset('images/Hotel lobby-image.png') }}'" />
                    </a>
                </div>
            </div>
        </div>
    </nav>
    <section class="pagewrapper">
        <div class="container">
            <div class="page__section pageGap">
                <div class="d-flex justify-content-between align-items-start mobileDisplay">
                    <span><img src="{{ asset('settings/images/trips/mobilearrow.svg') }}" alt=""></span>
                    <h2 class="profile__heading" style="padding-bottom: 16px;">{{ ($trip && $trip->destination_city) ? ('Trip to ' . $trip->destination_city) : 'Your Trip' }}</h2>
                </div>
                <div class="left__section">
                    <h3 class="Aiheading">AI Assistant</h3>
                    <div class="d-flex justify-content-between gap-3 align-items-center py-custom">
                        <div class="details Ai__Assistant">
                            <p>AI Assistant</p>
                        </div>
                        <div class="form-check mb-0 form-switch toggle-setting">
                            <input class="form-check-input" type="checkbox" id="toggle1">
                        </div>
                    </div>
                    <div class="custom__Nav">

                        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link tab__trip active" id="account-tab" data-bs-toggle="tab"
                                    data-bs-target="#account" type="button" role="tab">
                                    <span><img src="{{ asset('settings/images/trips/Overview.svg') }}"
                                            alt=""></span> Overview
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link tab__trip" id="reviews-tab" data-bs-toggle="tab"
                                    data-bs-target="#reviews" type="button" role="tab">
                                    <span><img src="{{ asset('settings/images/trips/Reservations.svg') }}"
                                            alt=""></span>Reservations
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link tab__trip" id="mail-tab" data-bs-toggle="tab"
                                    data-bs-target="#mail" type="button" role="tab">
                                    <span><img src="{{ asset('settings/images/trips/Budget.svg') }}"
                                            alt=""></span>Budget
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link tab__trip" id="user-tab" data-bs-toggle="tab"
                                    data-bs-target="#usersetting" type="button" role="tab">
                                    <span><img src="{{ asset('settings/images/trips/Notes.svg') }}"
                                            alt=""></span>Notes
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link tab__trip" id="notifications-tab" data-bs-toggle="tab"
                                    data-bs-target="#notifications" type="button" role="tab">
                                    <span><img src="{{ asset('settings/images/trips/Flights.svg') }}"
                                            alt=""></span>Flights
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link tab__trip" id="preferences-tab" data-bs-toggle="tab"
                                    data-bs-target="#preferences" type="button" role="tab">
                                    <span><img src="{{ asset('settings/images/trips/Places.svg') }}"
                                            alt=""></span>Places to visit
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="right__section">
                    <div class="d-flex justify-content-between align-items-start onlydesktop">

                        <h2 class="profile__heading" style="padding-bottom: 16px;">{{ ($trip && $trip->destination_city) ? ('Trip to ' . $trip->destination_city) : 'Your Trip' }}</h2>
                    </div>
                    <div class="tab-content" id="profileTabsContent">
                        <div class="tab-pane fade show active" id="account" role="tabpanel">
                            <div class="Notifications">
                                <h4>Reservations</h4>
                            </div>
                            <div class="custom__Nav">
                                <div class="tabs custom  trip__profiletabs paris__trip">
                                    <ul class="nav nav-tabs">
                                        <li class="nav-item">
                                            <button class="nav-link active">All</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link">Flights</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link">Hotels</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link">Activities</button>
                                        </li>
                                        <li class="nav-item">
                                            <button class="nav-link">Transportation</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="tabstipdetails paris__details">
                                <h4>Budgeting</h4>
                                <div class="d-flex justify-content-between align-items-center py-custom">
                                    <div class="paris__pricedetails">
                                        @php
                                            $currency = is_array($trip->settings ?? null) ? ($trip->settings['currency'] ?? '$') : '$';
                                            $budget = isset($trip->budget) ? (float)$trip->budget : 0;
                                        @endphp
                                        <p>{{ $currency }}{{ number_format($budget, 2) }}</p>
                                        <span>View details</span>
                                    </div>
                                    <div class="Some__Value">
                                        @php
                                            $start = $trip?->start_date ? $trip->start_date->format('M d, Y') : null;
                                            $end = $trip?->end_date ? $trip->end_date->format('M d, Y') : null;
                                            $dateRange = $start && $end ? ($start . ' - ' . $end) : ($start ?? $end ?? ($trip->destination_city ?? ''));
                                        @endphp
                                        <p>{{ $dateRange }}</p>
                                    </div>
                                </div>

                                <div class="note__Section">
                                    <h4>Notes</h4>
                                    <div class="note__box">
                                        <h5>Write a note</h5>
                                        <p>Keep track of important information about your trip.</p>
                                        @if(!empty($trip))
                                            <form id="addNoteForm" class="w-100" onsubmit="return false;">
                                                @csrf
                                                <input type="hidden" name="trip_id" id="trip_id" value="{{ $trip->id }}">
                                                <div class="mb-2">
                                                    <textarea name="note" id="noteText" class="form-control" rows="3" placeholder="Type your note..."></textarea>
                                                </div>
                                                <button id="addNoteBtn" class="upload__profile" type="submit">Add Note</button>
                                            </form>
                                        @else
                                            <div class="alert alert-warning mb-0" role="alert">
                                                No trip selected. Please create or select a trip to add notes.
                                            </div>
                                        @endif
                                    </div>
                                    <div class="mt-3">
                                        <ul id="notesList" class="list-unstyled m-0">
                                            @php
                                                $notesArr = [];
                                                if (!empty($trip?->notes)) {
                                                    $decoded = json_decode($trip->notes, true);
                                                    if (is_array($decoded)) {
                                                        $notesArr = $decoded;
                                                    } else {
                                                        $notesArr = [['text' => (string) $trip->notes]];
                                                    }
                                                }
                                            @endphp
                                            @foreach($notesArr as $n)
                                                <li class="py-1">{{ $n['text'] ?? '' }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>

                                <div class="note__Section" id="section-flights">
                                    <h4>Flights</h4>
                                    <div class="d-flex justify-content-between gap-3 align-items-center py-custom">
                                        <div class="details Ai__Assistant">
                                            <p>Live updates</p>
                                        </div>
                                        <div class="form-check mb-0 form-switch toggle-setting">
                                            @php $liveFlights = is_array($trip->settings ?? null) ? (bool)($trip->settings['live_flights'] ?? false) : false; @endphp
                                            <input class="form-check-input" type="checkbox" id="toggle1" {{ $liveFlights ? 'checked' : '' }} disabled>
                                        </div>
                                    </div>

                                    @php
                                        $flights = is_array($trip->flights ?? null) ? $trip->flights : [];
                                    @endphp
                                    @if(!empty($flights))
                                        <div class="list-group">
                                            @foreach($flights as $f)
                                                @php
                                                    $from = $f['from'] ?? [];
                                                    $to = $f['to'] ?? [];
                                                    $fromCity = $from['city'] ?? ($from['airport'] ?? '');
                                                    $toCity = $to['city'] ?? ($to['airport'] ?? '');
                                                    $dep = $f['depart_time'] ?? ($f['departure'] ?? null);
                                                    $arr = $f['arrive_time'] ?? ($f['arrival'] ?? null);
                                                    $carrier = $f['carrier'] ?? '';
                                                    $num = $f['number'] ?? '';
                                                    $direction = !empty($f['type']) ? ucfirst($f['type']) : '';
                                                @endphp
                                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                                    <div class="me-3">
                                                        <p class="mb-1 text-muted">{{ $direction }}</p>
                                                        <h5 class="mb-1">{{ trim(($fromCity ?: '—') . ' to ' . ($toCity ?: '—')) }}</h5>
                                                        <small class="text-muted">{{ $carrier }} {{ $num }}</small>
                                                        <div>{{ $dep ? (is_string($dep) ? $dep : '') : '—' }}
                                                            @if($dep || $arr)
                                                                -
                                                            @endif
                                                            {{ $arr ? (is_string($arr) ? $arr : '') : '—' }}</div>
                                                    </div>
                                                    <div class="round__tripright">
                                                        <img src="{{ asset('settings/images/trips/tripimg.png') }}" alt="">
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mt-2">No flights added for this trip.</p>
                                    @endif
                                </div>
                                <div class="note__Section" id="section-hotels">
                                    <h4>Hotels</h4>
                                    @if(isset($hotels) && $hotels->count())
                                        <div class="list-group">
                                            @foreach($hotels as $h)
                                                @php
                                                    $hid = $h->hotelid ?? null;
                                                    $imgUrl = $hid ? "https://photo.hotellook.com/image_v2/crop/h{$hid}_0/240/180.jpg" : asset('images/Hotel lobby-image.png');
                                                @endphp
                                                <div class="list-group-item d-flex align-items-start">
                                                    <div class="me-3 flex-shrink-0" style="width:120px;">
                                                        <img src="{{ $imgUrl }}" alt="Hotel image" class="img-fluid rounded" style="object-fit:cover;width:120px;height:90px;"
                                                             onerror="this.onerror=null;this.src='{{ asset('images/Hotel lobby-image.png') }}'">
                                                    </div>
                                                    <div class="me-auto">
                                                        <h5 class="mb-1">{{ $h->name ?? 'Hotel' }}</h5>
                                                        <small class="text-muted">{{ $h->cityName ?? '' }} {{ isset($h->countryName) ? ', '.$h->countryName : '' }}</small>
                                                        <div>{{ $h->address ?? '' }}</div>
                                                        @if(!empty($h->stars))
                                                            <div>Rating: {{ $h->stars }}★</div>
                                                        @endif
                                                    </div>
                                                    @if(!empty($h->pricefrom))
                                                        <div class="text-nowrap ms-3"><strong>${{ number_format((float)$h->pricefrom, 0) }}</strong></div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No hotels found for this destination.</p>
                                    @endif
                                </div>
                                <div class="note__Section" id="section-activities">
                                    <h4>Activities</h4>
                                    @if(isset($activities) && $activities->count())
                                        <div class="list-group">
                                            @foreach($activities as $a)
                                                @php
                                                    $sid = $a->SightId ?? null;
                                                    $img = isset($activityImagesMap[$sid]) ? $activityImagesMap[$sid] : asset('images/Hotel lobby-image.png');
                                                @endphp
                                                <div class="list-group-item d-flex align-items-start">
                                                    <div class="me-3 flex-shrink-0" style="width:120px;">
                                                        <img src="{{ $img }}" alt="{{ $a->Title ?? 'Activity' }} image" class="img-fluid rounded" style="object-fit:cover;width:120px;height:90px;"
                                                             onerror="this.onerror=null;this.src='{{ asset('images/Hotel lobby-image.png') }}'">
                                                    </div>
                                                    <div class="me-auto">
                                                        <h5 class="mb-1">{{ $a->Title ?? 'Activity' }}</h5>
                                                        <small class="text-muted">{{ $a->cityName ?? '' }} {{ isset($a->countryName) ? ', '.$a->countryName : '' }}</small>
                                                        @if(!empty($a->Address))
                                                            <div>{{ $a->Address }}</div>
                                                        @endif
                                                        @if(!empty($a->short_description))
                                                            <p class="mb-0">{{ \Illuminate\Support\Str::limit($a->short_description, 140) }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">No activities found for this destination.</p>
                                    @endif
                                </div>
                                <div class="note__Section" id="section-places">
                                    <h4>Places to visit</h4>
                                    <div class="note__box">
                                        <h5>Create a list</h5>
                                        <p>Save places you want to visit during your trip.</p>
                                        @if(!empty($trip))
                                            <form id="createPlaceListForm" class="d-flex gap-2 align-items-center" onsubmit="return false;">
                                                @csrf
                                                <input type="hidden" name="trip_id" value="{{ $trip->id }}" />
                                                <input type="text" name="name" class="form-control" placeholder="List name (e.g., Day 1)" style="max-width: 240px;" />
                                                <button id="createPlaceListBtn" class="upload__profile" type="submit">New list</button>
                                            </form>
                                        @endif
                                    </div>
                                    <div id="placeLists" class="mt-3">
                                        @php $placeLists = is_array($trip->places ?? null) ? $trip->places : []; @endphp
                                        @if(!empty($placeLists))
                                            @foreach($placeLists as $list)
                                                <div class="card mb-2" data-list-id="{{ $list['id'] ?? '' }}">
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <h5 class="m-0">{{ $list['name'] ?? 'List' }}</h5>
                                                        </div>
                                                        <ul class="m-0 ps-0">
                                                            @foreach(($list['items'] ?? []) as $it)
                                                                @php
                                                                    $pt = trim((string)($it['title'] ?? ''));
                                                                    $pkey = \Illuminate\Support\Str::lower($pt);
                                                                    $pimg = isset($placeItemImages[$pkey]) ? $placeItemImages[$pkey] : asset('images/Hotel lobby-image.png');
                                                                @endphp
                                                                <li class="py-2 list-unstyled">
                                                                    <div class="d-flex align-items-start">
                                                                        <div class="me-3 flex-shrink-0" style="width:90px;">
                                                                            <img src="{{ $pimg }}" alt="{{ $pt }} image" class="img-fluid rounded" style="object-fit:cover;width:90px;height:68px;"
                                                                                 onerror="this.onerror=null;this.src='{{ asset('images/Hotel lobby-image.png') }}'">
                                                                        </div>
                                                                        <div class="me-auto">
                                                                            <strong class="d-block">{{ $pt }}</strong>
                                                                            @if(!empty($it['address']))
                                                                                <span class="text-muted">{{ $it['address'] }}</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </li>
                                                            @endforeach
                                                            @if(empty($list['items']))
                                                                <li class="text-muted">No items yet.</li>
                                                            @endif
                                                        </ul>
                                                        @if(!empty($trip))
                                                        <form class="addPlaceItemForm d-flex gap-2 align-items-center mt-2" onsubmit="return false;">
                                                            @csrf
                                                            <input type="hidden" name="trip_id" value="{{ $trip->id }}" />
                                                            <input type="hidden" name="list_id" value="{{ $list['id'] ?? '' }}" />
                                                            <input type="text" name="title" class="form-control" placeholder="Add place title" style="max-width: 220px;" />
                                                            <input type="text" name="address" class="form-control" placeholder="Address (optional)" style="max-width: 280px;" />
                                                            <button class="btn btn-sm btn-primary" type="submit">Add</button>
                                                        </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted mb-0">No lists yet. Create your first list.</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="note__Section">
                                    <h4>Itinerary</h4>
                                    <div class="date__section">
                                        @php
                                            $start = $trip?->start_date;
                                            $end = $trip?->end_date;
                                            $period = null;
                                            if ($start && $end) {
                                                $period = \Carbon\CarbonPeriod::create($start, $end);
                                            } elseif ($start) {
                                                $period = \Carbon\CarbonPeriod::create($start, $start);
                                            } elseif ($end) {
                                                $period = \Carbon\CarbonPeriod::create($end, $end);
                                            }
                                            $dateRangeStr = ($start ? $start->format('M d') : '') . ($start && $end ? ' - ' : '') . ($end ? $end->format('M d') : '');
                                            $itinerary = is_array($trip->itinerary ?? null) ? $trip->itinerary : [];
                                        @endphp
                                        <h6>{{ $dateRangeStr ?: 'Dates not set' }}</h6>
                                        <div class="custom__scrollTab">
                                            <div class="tabs custom trip__profiletabs datetabs">
                                                <ul class="nav nav-tabs p-0" role="tablist">
                                                    @if($period)
                                                        @php $idx = 0; @endphp
                                                        @foreach($period as $d)
                                                            @php
                                                                $tabId = 'day-' . $d->format('Ymd');
                                                            @endphp
                                                            <li class="nav-item" role="presentation">
                                                                <button class="nav-link {{ $idx === 0 ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#{{ $tabId }}" type="button" role="tab">
                                                                    {{ $d->format('M d') }}
                                                                </button>
                                                            </li>
                                                            @php $idx++; @endphp
                                                        @endforeach
                                                    @else
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" type="button" disabled>No dates</button>
                                                        </li>
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="tab-content" id="tripItineraryContent">
                                            @if($period)
                                                @php $idx = 0; @endphp
                                                @foreach($period as $d)
                                                    @php
                                                        $paneId = 'day-' . $d->format('Ymd');
                                                        $key = $d->format('Y-m-d');
                                                        $items = $itinerary[$key] ?? [];
                                                    @endphp
                                                    <div class="tab-pane fade {{ $idx === 0 ? 'show active' : '' }}" id="{{ $paneId }}" role="tabpanel">
                                                        @if(!empty($items))
                                                            <div class="hotel___Itinerarydetails">
                                                                <ul>
                                                                    @foreach($items as $it)
                                                                        <li>
                                                                            {{ $it['title'] ?? ($it['text'] ?? '') }}
                                                                            @if(!empty($it['time']))
                                                                                <span>{{ $it['time'] }}</span>
                                                                            @endif
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                            </div>
                                                        @else
                                                            <p class="mt-4 text-muted">No items for this day.</p>
                                                        @endif
                                                    </div>
                                                    @php $idx++; @endphp
                                                @endforeach
                                            @else
                                                <div class="tab-pane fade show active" role="tabpanel">
                                                    <p class="mt-4 text-muted">Set trip dates to build your itinerary.</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="reviews" role="tabpanel">
                            @php
                                // Placeholder dynamic state for reviews: none implemented yet
                                $hasTrip = !empty($trip?->id);
                            @endphp
                            @if($hasTrip)
                                <p class="mt-3 text-muted">No reviews linked to this trip yet.</p>
                            @else
                                <div class="alert alert-warning mt-3">No trip selected.</div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="mail" role="tabpanel">
                            @if(!empty($trip))
                                <p class="mt-3 text-muted">No messages for this trip.</p>
                            @else
                                <div class="alert alert-warning mt-3">No trip selected.</div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="usersetting" role="tabpanel">
                            @php $settings = is_array($trip->settings ?? null) ? $trip->settings : []; @endphp
                            @if(!empty($trip))
                                @if(!empty($settings))
                                    <div class="table-responsive mt-2">
                                        <table class="table table-sm align-middle">
                                            <thead>
                                                <tr><th style="width: 220px;">Setting</th><th>Value</th></tr>
                                            </thead>
                                            <tbody>
                                                @foreach($settings as $k => $v)
                                                    <tr>
                                                        <td><code>{{ $k }}</code></td>
                                                        <td>
                                                            @if(is_array($v))
                                                                <pre class="mb-0" style="white-space: pre-wrap; word-break: break-word;">{{ json_encode($v, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                                            @else
                                                                {{ is_bool($v) ? ($v ? 'true' : 'false') : (string)$v }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="mt-3 text-muted">No settings configured for this trip.</p>
                                @endif
                            @else
                                <div class="alert alert-warning mt-3">No trip selected.</div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="notifications" role="tabpanel">
                            @php
                                $notif = (is_array($trip->settings ?? null) && isset($trip->settings['notifications']) && is_array($trip->settings['notifications']))
                                    ? $trip->settings['notifications'] : [];
                            @endphp
                            @if(!empty($trip))
                                @if(!empty($notif))
                                    <div class="mt-2">
                                        @foreach($notif as $key => $enabled)
                                            <div class="form-check form-switch mb-2">
                                                <input class="form-check-input" type="checkbox" id="notif_{{ $key }}" {{ $enabled ? 'checked' : '' }} disabled>
                                                <label class="form-check-label" for="notif_{{ $key }}">{{ ucfirst(str_replace('_',' ', $key)) }}</label>
                                            </div>
                                        @endforeach
                                        <small class="text-muted">Notification preferences are read-only here.</small>
                                    </div>
                                @else
                                    <p class="mt-3 text-muted">No notification preferences found.</p>
                                @endif
                            @else
                                <div class="alert alert-warning mt-3">No trip selected.</div>
                            @endif
                        </div>
                        <div class="tab-pane fade" id="preferences" role="tabpanel">
                            @php
                                $currency = is_array($trip->settings ?? null) ? ($trip->settings['currency'] ?? '$') : '$';
                                $start = $trip?->start_date ? $trip->start_date->format('M d, Y') : null;
                                $end = $trip?->end_date ? $trip->end_date->format('M d, Y') : null;
                            @endphp
                            @if(!empty($trip))
                                <ul class="mt-2">
                                    <li><strong>Destination:</strong> {{ $trip->destination_city ?? '—' }}{{ !empty($trip->destination_country) ? ', '.$trip->destination_country : '' }}</li>
                                    <li><strong>Dates:</strong> {{ $start && $end ? ($start.' - '.$end) : ($start ?? $end ?? '—') }}</li>
                                    <li><strong>Currency:</strong> {{ $currency }}</li>
                                    <li><strong>Budget:</strong> {{ isset($trip->budget) ? ($currency . number_format((float)$trip->budget, 2)) : '—' }}</li>
                                </ul>
                            @else
                                <div class="alert alert-warning mt-3">No trip selected.</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="mapSidebar">
                    <div class="map_section">
                        <div id="mapTrip" style="width: 100%; height: 260px; border-radius: 8px; overflow: hidden;"></div>
                    </div>
                    <div class="group-button d-flex justify-content-between">
                        <button id="zoomFitBtn" class="upload__profile">Zoom into places</button>
                        <div class="d-flex gap-2">
                            <button id="zoomInBtn" class="upload__profile">+</button>
                            <button id="zoomOutBtn" class="upload__profile">-</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection

@section('scripts')
<script src="{{ asset('js/leaflet.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('addNoteForm');
    if (!form) return;
    const noteText = document.getElementById('noteText');
    const tripIdEl = document.getElementById('trip_id');
    const list = document.getElementById('notesList');

    form.addEventListener('submit', async function (e) {
        // prevent default submission; returning false in addEventListener doesn't stop submit
        if (e && typeof e.preventDefault === 'function') e.preventDefault();
        const tripId = (tripIdEl && tripIdEl.value) ? tripIdEl.value : '';
        const text = (noteText && noteText.value) ? noteText.value.trim() : '';
        if (!tripId) { alert('Trip not found.'); return false; }
        if (!text) { noteText.focus(); return false; }

        const formData = new FormData(form);
        try {
            const res = await fetch("{{ route('trip.addNote') }}", {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                body: formData
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                alert(err.message || 'Failed to add note');
                return false;
            }
            const data = await res.json();
            if (data && data.ok && data.note && list) {
                const li = document.createElement('li');
                li.className = 'py-1';
                li.textContent = data.note.text || '';
                list.appendChild(li);
                if (noteText) noteText.value = '';
            }
        } catch (e) {
            console.error(e);
            alert('Network error');
        }
        return false;
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    try {
        if (typeof L === 'undefined') return; // Leaflet not loaded
        const mapEl = document.getElementById('mapTrip');
        if (!mapEl) return;

        // Default center to Paris if no trip coords available
        const centerLat = {{ isset($trip) && !empty($trip->latitude ?? null) ? $trip->latitude : 48.8566 }};
        const centerLng = {{ isset($trip) && !empty($trip->longitude ?? null) ? $trip->longitude : 2.3522 }};

        const map = L.map('mapTrip', { zoomControl: false }).setView([centerLat, centerLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // Layer groups for hotels and activities
        const hotelsLayer = L.layerGroup().addTo(map);
        const activitiesLayer = L.layerGroup().addTo(map);
        const allBounds = L.latLngBounds([]);

        // Data from controller
        const hotelsData = @json($hotels ?? []);
        const activitiesData = @json($activities ?? []);

        // Add hotel markers
        if (Array.isArray(hotelsData)) {
            hotelsData.forEach(h => {
                const lat = parseFloat(h.Latitude ?? h.latitude);
                const lng = parseFloat(h.longnitude ?? h.Longitude ?? h.longitude);
                if (!isFinite(lat) || !isFinite(lng)) return;
                const m = L.marker([lat, lng], { title: h.name || 'Hotel' })
                    .bindPopup(`<strong>${(h.name||'Hotel')}</strong><br>${(h.address||'')}`);
                m.addTo(hotelsLayer);
                allBounds.extend([lat, lng]);
            });
        }

        // Add activity markers
        if (Array.isArray(activitiesData)) {
            activitiesData.forEach(a => {
                const lat = parseFloat(a.Latitude ?? a.latitude);
                const lng = parseFloat(a.Longitude ?? a.longitude);
                if (!isFinite(lat) || !isFinite(lng)) return;
                const m = L.marker([lat, lng], { title: a.Title || 'Activity' })
                    .bindPopup(`<strong>${(a.Title||'Activity')}</strong><br>${(a.Address||'')}`);
                m.addTo(activitiesLayer);
                allBounds.extend([lat, lng]);
            });
        }

        const zoomInBtn = document.getElementById('zoomInBtn');
        const zoomOutBtn = document.getElementById('zoomOutBtn');
        const zoomFitBtn = document.getElementById('zoomFitBtn');

        if (zoomInBtn) zoomInBtn.addEventListener('click', function (e) { e.preventDefault(); map.zoomIn(); });
        if (zoomOutBtn) zoomOutBtn.addEventListener('click', function (e) { e.preventDefault(); map.zoomOut(); });
        if (zoomFitBtn) zoomFitBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (allBounds.isValid()) {
                map.fitBounds(allBounds.pad(0.2));
            } else {
                const bounds = L.latLngBounds([[centerLat, centerLng]]);
                map.fitBounds(bounds.pad(0.5));
            }
        });

        // Inner Reservations sub-tabs toggling
        const innerTabs = document.querySelectorAll('.paris__trip .nav-tabs .nav-link');
        const sections = {
            'Flights': document.getElementById('section-flights'),
            'Hotels': document.getElementById('section-hotels'),
            'Activities': document.getElementById('section-activities')
        };
        function showAllSections() {
            Object.values(sections).forEach(el => { if (el) el.style.display = ''; });
        }
        function showOnly(name) {
            Object.entries(sections).forEach(([key, el]) => {
                if (!el) return;
                el.style.display = (key === name) ? '' : 'none';
            });
        }
        innerTabs.forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                innerTabs.forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                const label = this.textContent.trim();
                if (label === 'All') { showAllSections(); }
                else if (sections[label]) { showOnly(label); }
            });
        });

        // Initialize with All visible
        showAllSections();
    } catch (err) {
        console.error('Trip map init error', err);
    }
});
</script>
<script>
// Places: create list and add items
document.addEventListener('DOMContentLoaded', function() {
    const createForm = document.getElementById('createPlaceListForm');
    const listsContainer = document.getElementById('placeLists');
    if (createForm && listsContainer) {
        createForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const nameInput = createForm.querySelector('input[name="name"]');
            const name = (nameInput?.value || '').trim();
            if (!name) { nameInput?.focus(); return; }
            const fd = new FormData(createForm);
            try {
                const res = await fetch("{{ route('trip.places.addList') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                });
                const data = await res.json();
                if (!res.ok || !data?.ok) throw new Error(data?.message || 'Failed');
                // Prepend new list card
                const list = data.list;
                const card = document.createElement('div');
                card.className = 'card mb-2';
                card.setAttribute('data-list-id', list.id);
                card.innerHTML = `
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="m-0"></h5>
                        </div>
                        <ul class="m-0 ps-3"><li class="text-muted">No items yet.</li></ul>
                        <form class="addPlaceItemForm d-flex gap-2 align-items-center mt-2" onsubmit="return false;">
                            @csrf
                            <input type="hidden" name="trip_id" value="{{ $trip->id ?? '' }}" />
                            <input type="hidden" name="list_id" value="${list.id}" />
                            <input type="text" name="title" class="form-control" placeholder="Add place title" style="max-width: 220px;" />
                            <input type="text" name="address" class="form-control" placeholder="Address (optional)" style="max-width: 280px;" />
                            <button class="btn btn-sm btn-primary" type="submit">Add</button>
                        </form>
                    </div>`;
                card.querySelector('h5').textContent = list.name || 'List';
                listsContainer.prepend(card);
            } catch (err) {
                console.error(err);
                alert(err.message || 'Failed to create list');
            } finally {
                if (nameInput) nameInput.value = '';
            }
        });
    }

    // Delegate add item submits
    if (listsContainer) {
        listsContainer.addEventListener('submit', async function(e) {
            const form = e.target.closest('form.addPlaceItemForm');
            if (!form) return;
            e.preventDefault();
            const titleInput = form.querySelector('input[name="title"]');
            const title = (titleInput?.value || '').trim();
            if (!title) { titleInput?.focus(); return; }
            const fd = new FormData(form);
            try {
                const res = await fetch("{{ route('trip.places.addItem') }}", {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    body: fd
                });
                const data = await res.json();
                if (!res.ok || !data?.ok) throw new Error(data?.message || 'Failed');
                // Append item into the matching card
                const listId = data.list_id;
                const card = listsContainer.querySelector(`.card[data-list-id="${CSS.escape(listId)}"]`);
                if (card) {
                    const ul = card.querySelector('ul');
                    if (ul) {
                        // remove 'No items yet.' if present
                        const firstLi = ul.querySelector('li.text-muted');
                        if (firstLi) firstLi.remove();
                        const li = document.createElement('li');
                        li.className = 'py-1';
                        li.innerHTML = `<strong>${data.item.title || ''}</strong>` + (data.item.address ? ` <span class="text-muted"> — ${data.item.address}</span>` : '');
                        ul.appendChild(li);
                    }
                }
                if (titleInput) titleInput.value = '';
                const addrInput = form.querySelector('input[name="address"]');
                if (addrInput) addrInput.value = '';
            } catch (err) {
                console.error(err);
                alert(err.message || 'Failed to add item');
            }
        });
    }
});
</script>
@endsection
