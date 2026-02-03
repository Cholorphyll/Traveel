var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
    .location.port : '');
var base_url = baseURL + '/';

// Function to validate email format
function validateEmail(input) {
    var email = typeof input === 'string' ? input : input.value.trim();
    var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return emailRegex.test(email);
}
    
jQuery(function($) {
    // Add real-time email validation for signup form
    $(document).ready(function() {
        // Email validation for signup form
        $('#email').on('input', function() {
            var email = $(this).val().trim();
            var validationMessage = $('#email-validation-message');
            
            // Clear previous validation message
            validationMessage.text('');
            
            // Skip validation if field is empty
            if (email === '') {
                return;
            }
            
            // Validate email format
            if (!validateEmail(email)) {
                validationMessage.text('Please enter a valid email address');
            }
        });
        
        // Initial validation for pre-filled values
        var emailInput = $('#email');
        if (emailInput.length && emailInput.val().trim() !== '') {
            var email = emailInput.val().trim();
            if (!validateEmail(email)) {
                $('#email-validation-message').text('Please enter a valid email address');
            }
        }
    });

        $('#userlogin').on('submit', function(e) {
            e.preventDefault(); 
            var formData = $(this).serialize(); // Serialize form data
            
            var email = $('#emailAddress').val(); 
            if(email == ""){
               return $('.email-msg').text('Email is required'); 
            } else {
                $('.email-msg').text('');
            }
            var token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token from the meta tag
    
            $.ajax({
                type: 'POST',
                url: base_url + 'checkemail', 
                data: formData + '&_token=' + token, 
                success: function(response) {
                    if (response == 0) {
                        var signInModal = bootstrap.Modal.getInstance(document.querySelector('#signInModal'));
                        if (signInModal) signInModal.hide();
                        
                        var signUpModal = new bootstrap.Modal(document.querySelector('#signUpModal'));
                        signUpModal.show();
                        $('.msg').removeClass('d-none').text('Don\'t have an account? Please register');  
                    } else {
                        var signInModal = bootstrap.Modal.getInstance(document.querySelector('#signInModal'));
                        if (signInModal) signInModal.hide(); // Hide the current modal
                        
                        var logInModal = new bootstrap.Modal(document.querySelector('#logInModal'));
                        logInModal.show(); // Show the login modal
                        $('.email-value').text(response);
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $('.text-danger').remove(); // Clear previous errors
                    if (errors.email) {
                        $('#emailAddress').after('<span class="text-danger">' + errors.email[0] + '</span>');
                    }
                }
            });
        });
    
        // Use event delegation for elements within #logInModal
        $(document).on('click', '[data-bs-toggle="modal"]', function() {
            var currentModal = $(this).closest('.modal');
            var currentModalObj = bootstrap.Modal.getInstance(currentModal[0]);
            if (currentModalObj) currentModalObj.hide(); // Hide the current modal
            
            var targetModal = $(this).data('bs-target');
            var targetModalEl = document.querySelector(targetModal);
            if (targetModalEl) {
                var targetModalObj = new bootstrap.Modal(targetModalEl);
                targetModalObj.show(); // Show the target modal
            }
        });

    // --- Modal cleanup helpers to prevent stuck backdrops/dim screen ---
        function cleanModalArtifacts() {
            // Remove any stray backdrops
            $('.modal-backdrop').remove();
            // Restore body scroll and padding
            $('body').removeClass('modal-open').css({ overflow: '', paddingRight: '' });
        }

        // When any modal is fully hidden, clean artifacts if no other modal is open
        $(document).on('hidden.bs.modal', '.modal', function () {
            // Defer slightly to let Bootstrap finish its own teardown
            setTimeout(function() {
                if ($('.modal.show').length === 0) {
                    cleanModalArtifacts();
                }
            }, 50);
        });

        // Ensure only one modal is visible at a time
        $(document).on('show.bs.modal', '.modal', function (e) {
            var $this = $(this);
            // Hide any other open modals first
            $('.modal.show').not($this).each(function () {
                var inst = bootstrap.Modal.getInstance(this);
                if (inst) { inst.hide(); }
                else { $(this).modal('hide'); }
            });
            // Also clear stray artifacts before showing a new modal
            cleanModalArtifacts();
        });
    

        // Set current URL in the hidden field when the login modal is shown
        $(document).on('shown.bs.modal', '#logInModal', function() {
            $('#current_page_url').val(window.location.href);
        });

		 // Google login button (preserve original button design, just bind click)
        $(document).on('click', '#google-login-btn', function(e) {
            e.preventDefault();
            var redirect = encodeURIComponent(window.location.href);
            // Hide any open modal before navigating (defensive)
            var anyOpen = document.querySelector('.modal.show');
            if (anyOpen) {
                var inst = bootstrap.Modal.getInstance(anyOpen);
                if (inst) { inst.hide(); }
            }
            window.location.href = base_url + 'auth/google?redirect_url=' + redirect;
        });

        // Handle login form submission
        $('#logInModal').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
    
            var formData = $(this).serialize(); // Serialize form data
            var email = $('.email-value').text();
            var password = $('#passwordLogin').val();
            var currentUrl = $('#current_page_url').val() || window.location.href; // Get the current page URL
            
            // Clear previous error messages
            $('.password-msg').text('');
            $('.login-error-msg').addClass('d-none').text('');
            
            if(password == ""){
                $('.password-msg').text('Password is required');
                return false;  
            }
            
            var token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token from the meta tag
    
            $.ajax({
                type: 'POST',
                url: base_url + 'userlongin', // Use the form's action attribute
                data: {
                    password: password, 
                    email: email,
                    redirect_url: currentUrl, // Pass the current URL to the controller
                    _token: token       
                },
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        if (response.message) {
                            $('.login-error-msg').removeClass('d-none alert-danger').addClass('alert-success').text(response.message);
                        }
                        
                        // Check if there's a pending review
                        const pendingReview = sessionStorage.getItem('pendingReview');
                        if (pendingReview) {
                            // Hide the sign-in modal immediately
                            var signInModal = bootstrap.Modal.getInstance(document.getElementById('signInModal'));
                            if (signInModal) {
                                signInModal.hide();
                            } else {
                                $('#signInModal').modal('hide');
                            }
                            
                            // Remove any existing hidden.bs.modal handlers to prevent duplicates
                            $('#signInModal').off('hidden.bs.modal');
                            
                            // Set a flag in session storage to indicate we need to show the review form
                            sessionStorage.setItem('showReviewAfterLogin', 'true');
                            
                            // Force a page reload to ensure all data is properly loaded
                            window.location.reload();
                            return;
                        }
                        
                        // Original redirect logic for non-review cases
                        setTimeout(function() {
                            if (response.redirectUrl) {
                                window.location.href = response.redirectUrl;
                            } else {
                                window.location.reload();
                            }
                        }, 500);
                    } else {
                        // Show error message
                        $('.password-msg').text(response.message || 'Invalid password');
                        $('.login-error-msg').removeClass('d-none').addClass('alert-danger').text(response.message || 'Login failed. Please check your credentials.');
                    }
                },
                error: function(xhr) {
                    // Handle AJAX errors
                    $('.text-danger').remove(); // Clear previous errors
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        var errors = xhr.responseJSON.errors;
                        if (errors.password) {
                            $('.password-msg').text(errors.password[0]);
                        }
                        if (errors.email) {
                            $('#emailAddress').after('<span class="text-danger">' + errors.email[0] + '</span>');
                        }
                        $('.login-error-msg').removeClass('d-none').addClass('alert-danger').text('Login failed. Please check your credentials.');
                    } else {
                        $('.login-error-msg').removeClass('d-none').addClass('alert-danger').text('An error occurred. Please try again.');
                    }
                }
            });
        });

        $('#forgotPswdModal').on('submit', function(e) {
            e.preventDefault(); 

            var email = $('#emailForgotPassword').val();
          
            if(email == ""){
               
                return  $('.tr-validation-message').text('Email is requierd');  
            }else{
                $('.tr-validation-message').text();
            }
            var token = $('meta[name="csrf-token"]').attr('content'); // Get the CSRF token from the meta tag
         
            $.ajax({
                type: 'POST',
                url: base_url + 'sendlink_forgorpassword', // Use the form's action attribute
                data: {
                    email: email, 
                    _token: token       
                },
                success: function(response) {
                    if (response == 0) {
                        
                      $('.error-msg').text('Account not found. Please choose another email.');
                    } else if(response == 2) {
                        $('.error-msg').text('Failed to send verification email. Please try again later.');       
                    }else{
                        console.log(response)
                        // $('#logInModal').css('display','block').addClass('show');
                        // $('#signInModal').css('display','none').removeClass('show');
                        $('.forgot-success-msg').removeClass('d-none').text('A link to reset password has been sent to '+response)  
                        $('.button-msg').text('Re-send link')
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $('.text-danger').remove(); // Clear previous errors
                    if (errors.email) {
                        $('#emailAddress').after('<span class="text-danger">' + errors.email[0] + '</span>');
                    }
                }
            });
        });




        //sign up 
        $('#register').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission
        
            // Clear previous error messages
            $('.text-danger').text('');
        
            // Collect form field values
            var firstName = $('#firstName').val();           
            var lastName = $('#surnameName').val();
            var currentCity = $('#search_city').val();
            var cityId = $('#selected_city_id').val();
            var email = $('#emailSignUp').val();
            var password = $('#passwordSignUp').val();
            var currentUrl = window.location.href; // Get the current page URL
            var errors = false;
        
            // Validate fields
            if (firstName === "") {
                $('.first_name_msg').text('First name is required');
                errors = true;
            }
            if (lastName === "") {
                $('.last_name_msg').text('Surname is required');
                errors = true;
            }
            if (currentCity === "") {
                $('.currentCity_msg').text('Current city is required');
                errors = true;
            }
            if (email === "") {
                $('.username_msg').text('Email is required');
                errors = true;
            } else if (!validateEmail(email)) {
                $('.username_msg').text('Invalid email address');
                errors = true;
            }
            if (password === "") {
                $('.password_msg').text('Password is required');
                errors = true;
            } else if (password.length < 8) {
                $('.password_msg').text('Password must be at least 8 characters long');
                errors = true;
            }
        
		  var termsChecked = $('.term').is(':checked');

			if (!termsChecked) {
				$('.term_msg').text('You must agree to the terms and conditions');
				errors = true;
			}
          
            if (errors) {
                return;
            }
        
            var token = $('meta[name="csrf-token"]').attr('content');
        
            $.ajax({
                type: 'POST',
                url: base_url + 'register_user', // Use the form's action attribute
                data: {
                    first_name: firstName,
                    last_name: lastName,
                    current_city: currentCity,
                    city_id: cityId,
                    email: email,
                    password: password,
                    redirect_url: currentUrl, // Pass the current URL for redirection after verification
                    _token: token       
                },
                success: function(response) {
                    if (response == 0) {
                        $('.error-msg').removeClass('d-none').text('Failed to send verification email. Please try again later.');  
                    } else if (response.emailExists) {
                        $('.username_msg').text('Email already exists. Please choose another email.').css('color', 'red');
                    }  else {
                        $('.success-msg').removeClass('d-none').text('Registration successful. Please check your email for verification.');
                        
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON.errors;
                    $('.text-danger').remove(); // Clear previous errors
                    if (errors.email) {
                        $('#emailAddress').after('<span class="text-danger">' + errors.email[0] + '</span>');
                    }
                }
            });
        });
        
        // Function to validate email format
        function validateEmail(email) {
            var emailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailRegex.test(email);
        }

        // Real-time email validation
        $('#emailSignUp').on('input', function() {
            var email = $(this).val();
            if (!validateEmail(email)) {
                $('.username_msg').text('Invalid email address').css('color', 'red');
            } else {
                $('.username_msg').text('').css('color', '');
            }
        });

        $(document).ready(function() {
            // Get the current URL
            var currentUrl = window.location.href;
        
            // Check if the URL contains the 'varification' path and a 'token' parameter
            if (currentUrl.includes('varification') && currentUrl.includes('token=')) {
                // Open the modal
                $('#signInModal').modal('show');
            }
            
            // Check if URL has openModal parameter
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('openModal')) {
                const modalId = urlParams.get('openModal');
                if (modalId) {
                    $('#' + modalId).modal('show');
                }
            }
            
            // Add event handlers to maintain city field spacing when interacting with other form fields
            $('input, select, textarea').on('focus click blur', function() {
                // Apply spacing to city field label
                var cityLabel = $('.tr-city-field .tr-field-label');
                if (cityLabel.length) {
                    cityLabel.css({
                        'margin-bottom': '8px',
                        'display': 'block'
                    });
                }
                
                // Also ensure the spacing is maintained when the tr-value-field class is present
                $('.tr-city-field.tr-value-field .tr-field-label').css({
                    'margin-bottom': '8px',
                    'display': 'block'
                });
            });
        });
        
//resetpassword
$('#reset-password').on('submit', function(e) {
    e.preventDefault(); // Prevent default form submission

    var newPassword = $('#newPassword').val();
    var confirmPassword = $('#confirmPassword').val();
    var token = $('#token').val();

    // Clear previous error messages
    $('.password-msg, .confirm-password-msg').text('').css('color', '');

    // Validate passwords
    if (newPassword === "") {
        $('.password-msg').text('Password is required').css('color', 'red');
        return;
    } 
    if (newPassword.length < 8) {
        $('.password-msg').text('Password must be at least 8 characters long').css('color', 'red');
        return;
    }
    if (confirmPassword === "") {
        $('.confirm-password-msg').text('Confirm Password is required').css('color', 'red');
        return;
    } 
    if (newPassword !== confirmPassword) {
        $('.confirm-password-msg').text('Passwords do not match').css('color', 'red');
        return;
    }

    // CSRF token
    var csrfToken = $('meta[name="csrf-token"]').attr('content');

    // AJAX request
    $.ajax({
        type: 'POST',
        url: base_url + 'save_reset_password', // Your reset password endpoint
        data: {
            password: newPassword,
            email: token,
            _token: csrfToken
        },
        success: function(response) {
            if (response == 1) {
                $('.success-msg').removeClass('d-none').text('Password reset successful. You can now login.');
                $('#resetPswdModal').modal('hide'); 
                $('#signInModal').modal('show'); 
               
               
            } else {
                // Handle errors from the server
                $('.password-msg').text(response.message || 'An error occurred').css('color', 'red');
            }
        },
        error: function(xhr) {
            // Handle AJAX errors
            $('.password-msg').text('An error occurred. Please try again.').css('color', 'red');
        }
    });
});


//end passwod
      



});

 // --- Handle City Selection ---
 $(document).on('click', '.city-option', function() {
    var cityName = $(this).text();
    var cityId = $(this).data('id');
    $('#search_city').val(cityName);
    $('#selected_city_id').val(cityId);
    $('#citylist').hide();
});

// --- Hide Dropdown on Outside Click ---
$(document).on('click', function(e) {
    if (!$(e.target).closest('#search_city, #citylist').length) {
        $('#citylist').hide();
    }
});


//location search 
$(document).ready(function(){
    let debounceTimeout;

    $(document).on('keyup', '#search_city', function(){
        clearTimeout(debounceTimeout);
        
        debounceTimeout = setTimeout(function() {
            var value = $('#search_city').val();
            if(value != ""){
                $("#citylist").css("display", "block");
                $(".hotel_recent_his").removeClass("d-none");
                
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: base_url + 'search_city',
                    data: { 
                        'val': value,
                        '_token': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response){
                        $('#citylist').html("");
                        response.forEach(function(country) {
                            $('#citylist').append(
                                '<li data-id="'+ country.id +'" >' +
                                    '<div class="tr-place-info">' +
                                        '<div class="tr-location-icon">' + 
                                            '<svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg"><g clip-path="url(#clip0_441_2146)"><path d="M13.125 6.73828C13.125 11.1133 7.5 14.8633 7.5 14.8633C7.5 14.8633 1.875 11.1133 1.875 6.73828C1.875 5.24644 2.46763 3.8157 3.52252 2.76081C4.57742 1.70591 6.00816 1.11328 7.5 1.11328C8.99184 1.11328 10.4226 1.70591 11.4775 2.76081C12.5324 3.8157 13.125 5.24644 13.125 6.73828Z" stroke="#5E5E5E" stroke-linecap="round" stroke-linejoin="round"/><path d="M7.5 8.61328C8.53553 8.61328 9.375 7.77382 9.375 6.73828C9.375 5.70275 8.53553 4.86328 7.5 4.86328C6.46447 4.86328 5.625 5.70275 5.625 6.73828C5.625 7.77382 6.46447 8.61328 7.5 8.61328Z" stroke="#5E5E5E" stroke-linecap="round" stroke-linejoin="round"/></g><defs><clipPath id="clip0_441_2146"><rect width="15" height="15" fill="white" transform="translate(0 0.488281)"/></clipPath></defs></svg>' + 
                                        '</div>' +
                                        '<div class="tr-location-info">' + country.value + ', ' + country.country + '</div>' +
                                    '</div>' +
                                '</li>'
                            );
                        });
                    }
                });
            } else {
                $('#citylist').append("");
            }
        }, 300); 
    });

    $(document).on('click', "li", function(){
        var selectedText = $(this).text();
        var selectedId = $(this).data('id');
        
        $("#search_city").val(selectedText).attr('data-id', selectedId);
        $('#citylist').fadeOut();
        
        // Ensure the label spacing is maintained
        var cityLabel = $('.tr-city-field .tr-field-label');
        cityLabel.css({
            'margin-bottom': '8px',
            'display': 'block'
        });
        
        // Add the class but preserve the styling
        if ($("#search_city").val() || $("#search_city").attr('data-id')) {
          var cityField = $("#search_city").closest('.tr-field');
          cityField.addClass('tr-value-field');
          // Ensure the label maintains its spacing even with the class added
          cityField.find('.tr-field-label').css({
            'margin-bottom': '8px',
            'display': 'block'
          });
        } else {
          $("#search_city").closest('.tr-field').removeClass('tr-value-field');
        }
    });
});


$(document).ready(function() {
  // Function to attach event listener to changePictureLink
  
  // Handle form submission with jQuery
  $('#changeimg').submit(function(event) {
      event.preventDefault();

      var formData = new FormData($(this)[0]); // Initialize FormData with the form

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: base_url + 'changeprofileimg', 
          data: formData, 
          processData: false, 
          contentType: false, 
          success: function(response) {
              $('#exampleModalss').modal('hide');
              $('.getimge').html(response.image_result);
              
              $('#uploadButton').css('display','none')
              attachEventListener();
              
              $('.navbar-nav').html(response.usernavimg);
              $('#changeimg, #uploadButton').show();
          },
          error: function(xhr, textStatus, errorThrown) {
              console.log(xhr.responseText);
          }
      });
  });
});





	  
	  $(document).ready(function() {

    function attachEventListener() {
        // Remove any existing click event listeners to prevent duplicate triggers
        $(document).off('click', '#changePictureLink').on('click', '#changePictureLink', function(event) {
            event.preventDefault(); 
            $('#fileInput').click();
            $('#changeimg, #uploadButton').toggle();
			$('#uploadButton').css('display','block');
			$('#changeimg').css('display','block');
			
        });
    }

    attachEventListener();

    $('#myForm').submit(function(event) {
        event.preventDefault();

        var formData = new FormData($(this)[0]);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: base_url + 'updateprofile',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#exampleModalss').modal('hide');
                $('.getupdata').html(response.profile_data);
                $('.getimge').html(response.image_result);
                $('.navbar-nav').html(response.usernavimg);
                $('.user-msg').html('');

                // Reattach event listener after content update
                attachEventListener();
            },
            error: function(xhr, textStatus, errorThrown) {
                if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    if (errors.Username) {
                        $('.user-msg').html(errors.Username[0]).show().css('color', 'red');
                    } else {
                        $('.user-msg').html('').hide();
                    }
                } else {
                    console.log(xhr.responseText);
                }
            }
        });
    });
});


function myFunction() {
    var x = document.getElementById("myInput");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
  }


  function myFunctions() {
    var x = document.getElementById("myInput2");
    if (x.type === "password") {
      x.type = "text";
    } else {
      x.type = "password";
    }
}
