var url = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = url + '/';  
 
$(document).ready(function() {
  var data = $('.locids').text();    
  
  $.ajax({
      type: 'POST',
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      url: base_url + 'stays_locdata',
      data: { 'data': data },
      success: function(response) {
          $('.getdata').html(response);
      },
  });
  
  // Set up observer for calendar date selection
  setupCalendarObserver();
  
  // Set up click outside handler to close calendar
  setupClickOutsideHandlers();

  // Use event delegation to handle clicks on dynamically loaded elements
  $('.getdata').on('click', '.tr-city-wise-hotel-list .tr-city-name', function() {
      var hotelLists = $(this).next('.tr-hotel-lists');
      if (hotelLists.hasClass('open')) {
          hotelLists.removeClass('open');
      } else {
          $('.tr-hotel-lists').removeClass('open'); // Close other hotel lists
          hotelLists.addClass('open'); // Open the clicked one
      }
  });
  
  // Handle click on mobile search box to automatically open search modal with recent history
  $('.tr-mobile-where').on('click', function() {
    // Trigger click on the search field to open the modal
    $('.tr-form-where').addClass('active');
    $('.tr-form-booking-date, .tr-form-who').removeClass('active');
    
    // Show the search input and focus on it
    $('#searchhotel').focus();
    
    // Load and display recent search history
    loadRecentSearchHistory();
  });
  
  // Update mobile search box text when a location is selected and close the modal
  // Using a more direct approach to handle city selection and modal closing
  $(document).on('click', '#hotel_loc_list a, #hotel_loc_list li, .autoCompletewrapper a, .autoCompletewrapper li', function(e) {
    e.preventDefault();
    // Get the text and properly clean it by removing extra spaces
    var rawText = $(this).text();
    var selectedLocation = rawText.replace(/\s+/g, ' ').trim();
    
    console.log('Original text:', rawText);
    console.log('Cleaned text:', selectedLocation);
    
    // Update the mobile search box text
    $('.tr-location-label').text(selectedLocation);
    
    // Update the searchhotel input value
    $('#searchhotel').val(selectedLocation);
    
    // Force close the modal with multiple approaches
    setTimeout(function() {
      // Try multiple approaches to close the modal
      $('.tr-form-where').removeClass('active');
      $('.tr-form-where').hide().show(); // Force DOM refresh
      
      // Trigger the continue button click which we know closes the modal
      $('.tr-form-where .tr-form-btn button').trigger('click');
      
      console.log('Forced modal close for:', selectedLocation);
      
      // After a short delay, open the calendar modal
      setTimeout(function() {
        // Activate the date selection form
        $('.tr-form-booking-date').addClass('active');
        // Show the calendar modal
        $('#calendarsModal3').show();
        console.log('Calendar modal should now be open');
      }, 300);
    }, 100);
  });
  
  // Add direct click handler for the continue button to ensure it closes the modal
  $('.tr-form-where .tr-form-btn button').on('click', function() {
    console.log('Continue button clicked, closing modal');
    $('.tr-form-where').removeClass('active');
  });
  
  // Add a global click handler for any element in the search results
  $(document).on('click', '#recentSearchsDestination, #hotel_loc_list, .autoCompletewrapper', function(e) {
    var target = $(e.target);
    
    // If the click was on a link or list item
    if (target.is('a') || target.is('li') || target.parent().is('a') || target.parent().is('li')) {
      e.preventDefault();
      
      // Get the text content and properly clean it
      var rawText = target.text();
      var selectedLocation = rawText.replace(/\s+/g, ' ').trim();
      
      // If no text was found, try the parent element
      if (selectedLocation === '') {
        rawText = target.parent().text();
        selectedLocation = rawText.replace(/\s+/g, ' ').trim();
      }
      
      console.log('Original text (global):', rawText);
      console.log('Cleaned text (global):', selectedLocation);
      
      // Update the mobile search box text
      $('.tr-location-label').text(selectedLocation);
      
      // Update the searchhotel input value
      $('#searchhotel').val(selectedLocation);
      
      // Force close the modal with multiple approaches
      setTimeout(function() {
        // Try multiple approaches to close the modal
        $('.tr-form-where').removeClass('active');
        $('.tr-form-where').css('display', 'none').css('display', ''); // Force style refresh
        
        // Trigger the continue button click
        $('.tr-form-where .tr-form-btn button').trigger('click');
        
        console.log('Global handler: forced modal close for:', selectedLocation);
        
        // After a short delay, open the calendar modal
        setTimeout(function() {
          // Activate the date selection form
          $('.tr-form-booking-date').addClass('active');
          // Show the calendar modal
          $('#calendarsModal3').show();
          console.log('Calendar modal should now be open (global handler)');
        }, 300);
      }, 100);
    }
  });
  
  // Function to load hotel recent search history
  function loadRecentSearchHistory() {
    // Show the recent searches container
    $('#recentSearchsDestination').show();
    
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'get',
      url: base_url + 'recenthotels', // Using the hotel-specific endpoint
      data: {},
      success: function(response) {
        // Display the recent search history
        $('#recentSearchsDestination').show();
        $('#hotel_loc_list').empty().html(response);
        
        // Focus the input field after loading history
        $('#searchhotel').focus();
      }
    });
  }
  
  // Close the search modal and update the main search box when Continue button is clicked
  $(document).on('click', '.tr-form-where .tr-form-btn button', function() {
    // Get the selected location from the search input
    var selectedLocation = $('#searchhotel').val().trim();
    if (selectedLocation) {
      // Update the mobile search box text
      $('.tr-location-label').text(selectedLocation);
    }
    // Close the search modal
    $('.tr-form-where').removeClass('active');
  });
  
  // Handle click on the main search input to show recent search history
  $('#searchhotel').on('click', function() {
    loadRecentSearchHistory();
  });
  
  // Handle the close button in mobile view
  $(document).on('click', '.tr-close-btn', function() {
    // Get the selected location from the search input
    var selectedLocation = $('#searchhotel').val().trim();
    if (selectedLocation) {
      // Update the mobile search box text
      $('.tr-location-label').text(selectedLocation);
    }
    // Close the search modal
    $('.tr-form-where').removeClass('active');
  });
  
  // Function to set up click outside handlers to close modals
  function setupClickOutsideHandlers() {
    // Handle clicks outside the calendar to close it
    $(document).on('click', function(event) {
      // Check if calendar is visible
      if ($('#calendarsModal3').is(':visible')) {
        // If click is outside the calendar and not on the date inputs
        if (!$(event.target).closest('#calendarsModal3, #checkInInput3, #checkOutInput3, .tr-form-booking-date').length) {
          console.log('Click outside calendar detected, closing calendar');
          // Hide the calendar
          $('#calendarsModal3').hide();
          // Remove active class from date form section
          $('.tr-form-booking-date').removeClass('active');
        }
      }
      
      // Handle clicks outside the search modal to close it
      if ($('.tr-form-where').hasClass('active')) {
        if (!$(event.target).closest('.tr-form-where, .tr-mobile-where').length) {
          console.log('Click outside search modal detected, closing search modal');
          $('.tr-form-where').removeClass('active');
        }
      }
    });
  }
  
  // Function to set up calendar observer to detect date selection
  function setupCalendarObserver() {
    // Add a direct event listener for date selection in the calendar
    $(document).on('click', '#checkInCalendar3 .calendarBody td, #checkOutCalendar3 .calendarBody td', function() {
      console.log('Calendar date clicked');
      
      // No automatic search triggering - just log the date selection
      setTimeout(function() {
        const checkInInput = $('#checkInInput3');
        const checkOutInput = $('#checkOutInput3');
        
        console.log('Dates selected:', checkInInput.val(), checkOutInput.val());
      }, 300);
    });
    
    // Monitor the Next button in the date selection form - but don't auto-trigger search
    $('.tr-form-booking-date .tr-form-btn button').on('click', function() {
      console.log('Date selection Next button clicked');
      // Close the calendar modal
      $('#calendarsModal3').hide();
      $('.tr-form-booking-date').removeClass('active');
      
      // Activate the guest selection form if needed
      $('.tr-form-who').addClass('active');
    });
  }
  
  // Function to submit the hotel search form
  function submitHotelSearch() {
    // Get form data
    var location = $('#searchhotel').val().trim();
    var checkIn = $('#checkInInput3').val();
    var checkOut = $('#checkOutInput3').val();
    var guests = $('#totalRoomAndGuest3').val() || '1 Room, 2 Guests';
    var locationId = $('#location_id').text();
    
    console.log('Submitting search with:', location, checkIn, checkOut, guests);
    
    // Check if we have the minimum required data
    if (location && checkIn && checkOut) {
      // Create the search URL
      var searchUrl = base_url + 'hotel-search?';
      searchUrl += 'location=' + encodeURIComponent(location);
      searchUrl += '&checkin=' + encodeURIComponent(checkIn);
      searchUrl += '&checkout=' + encodeURIComponent(checkOut);
      searchUrl += '&guests=' + encodeURIComponent(guests);
      
      if (locationId) {
        searchUrl += '&location_id=' + encodeURIComponent(locationId);
      }
      
      // Navigate to the search results page
      window.location.href = searchUrl;
    } else {
      console.log('Missing required search parameters');
    }
  }
});
