var url = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = url + '/';

$(document).ready(function() {
  // Handle click on mobile search box to automatically open search modal with recent history for explore section
  $('.tr-explore-form .tr-mobile-where, .tr-explore-form .tr-location-label').on('click', function() {
    // Trigger click on the search field to open the modal
    $('.tr-explore-form .tr-form-where').addClass('active');
    
    // Show the search input and focus on it
    $('#searchlocation').focus();
    
    // Load and display recent search history
    loadExploreRecentHistory();
  });

  // Function to load explore recent search history
  function loadExploreRecentHistory() {
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'get',
      url: base_url + 'recenthistory',
      data: {},
      success: function(response) {
        $("#recentSearchLocation").css("display", "block");
        $(".recent-his").removeClass("d-none");
        $('#loc-list').html(response);
      }
    });
  }

  // Update mobile search box text when a location is selected and close the modal
  $(document).on('click', '#loc-list a, #loc-list li, .autoCompletewrapper a, .autoCompletewrapper li', function(e) {
    e.preventDefault();
    
    // Get the text and properly clean it by removing extra spaces
    var rawText = $(this).text();
    var selectedLocation = rawText.replace(/\s+/g, ' ').trim();
    
    // Update the mobile search box text
    $('.tr-location-label').text(selectedLocation);
    
    // Update the searchlocation input value
    $('#searchlocation').val(selectedLocation);
    
    // Force close the modal
    setTimeout(function() {
      // Close the modal
      $('.tr-explore-form .tr-form-where').removeClass('active');
      
      // Trigger the continue button click which closes the modal
      $('.tr-explore-form .tr-form-where .tr-form-btn button').trigger('click');
    }, 100);
  });
  
  // Handle the close button in mobile view
  $(document).on('click', '.tr-explore-form .tr-close-btn', function() {
    // Get the selected location from the search input
    var selectedLocation = $('#searchlocation').val().trim();
    if (selectedLocation) {
      // Update the mobile search box text
      $('.tr-location-label').text(selectedLocation);
    }
    // Close the search modal
    $('.tr-explore-form .tr-form-where').removeClass('active');
  });
});
