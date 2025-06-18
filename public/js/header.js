var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';



// start search
$(document).ready(function() {
  var locationIdValue = $('#locid').text();

  $.ajax({
    type: 'GET',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: base_url + 'addLocationfaqfont',
    data: { 'locationIdValue': locationIdValue},
    success: function(response) {
        $('#faqdata').html(response);
     
    },
    error: function(xhr, status, error) {
      // Handle the error
      console.log(error);
    }
  });
});



$(document).on('click', '.filter_sightbycat input[type="checkbox"]', function (e) {
  e.stopPropagation();

  var $parent = $(this).closest('.filter_sightbycat');
  var catid = $parent.data('catid'); // Get the category ID from data attribute
  var names = $parent.data('name'); // Get the name from data attribute
  var locationId = $('#locid').text(); // Get the location ID

  // Check if the checkbox is checked
  if ($(this).is(':checked')) {
      // Checkbox is checked, add filter
      makeAjaxRequest(catid, locationId, names, null, false);
  } else {
      // Checkbox is unchecked, remove filter
      makeAjaxRequest(null, locationId, null, catid, true); // Pass catid as delcatid
  }
});

// The existing delete-icon click event remains unchanged
$(document).on('click', '.delete-icon.filter_sightbycat', function (e) {
  e.stopPropagation();

  var delcatid = $(this).data('delcatid');
  var locationId = $('#locid').text();

  makeAjaxRequest(null, locationId, null, delcatid, true);
});

// AJAX function to send the request
function makeAjaxRequest(catid, locationId, names, delcatid, clearfilter) {
  $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: base_url + 'filtersightbycat',
      data: {
          'catid': catid,
          'locationId': locationId,
          'names': names,
          'delcatid': delcatid, // This is where the removed filter's ID is sent
          'clearfilter': clearfilter,
          '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function (response) {
          $('#getcatfilterdata').html(response.html);
          var mapData = JSON.parse(response.mapData);
          updateMapWithFilteredDatss(mapData);
      }
  });
}




function updateMapWithFilteredDatss(getrest) {
  var newLocations = [];
  
  // Remove existing markers from the map
  for (var key in markers) {
      if (markers.hasOwnProperty(key)) {
          var existingMarker = markers[key];
       
          map.removeLayer(existingMarker);
      }
  }
  
  markers = {}; // Clear the markers object
  
  var iconUrl = 'public/js/images/3.svg';
  
  getrest.forEach(function(data) {
      if (data.Latitude && data.Longitude) {
          var marker;
          if (data.ismustsee == 1) {
              marker = new L.Marker([parseFloat(data.Latitude), parseFloat(data.Longitude)], {
                  icon: L.icon({
                      iconUrl: iconUrl,
                      iconSize: [52, 53],
                  })
              });
          } else {
              marker = new L.Marker([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
          }
          
          var markerId = data.SightId;
          
          if (!markers.hasOwnProperty(markerId)) {
              marker.on('mouseover', function(e) {
                  showLocationss(e, data.name, data.recmd, data.cat, data.tm, data.cityName, data.imagePath);
              });
              
              marker.on('mouseout', function(e) {
                  map.closePopup();
              });
              
              marker.addTo(map);
              markers[markerId] = marker;
          }
          
          newLocations.push([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
      }
  });
  

  
  //var group = new L.featureGroup(newLocations);
  //map.fitBounds(group.getBounds());
  if (newLocations.length > 0) {
        var bounds = L.latLngBounds(newLocations);
        map.fitBounds(bounds, { padding: [50, 50] }); 
    } else {
        console.log('No valid locations to fit.');
    }
}

function showLocationss(e, name, recmd, cat, tm, cityName, imagePath) {
  var marker = e.target;
  var popupContent = `
  <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important;
    right: -1px;">
    <div class="tr-historical-monument">
      <div class="tr-heading-with-distance">
        <div class="tr-category">${cat}</div>      
      </div>
      <div class="tr-image">
        <a href="javascript:void(0);">
          <img loading="lazy" src="${imagePath}" alt="" height="109" width="280">
        </a>
      </div>
      <div class="tr-details">
        <h3><a href="javascript:void(0);" target="_blank">${name}</a></h3>
        <div class="tr-location">${cityName || ''}</div>
        <div class="tr-like-review">
          <div class="tr-heart">
            <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z"
                fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </div>
          <div class="tr-ranting-percent">${recmd || ''}</div>
        </div>
      </div>
      <div class="tr-more-inform">
        <ul>
          <li><span>Open</span>${tm || ''}</li>         
        </ul>
      </div>
    </div>
  </div>`;
  marker.bindPopup(popupContent, { offset: L.point(0, -20) }).openPopup();
}

//end code sight search where
//end  filter sights by category





//end code map 



 var categoryElements = document.querySelectorAll(".filter_sightbycat");
 categoryElements.forEach(function(element) {
   element.addEventListener("click", function() {
     var categoryId = this.getAttribute("data-catid");

     // Swap category positions
     var container = document.querySelector(".container");
     var slideElements = document.querySelectorAll(".slide");

     // Find the index of the clicked category
     var clickedIndex = Array.from(slideElements).findIndex(function(slideElement) {
       return slideElement.contains(element);
     });

     // Move the clicked category to the first position
     container.insertBefore(slideElements[clickedIndex], slideElements[0]);

     // Output the new order of categories
     console.log("New Order:", Array.from(slideElements).map(function(slideElement) {
       return slideElement.textContent.trim();
     }));

     // Add your code to display relevant content based on the new order
   });
 });








//start sight listing search code


var searchTimeouts;

$(document).on('keyup', '.serch_sights', function() {
    var value = $(this).val();
    var locationId = $('#locid').text();
    clearTimeout(searchTimeouts);

    searchTimeouts = setTimeout(function() {
       sightSearch(value, locationId); 
    }, 500);
});
function sightSearch(value, locationId) {
  if(value !=""){
      $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'searchsightlisting',
        data: {
            'val': value,
            'locId': locationId,
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            $('.search-result').empty();
            let resultHtml = '<ul>';     
            if (response.length > 0) {
                response.forEach(function(item) {
                    
                    resultHtml += `
                        <li class="result-item" data-value="${item.value}" data-type="${item.type}">
                            <div class="tr-place-info">
                                <div class="tr-location-icon"></div>
                                <div class="tr-location-info">
                                    <div class="tr-name">${item.value}</div>
                                    <div class="tr-category">${item.type}</div>
                                </div>
                            </div>
                        </li>
                    `;
                });
            } else {          
                resultHtml += `
                    <li>
                        <div class="tr-place-info">
                            <div class="tr-location-info">
                                <div class="tr-name">Result not found</div>
                            </div>
                        </div>
                    </li>
                `;
            }
            resultHtml += '</ul>';
            $('.search-result').html(resultHtml);       
            $('.result-item').on('click', function() {             
                const selectedValue = $(this).data('value');
                const selectedType = $(this).data('type');          
                $('#serch_sights').val(selectedValue);         
                $('.res_type').text(selectedType); 
                $('.search-result').empty();
            });
        },
        error: function(xhr, status, error) {
            console.error("Error fetching sight listings:", error);
        }
      });

  }else{
    $.ajax({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: base_url + 'sightlistinghistory',
      data: {        
          '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
          $('.search-result').empty();
          let resultHtml = '<h5>Recent searches</h5><ul>';     
          if (response.length > 0) {
              response.forEach(function(item) {
                  
                  resultHtml += `
                      <li class="result-item" data-value="${item.value}" data-type="${item.type}">
                          <div class="tr-place-info">
                              <div class="tr-location-icon"></div>
                              <div class="tr-location-info">
                                  <div class="tr-name">${item.value}</div>
                                    ${item.type ? `<div class="tr-category">${item.type}</div>`: ''}
                              </div>
                          </div>
                      </li>
                  `;
              });
          }
          resultHtml += '</ul>';
          $('.search-result').html(resultHtml);       
          $('.result-item').on('click', function() {             
              const selectedValue = $(this).data('value');
          //    const selectedType = $(this).data('type');          
              $('#serch_sights').val(selectedValue);         
            //  $('.res_type').text(selectedType); 
              $('.search-result').empty();
          });
      },
      error: function(xhr, status, error) {
          console.error("Error fetching sight listings:", error);
      }
    });

  }

  
}


//new code
$(document).on('click', '#serch_sights', function() {
  var value = $('#serch_sights').val();
 // var res_type = $('#res_type').text();
  //var locationId = $('#locid').text(); 
  if(value ==""){
  $.ajax({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    type: 'POST',
    url: base_url + 'sightlistinghistory',
    data: {        
        '_token': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
        $('.search-result').empty();
        let resultHtml = '<h5>Recent searches</h5><ul>';     
        if (response.length > 0) {
            response.forEach(function(item) {
                
                resultHtml += `
                    <li class="result-item" data-value="${item.value}" data-type="${item.type}">
                        <div class="tr-place-info">
                            <div class="tr-location-icon"></div>
                            <div class="tr-location-info">
                                <div class="tr-name">${item.value}</div>                          
                                   ${item.type ? `<div class="tr-category">${item.type}</div>`: ''}
                            </div>
                        </div>
                    </li>
                `;
            });
        } else {          
            resultHtml += `
                <li class="result-item" data-value="Restaurant">
                    <div class="tr-place-info">
                        <div class="tr-location-info">
                            <div class="tr-name">Restaurant</div>
                        </div>
                    </div>
                </li>
                  <li class="result-item" data-value="Experince">
                    <div class="tr-place-info">
                        <div class="tr-location-info">
                            <div class="tr-name">Experince</div>
                        </div>
                    </div>
                </li>
            `;
        }
        resultHtml += '</ul>';
        $('.search-result').html(resultHtml);       
        $('.result-item').on('click', function() {             
            const selectedValue = $(this).data('value');
        //    const selectedType = $(this).data('type');          
            $('#serch_sights').val(selectedValue);         
          //  $('.res_type').text(selectedType); 
            $('.search-result').empty();
        });
    },
    error: function(xhr, status, error) {
        console.error("Error fetching sight listings:", error);
    }
  });
}
});




//start code sight search where
$(document).on('click', '#serch_sightsdata', function() {
  var value = $('#serch_sights').val();
  var res_type = $('#res_type').text();
  var locationId = $('#locid').text(); 

  performSightSearch(value, locationId,res_type); 
});


function performSightSearch(value, locationId) {
    
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: base_url + 'search_sights',
                data: {
                    'val': value,
                    'locationId': locationId,
                    '_token': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                  $('#getcatfilterdata').html(response.html);
                    var mapData = JSON.parse(response.mapData);
                    updateMapWithFilteredDats(mapData);
                  
                }
            });
      
}




function updateMapWithFilteredDats(getrest) {
 
  
  var newLocations = [];
  
  // Remove existing markers from the map
  for (var key in markers) {
      if (markers.hasOwnProperty(key)) {
          var existingMarker = markers[key];      
          map.removeLayer(existingMarker);
      }
  }
  
  markers = {}; // Clear the markers object
  
  var iconUrl = 'public/js/images/3.svg';
  
  getrest.forEach(function(data) {
      if (data.Latitude && data.Longitude) {
          var marker;
          if (data.ismustsee == 1) {
              marker = new L.Marker([parseFloat(data.Latitude), parseFloat(data.Longitude)], {
                  icon: L.icon({
                      iconUrl: iconUrl,
                      iconSize: [52, 53],
                  })
              });
          } else {
              marker = new L.Marker([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
          }
          
          var markerId = data.SightId;
          
          if (!markers.hasOwnProperty(markerId)) {
              marker.on('mouseover', function(e) {
                  showLocations(e, data.name, data.recmd, data.cat, data.tm, data.cityName, data.imagePath);
              });
              
              marker.on('mouseout', function(e) {
                  map.closePopup();
              });
              
              marker.addTo(map);
              markers[markerId] = marker;
          }
          
          newLocations.push([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
      }
  });
  
  if (newLocations.length > 0) {      
        // Create bounds using L.latLngBounds
        var bounds = L.latLngBounds(newLocations);
        map.fitBounds(bounds, { padding: [50, 50] }); // Adjust padding as needed
	  
    } else {
        console.log('No valid locations to fit.');
    }
	
}

function showLocations(e, name, recmd, cat, tm, cityName, imagePath) {
  var marker = e.target;
  var popupContent = `
  <div class="tr-map-tooltip tr-explore-listing" style="top: -214px !important;
    right: -1px;">
    <div class="tr-historical-monument">
      <div class="tr-heading-with-distance">
        <div class="tr-category">${cat}</div>     
      </div>
      <div class="tr-image">
        <a href="javascript:void(0);">
          <img loading="lazy" src="${imagePath}" alt="" height="109" width="280">
        </a>
      </div>
      <div class="tr-details">
        <h3><a href="javascript:void(0);" target="_blank">${name}</a></h3>
        <div class="tr-location">${cityName || ''}</div>
        <div class="tr-like-review">
          <div class="tr-heart">
            <svg width="12" height="11" viewBox="0 0 12 11" fill="none" xmlns="http://www.w3.org/2000/svg">
              <path fill-rule="evenodd" clip-rule="evenodd"
                d="M5.99604 2.28959C5.02968 1.20745 3.41823 0.916356 2.20745 1.90727C0.996677 2.89818 0.826217 4.55494 1.77704 5.7269L5.99604 9.63412L10.215 5.7269C11.1659 4.55494 11.0162 2.88776 9.78463 1.90727C8.55304 0.92678 6.96239 1.20745 5.99604 2.28959Z"
                fill="white" stroke="white" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </div>
          <div class="tr-ranting-percent">${recmd || ''}</div>
        </div>
      </div>
      <div class="tr-more-inform">
        <ul>
          <li><span>Open</span>${tm || ''}</li>         
        </ul>
      </div>
    </div>
  </div>`;
  marker.bindPopup(popupContent, { offset: L.point(0, -20) }).openPopup();
}

//end code sight search where

//end code sight search where





// Add event listeners to stars
const stars = document.querySelectorAll('.stars');

stars.forEach((star, index) => {
    star.addEventListener('click', function() {
        for (let i = 0; i <= 5; i++) {
             stars[i].classList.replace('far', 'fas');
             if (i <= index ) {
               stars[i].classList.replace('far', 'fas');
             } else {
                 stars[i].classList.replace('fas', 'far');
             }
        }

    });
});

// $.noConflict();
// $(document).ready(function () {
//     $('.logo-carousel').slick({
//         slidesToShow: 4,
//         infinite: false,
//         slidesToScroll: 1,
//         autoplay: false,
//         autoplaySpeed: 100,
//         arrows: true,
//         dots: false,
//         pauseOnHover: false,
//         responsive: [{
//             breakpoint: 768,
//             settings: {
//                 slidesToShow: 2
//             }
//         }, {
//             breakpoint: 520,
//             settings: {
//                 slidesToShow: 2
//             }
//         }]
//     });
// });




let serchInput = document.querySelector("header .search-box input");
let serchBox = document.querySelector("header .search-box");
let serchClose = document.querySelector("header  .close");
let serchinfo = document.querySelector("header .search-info");
let serchBoxinfo = document.querySelector("header .search-box-info");

// if (serchClose.classList.contains("d-none")) {
//   serchBox.addEventListener("click", () => {
//     serchInput.focus();
//     serchBox.classList.add("active");
//     serchinfo.classList.add("d-none");
//     serchBoxinfo.classList.remove("d-none");
//     serchInput.classList.remove("fs-22");
//     serchInput.classList.add("fs-16");
//     serchClose.classList.remove("d-none");
//     serchBox.classList.remove("justify-content-center");
//     serchInput.placeholder ="Where are you going?";

//   });
// } else {
//   serchBox.addEventListener("click", () => {
//     serchInput.blur();
//   });
// }
// serchInput.addEventListener("blur", () => {
//   serchBox.classList.add("justify-content-center");
//   serchInput.placeholder = placeholderValue;

//   serchBox.classList.remove("active");
//   serchInput.classList.add("fs-22");
//   serchInput.classList.remove("fs-16");
//   serchBoxinfo.classList.add("d-none");
//   serchinfo.classList.remove("d-none");
//   serchClose.classList.add("d-none");
// });
// serchClose.addEventListener("click", () => {
//   serchInput.blur();
//   serchBox.classList.remove("active");
// });



