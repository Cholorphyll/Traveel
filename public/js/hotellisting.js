
var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';


function fetchFiltered(){
  // $priceFrom = $("#rangeSliderExample5MinResult").slider("values", 0);
  // $priceTo = $("#rangeSliderExample5MaxResult").slider("values", 1);
  $priceFrom = $("#rangeSliderExample5MinResult").text();
  $priceTo = $("#rangeSliderExample5MaxResult").text();
  var locationid=$('.loc_id').text();
  // var typeval = [];
  
  // $('.filter-drop-down.type input[type=checkbox]:checked').each(function() {
  //   typeval.push($(this).val());
  // });
  // var hoteltp = typeval.join(',');

  var ut=[]
  $('.user-rating input[name="use_rat"]:checked').each(function() {
    ut.push($(this).val()); 
  });
  var userrating =  ut.join(',');


  var starRating = $('.star-rating input[name="rating"]:checked').val();

  var ht=[]
  var hoteltype = $('#hoteltype input[name="hoteltypes"]:checked').each(function(){
    ht.push($(this).val());
  })
  var hotelaa =  ht.join(',');

  $('input[name="mnt"]:checked').each(function () {
    var labelText = $(this).next('label').text().trim();

});

var mnt=[]
var amenities = $('.mnts input[name="mnt"]:checked').each(function(){
  mnt.push($(this).val());
})
var mnts = mnt.join(',');

 var distance = $('.rangevalue').text();


var address = $('#address').val();


  var nabour =[]
  $('.neighbourhoods input[type=checkbox]:checked').each(function() {
    nabour.push($(this).val()); 
  });
  var neibourhood = nabour.join(',');

  $.ajax({
    type:'post',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
   
    url: base_url + 'filter_hotel_list',
    data: { 'priceFrom': $priceFrom,'priceTo':$priceTo,'hoteltype':hotelaa,'neibourhood':neibourhood,'distance':distance,'starRating':starRating,'userrating':userrating,'locationid':locationid,'mnt':mnts,'address':address,
    '_token': $('meta[name="csrf-token"]').attr('content')},  
    success: function(response){
      $('.filter-listing').html(response);
     
    }
  });


}


$(document).on('click','#filterchackinout', function(){

  var checkin = $('.t-input-check-in').val();

  var checkout =  $('.t-input-check-out').val();
	
  if (checkin === 'null' || checkout === 'null') {
        alert('Please select both check-in and check-out dates ');
        return;
    }
	
   var rooms = $('#totalroom').text();
   var guest = $('#totalguests').text(); 
   var lid = $('.loc_id').text(); 
	
  //  var child1 = $('.child-1').val(); location_id
  //  var child2 = $('.child-2').val(); 

	
	
 $.ajax({
   headers: {
       'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
   },
   type:'POST',
   url: base_url + 'filter_availble_hotel',
   data: { 'checkin': checkin,'checkout':checkout,'rooms':rooms,'guest':guest,'lid':lid,
   '_token': $('meta[name="csrf-token"]').attr('content')},
   success: function(response){ 
	 
	   if(response == 22){
	   		return;
	   }
	     if(response == ""){
	   		return  $('.filter-listing').html("<h3 class='m-3'> Search is not finished, please try again.</h3>")
	   }
    $('.filter-listing').html(response);
     
   }
 });

})



// multi slider
$(function () {
  $("#slider-range").slider({
    range: true,
    min: 0,
    max: 250,
    values: [0, 140],
    slide: function (event, ui) {
      $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
      //alert("value chamged --- " + ui.values[0] + " vv   " + ui.values[1]);
    },
    change: function (event, ui) {
    // fetchFiltered();
    }
  });
  $("#amount").val(
    "$" +
      $("#slider-range").slider("values", 0) +
      " - $" +
      $("#slider-range").slider("values", 1)
  );
});

document.querySelector(".rooms-select").addEventListener("click", () => {
  // document.querySelector("#");
  document.querySelector(".rooms-num").classList.toggle("d-none");
});




 
 $(document).ready(function () {
  const minus = $('.decrement');
  const plus = $('.increment');
  const input = $('.inputfield');
  const reset = $('.Reset');
  const apply = $('.apply');
  const totalguests = $('.totalguests');
  const inputrooms = $('#inputRooms');
  
  const roomcount = $('.roomcount');





  input.val(0);
  $('#inputRooms').val(0)
  minus.click(function () {

      var value = $(this).siblings(input).val();
      if (value > 1) {
          value--;
      }
      $(this).siblings(input).val(value);
  });

  plus.click(function () {
      var value = $(this).siblings(input).val();
      // alert(value);
      value++;
      $(this).siblings(input).val(value);
  })


  $(reset).click(function (e) {
      e.preventDefault();
      input.val(0)
      $('#inputRooms').val(0)
      roomcount.text('')
      $('.error-msg').hide();
  });


  $(apply).click(function (e) {
      e.preventDefault();
      var totalPoints = 0;
      $('.counter .inputfield').each(function () {
          totalPoints = parseFloat($(this).val()) + totalPoints;
      });
      totalguests.text(totalPoints)
      roomcount.text(inputrooms.val()) 

       var childrensValue = parseInt($('.Childrens').val());


      if (childrensValue > 0) {
        $('.child-1').prop('required', true);
        $('.error-msg').show();
      } else {

        $('.child-1').prop('required', false);
        $('.error-msg').hide();
      }

      
  });

});



  