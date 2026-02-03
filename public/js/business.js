var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
.location.port : '');
var base_url = baseURL + '/';
    
    $(document).ready(function() {
        // Hide additional rows initially
        $('.additional-row').hide();

        // Show additional rows when clicking "View More"
        $('#viewMore').on('click', function() {
            $('.additional-row').show();
            $('#viewMoreRow').hide();
        });

        // Hide additional rows when clicking "View Less"
        $('#viewLess').on('click', function() {
            $('.additional-row').hide();
            $('#viewMoreRow').show();
        });
    });


    
$(document).ready(function() {
    // Function to attach event listener to changePictureLink
    function attachEventListener() {
        $('#changePictureLink').on('click', function(event) {
            event.preventDefault(); // Prevent the default action of the link
            $('#fileInput').click(); 
  
            // Toggle form and submit button display
            // $('#changeimg, #uploadButton').toggle();
            $('#changeimg').show();
            $('#uploadButton').css('display','block');
            $('#uploadButton').removeClass('d-none');
            
        });
    }
  
    // Initial attachment of event listener
    attachEventListener();
  
    // Handle form submission with jQuery
    $('#changeimg').submit(function(event) {
        event.preventDefault(); 
      
        var formData = new FormData($(this)[0]); // Initialize FormData with the form
     
        formData.append('test', 1); 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'POST',
            url: base_url + 'busi_changeprofileimg', 
            data: formData,
            processData: false, 
            contentType: false, 
            success: function(response) { 
                $('#exampleModalss').modal('hide');
                $('.getimge').html(response.image_result);
                $('.nav-result').html(response.usernavimg);
                $('#uploadButton').css('display','none')
                // Reattach event listener after AJAX success
                attachEventListener();
                
                // Show the form and upload button again
              //  $('#changeimg, #uploadButton').show();
            },
            error: function(xhr, textStatus, errorThrown) {
                console.log(xhr.responseText);
            }
        });
    });

    $('#myForm').submit(function(event) {
        event.preventDefault();
  
        var formData = new FormData($(this)[0]); // Initialize FormData with the form
  
        // If all validations pass, submit the form using AJAX
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: base_url + 'bus_updateprofile', 
          data: formData,
          processData: false,
          contentType: false,
          success: function(response) {
            $('#exampleModalss').modal('hide');
            $('.getupdata').html(response.profile_data);
            $('.nav-result').html(response.usernavimg);
            $('.getimge').html(response.image_result);
            $('.user-msg').html('');

            // Reattach event listener after AJAX success
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



$(document).ready(function() {
  $('#add-uploadimg').submit(function(event) {
    event.preventDefault();

    var formData = new FormData($(this)[0]); // Initialize FormData with the form
   
    $('.up-text').removeClass('d-none');
    $('.upload-button').prop('disabled', true);
    // If all validations pass, submit the form using AJAX
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'add_sight_busi_img', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('.upload-button').prop('disabled', false);
        $('.up-text').addClass('d-none');
        $('.show-img').html(response);
        $('#previewImage').css('display','none');
        
       
      },
      
    });
  });

  
$(document).on('click', '.delete-sight_image', function() {
  var id = $(this).data('id');
  var sightid = $(this).data('sightid');

  if (confirm('Are you sure you want to delete this image?')) {
    $.ajax({
      url: base_url + 'delete_sight_image',
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      data: {
        id: id,
        sightid: sightid
      },
      success: function(response) {
        $('.show-img').html(response);
      }
    });
  }
});
});

$(document).ready(function() {
  $('#claim-list').submit(function(event) {
    event.preventDefault();

    // Check if all required fields are filled
    var isValid = true;

    $('#claim-list input[text],input[email], #claim-list select[required]').each(function() {
	//	alert(this.value);
      if (!this.value.trim() || this.value === '-1') {
		  
        isValid = false;
		 
        $(this).next('.validation-msg').text('This field is required.').css('color', 'red');
      } else {
        $(this).next('.validation-msg').text('');
      }
    });

    //  alert(isValid);
  
    // Special case for checkboxes
    if (!$('#flexCheckDefault1').is(':checked')) {
      isValid = false;
      $('#check1-msg').text('This field is required.').css('color','red');
    } else {
      $('#check1-msg').text('');
    }

    if (!$('#flexCheckDefault2').is(':checked')) {
      isValid = false;
      $('#check2-msg').text('This field is required.').css('color','red');
    } else {
      $('#check2-msg').text('');
    }

    if (!$('#flexCheckDefault3').is(':checked')) {
      isValid = false;
      $('#check3-msg').text('This field is required.').css('color','red');
    } else {
      $('#check3-msg').text('');
    }
	  
	   if (isValid == false) {
      return;
     }
// alert();
	

    var formData = new FormData($(this)[0]); // Initialize FormData with the form
   
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'claimlist_save', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.redirect_url) {
          window.location.href = response.redirect_url;
        }
        // $('.show-img').html(response);
     
      //   if(response == 22){
      //     $('#exampleModal1').removeClass('show');
      //     $('body').removeClass('modal-open');
      //     $('.modal-backdrop').remove();
          
          
      //  return  $('#exampleModal2').modal('show');
         
      //   }
      },
    });
  });
});

// document.addEventListener("DOMContentLoaded", function() {
//   var businessListingLink = document.getElementById("business-Listing");
//   var businessInfoLink = document.getElementById("Business-info");
//   var Photos_videos = document.getElementById("Photos-&-videos");


//   var busList = document.querySelector(".bus-list");
//   var busInfo = document.querySelector(".bus-info");
//   var photos = document.querySelector(".photos");

//   businessListingLink.addEventListener("click", function(event) {
//       event.preventDefault();
//       busList.classList.remove("d-none");
//       busInfo.classList.add("d-none");
//       photos.classList.add("d-none");
//       businessListingLink.css("color","blue");
//     //  businessListingLink.css("text-decoration: underline;color: blue; ");
//   });

//   businessInfoLink.addEventListener("click", function(event) {
//       event.preventDefault();
//       busList.classList.add("d-none");
//       busInfo.classList.remove("d-none");
//       photos.classList.add("d-none");
//       businessInfoLink.css("color","blue");
//   });

//   Photos_videos.addEventListener("click", function(event) {
//     event.preventDefault();
  
//     busList.classList.add("d-none");
//     busInfo.classList.add("d-none");
//     photos.classList.remove("d-none");
//     businessInfoLink.css("color","blue");
// });


// });








$(document).ready(function() {
  $('#edit-business-hotel-detail').submit(function(event) {
    event.preventDefault();
 // Check if all required fields are filled
    let isValid = true;

    $('#claim-list input[required], #claim-list select[required]').each(function() {
      if (!this.value.trim() || this.value === '-1') {
        isValid = false;
        $(this).next('.validation-msg').text('This field is required.').css('color', 'red');
      } else {
        $(this).next('.validation-msg').text('');
      }
    });


    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'update-hotel-data', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModalss').modal('hide');
        $('.update-button').prop('disabled', false);
        $('.general-information').html(response);
        $('.up-text').addClass('d-none');

       
        
               
      },
      
    });
  });
});






$(document).ready(function() {
  $('#update-amenity').submit(function(event) {
    event.preventDefault();

    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button1').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'update-hotel-amenity', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModal1').modal('hide');
        $('.update-button1').prop('disabled', false);
        $('.get-amenity').html(response);
        $('.up-text').addClass('d-none');               
      },
      
    });
  });
});



$(document).ready(function() {
  $('#update-contact').submit(function(event) {
    event.preventDefault();

    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button2').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'update_hotel_contact', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModal2').modal('hide');
        $('.update-button2').prop('disabled', false);
        $('.getcontact').html(response);
        $('.up-text').addClass('d-none');               
      },
      
    });
  });
});





$(document).ready(function() {
  $('#add-hotel-img').submit(function(event) {
    event.preventDefault();

    var formData = new FormData($(this)[0]); // Initialize FormData with the form
   
    $('.up-text').removeClass('d-none');
    $('.upload-button').prop('disabled', true);
    // If all validations pass, submit the form using AJAX
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'save_add_hotel_images', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('.upload-button').prop('disabled', false);
        $('.up-text').addClass('d-none');
        $('.show-img').html(response);
        $('#previewImage').css('display','none');
        
       
      },
      
    });
  });

//delete hotel 

  // Use event delegation for the delete icon click event
  $(document).on('click', '.delete-hotel-image', function() {
    var id = $(this).data('id');
    var restid = $(this).data('restid');

    if (confirm('Are you sure you want to delete this image?')) {
      $.ajax({
        url: base_url + 'delete_hotel_image',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        data: {
          id: id,
          hotelid: restid
        },
        success: function(response) {
          $('.show-img').html(response);
        }
      });
    }
  });




//end delete hotel image



});





$(document).ready(function() {
  $('#imagevarification').submit(function(event) {
    event.preventDefault();
  let isValid = true;

    $('#add-hotel-img input[required], #add-hotel-img select[required]').each(function() {
      if (!this.value.trim() || this.value === '-1') {
        isValid = false;
        $(this).next('.validation-msg').text('This field is required.').css('color', 'red');
      } else {
        $(this).next('.validation-msg').text('');
      }
    });


    var fileInput = $('#fileInput')[0];
  
    
    if (fileInput.files.length == 0) {
        isValid = false;
         $('.image-msg').text('Image is required.').css('color', 'red');
         return false;
    } else {
        $('.image-msg').text('');
    }

    var formData = new FormData($(this)[0]); // Initialize FormData with the form
   
    $('.up-text').removeClass('d-none');
    $('.upload-button').prop('disabled', true);
    // If all validations pass, submit the form using AJAX
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'image_vatification', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModal2').modal('hide');
        $('.upload-button').prop('disabled', false);
        $('.up-text').addClass('d-none');
        $('.varify-img').html(response);
        $('#previewImage').css('display','none');
        
        
      },
      
    });
  });
});




$(document).ready(function() {
  $('#varifyphone').submit(function(event) {
    event.preventDefault();
  let isValid = true;


    var phone = $('.phone').val();
  
    var phonePattern = /^\d{10}$/;

   
    if (phone.trim() === "") {
      isValid = false;
      $('.phone-msg').text('This field is required.').css('color', 'red');
    } else if (!phonePattern.test(phone)) { // Check if the phone number format is valid
      isValid = false;
      $('.phone-msg').text('Please enter a valid 10-digit phone number.').css('color', 'red');
    } else {
      $('.phone-msg').text(''); // Clear error message if phone number is valid
    }

    var formData = new FormData($(this)[0]);
   
    $('.up-text').removeClass('d-none');
    $('.upload-button').prop('disabled', true);
    // If all validations pass, submit the form using AJAX
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'send_business_sms', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.redirect_url) {
          window.location.href = response.redirect_url;
        }
        // $('#exampleModal2').modal('hide');
        // $('.upload-button').prop('disabled', false);
        // $('.up-text').addClass('d-none');
        // $('.varify-img').html(response);
        // $('#previewImage').css('display','none');
        
        
      },
      
    });
  });
});







$(document).ready(function() {
  $('#submit_otp').submit(function(event) {
    event.preventDefault();
  let isValid = true;


    var phone = $('.otp').val();
  
    var phonePattern = /^\d{10}$/;

   alert(phone);
    if (phone.trim() === "") {
      isValid = false;
      $('.otp-msg').text('This field is required.').css('color', 'red');
      return false;
    } else {
      $('.otp-msg').text(''); 
    }

    var formData = new FormData($(this)[0]);
   
    $('.up-text').removeClass('d-none');
    $('.upload-button').prop('disabled', true);
   
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'verifyOtp', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        if (response.redirect_url) {
          window.location.href = response.redirect_url;
        }
       
        
      },
      
    });
  });
});






$(document).ready(function() {
  $('#edit-gi-rest').submit(function(event) {
    event.preventDefault();


    
    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'edit-gi-rest', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModalss').modal('hide');
        $('.update-button').prop('disabled', false);
        $('.general-information').html(response);
        $('.up-text').addClass('d-none');

       
        
               
      },
      
    });
  });
});


$(document).ready(function() {
  $('#update-restaurant-contact').submit(function(event) {
    event.preventDefault();

    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button2').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'update_restaurant_contact', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModal2').modal('hide');
        $('.update-button2').prop('disabled', false);
        $('.getcontact').html(response);
        $('.up-text').addClass('d-none');               
      },
      
    });
  });
});


// restaurant images



// $(document).ready(function() {
//   $('#add-rest-img').submit(function(event) {
//     event.preventDefault();

//     var formData = new FormData($(this)[0]); // Initialize FormData with the form
   
//     $('.up-text').removeClass('d-none');
//     $('.upload-button').prop('disabled', true);
//     // If all validations pass, submit the form using AJAX
//     $.ajax({
//       headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//       },
//       type: 'POST',
//       url: base_url + 'bus_add_rest_images', 
//       data: formData,
//       processData: false,
//       contentType: false,
//       success: function(response) {
//         $('.upload-button').prop('disabled', false);
//         $('.up-text').addClass('d-none');
//         $('.show-img').html(response);
//         $('#previewImage').css('display','none');
        
       
//       },
      
//     });
//   });
// });


// $(document).ready(function() {
//   $('#add-rest-img').submit(function(event) {
//     event.preventDefault();

//     var formData = new FormData($(this)[0]); // Initialize FormData with the form
   
//     $('.up-text').removeClass('d-none');
//     $('.upload-button').prop('disabled', true);
//     // If all validations pass, submit the form using AJAX
//     $.ajax({
//       headers: {
//         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//       },
//       type: 'POST',
//       url: base_url + 'bus_add_rest_images', 
//       data: formData,
//       processData: false,
//       contentType: false,
//       success: function(response) {
//         $('.upload-button').prop('disabled', false);
//         $('.up-text').addClass('d-none');
//         $('.show-img').html(response);
//         $('#previewImage').css('display','none');
        
       
//       },
      
//     });
//   });

//   $('.delete-icon').on('click', function() {
//     var id = $(this).data('id');
//     var restid = $(this).data('restid');
//    // var imageWrapper = $(this).closest('.image-wrapper');


//     if(confirm('Are you sure you want to delete this image?')) {
//       $.ajax({
//         url: base_url + 'delete_rest_image', 
//         headers: {
//           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//         },
//         type: 'post',
//         data: {
//           id: id, restid: restid
//         },
//         success: function(response) {
//          // if(response) {
//             $('.show-img').html(response);
//           // } else {
//           //   alert('Failed to delete image.');
//           // }
//         }
    
//       });
//     }
//   });
// });


$(document).ready(function() {
  $('#add-rest-img').submit(function(event) {
    event.preventDefault();

    var formData = new FormData($(this)[0]); // Initialize FormData with the form

    $('.up-text').removeClass('d-none');
    $('.upload-button').prop('disabled', true);

    // If all validations pass, submit the form using AJAX
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'bus_add_rest_images',
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('.upload-button').prop('disabled', false);
        $('.up-text').addClass('d-none');
        $('.show-img').html(response);
        $('#previewImage').css('display', 'none');
      }
    });
  });

  // Use event delegation for the delete icon click event
  $(document).on('click', '.delete-icon', function() {
    var id = $(this).data('id');
    var restid = $(this).data('restid');

    if (confirm('Are you sure you want to delete this image?')) {
      $.ajax({
        url: base_url + 'delete_rest_image',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        data: {
          id: id,
          restid: restid
        },
        success: function(response) {
          $('.show-img').html(response);
        }
      });
    }
  });
});




$(document).ready(function() {
  $('#update_menu_link').submit(function(event) {
    event.preventDefault();


    
    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'update_menu_link', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModalss').modal('hide');
        $('.update-button').prop('disabled', false);
        $('.updated-menulink').html(response);
        $('.up-text').addClass('d-none');

       
        
               
      },
      
    });
  });
});



$(document).ready(function() {
  $('#add-rest-menu').submit(function(event) {
    event.preventDefault();


    
    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button').prop('disabled', true);
 
    $.ajax({
      headers: {

        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'update_menu', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModalss').modal('hide');
        $('.update-button').prop('disabled', false);
        $('.show-menu_items').html(response);
        $('.up-text').addClass('d-none');

       
        
               
      },
      
    });
  });
});




$(document).ready(function() {
  $('#edit_sight_info').submit(function(event) {
    event.preventDefault();


    
    var formData = new FormData($(this)[0]); 
   
    $('.up-text').removeClass('d-none');
    $('.update-button').prop('disabled', true);
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'save_sight_info', 
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        $('#exampleModalss').modal('hide');
        $('.update-button').prop('disabled', false);
        $('.general-information').html(response);
        $('.up-text').addClass('d-none');

       
        
               
      },
      
    });
  });
});



