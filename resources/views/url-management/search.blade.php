<x-app-layout>
<div class="pc-container">
    <div class="pc-content">
        <div class="page-header">
            <div class="page-block">
                <div class="row align-items-center">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item" aria-current="page">URL Management</li>
                            <li class="breadcrumb-item" aria-current="page">Upload URLs</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">Search URLs</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('url.search') }}" method="GET" class="mb-4">
                <div class="input-group">
                    <input type="url" name="url" class="form-control" placeholder="Enter URL to search..." value="{{ $url ?? '' }}" required>
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>

            @if(isset($error))
                <div class="alert alert-danger">
                    {{ $error }}
                </div>
            @endif

            @if(isset($listing))
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">{{ $listing->url }}</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach(['About', 'Amenities', 'Keywords'] as $category)
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-header d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0">{{ $category }}</h6>
                                            <button type="button" class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#addContentModal{{ $listing->id }}{{ $category }}">
                                                +
                                            </button>
                                        </div>
                                        <div class="card-body">
                                            @php
                                                $content = $listing->contents->where('category', $category)->first();
                                            @endphp
                                            @if($content)
                                                <div class="mb-2">
                                                    <div class="form-check">
                                                        <input type="checkbox" 
                                                            class="form-check-input" 
                                                            {{ $content->status === 'accepted' ? 'checked' : '' }}
                                                            onchange="document.getElementById('updateForm{{ $content->id }}').submit();">
                                                        <label class="form-check-label">
                                                            <span class="badge {{ $content->status === 'accepted' ? 'bg-success' : 'bg-warning' }}">
                                                                {{ $content->status }}
                                                            </span>
                                                        </label>
                                                    </div>
                                                    <p class="mt-2">{{ $content->content_text }}</p>
                                                </div>
                                                <form id="updateForm{{ $content->id }}" 
                                                    action="{{ route('url.content.update') }}" 
                                                    method="POST" 
                                                    style="display: none;">
                                                    @csrf
                                                    <input type="hidden" name="content_id" value="{{ $content->id }}">
                                                </form>
                                            @else
                                                <p class="text-muted">No content added</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Content Modal -->
                                <div class="modal fade" id="addContentModal{{ $listing->id }}{{ $category }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form action="{{ route('url.content.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="listing_id" value="{{ $listing->id }}">
                                                <input type="hidden" name="category" value="{{ $category }}">
                                                
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Add {{ $category }} Content</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label">Content</label>
                                                        <textarea name="content_text" class="form-control" rows="4" required></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-primary">Save Content</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
</x-app-layout>
