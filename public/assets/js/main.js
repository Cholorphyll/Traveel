
var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';

// var editBtn = document.querySelector('.edit-btn')
// var formEditButtons = document.querySelector('.form-edit-buttons')
// var saveButton = document.querySelector('.save-button')
// var cancelButton = document.querySelector('.cancel-button')
// var formGroupEditable = document.querySelector('.form-group-editable')

// editBtn.addEventListener('click', function () {
//     formEditButtons.classList.remove('d-none')
//     formGroupEditable.classList.remove('disabled')
// })


// let btns = document.querySelectorAll('button');

// for (i of btns) {
//     i.addEventListener('click', function () {
//         formEditButtons.classList.add('d-none')
//         formGroupEditable.classList.add('disabled')
//     });
// }

// for (allbtns of btns) {
//     allbtns.addEventListener('click', function () {
//         formEditButtons.classList.add('d-none')
//         formGroupEditable.classList.add('disabled')
//     });
// }

// function editReview(id) {
//   $("#reviewField-" + id).prop("disabled", false);
//   $("#buttonsContainer-" + id).removeClass("d-none");
//   $("#editBtn-" + id).addClass("d-none");
// }

function editReview(id) {
  var textarea = $("#reviewField-" + id);
  textarea.prop("disabled", false);
  var value = $('#edit-btn').attr('value');
  // alert(value)
  // $("#buttonsContainer-" + id).removeClass("d-none");
  $("#editBtn-" + id).addClass("d-none");
   
  if (value == 1) {
    $("#buttonsContainer-" + id + "-2").removeClass("d-none");
  } else if (value == 2) {
    $("#buttonsContainer-" + id + "-1").removeClass("d-none");
  } else if (value == 3) {
    $("#buttonsContainer-" + id + "-3").removeClass("d-none");
  } else {
    $("#buttonsContainer-" + id + "-2").removeClass("d-none");
    $("#buttonsContainer-" + id + "-1").removeClass("d-none");
  }

 
}

function editlp(id) {
  var textarea = $("#reviewField-" + id);
  textarea.prop("disabled", false);
  var value = $('#edit-btn').attr('value');
  // alert(value)
   $("#buttonsContainer-" + id).removeClass("d-none");

  $("#editBtn-" + id).addClass("d-none");

   
 
}
function cancellp(id) {
 
  $("#buttonsContainer-" + id).addClass("d-none");
 
}

function saveReview(id,button) {
  $('.loadResult').removeClass('hide');
  var fieldValue  = $(button).val();
  var id = id;
  
  $('.review-option').removeClass('sel-option')
  $('.review-option').css({
    'font-weight': '',
    'text-decoration': ''
  });
  $('.opt-' + fieldValue).css({
    'font-weight': 'bold',
    'text-decoration': 'underline'
  });
  $('.opt-' + fieldValue).addClass('sel-option');


  var sightid = $('#sightid').val();
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'POST',
    url: base_url + 'attraction/update_review',
    data: { 'value': fieldValue,'id':id,'sightid':sightid,
    '_token': $('meta[name="csrf-token"]').attr('content')},
    success: function(response){
      $('.loadResult').addClass('hide');
      $('.list-container').html(response);

        $("#reviewField-" + id).prop("disabled", true);
        $("#buttonsContainer-" + id).addClass("d-none");  
        $("#buttonsContainer-" + id + "-0").addClass("d-none");
        $("#buttonsContainer-" + id + "-1").addClass("d-none");
        $("#buttonsContainer-" + id + "-2").addClass("d-none");
        $("#editBtn-" + id).removeClass("d-none");
       
    }
});


}

function cancelReview(id) {
  var textarea = $("#reviewField-" + id);
  var initialValue = textarea.data("initial-value"); // Retrieve the initial value

  textarea.prop("disabled", true);
  textarea.val(initialValue); // Restore the initial value
  $("#buttonsContainer-" + id).addClass("d-none");
  $("#editBtn-" + id).removeClass("d-none");
}



function editattcat(id) {

  $(".editattcat-" + id).removeClass("d-none");
}


$(document).on('click','#search_location', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#search_input').val();
 // var baseUrl = $(location).attr("href");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'listing/filter_location',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
              
           
          }
      });
  })
  
  $(document).on('click','#search_attraction', function(){	 
    $('.loadResult').removeClass('hide');
    var value = $('#search_attinput').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url: base_url+  'attraction/search_attracion',
            data: { 'value': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.loadResult').addClass('hide');
                $('.getfilterDataat').html(response) 
            
             
            }
        });
    })
  // $(document).ready(function() {

  //   $('#country-input').autocomplete({
     
  //     source: function(request, response) {
  //       $.ajax({
  //         type: 'POST',
  //         url: "/listing/search_parentcon",
  //         data: {
  //           val: $('#country-input').val(),
  //           _token: $('meta[name="csrf-token"]').attr('content')
  //         },
  //         success: function(data) {
            
  //           var suggestions = data.map(function(item) {
  //             return item.value;
  //           });
  //           response(suggestions);
  //         }
  //       });
  //     },
  //     minLength: 1,
  //     appendTo: null 
  //   });
  // });

  $(document).on('keyup','#search_city', function(){	 
   
    var value = $('#search_city').val();
    if(value != ""){
      $("#citylist").css("display", "block");

        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url:  base_url+  'attraction/search_city',
            data: { 'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
         
            success: function(response){
        
              $('#citylist').html("");
  
         
              response.forEach(function(country) {
                  var listItem = $('<li>').text(country.value + ',' + country.country);
               //   $('#country-list').fadeIn();
                  $('#citylist').append(listItem);
              });
           }
        });
      }else{
     
        $('#citylist').append("");
     //   $('#country-list').fadeOut();
      }
      $(document).on('click', "li", function(){
        $("#search_city").val($(this).text());
        $('#citylist').fadeOut();
  
      });
    }) 


    
    $(document).on('keyup', '#search_attractionfaq', function(){     
    //  $('.loadResult').removeClass('hide');  
      var value = $('#search_attractionfaq').val();
  if(value != ""){
    $("#country-list").css("display", "block");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: base_url+  'attraction/search_attr',
          data: {
              'val': value,
              '_token': $('meta[name="csrf-token"]').attr('content')
          },
       
           success: function(response){
        
            $('#country-list').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);
             //   $('#country-list').fadeIn();
                $('#country-list').append(listItem);
            });
         }
        
      });
    }else{
     
      $('#country-list').append("");
   //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function(){
      $("#search_attractionfaq").val($(this).text());
      $('#country-list').fadeOut();

    });
  });

  $(document).ready(function() {
    var fieldCount = 0; 

    $('#addstButton').on('click', function() {
      fieldCount++;

      var html = `
        <div class="row">
          <div class="col-md-6">
            <div class="form-group mt-3">
              <strong>Station Name</strong>
              <input type="text" name="station_name[]" class="form-control rounded-3" placeholder="Station Name" required>
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group mt-3">
              <strong>Time</strong>
              <input type="text" name="time[]" class="form-control rounded-3" placeholder="15 minute walk" required>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="form-group mt-3">
            <strong>Duration</strong>
            <input type="number" name='duration[]' class="form-control rounded-3"
              placeholder="Duration" required>
          </div>
        </div>
      `;

      $('#station').append(html);
    });
  });

  $(document).on('keyup', '#search_cat', function(){     
    //  $('.loadResult').removeClass('hide');  
      var value = $('#search_cat').val();
  if(value != ""){
    $("#cat-list").css("display", "block");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url:  base_url+ 'attraction/search_attr_cate',
          data: {
              'val': value,
              '_token': $('meta[name="csrf-token"]').attr('content')
          },
       
           success: function(response){
            $('#cat-list').html("");
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);
           
                $('#cat-list').append(listItem);
            });
         }
        
      });
    }else{
     
      $('#cat-list').append("");
      $('#cat-listt').fadeOut();
    }
   
    $(document).on('click', "#cat-list li", function(){
      $(".sp-category #search_cat").val($(this).text()); // Set value only for "search_cat" input
      $('#cat-list').fadeOut();
  });
  });

  $(document).on('change','#sort_att_review', function(){	 
    // $('.loadResult').removeClass('hide');
    var value = $('#sort_att_review').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:  base_url+ 'attraction/filter_manage_att_review',
            data: { 'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.list-container').html(response);
             
            }
        });
    }) 
    
   $(document).on('keyup','#filter_reviews', function(){	 
      if( $('#filter_reviews').val() != ""){
    //   $('.loadResult').removeClass('hide');
      var value = $('#filter_reviews').val();
      var id = $('#sightid').val();

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url: base_url+ 'attraction/search_attreviews',
              data: { 'val': value,'id':id,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
             //   $('.loadResult').addClass('hide');
                $('.list-container').html(response);
               
              }
          });
        }
  }) 
  $(document).on('click','.review-option', function(){	 
     $('.loadResult').removeClass('hide');
    var value = $(this).data('value');
    
    var id = $('#sightid').val();

    $('.review-option').removeClass('sel-option')
    $('.review-option').css({
      'font-weight': '',
      'text-decoration': ''
    });
    $('.opt-' + value).css({
      'font-weight': 'bold',
      'text-decoration': 'underline'
    });
    $('.opt-' + value).addClass('sel-option');
    
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url: base_url+ 'attraction/filterreview',
            data: { 'val': value,'id':id,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.loadResult').addClass('hide');
            
              $('.list-container').html(response);
                
               
            } 
        });
    }) 

    $(document).on('click','.filter-review-option', function(){	 
      $('.loadResult').removeClass('hide');
       var value = $(this).data('value');
    // alert(value);
     
         $.ajax({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             type:'post',
            url:  base_url+ 'attraction/filterreview_manage',
             data: { 'val': value,
             '_token': $('meta[name="csrf-token"]').attr('content')},
             success: function(response){
               $('.loadResult').addClass('hide');
           
               $('.list-container').html(response);
               
              
             }
         });
     }) 
    $(document).on('change','#sort_att_review_edit', function(){	 
  //   $('.loadResult').removeClass('hide');
      var value = $('#sort_att_review_edit').val();
 
      var id = $('#sightid').val();
      var rval = $('.sel-option').data('value');
      
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url + 'attraction/filter_review_edit',
              data: { 'val': value,'id':id,'rval':rval,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                $('.list-container').html(response);
               
              }
          });
      }) 

      
      // edit Location faq  

    $(document).on('click','.updatefaq', function(){	 
     
      var faqId = $(this).data('id');
      var questionTextarea =  $('#question' + faqId).val(); 
    
      var answerTextarea = $('#Answer' + faqId).val();
      var locationid = $('#locationid').val();
   
      var isValid = true;

      if (questionTextarea == '') {
        
        $('#questionmsg-'+faqId).text('Question is required.').css('color', 'red');
        isValid = false;
      } else {
        $('#questionmsg-'+faqId).text('');
      }
   
      if (!isValid) {
        return; 
     }

      $("#buttonsContainer-" + faqId).addClass("d-none");
      $('#question' + faqId).prop('disabled', true);
      $('#Answer' + faqId).prop('disabled', true);

      $('#listing' + faqId).prop('disabled', true); 
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url+ 'listing/update_editfaq',
              data: { 'question': questionTextarea,'faqId':faqId,'answer':answerTextarea,'locationid':locationid,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                console.log(response)
                $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
               setTimeout(function() {
                $('#Success').html('');
            }, 3000);
              }
          });
      }) 
      


      function cancellp(index) {
        var originalQuestion = $('#question' + index).data('original-value');
        var originalAnswer = $('#Answer' + index).data('original-value');
        var listing = JSON.stringify($('#listing' + index).data('original-value'));
        $("#buttonsContainer-" + index).addClass("d-none");
     
     
        $('#question' + index).val(originalQuestion).prop('disabled', true);
        $('#Answer' + index).val(originalAnswer).prop('disabled', true);
        $('#listing' + index).val(listing).prop('disabled', true);
        $('#questionmsg-'+faqId).text('');
        $('#answer-'+faqId).text('');
     }
     
     function editlp(id) {
      var textarea = $("#reviewField-" + id);
      textarea.prop("disabled", false);
      var value = $('#edit-btn').attr('value');
      // alert(value)
       $("#buttonsContainer-" + id).removeClass("d-none");
    
      $("#editBtn-" + id).addClass("d-none");
    
      $('#question' + id).prop('disabled', false);
      $('#Answer' + id).prop('disabled', false);
      $('#listing' + id).prop('disabled', false);
     
    }
// end edit sight faq
// filter hotel
$(document).on('click','#searchHotel', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#search_input').val();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'hotels/filter_hotel',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
          }
      });
  })
  

// edit sight faq

    $(document).on('click','.updatesightfaq', function(){	 
      
       var faqId = $(this).data('id');
       var questionTextarea =  $('#question' + faqId).val(); 
       var answerTextarea =   $('#Answer' + faqId).val();
     

       var isValid = true;

       if (questionTextarea == '') {
         
         $('#questionmsg-'+faqId).text('Question is required.').css('color', 'red');
         isValid = false;
      } else {
        $('#questionmsg-'+faqId).text('');
      }
      // if (answerTextarea == '') {
      //   $('#answer-'+faqId).text('Answer is required.').css('color', 'red');
      //   isValid = false;

      // } else {
      //   $('#answer-'+faqId).text('');
      // }
      if (!isValid) {
        return; // Stop execution if the form is not valid
      }
      $("#buttonsContainer-" + faqId).addClass("d-none");
      $('#question' + faqId).prop('disabled', true);
      $('#Answer' + faqId).prop('disabled', true);
           $.ajax({
               headers: {
                   'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
               },
               type:'post',
               url:  base_url+ 'attraction/update_faq',
               data: { 'question': questionTextarea,'faqId':faqId,'answer':answerTextarea,
               '_token': $('meta[name="csrf-token"]').attr('content')},
               success: function(response){
              //  alert(response);
                 $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
                setTimeout(function() {
                 $('#Success').html('');
             }, 30000);
               }
           });
       }) 
       
       function cancelbtn(index) {
         var originalQuestion = $('#question' + index).data('original-value');
         var originalAnswer = $('#Answer' + index).data('original-value');
         $("#buttonsContainer-" + index).addClass("d-none");
      
      
         $('#question' + index).val(originalQuestion).prop('disabled', true);
         $('#Answer' + index).val(originalAnswer).prop('disabled', true);
       
         $('#questionmsg-'+index).text('');
         $('#answer-'+index).text('');
      }
      
      function editsightfaq(id) {
       var textarea = $("#reviewField-" + id);
       textarea.prop("disabled", false);
       var value = $('#edit-btn').attr('value');
       // alert(value)
        $("#buttonsContainer-" + id).removeClass("d-none");
     
       $("#editBtn-" + id).addClass("d-none");
     
       $('#question' + id).prop('disabled', false);
       $('#Answer' + id).prop('disabled', false);
       
      
     }
  //end edit sight faq
  
  function hotelcancle(index) {
    var originalvalue = $('.inp-' + index).data('original-value');
    $("#buttonsContainer-" + index).addClass("d-none");
 
 
    $('.inp-' + index).val(originalvalue).prop('disabled', true);
   
  
    $('#errors-'+index).text('');
    
 }
 
 function edithotel(id) {
  var textarea = $("#edithotel-" + id);
  textarea.prop("disabled", false);
  var value = $('#edit-btn').attr('value');
  // alert(value)
   $("#buttonsContainer-" + id).removeClass("d-none");

  $("#editBtn-" + id).addClass("d-none");

  $('.inp-' + id).prop('disabled', false);
  
  
 
}

$(document).on('keyup','#searchHotelcity', function(){	 
   
  var value = $('#searchHotelcity').val();

  if(value != ""){
    $("#citylisth").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/searchCity',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#citylisth').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value + ',' + country.country);
             //   $('#country-list').fadeIn();
                $('#citylisth').append(listItem);
            });
         }
      });
    }else{
   
      $('#citylisth').html("");
   //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function(){
      var text = $(this).text();
      var parts = text.split(",");
      var cityName = parts[0].trim();
      var country = parts[1].trim();
      
      
      $("#searchHotelcity").val(cityName);
      $("#country").val(country);
      $('#citylisth').fadeOut();

    });
  }) 

 // start hotel review
 
    $(document).on('change','#sort_hotel_review', function(){	 
      $('.loadResult').removeClass('hide');
      var filter_option = $('.sel-option').data('value');
      var value = $('#sort_hotel_review').val();
      var id = $('#hotelid').val();
      
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url + 'hotels/sortHotelReview', 
              data: { 'val':value,'id':id,'filter_option':filter_option,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                $('.list-container').html(response);
                
              }
          });
      }) 

      $(document).on('keyup','#filterhotelbyid', function(){	 
        if( $('#filterhotelbyid').val() != ""){
      //   $('.loadResult').removeClass('hide');
        var value = $('#filterhotelbyid').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                url: base_url+ 'hotels/filterhotelbyid',
                data: { 'val': value,
                '_token': $('meta[name="csrf-token"]').attr('content')},
                success: function(response){
              //   $('.loadResult').addClass('hide');
                  $('.list-container').html(response);
                
                }
            });
          }
    }) 

    function markReviewspam(id) {
      //$('.loadResult').removeClass('hide');
      var fieldValue  = $('.mark-spam').data('value');
      var hotelid = $('#hotelid').val();
      
      var id = id;
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        url: base_url + 'hotels/update_hotelreview',
        data: { 'value': fieldValue,'id':id,'hotelid':hotelid,
        '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
          $('.loadResult').addClass('hide');
            
            $("#reviewField-" + id).prop("disabled", true);
            $("#buttonsContainer-" + id).addClass("d-none");
            $("#buttonsContainer-" + id + "-0").addClass("d-none");
            $("#buttonsContainer-" + id + "-1").addClass("d-none");
            $("#buttonsContainer-" + id + "-2").addClass("d-none");
            $("#buttonsContainer-" + id + "-5").addClass("d-none");
            $("#editBtn-" + id).removeClass("d-none");

            $('.list-container').html(response);
        }
    });


    }

    function aproveReview(id,button) {
      $('.loadResult').removeClass('hide');
      var fieldValue  = $(button).val();
      var id = id;
      var hotelid = $('#hotelid').val();

      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        url: base_url + 'hotels/update_hotelreview',
        data: { 'value': fieldValue,'id':id,'hotelid':hotelid,
        '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
          $('.loadResult').addClass('hide');
            
            $("#reviewField-" + id).prop("disabled", true);
            $("#buttonsContainer-" + id).addClass("d-none");
            $("#buttonsContainer-" + id + "-0").addClass("d-none");
            $("#buttonsContainer-" + id + "-1").addClass("d-none");
            $("#buttonsContainer-" + id + "-2").addClass("d-none");
            $("#buttonsContainer-" + id + "-5").addClass("d-none");
            $("#editBtn-" + id).removeClass("d-none");
            $('.list-container').html(response);

            $('.hotel-review-option').removeClass('sel-option')
            $('.hotel-review-option').css({
              'font-weight': '',
              'text-decoration': ''
            });
            $('.opt-' + fieldValue).css({
              'font-weight': 'bold',
              'text-decoration': 'underline'
            });
            $('.opt-' + fieldValue).addClass('sel-option');

        }
    });


    }

    function edithotelReview(id) {
      var textarea = $("#reviewField-" + id);
      textarea.prop("disabled", false);
      var value = $('#edit-btn').attr('value');
      // alert(value)
      // $("#buttonsContainer-" + id).removeClass("d-none");
      $("#editBtn-" + id).addClass("d-none");
      
      if (value == 1) {
        $("#buttonsContainer-" + id + "-2").removeClass("d-none");
        $("#buttonsContainer-" + id + "-5").removeClass("d-none");
      } else if (value == 2) {
        $("#buttonsContainer-" + id + "-1").removeClass("d-none");
        $("#buttonsContainer-" + id + "-5").removeClass("d-none");
      } else if (value == 3) {
        $("#buttonsContainer-" + id + "-3").removeClass("d-none");
      } else {
        $("#buttonsContainer-" + id + "-2").removeClass("d-none");
        $("#buttonsContainer-" + id + "-1").removeClass("d-none");
        $("#buttonsContainer-" + id + "-5").removeClass("d-none");
      }

    
    }

    $(document).on('click','.hotel-review-option', function(){	 
        $('.loadResult').removeClass('hide');
        var value = $(this).data('value');

        $('.hotel-review-option').removeClass('sel-option')
        $('.hotel-review-option').css({
          'font-weight': '',
          'text-decoration': ''
        });
        $('.opt-' + value).css({
          'font-weight': 'bold',
          'text-decoration': 'underline'
        });
        $('.opt-' + value).addClass('sel-option');
      var id = $('#hotelid').val();
    
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url: base_url+ 'hotels/ftrhotelrewview',
            data: { 'val': value,'id':id,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.loadResult').addClass('hide');
            
              $('.list-container').html(response);
                
              
            } 
        });
    }) 
//end hotel reviews

//start hotel Faq

    function edit_Hotelfaq(id) {
      var textarea = $("#reviewField-" + id);
      textarea.prop("disabled", false);
      var value = $('#edit-btn').attr('value');
      // alert(value)
      $("#buttonsContainer-" + id).removeClass("d-none");

      $("#editBtn-" + id).addClass("d-none");

      $('#question' + id).prop('disabled', false);
      $('#Answer' + id).prop('disabled', false);


    }
    function cancel_hotelfaq(index) {
      var originalQuestion = $('#question' + index).data('original-value');
      var originalAnswer = $('#Answer' + index).data('original-value');
      $("#buttonsContainer-" + index).addClass("d-none");


      $('#question' + index).val(originalQuestion).prop('disabled', true);
      $('#Answer' + index).val(originalAnswer).prop('disabled', true);

      $('#questionmsg-'+index).text('');
      $('#answer-'+index).text('');
    }

    $(document).on('click','.updateHotelfaq', function(){	 
      
      var faqId = $(this).data('id');
      var questionTextarea =  $('#question' + faqId).val(); 
      var answerTextarea =   $('#Answer' + faqId).val();
    

      var isValid = true;

      if (questionTextarea == '') {
        $('#questionmsg-'+faqId).text('Question is required.').css('color', 'red');
        isValid = false;
     } else {
       $('#questionmsg-'+faqId).text('');
     }
    //  if (answerTextarea == '') {
    //    $('#answer-'+faqId).text('Answer is required.').css('color', 'red');
    //    isValid = false;

    //  } else {
    //    $('#answer-'+faqId).text('');
    //  }
     if (!isValid) {
       return; // Stop execution if the form is not valid
     }
     $("#buttonsContainer-" + faqId).addClass("d-none");
     $('#question' + faqId).prop('disabled', true);
     $('#Answer' + faqId).prop('disabled', true);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url+ 'hotels/update_hotel_faq',
              data: { 'question': questionTextarea,'faqId':faqId,'answer':answerTextarea,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
             //  alert(response);
                $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
               setTimeout(function() {
                $('#Success').html('');
            }, 30000);
              }
          });
      }) 
  //add list faq
      //add list faq
      $(document).ready(function() {
        $('#savecustomButton').click(function() {
          var question = $('#addques').val().trim();

          if (question === "") {
            $('#errorcustfaq').text('Question is Required.').css('color', 'red');
            setTimeout(function(){
              $('#errorcustfaq').text('');
            },30000);
            return;
          }
          $('#savecustomButton').addClass('disabled');
          $('.cancelbtn').addClass('disabled');

          $('#errorcustfaq').text('');
          var hotelid = $('#savecustomButton').data('id');
       
          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:  base_url+ 'hotels/add_hotel_faq',
            data: { 'checkboxText': question,'hotelid':hotelid,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
			  $('#saveButton').removeClass('disabled');
              $('.cancelbtn').removeClass('disabled');
              $('.getupdatedfaq').html(response);
              $('#staticBackdrop').modal('hide');
              var question = $('#addques').val('')
         
              $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
            setTimeout(function() {
              $('#Success').html('');
          }, 30000);
            }
        });

        });
      });
      
      
      //add custom faq
     $(document).ready(function() {
        $('#saveButton').click(function() {
        
          var checkedCheckboxes = $('input[type="checkbox"]:checked');
          if (checkedCheckboxes.length === 0) {
            $('#errorcheck').text('Please Select a question').css('color', 'red');
            setTimeout(function(){
              $('#errorcheck').text('');
            },30000);
            return;
          }
          $('#saveButton').addClass('disabled');
          $('#cancel-list').addClass('disabled');
          
          var checkboxText = '';
          if (checkedCheckboxes.length != 0) {
            $('#errorcheck').text('');
            checkedCheckboxes.each(function() {
              checkboxText += $(this).next('span').text() + '\n';
            });
          }
         var hotelid = $('#saveButton').data('id');

          $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:  base_url+ 'hotels/add_hotel_faq',
            data: { 'checkboxText': checkboxText,'hotelid':hotelid,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('#saveButton').removeClass('disabled');
              $('#cancel-list').removeClass('disabled');

              $('#staticBackdrop').modal('hide');
              $('.getupdatedfaq').html(response);
              $('.checkbox').prop('checked', false);
         
              $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
            setTimeout(function() {
              $('#Success').html('');
          }, 30000);
            }
        });

         
        });
      });

	// end custom faq

      $(document).ready(function() {
      
          $('.modal-header').on('click','.custom-faq',function(){
         
          $('.list-faq').css({
            'font-weight': '',
            'text-decoration': ''
          });
          $('.custom-faq').css({
            'font-weight': 'bold',
            'text-decoration': 'underline'
          });
          $('.custom-faq-buttons').find('button').removeAttr('disabled');
          $('.list-buttons').find('button').prop('disabled', true);
        });

       
          $('.modal-header').on('click','.list-faq',function(){
         
          $('.custom-faq').css({
            'font-weight': '',
            'text-decoration': ''
          });
          $('.list-faq').css({
            'font-weight': 'bold',
            'text-decoration': 'underline'
          });
          $('.list-buttons').find('button').removeAttr('disabled');
          $('.custom-faq-buttons').find('button').prop('disabled', true);
        });

      });

     



      // $(document).ready(function() {
      //   $('.checkbox').click(function() {
      //     $('.list-buttons').removeClass('d-none')
      //   });
      // });
      // $(document).ready(function() {
      //   $('#cancel-list').click(function() {
      //     $('.list-buttons').addClass('d-none')
      //   });
      // });
      
      //end hotel Faq

    
//hotel category

$(document).ready(function() {
 
    $(document).on('click','.delete-category',function(){
     

    var id = $('.delete-category').data('id');
    var hotelid = $('#hotelid').val();
    var confirmation = confirm("Are you sure you want to delete this hotel category?");

    if (confirmation) {
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: base_url + 'hotels/updateHotelCategory',
        data: { 'hotelid': hotelid, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
          $('#staticBackdrop').modal('hide');
          $('.getupdatedfaq').html(response);
         
        }
      });
    } else {
      // If user cancels the deletion
      console.log("Deletion canceled");
    }
  });
});

$(document).on('keyup','#search_category', function(){	 
   
  var value = $('#search_category').val();
  if(value != ""){
    $("#catlist").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/search_category',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#catlist').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);
             //   $('#country-list').fadeIn();
                $('#catlist').append(listItem);
            });
         }
      });
    }else{
   
      $('#catlist').append("");
   //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function(){
      $("#search_category").val($(this).text());
      $('#catlist').fadeOut();

    });
  }) 

  $(document).ready(function() {
  
      $(document).on('click','#addnewcat',function(){
      var id = $('#addnewcat').data('id');
  
      var value = $('#search_category').val();
   
      $('#add_cat').text('Adding Category..').css('color', 'green');
      if (value === "") {
        $('#add_cat').text('');
        $('#inputerror').text('Category is Required.').css('color', 'red');
        setTimeout(function(){
          $('#inputerror').text('');
        },30000);
        return;
      }
      
      $('#inputerror').text('');

      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: base_url + 'hotels/addNewCategory',
        data: { 'value': value, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
          $('#add_cat').text('');
          if(response == 'false'){
            return  $('#inputerror').text('Invalid Category').css('color', 'red');
          }else if(response == 2){
            return  $('#inputerror').text('Category already exist.').css('color', 'red');
          }
          $('#search_category').val('');
          $('#staticBackdrop').modal('hide');
          $('.getupdatedfaq').html(response);
          
        }
      });
    
    });
  });

  //start hotel landing page
  function editHlanding(index) {
 
    var originalQuestion = $('#name' + index).data('original-value');
  
   
    $("#buttonsContainer-" + index).removeClass("d-none");


    $('#name' + index).val(originalQuestion).prop('disabled', false);
    $('#questionmsg-'+index).text('');
  }

  function editHlanding(id) {
    
    $('#name' + id).prop('disabled', false);
    // alert(value)
    $("#buttonsContainer-" + id).removeClass("d-none");

  }

  function cancelHLanding(index) {
      var originalQuestion = $('#name' + index).data('original-value');
    $("#buttonsContainer-" + index).addClass("d-none");


    $('#name' + index).val(originalQuestion).prop('disabled', true);

    $('#questionmsg-'+index).text('');
  }

  $(document).on('click','.updatelanding', function(){	 
      
    var landing = $(this).data('id'); 
    var colid = $(this).data('colid');
    var value =  $('#name' + colid).val(); 
    
    var isValid = true;

    if (value == '') {
      $('#questionmsg-'+colid).text('Data is required.').css('color', 'red');
      isValid = false;
   } else {
     $('#questionmsg-'+colid).text('');
   }
  
   if (!isValid) {
     return; // Stop execution if the form is not valid
   }
   $("#buttonsContainer-" + colid).addClass("d-none");
   $('#name' + colid).prop('disabled', true);
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:  base_url+ 'hotels/updateLanding',
            data: { 'landing': landing,'colid':colid,'value':value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
           //  alert(response);
              $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
             setTimeout(function() {
              $('#Success').html('');
          }, 30000);
            }
        });
    }) 


    $(document).on('click','#hidepage', function(){	 
      var landing = $(this).data('id'); 
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url+ 'hotels/hidepage',
              data: { 'landing': landing,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
             //  alert(response);
                $('#Success').html('<div class="alert alert-success">Page Hide Successfully.</div>');              
               setTimeout(function() {
                $('#Success').html('');
            }, 30000);
              }
          });
      }) 
      
      $(document).on('click','#delete-landing-page', function(){	 
        var result = confirm("Are you sure you want to delete this Page?");
        if (result) {
           var landing = $(this).data('id'); 
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  type:'post',
                  url:  base_url+ 'hotels/delete_landing',
                  data: { 'landing': landing,
                  '_token': $('meta[name="csrf-token"]').attr('content')},
                  success: function(response){
                //  alert(response);
                    $('#Success').html('<div class="alert alert-danger">Page Delted Successfully.</div>');              
                  setTimeout(function() {
                    $('#Success').html('');
                }, 30000);
                  }
              });
          } 
        }) 


        $(document).on('keyup','#search_hotelcategory', function(){	 
   
          var value = $('#search_hotelcategory').val();
          if(value != ""){
            $("#catlist").css("display", "block");
        
              $.ajax({
                  headers: {
                      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                  },
                  type:'POST',
                  url:  base_url+  'hotels/search_category',
                  data: { 'val': value,
                  '_token': $('meta[name="csrf-token"]').attr('content')},
               
                  success: function(response){
              
                    $('#catlist').html("");
        
               
                    response.forEach(function(country) {
                        var listItem = $('<li>').text(country.value);
                     //   $('#country-list').fadeIn();
                        $('#catlist').append(listItem);
                    });
                 }
              });
            }else{
           
              $('#catlist').append("");
           //   $('#country-list').fadeOut();
            }
            $(document).on('click', "li", function(){
              $("#search_hotelcategory").val($(this).text());
              $('#catlist').fadeOut();
        
            });
          }) 


//similar hotel

//nearby search
      // $(document).ready(function() {
      //   $(".nearby input[type='radio']").on('change', function() {
      //     var selectedValue = $(this).attr("id");         
      //     if (selectedValue == "Attraction") {
      //       $('.change-field').html('<input type="text" name="Attraction" id="Attraction" class="inputval form-control rounded-3" placeholder="Search Attraction"><i class="ti ti-search"></i>');
      //     } else if (selectedValue == "Hotel") {
      //       $('.change-field').html('<input type="text" name="Hotel" id="Hotel" class="inputval form-control rounded-3" placeholder="Search Hotel"><i class="ti ti-search"></i>');
      //     } else if (selectedValue == "Restaurent") {
      //       $('.change-field').html('<input type="text" name="Restaurent" id="Restaurent" class="inputval form-control rounded-3" placeholder="Search Restaurent"><i class="ti ti-search"></i>');
      //     } else if (selectedValue == "Neighborhood") {
      //       $('.change-field').html('<input type="text" name="Neighborhood" id="Neighborhood" class="inputval form-control rounded-3" placeholder="Search Neighborhood"><i class="ti ti-search"></i>');
      //     } else if (selectedValue == "Airport") {
      //       $('.change-field').html('<input type="text" name="Airport" id="Airport" class="inputval form-control rounded-3" placeholder="Search Airport"><i class="ti ti-search"></i>');
      //     } 
      //   });
      // });

   //nearby search
   $(document).ready(function() {
    $(".nearby input[type='radio']").on('change', function() {
      var selectedValue = $(this).attr("id");    
      if (selectedValue == "Attraction") {
        $('.change-field').html('<input type="text" name="Attraction" id="Attraction" class="inputval form-control rounded-3" placeholder="Search Attraction"><i class="ti ti-search"></i>');
      } else if (selectedValue == "Hotel") {
        $('.change-field').html('<input type="text" name="Hotel" id="Hotel" class="inputval form-control rounded-3" placeholder="Search Hotel"><i class="ti ti-search"></i>');
      } else if (selectedValue == "Restaurent") {
        $('.change-field').html('<input type="text" name="Restaurent" id="Restaurent" class="inputval form-control rounded-3" placeholder="Search Restaurent"><i class="ti ti-search"></i>');
      } else if (selectedValue == "Neighborhood") {
        $('.change-field').html('<input type="text" name="Neighborhood" id="Neighborhood" class="inputval form-control rounded-3" placeholder="Search Neighborhood"><i class="ti ti-search"></i>');
      } else if (selectedValue == "Airport") {
        $('.change-field').html('<input type="text" name="Airport" id="Airport" class="inputval form-control rounded-3" placeholder="Search Airport"><i class="ti ti-search"></i>');
      } 
    });
  });


  var searchTimer;

$(document).on('keyup', '#Attraction', function() {
  var value = $('.inputval').val();
  
  // Clear previous timer
  clearTimeout(searchTimer);

  if (value != "") {
    // Set new timer for 500ms delay
    searchTimer = setTimeout(function() {
      $(".att-list").css("display", "block");
      
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'attraction/search_attr',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('.att-list').html("");
          response.forEach(function(country) {
            var listItem = $('<li>')
              .text(country.value)
              .attr('data-id', country.id);
            $('.att-list').append(listItem);
          });
        }
      });
    }, 500); // 500ms delay
  } else {
    $('.att-list').html("");
  }
});

$(document).on('click', ".att-list li", function(){
  $('.value').html('<button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value" data-id="' + $(this).data('id') + '">' + $(this).text() + '</button>');
  $('.att-list').fadeOut();
});




// Initialize searchTimer with null
var searchTimer = null;

$(document).on('keyup', '#Hotel', function() {    
  var value = $('.inputval').val();
  
  // Clear previous timer if it exists
  if (searchTimer !== null) {
    clearTimeout(searchTimer);
  }

  if (value != "") {
    // Set new timer for 500ms delay
    searchTimer = setTimeout(function() {
      $('.att-list').html("");
      $(".att-list").css("display", "block");
      
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'hotels/search_hotel',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('.att-list').html("");
          response.forEach(function(country) {
            var listItem = $('<li>')
              .text(country.value)
              .attr('data-id', country.id); // Add data-id attribute
            $('.att-list').append(listItem);
          });
        }
      });
    }, 500); // 500ms delay
  } else {
    $('.att-list').html("");
  }
});

// Update click handler to use data-id
$(document).on('click', ".att-list li", function() {
  $('.value').html('<button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value" data-id="' + $(this).data('id') + '">' + $(this).text() + '</button>');
  $('.att-list').fadeOut();
  $('.att-list').html("");
});
  

  
// Initialize searchTimer with null if not already initialized
var searchTimer = null;

$(document).on('keyup', '#Restaurent', function(){    
  var value = $('.inputval').val();
  
  // Clear previous timer if it exists
  if (searchTimer !== null) {
    clearTimeout(searchTimer);
  }

  if (value != "") {
    // Set new timer for 500ms delay
    searchTimer = setTimeout(function() {
      $('.att-list').html("");
      $(".att-list").css("display", "block");
      
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'hotels/search_restaurent',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('.att-list').html("");
          response.forEach(function(country) {
            var listItem = $('<li>')
              .text(country.value)
              .attr('data-id', country.id); // Add data-id attribute
            $('.att-list').append(listItem);
          });
        }
      });
    }, 500); // 500ms delay
  } else {
    $('.att-list').html("");
  }
});

// Update click handler to use data-id
$(document).on('click', ".att-list li", function(){
  $('.value').html('<button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value" data-id="' + $(this).data('id') + '">' + $(this).text() + '</button>');
  $('.att-list').fadeOut();
  $('.att-list').html("");
});
  $(document).on('keyup', '#Neighborhood', function(){    
    var value = $('.inputval').val();
    if (value != "") {
      $('.att-list').html("");
      $(".att-list").css("display", "block");
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'hotels/search_neighborhood',
        data: {
          'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
          $('.att-list').html("");
          response.forEach(function(country) {
            // Add the ID to the li element as a data attribute
            var listItem = $('<li>')
              .text(country.value)
              .attr('data-id', country.id);
            $('.att-list').append(listItem);
          });
        }
      });
    } else {
      $('.att-list').html("");
    }
    $(document).on('click', ".att-list li", function(){
      var itemId = $(this).data('id');
      var itemText = $(this).text();
      
      // Add both text and ID to the button
      $('.value').html('<button class="btn btn-secondary btn-lg border-0 margin-l ml-3 nearby-value" data-id="' + itemId + '">' + itemText + '</button>');
      $('.att-list').fadeOut();
      $('.att-list').html("");
    });
  });



//landing with filters

  $(document).ready(function() {
     $(document).on('change', '.select-filters', function() {
      var selectedValue = $(this).val();
   
      if (selectedValue == "Hotel Amenities") {

        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Hotel-Amenities" class="Hotel-Amenities form-control rounded-3" placeholder="Search Hotel Amenities"><i class="ti ti-search"></i><div id="hamt_list"></div>');

      } else if (selectedValue == "Room Amenities") {
       
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><select class="room-amenities form-select"><option >Select Room Amenities</option><option value="Toilet paper">Toilet paper</option><option value="Towels">Towels</option> <option value="Additional toilet">Additional toilet</option><option value="Private bathroom">Private bathroom</option> <option value="Toilet">Toilet</option><option value="Free toiletries">Free toiletries</option> <option value="Hairdryer">Hairdryer</option> <option value="Shower">Shower</option> <option value="Linen">Linen</option><option value="Socket near the bed">Socket near the bed</option> <option value="Clothes rack">Clothes rack</option> <option value="Flat-screen TV">Flat-screen TV</option> <option value="Radio">Radio</option> <option value="Telephone">Telephone</option>  <option value="TV">TV</option> <option value="Coffee house on site">Coffee house on site</option>   <option value="Fruits">Fruits</option> <option value="Wine/champagne">Wine/champagne</option>  <option value="Kid-friendly buffet">Kid-friendly buffet</option> <option value="Kid meals">Kid meals</option> <option value="Special diet menus (on request)">Special diet menus (on request)</option> <option value="Snack bar">Snack bar</option>  <option value="Bar">Bar</option>   <option value="Tea/Coffee maker">Tea/Coffee maker</option>  </select>');
      } else if (selectedValue == "Hotel Class Ratings") {
        $('.change-with-filter').html('<select id="starRatingSelect" class="form-control"><option>Select Rating</option><option value="1 Star">1 Star</option><option value="2 Star">2 Stars</option><option value="3 Star">3 Stars</option><option value="4 Star">4 Stars</option><option value="5 Star">5 Stars</option></select>');
      } else if (selectedValue == "Hotel Pricing") {
        $('.change-with-filter').html('<select class="Hotel-Pricing  form-select"><option >Select Price</option><option value="500">500</option><option value="1000">1000</option><option value="1500">1500</option></select>');
      } else if (selectedValue == "Room Types") { 
        $('.change-with-filter').html(' <div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Room_type" class="Room-type form-control rounded-3" placeholder="Search Room-type"><i class="ti ti-search"></i><div id="room_list"></div>');
      }  else if (selectedValue == "Distance") { 
        $('.change-with-filter').html('<select id="distanceSelect" class="form-control"><option value="">select</option>   <option value="0.5 km">Less than 0.5 km</option>   <option value="1 km">1 km</option> <option value="1.5 km">1.5 km</option> <option value="2 km">2 km</option>  <option value="2.5 km">2.5 km</option> <option value="3 km">3 km</option>');
      }  else if (selectedValue == "Hotel Style") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="hotel-style" class="hotel-style form-control rounded-3" placeholder="Search Hotel Style"><i class="ti ti-search"></i><div id="hotel-type-list"></div>');
      } 
      else if (selectedValue == "On-site Restaurants") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="On-site-Restaurants" class="hotel-style form-control rounded-3" placeholder="Search On-site-Restaurants"><i class="ti ti-search"></i><div id="On-site-list"></div>');
      } else if (selectedValue == "Hotel Tags") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Hotel-Tags" class="Hotel-Tags form-control rounded-3" placeholder="Search Hotel Tags"><i class="ti ti-search"></i><div id="Hotel-Tags-list"></div>');
      } else if (selectedValue == "Hotel Tags") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Hotel-Tags" class="Hotel-Tags form-control rounded-3" placeholder="Search Hotel Tags"><i class="ti ti-search"></i><div id="Hotel-Tags-list"></div>');
      } else if (selectedValue == "Metro/Public Transit Access") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Public-Transit-Access" class="Public Transit Access form-control rounded-3" placeholder="Search Public Transit Access"><i class="ti ti-search"></i><div id="Public-Transit-Access-list"></div>'); 
      } else if (selectedValue == "Metro/Public Transit Access") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="access" class="Public Transit Access form-control rounded-3" placeholder="Search Public Transit Access"><i class="ti ti-search"></i><div id="access-list"></div>'); 
      } else if (selectedValue == "Access") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="access" class="Public Transit Access form-control rounded-3" placeholder="Search Access"><i class="ti ti-search"></i><div id="access-list"></div>'); 
      } else if (selectedValue == "amenities") { 
        $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="ameni_ties" class="Amenities form-control rounded-3" placeholder="Search Amenities"><i class="ti ti-search"></i><div id="amenity-list"></div>');
      }

      
      
     
    });


    

    $(document).on('keyup','#Hotel-Amenities', function(){	 
   
      var value = $('#Hotel-Amenities').val();
      if(value != ""){
        $("#hamt_list").css("display", "block");
  
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'POST',
              url:  base_url+  'hotels/search_hotel_amenti',
              data: { 'val': value,
              '_token': $('meta[name="csrf-token"]').attr('content')},
           
              success: function(response){
          
                $('#hamt_list').html("");
    
            
                response.forEach(function(country) {
                    var listItem = $('<li>').text(country.value);
                 //   $('#country-list').fadeIn();
                    $('#hamt_list').append(listItem);
                });
             }
          });
        }else{       
          $('#hamt_list').append("");    
        }
      
      })  

   // start landing amenities
      $(document).on('keyup','#ameni_ties', function(){	 
   
        var value = $('#ameni_ties').val();
        $('#amenity-list').html("");
        if(value != ""){
          $("#amenity-list").css("display", "block");
    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }, 
                type:'POST',
                url:  base_url+  'hotels/amenties',
                data: { 'val': value,
                '_token': $('meta[name="csrf-token"]').attr('content')},
             
                success: function(response){
            
                 
      
              
                  response.forEach(function(country) {
                      var listItem = $('<li>').text(country.value);
                   //   $('#country-list').fadeIn();
                      $('#amenity-list').append(listItem);
                  });
               }
            });
          }else{       
            $('#hamt_list').append("");    
          }
        
        }) 


        $(document).ready(function() {
          $(document).on('click', '#amenity-list li', function() {
               
               var selectedValue = $(this).text();
               $('#amenity-list').css("display","none");
             
               $('#amenity-list').val('');
         
               var existingSpan = $('.Amenities').find('span');
             
               var valueExists = false;
             
               existingSpan.each(function() {
                 console.log(existingSpan);
                 if ($(this).find('button').text().trim() === selectedValue) {
                   valueExists = true;
                   return false;
                 }
               });    
               if (existingSpan.length > 0) {
                 if (!valueExists) {
                   var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
                   $('.Amenities').append('<span class="margin-l"><button class="btn btn-secondary">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
                 }
               } else {
         
                 var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
                 $('.Amenities').html('<span><button class="btn btn-secondary ">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
               }
               });
       
         $(document).on('click', '.Amenities i.fa-trash', function() {
           $(this).closest('span').remove();
         });
       });
       
        // end landing amenities

//room mnt

    $(document).on('change', '.change-with-filter .room-amenities', function() {
      var selectedValue = $(this).val();
      $('.mnt-heading').removeClass('d-none');
    
   
      var existingSpan = $('.mnt').find('span');

      var valueExists = false;

      existingSpan.each(function() {
        if ($(this).data('value') === selectedValue) {
          valueExists = true;
          return false;
        }
      });
    
      if (existingSpan.length > 0) {
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.mnt').append('<span class="margin-l" ><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
      } else {
     
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.mnt').html('<span class="mt-5 "><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      }
    });

 //hotel mnt   
    $(document).on('click', '#hamt_list li', function() {
      
      var selectedValue = $(this).text();

      $('#hamt_list').css("display","none");
      $('.hotel-mnt-heading').removeClass('d-none');
      $('#Hotel-Amenities').val('');
     
      var existingSpan = $('.hotel-mnt').find('span');
      var valueExists = false;

      existingSpan.each(function() {
        if ($(this).text().trim() === selectedValue) {
          valueExists = true;
          return false;
        }
      });
    
      if (existingSpan.length > 0) {
     
          if (!valueExists) {
                  var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
                  $('.hotel-mnt').append('<span class="margin-l"><button class="btn btn-secondary  margin-top hotel-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
      } else {
    
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.hotel-mnt').html('<span><button class="btn btn-secondary margin-top hotel-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      }
    });

// hotel pricing
    $(document).on('change', '.change-with-filter .Hotel-Pricing', function() {
      var selectedValue = $(this).val();
      $('.Hotel-Pricing-heading').removeClass('d-none');
    
      // Check if the span already exists in .mnt class
      var existingSpan = $('.Hotel_Pricing').find('span');
      var valueExists = false;

      existingSpan.each(function() {
        if ($(this).find('button').text().trim() === selectedValue) {
          valueExists = true;
          return false;
        }
      });

      if (existingSpan.length > 0) {
      
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.Hotel_Pricing').append('<span class="margin-l"><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
      } else {
        // Span doesn't exist, append a new span with the button
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.Hotel_Pricing').html('<span><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      }

    });

    
    // star rating
$(document).on('change', '.change-with-filter #starRatingSelect', function() {
  var selectedValue = $(this).val();
  $('.star-heading').removeClass('d-none');

 
  var existingSpan = $('.star-rating').find('span');
  var valueExists = false;

  existingSpan.each(function() {
    if ($(this).find('button').text().trim() === selectedValue) {
      valueExists = true;
      return false;
    }
  });


  if (existingSpan.length > 0) {
    if (!valueExists) {
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.star-rating').append('<span class="margin-l"><button class="btn btn-secondary  margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    }
  } else {
    
    var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
    $('.star-rating').html('<span><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
  }
});



// Distance
  $(document).on('change', '.change-with-filter #distanceSelect', function() {
    var selectedValue = $(this).val();
    $('.distance-heading').removeClass('d-none');

    var existingSpan = $('.distance').find('span');
    var valueExists = false;

    existingSpan.each(function() {
      if ($(this).find('button').text().trim() === selectedValue) {
        valueExists = true;
        return false;
      }
    });

    if (existingSpan.length > 0) {
      if (!valueExists) {
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.distance').append('<span class="margin-l"><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      } 
    } else {
      
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.distance').html('<span><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    }
  });

//room type
    $(document).on('keyup','#Room_type', function(){	 
   
      var value = $('#Room_type').val();
      if(value != ""){
        $("#room_list").css("display", "block");
  
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'POST',
              url:  base_url+  'hotels/get_Room_type',
              data: { 'val': value,
              '_token': $('meta[name="csrf-token"]').attr('content')},
           
              success: function(response){
          
                $('#room_list').html("");
    
           
                response.forEach(function(country) {
                    var listItem = $('<li>').text(country.value);
                 //   $('#country-list').fadeIn();
                    $('#room_list').append(listItem);
                });
             }
          });
        }else{
       
          $('#room_list').append("");
       //   $('#country-list').fadeOut();
        }
      
      }) 

  
$(document).on('click', '#room_list li', function() {
      
      var selectedValue = $(this).text();
      $('#room_list').css("display","none");
      $('.room-type-heading').removeClass('d-none');
      $('#room_list').val('');

      var existingSpan = $('.room_type').find('span');

      var existingSpan = $('.room_type').find('span');
      var valueExists = false;
    
      existingSpan.each(function() {
        if ($(this).find('button').text().trim() === selectedValue) {
          valueExists = true;
          return false;
        }
      });
    
    
      if (existingSpan.length > 0) {
        if (!valueExists) {
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          $('.room_type').append('<span class="margin-l"><button class="btn btn-secondary  room_type' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
      } else {

        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.room_type').html('<span><button class="btn btn-secondary room_type' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      }
      });

  
    
   }); 




//Hotel style    

$(document).on('keyup','#hotel-style', function(){	 
   
  var value = $('#hotel-style').val();
  if(value != ""){
    $("#hotel-type-list").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/get_hotel_type',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#hotel-type-list').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);
             //   $('#country-list').fadeIn();
                $('#hotel-type-list').append(listItem);
            });
         }
      });
    }else{
   
      $('#hotel-type-list').append("");
   //   $('#country-list').fadeOut();
    }
  
  }) 

$(document).ready(function() {
   $(document).on('click', '#hotel-type-list li', function() {
        
        var selectedValue = $(this).text();
        $('#hotel-type-list').css("display","none");
      
        $('#hotel-type-list').val('');
  
        var existingSpan = $('.hotel-style').find('span');
  
        var existingSpan = $('.hotel-style').find('span');
        var valueExists = false;
      
        existingSpan.each(function() {
          console.log(existingSpan);
          if ($(this).find('button').text().trim() === selectedValue) {
            valueExists = true;
            return false;
          }
        });
      
      
        if (existingSpan.length > 0) {
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.hotel-style').append('<span class="margin-l"><button class="btn btn-secondary  hotel-style' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
        } else {
  
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          $('.hotel-style').html('<span><button class="btn btn-secondary hotel-style' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
        });

  $(document).on('click', '.hotel-style i.fa-trash', function() {
    $(this).closest('span').remove();
  });
});

//end hotel style 

// one sight restaurant  

$(document).on('keyup','#On-site-Restaurants', function(){	 
   
  var value = $('#On-site-Restaurants').val();
  if(value != ""){
    $("#On-site-list").css("display", "block");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/get_onsight_restaurant',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){      
            $('#On-site-list').html("");       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);     
                $('#On-site-list').append(listItem);
            });
         }
      });
    }else{
   
      $('#On-site-list').append("");  
    }
  
  }) 

$(document).ready(function() {
   $(document).on('click', '#On-site-list li', function() {
        
        var selectedValue = $(this).text();
        $('#On-site-list').css("display","none");
      
        $('#On-site-list').val('');
  
        var existingSpan = $('.on-site-restaurants').find('span');
      
        var valueExists = false;
      
        existingSpan.each(function() {
          console.log(existingSpan);
          if ($(this).find('button').text().trim() === selectedValue) {
            valueExists = true;
            return false;
          }
        });
      
      
        if (existingSpan.length > 0) {
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.on-site-restaurants').append('<span class="margin-l"><button class="btn btn-secondary on-site-restaurants' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
        } else {
  
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          $('.on-site-restaurants').html('<span><button class="btn btn-secondary on-site-restaurants' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
        });

  $(document).on('click', '#onsite-restaurants i.fa-trash', function() {
    $(this).closest('span').remove();
  });
});

//end on sight restaurant

// Hotel-Tags

$(document).on('keyup','#Hotel-Tags', function(){	 
   
  var value = $('#Hotel-Tags').val();
  if(value != ""){
    $("#Hotel-Tags-list").css("display", "block");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/get_hotel_tags',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){      
            $('#Hotel-Tags-list').html("");       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);     
                $('#Hotel-Tags-list').append(listItem);
            });
         }
      });
    }else{
   
      $('#Hotel-Tags-list').append("");  
    }
  
  }) 

$(document).ready(function() {
   $(document).on('click', '#Hotel-Tags-list li', function() {
        
        var selectedValue = $(this).text();
        $('#Hotel-Tags-list').css("display","none");
      
        $('#Hotel-Tags-list').val('');
  
        var existingSpan = $('.Hotel_Tags').find('span');
      
        var valueExists = false;
      
        existingSpan.each(function() {
          console.log(existingSpan);
          if ($(this).find('button').text().trim() === selectedValue) {
            valueExists = true;
            return false;
          }
        });    
        if (existingSpan.length > 0) {
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.Hotel_Tags').append('<span class="margin-l"><button class="btn btn-secondary on-site-restaurants' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
        } else {
  
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          $('.Hotel_Tags').html('<span><button class="btn btn-secondary on-site-restaurants' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
        });

  $(document).on('click', '.Hotel_Tags i.fa-trash', function() {
    $(this).closest('span').remove();
  });
});

//end on hotel tags'

// public transit

$(document).on('keyup','#Public-Transit-Access', function(){	 
   
  var value = $('#Public-Transit-Access').val();
  if(value != ""){
    $("#Public-Transit-Access-list").css("display", "block");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/get_public_transit',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){      
            $('#Public-Transit-Access-list').html("");       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);     
                $('#Public-Transit-Access-list').append(listItem);
            });
         }
      });
    }else{
   
      $('#Public-Transit-Access-list').append("");  
    }
  
  }) 

$(document).ready(function() {
   $(document).on('click', '#Public-Transit-Access-list li', function() {
        
        var selectedValue = $(this).text();
        $('#Public-Transit-Access-list').css("display","none");
      
        $('#Public-Transit-Access-list').val('');
  
        var existingSpan = $('.Public_Transit').find('span');
      
        var valueExists = false;
      
        existingSpan.each(function() {
          console.log(existingSpan);
          if ($(this).find('button').text().trim() === selectedValue) {
            valueExists = true;
            return false;
          }
        });    
        if (existingSpan.length > 0) {
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.Public_Transit').append('<span class="margin-l"><button class="btn btn-secondary on-site-restaurants' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
        } else {
  
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          $('.Public_Transit').html('<span><button class="btn btn-secondary on-site-restaurants' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
        });

  $(document).on('click', '.Public_Transit i.fa-trash', function() {
    $(this).closest('span').remove();
  });
});

//end on  public transit'

// access 

$(document).on('keyup','#access', function(){	 
   
  var value = $('#access').val();
  if(value != ""){
    $("#access-list").css("display", "block");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'hotels/get_access',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){      
            $('#access-list').html("");       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);     
                $('#access-list').append(listItem);
            });
         }
      });
    }else{
   
      $('#access-list').append("");  
    }
  
  }) 

$(document).ready(function() {
   $(document).on('click', '#access-list li', function() {
        
        var selectedValue = $(this).text();
        $('#access-list').css("display","none");
      
        $('#access-list').val('');
  
        var existingSpan = $('.Access').find('span');
      
        var valueExists = false;
      
        existingSpan.each(function() {
          console.log(existingSpan);
          if ($(this).find('button').text().trim() === selectedValue) {
            valueExists = true;
            return false;
          }
        });    
        if (existingSpan.length > 0) {
          if (!valueExists) {
            var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
            $('.Access').append('<span class="margin-l"><button class="btn btn-secondary">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
          }
        } else {
  
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          $('.Access').html('<span><button class="btn btn-secondary ">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
        });

  $(document).on('click', '.Access i.fa-trash', function() {
    $(this).closest('span').remove();
  });
});

//end on  public access'


  $(document).on('click', '.star-rating i.fa-trash', function() {
    $(this).closest('span').remove();
  });
  $(document).on('click', '.distance i.fa-trash', function() {
    $(this).closest('span').remove();
  });
  $(document).on('click', '.room_type i.fa-trash', function() {
    $(this).closest('span').remove();
  });
  $(document).on('click', '.Hotel_Pricing i.fa-trash', function() {
    $(this).closest('span').remove();
  });
  $(document).on('click', '.hotel-mnt i.fa-trash', function() {
    $(this).closest('span').remove();
  });
  $(document).on('click', '.mnt i.fa-trash', function() {
    $(this).closest('span').remove();
  });

//end delete values
  
//end with filter



//add landing page





//end add landing page
$(document).on('click','#add_landing', function(){	 
  var name = $('#name').val();
  var slug = $('#slug').val(); 
  var meta_title = $('#meta_title').val();  
  var meta_tags = $('#meta_tags').val();
  var about = $('#about').val();
  
  // Validate required fields
  if (!name || name.trim() === '') {
    $('#Success').html('<div class="alert alert-danger">Name is required</div>');
    return;
  }

  $('.loadResult').removeClass('hide');
  var hotelId = $('#hotelId').val();
  var locationId = $('#locationId').val();
  
  var nearby = $('.nearby-value').text();
  var neighborhoodId = $('.nearby-value').data('id');

  var roommntArray = [];
  $('.mnt button').each(function() {
    var buttonText = $(this).text();
    roommntArray.push(buttonText);
  })

  var ratingarray = [];
  $('.star-rating button').each(function() {
    var startext = $(this).text();
    ratingarray.push(startext);
  });

  var hotelmntarray = [];
  $('.hotel-mnt button').each(function() {
    var hotemnttext = $(this).text();
    hotelmntarray.push(hotemnttext);
  });

  var HotelPricing_array = [];
  $('.Hotel_Pricing button').each(function() {
    var hotepricetext = $(this).text();
    HotelPricing_array.push(hotepricetext);
  });

  var room_type_array = [];
  $('.room_type button').each(function() {
    var roomtypetext = $(this).text();
    room_type_array.push(roomtypetext); 
  });  

  var distance_array = [];
  $('.distance button').each(function() {
    var distancetext = $(this).text();
    distance_array.push(distancetext); 
  }); 

  var hotelstyle_array = [];
  $('.hotel-style button').each(function() {
    var hotelstyletext = $(this).text();
    hotelstyle_array.push(hotelstyletext); 
  });
  
  var onsiterestaurants_array = [];
  $('.on-site-restaurants button').each(function() {
    var on_sitetext = $(this).text();
    onsiterestaurants_array.push(on_sitetext); 
  });

  var Hotel_Tags_array = [];
  $('.Hotel_Tags button').each(function() {
    var Hotel_Tagstext = $(this).text();
    Hotel_Tags_array.push(Hotel_Tagstext); 
  });

  var Public_Transit_array = [];
  $('.Public_Transit button').each(function() {
    var Public_Transit = $(this).text();
    Public_Transit_array.push(Public_Transit); 
  });

  var Access_value_array = [];
  $('.Access button').each(function() {
    var accesstext = $(this).text();
    if (Access_value_array.indexOf(accesstext) === -1) {
      Access_value_array.push(accesstext);
    }
  });

  var nearbytype = $('input[name="near_by"]:checked').val();

  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: base_url + 'hotels/store_landing',
    data: { 
      'name': name,
      'slug': slug,
      'meta_title': meta_title,
      'meta_desc': meta_tags,
      'about': about,
      'nearby': nearby,
      'roommntArray': roommntArray,
      'ratingarray': ratingarray,
      'hotelmntarray': hotelmntarray,
      'HotelPricing_array': HotelPricing_array,
      'room_type_array': room_type_array,
      'distance_array': distance_array,
      'hotelstyle_array': hotelstyle_array,
      'onsiterestaurants_array': onsiterestaurants_array,
      'Hotel_Tags_array': Hotel_Tags_array,
      'Public_Transit_array': Public_Transit_array,
      'Access_value_array': Access_value_array,
      'hotelId': hotelId,
      'nearbytype': nearbytype,
      'nearbyid':neighborhoodId,
      'locationId':locationId,
      '_token': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response){
      $('.loadResult').addClass('hide');
      if(response == 1){
        var editUrl = base_url + 'hotels/edit_hotel_landing/' + hotelId;
        window.location.href = editUrl;
      } else {
        $('#Success').html('<div class="alert alert-danger">Landing page not Added</div>')
      }
      console.log(response);
    }
  });
});


  
  $(document).on('click','.updatelandigfilters', function(){	 
    $('.loadResult').removeClass('hide');

    var id =  $(this).data('id');
    var colid = $(this).data('colid');
    var hotelId =  $('#hotelId').val();
    var nearby = $('.nearby-value').text();
  


    $("#buttonsContainer-" + 6).addClass("d-none");
   

    var roommntArray = [];
    $('.mnt button').each(function() {
      var buttonText = $(this).text();
      roommntArray.push(buttonText);
    });
    var ratingarray = [];
    $('.star-rating button').each(function() {
      var startext = $(this).text();
      ratingarray.push(startext);
    });
  
    var hotelmntarray = [];
    $('.hotel-mnt button').each(function() {
      var hotemnttext = $(this).text();
      hotelmntarray.push(hotemnttext);
    });
    var HotelPricing_array = [];
    $('.Hotel_Pricing button').each(function() {
      var hotepricetext = $(this).text();
      HotelPricing_array.push(hotepricetext);
    });
    var room_type_array = [];
    $('.room_type button').each(function() {
      var roomtypetext = $(this).text();
      room_type_array.push(roomtypetext); 
    });  
    var distance_array = [];
    $('.distance button').each(function() {
      var distancetext = $(this).text();
      distance_array.push(distancetext); 
    }); 
  
    var hotelstyle_array = [];
    $('.hotel-style button').each(function() {
      var hotelstyletext = $(this).text();
      hotelstyle_array.push(hotelstyletext); 
    });
    
    var onsiterestaurants_array = [];
    $('.on-site-restaurants button').each(function() {
      var on_sitetext = $(this).text();
      onsiterestaurants_array.push(on_sitetext); 
    });
  
    var Hotel_Tags_array = [];
    $('.Hotel_Tags button').each(function() {
      var Hotel_Tagstext = $(this).text();
      Hotel_Tags_array.push(Hotel_Tagstext); 
    });
  
    var Public_Transit_array = [];
    $('.Public_Transit button').each(function() {
      var Public_Transit = $(this).text();
      Public_Transit_array.push(Public_Transit); 
    });
  
    var Access_value_array = [];
  
    $('.Access button').each(function() {
      var accesstext = $(this).text();
      if (Access_value_array.indexOf(accesstext) === -1) {
        Access_value_array.push(accesstext);
      }
    });

    var amenities_array = [];
    $('.Amenities button').each(function() {
        var mnt_val = $.trim($(this).text());
        amenities_array.push(mnt_val);
    });
    
    console.log(amenities_array);
    

    var amenities_set = new Set();
$('.Amenities button').each(function() {
    var mnt_val = $.trim($(this).text());
    amenities_set.add(mnt_val);
});

var amenities_array = Array.from(amenities_set);
console.log(amenities_array);
  
  //alert(amenities_array);
    var nearbytype = $('input[name="near_by"]:checked').val();
   
  
  
        $.ajax({ 
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url: base_url+ 'hotels/update_landingfilter',
            data: { 'nearby':nearby,'roommntArray':roommntArray,'ratingarray':ratingarray,'hotelmntarray':hotelmntarray,'HotelPricing_array':HotelPricing_array,'room_type_array':room_type_array,'distance_array':distance_array,'hotelstyle_array':hotelstyle_array,'onsiterestaurants_array':onsiterestaurants_array,'Hotel_Tags_array':Hotel_Tags_array,'Public_Transit_array':Public_Transit_array,'Access_value_array':Access_value_array,'hotelId':hotelId ,'nearbytype':nearbytype,'hotelId':hotelId,'id':id,'amenities_array':amenities_array,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
             $('.loadResult').addClass('hide');
             if(response == 1){
       
              $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
              setTimeout(function() {
               $('#Success').html('');
               }, 30000);
             }else{
              $('#Success').html('<div class="alert alert-success"> Data not updated.</div>');              
              setTimeout(function() {
               $('#Success').html('');
               }, 30000);
              
             }
       
              console.log(response);
            }
        });
    })
  //restaurent

$(document).on('click','#searchRestaurnt', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#search_input').val();

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'restaurant/searchrest',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
          }
      });
  })

  $(document).ready(function() {
    var fieldCount = 0; 

    $('#addmenu').on('click', function() {
      fieldCount++;

      var html = ' <div class="col-md-6 mn"> <div class="form-group mt-3"> <strong>Menu Item '+fieldCount+'</strong>  <input type="text" name="menu[]" class="form-control rounded-3" placeholder="Menu Item" required>  </div>  </div>  ';

      $('#menuitem').append(html);
    });

    $(document).on('click', '.fa-trash', function() {
      $(this).closest('.col-md-6').remove();
    });
  });

//feature
  $(document).on('keyup','#searchfeature', function(){	 
   
    var value = $('#searchfeature').val();
    if(value != ""){
      $("#featurelist").css("display", "block");
      $('#featurelist').append("");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url:  base_url+  'restaurant/searchfeature',
            data: { 'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
         
            success: function(response){
        
              $('#featurelist').html("");
  
         
              response.forEach(function(country) {
                  var listItem = $('<li>').text(country.value);
               //   $('#country-list').fadeIn();
                  $('#featurelist').append(listItem);
              });
           }
        });
      }else{
     
        $('#featurelist').append("");

      }
    
    }) 



    $(document).on('click', '#featurelist li', function() {
      var selectedValue = $(this).text();
     
      $('#featurelist').css("display", "none");
      $('.featurelist-heading').removeClass('d-none');
      $('#searchfeature').val('');
  
      // Check if the feature already exists in .feature-mnt
      var existingFeature = $('.feature-mnt').find('input[value="' + selectedValue + '"]').length > 0;
      var checkspan = $('.feature-mnt').find('span').length > 0;
      if(checkspan){
        if (!existingFeature) {
          // Feature doesn't exist, append a new span with the button and hidden input
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          var newButton = '<span class="margin-l ft"><button class="btn btn-secondary featurn feature-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i><input type="hidden" value="'+selectedValue+'" name="featureitem[]"></span>';
          $('.feature-mnt span:last').append(newButton);
        } 
      }else{
        if (!existingFeature) {
        
          var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
          var newButton = '<span class="margin-l ft"><button class="btn btn-secondary featurn feature-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i><input type="hidden" value="'+selectedValue+'" name="featureitem[]"></span>';
      
          $('.feature-mnt').append(newButton);
        } 
      }
     

  });
  

  
    $(document).on('click', 'i.fa-trash', function() {
      $(this).closest('span.ft').remove();
  });

//feature end 
//Dietary
$(document).on('keyup','#searchDietary', function(){	 
   
  var value = $('#searchDietary').val();
  if(value != ""){
    $("#Dietarylist").css("display", "block");
    $('#Dietarylist').append("");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'restaurant/searchDietary',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#Dietarylist').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);
             //   $('#country-list').fadeIn();
                $('#Dietarylist').append(listItem);
            });
         }
      });
    }else{
   
      $('#Dietarylist').append("");

    }
  
  }) 



  $(document).on('click', '#Dietarylist li', function() {
    var selectedValue = $(this).text();
   
    $('#Dietarylist').css("display", "none");
    $('.Dietarylist-heading').removeClass('d-none');
    $('#searchDietary').val('');

    // Check if the feature already exists in .feature-mnt
    var existingFeature = $('.Dietary-mnt').find('input[value="' + selectedValue + '"]').length > 0;
    var checkspan = $('.Dietary-mnt').find('span').length > 0;
    if(checkspan){
      if (!existingFeature) {
        // Feature doesn't exist, append a new span with the button and hidden input
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        var newButton = '<span class="margin-l dt"><button class="btn btn-secondary Dietary Dietary-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i><input type="hidden" value="'+selectedValue+'" name="dietryitems[]"></span>';
        $('.Dietary-mnt span:last').append(newButton);
      } 
    }else{
      if (!existingFeature) {
      
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        var newButton = '<span class="margin-l dt"><button class="btn btn-secondary Dietary Dietary-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i><input type="hidden" value="'+selectedValue+'" name="dietryitems[]"></span>';
    
        $('.Dietary-mnt').append(newButton);
      } 
    }
   

});



  $(document).on('click', 'i.fa-trash', function() {
    $(this).closest('span.dt').remove();
});

//Dietary end 



$(document).ready(function() {
  $('#savecustom-faq').click(function() {
  
    $('#savecustom-faq').prop('disabled', true);
    var question = $('#addques').val().trim();

    if (question === "") {
      $('#errorcustfaq').text('Question is Required.').css('color', 'red');
      setTimeout(function(){
        $('#errorcustfaq').text('');
      },30000);
      return;
    }
    $('#errorcustfaq').text('');
    var restid = $('#savecustom-faq').data('id');

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url:  base_url+ 'restaurant/add_rest_faq',
      data: { 'checkboxText': question,'restid':restid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('#savecustom-faq').prop('disabled', false);
        $('.getupdatedfaq').html(response);
        $('#staticBackdrop').modal('hide');
        var question = $('#addques').val('')
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 30000);
      }
  });

  });
});

$(document).ready(function() {
  $('#saveButton-faq').click(function() {
    $('#saveButton-faq').prop('disabled', true);
    var checkedCheckboxes = $('input[type="checkbox"]:checked');
    if (checkedCheckboxes.length === 0) {
      $('#errorcheck').text('Please Select a question').css('color', 'red');
      setTimeout(function(){
        $('#errorcheck').text('');
      },30000);
      return;
    }
    var checkboxText = '';
    if (checkedCheckboxes.length != 0) {
      $('#errorcheck').text('');
      checkedCheckboxes.each(function() {
        checkboxText += $(this).next('span').text() + '\n';
      });
    }
   var restid = $('#saveButton-faq').data('id');
 
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url:  base_url+ 'restaurant/add_rest_faq',
      data: { 'checkboxText': checkboxText,'restid':restid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('#saveButton-faq').prop('disabled', false);
        $('#staticBackdrop').modal('hide');
        $('.getupdatedfaq').html(response);
        $('.checkbox').prop('checked', false);
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 30000);
      }
  });

   
  });
});



//update hotel
$(document).on('click','.updaterestfaq', function(){	 
  
  var faqId = $(this).data('id');
  var restid = $("#restid").val();
  var questionTextarea =  $('#question' + faqId).val(); 
  var answerTextarea =   $('#Answer' + faqId).val();


  var isValid = true;

  if (questionTextarea == '') {
    $('#questionmsg-'+faqId).text('Question is required.').css('color', 'red');
    isValid = false;
 } else {
   $('#questionmsg-'+faqId).text('');
 }
//  if (answerTextarea == '') {
//    $('#answer-'+faqId).text('Answer is required.').css('color', 'red');
//    isValid = false;

//  } else {
//    $('#answer-'+faqId).text('');
//  }
 if (!isValid) {
   return; // Stop execution if the form is not valid
 }
 $("#buttonsContainer-" + faqId).addClass("d-none");
 $('#question' + faqId).prop('disabled', true);
 $('#Answer' + faqId).prop('disabled', true);
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url:  base_url+ 'restaurant/update_rest_faq',
          data: { 'question': questionTextarea,'faqId':faqId,'answer':answerTextarea,'restid':restid,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.getupdatedfaq').html(response);
     
            $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);
          }
      });
  }) 




  function handleAddCuisineClick() {
    var id = $(this).data('id');
    $('#result').empty();
    $('#addcusisine').prop('disabled', true);

    var checkedValues = [];
    $('input[name="fruit"]:checked').each(function () {
      var value = $(this).val();
      checkedValues.push(value);
    });

    if (checkedValues.length === 0) {
      $('#inputerror').text('Please select a Cuisine').css('color', 'red');
      return;
    }

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: base_url + 'restaurant/addcuisine',
      data: { 'checkedValues': checkedValues, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content') },
      success: function (response) {
        $('#addcusisine').prop('disabled', false);
        $('input[name="fruit"]:checked').prop('checked', false);

        $('#staticBackdrop').modal('hide');
        $('.getupdatedfaq').html(response);

        $('#addcusisine').off('click').on('click', handleAddCuisineClick);

      }
    });
  }

  $(document).on('click', '#addcusisine', handleAddCuisineClick);



  function handleDeleteCuisineClick() {
    var id = $(this).data('id');
    var restid = $('#restname').val();
    var confirmation = confirm("Are you sure you want to delete this Cuisine?");

    if (confirmation) {
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: base_url + 'restaurant/deleterestaurantcus',
        data: { 'restid': restid, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content') },
        success: function(response) {
          $('#staticBackdrop').modal('hide');
          $('.getupdatedfaq').html(response);
        }
      });
    } else {
      console.log("Deletion canceled");
    }
  }

  $(document).on('click', '.delete-Cuisine', handleDeleteCuisineClick);



  //review

  $(document).on('click','.rest-review-option', function(){	 
    $('.loadResult').removeClass('hide');
    var value = $(this).data('value');

    $('.rest-review-option').removeClass('sel-option')
    $('.rest-review-option').css({
      'font-weight': '',
      'text-decoration': ''
    });
    $('.opt-' + value).css({
      'font-weight': 'bold',
      'text-decoration': 'underline'
    });
    $('.opt-' + value).addClass('sel-option');
  var id = $('#restid').val();

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'post',
        url: base_url+ 'restaurant/ftrrestrewview',
        data: { 'val': value,'id':id,
        '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
          $('.loadResult').addClass('hide');
        
          $('.list-container').html(response);
            
          
        } 
    });
}) 


function editrestReview(id) {
  var textarea = $("#reviewField-" + id);
  textarea.prop("disabled", false);
  var value = $('#edit-btn').attr('value');
  // alert(value)
  // $("#buttonsContainer-" + id).removeClass("d-none");
  $("#editBtn-" + id).addClass("d-none");
  
  if (value == 1) {
    $("#buttonsContainer-" + id + "-2").removeClass("d-none");
    $("#buttonsContainer-" + id + "-5").removeClass("d-none");
  } else if (value == 2) {
    $("#buttonsContainer-" + id + "-1").removeClass("d-none");
    $("#buttonsContainer-" + id + "-5").removeClass("d-none");
  } else if (value == 3) {
    $("#buttonsContainer-" + id + "-3").removeClass("d-none");
  } else {
    $("#buttonsContainer-" + id + "-2").removeClass("d-none");
    $("#buttonsContainer-" + id + "-1").removeClass("d-none");
    $("#buttonsContainer-" + id + "-5").removeClass("d-none");
  }


}

function aproverestReview(id,button) {
  $('.loadResult').removeClass('hide');
  var fieldValue  = $(button).val();
  var id = id;
  
  var restid = $('#restid').val();

  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'POST',
    url: base_url + 'restaurant/update_resteview',
    data: { 'value': fieldValue,'id':id,'restid':restid,
    '_token': $('meta[name="csrf-token"]').attr('content')},
    success: function(response){
      $('.loadResult').addClass('hide');
        
        $("#reviewField-" + id).prop("disabled", true);
        $("#buttonsContainer-" + id).addClass("d-none");
        $("#buttonsContainer-" + id + "-0").addClass("d-none");
        $("#buttonsContainer-" + id + "-1").addClass("d-none");
        $("#buttonsContainer-" + id + "-2").addClass("d-none");
        $("#buttonsContainer-" + id + "-5").addClass("d-none");
        $("#editBtn-" + id).removeClass("d-none");

        $('.list-container').html(response);
        $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);

        $('.rest-review-option').removeClass('sel-option')
        $('.rest-review-option').css({
          'font-weight': '',
          'text-decoration': ''
        });
        $('.opt-' + fieldValue).css({
          'font-weight': 'bold',
          'text-decoration': 'underline'
        });
        $('.opt-' + fieldValue).addClass('sel-option');
       
    }

});


}

function markRestReviewspam(id) {
  //$('.loadResult').removeClass('hide');
  var fieldValue  = $('.mark-spam').data('value');
  var id = id;
  var restid = $('#restid').val();
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'POST',
    url: base_url + 'restaurant/update_resteview',
    data: { 'value': fieldValue,'id':id,'restid':restid,
    '_token': $('meta[name="csrf-token"]').attr('content')},
    success: function(response){
      $('.loadResult').addClass('hide');
        
        $("#reviewField-" + id).prop("disabled", true);
        $("#buttonsContainer-" + id).addClass("d-none");
        $("#buttonsContainer-" + id + "-0").addClass("d-none");
        $("#buttonsContainer-" + id + "-1").addClass("d-none");
        $("#buttonsContainer-" + id + "-2").addClass("d-none");
        $("#buttonsContainer-" + id + "-5").addClass("d-none");
        $("#editBtn-" + id).removeClass("d-none");

        
        $('.list-container').html(response);
        $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);

        $('.rest-review-option').removeClass('sel-option')
        $('.rest-review-option').css({
          'font-weight': '',
          'text-decoration': ''
        });
        $('.opt-' + 3).css({
          'font-weight': 'bold',
          'text-decoration': 'underline'
        });
        $('.opt-' + 3).addClass('sel-option');
    }
});


}


  $(document).on('change','#sort_rest_review', function(){	 
      $('.loadResult').removeClass('hide');
      var filter_option = $('.sel-option').data('value');
      var value = $('#sort_rest_review').val();
      var id = $('#restid').val();

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url + 'restaurant/sortRestReview', 
              data: { 'val':value,'id':id,'filter_option':filter_option,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                $('.list-container').html(response);
                
              }
          });
      }) 

  $(document).on('keyup','#filRestReviewbyid', function(){	 
    if( $('#filRestReviewbyid').val() != ""){
  //   $('.loadResult').removeClass('hide');
    var value = $('#filRestReviewbyid').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url: base_url+ 'restaurant/filterrestbyid',
            data: { 'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
          //   $('.loadResult').addClass('hide');
              $('.list-container').html(response);
            
            }
        });
      }
 }) 



 function deleterestReview(id,button) {
  $('.loadResult').removeClass('hide');
  var fieldValue  = $(button).val();
 
  var id = id;
  
  var restid = $('#restid').val();
 
  var confirmation = confirm("Are you sure you want to delete this review?");

  if(confirmation){
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'POST',
    url: base_url + 'restaurant/deleterestreview',
    data: { 'value':fieldValue,'id':id,'restid':restid,
    '_token': $('meta[name="csrf-token"]').attr('content')},
    success: function(response){      
        
        $("#reviewField-" + id).prop("disabled", true);
        $("#buttonsContainer-" + id).addClass("d-none");
        $("#buttonsContainer-" + id + "-0").addClass("d-none");
        $("#buttonsContainer-" + id + "-1").addClass("d-none");
        $("#buttonsContainer-" + id + "-2").addClass("d-none");
        $("#buttonsContainer-" + id + "-5").addClass("d-none");
        $("#editBtn-" + id).removeClass("d-none");

        $('.list-container').html(response);
        $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);

        $('.rest-review-option').removeClass('sel-option')
        $('.rest-review-option').css({
          'font-weight': '',
          'text-decoration': ''
        });
        $('.opt-' + 3).css({
          'font-weight': 'bold',
          'text-decoration': 'underline'
        });
        $('.opt-' + 3).addClass('sel-option');
       
    }

 
});

}

$('.loadResult').addClass('hide');
}

// restaurant images

function deleteImage(id) {
  $('.loadResult').removeClass('hide');
  var restid = $('#restid').val();
  if (confirm('Are you sure you want to delete this image?')) {

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      url: base_url + 'restaurant/delete_review_image',
      data: { 'id':id,'restid':restid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){  
        $('.loadResult').addClass('hide');
        $('.getupdatedimges').html(response);
        $('#Success').html('<div class="alert alert-danger">Image deleted Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);
      }
    });

  }
  $('.loadResult').addClass('hide');
}



$(document).on('keyup','#filterrestimgbyid', function(){	 
  var restid = $('#restid').val();


 //  $('.loadResult').removeClass('hide');
  var value = $('#filterrestimgbyid').val();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post', 
          url: base_url+ 'restaurant/filterimagebyid',  
          data: { 'val': value,'restid':restid,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
       //    $('.loadResult').addClass('hide');
          $('.getupdatedimges').html(response);
          
          }
      });
    
}) 

//end hotel landing page

//add attraction landing page 

$(document).ready(function() {
  $(document).on('change', '.attraction-select-filters', function() {
   var selectedValue = $(this).val();

   if (selectedValue == "Category") {

     $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Attraction-Category" class="Hotel-Amenities form-control rounded-3" placeholder="Search Attraction Category"><i class="ti ti-search"></i><div id="Category_list"></div>');

   }  else if (selectedValue == "Hotel Class Ratings") {    
     $('.change-with-filter').html('<select id="starRatingSelect" class="form-control"><option>Select Rating</option><option value="1 Star">1 Star</option><option value="2 Star">2 Stars</option><option value="3 Star">3 Star</option><option value="4 Star">4 Stars</option><option value="5 Star">5 Stars</option></select>');
   } else if (selectedValue == "traveler Types") {
     $('.change-with-filter').html('<select id="travel_type" class="form-control"><option>Select Traveler Type</option><option value="Family Friendly">Family Friendly</option><option value=" Kids Friendly"> Kids Friendly</option><option value="Couples">Couples</option><option value="Solo Traveler">Solo Traveler</option></select>');
   } else if (selectedValue == "Attraction Tags") { 
     $('.change-with-filter').html('<select id="Attraction_Tags" class="form-control"><option>Select Attraction Tags</option><option value="Wheelchair Accessible">Wheelchair Accessible</option></select>');
   }  else if (selectedValue == "Distance") { 
     $('.change-with-filter').html('<select id="distanceSelect" class="form-control"><option value="">Select</option>   <option value="0.5 km">Less than 0.5 km</option>   <option value="1 km">1 km</option> <option value="1.5 km">1.5 km</option> <option value="2 km">2 km</option>  <option value="2.5 km">2.5 km</option> <option value="3 km">3 km</option></select>');
   }  else if (selectedValue == "Duration") { 
    $('.change-with-filter').html('<select id="duration" class="form-control"><option value="">select</option><option value="duration1">Duration1</option><option value="duration2">Duration2</option> <option value="duration3">Duration3</option> </select>');
  } 
    

   
   
  
 });
})

 //search catgory

 $(document).on('keyup','#Attraction-Category', function(){	 

   var value = $('#Attraction-Category').val();
   if(value != ""){
     $("#Category_list").css("display", "block");

       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           type:'POST',
           url:  base_url+  'attraction/get_category',
           data: { 'val': value,
           '_token': $('meta[name="csrf-token"]').attr('content')},
        
           success: function(response){
       
             $('#Category_list').html("");
 
        
             response.forEach(function(country) {
                 var listItem = $('<li>').text(country.value);
              //   $('#country-list').fadeIn();
                 $('#Category_list').append(listItem);
             });
          }
       });
     }else{       
       $('#Category_list').append("");    
     }
   
   }) 
  $(document).on('click', '#Category_list li', function() {
      
    var selectedValue = $(this).text();

    $('#Category_list').css("display","none");
   
    $('#Attraction-Category').val('');
   
    var existingSpan = $('.category-value').find('span');
    var valueExists = false;

    existingSpan.each(function() {
      if ($(this).text().trim() === selectedValue) {
        valueExists = true;
        return false;
      }
    });
  
    if (existingSpan.length > 0) {
   
        if (!valueExists) {
                var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
                $('.category-value').append('<span class="margin-l"><button class="btn btn-secondary  margin-top hotel-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
    } else {
  
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.category-value').html('<span><button class="btn btn-secondary margin-top hotel-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    }
  });

  $(document).on('click', '.category-value i.fa-trash', function() {
    $(this).closest('span').remove();
  });
 //search catgory

//search traveler_list

// $(document).on('keyup','#traveler_Types', function(){	 

//   var value = $('#traveler_Types').val();
//   if(value != ""){
//     $("#traveler_list").css("display", "block");

//       $.ajax({
//           headers: {
//               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
//           },
//           type:'POST',
//           url:  base_url+  'attraction/get_traveler_type',
//           data: { 'val': value,
//           '_token': $('meta[name="csrf-token"]').attr('content')},
       
//           success: function(response){
      
//             $('#traveler_list').html("");

       
//             response.forEach(function(country) {
//                 var listItem = $('<li>').text(country.value);
//              //   $('#country-list').fadeIn();
//                 $('#traveler_list').append(listItem);
//             });
//          }
//       });
//     }else{       
//       $('#traveler_list').append("");    
//     }
  
//   }) 
 


 $(document).on('change', '.change-with-filter #travel_type', function() {
  var selectedValue = $(this).val();
 
  var existingSpan = $('.Traveler_Types_value').find('span');
  var valueExists = false;

  existingSpan.each(function() {
    if ($(this).find('button').text().trim() === selectedValue) {
      valueExists = true;
      return false;
    }
  });

  if (existingSpan.length > 0) {
    if (!valueExists) {
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.Traveler_Types_value').append('<span class="margin-l"><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    } 
  } else {
    
    var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
    $('.Traveler_Types_value').html('<span><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
  }
});
 $(document).on('click', '.Traveler_Types_value i.fa-trash', function() {
   $(this).closest('span').remove();
 });
//search travel_type

 


$(document).on('change', '#Attraction_Tags', function() {
  var selectedValue = $(this).val();
 
  var existingSpan = $('.Attraction_Tags_value').find('span');
  var valueExists = false;

  existingSpan.each(function() {
    if ($(this).find('button').text().trim() === selectedValue) {
      valueExists = true;
      return false;
    }
  });

  if (existingSpan.length > 0) {
    if (!valueExists) {
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.Attraction_Tags_value').append('<span class="margin-l"><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    } 
  } else {
    
    var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
    $('.Attraction_Tags_value').html('<span><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
  }
});
 $(document).on('click', '.Attraction_Tags_value i.fa-trash', function() {
   $(this).closest('span').remove();
 });

 //duration

 $(document).on('change', '#duration', function() {
  var selectedValue = $(this).val();
 
  var existingSpan = $('.duration_value').find('span');
  var valueExists = false;

  existingSpan.each(function() {
    if ($(this).find('button').text().trim() === selectedValue) {
      valueExists = true;
      return false;
    }
  });

  if (existingSpan.length > 0) {
    if (!valueExists) {
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.duration_value').append('<span class="margin-l"><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    } 
  } else {
    
    var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
    $('.duration_value').html('<span><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
  }
});
 $(document).on('click', '.duration_value i.fa-trash', function() {
   $(this).closest('span').remove();
 });

 

//add attraction landing

$(document).on('click','#addAttractionLanding', function(){	 
 // $('.loadResult').removeClass('hide');
  var name = $('#name').val();
  var slug = $('#slug').val(); 
  var meta_title = $('#metatitle').val();  
  var meta_desc = $('#metadesc').val();
  var about = $('#about').val();
  var sightid =  $('#sightid').val();

  var nearby = $('.nearby-value').text();

  var category_value = [];
  $('.category-value button').each(function() {
    var buttonText = $(this).text();
    category_value.push(buttonText);
  });

  var star_rating = [];
  $('.star-rating button').each(function() {
    var startext = $(this).text();
    star_rating.push(startext);
  });

  var duration_value = [];
  $('.duration_value button').each(function() {
    var hotemnttext = $(this).text();
    duration_value.push(hotemnttext);
  });
  var Traveler_Types_value = [];
  $('.Traveler_Types_value button').each(function() {
    var hotepricetext = $(this).text();
    Traveler_Types_value.push(hotepricetext);
  });
  var distance_array = [];
  $('.distance button').each(function() {
    var roomtypetext = $(this).text();
    distance_array.push(roomtypetext); 
  });  
  var Attraction_Tags_array = [];
  $('.Attraction_Tags_value button').each(function() {
    var Attraction_Tags = $(this).text();
    Attraction_Tags_array.push(Attraction_Tags); 
  }); 

  
  var nearbytype = $('input[name="near_by"]:checked').val();
 


      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'attraction/store_landing',
          data: { 'name': name,'slug':slug,'meta_title':meta_title,'meta_desc':meta_desc,'about':about,'nearby':nearby,'sightid':sightid,'category_value':category_value,'star_rating':star_rating,'duration_value':duration_value,'Traveler_Types_value':Traveler_Types_value,'distance_array':distance_array,'distance_array':distance_array,'Attraction_Tags_array':Attraction_Tags_array,'nearbytype':nearbytype,
          '_token': $('meta[name="csrf-token"]').attr('content')}, 
          success: function(response){
            $('.loadResult').addClass('hide');
            console.log(response);
           if(response == 1){
            var editUrl =  base_url+ 'attraction/edit_landing/'+ sightid;
            window.location.href = editUrl;
           }else{
              $('#Success').html('<div class="alert alert-danger">Landing page not Added</div>')
           }
     
          //   console.log(response);
          }
      });
  })

  //end add landing

//edit attraction landing

$(document).on('click','.UpdateAttractionLanding', function(){	 
  // $('.loadResult').removeClass('hide');
   var name = $('#name').val();
   var slug = $('#slug').val(); 
   var id = $('#id').val(); 
   var meta_title = $('#metatitle').val();  
   var meta_desc = $('#metadesc').val();
   var about = $('#about').val();
   var sightid =  $('#sightid').val();
   var nearby = $('.nb-value').text();
   var colid = $(this).data('colid');

   var category_value = [];
   $('.category-value button').each(function() {
     var buttonText = $(this).text();
     category_value.push(buttonText);
   });
 
   var star_rating = [];
   $('.star-rating button').each(function() {
     var startext = $(this).text();
     star_rating.push(startext);
   });
 
   var duration_value = [];
   $('.duration_value button').each(function() {
     var hotemnttext = $(this).text();
     duration_value.push(hotemnttext);
   });
   var Traveler_Types_value = [];
   $('.Traveler_Types_value button').each(function() {
     var hotepricetext = $(this).text();
     Traveler_Types_value.push(hotepricetext);
   });
   var distance_array = [];
   $('.distance button').each(function() {
     var roomtypetext = $(this).text();
     distance_array.push(roomtypetext); 
   });  
   var Attraction_Tags_array = [];
   $('.Attraction_Tags_value button').each(function() {
     var Attraction_Tags = $(this).text();
     Attraction_Tags_array.push(Attraction_Tags); 
   }); 
 
   var nearbytype = $('input[name="near_by"]:checked').val();
  
   $("#buttonsContainer-" + colid).addClass("d-none");
   $('.name' + colid).prop('disabled', true);
 
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           type:'POST',
           url: base_url+ 'attraction/update_landing',
           data: { 'name': name,'slug':slug,'meta_title':meta_title,'meta_desc':meta_desc,'about':about,'nearby':nearby,'sightid':sightid,'category_value':category_value,'star_rating':star_rating,'duration_value':duration_value,'Traveler_Types_value':Traveler_Types_value,'distance_array':distance_array,'distance_array':distance_array,'Attraction_Tags_array':Attraction_Tags_array,'nearbytype':nearbytype,'id':id,
           '_token': $('meta[name="csrf-token"]').attr('content')}, 
           success: function(response){
             $('.loadResult').addClass('hide');
             console.log(response);
            if(response == 1){
              $('#Success').html('<div class="alert alert-success">Data updated Successfully</div>')
            }else{
               $('#Success').html('<div class="alert alert-danger">Data not updated </div>')
            }
      
           //   console.log(response);
           }
       });
   })


   function editattlanding(index) {
  
    var originalQuestion = $('.name' + index).data('original-value');
  
   
    $("#buttonsContainer-" + index).removeClass("d-none");


    $('.name' + index).val(originalQuestion).prop('disabled', false);
    $('#questionmsg-'+index).text('');
  }

  function editattlanding(id) {
    
    $('.name' + id).prop('disabled', false);
    // alert(value)
    $("#buttonsContainer-" + id).removeClass("d-none");

  }

  function cancelAttLanding(index) {
      var originalQuestion = $('.name' + index).data('original-value');
    $("#buttonsContainer-" + index).addClass("d-none");


    $('.name' + index).val(originalQuestion).prop('disabled', true);

    $('#questionmsg-'+index).text('');
  }

 
  $(document).on('click','#att-hidepage', function(){	 
    var landing = $(this).data('id'); 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url:  base_url+ 'attraction/hidepage',
            data: { 'landing': landing,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
           //  alert(response);
              $('#Success').html('<div class="alert alert-success">Page Hide Successfully.</div>');              
             setTimeout(function() {
              $('#Success').html('');
          }, 30000);
            }
        });
    }) 
    
    $(document).on('click','#delete-att-landing-page', function(){	 
      var result = confirm("Are you sure you want to delete this Page?");
      if (result) {
         var landing = $(this).data('id'); 
 
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'post',
                url:  base_url+ 'attraction/delete_landing',
                data: { 'landing': landing,
                '_token': $('meta[name="csrf-token"]').attr('content')},
                success: function(response){
                 
                  if(response == 1){
                    location.reload();
                    $('#Success').html('<div class="alert alert-success">Data Deleted Successfully</div>')
                  }else{
                     $('#Success').html('<div class="alert alert-danger">Data not Deleted. </div>')
                  }
                }
            });
        } 
      }) 
   //end edit landing

 //start manage landing page 

   $(document).ready(function() {
 
    $('#attractionBox').show();
  
    
    $('input[name="page_type"]').on('change', function() {
      $('.getfilterDataat').html('');
      $('.getfilterData').html('');
        var selectedValue = $(this).val();
        $('.searchBox').hide();
     
        $('#' + selectedValue.toLowerCase() + 'Box').show();
    });
});

$(document).on('click', '.searchlandingpage', function() {
  $('.loadResult').removeClass('hide');
  $('.getfilterDataat').html('');
  var container = $(this).closest('.searchBox'); // Find the closest parent with the class "searchBox"
  var value = container.find('.searchlandingvalue').val(); // Get the value from the input within the container
  var type = $(this).data('type');

  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'landing/search_landing_page',
      data: {
          'value': value,
          'type': type,
          '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
          $('.loadResult').addClass('hide');
          $('.getfilterDataat').html(response);
      }
  });
});


$(document).on('click', '.pagination a', function(e) {
    e.preventDefault();
    let page = $(this).attr('href').split('page=')[1];

    // Always get `type` and `value` from hidden inputs
    let type = $('#searchType').val();
    let value = $('#searchValue').val();

    console.log('Pagination Triggered - Page:', page, 'Type:', type, 'Value:', value);

    fetchFilteredData(page, type, value);
});

function fetchFilteredData(page, type, value) {
    // Log the received parameters and their sources
    console.log('Before Fallback - Page:', page, 'Type:', type, 'Value:', value);

    // Fallback to hidden inputs if type or value is undefined
    type = type || $('#searchType').val();
    value = value || $('#searchValue').val();

    console.log('After Fallback - Page:', page, 'Type:', type, 'Value:', value);

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'landing/search_landing_page?page=' + page,
        data: {
            'value': value,
            'type': type,
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('.loadResult').addClass('hide');
            $('.getfilterDataat').html(response);

            // Ensure hidden inputs are updated with the latest values
            $('#searchType').val(type);
            $('#searchValue').val(value);
        },
        error: function(xhr, status, error) {
            console.error('Error:', xhr.responseText || error);
        }
    });
}

$(document).on('click', '.searchlandingtype', function() {
  $('.loadResult').removeClass('hide');
  $('.getfilterDataat').html('');
var container = $(this).closest('.searchBox'); // Find the closest parent with the class "searchBox"
var value = container.find('.searchlandingvalue').val(); // Get the value from the input within the container
//var type = $(this).data('type');
var type = $('input[name="page_type"]:checked').val(); // Get the value of the checked radio button

$.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: base_url + 'landing/search_add_landing_page',
    data: {
        'value': value,
        'type': type,
        '_token': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
        $('.loadResult').addClass('hide');
        $('.getfilterDataat').html(response);
    }
});
});



//experience landing

$(document).ready(function() {
  $(document).on('change', '.exp-select-filters', function() {
   var selectedValue = $(this).val();

   if (selectedValue == "Category") {

     $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Attraction-Category" class="Hotel-Amenities form-control rounded-3" placeholder="Search Attraction Category"><i class="ti ti-search"></i><div id="Category_list"></div>');

   }  else if (selectedValue == "Hotel Class Ratings") {    
     $('.change-with-filter').html('<select id="starRatingSelect" class="form-control"><option>Select Rating</option><option value="1 Star">1 Star</option><option value="2 Star">2 Stars</option><option value="3 Star">3 Star</option><option value="4 Star">4 Stars</option><option value="5 Star">5 Stars</option></select>');
   } else if (selectedValue == "Languages") {
     $('.change-with-filter').html('<div class="form-search form-search-icon-right change-field mb-3"><input type="text" name="" id="Languages" class="Languages form-control rounded-3" placeholder="Search Languages"><i class="ti ti-search"></i><div id="Languages_list"></div>');
   } else if (selectedValue == "Mobile Ticket") { 
     $('.change-with-filter').html('<select id="Mobile_Ticket" class="form-control"><option>Select Mobile Ticket</option><option value="Available">Available</option></select>');
   }  else if (selectedValue == "Duration") { 
     $('.change-with-filter').html('<select id="duration" class="form-control"><option value="">select</option><option value="Full Day Tours">Full Day Tours</option><option value="2 Days Tours">2 Days Tours</option> <option value="3 Days Tours">3 Days Tours</option> <option value="4 Days Tours">4 Days Tours</option> <option value="5 Days Tours">5 Days Tours</option> <option value="Weekend Tours">Weekend Tours</option><option value=" Half-day Tours"> Half-day Tours</option><option value="Multi-Day Tours">Multi-Day Tours</option><option value="Night Tours">Night Tours</option><option value="1 hour Tour">1 hour Tour</option><option value="Upto 3 hours Tours">Upto 3 hours Tours</option> </select>');
   }  else if (selectedValue == "Experience Tags") { 
    $('.change-with-filter').html('<select id="Experience_Tags" class="form-control"><option value="">Select Experience Tags</option><option value="Free Cancellation">Free Cancellation</option><option value="Adults Only">Adults Only</option><option value="Kids-Friendly">Kids-Friendly</option> <option value="Pickup/Drop Included">Pickup/Drop Included</option> <option value="Food/Drinks Included">Food/Drinks Included</option> <option value="Wheelchair Accessible">Wheelchair Accessible</option><option value="Stroller Accessible">Stroller Accessible</option><option value="Service Animals Allowed">Service Animals Allowed</option><option value="Entry Fee Included">Entry Fee Included</option> </select>');
  }
    
     
  
 });
})

 //search catgory

 $(document).on('keyup','#Languages', function(){	 

   var value = $('#Languages').val();
   if(value != ""){
     $("#Languages_list").css("display", "block");

       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           type:'POST',
           url:  base_url+  'landing/search_language',
           data: { 'val': value,
           '_token': $('meta[name="csrf-token"]').attr('content')},
        
           success: function(response){
       
             $('#Languages_list').html("");
 
        
             response.forEach(function(country) {
                 var listItem = $('<li>').text(country.value);
              //   $('#country-list').fadeIn();
                 $('#Languages_list').append(listItem);
             });
          }
       });
     }else{       
       $('#Languages_list').append("");    
     }
   
   }) 
  $(document).on('click', '#Languages_list li', function() {
      
    var selectedValue = $(this).text();

    $('#Languages_list').css("display","none");
   
    $('#Attraction-Category').val('');
   
    var existingSpan = $('.Languages_value').find('span');
    var valueExists = false;

    existingSpan.each(function() {
      if ($(this).text().trim() === selectedValue) {
        valueExists = true;
        return false;
      }
    });
  
    if (existingSpan.length > 0) {
   
        if (!valueExists) {
                var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
                $('.Languages_value').append('<span class="margin-l"><button class="btn btn-secondary  margin-top hotel-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
        }
    } else {
  
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.Languages_value').html('<span><button class="btn btn-secondary margin-top hotel-mnt' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    }
  });

  $(document).on('click', '.Languages_value i.fa-trash', function() {
    $(this).closest('span').remove();
  });

  //mobile ticket
  $(document).on('change', '#Mobile_Ticket', function() {
    var selectedValue = $(this).val();

    var existingSpan = $('.Mobile_Ticket_value').find('span');
    var valueExists = false;
  
    existingSpan.each(function() {
      if ($(this).find('button').val().trim() === selectedValue) {
        valueExists = true;
        return false;
      }
    });
  
    if (existingSpan.length > 0) {
      if (!valueExists) {
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.Mobile_Ticket_value').append('<span class="margin-l"><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      } 
    } else {
      
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.Mobile_Ticket_value').html('<span><button class="btn btn-secondary ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    }
  });
   $(document).on('click', '.Mobile_Ticket_value i.fa-trash', function() {
     $(this).closest('span').remove();
   });
  
   

   //Experience Tags 
  $(document).on('change', '#Experience_Tags', function() {
    var selectedValue = $(this).val();

    var existingSpan = $('.Experience_Tags_val').find('span');
    var valueExists = false;
  
    existingSpan.each(function() {
      if ($(this).find('button').text().trim() === selectedValue) {
        valueExists = true;
        return false;
      }
    });
  
    if (existingSpan.length > 0) {
      if (!valueExists) {
        var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
        $('.Experience_Tags_val').append('<span class="margin-l"><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
      } 
    } else {
      
      var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
      $('.Experience_Tags_val').html('<span><button class="btn btn-secondary margin-top ' + dynamicClass + '">' + selectedValue + '</button><i class="fa fa-trash ml-3" id="'+dynamicClass+'"></i></span>');
    }
  });
   $(document).on('click', '.Experience_Tags_val i.fa-trash', function() {
     $(this).closest('span').remove();
   });
//enc exp landing

//add experience landing
$(document).on('click','#add_exp_landing', function(){	 
   $('.loadResult').removeClass('hide');
   var name = $('#name').val();
   var slug = $('#slug').val();
   
   if (name === '') {
    
     $('.loadResult').addClass('hide');
     $(".errorname").show();
     
     return false;
   }

   $(".errorname").hide();
   if (slug === '') {
    $('.loadResult').addClass('hide');
    $(".errorslug").show();
    
    return false;
  }

   $(".errorslug").hide();
   var id = $('#id').val(); 
   var meta_title = $('#metatitle').val();  
   var meta_desc = $('#metadesc').val();
   var about = $('#about').val();
   var exp_id =  $('#exp_id').val();
   var nearby = $('.value').text();
   var colid = $(this).data('colid');

   var category_value = [];
   $('.category-value button').each(function() {
     var buttonText = $(this).text();
     category_value.push(buttonText);
   });
 
   var star_rating = [];
   $('.star-rating button').each(function() {
     var startext = $(this).text();
     star_rating.push(startext);
   });
 
   var duration_value = [];
   $('.duration_value button').each(function() {
     var hotemnttext = $(this).text();
     duration_value.push(hotemnttext);
   });
   var Languages_value = [];
   $('.Languages_value button').each(function() {
     var Languages = $(this).text();
     Languages_value.push(Languages);
   });
   var Mobile_Ticket_value  = [];
   $('.Mobile_Ticket_value  button').each(function() {
     var Mobile_Ticket = $(this).text();
     Mobile_Ticket_value .push(Mobile_Ticket); 
   });  
   var Experience_Tags_val = [];
   $('.Experience_Tags_val button').each(function() {
     var Experience_Tags = $(this).text();
     Experience_Tags_val.push(Experience_Tags); 
   }); 
 
   var nearbytype = $('input[name="near_by"]:checked').val();
  
   $("#buttonsContainer-" + colid).addClass("d-none");
   $('.name' + colid).prop('disabled', true);
 
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           type:'POST',
           url: base_url+ 'landing/store_exp_landing',
           data: { 'name': name,'slug':slug,'meta_title':meta_title,'meta_desc':meta_desc,'about':about,'nearby':nearby,'exp_id':exp_id,'category_value':category_value,'star_rating':star_rating,'duration_value':duration_value,'Languages_value':Languages_value,'Mobile_Ticket_value':Mobile_Ticket_value,'Experience_Tags_val':Experience_Tags_val,'nearbytype':nearbytype,
           '_token': $('meta[name="csrf-token"]').attr('content')}, 
           success: function(response){
            console.log(response)
            
            window.location.href = base_url+ 'landing';
             $('.loadResult').addClass('hide');
             console.log(response);
            if(response == 1){
              $('#Success').html('<div class="alert alert-success">Data updated Successfully</div>')
            }else{
               $('#Success').html('<div class="alert alert-danger">Data not updated </div>')
            }
      
           //   console.log(response);
           }
       });
   })
   //end experience landing
//Update experience landing
$(document).on('click','.updateexplanding', function(){	 
   $('.loadResult').removeClass('hide');
   var name = $('#name').val();
   var slug = $('#slug').val();
   
   if (name === '') {
    
     $('.loadResult').addClass('hide');
     $(".errorname").show();
     
     return false;
   }

   $(".errorname").hide();
   if (slug === '') {
    $('.loadResult').addClass('hide');
    $(".errorslug").show();
    
    return false;
  }
  
   $(".errorslug").hide();
   var id = $('#id').val(); 
   var meta_title = $('#metatitle').val();  
   var meta_desc = $('#metadesc').val();
   var about = $('#about').val();
   var exp_id =  $('#exp_id').val();
   var nearby = $('.value').text();
   var colid = $(this).data('colid');

   var category_value = [];
   $('.category-value button').each(function() {
     var buttonText = $(this).text();
     category_value.push(buttonText);
   });
 
   var star_rating = [];
   $('.star-rating button').each(function() {
     var startext = $(this).text();
     star_rating.push(startext);
   });
 
   var duration_value = [];
   $('.duration_value button').each(function() {
     var hotemnttext = $(this).text();
     duration_value.push(hotemnttext);
   });
   var Languages_value = [];
   $('.Languages_value button').each(function() {
     var Languages = $(this).text();
     Languages_value.push(Languages);
   });
   var Mobile_Ticket_value  = [];
   $('.Mobile_Ticket_value  button').each(function() {
     var Mobile_Ticket = $(this).text();
     Mobile_Ticket_value .push(Mobile_Ticket); 
   });  
   var Experience_Tags_val = [];
   $('.Experience_Tags_val button').each(function() {
     var Experience_Tags = $(this).text();
     Experience_Tags_val.push(Experience_Tags); 
   }); 
 
   var nearbytype = $('input[name="near_by"]:checked').val();
  
   $("#buttonsContainer-" + colid).addClass("d-none");
   $('.name' + colid).prop('disabled', true);
 
       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           type:'POST',
           url: base_url+ 'landing/update_exp_landing',
           data: { 'name': name,'slug':slug,'meta_title':meta_title,'meta_desc':meta_desc,'about':about,'nearby':nearby,'exp_id':exp_id,'category_value':category_value,'star_rating':star_rating,'duration_value':duration_value,'Languages_value':Languages_value,'Mobile_Ticket_value':Mobile_Ticket_value,'Experience_Tags_val':Experience_Tags_val,'nearbytype':nearbytype,'id':id,
           '_token': $('meta[name="csrf-token"]').attr('content')}, 
           success: function(response){
            console.log(response)
            
           // window.location.href = base_url+ 'landing';
             $('.loadResult').addClass('hide');
             console.log(response);
            if(response == 1){
              $('#Success').html('<div class="alert alert-success">Data updated Successfully</div>')
            }else{
               $('#Success').html('<div class="alert alert-danger">Data not updated </div>')
            }
      
           //   console.log(response);
           }
       });
   })
   //end experience landing

   function editexplanding(index) {

    var originalQuestion = $('.name' + index).data('original-value');
  
   
    $("#buttonsContainer-" + index).removeClass("d-none");
  
  
    $('.name' + index).val(originalQuestion).prop('disabled', false);
    $('#questionmsg-'+index).text('');
  }

   function cancelexpLanding(index) {
    var originalQuestion = $('.name' + index).data('original-value');
    $("#buttonsContainer-" + index).addClass("d-none");


    $('.name' + index).val(originalQuestion).prop('disabled', true);

    $('#questionmsg-'+index).text('');
}


// $(document).on('click', '.nb-value i.fa-trash', function() {
//   $(this).closest('span').remove();
// });



$(document).on('click','#exp-hidepage', function(){	 
  var landing = $(this).data('id'); 
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url:  base_url+ 'landing/hidepage_exp',
          data: { 'landing': landing,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
         //  alert(response);
            $('#Success').html('<div class="alert alert-success">Page Hide Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);
          }
      });
  }) 
  
  $(document).on('click','#delete-exp-landing-page', function(){	 
    var result = confirm("Are you sure you want to delete this Page?");
    if (result) {
       var landing = $(this).data('id'); 

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url+ 'landing/delete_landing_exp',
              data: { 'landing': landing,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
               
                if(response == 1){
                  location.reload();
                  $('#Success').html('<div class="alert alert-success">Data Deleted Successfully</div>')
                }else{
                   $('#Success').html('<div class="alert alert-danger">Data not Deleted. </div>')
                }
              }
          });
      } 
    }) 

//end landing page 













//start Experince

$(document).on('click','#search_experince', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#search_input').val();

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+  'experience/search_experince',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterDataat').html(response) 
              
           
          }
      });
  })



  $(document).on('keyup','#searcLanguages', function(){	 

    var value = $('#searcLanguages').val();
   
    if(value != ""){
      $("#Languageslist").css("display", "block");
 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url:  base_url+  'experience/search_language',
            data: { 'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
         
            success: function(response){
        
              $('#Languageslist').html("");
  
         
              response.forEach(function(country) {
                  var listItem = $('<li>').text(country.value);
               //   $('#country-list').fadeIn();
                  $('#Languageslist').append(listItem);
              });
           }
        });
      }else{       
        $('#Languageslist').append("");    
      }
    
    }) 
   $(document).on('click', '#Languageslist li', function() {
       
     var selectedValue = $(this).text();
 
     $('#Languageslist').css("display","none");
    
     $('#searcLanguages').val('');
    
     var existingSpan = $('.Language_value').find('span');
     var valueExists = false;
 
     existingSpan.each(function() {
       if ($(this).text().trim() === selectedValue) {
         valueExists = true;
         return false;
       }
     });
   
     if (existingSpan.length > 0) {
    
         if (!valueExists) {
                 var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
                 $('.Language_value').append('<span class="margin-l"><input type="hidden" value="' + selectedValue + '" name="language[]"><button class="btn btn-secondary  margin-top">' + selectedValue + '</button><i class="fa fa-trash ml-3" ></i></span>');
         }
     } else {
   
       var dynamicClass = 'btn-' + selectedValue.toLowerCase().replace(/ /g, '-');
       $('.Language_value').html('<span><input type="hidden" value="' + selectedValue + '" name="language[]"><button class="btn btn-secondary margin-top ">' + selectedValue + '</button><i class="fa fa-trash ml-3" ></i></span>');
     }
   });
 
   $(document).on('click', '.Language_value i.fa-trash', function() {
     $(this).closest('span').remove();
   });
//end experince


$(document).on('keyup','#searchlocationcity', function(){	 
   
  var value = $('#searchlocationcity').val();

  if(value != ""){
    $("#citylisth").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'experience/searchCity',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#citylisth').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value + ',' + country.country);
             //   $('#country-list').fadeIn();
                $('#citylisth').append(listItem);
            });
         }
      });
    }else{
   
      $('#citylisth').html("");
   //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function(){
      var text = $(this).text();
      var parts = text.split(",");
      var cityName = parts[0].trim();
      var country = parts[1].trim();
      
      
      $("#searchlocationcity").val(cityName);
      $("#country").val(country);
      $('#citylisth').fadeOut();

    });
  }) 



  function editexp(index) {

 
    $("#buttonsContainer-" + index).removeClass("d-none");
  
  
    $('.name' + index).prop('disabled', false);
   
  }

   function cancelexp(index) {
    //var originalQuestion = $('.name' + index).data('original-value');
    $("#buttonsContainer-" + index).addClass("d-none");


     $('.name' + index).prop('disabled', true);

    // $('#questionmsg-'+index).text('');
}


$(document).on('click','.updateexp', function(){	
 
   $('.loadResult').removeClass('hide');
  var name = $('input[name="name"]').val();
  var slug = $('input[name="slug"]').val();

  if (name === '') {
   
    $('.loadResult').addClass('hide');
    $(".errorname").show();
    
    return false;
  }

  $(".errorname").hide();
  if (slug === '') {
   $('.loadResult').addClass('hide');
   $(".errorslug").show();
   
   return false;
 }
 
  $(".errorslug").hide();
 
  
  var meta_title = $('input[name="MetaTagTitle"]').val();    

  var meta_desc = $('textarea[name="MetaTagDescription"]').val();
  var about = $('textarea[name="about"]').val();
  var exp_id =  $('#exp_id').val();
  var addressline1 = $('textarea[name="addressline1"]').val();
  var addressline2 = $('input[name="addressline2"]').val();
  var neighborhood = $('textarea[name="neighborhood"]').val();  
  var ctname = $('input[name="ctname"]').val();
  var county = $('input[name="county"]').val();
  var pincode = $('input[name="pincode"]').val();
  var Latitude = $('input[name="Latitude"]').val();
  var Longitude = $('input[name="Longitude"]').val();
  var pincode = $('input[name="pincode"]').val();
  var mobile_tickets =  $('select[name="mobile_tickets"]').val();
  var Confirmation = $('textarea[name="Confirmation"]').val();  
  var duration = $('input[name="duration"]').val();
  var free_cancellation = $('input[name="free_cancellation"]').val();
  var pickup = $('select[name="pickup"]').val();
  var bookingfee = $('select[name="bookingfee"]').val();
  var language = [];
  $('input[name="language[]"]').each(function() {
      language.push($(this).val());
  });


  var website = $('input[name="website"]').val();
  var phone= $('input[name="phone"]').val();
  var email= $('input[name="email"]').val();
  var adult_price= $('input[name="adult_price"]').val();
  var price_minor= $('input[name="price_minor"]').val();
  var Inclusions = $('textarea[name="Inclusions"]').val();
  var exclusions = $('textarea[name="exclusions"]').val(); 
  var departure_point = $('input[name="departure_point"]').val();
  var return_point = $('input[name="return_point"]').val();
  var additionaldetails = $('textarea[name="additionaldetails"]').val();
  
  var colid = $(this).data('colid');

  $("#buttonsContainer-" + colid).addClass("d-none");
  $('.name' + colid).prop('disabled', true);

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'experience/update_experience',
          data: { 'name': name,'slug':slug,'MetaTagTitle':meta_title,'meta_desc':meta_desc,'about':about,'addressline1':addressline1,'addressline2':addressline2,'exp_id':exp_id,'pickup':pickup,'addressline2':addressline2,'neighborhood':neighborhood,'ctname':ctname,'pincode':pincode,'Latitude':Latitude,'Longitude':Longitude,'pincode':pincode,'bookingfee':bookingfee,'duration':duration,'language':language,'free_cancellation':free_cancellation,'Confirmation':Confirmation,'mobile_tickets':mobile_tickets,'additionaldetails':additionaldetails,'return_point':return_point,'departure_point':departure_point,'exclusions':exclusions,'Inclusions':Inclusions,'price_minor':price_minor,'adult_price':adult_price,'email':email,'phone':phone,'website':website,
          '_token': $('meta[name="csrf-token"]').attr('content')}, 
          success: function(response){
           console.log(response)
           
          // window.location.href = base_url+ 'landing';
            $('.loadResult').addClass('hide');
            console.log(response);
           if(response == 1){
             $('#Success').html('<div class="alert alert-success">Data updated Successfully</div>')
           }else{
              $('#Success').html('<div class="alert alert-danger">Data not updated </div>')
           }
     
          //   console.log(response);
          }
      });
  })

//faq

$(document).ready(function() {
  $('#saveexpfaq').click(function() {
    var question = $('#addques').val().trim();

    if (question === "") {
      $('#errorcustfaq').text('Question is Required.').css('color', 'red');
      setTimeout(function(){
        $('#errorcustfaq').text('');
      },30000);
      return;
    }
    $('#errorcustfaq').text('');
    var ExperienceId = $('#saveexpfaq').data('id');
 
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url: base_url+ 'experience/add_exp_faq', 
      data: { 'checkboxText': question,'ExperienceId':ExperienceId,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('.getupdatedfaq').html(response);
        $('#staticBackdrop').modal('hide');
        var question = $('#addques').val('')
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 30000);
      }
  });

  });
});


$(document).ready(function() {
  $('#savelistfaq').click(function() {
    var checkedCheckboxes = $('input[type="checkbox"]:checked');
  
    if (checkedCheckboxes.length === 0) {
      $('#errorcheck').text('Please Select a question').css('color', 'red');
      setTimeout(function(){
        $('#errorcheck').text('');
      }, 3000);
      return;
    }
    
    var checkboxText = '';
    checkedCheckboxes.each(function() {
      checkboxText += $(this).next('span').text() + '\n';
    });
    
    var ExperienceId = $(this).data('id');

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: base_url + 'experience/add_exp_faq', 
      data: {
        'checkboxText': checkboxText,
        'ExperienceId': ExperienceId,
        '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('#staticBackdrop').modal('hide');
        $('.getupdatedfaq').html(response);
        $('.checkbox').prop('checked', false);
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
        setTimeout(function() {
          $('#Success').html('');
        }, 3000);
      }
    });
  });
});

      
    function edit_expfaq(id) {
      var textarea = $("#reviewField-" + id);
      textarea.prop("disabled", false);
      var value = $('#edit-btn').attr('value');
      // alert(value)
      $("#buttonsContainer-" + id).removeClass("d-none");

      $("#editBtn-" + id).addClass("d-none");

      $('#question' + id).prop('disabled', false);
      $('#Answer' + id).prop('disabled', false);


    }
    function cancel_expfaq(index) {
      var originalQuestion = $('#question' + index).data('original-value');
      var originalAnswer = $('#Answer' + index).data('original-value');
      $("#buttonsContainer-" + index).addClass("d-none");
      $('#question' + index).val(originalQuestion).prop('disabled', true);
      $('#Answer' + index).val(originalAnswer).prop('disabled', true);

      $('#questionmsg-'+index).text('');
      $('#answer-'+index).text('');
    }

    $(document).on('click','.updateexpfaq', function(){	 
      $('.loadResult').removeClass('hide');
      var faqId = $(this).data('id');
      var questionTextarea =  $('#question' + faqId).val(); 
      var answerTextarea =   $('#Answer' + faqId).val();
    

      var isValid = true;

      if (questionTextarea == '') {
        $('.loadResult').addClass('hide');
        $('#questionmsg-'+faqId).text('Question is required.').css('color', 'red');
        isValid = false;
     } else {
       $('#questionmsg-'+faqId).text('');
     }
  
     if (!isValid) {
       return;
     }
     $("#buttonsContainer-" + faqId).addClass("d-none");
     $('#question' + faqId).prop('disabled', true);
     $('#Answer' + faqId).prop('disabled', true);
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'post',
              url:  base_url+ 'experience/update_exp_faq',
              data: { 'question': questionTextarea,'faqId':faqId,'answer':answerTextarea,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                $('#Success').html('<div class="alert alert-success">Data updated Successfully.</div>');              
               setTimeout(function() {
                $('#Success').html('');
            }, 30000);
              }
          });
      }) 
//end faq
// add itenery 




$(document).ready(function() {
  var fieldCount = 1; 

  $('#addnewitinerary').on('click', function() {
   
    var closestDayElement = $('.dayvalue').last();
    var dayValue = closestDayElement.attr('value');

    var newday = $('#swich_day').val();

    if (newday == 1) {
      fieldCount = 0;
    }
    fieldCount++;


    var html = `
 
      <div class="col-md-6 inp all-inp">
      <strong>Itinerary Point ${fieldCount}</strong>
      <div class="form-search form-search-icon-right">
        <input type="text" name="Itinerary[][${dayValue}]" class="form-control rounded-3 searchAttraction"
          placeholder="Search an attraction" required>
        <i class="ti ti-search"></i>
      </div>
      <span class="att_list"></span>
    </div>
   
    `;

    $('.inp:last').after(html);
    $('#swich_day').val('0');
  });
});





$(document).ready(function() {
  var fieldCount = 2;
  var isAddingDay = false;

  $('#addday').on('click', function() {
    if (isAddingDay) {
      return; 
    }

    isAddingDay = true;
   
    var html = `
    <input type="hidden" value="${fieldCount}" class="dayvalue">
    <h5 class="inp mt-3 day" id="day${fieldCount}">Day ${fieldCount}</h5>
    
    
    `;

    $('#swich_day').val('1')
    var lastInp = $('.inp:last');

    if (!lastInp.is('.day')) {
      lastInp.after(html);
      fieldCount++;
    }

    isAddingDay = false;
  });
});


$(document).ready(function() {
  $(".itenerytype").change(function() {
    if ($("#multiDayRadio").prop("checked")) {
      $("#addday").removeClass('d-none');
      $("#day1").removeClass('d-none');
      $('#0-day').val('1');
      var searchInput = $('.searchAttraction');     
      searchInput.attr('name', 'Itinerary[][1]');
    } else {

      $(".day").addClass('d-none');
      var searchInputs = $('.searchAttraction');
      $(".input-value").each(function() {
        $(this).find(".all-inp:not(:first)").remove();
    });
      searchInputs.attr('name', 'Itinerary[][0]');

    }
  });
});
// end add itenery 

$(document).ready(function() {
  $(".itenerytype-edit").change(function() {
    if ($("#multiDayRadio").prop("checked")) {
      $("#addday").removeClass('d-none');
      $("#day1").removeClass('d-none');
      $('#0-day').val('1');
      var searchInput = $('.searchAttraction');     
      searchInput.attr('name', 'Itinerary[][1]');
    } else {

     $(".day").addClass('d-none');
       var searchInputs = $('.searchAttraction');
     
       searchInputs.attr('name', 'Itinerary[][0]');

     
      
       searchInputs.each(function(index) {
        var itineraryPoint = index + 1; // Adjust index to start from 1
        $('.Itinerary_Point-' + itineraryPoint).text('Itinerary Point ' + itineraryPoint);
      });

    }
  });
});



$(document).ready(function() {
  var fieldCount = 2;
  var isAddingDay = false;

  $('#adddayedit').on('click', function() {
    if (isAddingDay) {
      return; 
    }

    isAddingDay = true;

    var lastday= $('.day:last');
   
    var dayText = lastday.text();
    var currentDayNumber = parseInt(dayText.match(/\d+/)[0]); 
    var nextDayNumber = currentDayNumber + 1; // Increment the numeric part


    var html = `
    <input type="hidden" value="${nextDayNumber}" class="dayvalue">
    <h5 class="inp mt-3 day" id="day${nextDayNumber}">Day ${nextDayNumber}</h5>
    
    
    `;

    $('#swich_day').val('1')
    var lastInp = $('.inp:last');

    if (!lastInp.is('.day')) {
      lastInp.after(html);
      nextDayNumber++;
    }

    isAddingDay = false;
  });
});




$(document).ready(function() {
  var searchTimer;

  $(document).on('keyup', '.searchAttraction', function() {
    var $this = $(this);
    var value = $this.val();
    var attList = $this.closest(".inp").find(".att_list");

    // Clear previous timer
    clearTimeout(searchTimer);

    if (value !== "") {
      // Set new timer for 500ms delay
      searchTimer = setTimeout(function() {
        attList.css("display", "block");
        
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: base_url + 'attraction/search_attr',
          data: {
            'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')
          },
          success: function(response) {
            attList.html("");
            
            response.forEach(function(country) {
              // Add data-id to the li element
              var listItem = $('<li>')
                .text(country.value)
                .attr('data-id', country.id);
              attList.append(listItem);
            });
          }
        });
      }, 500); // 500ms delay
    } else {
      attList.html("");
    }
  });

  // Update click handler to use data-id
  $(document).on('click', ".att_list li", function() {
    var searchText = $(this).text();
    var attractionId = $(this).data('id');
    var searchInput = $(this).closest(".inp").find(".searchAttraction");
    
    searchInput
      .val(searchText)
      .attr('data-id', attractionId); // Store ID on input
    
    $(this).closest(".att_list").fadeOut();
  });
});

// ... existing code ...



function edititn(index) {

 
  $(".buttonsContainer-" + index).removeClass("d-none");


  $('.name'+ index).prop('disabled', false);
 
}
function cancelitn(id) {
  var originalQuestion = $('.name' + id).data('original-value');
  $(".buttonsContainer-" + id).addClass("d-none");
  $('.name' + id).val(originalQuestion).prop('disabled', true);

  $('.name' + id).closest(".input-value").find(".att_list").fadeOut();

}
 
 
$(document).on('click', '.updateItinery', function() {
  var id = $(this).data('id');
  var expid = $('#exp_id').val();
  var value = $('.name'+id).val();

    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'experience/update_itinerary',
      data: {
        'value': value,'id':id,'exp_id':expid,
        '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
      //  alert(response)
        $(".buttonsContainer-" + id).addClass("d-none");
        $('.name' + id).prop('disabled', true);
      }
    });

});

//add new in update

$(document).on('click', '.addnewItinery', function() {
  $('.loadResult').removeClass('hide');
  var expid = $('#exp_id').val();

  var dataArray = {};

  $('input[name^="Itinerarynew[]"]').each(function() {
      var day = $(this).data('day');
      var value = $(this).val();
      dataArray[value] = day;
 
  });


    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'experience/additinerary',
      data: {
        'dataArray': dataArray,'exp_id':expid,
        '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('.loadResult').addClass('hide');
        location.reload();
      }
    });

});



$(document).ready(function() {
  var fieldCount = 1; 

  $('#addnewitinerarye').on('click', function() {
   
    var closestDayElement = $('.dayvalue').last();
    var dayValue = closestDayElement.attr('value');

    var newday = $('#swich_day').val();

    $('.save-iten').removeClass('d-none');

    if (newday == 1) {
      fieldCount = 0;
    }
    fieldCount++;

    var lastcout = $('.inp:last').find('strong').text();
    var restValue = lastcout.replace('Itinerary Point', '');
    restValue++;
    var html = `
    
      <div class="col-md-6 inp all-inp mt-3">
      <strong>Itinerary Point ${restValue}</strong>
      <div class="form-search form-search-icon-right">
        <input type="text" name="Itinerarynew[]" data-day="${dayValue}" class="form-control rounded-3 searchAttraction"
          placeholder="Search an attraction" required>
        <i class="ti ti-search"></i>
      </div>
      <span class="att_list"></span>
    </div>
   
    `;

    $('.inp:last').after(html);
    $('#swich_day').val('0');
  });
});





//start review

function editexpReview(id) {
  var textarea = $("#reviewField-" + id);
  textarea.prop("disabled", false);
  var value = $('#edit-btn').attr('value');
  // alert(value)
  // $("#buttonsContainer-" + id).removeClass("d-none");
  $("#editBtn-" + id).addClass("d-none");
  
  if (value == 1) {
    $("#buttonsContainer-" + id + "-2").removeClass("d-none");
    $("#buttonsContainer-" + id + "-5").removeClass("d-none");
  } else if (value == 2) {
    $("#buttonsContainer-" + id + "-1").removeClass("d-none");
    $("#buttonsContainer-" + id + "-5").removeClass("d-none");
  } else if (value == 3) {
    $("#buttonsContainer-" + id + "-3").removeClass("d-none");
  } else {
    $("#buttonsContainer-" + id + "-2").removeClass("d-none");
    $("#buttonsContainer-" + id + "-1").removeClass("d-none");
    $("#buttonsContainer-" + id + "-5").removeClass("d-none");
  }


}
function expReviewAprove(id,button) {
  //$('.loadResult').removeClass('hide');
  var fieldValue  = $(button).val();
  var expid = $('#expid').val();
  var id = id;

  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type:'POST',
    url: base_url + 'experience/update_expreview',
    data: { 'value': fieldValue,'id':id,'expid':expid,
    '_token': $('meta[name="csrf-token"]').attr('content')},
    success: function(response){
      $('.loadResult').addClass('hide');
        
        $("#reviewField-" + id).prop("disabled", true);
        $("#buttonsContainer-" + id).addClass("d-none");
        $("#buttonsContainer-" + id + "-0").addClass("d-none");
        $("#buttonsContainer-" + id + "-1").addClass("d-none");
        $("#buttonsContainer-" + id + "-2").addClass("d-none");
        $("#buttonsContainer-" + id + "-5").addClass("d-none");
        $("#editBtn-" + id).removeClass("d-none");

        $('.list-container').html(response);

        $('.exp-review-option').removeClass('sel-option')
        $('.exp-review-option').css({
          'font-weight': '',
          'text-decoration': ''
        });
        $('.opt-' + fieldValue).css({
          'font-weight': 'bold',
          'text-decoration': 'underline'
        });
        $('.opt-' + fieldValue).addClass('sel-option');
    }
});


}

$(document).on('click','.exp-review-option', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $(this).data('value');

  $('.exp-review-option').removeClass('sel-option')
  $('.exp-review-option').css({
    'font-weight': '',
    'text-decoration': ''
  });
  $('.opt-' + value).css({
    'font-weight': 'bold',
    'text-decoration': 'underline'
  });
  $('.opt-' + value).addClass('sel-option');
var id = $('#expid').val();

  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url: base_url+ 'experience/ftrexprewview',
      data: { 'val': value,'id':id,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('.loadResult').addClass('hide');      
        $('.list-container').html(response);
          
        
      } 
  });
}) 



$(document).on('change','#sort_exp_review', function(){	 
  $('.loadResult').removeClass('hide');
  var filter_option = $('.sel-option').data('value');
  var value = $('#sort_exp_review').val();
  var id = $('#expid').val();

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url:  base_url + 'experience/sortexpReview', 
          data: { 'val':value,'id':id,'filter_option':filter_option,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
            $('.list-container').html(response);
            
          }
      });
  }) 

  $(document).on('keyup','#filterexpbyid', function(){	 
    if( $('#filterexpbyid').val() != ""){
  //   $('.loadResult').removeClass('hide');
    var value = $('#filterexpbyid').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post',
            url: base_url+ 'experience/filterexpbyid',
            data: { 'val': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
          //   $('.loadResult').addClass('hide');
              $('.list-container').html(response);
            
            }
        });
      }
}) 




function markExpReviewspam(id) {
  //$('.loadResult').removeClass('hide');
  var fieldValue  = $('.mark-spam').data('value');

  var id = id;
  var expid = $('#expid').val();
  var fval = '3';
  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      url: base_url + 'experience/update_expreview',
      data: { 'value': fieldValue,'id':id,'expid':expid,'fval':fval,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('.loadResult').addClass('hide');
          
          $("#reviewField-" + id).prop("disabled", true);
          $("#buttonsContainer-" + id).addClass("d-none");
          $("#buttonsContainer-" + id + "-0").addClass("d-none");
          $("#buttonsContainer-" + id + "-1").addClass("d-none");
          $("#buttonsContainer-" + id + "-2").addClass("d-none");
          $("#buttonsContainer-" + id + "-5").addClass("d-none");
          $("#editBtn-" + id).removeClass("d-none");
          $('.list-container').html(response);
          $('.list-container').html(response);

          $('.exp-review-option').removeClass('sel-option')
          $('.exp-review-option').css({
            'font-weight': '',
            'text-decoration': ''
          });
          $('.opt-' + 3).css({
            'font-weight': 'bold',
            'text-decoration': 'underline'
          });
          $('.opt-' + 3).addClass('sel-option');
      }
    });

 }


//start exp category 
$(document).on('keyup','#search_exp_category', function(){	 
   
  var value = $('#search_exp_category').val();
  if(value != ""){
    $("#catlist").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'experience/search_exp_category',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#catlist').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value);
             //   $('#country-list').fadeIn();
                $('#catlist').append(listItem);
            });
         }
      });
    }else{
   
      $('#catlist').append("");
   //   $('#country-list').fadeOut();
    }
    $(document).on('click', "li", function(){
      $("#search_exp_category").val($(this).text());
      $('#catlist').fadeOut();

    });
  }) 


  $(document).ready(function() {
    $(document).on('click', '#addexp-cat', function() {
     
      var id = $('#addexp-cat').data('id');
      var value = $('#search_exp_category').val();
      $('#add-msg').text('Category adding..').css('color', 'green');

      if (value == "") {
        $('#add-msg').text('');
        $('#inputerror').text('Category is Required.').css('color', 'red');
        setTimeout(function(){
          $('#inputerror').text('');
        },30000);
        return;
      }
      $('#inputerror').text('');

      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: base_url + 'experience/addexpcat',
        data: { 'value': value, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){
          $('#add-msg').text('');
          if(response == 3){
            return  $('#inputerror').text('Invalid Category').css('color', 'red');
          }else if(response == 2){
            return  $('#inputerror').text('Category already exist.').css('color', 'red');
          }
          $('#staticBackdrop').modal('hide');
          $('#search_category').val();          
       
          $('.getupdatedfaq').html(response);
          
        }
      });
    
    });
  });


  $(document).ready(function() {
  
    $('.getupdatedfaq').on('click', '.delete-category-exp', function() {
        var id = $(this).data('id'); 
        var expid = $('#expid').val();
        var confirmation = confirm("Are you sure you want to delete this category?");

        if (confirmation) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'post',
                url: base_url + 'experience/deleteexp_category',
                data: { 'expid': expid, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
                success: function(response){
                  $('#staticBackdrop').modal('hide');
                    $('.getupdatedfaq').html(response);
                 
                }
            });
        } else {
           
            console.log("Deletion canceled");
        }
    });
});

  //end experience

  function deleteexpImage(id) {
    $('.loadResult').removeClass('hide');
    var restid = $('#expid').val();
    if (confirm('Are you sure you want to delete this image?')) {
  
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type:'POST',
        url: base_url + 'experience/delete_exp_image',
        data: { 'id':id,'expid':restid,
        '_token': $('meta[name="csrf-token"]').attr('content')},
        success: function(response){  
          $('.loadResult').addClass('hide');
          $('.getupdatedimges').html(response);
          $('#Success').html('<div class="alert alert-danger">Image deleted Successfully.</div>');              
             setTimeout(function() {
              $('#Success').html('');
          }, 30000);
        }
      });
  
    }
    $('.loadResult').addClass('hide');
  }

  $(document).on('keyup','#filter_exp_imgbyid', function(){	 
    var expid = $('#expid').val();  
    var value = $('#filter_exp_imgbyid').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'post', 
            url: base_url+ 'experience/filter_exp_image',  
            data: { 'val': value,'expid':expid,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
        
            $('.getupdatedimges').html(response);
            
            }
        });
      
  }) 

 function editexpimg(index){
    $('.edit-'+index).removeClass('d-none');

 }

 


 $(document).ready(function() {
  $(document).on('click', '.uploadimg', function() {
    var img =  $('.img').val();
    if(img != ""){
       return  $('#inputerror').text('Image is uploading..').css('color', 'green');
    } 
    
  });
});


//end functions


//attraction cat

$(document).ready(function() {
  $(document).on('click', '#saveatt-cat', function() {
   
    var id = $('#saveatt-cat').data('id');
    var value = $('#search_cat').val();

    if (value == "") {
      $('#inputerror').text('Category is Required.').css('color', 'red');
      setTimeout(function(){
        $('#inputerror').text('');
      },30000);
      return;
    }
    $('#inputerror').text('');

    $('#inputerror').text('Please wait...').css('color', 'green');
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: base_url + 'attraction/save_category',
      data: { 'value': value, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('#inputerror').text('');
        if(response == 3){
          return  $('#inputerror').text('Invalid Category').css('color', 'red');
        }else if(response == 2){
          return  $('#inputerror').text('Category already exist.').css('color', 'red');
        }
        
        $('#search_cat').val();
        $('#staticBackdrop').modal('hide');
        $('.getupdatedfaq').html(response);
        
      }
    });
  
  });
});
$(document).ready(function() {
  
  $('.getupdatedfaq').on('click', '.delete_att_cat', function() {
      var id = $(this).data('id'); 
      var sightid = $('#sightid').val();
      var confirmation = confirm("Are you sure you want to delete this category?");

      if (confirmation) {
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'post',
              url: base_url + 'attraction/deleteatt_category',
              data: { 'sightid': sightid, 'id': id, '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('#staticBackdrop').modal('hide');
                  $('.getupdatedfaq').html(response);
                 
                  $('#Success').html('<div class="alert alert-danger">Category deleted Successfully.</div>');         
              }
          });
      } else {
         
          console.log("Deletion canceled");
      }
  });
});




function markattReviewspam(id) {
  //$('.loadResult').removeClass('hide');
  var fieldValue  = $('.mark-spam').data('value');

  var id = id;
  var sightid = $('#sightid').val();
  var fval = '3';

  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      url: base_url + 'attraction/update_markspan',
      data: { 'value': fieldValue,'id':id,'sightid':sightid,'fval':fval,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('.loadResult').addClass('hide');
          
          $("#reviewField-" + id).prop("disabled", true);
          $("#buttonsContainer-" + id).addClass("d-none");
          $("#buttonsContainer-" + id + "-0").addClass("d-none");
          $("#buttonsContainer-" + id + "-1").addClass("d-none");
          $("#buttonsContainer-" + id + "-2").addClass("d-none");
          $("#buttonsContainer-" + id + "-5").addClass("d-none");
          $("#editBtn-" + id).removeClass("d-none");
          $('.list-container').html(response);
          

          $('.exp-review-option').removeClass('sel-option')
          $('.exp-review-option').css({
            'font-weight': '',
            'text-decoration': ''
          });
          $('.opt-' + 3).css({
            'font-weight': 'bold',
            'text-decoration': 'underline'
          });
          $('.opt-' + 3).addClass('sel-option');
      }
    });

 }

// end attraction cat



//add attraction faq
$(document).ready(function() {
  $('#saveAtt_cusfaq').click(function() {
    var question = $('#addques').val().trim();

    if (question === "") {
      $('#errorcustfaq').text('Question is Required.').css('color', 'red');
      setTimeout(function(){
        $('#errorcustfaq').text('');
      },30000);
      return;
    }
    $('#errorcustfaq').text('');
    var sightid = $('#saveAtt_cusfaq').data('id');
 
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url:  base_url+ 'attraction/add_sight_faq',
      data: { 'checkboxText': question,'sightid':sightid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('.getupdateddata').html(response);
        $('#staticBackdrop').modal('hide');
        var question = $('#addques').val('')
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 30000);
      }
  });

  });
});

//add custom faq
$(document).ready(function() {
  $('#save_att_faq').click(function() {
    var checkedCheckboxes = $('input[type="checkbox"]:checked');
    if (checkedCheckboxes.length === 0) {
      $('#errorcheck').text('Please Select a question').css('color', 'red');
      setTimeout(function(){
        $('#errorcheck').text('');
      },30000);
      return;
    }
    var checkboxText = '';
    if (checkedCheckboxes.length != 0) {
      $('#errorcheck').text('');
      checkedCheckboxes.each(function() {
        checkboxText += $(this).next('span').text() + '\n';
      });
    }
   var sightid = $('#save_att_faq').data('id');

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url:  base_url+ 'attraction/add_sight_faq',
      data: { 'checkboxText': checkboxText,'sightid':sightid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('#staticBackdrop').modal('hide');
        $('.getupdateddata').html(response);
        $('.checkbox').prop('checked', false);
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 30000);
      }
  });

   
  });
});

//end attraction faq

//start reviews function
$(document).on('click','#search_r_attraction', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#searchinput').val();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+  'reviews/search_r_attracion',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
          
           
          }
      });
  })

  $(document).on('click','.search-r-hotel', function(){	 
    $('.loadResult').removeClass('hide');
    var value = $('.hotelinput').val();
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url: base_url+ 'reviews/filter_r_hotel',
            data: { 'value': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.loadResult').addClass('hide');
                $('.getfilterData').html(response) 
            }
        });
    })

    
    $(document).on('click','.search-r-restaurent', function(){	 
      $('.loadResult').removeClass('hide');
      var value = $('.rest_input').val();
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'POST',
              url: base_url+ 'reviews/searchr_restaurant',
              data: { 'value': value,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                  $('.getfilterData').html(response) 
              }
          });
      })
         
    $(document).on('click','.search-r-experience', function(){	 
      $('.loadResult').removeClass('hide');
      var value = $('.exp_inp').val();
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'POST',
              url: base_url+ 'reviews/searchr_experience',
              data: { 'value': value,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                  $('.getfilterData').html(response) 
              }
          });
      })
//end reviews function
//start faq function
$(document).on('click','#search_faq_attraction', function(){	 
  $('.loadResult').removeClass('hide');
  
  var value = $('#searchinput').val();
  $('.getfilterData').html("");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+  'faq/searchfaqattracion',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
          
          }
      });
  })

  $(document).on('click','.search-faq-hotel', function(){	 
    $('.loadResult').removeClass('hide');
    var value = $('.hotelinput').val();
    $('.getfilterData').html("");
  
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url: base_url+ 'faq/filter_faq_hotel',
            data: { 'value': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.loadResult').addClass('hide');
                $('.getfilterData').html(response) 
            }
        });
    })
    $(document).on('click','.search-faq-restaurent', function(){	 
      $('.loadResult').removeClass('hide');
      var value = $('.rest_input').val();
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'POST',
              url: base_url+ 'faq/search_faq_restaurant',
              data: { 'value': value,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                  $('.getfilterData').html(response) 
              }
          });
      })
     
      $(document).on('click','.search-faq-experience', function(){	 
        $('.loadResult').removeClass('hide');
        var value = $('.exp_inp').val();
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type:'POST',
                url: base_url+ 'faq/search_faq_experience',
                data: { 'value': value,
                '_token': $('meta[name="csrf-token"]').attr('content')},
                success: function(response){
                  $('.loadResult').addClass('hide');
                    $('.getfilterData').html(response) 
                }
            });
        })
//end faq function
//start category function
$(document).on('click','#search_cat_attraction', function(){	 
  $('.loadResult').removeClass('hide');
  
  var value = $('#searchinput').val();
  $('.getfilterData').html("");
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+  'category/search_cat_attracion',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response)         
           
          }
      });
  })

  $(document).on('click','.search-cat-hotel', function(){	 
    $('.loadResult').removeClass('hide');
    var value = $('.hotelinput').val();
    $('.getfilterData').html("");
  
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url: base_url+ 'category/search_cat_hotel',
            data: { 'value': value,
            '_token': $('meta[name="csrf-token"]').attr('content')},
            success: function(response){
              $('.loadResult').addClass('hide');
                $('.getfilterData').html(response) 
            }
        });
    })
    $(document).on('click','.search-cat-experience', function(){	 
      $('.loadResult').removeClass('hide');
      var value = $('.exp_inp').val();
    
          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type:'POST',
              url: base_url+ 'category/search_cat_experience',
              data: { 'value': value,
              '_token': $('meta[name="csrf-token"]').attr('content')},
              success: function(response){
                $('.loadResult').addClass('hide');
                  $('.getfilterData').html(response) 
              }
          });
      })

//end category function

// start user

$(document).on('click','#search_users', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#search_input').val();

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'users/search_user',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
          }
      });
  })



$(document).on('keyup','#search_usercity', function(){	 
   
  var value = $('#search_usercity').val();
  if(value != ""){
    $("#citylist").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'attraction/search_city',
          data: { 'val': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
       
          success: function(response){
      
            $('#citylist').html("");

       
            response.forEach(function(country) {
                var listItem = $('<li>').text(country.value + ',' + country.country);
          
                $('#citylist').append(listItem);
            });
         }
      });
    }else{
   
      $('#citylist').append("");
   
    }
 
    $(document).on('click', "#citylist li", function(){
      var cityCountry = $(this).text(); 
      var city = cityCountry.split(',')[0]; 
      $("#search_usercity").val(city); 
      $('#citylist').fadeOut();
  });
  }) 

  function edit_user(index) {
  
    var originalQuestion = $('.name' + index).data('original-value');
  
   
    $("#buttonsContainer-" + index).removeClass("d-none");


    $('.name' + index).prop('disabled', false);
    $('#questionmsg-'+index).text('');
  }
  function canceluseredit(index) {
    var originalQuestion = $('.name' + index).data('original-value');
    $("#buttonsContainer-" + index).addClass("d-none");


    $('.name' + index).val(originalQuestion).prop('disabled', true);

    $('#questionmsg-'+index).text('');
    $('.name-error').text('');
    $('.email-error').text('');
 }

$(document).on('click','.update_user', function(){	 
  // $('.loadResult').removeClass('hide');
   var fname = $('#fname').val();
   var lname = $('#lname').val(); 
   var id = $('#id').val(); 
   var ctname = $('#search_usercity').val();  
   var username = $('#username').val();
   var colid = $(this).data('colid');
   var email = $('#Email').val();
   var phone = $('#phone').val();

   if(colid != 6){
    $("#buttonsContainer-" + colid).addClass("d-none");
    $('.name' + colid).prop('disabled', true);
   }
   var originalQuestion = $('.name' + colid).data('original-value');
   $('.city-error').text('');

   
   
    if (email.trim() != '') {
         var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; 
          if (!emailPattern.test(email)) {
            $('.email-error').text('Please enter a valid email address').css('color','red');
             return  setTimeout(function() {
                        $('.email-error').text('').css('color', ''); 
                    }, 4000); 
          } 
    }

       $.ajax({
           headers: {
               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
           },
           type:'POST',
           url: base_url+ 'users/update_user',
           data: { 'fname': fname,'lname':lname,'ctname':ctname,'username':username,'Email':email,'id':id,'phone':phone,
           '_token': $('meta[name="csrf-token"]').attr('content')}, 
           success: function(response){
             $('.loadResult').addClass('hide');
             if(response == 3){                  
                  $('.city-error').text('City not found.').css('color','red');
                  return  setTimeout(function() {
                              $('.city-error').text('').css('color', ''); 
                          }, 4000);
            }
            if(response == 1){
                $('.name' + colid).val(originalQuestion).prop('disabled', true);    
                  $('#Success').html('<div class="alert alert-danger">Data not updated </div>')
                return  setTimeout(function() {
                    $('#Success').html(''); 
                }, 40000);
              }
             $('.getupdateddata').html(response);
           
           }
       });
   })

//end user




//location faq


$(document).ready(function() {
  $('#savelocfaq').click(function() {
    var checkedCheckboxes = $('input[type="checkbox"]:checked');

    $('#waitmsg').text('Please Wait..').css('color', 'green');
    setTimeout(function(){
      $('#waitmsg').text('');
    },30000);
   
    if (checkedCheckboxes.length === 0) {
      $('#errorcheck').text('Please Select a question').css('color', 'red');
      setTimeout(function(){
        $('#errorcheck').text('');
      },30000);
      return;
    }
    var checkboxText = '';
    if (checkedCheckboxes.length != 0) {
      $('#errorcheck').text('');
      checkedCheckboxes.each(function() {
        checkboxText += $(this).next('span').text() + '\n';
      });
    }
   var locationid = $('#savelocfaq').data('id');

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url:  base_url+ 'listing/add_location_faq',
      data: { 'checkboxText': checkboxText,'locationid':locationid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('#waitmsg').text('');
        if(response == 3){
            $('#errorcheck').text('This Faq is already exist for this Location.').css('color', 'red');
            return     setTimeout(function(){
            $('#errorcheck').text('');
          },3000);
        }
        $('#staticBackdrop').modal('hide');
      
        $('.getupdatedfaq').html(response);
        $('.checkbox').prop('checked', false);
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 30000);
      }
  });

   
  });
});

$(document).ready(function() {
  $('#savecuslocfaq').click(function() {
    var question = $('#addques').val().trim();
   
      $('#waitmsg').text('Please Wait..').css('color', 'green');
      setTimeout(function(){
        $('#waitmsg').text('');
      },30000);
    
    if (question === "") {
      $('#errorcustfaq').text('Question is Required.').css('color', 'red');
      setTimeout(function(){
        $('#errorcustfaq').text('');
      },30000);
      return;
    }
    $('#errorcustfaq').text('');
    var locationid = $('#savecuslocfaq').data('id');
 
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url:  base_url+ 'listing/add_location_faq',
      data: { 'checkboxText': question,'locationid':locationid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){
        $('#waitmsg').text('');
        if(response == 3){
            $('#errorcheck').text('This Faq is already exist for this Location.').css('color', 'red');
            return     setTimeout(function(){
            $('#errorcheck').text('');
          },30000);
        }
        $('#staticBackdrop').modal('hide');
        $('.getupdatedfaq').html(response);
       
        var question = $('#addques').val('')
   
        $('#Success').html('<div class="alert alert-success">Faq Added Successfully.</div>');              
      setTimeout(function() {
        $('#Success').html('');
    }, 3000);
      }
  });

  });
});



//end locaton faq
    //edit Location
    function editLocation(index) {
    
      var originalQuestion = $('.name' + index).data('orgdata'); 
      $("#buttonsContainer-" + index).removeClass("d-none");
    //  $('.name' + index).val(originalQuestion).prop('disabled', false);
    $('.name' + index).prop('disabled', false);
    }

    function cancelLoc(index) {
      var orgdata = $('.name' + index).data('orgdata');
      $("#buttonsContainer-" + index).addClass("d-none");
      $('.name' + index).val(orgdata).prop('disabled', true);

    }

    $(document).on('click','.updateLocation', function(){	
    
     $('.loadResult').removeClass('hide');

    var colid = $(this).data('colid');
 
    var parLoc = $('#search_city').val();
    var Name = $('input[name="location_name"]').val();
    var page_slug = $('input[name="page_slug"]').val();
    var Countryid = $('#Countryid').val();
   
   

    if (Name === '') {
      
      $('.loadResult').addClass('hide');
      $(".errorname").text('Location name is required.').css('color','red');
      
      return false;
    }

    $(".errorname").hide();
    if (page_slug === '') {
      $('.loadResult').addClass('hide');
      $(".errorslug").text('Page Slug is required.').css('color','red');
      
      return false;
    }

    $(".errorslug").text(' ');
    $(".errorname").text(' ');
    
    var meta_title = $('input[name="meta_title"]').val();
    var meta_desc = $('textarea[name="meta_desc"]').val();
    var about = $('textarea[name="about"]').val();
    var locId =  $('#locid').val();

   
    var pincode = $('input[name="pincode"]').val();
   
    $("#buttonsContainer-" + colid).addClass("d-none");
    $('.name' + colid).prop('disabled', true);
 
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type:'POST',
            url: base_url+ 'listing/update_listing',
            data: { 'parLoc': parLoc,'Name':Name,'Countryid':Countryid,'meta_title':meta_title,'meta_desc':meta_desc,'about':about,'pincode':pincode,'locId':locId,'page_slug':page_slug,
            '_token': $('meta[name="csrf-token"]').attr('content')}, 
            success: function(response){

              
            // window.location.href = base_url+ 'landing';
              $('.loadResult').addClass('hide');
              $('.getupdata').html(response);
              $('#Success').html('<div class="alert alert-success">Data updated Successfully</div>')
              setTimeout(function(){
                $('#Success').html('');
              },3000);
            
            }
        });
    })
    //end edit Location




//admin user

$(document).on('click','#search_admin_users', function(){	 
  $('.loadResult').removeClass('hide');
  var value = $('#search_input').val();

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url: base_url+ 'users/search_admin_user',
          data: { 'value': value,
          '_token': $('meta[name="csrf-token"]').attr('content')},
          success: function(response){
            $('.loadResult').addClass('hide');
              $('.getfilterData').html(response) 
          }
      });
  })


  $(document).on('click','.update_admin_user', function(){	 
    // $('.loadResult').removeClass('hide');
     var name = $('#name').val();     
     var id = $('#id').val(); 
     var email = $('#email').val();  
    var colid = $(this).data('colid');

     if(name == ""){
        return $('.name-error').text('Name is required.').css('color','red');
     }

     if(email == ""){
      return $('.email-error').text('Email is required.').css('color','red');
    }
      if (email.trim() != '') {
           var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/; 
            if (!emailPattern.test(email)) {
              $('.email-error').text('Please enter a valid email address').css('color','red');
               return  setTimeout(function() {
                          $('.email-error').text('').css('color', ''); 
                      }, 4000); 
            } 
      }

      $('.name-error').text('');
      $('.email-error').text('');

      $("#buttonsContainer-" + colid).addClass("d-none");
      $('.name' + colid).prop('disabled', true);
         $.ajax({
             headers: {
                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
             },
             type:'POST',
             url: base_url+ 'users/update_admin_user',
             data: { 'name': name,'email':email,'id':id,
             '_token': $('meta[name="csrf-token"]').attr('content')}, 
             success: function(response){
               $('.loadResult').addClass('hide');
             
              if(response == 2){
                  $('.name' + colid).val(originalQuestion).prop('disabled', true);    
                    $('#Success').html('<div class="alert alert-danger">Data not updated </div>')
                  return  setTimeout(function() {
                      $('#Success').html(''); 
                  }, 40000);
                }

               $('.getupdateddata').html(response);
               $('#Success').html('<div class="alert alert-success">Data updated Successfully</div>')
               return  setTimeout(function() {
                   $('#Success').html(''); 
               }, 40000);
             
             }
         });
     })
  //end admin user

//edit image

function deleteattImage(id) {
  $('.loadResult').removeClass('hide');
  var sighid = $('#sighid').val();

  if (confirm('Are you sure you want to delete this image?')) {

    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'POST',
      url: base_url + 'attraction/delete_sight_image',
      data: { 'id':id,'sighid':sighid,
      '_token': $('meta[name="csrf-token"]').attr('content')},
      success: function(response){  
        $('.loadResult').addClass('hide');
        $('.getupdatedimges').html(response);
        $('#Success').html('<div class="alert alert-danger">Image deleted Successfully.</div>');              
           setTimeout(function() {
            $('#Success').html('');
        }, 30000);
      }
    });

  }
  $('.loadResult').addClass('hide');
}

$(document).on('keyup','#filter-sightimgbyid', function(){	 
  var sighid = $('#sighid').val();  
  var value = $('#filter-sightimgbyid').val();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post', 
          url: base_url+ 'attraction/filter_sight_image',  
          data: { 'val': value,'sighid':sighid,
          '_token': $('meta[name="csrf-token"]').attr('content')},

          success: function(response){
      
          $('.getupdatedimges').html(response);
          
          }
      });
    
}) 
// edit user


$(document).on('click','#delete-user',function(){
  var id = $(this).data('userid');
 
  if(confirm('Are you sure you want to delete this user?')){
    $('.loadResult').removeClass('hide');
    $.ajax({
      header:{
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url: base_url+'users/delete_admin_users',
      data:{'id':id,'_token':$('meta[name="csrf-token"]').attr('content')  },
      success:function(response){
        $('.loadResult').addClass('hide');
        $('.getupdateddata').html(response);
      }
    });

  }


});

$(document).on('change','.adm-active',function() {
  $('.loadResult').removeClass('hide');
  var value = $(this).val();
  var id =  $(this).data('id');
 
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type:'post',
      url: base_url+'users/admin_active',
      data:{ 'value':value,'id':id,'_token': $('meta[name="csrf-token"]').attr('content')},
      success:function(response){
        $('.loadResult').addClass('hide');
        $('.getupdateddata').html(response);
      }
    });
    
});



  
    // business functions 
    $(document).on('click','#delete-business-user',function(){
      var id = $(this).data('userid');
  
      if(confirm('Are you sure you want to delete this user?')){
        $('.loadResult').removeClass('hide');
        $.ajax({
          header:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url: base_url+'business/delete_business_users',
          data:{'id':id,'_token':$('meta[name="csrf-token"]').attr('content')  },
          success:function(response){
            $('.loadResult').addClass('hide');
            $('.getupdateddata').html(response);
          }
        });
    
      }
    
    
    });
  
    
    $(document).on('change','.buss-active',function() {
      $('.loadResult').removeClass('hide');
      var value = $(this).val();
      var id =  $(this).data('id');
     
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url: base_url+'business/business_active',
          data:{ 'value':value,'id':id,'_token': $('meta[name="csrf-token"]').attr('content')},
          success:function(response){
            $('.loadResult').addClass('hide');
            $('.getupdateddata').html(response);
          }
        });
        
    });

    $(document).on('change','.buss-status-active',function() {
      $('.loadResult').removeClass('hide');
      var value = $(this).val();
      var id =  $(this).data('id');
     
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url: base_url+'business/business_status_active',
          data:{ 'value':value,'id':id,'_token': $('meta[name="csrf-token"]').attr('content')},
          success:function(response){
            $('.loadResult').addClass('hide');
            $('.getupdateddata').html(response);
          }
        });
        
    });



    $(document).on('click','#delete-business',function(){
      var id = $(this).data('userid');
  
      if(confirm('Are you sure you want to delete this business?')){
        $('.loadResult').removeClass('hide');
        $.ajax({
          header:{
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'post',
          url: base_url+'business/delete_business',
          data:{'id':id,'_token':$('meta[name="csrf-token"]').attr('content')  },
          success:function(response){
            $('.loadResult').addClass('hide');
            $('.getupdateddata').html(response);
          }
        });
    
      }
    
    
    });
    //end business functions 









//start hotel landing listing 


//end add landing page
$(document).on('click','#add_hotel_listing_landing', function(){	 

  
  var name = $('#name').val();
 
  var slug = $('#slug').val(); 
  var meta_title = $('#meta_title').val();  
  var meta_tags = $('#meta_tags').val();
  var about = $('#about').val();
  var keywords = $('#keywords').val();
  // Validate required fields
  if (!name || name.trim() === '') {
    $('#Success').html('<div class="alert alert-danger">Name is required</div>');
    return;
  }

  $('.loadResult').removeClass('hide');
  var hotelId = $('#hotelId').val();
  var locationId = $('#locationId').val();
  
  var nearby = $('.nearby-value').text();
  var neighborhoodId = $('.nearby-value').data('id');

  var roommntArray = [];
  $('.mnt button').each(function() {
    var buttonText = $(this).text();
    roommntArray.push(buttonText);
  })

  var ratingarray = [];
  $('.star-rating button').each(function() {
    var startext = $(this).text();
    ratingarray.push(startext);
  });

  var hotelmntarray = [];
  $('.hotel-mnt button').each(function() {
    var hotemnttext = $(this).text();
    hotelmntarray.push(hotemnttext);
  });


  var nearbytype = $('input[name="near_by"]:checked').val();

  $.ajax({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: base_url + 'landing/store_hotel_listing_landing',
    data: { 
      'name': name,
      'slug': slug,
      'meta_title': meta_title,
      'meta_desc': meta_tags,
      'about': about,
      'nearby': nearby,
      'roommntArray': roommntArray,
      'ratingarray': ratingarray,
      'hotelmntarray': hotelmntarray, 
      'keywords': keywords,
      'nearbytype': nearbytype,
      'nearbyid':neighborhoodId,
      'locationId':locationId,
      '_token': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response){
      $('.loadResult').addClass('hide');
      if(response == 1){
        var editUrl = base_url + 'landing/show-all-hotel-landing-listings';
        window.location.href = editUrl;
      } else {
        $('#Success').html('<div class="alert alert-danger">Landing page not Added</div>')
      }
      console.log(response);
    }
  });
});


//edit hotel landing section

// hotel-listing-edit.js

function editHlanding(section) {
  // Enable the textarea for the specified section
  const textarea = document.getElementById(getTextareaId(section));
  if (textarea) {
    textarea.disabled = false;
    textarea.focus();
  }

  // Show the save and cancel buttons for the specified section
  const buttonsContainer = document.getElementById('buttonsContainer-' + section);
  if (buttonsContainer) {
    buttonsContainer.classList.remove('d-none');
  }
}

function cancelHLanding(section) {
  // Disable the textarea and reset its value to the original
  const textarea = document.getElementById(getTextareaId(section));
  if (textarea) {
    textarea.disabled = true;
    textarea.value = textarea.getAttribute('data-original-value');
  }

  // Hide the save and cancel buttons for the specified section
  const buttonsContainer = document.getElementById('buttonsContainer-' + section);
  if (buttonsContainer) {
    buttonsContainer.classList.add('d-none');
  }
}

function getTextareaId(section) {
  switch (section) {
    case 1: return 'name';
    case 2: return 'slug';
    case 3: return 'meta_title';
    case 4: return 'meta_tags';
    case 5: return 'Keywords';
    case 6: return 'about';
    
    // Add more cases if needed for other sections
    default: return null;
  }
}
//end edit hotel landing section


$(document).on('click', '.update_hotel_listing_landing', function() {
  // Get the column ID to know which section we're updating
  var colId = $(this).data('colid');
  
  // Get basic form values
  var name = $('#name').val();
  var slug = $('#slug').val();
  var meta_title = $('#meta_title').val();
  var meta_tags = $('#meta_tags').val();
  var about = $('#about').val();
  var keywords = $('#Keywords').val();
  var listingId = $('#listingId').val();
  var locationId = $('#locationId').val();

  // Validate required fields
  if (!name || name.trim() === '') {
      $('#Success').html('<div class="alert alert-danger">Name is required</div>');
      return;
  }

  $('.loadResult').removeClass('hide');

  // Get nearby values
  var nearby = $('.nearby-value').text();
  var neighborhoodId = $('#nearbyid').val();

  var ratingarray = [];
  $('.star-rating button').each(function() {
      var startext = $(this).text();
      ratingarray.push(startext);
  });

  var hotelmntarray = [];
  $('.hotel-mnt button').each(function() {
      var hotemnttext = $(this).text();
      hotelmntarray.push(hotemnttext);
  });

  var nearbytype = $('input[name="near_by"]:checked').val();

  // Send AJAX request
  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'landing/updatehotel-listing-landing/' + listingId,
      data: {
          'name': name,
          'slug': slug,
          'meta_title': meta_title,
          'meta_desc': meta_tags,
          'about': about,
          'nearby': nearby,        
          'ratingarray': ratingarray,
          'hotelmntarray': hotelmntarray,    
          'keywords': keywords,
          'nearbytype': nearbytype,
          'nearbyid': neighborhoodId,
          'locationId': locationId,
          '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
          $('.loadResult').addClass('hide');
          if (response == 1) {
              $('#Success').html('<div class="alert alert-success">Landing page updated successfully</div>');
              
              // Disable the textarea and hide buttons after successful save
              const textarea = document.getElementById(getTextareaId(colId));
              if (textarea) {
                  textarea.disabled = true;
                  // Update the original value attribute
                  textarea.setAttribute('data-original-value', textarea.value);
              }

              // Hide the buttons container
              const buttonsContainer = document.getElementById('buttonsContainer-' + colId);
              if (buttonsContainer) {
                  buttonsContainer.classList.add('d-none');
              }

              // If it's the nearby section (colId === 7)
              /*if (colId === 7) {
                  // Disable all inputs in the nearby section
                  $('.nearby input[type="radio"]').prop('disabled', true);
                  $('.inputval').prop('disabled', true);
                  $('.btn-secondary').prop('disabled', true);
              }*/

              setTimeout(function() {
               //   window.location.href = base_url + 'landing/show-all-hotel-landing-listings';
              }, 1500);
          } else {
              $('#Success').html('<div class="alert alert-danger">Error updating landing page</div>');
          }
      },
      error: function(xhr, status, error) {
          $('.loadResult').addClass('hide');
          $('#Success').html('<div class="alert alert-danger">Error: ' + error + '</div>');
      }
  });
});

//edit hotel listing landing page


//search listing 

// Add search functionality
// ... existing code ...

// Hotel Landing Page Search Function

$(document).ready(function() {
    let searchTimer;
    const searchInput = $('#searchInput');
    const clearButton = $('#clearSearch');

    searchInput.on('keyup', function() {
        clearTimeout(searchTimer);
        const searchTerm = $(this).val();

        // Show/hide clear button
        clearButton.toggle(searchTerm.length > 0);

        // Set a timer to prevent too many requests
        searchTimer = setTimeout(function() {
            $.ajax({
                url: base_url + 'landing/hotel_listing_landing_search',
                method: 'GET',
                data: { 
                    search: searchTerm
                },
                success: function(response) {
                    $('.searchResults').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Search error:', error);
                }
            });
        }, 300);
    });

    // Clear search
    clearButton.on('click', function() {
        searchInput.val('');
        $(this).hide();
        searchInput.trigger('keyup');
    });
});

//end search listing 




//end hotel landing listing
































// Select date time Popup


    let datetimsBtn = document.querySelector(".datetims");
    let adddatetims = document.querySelector(".add-datetims");
    let closedatetims = adddatetims.querySelector(".close-datetims");
    datetimsBtn.addEventListener("click", () => {
      adddatetims.classList.remove("d-none");
    });
    closedatetims.addEventListener("click", () => {
      adddatetims.classList.add("d-none");
    });

    $(document).on('click', '.plusicon', function() {
      var html = '<div class="row pls">' +
        '<div class="col-md-5 col-5">' +
        '<div class="mb-3 mt-3">' +
        '<input type="time" class="form-control" id="clopen" placeholder="Enter email" name="opentime[]">' +
        '</div>' +
        '</div>' +
        '<div class="col-md-5 col-5">' +
        '<div class="mb-3 mt-3">' +
        '<input type="time" class="form-control" id="cltime" placeholder="Enter email" name="cltime[]">' +
        '</div>' +
        '</div>' +
        '<div class="col-md-2 col-2">' +
        '<div class="closeicon">x</div>' +
        '</div>' +
        '</div>';
    
      $('.pls').last().after(html);
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
    
      var uncheckedIds = [];
      $('.invisible-checkboxes input[type="checkbox"]').not(':checked').each(function () {
        uncheckedIds.push($(this).attr('id'));
      });
    
      // Get open 24 hours and closed status
      var open24Hours = $('#inlineCheckbox1').prop('checked') ? 1 : 0;
      var closed = $('#inlineCheckbox2').prop('checked') ? 1 : 0;
      var sightid = $('#sightid').val();


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
      if (selectedDays.length === openingTimes.length ||open24Hours == 1 || openingTimes.length === 1 && openingTimes.every(time => time !== "") &&  closingTimes.every(time => time !== ""))  {
        $('.error').text('');
        // Prepare data to be sent to the server
        // var data = {
        //   uncheckedIds: uncheckedIds,
        //   selectedDays: selectedDays,
        //   open24Hours: open24Hours,
        //   closed: uncheckedCount,
        //   openingTimes: openingTimes,
        //   closingTimes: closingTimes,
        //   sightid:sightid,
        //   _token: $('meta[name="csrf-token"]').attr('content'),
        // };
    
        // Send the data to the server via AJAX
       $('.add-datetims').addClass('d-none');
       $('.error').text('');
       
      } else {
        
      //  alert("Please choose opening and closing time for all days");
        $('.error').text('Please choose opening and closing time for all days or same for all').css('color','red');
      }
    });

























