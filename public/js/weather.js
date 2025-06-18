var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';

$(document).on('keyup','#searchHotelcity', function(){	 
   
  var value = $('#searchHotelcity').val();


  if(value != ""){
    $("#citylisth").css("display", "block");

      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type:'POST',
          url:  base_url+  'searchloc',
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


    $(document).off('click', "li").on('click', "li", function(){
      var text = $(this).text();
      var parts = text.split(",");
      var cityName = parts[0].trim();
      var country = parts[1].trim();
  
      $("#searchHotelcity").val(cityName);
      $("#Country").val(country);
      $('#citylisth').fadeOut();
      var width = $('.width').val();
      var height = $('.height').val();
      if(cityName !="" && country !=""){
        var locname = cityName + ',' + country;
      }else if(country !=""){
        var locname =  country;
      }
      
      var theme = $('.theme-name').text();
      //start
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: base_url + 'getwearapi',
          data: {
              'locname': locname,'width':width,'height':height,'theme':theme,
              '_token': $('meta[name="csrf-token"]').attr('content')
          },
  
          success: function(response) {
             $('.getresult').html(response)
          
             $('#generate-code-button').removeClass('disabled');
             
          }
      });
      //end
  });

 

  }) 


  
  $(document).on('click', '#search-weather', function() {
   
    var value = $('#searchHotelcity').val();

    var cityName =  $("#searchHotelcity").val();
    var country =  $("#Country").val();
  
    var width = $('.width').val();
    var height = $('.height').val();
    if(cityName !="" && country !=""){
      var locname = cityName + ',' + country;
    }else if(country !=""){
      var locname =  country;
    }
    
  
      //start
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: base_url + 'getwearapi',
          data: {
              'locname': locname,'width':width,'height':height,
              '_token': $('meta[name="csrf-token"]').attr('content')
          },
  
          success: function(response) {
             $('.getresult').html(response)
          
             $('#generate-code-button').removeClass('disabled');
             
          }
      });


  }) 

//end code


  $(document).ready(function() {
    $('#get-code-button').click(function() {
        $('#weather-widget-container').show();
        $('#weather-widget-modal').modal('show');
    });
});
 

//get data 
function fetchWeather(city) {
  const apiKey = 'YOUR_API_KEY';
  const apiUrl = `https://api.openweathermap.org/data/2.5/weather?q=${city}&appid=${apiKey}&units=metric`;

  return fetch(apiUrl)
    .then(response => {
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    });
}

function updateWeatherWidget(city) {
  fetchWeather(city)
    .then(weatherData => {
      const weatherLink = document.getElementById('weather-link');
      weatherLink.textContent = `${city} Weather: ${weatherData.main.temp}°C, ${weatherData.weather[0].description}`;
    })
    .catch(error => {
      console.error('Error fetching weather data:', error);
    });
}

document.addEventListener('DOMContentLoaded', function() {
  const weatherLink = document.getElementById('weather-link');
  weatherLink.addEventListener('click', function(event) {
    event.preventDefault();
    updateWeatherWidget('New York'); // Default city
  });
});


function toggleTheme() {
  $('.theme-val').removeClass('d-none');  
  $('.abc').addClass('d-none');
  $('.setting').removeClass('text-underline');
  $('.Theme').addClass('text-underline');
}   

function toggleSetting() {
  $('.abc').removeClass('d-none'); 
  $('.theme-val').addClass('d-none') 
  $('.setting').addClass('text-underline');
  $('.Theme').removeClass('text-underline');
  
}

// function applyStyleToWeather(card) {
//   var dataVal = card.getAttribute('data-val'); // Get the value of data-val attribute
//   var weatherElement = document.querySelector('.mail-weather'); // Get the mail-weather element

//   // Apply the style to the mail-weather element
//   if (weatherElement) {
//       weatherElement.setAttribute('style', dataVal);
//   } else {
//       console.error('.mail-weather element not found.');
//   }
// }



// function applyStyleToWeather(card) {
//   var theme = card.getAttribute('data-theme');
//   $('.theme-name').text(theme);
//   var datatextcolor = card.getAttribute('data-textcol');
//   var weatherElements = document.querySelectorAll('.mail-weather');
//   var textElements = document.querySelectorAll('.text-color');

//   weatherElements.forEach(function(weatherElement) {
//     weatherElement.classList.add(theme); // Use weatherElement instead of weatherElements
//   });

//   textElements.forEach(function(textElement) {
//     textElement.classList.add(datatextcolor);
//   });
// }


function applyStyleToWeather(card) {
  var theme = card.getAttribute('data-theme');
  $('.theme-name').text(theme);
  var datatextcolor = card.getAttribute('data-textcol');
  var weatherElements = document.querySelectorAll('.mail-weather');
  var textElements = document.querySelectorAll('.text-color');

  
  weatherElements.forEach(function(weatherElement) {
      weatherElement.classList.remove('dark-grey', 'blue','yellow','basic-white','light-grey'); 
      weatherElement.classList.add(theme);
  });

  
  textElements.forEach(function(textElement) {
      textElement.classList.remove('white', 'black');
      textElement.classList.add(datatextcolor);
  });
}

