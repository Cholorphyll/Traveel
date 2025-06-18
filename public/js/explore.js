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
  

//start add review
$(document).ready(function() {

  $(document).on('submit', '#s-review', function(event) {
    event.preventDefault(); 
   
  var sightId = $('#sightId').val();
  var rating = $(".recommend.selected").val();
  var name = $('.add_review #name').val();
  var email = $('.add_review #email').val();
  var review = $('.add_review #review').val();
  var files = $('#files')[0].files;
  var imagedata = new FormData();
  var gowith = $(".go-with .selected").text();


  // Field validation
  var isValid = true;

  if (name == '') {
    $('.add_review #name-error').text('Name is required.').css('color', 'red');
    isValid = false;
  } else {
    $('.add_review #name-error').text('');
  }

  if (email == '') {
    $('.add_review #email-error').text('Email is required.').css('color', 'red');
    isValid = false;
  } else if (!isValidEmail(email)) {
    $('.add_review #email-error').text('Please enter a valid email.').css('color', 'red');
    isValid = false;
  } else {
    $('.add_review #email-error').text('').css('color', 'red');
  }

  if (review == '') {
    $('.add_review #review-error').text('Review Description is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#review-error').text('');
  }

  

  if (gowith == '') {
    $('#go-with-error').text('this field is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#go-with-error').text('');
  }

  if (!isValid) {
    return;
  }
  $('.tr-write-review-modal').removeClass('open');
  $('body').removeClass('modal-open'); 
  $('body').css('overflow', 'auto'); 

  //$('.tr-write-review-modal').removeClass('open');
 // $('.add_review').addClass("d-none");
  
  imagedata.append('rating', rating);
  imagedata.append('name', name);
  imagedata.append('email', email);
  imagedata.append('review', review);
  imagedata.append('sightId', sightId);
  imagedata.append('gowith', gowith);
  
  // Add CSRF token
  var csrfToken = $('meta[name="csrf-token"]').attr('content');
  imagedata.append('_token', csrfToken);

  // Add each selected file to the FormData
  for (var i = 0; i < files.length; i++) {
    imagedata.append('files[]', files[i]);
  }


  // Now, make the AJAX request
  $.ajax({
    type: 'POST',
    processData: false,
    contentType: false,
    url: base_url + 'add_sightreview',
    data: imagedata,
    success: function(response) {
    //  $('.add_review').addClass("d-none");
    //$('.tr-write-review-modal').removeClass('open');
	  $(".pip").text('');
      $('.add_review #name').val('');
      $('.add_review #email').val('');
      $('.add_review #review').val('');
      $('.tr-file-upload-image').attr('src', '');
   //update rating
       $('.review-rating-count').text(response.averageRatingPercentage+'%');
       $('.rcmd-count').text(response.positiveReviews);
       $('.notrcmd-count').text(response.negativeReviews);
     //end update rating
      $('.review-data').html(response.reviewhtml);
		
      $('.filter-option').removeClass('active');
      $('#msg').html('<div class="alert alert-success" role="alert">Review added successfully.</div>');

      var alertTimeout = setTimeout(function() {
        $('#msg').empty();
      }, 60000);
    },
    error: function(xhr, status, error) {
      console.log(error);
    }
  });
});

//filter review

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
});

//end add review
function isValidEmail(email) {
  // Regular expression for email validation
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}


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
  
  $.ajax({
    url: base_url + 'add_sight_images',
    method: 'POST',
    data: formData,
    contentType: false,
    processData: false, 
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
