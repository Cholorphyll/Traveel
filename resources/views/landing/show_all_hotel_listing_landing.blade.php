<x-app-layout>
  <div class="pc-container">
    <div class="pc-content">
      <!-- [ breadcrumb ] start -->
      <div class="page-header">
        <div class="page-block">
          <div class="row align-items-center">
            <div class="col-md-12">
              <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('dashboard')}}">Dashboard</a></li>
                <li class="breadcrumb-item" aria-current="page">Hotel Landing Pages</li>
              </ul>
            </div>
            <div class="col-md-12">
              <div class="page-header-title">
                <h2 class="mb-0">Hotel Landing Pages</h2>
              </div>  
            </div>
          </div>
        </div>
      </div>
      
      <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900 dark:text-gray-100">
              <div class="row justify-content-center">
                <div class="col-md-12">
                  <div class="white_shd full margin_bottom_30">
                    <!-- Add New Button -->
                  
                      <!-- Add Search Form Here -->
                   <!-- Add Search Form Here -->
                  <!-- Add this where you want the search input -->
                  <div class="row mb-3 mt-3">
                      <div class="col-md-6">
                          <div class="input-group">
                              <input type="text" 
                                    id="searchInput"
                                    class="form-control" 
                                    placeholder="Search by name...">
                              <button class="btn btn-secondary" 
                                      type="button" 
                                      id="clearSearch" 
                                      style="display: none;">
                                  <i class="ti ti-x"></i> Clear
                              </button>
                          </div>
                      </div>

                      <div class="col-md-3"><a href="{{ route('addlandingpage_search') }}" class="btn btn-primary" style="float: inline-end;">
                        <i class="ti ti-plus"></i> Add New Landing Page
                      </a></div>
					  <div class="col-md-3">
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#uploadCsvModal" style="float: inline-end;">
        <i class="ti ti-upload"></i> Upload Bulk CSV File
    </button>
</div>
                  </div>
					
                  <!-- Add this where you want to display results -->

                      <!-- Success/Error Messages -->
                    <!-- Success/Error Messages -->
                    @if ($message = Session::get('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                      {{ $message }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    @if ($message = Session::get('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                      {{ $message }}
                      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    @endif

                    <!-- Table -->
                    <div class="table-responsive searchResults">
                      <table class="table table-hover">
                        <thead>
                          <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Meta Title</th>
                            <th>Location ID</th>
                            <th>Actions</th>
                          </tr>
                        </thead>
                        <tbody>
                          @forelse ($listings as $key => $listing)
                          <tr>
                            <td>{{ $listings->firstItem() + $key }}</td>
                            <td>{{ $listing->name }}</td>
                            <td>{{ $listing->slug }}</td>
                            <td>{{ $listing->meta_tag_title }}</td>
                            <td>{{ $listing->location_id }}</td>
                            <td>
                              <div class="btn-group" role="group">
                                <a href="{{ route('edit.hotel.listing.landing', $listing->id) }}" 
                                   class="btn btn-sm btn-primary">
                                   <i class="ti ti-edit"></i> Edit
                                </a>
                                <form action="{{ route('delete.hotel.listing.landing', $listing->id) }}" method="POST" style="display:inline;">
                                  @csrf
                                  @method('DELETE')
                                  <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this listing?');">
                                    <i class="ti ti-trash"></i> Delete
                                  </button>
                                </form>
                              </div>
                            </td>
                          </tr>
                          @empty
                          <tr>
                            <td colspan="6" class="text-center">No landing pages found</td>
                          </tr>
                          @endforelse
                        </tbody>
                      </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                      {{ $listings->links() }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this landing page?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
          </form>
        </div>
      </div>
    </div>
  </div>
<!-- Modal Structure -->
<div class="modal fade" id="uploadCsvModal" tabindex="-1" aria-labelledby="uploadCsvModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="uploadCsvModalLabel" style="margin-top: 20px; margin-bottom: 10px;">Upload CSV File</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">
                <form action="{{ route('store_hotel_listing_landing_csv') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="margin-l" style="margin-left: 39px;">
                        <div class="col-md-8 form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <input type="file" name="csv_file" id="csv_file" class="form-control" accept=".csv" required>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-outline-dark mt-3">Upload and Create Landing Pages</button>
                </form>
            </div>
        </div>
    </div>
</div>
  @push('scripts')
  <script>
    // Handle delete button click
    document.querySelectorAll('.delete-listing').forEach(button => {
      button.addEventListener('click', function() {
        const id = this.getAttribute('data-id');
        const form = document.getElementById('deleteForm');
        form.action = `/hotel-listing-landing/${id}`;
      });
    });
  </script>
  @endpush
</x-app-layout>