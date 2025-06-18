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