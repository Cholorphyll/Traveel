function editHlanding(fieldId) {
    // Enable the textarea or inputs for editing
    if (fieldId === 6) {
        // Enable landing page type radio buttons
        $('input[name="page_type"]').prop('disabled', false);
    } else if (fieldId === 7) {
        // Enable nearby radio buttons and input
        $('input[name="near_by"]').prop('disabled', false);
        $('.inputval').prop('disabled', false);
    } else if (fieldId === 8) {
        // Enable conditions select and inputs
        $('.select-filters').prop('disabled', false);
        $('.change-with-filter input').prop('disabled', false);
    } else {
        $(`#name${fieldId}`).prop('disabled', false);
    }
    
    // Show the buttons container
    $(`#buttonsContainer-${fieldId}`).removeClass('d-none');
}

function cancelHLanding(fieldId) {
    if (fieldId === 6) {
        // Disable landing page type radio buttons
        $('input[name="page_type"]').prop('disabled', true);
    } else if (fieldId === 7) {
        // Disable nearby radio buttons and input
        $('input[name="near_by"]').prop('disabled', true);
        $('.inputval').prop('disabled', true);
    } else if (fieldId === 8) {
        // Disable conditions select and inputs
        $('.select-filters').prop('disabled', true);
        $('.change-with-filter input').prop('disabled', true);
    } else {
        // Disable the textarea
        const textarea = $(`#name${fieldId}`);
        textarea.prop('disabled', true);
        textarea.val(textarea.data('original-value'));
    }
    
    // Hide the buttons container
    $(`#buttonsContainer-${fieldId}`).addClass('d-none');
}

$(document).ready(function() {
    console.log('Document ready - initializing save functionality');

    // Remove disabled state from all inputs since we're using a single save button
    $('input[name="page_type"]').prop('disabled', false);
    $('input[name="near_by"]').prop('disabled', false);
    $('.inputval').prop('disabled', false);
    $('.select-filters').prop('disabled', false);
    $('.change-with-filter input').prop('disabled', false);

    // Attach click handler to save button
    $(document).on('click', '#saveAllChanges', function(e) {
        e.preventDefault();
        console.log('Save button clicked');
        
        const id = $(this).data('id');
        console.log('Saving data for ID:', id);
        
        // Show loading state
        const $saveButton = $(this);
        const originalText = $saveButton.text();
        $saveButton.prop('disabled', true).text('Saving...');

        // Get CSRF token
        const token = $('meta[name="csrf-token"]').attr('content');
        if (!token) {
            alert('CSRF token not found. Please refresh the page.');
            $saveButton.prop('disabled', false).text(originalText);
            return;
        }

        // Collect data from conditions section
        const data = {
			Name: $('#name1').val().trim(),
            Slug: $('#name2').val().trim(),
            MetaTagTitle: $('#name3').val().trim(),
            MetaTagDescription: $('#name4').val().trim(),
            About: $('#name5').val().trim(),
            page_type: $('input[name="page_type"]:checked').val(),
            Nearby_Type: $('input[name="near_by"]:checked').val(),
            NearbyId: $('.nearby-value').text().trim(),
            Nearbyname: $('.inputval').val().trim(),
            Rating: $('.star-rating button').map(function() { return $(this).text().trim(); }).get().join(','),
            HotelAmenities: $('.hotel-mnt button').map(function() { return $(this).text().trim(); }).get().join(','),
            Room_Amenities: $('.mnt button').map(function() { return $(this).text().trim(); }).get().join(','),
            Hotel_Pricing: $('.Hotel_Pricing button').map(function() { return $(this).text().trim(); }).get().join(','),
            RoomType: $('.room_type button').map(function() { return $(this).text().trim(); }).get().join(','),
            Distance: $('.distance button').map(function() { return $(this).text().trim(); }).get().join(','),
            Hotel_Style: $('.hotel-style button').map(function() { return $(this).text().trim(); }).get().join(','),
            OnSiteRestaurants: $('.on-site-restaurants button').map(function() { return $(this).text().trim(); }).get().join(','),
            HotelTags: $('.Hotel_Tags button').map(function() { return $(this).text().trim(); }).get().join(','),
            PublicTransitAccess: $('.Public_Transit button').map(function() { return $(this).text().trim(); }).get().join(','),
            Access: $('.Access button').map(function() { return $(this).text().trim(); }).get().join(',')
        };

        console.log('Sending data:', data);

        // Make the AJAX request
        $.ajax({
            url: '/landing/update-hotel-landing/' + id,
            method: 'POST',
            data: data,
            headers: {
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.status === 'success') {
                    alert('All changes saved successfully');
                } else {
                    alert('Error: ' + (response.message || 'Unknown error occurred'));
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', {xhr, status, error});
                let errorMessage = 'Error saving changes';
                try {
                    const response = JSON.parse(xhr.responseText);
                    errorMessage += ': ' + (response.message || error);
                } catch(e) {
                    errorMessage += ': ' + error;
                }
                alert(errorMessage);
            },
            complete: function() {
                // Restore button state
                $saveButton.prop('disabled', false).text(originalText);
            }
        });
    });

    // Helper function to get button values from a container
    function getButtonValues(containerSelector) {
        const values = [];
        $(containerSelector + ' button').each(function() {
            values.push($(this).text().trim());
        });
        return values.join(','); // Convert array to comma-separated string
    }
});
