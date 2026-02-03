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
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Content Status</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
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
                <h5 class="modal-title">Add Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="contentForm">
                    <input type="hidden" id="listing_id" name="listing_id">
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-select" required onchange="toggleContentFields(this.value)">
                            <option value="About">About</option>
                            <option value="Amenities">Amenities</option>
                            <option value="FAQ">FAQ</option>
                        </select>
                    </div>
                    <div class="mb-3" id="standardContentField">
                        <label class="form-label">Content</label>
                        <textarea name="content_text" class="form-control" rows="4" required></textarea>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadListings();
});

function loadListings(page = 1) {
    fetch(`{{ route('url.listing.get') }}?page=${page}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('listings-table');
            tableBody.innerHTML = '';
            
            data.data.forEach(listing => {
                const latestContent = listing.contents[0] || null;
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${listing.url}</td>
                    <td>
                        <span class="badge ${getStatusBadgeClass(latestContent?.status)}">
                            ${latestContent ? latestContent.status : 'No content'}
                        </span>
                    </td>
                    <td>${listing.added_by_name || 'System'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="showAddContentModal(${listing.id})">
                            Add Content
                        </button>
                        <button class="btn btn-sm btn-info" onclick="viewContents(${listing.id})">
                            View Contents
                        </button>
                    </td>
                `;
                tableBody.appendChild(row);
            });

            // Update pagination
            const paginationDiv = document.getElementById('pagination-links');
            paginationDiv.innerHTML = createPaginationLinks(data);
        })
        .catch(error => console.error('Error:', error));
}

function getStatusBadgeClass(status) {
    switch(status) {
        case 'pending': return 'bg-warning';
        case 'accepted': return 'bg-success';
        default: return 'bg-secondary';
    }
}

function toggleContentFields(category) {
    const standardField = document.getElementById('standardContentField');
    const faqFields = document.getElementById('faqFields');
    
    if (category === 'FAQ') {
        standardField.classList.add('d-none');
        faqFields.classList.remove('d-none');
        document.querySelector('[name="content_text"]').removeAttribute('required');
        document.querySelector('[name="HotelQuestion"]').setAttribute('required', 'required');
        document.querySelector('[name="HotelAnswer"]').setAttribute('required', 'required');
    } else {
        standardField.classList.remove('d-none');
        faqFields.classList.add('d-none');
        document.querySelector('[name="content_text"]').setAttribute('required', 'required');
        document.querySelector('[name="HotelQuestion"]').removeAttribute('required');
        document.querySelector('[name="HotelAnswer"]').removeAttribute('required');
    }
}

function showAddContentModal(listingId) {
    document.getElementById('listing_id').value = listingId;
    document.getElementById('contentForm').reset();
    // Reset the form fields visibility
    toggleContentFields(document.querySelector('[name="category"]').value);
    const modal = new bootstrap.Modal(document.getElementById('addContentModal'));
    modal.show();
}

function submitContent() {
    const form = document.getElementById('contentForm');
    const formData = new FormData(form);
    const category = formData.get('category');
    
    let data = {
        listing_id: formData.get('listing_id'),
        category: category,
        status: 'pending'
    };

    if (category === 'FAQ') {
        data.HotelQuestion = formData.get('HotelQuestion');
        data.HotelAnswer = formData.get('HotelAnswer');
        data.content_text = ''; // Empty for FAQ type
    } else {
        data.content_text = formData.get('content_text');
        data.HotelQuestion = '';
        data.HotelAnswer = '';
    }

    fetch('{{ route("url.content.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('addContentModal')).hide();
            form.reset();
            loadListings();
        } else if (data.error) {
            alert(data.error);
        }
    })
    .catch(error => console.error('Error:', error));
}

function createPaginationLinks(data) {
    if (!data.meta || data.meta.total <= data.meta.per_page) return '';

    let html = '<nav><ul class="pagination">';
    
    // Previous link
    html += `
        <li class="page-item ${data.meta.current_page === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadListings(${data.meta.current_page - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= Math.ceil(data.meta.total / data.meta.per_page); i++) {
        html += `
            <li class="page-item ${data.meta.current_page === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadListings(${i})">${i}</a>
            </li>
        `;
    }

    // Next link
    html += `
        <li class="page-item ${data.meta.current_page === data.meta.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadListings(${data.meta.current_page + 1})">Next</a>
        </li>
    `;

    html += '</ul></nav>';
    return html;
}
</script>
@endpush
</x-app-layout>
