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
                            <li class="breadcrumb-item" aria-current="page">QA Dashboard</li>
                        </ul>
                    </div>
                    <div class="col-md-12">
                        <div class="page-header-title">
                            <h2 class="mb-0">QA Dashboard</h2>
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
                                <h5 class="mb-0">Pending Content Review</h5>
                            </div>
                            <div class="col-auto">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary" onclick="loadPendingContent('pending')">Pending</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="loadPendingContent('needs_update')">Needs Update</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>URL</th>
                                        <th>Category</th>
                                        <th>Content</th>
                                        <th>Added By</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="content-table">
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

<!-- Review Content Modal -->
<div class="modal fade" id="reviewContentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="reviewForm">
                    <input type="hidden" id="content_id" name="content_id">
                    <div class="mb-3">
                        <label class="form-label">URL</label>
                        <p id="review_url" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <p id="review_category" class="form-control-plaintext"></p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Content</label>
                        <div id="review_content" class="border rounded p-3 bg-light"></div>
                    </div>
                    <div class="mb-3" id="update_request_section" style="display: none;">
                        <label class="form-label">Update Request Message</label>
                        <textarea name="update_message" class="form-control" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" onclick="acceptContent()">Accept</button>
                <button type="button" class="btn btn-warning" onclick="showUpdateRequestSection()">Request Update</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentStatus = 'pending';

document.addEventListener('DOMContentLoaded', function() {
    loadPendingContent(currentStatus);
});

function loadPendingContent(status, page = 1) {
    currentStatus = status;
    fetch(`{{ route('qa.pending_content') }}?status=${status}&page=${page}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('content-table');
            tableBody.innerHTML = '';
            
            data.data.forEach(content => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${content.listing.url}</td>
                    <td>${content.category}</td>
                    <td>${content.content_text.substring(0, 100)}...</td>
                    <td>${content.added_by_name}</td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="showReviewModal(${JSON.stringify(content).replace(/"/g, '&quot;')})">
                            Review
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

function showReviewModal(content) {
    document.getElementById('content_id').value = content.id;
    document.getElementById('review_url').textContent = content.listing.url;
    document.getElementById('review_category').textContent = content.category;
    document.getElementById('review_content').textContent = content.content_text;
    document.getElementById('update_request_section').style.display = 'none';
    
    const modal = new bootstrap.Modal(document.getElementById('reviewContentModal'));
    modal.show();
}

function showUpdateRequestSection() {
    document.getElementById('update_request_section').style.display = 'block';
}

function acceptContent() {
    const contentId = document.getElementById('content_id').value;

    fetch('{{ route("qa.accept_content") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ content_id: contentId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('reviewContentModal')).hide();
            loadPendingContent(currentStatus);
        }
    })
    .catch(error => console.error('Error:', error));
}

function requestUpdate() {
    const form = document.getElementById('reviewForm');
    const formData = new FormData(form);

    fetch('{{ route("qa.request_update") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('reviewContentModal')).hide();
            form.reset();
            loadPendingContent(currentStatus);
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
            <a class="page-link" href="#" onclick="loadPendingContent('${currentStatus}', ${data.meta.current_page - 1})">Previous</a>
        </li>
    `;

    // Page numbers
    for (let i = 1; i <= Math.ceil(data.meta.total / data.meta.per_page); i++) {
        html += `
            <li class="page-item ${data.meta.current_page === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="loadPendingContent('${currentStatus}', ${i})">${i}</a>
            </li>
        `;
    }

    // Next link
    html += `
        <li class="page-item ${data.meta.current_page === data.meta.last_page ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="loadPendingContent('${currentStatus}', ${data.meta.current_page + 1})">Next</a>
        </li>
    `;

    html += '</ul></nav>';
    return html;
}
</script>
@endpush
</x-app-layout>
