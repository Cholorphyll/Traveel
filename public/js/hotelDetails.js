var url = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = url + '/';
var testurl = url + '/';



$(window).on('load', function() {
 // var id =  $('#hid').text();  
  
 var hname =  $('#hname').text();
  var hotelid =  $('#hotelid').text();
  var Latitude =  $('#Latitude').text();  
  var longnitude =  $('#longnitude').text();  
  var hid =  $('#hid').text(); 
  
  $.ajax({
    type: 'Post',  
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: testurl + 'addHotledetailFaq',
    data: { 'hotelid': hotelid,'Latitude':Latitude,'longnitude':longnitude,'hname':hname,'hid':hid },
    success: function(response) {
   
      var hotelfaq= response.html
      
      $('#detailfaqdata').html(hotelfaq);
  
    },
  
   });
  });
//start review


function selectButton(element) {
  var container = document.querySelector('.go-with');  
  var items = container.getElementsByTagName('li');  
  for (var i = 0; i < items.length; i++) {
      items[i].classList.remove('selected');
  }
  
  element.classList.add('selected');
}


// add review

//$(document).on('click', '#addReview', function() {  
  $('#addReview').on('submit', function(e) {
    e.preventDefault(); // Prevent the default form submission

  var hotelid = $('#hid').text().trim();
  var hname = $('#hname').text().trim();
    
  // Get all ratings
  var rating = $(".star-exp .star.selected:last").data('rating');
  var cleanrating = $(".star-cleanliness .star.selected:last").data('rating');
  var starlocation = $(".star-location .star.selected:last").data('rating');
  var starservice = $(".star-service .star.selected:last").data('rating');
  var starvalue = $(".star-value .star.selected:last").data('rating');
  var gowith = $(".go-with .selected").text();
    
  var name = $('#name').val().trim();
  var email = $('#email').val().trim();
  var review = $('#review').val().trim();


  // var files = $('#files')[0].files;
  // var imagedata = new FormData();  
  var files = $('#files')[0].files;
  var imagedata = new FormData();

  // Append each file to the FormData object
  for (var i = 0; i < files.length; i++) {
      imagedata.append('files[]', files[i]);
  }

 
  // Field validation
  var isValid = true;
  var errorMessages = [];

  // Validate name
  if (!name) {
    $('#name-error').text('Name is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#name-error').text('');
  }

  // Validate email
  if (!email) {
    $('#email-error').text('Email is required.').css('color', 'red');
    isValid = false;
  } else if (!isValidEmail(email)) {
    $('#email-error').text('Please enter a valid email.').css('color', 'red');
    isValid = false;
  } else {
    $('#email-error').text('');
  }

  // Validate review
  if (!review) {
    $('#review-error').text('Review is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#review-error').text('');
  }

  // Validate ratings
  if (!rating) {
    $('#rating-error').text('Overall rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#rating-error').text('');
  }

  if (!cleanrating) {
    $('#cleanliness-rating-error').text('Cleanliness rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#cleanliness-rating-error').text('');
  }

  if (!starlocation) {
    $('#location-rating-error').text('Location rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#location-rating-error').text('');
  }

  if (!starservice) {
    $('#service-rating-error').text('Service rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#service-rating-error').text('');
  }

  if (!starvalue) {
    $('#value-rating-error').text('Value rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#value-rating-error').text('');
  }

  // Validate go with
  if (!gowith) {
    $('#go-with-error').text('Please select who you traveled with.').css('color', 'red');
    isValid = false;
  } else {
    $('#go-with-error').text('');
  }

  if (!isValid) {
    return;
  }


  //$('.add_review').modal('hide');
  imagedata.append('rating', rating);
  imagedata.append('name', name);
  imagedata.append('email', email);
  imagedata.append('review', review);
  imagedata.append('hotelid', hotelid);
  imagedata.append('hotel_email', email);
  imagedata.append('hname', hname);

  //imagedata.append('exprating', exprating);
  imagedata.append('cleanrating', cleanrating);
  imagedata.append('starlocation', starlocation);
  imagedata.append('starservice', starservice);
  imagedata.append('starvalue', starvalue);
  imagedata.append('gowith', gowith);

  
  // Add CSRF token
  var csrfToken = $('meta[name="csrf-token"]').attr('content');
  imagedata.append('_token', csrfToken);

  
  // Add each selected file to the FormData
  for (var i = 0; i < files.length; i++) {
      imagedata.append('files[]', files[i]);
  }
  $('.add_review').modal('hide');
  // Now, make the AJAX request
  $.ajax({
    type: 'POST',
    processData: false,
    contentType: false,
    url: testurl + 'add_Hotelreview',
    data: imagedata,
    success: function(response) {
      // $('.add_review').addClass("d-none");
        $('#name').val('');
        $('#email').val('');
        $('#review').val('');
        $('.getreview').html(response);
  
        // Reset the star ratings
        $('.star').removeClass('selected');
        $('.go-with .btn').removeClass('selected');
  
        // Reset file input
        $('#files').val('');
     
        
      $('.getreview').html(response);
   // $('.tr-write-review-modal').removeClass('open');
      $('#msg').html('<div class="alert alert-success" role="alert">Review added successfully.</div>');

      var alertTimeout = setTimeout(function() {
        $('#msg').empty();
      }, 60000);
    },
    // error: function(xhr, status, error) {
    //   console.log(error);
    // }
  });
});
//end add review



//review filter


$(document).on('click', '#addReview', function() {  

  var hotelid = $('#hid').text();
  var email = $('#email').text();
  var hname = $('#hname').text();
  
  var rating = $(".star.selected:last").data('rating');
  var exprating = $(".star-exp .star.selected:last").data('rating');
  var cleanrating = $(".star-cleanliness .star.selected:last").data('rating');

  var starlocation = $(".star-location .star.selected:last").data('rating');
  var starservice = $(".star-service .star.selected:last").data('rating');
  var starvalue = $(".star-value .star.selected:last").data('rating');

  var gowith = $(".go-with .selected").text();
  


  
  var name = $('.add_review #name').val();

  var email = $('.add_review #email').val();

  var review = $('.add_review #review').val();

  // var files = $('#files')[0].files;
  // var imagedata = new FormData();  
  var files = $('#files')[0].files;
  var imagedata = new FormData();

  // Append each file to the FormData object
  for (var i = 0; i < files.length; i++) {
      imagedata.append('files[]', files[i]);
  }

  // Field validation
  var isValid = true;

  if (name == '') {
    $('.add_review #name-error').text('Name is required.').css('color', 'red');
    isValid = false;
  } else {
    $('.add_review #name-error').text('');
  }

  if (rating == undefined) {
    $('.add_review #rating-error').text('Rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('.add_review #rating-error').text('');
  }

  // start rat


  if (gowith == '') {
    $('#go-with-error').text('this field is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#go-with-error').text('');
  }
  
 // end rat

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

  if (!isValid) {
    return;
  }


  //$('.add_review').modal('hide');
  imagedata.append('rating', rating);
  imagedata.append('name', name);
  imagedata.append('email', email);
  imagedata.append('review', review);
  imagedata.append('hotelid', hotelid);
  imagedata.append('hotel_email', email);
  imagedata.append('hname', hname);

  imagedata.append('exprating', exprating);
  imagedata.append('cleanrating', cleanrating);
  imagedata.append('starlocation', starlocation);
  imagedata.append('starservice', starservice);
  imagedata.append('starvalue', starvalue);
  imagedata.append('gowith', gowith);

  
  // Add CSRF token
  var csrfToken = $('meta[name="csrf-token"]').attr('content');
  imagedata.append('_token', csrfToken);

  
  // Add each selected file to the FormData
  for (var i = 0; i < files.length; i++) {
      imagedata.append('files[]', files[i]);
  }
  $('.add_review').modal('hide');
  // Now, make the AJAX request
  $.ajax({
    type: 'POST',
    processData: false,
    contentType: false,
    url: testurl + 'add_Hotelreview',
    data: imagedata,
    success: function(response) {
      // $('.add_review').addClass("d-none");
      $('.add_review #name').val('');
        $('.add_review #email').val('');
        $('.add_review #review').val('');
        $('.getreview').html(response);
  
        // Reset the star ratings
        $('.star').removeClass('selected');
        $('.go-with .btn').removeClass('selected');
  
        // Reset file input
        $('#files').val('');
      $('.getreview').html(response);
      // $('#msg').html('<div class="alert alert-success" role="alert">Review added successfully.</div>');

      // var alertTimeout = setTimeout(function() {
      //   $('#msg').empty();
      // }, 60000);
    },
    // error: function(xhr, status, error) {
    //   console.log(error);
    // }
  });
});


// review filter



$(window).on('load', function() {
var Latitude = $('#Latitude').text();    
var longitude = $('#longnitude').text();     
var hotelid =  $('#hid').text();  
var locationid =  $('#locationid').text(); 
var stars =  $('#stars').text(); 
var propertyTypeId =  $('#propertyTypeId').text(); 

$.ajax({
  headers: {
    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
  },
  type: 'POST',
  url: testurl + 'saveTphotel_nearby',
  data: {
    'Latitude': Latitude,
    'longitude': longitude,
    'hotelid':hotelid,
    'locationid':locationid,
    'stars':stars,
    'propertyTypeId':propertyTypeId,
    '_token': $('meta[name="csrf-token"]').attr('content')
  },
  success: function(response) {
    var updatedHTML = response.html;
    var nearbyhotel = response.html3;  
        $('#nba').html(updatedHTML);        
        $('#sim-hotel').html(nearbyhotel);
      
  }

});
});

document.addEventListener("DOMContentLoaded", function () {
  const stars = document.querySelectorAll('.star');
  const ratingStars = document.getElementById('ratingStars');

  stars.forEach(star => {
      star.addEventListener('click', () => {
          const rating = parseInt(star.getAttribute('data-rating'), 10);
          setRating(rating);
      });
  });

  function setRating(rating) {
      stars.forEach(star => {
          const starRating = parseInt(star.getAttribute('data-rating'), 10);

          if (starRating <= rating) {
              star.classList.add('selected');
          } else {
              star.classList.remove('selected');
          }
      });

    
     // console.log('User rated:', rating);
  }
});


// ---------------------rating js
document.addEventListener("DOMContentLoaded", function () {
  const stars = document.querySelectorAll('.star1');
  const ratingStars = document.getElementById('ratingStars1');

  stars.forEach(star => {
      star.addEventListener('click', () => {
          const rating = parseInt(star.getAttribute('data-rating'), 10);
          setRating(rating);
      });
  });

  function setRating(rating) {
      stars.forEach(star => {
          const starRating = parseInt(star.getAttribute('data-rating'), 10);

          if (starRating <= rating) {
              star.classList.add('selected');
          } else {
              star.classList.remove('selected');
          }
      });

    
     // console.log('User rated:', rating);
  }
});

//end rating js



$(window).on('load', function() {
  if (window.File && window.FileList && window.FileReader) {
    $("#files").on("change", function(e) {
      var files = e.target.files,
        filesLength = files.length;
      for (var i = 0; i < filesLength; i++) {
        var f = files[i]
        var fileReader = new FileReader();
        fileReader.onload = (function(e) {
          var file = e.target;
          $("<span class=\"pip\">" +
            "<img class=\"imageThumb\" src=\"" + e.target.result + "\" title=\"" + file.name + "\"/>" +
            "<br/><span class=\"remove remove-image\"></span>" +
            "</span>").insertAfter("#files");
          $(".remove").click(function() {
            $(this).parent(".pip").remove();
          });

        });
        fileReader.readAsDataURL(f);
      }
      console.log(files);
    });
  } else {
    alert("Your browser doesn't support to File API")
  }
});

function isValidEmail(email) {
  // Regular expression for email validation
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}

// start add photos section


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

  
  $(document).on('click', '.filterchackinout', function() {
    var lid = $('#locationid').text();  
    var hid = $('#hotelid').text(); 
    var cityName = $('#cityName').text();
    //var checkinText = $('.checkinval2').text(); 
    var checkinText = $('.checkinval2').val();
  
    var checkins =  checkinText.replace(/➜/g, '').trim();

   // var checkins = $('#checkinval2').text(); 
  // var checkouts = $('#checkoutval2').text(); 
   
   // var checkout_text = $('.checkoutval2').text(); 
   var checkout_text = $('.checkoutval2').val(); 

    var checkouts =  checkout_text.replace(/➜/g, '').trim();
	var room = $('.room-count').text();
    var guest = $('.guest-count').text();
	  
	  $('#errormsg').text('');
   if (!checkins || !checkouts) {
		  $('#errormsg').text('Please choose Checkin and Checkout dates').css({
			  'color': 'red',
			  'margin': '10px 3px'
		  });
		  return; // Stop execution if either value is empty
    }else{
		 $('#errormsg').text('');
	}
		
	  
    $('#errormsg').text('')
    $('#totaldates').text(checkins+' - '+checkouts)
	$('.price-loader').removeClass('d-none');  
    $('.filterchackinout').addClass('d-none'); 
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url:  testurl+ 'getSignature',
          data: { 'lid': lid,'hid':hid,'cityName':cityName,'checkin':checkins,'checkout':checkouts,'guest':guest,'rooms':room,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.price-loader').addClass('d-none');
            $('.filterchackinout').removeClass('d-none');
            if(response == 0){
              return $('#errormsg').text('Please choose Checkin and Checkout date').css({
                'color': 'red',
                'margin': '10px 3'
              });
            }else if(response == 2){
              return $('#errormsg').text('Incorrect Date').css({
                'color': 'red',
                'margin': '10px 3'
              });
            }

           setTimeout(function() {
              $('#errormsg').text('');
          }, 3000);
         
         // $('#h-price').removeClass('d-none'); 
            $('#hotel_price').html(response);              
          
          }
      });
    }); 


 
  $('#filterchackinout2').click(function(e) {
       
    
        var childrensValue = parseInt($('.Childrens').val());
    
    
      
        var checkin = $('.checkinval2').text() + '-' + $('.checkoutval2').text();
        //  var checkout = $('#checkoutdate').val();
        var rooms = $('#totalroom2').text();
        var guest = $('.guestval2').text();
        // var child1 = $('.child-1').val();
        // var child2 = $('.child-2').val(); 
     
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: base_url + 'filter_availble_hotel',
          data: {
            'checkin': checkin,
            'rooms': rooms,
            'guest': guest,
            '_token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
    
          }
        });
      });
      

 // Commented out the auto-update of meta description on page load
// $(window).on('load', function() {
//        var id = $('#hid').text();
//        var locationid = $('#locationid').text();
    
//         $.ajax({
//           headers: {
//             'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//           },
//           type: 'POST',
//           url: testurl + 'updatemetadesc',
//           data: {
//             'id': id,
//             'locationid': locationid,           
//             '_token': $('meta[name="csrf-token"]').attr('content')
//           },
//           success: function(response) {
//     		 if (response.metaTagTitle) {
//                 // Update both document title and title tag
//                 document.title = response.metaTagTitle;
//                 $('#page-title').text(response.metaTagTitle);
//             }
            
//             if (response.MetaTagDescription) {
//                 // Update meta description using ID selector
//                 $('#meta-description').attr('content', response.MetaTagDescription);
//             }      
//           }
//         });
//       });
  
     //filter room 


   //filter_hotel_room_with_date
    $(document).ready(function() {
  var pagetype = $('#pagetype').text().trim();
    

  let endpoint;

  if (pagetype == "withdate") {
      endpoint = 'filter_hotel_room_with_date'; 
  } else if (pagetype == "withoutdate") {
      endpoint = 'filter_hotel_room'; 
  }
  

  $(document).on('click', '.filter', function () {
      // Toggle 'selected' class
      $(this).parent().toggleClass('selected');
      sendFilterRequest();
  });

  function sendFilterRequest() {
      var hotelid = $('#hotelid').text();
      var checkout = $('.checkout').text();
      var checkin = $('.checkin').text();
      var selectedFilters = $('.filter:checked').map(function() {
          return $(this).data('value');
      }).get();

      console.log('Selected Filters:', selectedFilters); // Log selected filters for debugging

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: testurl + endpoint,
          data: {
              'value': selectedFilters,
              'hotelid': hotelid,
              'checkin': checkin,
              'checkout': checkout
          },
          success: function (response) {
            console.log(response)
            //  console.log('AJAX Response:', response); // Log the response for debugging
             return $('#get_room_result').html(response); // Inject HTML response into the element
            
          },
          error: function (xhr, status, error) {
              console.error('AJAX Error:', status, error); // Log any AJAX errors
          }
      });
  }
});

     //filter room 



  
$(window).on('load', function() {
   
        var Latitude = $('#Latitude').text();
        var longnitude = $('#longnitude').text();
        var tid = $('#tid').text();
        var hname = $('#hname').text();
        var hid = $('#hid').text();
        var hotelid = $('#hotelid').text();
		var urlParams = new URLSearchParams(window.location.search);
        var checkin = urlParams.get('checkin');
        var checkout = urlParams.get('checkout');
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
            url: url,
            type: 'POST', 
            url: testurl + 'hoteldetailnearbyrest',
            data: { latitude: Latitude ,longnitude:longnitude,tid:tid,hname:hname,hid:hid,hotelid:hotelid,checkin:checkin,checkout:checkout},
            success: function(response) {
              
                $('#nearbyrest').html(response.html1);
              
                $('#sim-hotel').html(response.html2);
               
            },
            error: function(xhr, status, error) {
                // Handle errors
                console.error('AJAX error:', error);
            }
        });

  });


//hotel detail add rest
   
$(window).on('load', function() {
   
    var Latitude = $('#Latitude').text();
    var longnitude = $('#longnitude').text();
    var hname = $('#hname').text();
    var hid = $('#hid').text();
   // alert(longnitude)
  
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
        url: url,
        type: 'POST', 
        url: testurl + 'add_hoteldetail_nearbyrest',
        data: { latitude: Latitude ,longnitude:longnitude,hid:hid,hname:hname},
        success: function(response) {
          
            $('#nearbyrest').html(response.html1);
           
            
           
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('AJAX error:', error);
        }
    });

});

//hotel detail add rest

  
$(window).on('load', function() {

    var hotelid = $('#hotelid').text();
    var hname = $('#hname').text();
    var hid = $('#hid').text();
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
        url: url,
        type: 'POST', 
        url: testurl + 'hotel_detailfaqs',
        data: { hotelid: hotelid ,hname:hname,hid:hid},
        success: function(response) {
          
            $('#nba').html(response.html1);
       //     $('#sim-hotel').html(response.html2);
            $('#detailfaqdata').html(response.html3);
            $('#getreview').html(response.html4);
            $('#nearby_exp').html(response.html5);
           
        },
        error: function(xhr, status, error) {
            // Handle errors
            console.error('AJAX error:', error);
        }
    });

});



$(window).on('load', function() {
    var lid = $('#locationid').text();  
    var hid = $('#hotelid').text(); 
    var cityName = $('#cityName').text();
    var checkin = $('.checkin').text();
    var checkout = $('.checkout').text();
 

    if(checkin != "" && checkout != ""){
  
      var checkins=  checkin;
      var checkouts=  checkout;
    }else{
      var checkinText = $('#checkinval2').text(); 
      var checkins =  checkinText.replace(/➜/g, '').trim();

      var checkout_text = $('#checkoutval2').text(); 
      var checkouts =  checkout_text.replace(/➜/g, '').trim();
    }

    $('#errormsg').text('')
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url:  testurl+ 'insert_hotel_desction',
          data: { 'lid': lid,'hid':hid,'cityName':cityName,'checkin':checkins,'checkout':checkouts,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){       

          
          }
      });
    }); 

  

    
  // hotel description 

$(window).on('load', function() {
    var lid = $('#locationid').text();  
    var hid = $('#hotelid').text(); 
    var cityName = $('#cityName').text();
    var checkin = $('.checkin').text();
    var checkout = $('.checkout').text();
    var photoCount = $('.photoCount').text();

    if(checkin != "" && checkout != ""){
        var checkins=  checkin;
        var checkouts=  checkout;
    } else {
        var checkinText = $('#checkinval2').text(); 
        var checkins =  checkinText.replace(/➜/g, '').trim();
        var checkout_text = $('#checkoutval2').text(); 
        var checkouts =  checkout_text.replace(/➜/g, '').trim();
    }

    $('#errormsg').text('');

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: testurl + 'hotel_room_desc',
        data: {
            'lid': lid,
            'hid': hid,
            'cityName': cityName,
            'checkin': checkins,
            'checkout': checkouts,
            'photoCount': photoCount,
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          var roomsData = response.roomsData;
          var TPRoomtype = response.TPRoomtype;
       // var Roomdesc = TPRoomtype[0].Roomdesc;
         var Roomdesc = response.tpdesc;
      /*   Roomdesc = JSON.parse(Roomdesc);
      
          $.each(Roomdesc, function(key, value) {             
     
              var roomPriceHtml = '';
              if (roomsData[key]) {
                  var roomInfo = roomsData[key];
                  roomPriceHtml += '<li>';
                  roomPriceHtml += '<img loading="lazy" src="http://pics.avs.io/hl_gates/100/100/' + roomInfo.agencyId + '.png" alt="agoda">';
                  roomPriceHtml += '<a href="javascript:void(0);"><strong>$' + roomInfo.price + '</strong> /night</a>';
                  roomPriceHtml += '</li>';
              } 
      
              var modifiedKey = key.replace(/\s+/g, '-').replace(/[()]/g, '');
              $('.hotelroomprice-' + modifiedKey).html(roomPriceHtml);
      
          }); */
      }
      

    });
});



// Add this function to fetch and display transportation data
function loadTransportationData() {
  var hotelid = $('#hotelid').text().trim();
  
  if (!hotelid) {
      console.error('Hotel ID not found');
      return;
  }
  
  $.ajax({
      type: 'POST',
      url: base_url + 'hotel/transportation',
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      data: { hotelid: hotelid },
      dataType: 'json',
      success: function(response) {
          console.log('Transportation response:', response);
          if (response && Object.keys(response).length > 0) {
              updateTransportationUI(response);
          } else {
              console.log('No transportation data available');
              $('.tr-getting-lists').html('<p>No transportation information available</p>');
          }
      },
      error: function(xhr, status, error) {
          console.error('Error loading transportation data:', error);
      }
  });
}

// Function to update the UI with transportation data
function updateTransportationUI(data) {
  const container = $('.tr-getting-lists');
  if (!container.length) {
      console.error('Transportation container not found');
      return;
  }

  let html = '';

  // Helper function to create a transportation item
  function createTransportationItem(icon, name, distance, time, linkText) {
      // Convert time from seconds to minutes and format to 2 decimal places
      const formattedTime = time ? (parseFloat(time) / 60).toFixed(2) : '0.00';
      
      // Format distance to 2 decimal places if it exists
      const formattedDistance = distance ? (distance / 1000).toFixed(2) + ' km' : '';
      
      return `
          <div class="tr-getting-list">
              <div class="tr-place-info">
                  <div class="tr-place-name">${icon}${name}</div>
                  ${time ? `
                  <span class="tr-distance-time">
                      <svg width="13" height="14" viewBox="0 0 13 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                          <path d="M2.7085 12.1211L1.3335 13.4961M10.9585 12.1211L12.3335 13.4961M4.771 9.3711H4.77718M8.88981 9.3711H8.896M2.021 5.24609C4.771 7.99609 9.23975 7.99609 11.646 5.24609" stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round"/>
                          <path d="M2.20862 4.48572C2.77305 1.61334 3.55406 1.12109 6.45874 1.12109H7.20812C10.1128 1.12109 10.8931 1.61334 11.4582 4.48572L11.8384 6.42172C12.3575 9.06172 12.6167 10.3817 11.8604 11.2514C11.1042 12.1211 9.68106 12.1211 6.83343 12.1211C3.98649 12.1211 2.56268 12.1211 1.80643 11.2514C1.05018 10.3817 1.30937 9.06172 1.82843 6.42172L2.20862 4.48572Z" stroke="#222222" stroke-width="0.8" stroke-linecap="round" stroke-linejoin="round"/>
                      </svg>
                      ${formattedTime} min
                  </span>` : ''}
                  ${linkText ? `<a href="javascript:void(0);" class="tr-travel-type">${linkText}</a>` : ''}
              </div>
              ${distance ? `<div class="tr-distance">${formattedDistance}</div>` : ''}
          </div>
      `;
  }

  // Icons for different transportation types
  const icons = {
      airport: `<svg width="18" height="17" viewBox="0 0 18 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11.6677 8.21686L11.4782 8.25293L11.4075 8.43241L8.58835 15.5894L7.6097 15.7757L8.3748 9.30726L8.4309 8.83302L7.96178 8.92232L3.5739 9.75759L3.37156 9.79611L3.30699 9.99171L2.47522 12.5116L1.87823 12.6253L1.98228 9.2217L1.98466 9.14395L1.95388 9.07252L0.606627 5.94522L1.20367 5.83157L2.90392 7.86957L3.03583 8.02769L3.23812 7.98919L7.626 7.15392L8.09517 7.0646L7.86869 6.64412L4.77982 0.909331L5.75841 0.723048L11.0099 6.34373L11.1416 6.48469L11.3311 6.44861L15.7902 5.59979C16.0247 5.55515 16.2673 5.60549 16.4647 5.73973L16.6615 5.45033L16.4647 5.73973C16.6621 5.87398 16.798 6.08113 16.8426 6.31561C16.8873 6.55009 16.8369 6.79271 16.7027 6.99007L16.9921 7.18692L16.7027 6.99007C16.5685 7.18744 16.3613 7.3234 16.1268 7.36803L11.6677 8.21686Z" stroke="black" stroke-width="0.7"/></svg>`,
      train: `<svg width="20" height="17" viewBox="0 0 20 17" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M17.0835 9.43359H14.8335M3.58349 9.43359H5.83349M17.4116 5.68359L15.9485 1.78191C15.7289 1.19645 15.1693 0.808594 14.544 0.808594H6.12299C5.49773 0.808594 4.93804 1.19645 4.7185 1.78191L3.25537 5.68359M17.4116 5.68359L17.6299 6.26568C17.7524 6.59225 18.0646 6.80859 18.4133 6.80859C18.9068 6.80859 19.293 7.23341 19.2463 7.72462L18.8335 12.0586M17.4116 5.68359H18.9585M3.25537 5.68359L3.03708 6.26568C2.91462 6.59225 2.60243 6.80859 2.25366 6.80859C1.76023 6.80859 1.37395 7.23341 1.42073 7.72462L1.83349 12.0586M3.25537 5.68359H1.70849M1.83349 12.0586L1.95418 13.3258C2.0275 14.0957 2.67408 14.6836 3.44742 14.6836H3.5835M1.83349 12.0586V12.0586C1.55735 12.0586 1.3335 12.2824 1.3335 12.5586V15.0586C1.3335 15.4728 1.66928 15.8086 2.0835 15.8086H2.8335C3.24771 15.8086 3.5835 15.4728 3.5835 15.0586V14.6836M3.5835 14.6836H17.0835M17.0835 14.6836H17.2196C17.9929 14.6836 18.6395 14.0957 18.7128 13.3258L18.8335 12.0586M17.0835 14.6836V15.0586C17.0835 15.4728 17.4193 15.8086 17.8335 15.8086H18.5835C18.9977 15.8086 19.3335 15.4728 19.3335 15.0586V12.5586C19.3335 12.2825 19.1096 12.0586 18.8335 12.0586V12.0586M6.24161 3.33425L5.41255 5.82142C5.25067 6.30707 5.61214 6.80859 6.12406 6.80859H14.5429C15.0548 6.80859 15.4163 6.30707 15.2544 5.82142L14.4254 3.33425C14.2212 2.72174 13.648 2.68359 13.0024 2.68359H7.66463C7.01899 2.68359 6.44578 2.72174 6.24161 3.33425Z" stroke="black" stroke-width="0.7" stroke-linecap="round" stroke-linejoin="round"/></svg>`,
      subway: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M10 3.33333C5.4 3.33333 1.66667 4.16667 1.66667 6.66667V15C1.66667 16.8417 5.025 18.3333 10 18.3333C14.975 18.3333 18.3333 16.8417 18.3333 15V6.66667C18.3333 4.16667 14.6 3.33333 10 3.33333ZM10 15.8333C5.025 15.8333 3.33333 14.725 3.33333 13.3333V12.5C4.16667 13.6 6.66667 14.1667 10 14.1667C13.3333 14.1667 15.8333 13.6 16.6667 12.5V13.3333C16.6667 14.725 14.975 15.8333 10 15.8333ZM10 12.5C5.025 12.5 3.33333 11.3917 3.33333 10V9.16667C4.16667 10.2667 6.66667 10.8333 10 10.8333C13.3333 10.8333 15.8333 10.2667 16.6667 9.16667V10C16.6667 11.3917 14.975 12.5 10 12.5ZM10 8.33333C5.025 8.33333 3.33333 7.225 3.33333 5.83333C3.33333 4.44167 5.025 3.33333 10 3.33333C14.975 3.33333 16.6667 4.44167 16.6667 5.83333C16.6667 7.225 14.975 8.33333 10 8.33333Z" fill="black"/></svg>`,
      bus: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15 5H5C3.89543 5 3 5.89543 3 7V14C3 15.1046 3.89543 16 5 16H15C16.1046 16 17 15.1046 17 14V7C17 5.89543 16.1046 5 15 5Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 9H4V7C4 6.44772 4.44772 6 5 6H15C15.5523 6 16 6.44772 16 7V9Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M6 12H7" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M13 12H14" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`
  };

  // Add walkability score if available
  if (data.walkability) {
      html += createTransportationItem(
          icons.walk || icons.bus, // Fallback to bus icon if walk icon not available
          'Great for walks',
          null,
          null,
          `Grade: ${data.walkability.score} out of 100`
      );
  }

  // Add transportation items
  // In hotelDetails.js, update the updateTransportationUI function:

// Add a counter to track the number of items
let itemCount = 0;
const maxItems = 4;

// Add transportation items
if (data.airports && data.airports.length > 0) {
    data.airports.forEach(airport => {
        if (itemCount < maxItems) {
            html += createTransportationItem(
                icons.airport,
                airport.Name,
                airport.DistanceMtr,
                airport.EstimatedTravelTimeMin,
                'See all flights'
            );
            itemCount++;
        }
    });
}

// Handle trains
if (data.trains && data.trains.length > 0 && itemCount < maxItems) {
    data.trains.forEach(train => {
        if (itemCount < maxItems) {
            html += createTransportationItem(
                icons.train,
                train.Name,
                train.DistanceMtr,
                train.EstimatedTravelTimeMin,
                'Train Station'
            );
            itemCount++;
        }
    });
}

// Handle subways
if (data.subways && data.subways.length > 0 && itemCount < maxItems) {
    data.subways.forEach(subway => {
        if (itemCount < maxItems) {
            html += createTransportationItem(
                icons.subway,
                subway.Name,
                subway.DistanceMtr,
                subway.EstimatedTravelTimeMin,
                'Subway'
            );
            itemCount++;
        }
    });
}

// Handle buses
if (data.buses && data.buses.length > 0 && itemCount < maxItems) {
    data.buses.forEach(bus => {
        if (itemCount < maxItems) {
            html += createTransportationItem(
                icons.bus,
                bus.Name,
                bus.DistanceMtr,
                bus.EstimatedTravelTimeMin,
                'Bus Stop'
            );
            itemCount++;
        }
    });
}
  // Update the container
  container.html(html);
}

// Call the function when the document is ready
$(document).ready(function() {
  // Load transportation data after a short delay to ensure other elements are loaded
  setTimeout(loadTransportationData, 500);
});


  
//api call for hotel price 

// $(window).on('load', function() {
//   function fetchData() {
//       var lid = $('#locationid').text();  
//       var hid = $('#hotelid').text(); 
//       var cityName = $('#cityName').text();
//       var checkin = $('.checkin').text();
//       var checkout = $('.checkout').text();      
//       if(checkin != "" && checkout != ""){
//      //   alert(checkout);
//         var checkins=  checkin;
//         var checkouts=  checkout;
//       }else{
//         var checkinText = $('#checkinval2').text(); 
//         var checkins =  checkinText.replace(/➜/g, '').trim();

//         var checkout_text = $('#checkoutval2').text(); 
//         var checkouts =  checkout_text.replace(/➜/g, '').trim();
//       }
  

//       $('#errormsg').text('');
  
//    if (!checkins || !checkouts && !checkin || !checkout) {		
//   return;
//   }
  
  
//       $.ajax({
//           headers: {
//               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//           },
//           type: 'post',
//           url:  testurl + 'getSignature',
//           data: { 'lid': lid, 'hid': hid, 'cityName': cityName, 'checkin': checkins, 'checkout': checkouts, '_token': $('meta[name="csrf-token"]').attr('content') },
//           success: function(response) {
//               if (response == 0) {
//                   return $('#errormsg').text('Please choose Checkin and Checkout date').css({
//                       'color': 'red',
//                       'margin': '10px 3'
//                   });
//               } else if (response == 2) {
//                   return $('#errormsg').text('Incorrect Date').css({
//                       'color': 'red',
//                       'margin': '10px 3'
//                   });
//               }

//               setTimeout(function() {
//                   $('#errormsg').text('');
//               }, 3000);

//               $('#hotel_price').html(response);
//           }
//       });
//   }

//   // Call the function on page load
//   fetchData();
  
 
// });
//api call for hotel price 