var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';

document.querySelectorAll('input[name="rating"]').forEach(function(input) {
  input.addEventListener('click', function() {    
    document.querySelectorAll('input[name="rating"]').forEach(function(radio) {
      radio.classList.remove('selected');
    });   
    this.classList.add('selected');  
  });
});




$(document).ready(function() {
var sightId = $('.sightId').text();

var Latitude = $('.Latitude').text();
var Longitude = $('.Longitude').text();



$.ajax({
  type: 'Post',
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  url: base_url + 'add_sight_nbhotel',
  data: { 'sightId': sightId,'Latitude':Latitude,'Longitude':Longitude},
  success: function(response) {
    var near_hote= response.html
	   var html4= response.html4
	   var html5= response.html5
      $('#nearby_hotel').html(near_hote);
      $('#nearbyattraction').html(html4);
      $('#restaurant-data').html(html5);
      
  },

 });
});
$(document).ready(function() {
  var locationIdValue = $('#LocationId').val();
  var sightId = $('#sightId').val();

  $.ajax({
    type: 'GET',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: base_url + 'addsightfaqfront',
    data: { 'locationIdValue': locationIdValue,'sightId':sightId},
    success: function(response) {
        $('#faqdata').html(response);
        // alert(response);
    },
    error: function(xhr, status, error) {
      // Handle the error
      console.log(error);
    }
  });
});

    // Close the modal using Bootstrap's method
  

// Review form submission handler
$(document).on('submit', '#s-review', function(event) {
    event.preventDefault(); 
    
    // Get form data
    const formData = {
        sightId: $('#sightId').val(),
        rating: $(".recommend.selected").val(),
        review: $('.add_review #review').val(),
        gowith: $(".go-with .selected").text(),
        userId: $('meta[name="user-id"]').attr('content') || '0' // Add user ID to form data
    };

    // Check if user is logged in
    const isLoggedIn = $('meta[name="user-id"]').length > 0 && 
                      $('meta[name="user-id"]').attr('content') !== '0';
    
    if (!isLoggedIn) {
        // Store form data in session storage
        sessionStorage.setItem('pendingReview', JSON.stringify(formData));
        
        // Show sign in modal
        $('#signInModal').modal('show');
        return false;
    }
    
    // If user is logged in, submit the form with the user ID
    const userId = $('meta[name="user-id"]').attr('content');
    if (userId && userId !== '0') {
        formData.userId = userId; // Ensure we have the latest user ID
        submitReviewForm(formData);
    } else {
        console.error('User ID not found');
    }
   
  // Function to submit the review form
  function submitReviewForm(formData) {
    var files = $('#files')[0].files;
    var formDataObj = new FormData();
    var $submitBtn = $('#addReview');
    var originalBtnText = $submitBtn.html();
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Ensure CSRF token is available
    if (!csrfToken) {
      console.error('CSRF token not found');
      alert('Session expired. Please refresh the page and try again.');
      window.location.reload();
      return false;
    }

    // Show loading state
    $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');

    // Field validation
    var isValid = true;

    if (formData.review == '') {
      $('.add_review #review-error').text('Review Description is required.').css('color', 'red');
      isValid = false;
    } else {
      $('#review-error').text('');
    }

    if (formData.gowith == '') {
      $('#go-with-error').text('This field is required.').css('color', 'red');
      isValid = false;
    } else {
      $('#go-with-error').text('');
    }

    if (!isValid) {
      $submitBtn.prop('disabled', false).html(originalBtnText);
      return false;
    }
    
    // Prepare form data with all required fields
    formDataObj.append('_token', csrfToken);
    formDataObj.append('_method', 'POST');
    formDataObj.append('rating', formData.rating || ''); 
    formDataObj.append('review', formData.review || '');
    formDataObj.append('sightId', formData.sightId || '');
    formDataObj.append('gowith', formData.gowith || '');
    
    // Add user ID from meta tag or form data
    const userId = $('meta[name="user-id"]').attr('content') || formData.userId;
    if (userId && userId !== '0') {
        formDataObj.append('userId', userId);
    } else {
        console.error('User ID not found for review submission');
        alert('You must be logged in to submit a review.');
        $submitBtn.prop('disabled', false).html(originalBtnText);
        return false;
    }

    // Add files if any
    for (var i = 0; i < files.length; i++) {
      formDataObj.append('files[]', files[i]);
    }

    // Add headers
    const headers = {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest'
    };

    // Submit the form
    $.ajax({
      type: 'POST',
      processData: false,
      contentType: false,
      dataType: 'json',
      url: base_url + 'add_sightreview',
      headers: headers,
      data: formDataObj,
      success: function(response) {
        // Handle both JSON and plain text responses
        if (typeof response === 'string') {
          try {
            // Try to parse as JSON if it's a string
            response = response.trim();
            if ((response.startsWith('{') && response.endsWith('}')) || 
                (response.startsWith('[') && response.endsWith(']'))) {
              response = JSON.parse(response);
            } else if (response.toLowerCase().includes('success')) {
              // If it's a plain text success message, create a success response object
              response = { success: true, message: response };
            } else {
              // If it's some other text, treat it as an error
              throw new Error(response);
            }
          } catch (e) {
            console.warn('Response is not valid JSON, treating as plain text');
            // If we can't parse as JSON, treat it as a success message
            response = { success: true, message: response };
          }
        }

        // Reset form
        $('#s-review')[0].reset();
        $('.tr-file-upload-content').hide();
        $('.tr-image-upload-wrap').show();
        $('.pip').remove();
        
        // Close the modal
        $('.tr-write-review-modal').removeClass('open');
        $('body').removeClass('modal-open').css('overflow', 'auto');
        
        // Clear any pending review data
        sessionStorage.removeItem('pendingReview');
        sessionStorage.removeItem('showReviewAfterLogin');
        
        // Update review stats if available
        if (response.averageRatingPercentage !== undefined) {
            $('.review-rating-count').text(response.averageRatingPercentage + '%');
        }
        if (response.positiveReviews !== undefined) {
            $('.rcmd-count').text(response.positiveReviews);
        }
        if (response.negativeReviews !== undefined) {
            $('.notrcmd-count').text(response.negativeReviews);
        }
        
        // Prepend the new review to the reviews list
        if (response.reviewhtml) {
          try {
            // Remove any existing empty state message
            $('.no-reviews-message, .no-reviews').remove();
            
            // Create review list if it doesn't exist
            if ($('.review-list').length === 0) {
              $('.review-section').prepend('<div class="review-list"></div>');
            }
            
            // Create a temporary container to parse the HTML
            const $newReview = $(response.reviewhtml).filter('.review-item').first();
            
            if ($newReview.length) {
              // Add the review with animation
              $newReview.hide();
              $('.review-list').prepend($newReview);
              $newReview.fadeIn(500);
              
              // Update review count
              const currentCount = parseInt($('.review-count').text()) || 0;
              $('.review-count').text(currentCount + 1);
              
              // Initialize any plugins/event handlers
              if (typeof initializeReviewItems === 'function') {
                initializeReviewItems();
              }
            } else {
              window.location.reload();
            }
          } catch (e) {
            console.error('Error adding review HTML:', e);
            window.location.reload();
          }
        } else if (response.review) {
          // If we have review data but no HTML, try to construct the review item
          try {
            // Create review list if it doesn't exist
            if ($('.review-list').length === 0) {
              $('.review-section').prepend('<div class="review-list"></div>');
            }
            
            const reviewHtml = `
              <div class="review-item" style="display: none;">
                <div class="review-header">
                  <h4>${response.review.name || 'Anonymous'}</h4>
                  <div class="rating">
                    ${'<i class="fas fa-star"></i>'.repeat(response.review.rating || 5)}
                  </div>
                </div>
                <div class="review-date">Just now</div>
                <div class="review-content">${response.review.review || ''}</div>
              </div>`;
            
            // Remove any empty state message
            $('.no-reviews-message, .no-reviews').remove();
            
            // Add the review with animation
            const $newReview = $(reviewHtml);
            $('.review-list').prepend($newReview);
            $newReview.fadeIn(500);
            
            // Update review count
            const currentCount = parseInt($('.review-count').text()) || 0;
            $('.review-count').text(currentCount + 1);
            
            // Initialize any plugins/event handlers
            if (typeof initializeReviewItems === 'function') {
              initializeReviewItems();
            }
          } catch (e) {
            console.error('Error creating review HTML:', e);
            window.location.reload();
          }
        } else {
          // If we can't update the UI, reload the page
          window.location.reload();
        }
        
        // Reset filter to show all reviews
        $('.filter-option').removeClass('active');
        $('.filter-option[data-filter="all"]').addClass('active');
        
        // Update review counts if available in the response
        if (response.totalReviews !== undefined) {
          $('.review-count').text(response.totalReviews);
        }
        
        // Show success message
        var $successMsg = $('<div class="alert alert-success alert-dismissible fade show" role="alert">' +
          'Review added successfully.' +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
        '</div>').hide();
        
        // Insert after the review form or at the top of the review section
        const $reviewForm = $('.tr-write-review-modal .modal-body');
        if ($reviewForm.length) {
          $reviewForm.prepend($successMsg);
        } else {
          $('.review-section').prepend($successMsg);
        }
        $successMsg.fadeIn();
        
        // Scroll to the review section if it exists
        const $reviewSection = $('.review-section');
        if ($reviewSection.length) {
          $('html, body').animate({
            scrollTop: $reviewSection.offset().top - 100
          }, 500);
        }
        
        // Auto-hide success message after 5 seconds
        setTimeout(function() {
          $successMsg.fadeOut(400, function() { $(this).remove(); });
        }, 5000);
      },
      error: function(xhr, status, error) {
        console.error('AJAX Error:', status, error, xhr);
        var errorMessage = 'An error occurred while submitting your review. Please try again.';
        
        try {
          let response = xhr.responseJSON;
          if (!response && xhr.responseText) {
            try {
              response = JSON.parse(xhr.responseText);
            } catch (e) {
              response = { message: xhr.responseText };
            }
          }
          
          if (response) {
            // Handle specific error cases
            if (xhr.status === 401) {
              // Unauthorized - likely session expired
              errorMessage = 'Your session has expired. Please log in again.';
              setTimeout(() => window.location.reload(), 2000);
            } else if (xhr.status === 419) {
              // CSRF token mismatch
              errorMessage = 'Session expired. Refreshing page...';
              setTimeout(() => window.location.reload(), 1000);
            } else if (response.message) {
              errorMessage = response.message;
            } else if (response.errors) {
              // Laravel validation errors
              errorMessage = Object.values(response.errors).flat().join('\n');
            }
          }
        } catch (e) {
          // If we can't parse as JSON, try to get a meaningful error message
          if (xhr.responseText) {
            errorMessage = xhr.responseText;
          } else if (xhr.statusText) {
            errorMessage = xhr.statusText;
          }
        }
        
        // Show error message in a more user-friendly way
        const $errorAlert = $('<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
          errorMessage +
          '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
          '</div>');
        
        // Insert after the review form
        $('.tr-write-review-modal .modal-body').prepend($errorAlert);
        
        // Auto-remove after 5 seconds
        setTimeout(() => {
          $errorAlert.fadeOut(400, function() { $(this).remove(); });
        }, 5000);
      },
      complete: function() {
        // Re-enable submit button
        $submitBtn.prop('disabled', false).html(originalBtnText);
      }
    });
  }
});

// Check for pending review on page load
function checkPendingReview() {
    // Check if we need to show the review form after login
    const showReviewAfterLogin = sessionStorage.getItem('showReviewAfterLogin');
    const pendingReview = sessionStorage.getItem('pendingReview');
    
    if (showReviewAfterLogin === 'true' && pendingReview) {
        try {
            // Clear the flag first to prevent infinite loops
            sessionStorage.removeItem('showReviewAfterLogin');
            
            // Parse the pending review data
            const reviewData = JSON.parse(pendingReview);
            
            // Wait for the DOM to be fully loaded
            $(document).ready(function() {
                // Set the form values
            if ($('.add_review').length) {
                $('.add_review #name').val(reviewData.name || '');
                $('.add_review #email').val(reviewData.email || '');
                $('.add_review #review').val(reviewData.review || '');
                
                // Add user ID to the form data if available
                const userId = $('meta[name="user-id"]').attr('content');
                if (userId && userId !== '0') {
                    reviewData.userId = userId;
                }
                    
                    // Set the rating if it exists
                    if (reviewData.rating) {
                        $(`.add_review input[name="rating"]`).removeClass('selected');
                        $(`.add_review input[name="rating"][value="${reviewData.rating}"]`)
                            .prop('checked', true)
                            .addClass('selected');
                    }
                    
                    // Set the 'go with' selection if it exists
                    if (reviewData.gowith) {
                        $('.go-with li').removeClass('selected');
                        $(`.go-with li`).each(function() {
                            if ($(this).text().trim() === reviewData.gowith) {
                                $(this).addClass('selected');
                                return false; // Exit the loop once found
                            }
                        });
                    }
                    
                    // Show the review form
                    $('.tr-write-review-modal').addClass('open');
                    $('body').addClass('modal-open').css('overflow', 'hidden');
                    
                    // Scroll to the review form
                    $('html, body').animate({
                        scrollTop: $('.tr-write-review-modal').offset().top - 100
                    }, 500);
                    
                    // Clear the pending review from storage
                    sessionStorage.removeItem('pendingReview');
                }
            });
            
        } catch (e) {
            console.error('Error processing pending review:', e);
            // Clear any stored data to prevent issues
            sessionStorage.removeItem('showReviewAfterLogin');
            sessionStorage.removeItem('pendingReview');
        }
    } else if (!showReviewAfterLogin && pendingReview) {
        // If we're not showing the review form but have pending data, clear it
        sessionStorage.removeItem('pendingReview');
    }
}

// Call checkPendingReview when document is ready
$(document).ready(function() {
    checkPendingReview();
    
    // Handle successful login via modal
    $(document).on('submit', '#login-form', function(e) {
        e.preventDefault();
        
        const $form = $(this);
        const $submitBtn = $form.find('button[type="submit"]');
        const originalBtnText = $submitBtn.html();
        
        // Show loading state
        $submitBtn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Logging in...');
        
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            success: function(response) {
                if (response.success) {
                    // Close the login modal
                    const signInModal = bootstrap.Modal.getInstance(document.getElementById('signInModal'));
                    if (signInModal) {
                        signInModal.hide();
                    }
                    
                    // Set flag to indicate we just logged in and should show review form
                    sessionStorage.setItem('showReviewAfterLogin', 'true');
                    
                    // Reload the page to update the UI with logged-in state
                    window.location.reload();
                } else {
                    // Show error message
                    $('.login-error').text(response.message || 'Login failed. Please try again.').show();
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred. Please try again.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                $('.login-error').text(errorMessage).show();
            },
            complete: function() {
                $submitBtn.prop('disabled', false).html(originalBtnText);
            }
        });
    });
});

// Initialize review items with any necessary event handlers
function initializeReviewItems() {
  // Add any initialization code needed for review items
  // For example, tooltips, click handlers, etc.
  $('.review-item [data-toggle="tooltip"]').tooltip();
}

// Email validation helper function
function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

// Filter review
$('.filter-option').on('click', function () {
      $('.filter-option').removeClass('active');
      var sightId = $('.sightId').text();
      $(this).addClass('active');
      var filterType = $(this).data('filter');     
      $.ajax({
        url: base_url + 'filterReviews',
          type: 'Post',
          data: {
              filter: filterType,'sightId':sightId,  _token: $('meta[name="csrf-token"]').attr('content'),
          },
          success: function (response) {
          
              $('.review-data').html(response);
          },
          error: function (xhr, status, error) {
              console.log('Error fetching reviews:', error);
          }
      });
  });


//end filter review
// });

// //end add review
// function isValidEmail(email) {
//   // Regular expression for email validation
//   var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//   return emailPattern.test(email);
// }


let datetimsBtn = document.querySelector(".timming");
let adddatetims = document.querySelector(".add-datetims");
let closedatetims = adddatetims.querySelector(".close-datetims");
datetimsBtn.addEventListener("click", () => {
  adddatetims.classList.remove("d-none");
});
closedatetims.addEventListener("click", () => {
  adddatetims.classList.add("d-none");
});

$(document).on('click', '.plusicon', function() {
  var rowCount = $('.pls').length;
  if (rowCount >= 7) {
    // Limit reached, do not add more rows
    return;
  }
  
  var lastRow = $('.pls').last();
  var startValue = lastRow.find('#clopen').val();
  var endValue = lastRow.find('#cltime').val();

  var html = '<div class="row pls">' +
    '<div class="col-md-5 col-5">' +
    '<div class="mb-3 mt-3">' +
    '<input type="time" class="form-control" id="clopen" value="' + startValue + '" placeholder="Enter email" name="opentime[]">' +
    '</div>' +
    '</div>' +
    '<div class="col-md-5 col-5">' +
    '<div class="mb-3 mt-3">' +
    '<input type="time" class="form-control" id="cltime" value="' + endValue + '" placeholder="Enter email" name="cltime[]">' +
    '</div>' +
    '</div>' +
    '<div class="col-md-2 col-2">' +
    '<div class="closeicon">x</div>' +
    '</div>' +
    '</div>';

  lastRow.after(html);
}); 


$(document).on('click', '.closeicon', function() {
  $(this).closest('.row').remove();
});


$(document).on('click', '.save-time', function () {


  
  // Get selected days
  var selectedDays = [];
  $('.invisible-checkboxes input[type="checkbox"]:checked').each(function () {
    selectedDays.push($(this).attr('id'));
  });

  var uncheckedIdsl = [];
  $('.invisible-checkboxes input[type="checkbox"]').not(':checked').each(function () {
    uncheckedIdsl.push($(this).attr('id'));
  });

  var mainhours = $('input[name="mainhours"]:checked').val();



  // Get open 24 hours and closed status]
  var open24Hours = $('#inlineCheckbox1').prop('checked') ? 1 : 0;


  var closed = $('#inlineCheckbox2').prop('checked') ? 1 : 0;
  var sightid = $('.sightId').text();

  // Get opening and closing times
  var openingTimes = [];
  var closingTimes = [];
  $('.pls').each(function () {
    var openTime = $(this).find('input[name="opentime[]"]').val();
    var closeTime = $(this).find('input[name="cltime[]"]').val();
    openingTimes.push(openTime);
    closingTimes.push(closeTime);
  });


  // Check if count of selectedDays and openingTimes is the same, or if openingTimes count is 1
  if (selectedDays.length === openingTimes.length || open24Hours == 1 || closed == 1 || (openingTimes.length === 1 && openingTimes.every(time => time !== "") && closingTimes.every(time => time !== ""))) {
    $('.error').text('');

 
    // Prepare data to be sent to the server
    var data = {
      uncheckedIds: uncheckedIdsl,
      selectedDays: selectedDays,
      open24Hours: open24Hours,      
      openingTimes: openingTimes,
      closingTimes: closingTimes,
      sightid: sightid,
      closed:closed,
      mainhours:mainhours,
      _token: $('meta[name="csrf-token"]').attr('content'),
    };


    $.ajax({
      type: 'POST',
      url: base_url + 'edittiming',
      data: data,
      success: function (response) {
     
          $('#updtiming').html(response);

      },
      error: function (error) {
    
        console.error(error);
    
      }
    });

    $('.add-datetims').addClass('d-none');
    $('.error').text('');

  }else if(selectedDays == ""){
    $('.error').text('Please Select Days. ').css('color', 'red');
  }else if(selectedDays == "" || openingTimes == "" && open24Hours != 1){
    $('.error').text('Please choose opening and closing time. ').css('color', 'red');
  } else {
    $('.error').text('Please choose opening and closing time for all days or same for all').css('color', 'red');
  }
});
//show more




//start closed time
function toggleTimeInputs(day, checkbox) {
  var elements = document.getElementsByClassName(day);
  
  if (checkbox.checked) {
     
      $(elements).find('.clopen').val('00:00');
      $(elements).find('.cltime').val('00:00');
      $(elements).addClass('d-none');
  } else {
    
      $(elements).find('.clopen').val('09:00');
      $(elements).find('.cltime').val('17:00');
      $(elements).removeClass('d-none');
  }
}




//end closed






//update description
document.addEventListener("DOMContentLoaded", function() {
  const editDescToggler = document.getElementById("open_edit_desc");
  const editDesc = document.getElementById("edit_desc");

  editDescToggler.addEventListener("click", function() {
      editDesc.classList.toggle("d-none");
  });
});




$(document).ready(function() {
  
  $(document).on('click','#updatedesc',function(){
  var id = $('#updatedesc').data('id');
  $('.upd').removeClass('d-none');
  var desc = $('#descriptionTextarea').val(); // Use .val() to get the value of textarea


  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'post',
    url: base_url + 'update_sight_desc',
    data: { 'desc': desc, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
    success: function(response){
      $('#descriptionTextarea').val('');
      $('.upd').addClass('d-none');
      $('#edit_desc').addClass('d-none');
      $('#sight-desc').html(response);
      
    }
  });

});
});
//end update description 





document.addEventListener("DOMContentLoaded", () => {
  const viewMoreButtons = document.querySelectorAll(".view-more");

  viewMoreButtons.forEach((button) => {
      button.addEventListener("click", () => {
          const reviewContainer = button.closest(".review-container");
          const fullDescription = reviewContainer.querySelector(".full-description");
          const shortDescription = reviewContainer.querySelector(".short-description");

          if (fullDescription.classList.contains("d-none")) {
              fullDescription.classList.remove("d-none");
              shortDescription.classList.add("d-none");
              button.innerHTML = "<u>View Less</u>";
          } else {
              fullDescription.classList.add("d-none");
              shortDescription.classList.remove("d-none");
              button.innerHTML = "<u>View More</u>";
          }
      });
  });
});
//end show more

// start add photos section
function addmoreimages() {

  const newContent = document.createElement('div');
  newContent.innerHTML = `
    <div class="add-img-section clonesec border border-dark border-c b-10 my-3">
      <div class="d-flex align-items-md-center flex-md-nowrap flex-wrap m-3">
        <div class="add-img-section">
          <div class="field" align="left">
            <input type="file" name="files[]" class="dropzone" onchange="updateImagePreview(this)">
          </div>
          <div class="dropzone-desc" style="position: unset;margin:0; margin-top: 32px;">
            <img src="public/images/Group.png" width="81" height="57" alt="">
            <span class="text-decoration-underline">Upload Image</span>
          </div>
        </div>
        <input type="text" class="form-control mx-3 my-3 title" name="title[]" placeholder="Add image title">
        <span role="button" class="trash rounded-circle border p-4 d-inline-flex justify-content-center align-items-center" onclick="deleteImage(this)">
          <i class="fas fa-trash-alt"></i>
        </span>
      </div>
    </div>
  `;


  const lastClonesec = document.querySelector('.clonesec:last-child');

  lastClonesec.insertAdjacentElement('afterend', newContent);
}


function updateImagePreview(input) {
  const dropzoneDesc = input.closest('.add-img-section').querySelector('.dropzone-desc');
  const image = dropzoneDesc.querySelector('img');
  if (input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = function (e) {
      image.src = e.target.result;
    };

    reader.readAsDataURL(input.files[0]);
  }
}

function deleteImage(deleteButton) { 
  const addImgSection = deleteButton.closest('.add-img-section');
  const image = addImgSection.querySelector('.dropzone-desc img');
  image.src = "public/images/Group.png"; 
  const fileInput = addImgSection.querySelector('input[type="file"]');
  fileInput.value = null;
}




$(document).on('click', '#save_photo', function () {
  const formData = new FormData();
  const sightId = $('.sightId').text();

  const csrfToken = $('meta[name="csrf-token"]').attr('content');
  formData.append('_token', csrfToken);
  formData.append('sight_id', sightId); 
 var checkfile = false;
  $('.clonesec').each(function() {
    const fileInput = $(this).find('input[name="files[]"]')[0];
    const titleInput = $(this).find('input[name="title[]"]')[0]; 
   
  
    if (fileInput && fileInput.files.length > 0) {
      const file = fileInput.files[0];
      const title = titleInput ? titleInput.value : 'null'; 
  
    
      formData.append('title[]', title);
      formData.append('files[]', file);
       checkfile = true;
    }
  });  
  if (!checkfile) {
    
    $('.photo_error').text('Image is required').css('color','red');   
    return; 
  }
  $('.photo_error').text('uploading..').css('color','green');  
    // Set up AJAX request with proper headers
    $.ajax({
      url: base_url + 'add_sightreview',
      type: 'POST',
      data: formData,
      processData: false,
      contentType: false,
      headers: {
        'X-CSRF-TOKEN': csrfToken,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json'
      },
      xhrFields: {
        withCredentials: true  // Important for sending cookies with CORS
      }, 
    success: function(response) {
      $('#add-photo').addClass('d-none');  
	  $('.sightImages').html(response);
	  $('.photo_error').text('');  
      $('.add-img-section input[name="files[]"]').val('');
      $('.add-img-section input[name="title[]"]').val('');
      const addImgSections = document.querySelectorAll('.add-img-section');    
      addImgSections.forEach(addImgSection => {       
        const image = addImgSection.querySelector('.dropzone-desc img');     
   
        image.src = "public/images/Group.png";      
     
        const fileInput = addImgSection.querySelector('input[type="file"]');
        fileInput.value = null;
      });
    }
  });
});





// add photos
let addPhoto = document.querySelector("#add-photo");
let closePhoto = addPhoto.querySelector(".close-photo");
let addphBtn = document.querySelector(".addph");
addphBtn.addEventListener("click", () => {
  addPhoto.classList.remove("d-none");
});
closePhoto.addEventListener("click", () => {
  addPhoto.classList.add("d-none");
});
// end add photos section
 let aadrevButtons = document.querySelectorAll(".aadrev");
let addReview = document.querySelector(".add_review");

aadrevButtons.forEach((button) => {
  button.addEventListener("click", () => {
    addReview.classList.remove("d-none");
  });
});

document.addEventListener("click", (event) => {
  if (event.target.classList.contains("close-box")) {
    addReview.classList.add("d-none");
  }
});








// add tip
let addTip = document.querySelector(".add-tip");
let closeTip = addTip.querySelector(".close-tip");
let addtipBtn = document.querySelectorAll(".addtip");
addtipBtn.forEach((el) =>
  el.addEventListener("click", () => {
    addTip.classList.remove("d-none");
  })
);
closeTip.addEventListener("click", () => {
  addTip.classList.add("d-none");
});

// ==================================



// light box
document.querySelector(".lightbox .close").addEventListener("click", () => {
  document.querySelector(".lightbox ").classList.add("d-none");
  document.querySelector(".lightbox ").classList.remove("position-fixed");
});
document.querySelectorAll(".carousel-item").forEach((el) =>
  el.addEventListener("click", () => {
    document.querySelector(".lightbox ").classList.remove("d-none");
    document.querySelector(".lightbox ").classList.add("position-fixed");
  })
);
document
  .querySelector(".lightbox .like")
  .addEventListener("click", function () {
    this.classList.toggle("text-primary");
  });

$(".owl-carousel").owlCarousel({
  loop: true,
  margin: 60,
  nav: true,
  dots: false,
  navText: [
    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
    '<i class="fa fa-angle-right" aria-hidden="true"></i>',
  ],

  responsive: {
    0: {
      items: 5,
    },
    600: {
      items: 6,
    },
    1000: {
      items: 7,
    },
  },
});

//   end light box




function selectButton(element) {
  var container = document.querySelector('.go-with');  
  var items = container.getElementsByTagName('li');  
  for (var i = 0; i < items.length; i++) {
      items[i].classList.remove('selected');
  }
  
  element.classList.add('selected');
}
