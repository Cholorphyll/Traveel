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
                            <li class="breadcrumb-item" aria-current="page">URL Listings</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">URL Listings</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5 class="mb-0">Manage URL Content</h5>
                            </div>
                            <div class="col-auto">
                                <a href="{{ route('url.upload') }}" class="btn btn-primary">Upload New URLs</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="search-filter">
                            <input type="text" id="searchInput" placeholder="Search..." class="form-control" style="width: auto; display: inline-block;">
                            <button class="btn btn-primary" onclick="filterListings('ho')">ho</button>
                            <button class="btn btn-primary" onclick="filterListings('lo')">lo</button>
                            <button class="btn btn-primary" onclick="filterListings('hd')">hd</button>
                            <button class="btn btn-primary" onclick="filterListings('at')">at</button>
                            <button class="btn btn-primary" onclick="filterListings('rd')">rd</button>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Added By</th>
                                        <th>About</th>
                                        <th>Amenities</th>
                                        <th>FAQ Question</th>
                                        <th>FAQ Answer</th>
                                        <th>View All</th>
                                    </tr>
                                </thead>
                                <tbody id="listings-table">
                                    <!-- Data will be loaded via AJAX -->
                                </tbody>
                            </table>
                            <div id="pagination-links" class="d-flex justify-content-center"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Content Modal -->
<div class="modal fade" id="addContentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contentModalTitle">Add Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contentForm">
                    @csrf
                    <input type="hidden" id="listing_id" name="listing_id">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" onchange="toggleContentFields(this.value)">
                            <option value="About">About</option>
                            <option value="Amenities">Amenities</option>
                            <option value="FAQ">FAQ</option>
                        </select>
                    </div>
                    <div class="mb-3" id="aboutField">
                        <label class="form-label">About Content</label>
                        <textarea name="about" class="form-control" rows="4"></textarea>
                    </div>
					<div class="mb-3" id="aboutField">
                        <label class="form-label">About Content</label>
                        <textarea name="about" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3" id="amenitiesField">
                        <label class="form-label">Amenities Content</label>
                        <textarea name="amenities" class="form-control" rows="4"></textarea>
                    </div>
                    <div class="mb-3 d-none" id="faqFields">
                        <div class="mb-3">
                            <label class="form-label">Question</label>
                            <textarea name="HotelQuestion" class="form-control" rows="2"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Answer</label>
                            <textarea name="HotelAnswer" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="submitContent()">Save Content</button>
            </div>
        </div>
    </div>
</div>

<!-- View/Edit Contents Modal -->
<div class="modal fade" id="viewContentsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View/Edit Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editContentForm">
                    <input type="hidden" id="edit_listing_id" name="listing_id">
                    
                    <!-- About Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                About
                                <span id="aboutStatus" class="badge bg-secondary">No content</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <textarea name="about" id="edit_about" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" onclick="saveContent('About')">Save About</button>
                        </div>
                    </div>

                    <!-- Amenities Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                Amenities
                                <span id="amenitiesStatus" class="badge bg-secondary">No content</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <textarea name="amenities" id="edit_amenities" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" onclick="saveContent('Amenities')">Save Amenities</button>
                        </div>
                    </div>

                    <!-- FAQ Section -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6 class="mb-0 d-flex justify-content-between align-items-center">
                                FAQ
                                <span id="faqStatus" class="badge bg-secondary">No content</span>
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Question</label>
                                <textarea name="HotelQuestion" id="edit_question" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Answer</label>
                                <textarea name="HotelAnswer" id="edit_answer" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="button" class="btn btn-primary" onclick="saveContent('FAQ')">Save FAQ</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadListings();
});

function loadListings(page = 1, filter = '') {
    fetch(`{{ route('url.listing.get') }}?page=${page}&filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('listings-table');
            tableBody.innerHTML = '';
            
            data.data.forEach(listing => {
                let aboutContent = null;
                let amenitiesContent = null;
                let faqContent = null;

                // Find the latest content for each category
                if (listing.contents && listing.contents.length > 0) {
                    listing.contents.forEach(content => {
                        if (content.about) aboutContent = content;
                        if (content.amenities) amenitiesContent = content;
                        if (content.HotelQuestion) faqContent = content;
                    });
                }

                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${listing.url}</td>
                    <td>${listing.added_by_name || 'System'}</td>
                    
                    <!-- About Section -->
                    <td>
                        <span class="badge ${getStatusBadgeClass(aboutContent?.status)}">
                            ${aboutContent ? aboutContent.status : 'No content'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="showAddContentModal(${listing.id}, 'About')">
                            ${aboutContent ? 'Update About' : 'Add About'}
                        </button>
                    </td>
                    
                    <!-- Amenities Section -->
                    <td>
                        <span class="badge ${getStatusBadgeClass(amenitiesContent?.status)}">
                            ${amenitiesContent ? amenitiesContent.status : 'No content'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-info" onclick="showAddContentModal(${listing.id}, 'Amenities')">
                            ${amenitiesContent ? 'Update Amenities' : 'Add Amenities'}
                        </button>
                    </td>
                    
                    <!-- FAQ Section -->
                    <td>
                        <span class="badge ${getStatusBadgeClass(faqContent?.status)}">
                            ${faqContent ? faqContent.status : 'No content'}
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-success" onclick="showAddContentModal(${listing.id}, 'FAQ')">
                            ${faqContent ? 'Update FAQ' : 'Add FAQ'}
                        </button>
                    </td>
                    
                    <!-- View All Button -->
                    <td>
                        <button class="btn btn-sm btn-secondary" onclick="viewContents(${listing.id})">
                            <i class="fas fa-eye"></i> View All
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Pagination links
            const paginationLinks = document.getElementById('pagination-links');
            paginationLinks.innerHTML = '';

            const nav = document.createElement('nav');
            const ul = document.createElement('ul');
            ul.className = 'pagination justify-content-center';

            // Previous link
            const prevLi = document.createElement('li');
            prevLi.className = `page-item ${data.current_page === 1 ? 'disabled' : ''}`;
            const prevLink = document.createElement('a');
            prevLink.className = 'page-link';
            prevLink.href = '#';
            prevLink.innerText = 'Previous';
            prevLink.onclick = function() {
                if (data.current_page > 1) {
                    loadListings(data.current_page - 1, filter);
                }
            };
            prevLi.appendChild(prevLink);
            ul.appendChild(prevLi);

            // Page numbers
            const maxPagesToShow = 2;
            const startPage = Math.max(1, data.current_page - maxPagesToShow);
            const endPage = Math.min(data.last_page, data.current_page + maxPagesToShow);

            for (let i = startPage; i <= endPage; i++) {
                const li = document.createElement('li');
                li.className = `page-item ${data.current_page === i ? 'active' : ''}`;
                const link = document.createElement('a');
                link.className = 'page-link';
                link.href = '#';
                link.innerText = i;
                link.onclick = function() {
                    loadListings(i, filter);
                };
                li.appendChild(link);
                ul.appendChild(li);
            }

            // Next link
            const nextLi = document.createElement('li');
            nextLi.className = `page-item ${data.current_page === data.last_page ? 'disabled' : ''}`;
            const nextLink = document.createElement('a');
            nextLink.className = 'page-link';
            nextLink.href = '#';
            nextLink.innerText = 'Next';
            nextLink.onclick = function() {
                if (data.current_page < data.last_page) {
                    loadListings(data.current_page + 1, filter);
                }
            };
            nextLi.appendChild(nextLink);
            ul.appendChild(nextLi);

            nav.appendChild(ul);
            paginationLinks.appendChild(nav);

        })
        .catch(error => console.error('Error:', error));
}

function filterListings(filter) {
    console.log('filterListings function called'); // Check if function is triggered
    const searchInput = document.getElementById('searchInput').value;
    console.log('Filter value:', filter); // Log the filter value
    loadListings(1, filter);
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'pending': return 'bg-warning';
        case 'accepted': return 'bg-success';
        default: return 'bg-secondary';
    }
}

function toggleContentFields(category) {
    const aboutField = document.getElementById('aboutField');
    const amenitiesField = document.getElementById('amenitiesField');
    const faqFields = document.getElementById('faqFields');
    
    // Hide all fields first
    aboutField.classList.add('d-none');
    amenitiesField.classList.add('d-none');
    faqFields.classList.add('d-none');
    
    // Show based on category
    switch(category) {
        case 'About':
            aboutField.classList.remove('d-none');
            break;
        case 'Amenities':
            amenitiesField.classList.remove('d-none');
            break;
        case 'FAQ':
            faqFields.classList.remove('d-none');
            break;
    }
}

function showAddContentModal(listingId, category) {
    document.getElementById('listing_id').value = listingId;
    document.getElementById('contentForm').reset();
    
    // Update modal title
    document.getElementById('contentModalTitle').textContent = `Add ${category} Content`;
    
    // Set the selected category
    const categorySelect = document.querySelector('[name="category"]');
    categorySelect.value = category;
    categorySelect.disabled = true; // Disable category selection since it's pre-selected
    
    // Show only the relevant field
    document.getElementById('aboutField').classList.add('d-none');
    document.getElementById('amenitiesField').classList.add('d-none');
    document.getElementById('faqFields').classList.add('d-none');
    
    switch(category) {
        case 'About':
            document.getElementById('aboutField').classList.remove('d-none');
            document.querySelector('[name="about"]').setAttribute('required', 'required');
            break;
        case 'Amenities':
            document.getElementById('amenitiesField').classList.remove('d-none');
            document.querySelector('[name="amenities"]').setAttribute('required', 'required');
            break;
        case 'FAQ':
            document.getElementById('faqFields').classList.remove('d-none');
            document.querySelector('[name="HotelQuestion"]').setAttribute('required', 'required');
            document.querySelector('[name="HotelAnswer"]').setAttribute('required', 'required');
            break;
    }
    
    const modal = new bootstrap.Modal(document.getElementById('addContentModal'));
    modal.show();

    // Reset category select when modal is hidden
    document.getElementById('addContentModal').addEventListener('hidden.bs.modal', function () {
        categorySelect.disabled = false;
    });
}

function submitContent() {
    const form = document.getElementById('contentForm');
    const category = form.querySelector('[name="category"]').value;
    
    let formData = {
        listing_id: form.querySelector('#listing_id').value,
        from_view_all: false // Flag to indicate this is NOT from View All section
    };

    // Add data based on category
    if (category === 'About') {
        formData.about = form.querySelector('[name="about"]').value;
    } else if (category === 'Amenities') {
        formData.amenities = form.querySelector('[name="amenities"]').value;
    } else if (category === 'FAQ') {
        formData.HotelQuestion = form.querySelector('[name="HotelQuestion"]').value;
        formData.HotelAnswer = form.querySelector('[name="HotelAnswer"]').value;
    }

    fetch('{{ route("url.content.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal and refresh listings
            const modal = bootstrap.Modal.getInstance(document.getElementById('addContentModal'));
            modal.hide();
            loadListings();
        }
    })
    .catch(error => console.error('Error:', error));
}

function saveContent(category) {
    const form = document.getElementById('editContentForm');
    const listingId = form.querySelector('#edit_listing_id').value;
    
    let formData = {
        listing_id: listingId,
        from_view_all: true // Flag to indicate this is from View All section
    };

    // Add relevant fields based on category
    if (category === 'About') {
        formData.about = form.querySelector('#edit_about').value;
    } else if (category === 'Amenities') {
        formData.amenities = form.querySelector('#edit_amenities').value;
    } else if (category === 'FAQ') {
        formData.HotelQuestion = form.querySelector('#edit_question').value;
        formData.HotelAnswer = form.querySelector('#edit_answer').value;
    }

    fetch('{{ route("url.content.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update status badges
            if (category === 'About') {
                document.getElementById('aboutStatus').textContent = 'Updated';
                document.getElementById('aboutStatus').className = 'badge bg-success';
            } else if (category === 'Amenities') {
                document.getElementById('amenitiesStatus').textContent = 'Updated';
                document.getElementById('amenitiesStatus').className = 'badge bg-success';
            } else if (category === 'FAQ') {
                document.getElementById('faqStatus').textContent = 'Updated';
                document.getElementById('faqStatus').className = 'badge bg-success';
            }
            // Refresh the listings to show updated data
            loadListings();
        }
    })
    .catch(error => console.error('Error:', error));
}

function viewContents(listingId) {
    fetch(`/api/listings/${listingId}/content`)
        .then(response => response.json())
        .then(data => {
            // Set listing ID
            document.getElementById('edit_listing_id').value = listingId;
            
            // Reset form fields and status badges
            document.getElementById('edit_about').value = '';
            document.getElementById('edit_amenities').value = '';
            document.getElementById('edit_question').value = '';
            document.getElementById('edit_answer').value = '';
            
            ['about', 'amenities', 'faq'].forEach(section => {
                document.getElementById(`${section}Status`).className = 'badge bg-secondary';
                document.getElementById(`${section}Status`).textContent = 'No content';
            });

            if (data.contents && data.contents.length > 0) {
                data.contents.forEach(content => {
                    // Fill About content
                    if (content.about) {
                        document.getElementById('edit_about').value = content.about;
                        document.getElementById('aboutStatus').className = `badge ${getStatusBadgeClass(content.status)}`;
                        document.getElementById('aboutStatus').textContent = content.status;
                    }
                    
                    // Fill Amenities content
                    if (content.amenities) {
                        document.getElementById('edit_amenities').value = content.amenities;
                        document.getElementById('amenitiesStatus').className = `badge ${getStatusBadgeClass(content.status)}`;
                        document.getElementById('amenitiesStatus').textContent = content.status;
                    }
                    
                    // Fill FAQ content
                    if (content.HotelQuestion) {
                        document.getElementById('edit_question').value = content.HotelQuestion;
                        document.getElementById('edit_answer').value = content.HotelAnswer;
                        document.getElementById('faqStatus').className = `badge ${getStatusBadgeClass(content.status)}`;
                        document.getElementById('faqStatus').textContent = content.status;
                    }
                });
            }

            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('viewContentsModal'));
            modal.show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading content. Please try again.');
        });
}
</script>
@endpush
</x-app-layout>
