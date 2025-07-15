@section('styles')
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>
        .ck-editor__editable {
            min-height: 300px;
        }
        .margin-l {
            margin-left: 10px;
        }
    </style>
@endsection

<div class="py-12">
<div class="">
   <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
     <div class="p-6 text-gray-900 dark:text-gray-100">
     <div class="row justify-content-left">
       <div class="col-md-8">
           <table class="table">
             <thead>
               <tr>
                 <th scope="col">Location ID</th>
                 <th scope="col">Location Name</th>
                 <th scope="col">SEO Index</th>
                 <th scope="col">Action</th>
               </tr>
             </thead>
             <tbody>
             @if(!$listing->isEmpty())
               @foreach($listing as $value)
                 <tr>
                   <td>{{ $value->LocationId }}</td>
                   <td>{{ $value->Name }}, {{$value->countryname}}</td>
                   <td>
                     <div class="form-check">
                       <input type="checkbox" class="form-check-input seo-checkbox" 
                              data-id="{{ $value->LocationId }}"
                              {{ isset($value->show_in_index) && $value->show_in_index ? 'checked' : '' }}
                              onchange="updateSeoStatus(this)">
                     </div>
                   </td>
                   <td>                  
                     <a href="{{ route('edit_listing',[$value->LocationId])}}" target="_blank" class="btn btn-sm btn-primary mb-1">
                       <i class="fas fa-edit"></i> Edit Location
                     </a>
                     <a href="{{ route('edit_faq',[$value->LocationId])}}" target="_blank" class="btn btn-sm btn-info mb-1 margin-l">
                       <i class="fas fa-question-circle"></i> Edit FAQs
                     </a>
                     <button type="button" class="btn btn-sm btn-warning mb-1 margin-l edit-content" 
                             data-id="{{ $value->LocationId }}"
                             data-name="{{ $value->Name }}"
                             data-url="{{ route('update_location_content') }}"
                             onclick="openContentEditor({{ $value->LocationId }}, '{{ $value->Name }}')">
                         <i class="fas fa-edit"></i> Edit Content
                     </button>
                   </td>
                 </tr>
               @endforeach
               @else 
               <tr><td colspan="14">Data Not available.</td></tr>
             @endif
             </tbody>
           </table>  
           </div>
         </div>
       </div>
     </div>
   </div>
 </div>   
</div>
</div>

<!-- Edit Content Modal -->
<div class="modal fade" id="editContentModal" tabindex="-1" role="dialog" aria-labelledby="editContentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editContentModalLabel">Edit Location Content</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="contentForm" method="POST">
                @csrf
                <input type="hidden" name="location_id" id="location_id">
                <div class="modal-body">
                    <div class="form-group">
                        <span class="badge bg-dark edit-btn fa-pull-right mt-3" id="edit-btn" value="0" onclick="editLocationContent()">Edit</span>
                        <label for="location_content">About Location</label>
                        <textarea id="location_content" name="about" class="form-control" disabled>{!! $listing[0]->About ?? '' !!}</textarea>
                        <input type="hidden" id="about_location_content" name="about_location_content" value="{!! htmlspecialchars($listing[0]->About ?? '') !!}">
                    </div>
                    <span id="buttonsContainer" class="buttons-container-dd d-none mb-3">
                        <button type="button" value="1" class="btn btn-dark save-button px-4" id="saveContentBtn">Save</button>
                        <button type="button" value="2" class="btn btn-dark cancel-button px-4" onclick="cancelEdit()">Cancel</button>
                    </span>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Initialize CKEditor
    CKEDITOR.replace('location_content', {
        height: 300,
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo'] },
            { name: 'editing', items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt'] },
            { name: 'forms', items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'] },
            '/',
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'CopyFormatting', 'RemoveFormat'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl'] },
            { name: 'links', items: ['Link', 'Unlink', 'Anchor'] },
            { name: 'insert', items: ['Image', 'Table', 'HorizontalRule', 'SpecialChar', 'Iframe'] },
            '/',
            { name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize', 'ShowBlocks', 'About'] }
        ],
        removeButtons: 'Save,NewPage,Print,Preview,Form,Checkbox,Radio,TextField,Textarea,Select,Button,ImageButton,HiddenField,CreateDiv,Language,Flash,PageBreak,Iframe,About',
        format_tags: 'p;h1;h2;h3;pre',
        removeDialogTabs: 'image:advanced;link:advanced'
    });

    // Update hidden field when editor content changes
    CKEDITOR.instances.location_content.on('change', function() {
    $('#about_location_content').val(CKEDITOR.instances.location_content.getData());
});

// Function to enable editing
function editLocationContent() {
    $('.edit-btn').hide();
    $('#buttonsContainer').removeClass('d-none');
    $('#location_content').prop('disabled', false);
    CKEDITOR.instances.location_content.setReadOnly(false);
}

// Function to cancel editing
function cancelEdit() {
    $('.edit-btn').show();
    $('#buttonsContainer').addClass('d-none');
    $('#location_content').prop('disabled', true);
    CKEDITOR.instances.location_content.setReadOnly(true);
    
    // Reset to original content
    var originalContent = $('#about_location_content').val();
    CKEDITOR.instances.location_content.setData(originalContent);
            }

// Handle save button click
$('#saveContentBtn').on('click', function() {
    var locationId = '{{ $listing[0]->LocationId ?? 0 }}';
    var content = CKEDITOR.instances.location_content.getData();
    
    // Show loading state
    var btn = $(this);
    var originalText = btn.html();
    btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...');
    
    // Save via AJAX
    $.ajax({
        url: '{{ route("update_location_content") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            location_id: locationId,
            content: content
        },
        success: function(response) {
            if (response.success) {
                // Update hidden field with new content
                $('#about_location_content').val(content);
                // Disable editing
                cancelEdit();
                // Show success message
                alert('Content saved successfully!');
            } else {
                alert('Error: ' + (response.message || 'Failed to save content'));
            }
        },
        error: function(xhr) {
            console.error('Error saving content:', xhr.responseText);
            alert('Error saving content. Please try again.');
        },
        complete: function() {
            btn.prop('disabled', false).html(originalText);
        }
    });
});

// Function to open the content editor modal
function openContentEditor(locationId, locationName) {
    // Set the location ID in the modal form
    $('#location_id').val(locationId);
    
    // Set modal title with location name
    $('#editContentModalLabel').text('Edit Content for ' + locationName);
    
    // Load content via AJAX
    $.ajax({
        url: '{{ route("get_location_content", [":id"]) }}'.replace(':id', locationId),
        type: 'GET',
        success: function(response) {
            if (response.success) {
                // Set content in CKEditor - note the correct property path from the controller
                CKEDITOR.instances.location_content.setData(response.data.content);
                $('#about_location_content').val(response.data.content);
            } else {
                console.error('Failed to load content');
            }
        },
        error: function(xhr) {
            console.error('Error loading content:', xhr.responseText);
        },
        complete: function() {
            // Show the modal using Bootstrap 5 syntax
            var myModal = new bootstrap.Modal(document.getElementById('editContentModal'));
            myModal.show();
        }
    });
}

// Make sure CKEditor is properly initialized when the page loads
$(document).ready(function() {
    // Disable CKEditor by default
    if (CKEDITOR.instances.location_content) {
        CKEDITOR.instances.location_content.setReadOnly(true);
    }
});

function updateSeoStatus(checkbox) {
    const locationId = checkbox.dataset.id;
    const isChecked = checkbox.checked;
    
    fetch('/admin/location-seo/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            location_id: locationId,
            show_in_index: isChecked
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Optional: Show success message
            console.log('SEO status updated successfully');
        } else {
            // Revert checkbox if update failed
            checkbox.checked = !isChecked;
            console.error('Failed to update SEO status');
        }
    })
    .catch(error => {
        // Revert checkbox if request failed
        checkbox.checked = !isChecked;
        console.error('Error updating SEO status:', error);
    });
}
</script>