document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on a "withdate" page
    const isWithDatePage = document.querySelector('.tr-room-section-2[data-withdate="true"]');
    
    if (isWithDatePage) {
        // Get the loader and results container
        const loader = document.getElementById('loader');
        const resultsContainer = document.querySelector('.tr-room-section-2.filter-listing.responsive-container');
        
        if (loader && resultsContainer) {
            // Immediately show loader
            loader.style.display = 'block';
            
            // Function to check if hotel listings are rendered
            function checkHotelListings() {
                const hotelDetails = document.querySelectorAll('.tr-hotel-deatils[data-id]');
                if (hotelDetails.length > 0) {
                    // Results are loaded, hide loader and show actual results
                    loader.style.display = 'none';
                    
                    // Make each hotel visible as it loads
                    const hotelList = Array.from(hotelDetails);
                    hotelList.forEach((hotel, index) => {
                        hotel.style.display = 'flex';
                        hotel.style.order = index;
                    });
                    return true;
                }
                return false;
            }
            
            // Check periodically for hotel listings
            const checkInterval = setInterval(function() {
                if (checkHotelListings()) {
                    clearInterval(checkInterval);
                }
            }, 300); // Check more frequently
            
            // Fallback timeout in case API check fails or takes too long
            setTimeout(function() {
                loader.style.display = 'none';
                clearInterval(checkInterval);
            }, 30000); // 30 seconds timeout
        }
    }
});
