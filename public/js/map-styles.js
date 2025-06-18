// Map styling for water features and parks
document.addEventListener("DOMContentLoaded", function() {
  // This function will be called after the map is initialized
  window.customizeMapFeatures = function(map) {
    // Add water features (sky blue)
    fetch('https://d2ad6b4ur7yvpq.cloudfront.net/naturalearth-3.3.0/ne_10m_lakes.geojson')
      .then(response => response.json())
      .then(data => {
        L.geoJSON(data, {
          style: {
            color: "#87CEFA",
            weight: 1,
            opacity: 0.8,
            fillColor: "#87CEFA",
            fillOpacity: 0.6
          }
        }).addTo(map);
      })
      .catch(error => console.log("Water features could not be loaded"));
    
    // Add rivers (sky blue)
    fetch('https://d2ad6b4ur7yvpq.cloudfront.net/naturalearth-3.3.0/ne_10m_rivers_lake_centerlines.geojson')
      .then(response => response.json())
      .then(data => {
        L.geoJSON(data, {
          style: {
            color: "#87CEFA",
            weight: 2,
            opacity: 0.7
          }
        }).addTo(map);
      })
      .catch(error => console.log("River features could not be loaded"));
    
    // Add parks and forests (green)
    // This will only load when zoomed in to avoid performance issues
    map.on('zoomend', function() {
      if (map.getZoom() >= 10) {
        // Get current map bounds
        const bounds = map.getBounds();
        const bbox = [bounds.getSouth(), bounds.getWest(), bounds.getNorth(), bounds.getEast()].join(',');
        
        // Remove previous park layers if they exist
        map.eachLayer(function(layer) {
          if (layer.options && layer.options.parkLayer) {
            map.removeLayer(layer);
          }
        });
        
        // Fetch parks and forests data
        const overpassUrl = `https://overpass-api.de/api/interpreter?data=[out:json];(node["leisure"="park"](${bbox});way["leisure"="park"](${bbox});relation["leisure"="park"](${bbox});way["landuse"="forest"](${bbox});relation["landuse"="forest"](${bbox}));out body;>;out skel qt;`;
        
        fetch(overpassUrl)
          .then(response => response.json())
          .then(data => {
            if (data && data.elements && data.elements.length > 0) {
              // Process park data
              const parkPoints = [];
              
              data.elements.forEach(element => {
                if (element.type === "node" && element.tags && 
                    (element.tags.leisure === "park" || element.tags.landuse === "forest")) {
                  // Create circle markers for park points
                  const color = element.tags.landuse === "forest" ? "#228B22" : "#90EE90";
                  const circle = L.circle([element.lat, element.lon], {
                    radius: 100,
                    fillColor: color,
                    color: "#228B22",
                    weight: 1,
                    opacity: 0.8,
                    fillOpacity: 0.5,
                    parkLayer: true
                  }).addTo(map);
                  
                  parkPoints.push(circle);
                }
              });
            }
          })
          .catch(error => console.log("Park features could not be loaded"));
      }
    });
  };
});
