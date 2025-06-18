
var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';
//start search location


  //end country



  // inf scroll
let page = 1;
let loading = false;

var contid = $('.contid').text();
//var locid = $('#locid').text();
var slug = $('#slug').text();
   
// Function to load more content
function loadMoreContent() {
    if (!loading) {
        loading = true; 
        document.getElementById('loading').style.display = 'block';

        // Make an AJAX request to load more data
    // Make an AJAX request to load more data
fetch(base_url + `loadMoresightbycontinent?page=${page}&locid=${contid}`)
.then((response) => response.json())
.then((data) => {
    // Append the new HTML content to the container
    document.getElementById('getcatfilterdata').insertAdjacentHTML('beforeend', data.html);

    // Increment the page number
    page++;

    // Hide the loading indicator
    document.getElementById('loading').style.display = 'none';

    loading = false;
  if (data.mapData) {

        var mapData = JSON.parse(data.mapData); // Parse JSON data for map
        updateMapWithFilteredData(mapData);
    } else {
        // Handle the case where mapData is not present in the response
        console.error('No mapData in the response');
    }
})
.catch((error) => {
    console.error('Error loading more content:', error);
    loading = false;
});
    }
}

// Function to check if the user has scrolled to the bottom of the page
function checkScroll() {
    const contentContainer = document.getElementById('getcatfilterdata');
    if (contentContainer) {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollHeight = contentContainer.scrollHeight;
        const clientHeight = document.documentElement.clientHeight;

        if (scrollTop + clientHeight >= scrollHeight - 200) {
            loadMoreContent();
        }
    }
}


window.addEventListener('scroll', checkScroll);


loadMoreContent();

function updateMapWithFilteredData(getrest) {
    var newLocations = [];

    // Add existing markers to the list of new locations
    for (var key in markers) {
        if (markers.hasOwnProperty(key)) {
            var existingMarker = markers[key];
            var existingLatLng = existingMarker.getLatLng();
            newLocations.push([existingLatLng.lat, existingLatLng.lng]);
        }
    }

    var iconUrl = 'public/js/images/highlight-loc.png';

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

            var markerId = data.SightId; // Unique identifier for the marker

            // Check if a marker with the same SightId already exists
            if (!markers.hasOwnProperty(markerId)) {
                marker.on('mouseover', function(e) {
                    showLocation(e, data.name, data.recmd, data.cat, data.tm);
                });

                marker.on('mouseout', function(e) {
                    map.closePopup();
                });

                marker.addTo(map);
                markers[markerId] = marker; // Store the marker in the markers object
            }

            newLocations.push([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
        }
    });

    var group = new L.featureGroup(newLocations);
    map.fitBounds(group.getBounds());
}

// Rest of your code...

function showLocation(e, name, recommend,cat,tm ) {
    var marker = e.target;
  
    // Create the popup content with the name and recommend value
    var popupContent = '<strong>' + name + '</strong>' +
      '<br>Recommendss: ' + recommend + '<br>'+cat + '<br>' + tm ;
  
    marker.bindPopup(popupContent, { offset: L.point(0, -20) }).openPopup();
  }






  
