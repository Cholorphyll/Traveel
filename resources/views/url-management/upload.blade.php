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
                            <h2 class="mb-0">Upload URLs</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
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

                        <form action="{{ route('url.upload.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group mb-3">
                                <label class="form-label">Paste URLs (one per line)</label>
                                <textarea name="urls" class="form-control" rows="5" placeholder="https://example.com&#10;https://another-example.com">{{ old('urls') }}</textarea>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label class="form-label">Or upload a file with URLs</label>
                                <input type="file" name="url_file" class="form-control" accept=".txt,.csv">
                                <small class="form-text text-muted">Upload a text file or CSV file with one URL per line</small>
                            </div>

                            <button type="submit" class="btn btn-primary">Upload URLs</button>
                        </form>

                        @if(session('results'))
                            <div class="mt-4">
                                <h6>Results:</h6>
                                <ul class="list-group">
                                    @foreach(session('results') as $result)
                                        <li class="list-group-item {{ $result['isExisting'] ? 'text-danger' : 'text-success' }}">
                                            {{ $result['url'] }} - {{ $result['isExisting'] ? 'Already exists' : 'Added' }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</x-app-layout>
