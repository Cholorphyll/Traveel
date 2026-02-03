// Modal Sight Search AJAX
if (!(typeof window !== 'undefined' && window.TR_SIGHT_MODAL_SEARCH_V2)) {
$(document).on('click', '#serch_sightsdata', function(e) {
    e.preventDefault();
    var searchTerm = $('#serch_sights').val();
    if (!searchTerm) return;

    function escapeHtml(text) {
        try {
            return $('<div>').text(String(text || '')).html();
        } catch (e) {
            return '';
        }
    }

    function getLocationName() {
        try {
            var name = ($('#location_name').text() || '').trim();
            if (name) {
                return name;
            }
        } catch (e) {}
        try {
            var raw = ($('#slug').text() || '').trim();
            raw = String(raw || '').replace(/-/g, ' ');
            return raw.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
        } catch (e) {
            return '';
        }
    }

    function renderNoResults(term) {
        var loc = getLocationName();
        var safeTerm = escapeHtml(term);
        var safeLoc = escapeHtml(loc);
        var html = '';
        html += '<div class="tr-search-list"><div class="tr-details"><div class="tr-title">“' + safeTerm + '” in ' + (safeLoc ? ('“' + safeLoc + '”') : 'this area') + '</div></div></div>';
        html += '<div class="tr-search-list"><div class="tr-details"><div class="tr-title">“' + safeTerm + '” near me</div></div></div>';
        return html;
    }

    $.ajax({
        url: '/searchsightlisting',
        method: 'GET',
        data: { search: searchTerm },
        success: function(response) {
            // Expecting HTML chunk for .tr-search-lists
            if (!response || (typeof response === 'string' && response.trim() === '')) {
                $('.search-result .tr-search-lists').html(renderNoResults(searchTerm)).css('display', 'block');
                return;
            }
            $('.search-result .tr-search-lists').html(response).css('display', 'block');
        },
        error: function() {
            $('.search-result .tr-search-lists').html(renderNoResults(searchTerm)).css('display', 'block');
        }
    });
});
$('#serch_sights').on('keyup', function(e) {
    if (e.key === 'Enter') {
        $('#serch_sightsdata').trigger('click');
    }
});
}

// Function to convert input text to slug format
function convertToSlug(text) {
    return text
        .toLowerCase()                   // Convert to lowercase
        .trim()                         // Remove whitespace from both ends
        .replace(/\s+/g, '-')           // Replace spaces with -
        .replace(/[^\w\-]+/g, '')       // Remove all non-word characters
        .replace(/\-\-+/g, '-');        // Replace multiple - with single -
}

// Add event listener when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Get the name input element
    const nameInput = document.getElementById('name');
    // Get the slug input element (assuming you have one with id="slug")
    const slugInput = document.getElementById('slug');

    // Add input event listener to name field
    if (nameInput && slugInput) {
        nameInput.addEventListener('input', function() {
            // Convert name to slug and set it as slug input value
            slugInput.value = convertToSlug(this.value);
        });
    }
});
