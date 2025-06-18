<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Display Route on Map</title>
  <!-- Leaflet CSS -->
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

  <!-- Leaflet JavaScript -->
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

  <style>
    #map {
      height: 400px;
    }
  </style>
</head>
<body>
  <div id="map"></div>

  <script>
    // JSON data
    const jsonData = {
      "routes": [
        {
          "geometry": {
            "coordinates": [
              [-0.136986, 51.498648],
              [-0.13789, 51.499001],
              [-0.139797, 51.499608],
              [-0.140344, 51.49981],
              [-0.14078, 51.500064],
              [-0.140865, 51.500193],
              [-0.140829, 51.500424],
              [-0.140723, 51.500661]
            ],
            "type": "LineString"
          },
          "distance": 377.3 // Distance for this route in meters
        },
        {
          "geometry": {
            "coordinates": [
              // Coordinates for the second route...
            ],
            "type": "LineString"
          },
          "distance": 500 // Distance for the second route in meters
        }
      ],
      "waypoints": [
        {
          "name": "Buckingham Gate",
          "location": [-0.136986, 51.498648]
        },
        {
          "name": "Buckingham Palace Road",
          "location": [-0.140723, 51.500661]
        }
      ]
    };

    // Create a Leaflet map
    const map = L.map('map').setView([51.498648, -0.136986], 15);

    // Add a tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add each route polyline to the map
    jsonData.routes.forEach((route, index) => {
      const routeCoordinates = route.geometry.coordinates.map(coord => L.latLng(coord[1], coord[0]));
      L.polyline(routeCoordinates, { color: getRandomColor() }).addTo(map).bindPopup(`<b>Route ${index + 1}</b><br>Distance: ${route.distance} meters`);
    });

    // Add markers for waypoints
    jsonData.waypoints.forEach(waypoint => {
      L.marker(waypoint.location.reverse()).addTo(map)
        .bindPopup(`<b>${waypoint.name}</b>`);
    });

    // Function to generate random color for each route polyline
    function getRandomColor() {
      const letters = '0123456789ABCDEF';
      let color = '#';
      for (let i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
      }
      return color;
    }
  </script>
</body>
</html>
