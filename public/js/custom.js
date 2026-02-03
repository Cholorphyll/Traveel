var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';
//start search location


//home page search

// Lightweight timing logger for diagnostics
var TR_T0 = (typeof performance !== 'undefined' && performance.now) ? performance.now() : Date.now();
function trLog(label, data) {
  try {
    var t = (typeof performance !== 'undefined' && performance.now) ? performance.now() : Date.now();
    var delta = Math.round(t - TR_T0);
    if (data !== undefined) {
      console.log('[TR]', delta + 'ms', String(label), data);
    } else {
      console.log('[TR]', delta + 'ms', String(label));
    }
  } catch(e) { /* noop */ }
}

function trBuildSearchlistUrlFromSuggestionText(text) {
    try {
        var raw = String(text || '').trim();
        if (!raw) {
            return '';
        }

        var locIdForSearchlist = '';
        try { locIdForSearchlist = ($('#locid').text() || '').trim(); } catch (e) { locIdForSearchlist = ''; }
        var slugIdForSearchlist = getSightListingSlugIdFromUrl();

        var cleaned = raw.replace(/[“”\"]/g, '').trim();
        var q = '';
        var mNearMe = cleaned.match(/^(.+)\s+near\s+me$/i);
        var mNearLoc = cleaned.match(/^(.+)\s+near\s+(.+)$/i);
        var mInLoc = cleaned.match(/^(.+)\s+in\s+(.+)$/i);

        if (mNearMe && mNearMe[1]) {
            q = (mNearMe[1] + ' near me').trim();
        } else if (mNearLoc && mNearLoc[1] && mNearLoc[2]) {
            q = (mNearLoc[1] + ' near ' + mNearLoc[2]).trim();
        } else if (mInLoc && mInLoc[1] && mInLoc[2]) {
            q = (mInLoc[1] + ' near ' + mInLoc[2]).trim();
        } else {
            q = cleaned;
        }

        if (!q) {
            return '';
        }

        return trBuildBaseUrl('searchlist')
            + '?q=' + encodeURIComponent(q)
            + '&locId=' + encodeURIComponent(locIdForSearchlist)
            + '&slugid=' + encodeURIComponent(slugIdForSearchlist);
    } catch (e) {
        return '';
    }
}

//home page search
var searchTimeout;
// Log when the page begins unloading (navigation starting)
try {
  window.addEventListener('beforeunload', function() {
    trLog('beforeunload (navigation starting)');
  });
} catch(e) {}

$(document).on('keyup', '.search_explore', function() {
    var value = $(this).val();
    // Clear any previous search timeouts
    clearTimeout(searchTimeout);

    // Only perform search if value has at least 2 characters
    if (value.length >= 2) {
        // Set a new search timeout with reduced debounce time
        searchTimeout = setTimeout(function() {
            performSearch(value);
        }, 300); // Reduced debounce time for better responsiveness
    } else if (value.length === 0) {
        // If field is empty, show recent history
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'get',
            url: base_url + 'recenthistory',
            data: {},
            success: function(response) {
                $('#recent-search').removeClass('d-none');
                $('#loc-list').html("");
                $('#loc-list').html(response);
            }
        });
    }
});

function performSearch(value) {
    if (value.length >= 1) {
        $("#cat-list").css("display", "block");
        $(".recent-his").css("display", "block");
        
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'get',
            url: base_url + 'list-location',
            data: {
                'search': value
            },
            success: function(response) {
                var resultList = $('#searchResults');
                resultList.empty();
                $('#loc-list').html("");
                $('#recent-search').addClass('d-none');
                $('#loc-list').html(response);
            }
        });
    } else {
        $(".recent-his").removeClass("d-none");
        $("#cat-list").css("display", "block");
        $(".recent-his").css("display", "block");
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'get',
            url: base_url + 'recenthistory',
            data: {},
            success: function(response) {
                $('#recent-search').removeClass('d-none');
                var resultList = $('#searchResults');
                resultList.empty();
                $('#loc-list').html("");
                $('#loc-list').html(response);
            }
        });
    }
}



$(document).ready(function() {
  
  $('.search_explore').on('click', function() {
   
    var value = $(this).val();
    if (value.length <= 0) {
      $("#recentSearchLocation").css("display", "block");

      $("#cat-list").css("display", "block");

      
    
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'get',
        url: base_url + 'recenthistory',
        data: {
          // Your data here
        },
        success: function(response) {
          $("#recentSearchLocation").css("display", "block");
          $(".recent-his").removeClass("d-none");
          var resultList = $('#searchResults');
          resultList.empty();
          $('#loc-list').html(response);
          
        }
      });
    }
  });
});






//end home page search


var searchTimeout;

$(document).on('keyup', '#searchlocation', function() {
    var value = $(this).val();
  
    // Clear any previous search timeouts
    clearTimeout(searchTimeout);
    
    // Make sure the search results container is visible
    showSearchResultsContainer();
    
    // Set a new search timeout
    searchTimeout = setTimeout(function() {
        performSearch(value);
    }, 500); // Adjust the debounce time as needed 
});

function performSearch(value) {
  if (value.length >= 1) {
      // Ensure search results container is visible before AJAX call
      showSearchResultsContainer();
      
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: base_url + 'list-location',
          data: {
              'search': value
          },
          success: function(response) {
              var resultList = $('#searchResults');
              resultList.empty();
              $('#loc-list').html("");
              $('#recent-search').addClass('d-none');
              $('#loc-list').html(response);
              
              // Ensure results remain visible after AJAX response
              showSearchResultsContainer();
          }
      });
  } else {
      // Show recent history
      showSearchResultsContainer();
      $.ajax({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: base_url + 'recenthistory',
          data: {},
          success: function(response) {
              $('#recent-search').removeClass('d-none');
              var resultList = $('#searchResults');
              resultList.empty();
              $('#loc-list').html("");
              $('#loc-list').html(response);
              
              // Ensure results remain visible after AJAX response
              showSearchResultsContainer();
          }
      });
  }
}



$(document).ready(function() {
  
  $('#searchlocation').on('click', function() {
   
    var value = $(this).val();
    if (value.length <= 0) {
      $("#recentSearchLocation").css("display", "block");

      $("#cat-list").css("display", "block");

      
    
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'get',
        url: base_url + 'recenthistory',
        data: {
          // Your data here
        },
        success: function(response) {
          $("#recentSearchLocation").css("display", "block");
          $(".recent-his").removeClass("d-none");
          var resultList = $('#searchResults');
          resultList.empty();
          $('#loc-list').html(response);
          
        }
      });
    }
  });
});
//second function 
$(document).on('click', '.tr-more-price.tr', function() {
    const $this = $(this);
  
    const $priceListsSection = $this.closest('.tr-hotel-price-lists');
  
    if ($this.hasClass('active')) {
        // Hide the modal and remove active state if already active
        $this.removeClass('active');
        $(".more-options-modal").remove();
    } else {
  
        $(".tr-hotel-price-lists .tr-more-price.tr").removeClass('active');
        $(".tr-hotel-price-lists .more-options-modal").remove();
  
        $this.addClass('active');
        
  
        const $morePricesContainer = $priceListsSection.find('.more-prices-container');
        if ($morePricesContainer.length) {
            $morePricesContainer.hide(); // Hide the container
        }
  
    
        const $priceListsMore = $morePricesContainer.find('.tr-hotel-price-list').clone();
  
  
        $priceListsMore.each(function() {
            $(this).css('display', 'block'); // Remove any display: none
        });
  
   
        const $priceListsModal = $("<div class='more-options-modal'></div>");
        $priceListsSection.append($priceListsModal);
  
        
        const $modalContent = $("<div class='tr-hotel-price-lists'></div>");
        $priceListsModal.append($modalContent);
        $modalContent.append($priceListsMore);
  
     
        $priceListsModal.show();
    }
  });
  
  
  $(document).on('click', function(event) {
    if ($('.more-options-modal').length) {
        if (!$(event.target).closest('.more-options-modal, .tr-more-price.tr').length) {
            $('.tr-more-price.tr').removeClass('active');
            $(".more-options-modal").remove();
        }
    }
  });


//first function 


$(document).on('click', '.tr-more-prices.ls', function() {
    const $this = $(this);

    const $priceListsSection = $this.closest('.tr-hotel-price-lists');
  
    if ($this.hasClass('active')) {
        // Hide the modal and remove active state if already active
        $this.removeClass('active');
        $(".more-options-modals").remove();
    } else {
  
        $(".tr-hotel-price-lists .tr-more-prices").removeClass('active');
        $(".tr-hotel-price-lists .more-options-modals").remove();
  
        $this.addClass('active');
        
  
        const $morePricesContainer = $priceListsSection.find('.more-prices-containers');
        if ($morePricesContainer.length) {
            $morePricesContainer.hide(); // Hide the container
        }
  
    
        const $priceListsMore = $morePricesContainer.find('.tr-hotel-price-list').clone();
  
  
        $priceListsMore.each(function() {
            $(this).css('display', 'block'); // Remove any display: none
        });
  
   
        const $priceListsModal = $("<div class='more-options-modals'></div>");
        $priceListsSection.append($priceListsModal);
  
        
        const $modalContent = $("<div class='tr-hotel-price-lists'></div>");
        $priceListsModal.append($modalContent);
        $modalContent.append($priceListsMore);
  
     
        $priceListsModal.show();
    }
  });
  
  
  $(document).on('click', function(event) {
    if ($('.more-options-modals').length) {
        if (!$(event.target).closest('.more-options-modals, .tr-more-prices').length) {
            $('.tr-more-prices').removeClass('active');
            $(".more-options-modals").remove();
        }
    }
  });

//first function 
//open model
$(document).ready(function() {
    const urlParams = new URLSearchParams(window.location.search);
    const openModal = urlParams.get('openModal');
    const token = urlParams.get('token');

    if (openModal) {
        $(`#${openModal}`).modal('show');       
        if (openModal == 'resetPswdModal' && token) {
            $('#token').val(token);
        }

        // Remove only the openModal parameter while keeping other parameters intact
        urlParams.delete('openModal');

        const cleanUrl = window.location.origin + window.location.pathname + '?' + urlParams.toString();
        window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
    }
});






$(document).ready(function() {
  var searchResultsContainer = $('.recent-his');

  $(document).on('click', function(event) {
    var targetElement = $(event.target);
    
    // Only hide the search results if clicking outside both the search input and results container
    if (!targetElement.closest('.explore-search').length && 
        !targetElement.closest('#searchlocation').length && 
        !targetElement.closest('.recent-his').length && 
        !targetElement.closest('#loc-list').length) {
      searchResultsContainer.hide();
      $('#recentSearchLocation').css('display', 'none');
      $('#cat-list').css('display', 'none');
    }
  });
});

// Auto-opening search box and showing recent history functionality
$(document).ready(function() {
  // Function to load recent search history for Explore tab
  function loadExploreRecentHistory() {
    $("#recentSearchLocation").css("display", "block");
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'get',
      url: base_url + 'recenthistory',
      data: {},
      success: function(response) {
        $("#recentSearchLocation").css("display", "block");
        $('#loc-list').html(response);
        // Focus the input field after loading history
        $('.search_explore').focus();
      }
    });
  }
  
  // Function to load recent search history for Hotel tab
  function loadHotelRecentHistory() {
    trLog('loadHotelRecentHistory start');
    $("#recentSearchsDestination").css("display", "block");
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'get',
      url: base_url + 'recenthotels',
      data: {},
      success: function(response) {
        $("#recentSearchsDestination").css("display", "block");
        trLog('loadHotelRecentHistory success', { length: (response||[]).length });
        
        // Clear existing list
        $('#hotel_loc_list').empty();
        
        // Process the response for hotel search
        if (response.length > 0) {
          var resultList = $('#hotel_loc_list');
          
          // Generate HTML for each item
          var html = '';
          $.each(response, function(index, item) {
            var locationIconSvg = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M8.00016 8.66634C9.47292 8.66634 10.6668 7.47243 10.6668 5.99967C10.6668 4.52692 9.47292 3.33301 8.00016 3.33301C6.5274 3.33301 5.3335 4.52692 5.3335 5.99967C5.3335 7.47243 6.5274 8.66634 8.00016 8.66634Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M8 15.333C10.6667 12.6663 13.3333 10.3153 13.3333 6.66634C13.3333 3.72301 10.9433 1.33301 8 1.33301C5.05667 1.33301 2.66667 3.72301 2.66667 6.66634C2.66667 10.3153 5.33333 12.6663 8 15.333Z" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            var hotelIconSvg = '<svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1.3335 14.6663V5.99967C1.3335 5.64605 1.47397 5.30691 1.72402 5.05686C1.97407 4.80681 2.31321 4.66634 2.66683 4.66634H13.3335C13.6871 4.66634 14.0263 4.80681 14.2763 5.05686C14.5264 5.30691 14.6668 5.64605 14.6668 5.99967V14.6663" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M14.6668 14.6667H1.3335" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M8.6665 4.66699V1.33366H4.6665V4.66699" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.6665 8.66699H6.6665" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.3335 8.66699H11.3335" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M4.6665 11.333H6.6665" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/><path d="M9.3335 11.333H11.3335" stroke="#222222" stroke-linecap="round" stroke-linejoin="round"/></svg>';
          
            // Determine which icon to use - ensure hotel is treated as a number
            var isHotel = parseInt(item.hotel) === 1;
            var svgIcon = isHotel ? hotelIconSvg : locationIconSvg;
          
            // Parse location name and details
            var parts = item.value.split(',').map(part => part.trim()).filter(part => part);
            var name = parts[0];
            var details = parts.slice(1).join(', ');
          
            // Ensure valid `locationId`
            let locationId = item.id;
          
            // Properly formatted slug based on hotel type
            let slugPath;
            if (isHotel) {
              // For hotels (hd prefix) - ensure hd- prefix is added
              // Remove any existing prefixes first
              let cleanSlug = `${item.id}-${item.hotelid || ''}-${item.Slug || ''}`;
              cleanSlug = cleanSlug.replace(/^hd-/, '').replace(/^ho-/, '');
              slugPath = `hd-${cleanSlug}`;
            } else {
              // For locations (ho prefix)
              let cleanSlug = `${locationId}-${item.Slug || ''}`;
              cleanSlug = cleanSlug.replace(/^ho-/, '').replace(/^hd-/, '');
              slugPath = `ho-${cleanSlug}`;
            }
          
            // Create HTML for this item - wrap in li element with data attributes
            html += `<li data-id="${item.id}" data-slug="${slugPath}" data-hotel="${item.hotel}" data-locationid="${locationId}" data-hotelid="${item.hotelid || ''}">`;
            html += '<div class="tr-place-info">';
            html += '<div class="tr-location-icon">' + svgIcon + '</div>';
            html += '<div class="tr-location-info">';
            html += '<div class="tr-hotel-name">' + name + '</div>';
            html += '<div class="tr-hotel-city">' + details + '</div>';
            html += '</div></div>';
            html += '</li>';
          });
        
          // Add the HTML to the container
          resultList.html(html);
          trLog('rendered recent hotels', { count: $('#hotel_loc_list li').length });
          
          // Remove previous click handlers to avoid duplicates
          $('#hotel_loc_list li').off('click');
        
          // Add click handlers to the li items
          $('#hotel_loc_list li').on('click', function() {
            trLog('recent hotel item clicked');
            var id = $(this).data('id');
            var slug = $(this).data('slug');
            var hotel = $(this).data('hotel');
            var name = $(this).find('.tr-hotel-name').text();
            var details = $(this).find('.tr-hotel-city').text();
            var locationText = name + (details ? ', ' + details : '');
            var locationId = $(this).data('locationid') || $(this).data('id'); // Ensure valid locationId
            var hotelId = $(this).data('hotelid') || '';
            trLog('recent hotel details', { slug: slug, hotel: String(hotel), locationId: locationId });
          
            // Set the values in the form
            $('#searchhotel').val(locationText);
            $('#location_id').text(locationId);
            $('#slug').text(slug);
            $('#hotel').text(hotel);
          
            // Also update the mobile location text
            $('.location-name-mobile').text(locationText);
          
            // Store the selected location in a data attribute for persistence
            $('#searchhotel').attr('data-selected-location', locationText);
            
            // Make an AJAX call to ensure the selection is saved in session with correct hotel type
            $.ajax({
              headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: 'get',
              url: base_url + 'list_hotelsloc',
              data: {
                'search': name,  // Use the exact name to ensure it matches
                'city': details.split(',')[0] || '' // Use first part of details as city if available
              },
              success: function() { trLog('Hotel search history updated'); }
            });
          
            // Hide the search results completely
            $('#recentSearchsDestination').hide().css('display', 'none');
            $('.hotel_recent_his').addClass('d-none');
            $('.tr-recent-searchs-modal').css('display', 'none');
            $('#hotel_loc_list').css('display', 'none');
          
            // Simulate a click on the Continue button to close the modal
            $('.tr-form-where .tr-btn.tr-mobile').trigger('click');
          
            // Open the calendar automatically
            setTimeout(function() {
              $('#checkInInput1').trigger('click');
              $('#checkInInput1').focus();
              $('#calendarsModal').show();
              trLog('calendar open triggered (recent history)');
            }, 100);
          });
        }
        
        // Focus the input field after loading history
        $('#searchhotel').focus();
      }
    });
  }
  
  // For Explore tab - mobile view
  $('.tr-mobile-where').on('click', function() {
    // Load recent history and focus input
    setTimeout(function() {
      loadExploreRecentHistory();
    }, 10);
  });
  
  // For the location label
  $('.tr-location-label').on('click', function() {
    setTimeout(function() {
      loadExploreRecentHistory();
    }, 10);
  });
  
  // For Hotel tab - mobile view
  $('.tr-mobile-when').on('click', function() {
    setTimeout(function() {
      loadHotelRecentHistory();
    }, 10);
  });
  
  // For Hotel location label
  $('.location-name-mobile').on('click', function() {
    setTimeout(function() {
      loadHotelRecentHistory();
    }, 10);
  });
  
  // Direct override of the click handlers for mobile elements
  $('.tr-mobile').on('click', function() {
    // Determine which form is being opened
    setTimeout(function() {
      if ($('#exploreForm').hasClass('open')) {
        loadExploreRecentHistory();
      } else if ($('#hotelForm').hasClass('open')) {
        loadHotelRecentHistory();
      }
    }, 10);
  });
  
  // Handle the mobile close button click
  $('.tr-mobile.tr-close-btn').on('click', function() {
    setTimeout(function() {
      if ($(this).closest('#exploreForm').length) {
        loadExploreRecentHistory();
      } else if ($(this).closest('#hotelForm').length) {
        loadHotelRecentHistory();
      }
    }, 10);
  });
});

$(document).ready(function() {
  // Additional initialization code can go here
  
  // Hide the Next button on the calendar
  $('.tr-form-booking-date .tr-btn.tr-mobile').hide();
  
  // Add click handler for the Continue button in the hotel search form
  // This is now only a fallback in case the user doesn't select a location
  $('.tr-form-where .tr-btn').on('click', function() {
    // If we already have a selected location and both dates, navigate immediately
    var slug = $('#slug').text().trim();
    var ci = $('#checkInInput1').val();
    var co = $('#checkOutInput1').val();
    if (slug && ci && co) {
      trLog('search CTA immediate -> executeSearch');
      executeSearch(ci, co);
      return;
    }
    
    // Hide the current form section
    $('.tr-form-where').removeClass('open');
    
    // Show the calendar section
    $('.tr-form-booking-date').addClass('open');
    
    // Trigger a click on the check-in input to open the calendar
    setTimeout(function() {
      $('#checkInInput1').trigger('click');
      $('#checkInInput1').focus();
      $('#calendarsModal').show();
      trLog('calendar open (continue button)');
    }, 100);
  });
  
  // AGGRESSIVE SOLUTION: Set up a polling mechanism to check for date selections
  let dateCheckInterval;
  let previousCheckIn = '';
  let previousCheckOut = '';
  let searchTriggered = false;
  
  // Function to force search when both dates are selected
  function forceSearch() {
    const checkInValue = $('#checkInInput1').val();
    const checkOutValue = $('#checkOutInput1').val();
    
    // Only proceed if both values are present and at least one has changed
    if (checkInValue && checkOutValue && 
        (checkInValue !== previousCheckIn || checkOutValue !== previousCheckOut) && 
        !searchTriggered) {
      
      searchTriggered = true;
      
      // Update previous values
      previousCheckIn = checkInValue;
      previousCheckOut = checkOutValue;
      
      // Force close the calendar
      $('#calendarsModal').hide();
      $('.tr-calenders-modal').hide();
      $('.tr-form-booking-date').removeClass('open');
      trLog('dates selected', { checkInValue: checkInValue, checkOutValue: checkOutValue });
      
 var hotel = $('#hotel').text().trim();
if (hotel === "1") {
    try {
        var checkinDate = new Date(checkInValue);
        var checkoutDate = new Date(checkOutValue);
        
        if (!isNaN(checkinDate.getTime()) && !isNaN(checkoutDate.getTime())) {
            var formattedCheckin = checkinDate.getFullYear() + '-' + 
                                ('0' + (checkinDate.getMonth() + 1)).slice(-2) + '-' + 
                                ('0' + checkinDate.getDate()).slice(-2);
                                
            var formattedCheckout = checkoutDate.getFullYear() + '-' + 
                                ('0' + (checkoutDate.getMonth() + 1)).slice(-2) + '-' + 
                                ('0' + checkoutDate.getDate()).slice(-2);
                                
            trLog('forceSearch -> executeSearch (formatted)');
            executeSearch(formattedCheckin, formattedCheckout);
        } else {
            trLog('forceSearch -> executeSearch (raw)');
            executeSearch(checkInValue, checkOutValue);
        }
    } catch (e) {
        trLog('date formatting error', { error: String(e) });
        executeSearch(checkInValue, checkOutValue);
    }
} else {
    trLog('forceSearch (location) -> executeSearch');
    executeSearch(checkInValue, checkOutValue);
}
      
      // Reset the flag after some time
      setTimeout(function() {
        searchTriggered = false;
        trLog('searchTriggered reset');
      }, 3000);
    }
  }  
  // Function to execute the search
  function executeSearch(checkin, checkout) {
    // Get the slug and hotel type from the hidden spans
    var slug = $('#slug').text().trim();
    var hotel = $('#hotel').text().trim();
    var locationid = $('#location_id').text().trim();
    var lid = $('.loc_id').text().trim() || locationid; // Use location_id as fallback
    
    // Check if we have a valid slug
    if (!slug) {
      alert('Please select a location before searching');
      return;
    }
    
    var rooms = $('#totalRoom').val() || 1;
    var guest = $('#totalAdultsGuest').val() || 2;
    
    // Format dates
    let formattedCheckin = checkin.replace(/\s+/g, '-');
    let fcheckout = checkout.replace(/\s+/g, '-');
    trLog('executeSearch inputs', { slug: slug, hotel: hotel, locationid: locationid, lid: lid, rooms: rooms, guest: guest, checkin: formattedCheckin, checkout: fcheckout });
    
    // Build URL based on hotel type
  var url;
  if (hotel === "1") {
    // For hotels, ensure the URL starts with hd-
    // First remove any existing prefixes
    slug = slug.replace(/^(hd-|ho-)/, '');
    url = 'hd-' + slug + '?checkin=' + formattedCheckin + '&checkout=' + fcheckout;
  } else {
    url = 'ho-' + slug + 
        '?checkin=' + formattedCheckin +
        '&checkout=' + fcheckout +
        '&locationid=' + locationid +
        '&lid=' + lid +
        '&rooms=' + rooms +
        '&guest=' + guest;
  }  
    
    // Check for duplicate 'ho-' in the URL and remove one if found
    if (url.includes('ho-ho-')) {
      url = url.replace('ho-ho-', 'ho-');
    }
    trLog('navigate url built', { url: url });
    
    window.location.href = url;
  }
  
  // Start the polling when calendar is shown
  $(document).on('click', '#checkInInput1, #checkOutInput1', function() {
    // Clear any existing interval
    clearInterval(dateCheckInterval);
    
    // Set up a new interval to check for date selections every 300ms
    dateCheckInterval = setInterval(forceSearch, 300);
    trLog('dateCheckInterval started');
  });
  
  // Add direct event handlers for calendar date cells
  $(document).on('click', '#calendarPair1 .calendarBody td', function() {
    // Only proceed if this is a valid date cell (not disabled or empty)
    if (!$(this).hasClass('disabled') && !$(this).hasClass('t-disabled') && $(this).text().trim() !== '') {
      // Force a check after a short delay
      setTimeout(forceSearch, 500);
    }
  });

});

  $('#searchlocation').on('click', function() {
    searchResultsContainer.show(); 
  });
  //end search

// start hotels search 

var searchTimeout;

// Add this code before the keyup event handler for #searchhotel
$(document).ready(function() {
    // Restore saved location when form is opened
    var savedLocation = $('#searchhotel').attr('data-selected-location');
    if (savedLocation) {
      $('#searchhotel').val(savedLocation);
    }
    
    $('#searchhotel').on('click', function() {
      var value = $(this).val();
      if (value.length <= 0) {
        trLog('recenthotels AJAX start');
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'get',
          url: base_url + 'recenthotels',
          data: {},
          success: function(response) {
            $('#hotel_loc_list').empty(); // Clear existing list
            trLog('recenthotels AJAX success', { length: (response||[]).length });
            
            if (response.length > 0) {
              var resultList = $('#hotel_loc_list');
              
              $.each(response, function(index, item) {
                let svgIcon = item.hotel == 1 ? hotelIconSvg() : locationIconSvg();
                
                let parts = item.value.split(',').map(part => part.trim()).filter(part => part);
                let name = parts[0];
                let details = parts.slice(1).join(', ');
                
                // Ensure valid `locationId`
                let locationId = item.id;
                
                // Properly formatted slug
                let slugPath = item.hotel == 1
                  ? `${item.id}-${item.hotelid}-${item.Slug}`
                  : `ho-${locationId}-${item.Slug}`;
                
                resultList.append(
                  `<li data-id="${item.id}" data-slug="${slugPath}" data-hotel="${item.hotel}" data-locationid="${locationId}">
                    <div class="tr-place-info">
                      <div class="tr-location-icon">${svgIcon}</div>
                      <div class="tr-location-info">
                        <div class="tr-hotel-name">${name}</div>
                        <div class="tr-hotel-city">${details}</div>
                      </div>
                    </div>
                  </li>`
                );
              });
              trLog('rendered recenthotels list', { count: $('#hotel_loc_list li').length });
              
              var searchInput = $('#searchhotel');
              var slugElement = $('#slug');
              var locationid = $('#location_id');
              var hotel = $('#hotel');
              var mobilelocation = $('.location-name-mobile');
              
              resultList.on('click', 'li', function() {
                trLog('recenthotels suggestion clicked');
                var hoteldata = $(this).data('hotel');
                hotel.text(hoteldata);
                
                var name = $(this).find('.tr-hotel-name').text();
                var details = $(this).find('.tr-hotel-city').text();
                var locationText = name + (details ? ', ' + details : '');
                var slug = $(this).data('slug');
                var id = $(this).data('id');
                var locationId = $(this).data('locationid') || $(this).data('id');
                trLog('recenthotels suggestion details', { slug: slug, hotel: String(hoteldata), locationId: locationId });
                
                searchInput.val(locationText);
                mobilelocation.text(locationText);
                slugElement.text(slug);
                locationid.text(locationId);
                
                $('.hotel_recent_his').addClass('d-none');
                $("#hotel_loc_list").css("display", "none");
                $("#recentSearchsDestination").css("display", "none");
                
                // Simulate a click on the Continue button to close the modal
                $('.tr-form-where .tr-btn.tr-mobile').trigger('click');
                
                // Open the calendar automatically
                setTimeout(function() {
                    $('#checkInInput1').trigger('click');
                    $('#checkInInput1').focus();
                    $('#calendarsModal').show();
                    trLog('calendar open triggered (recenthotels click)');
                }, 100);
              });
              
              $(".tr-recent-searchs-modal").css("display", "block");
              $("#recentSearchsDestination").css("display", "block");
              $("#hotel_loc_list").css("display", "block");
              $('.hotel_recent_his').removeClass('d-none');
            } else {
              $('#hotel_loc_list').html('<li>No results found</li>');
            }
          }
        });
      }
    });
});

$(document).on('keyup', '#searchhotel', function() {
    var value = $(this).val();
    var city = $('#location-name').text();
    // Clear any previous search timeouts
    clearTimeout(searchTimeout);
    trLog('hotel keyup', { valueLen: (value||'').length, city: city });
    
    // Set a new search timeout
    searchTimeout = setTimeout(function() {
        trLog('hotel debounced -> hotperformSearch');
        hotperformSearch(value,city);
    }, 500); // Adjust the debounce time as needed 
});

function hotperformSearch(value, city) {
    trLog('hotperformSearch start', { value: value, city: city });
    if (value.length >= 1) {
        $(".tr-recent-searchs-modal").css("display", "block");
        $("#recentSearchsDestination").css("display", "block");
        $("#hotel_loc_list").css("display", "block");

        trLog('AJAX list_hotelsloc start');
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'get',
            url: base_url + 'list_hotelsloc',
            data: {
                'search': value,
                'city': city
            },
            success: function (response) {
                $('#hotel_loc_list').empty(); // Clear existing list
                trLog('AJAX list_hotelsloc success', { length: (response||[]).length });
                if (response.length > 0) {
                    var resultList = $('#hotel_loc_list');

                    $.each(response, function (index, item) {
                        let svgIcon = item.hotel == 1 ? hotelIconSvg() : locationIconSvg();
                        
                        let parts = item.value.split(',').map(part => part.trim()).filter(part => part);
                        let name = parts[0];
                        let details = parts.slice(1).join(', ');

                        // Ensure valid `locationId`
                        let locationId = item.id;

                        // Properly formatted slug
                        let slugPath;
                        if (parseInt(item.hotel) === 1) {
                            // For hotel type (hotel = 1) - ensure hd- prefix is added
                            // Remove any existing prefixes first
                            let cleanSlug = `${item.id}-${item.hotelid || ''}-${item.Slug || ''}`;
                            cleanSlug = cleanSlug.replace(/^hd-/, '').replace(/^ho-/, '');
                            slugPath = `hd-${cleanSlug}`;
                        } else {
                            // For location type (hotel = 0)
                            let cleanSlug = `${locationId}-${item.Slug || ''}`;
                            cleanSlug = cleanSlug.replace(/^ho-/, '').replace(/^hd-/, '');
                            slugPath = `ho-${cleanSlug}`;
                        }

                        resultList.append(
                            `<li data-id="${item.id}" data-slug="${slugPath}" data-hotel="${item.hotel}" data-locationid="${locationId}" data-hotelid="${item.hotelid || ''}">
                                <div class="tr-place-info">
                                    <div class="tr-location-icon">${svgIcon}</div>
                                    <div class="tr-location-info">
                                        <div class="tr-hotel-name">${name}</div>
                                        <div class="tr-hotel-city">${details}</div>
                                    </div>
                                </div>
                            </li>`
                        );
                    });
                    trLog('rendered hotel suggestions', { count: $('#hotel_loc_list li').length });

                    var searchInput = $('#searchhotel');
                    var slugElement = $('#slug'); 
                    var locationid = $('#location_id'); 
                    var hotel = $('#hotel'); 
                    var mobilelocation = $('.location-name-mobile');   

                    // Remove previous click handlers to avoid duplicates
                    resultList.off('click', 'li');
                    
                    resultList.on('click', 'li', function() {
                        trLog('hotel suggestion clicked start');
                        var hoteldata = $(this).data('hotel');     
                        hotel.text(hoteldata);
                        
                        var name = $(this).find('.tr-hotel-name').text();
                        var details = $(this).find('.tr-hotel-city').text();
                        var locationText = name + (details ? ', ' + details : '');
                        var slug = $(this).data('slug');  
                        var id = $(this).data('id');                  
                        var locationId = $(this).data('locationid') || $(this).data('id'); // Ensure valid locationId
                        var hotelId = $(this).data('hotelid') || '';
                        trLog('hotel suggestion selected', { slug: slug, hotel: String(hoteldata), locationId: locationId });

                        searchInput.val(locationText);   
                        mobilelocation.text(locationText);       
                        slugElement.text(slug);
                        locationid.text(locationId);
                        
                        // Store the selected location in a data attribute for persistence
                        searchInput.attr('data-selected-location', locationText);

                        // Make an AJAX call to ensure the selection is saved in session with correct hotel type
                        $.ajax({
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            type: 'get',
                            url: base_url + 'list_hotelsloc',
                            data: {
                                'search': name,  // Use the exact name to ensure it matches
                                'city': city
                            },
                            success: function() { trLog('history updated (list_hotelsloc)'); },
                            error: function(xhr) { trLog('history update error', { status: xhr && xhr.status }); }
                        });

                        $('.hotel_recent_his').addClass('d-none');
                        $("#hotel_loc_list").css("display", "none");
                        $("#recentSearchsDestination").css("display", "none");

                        // Simulate a click on the Continue button to close the modal
                        $('.tr-form-where .tr-btn.tr-mobile').trigger('click');
                        
                        // Open the calendar automatically
                        setTimeout(function() {
                            $('#checkInInput1').trigger('click');
                            $('#checkInInput1').focus();
                            $('#calendarsModal').show();
                            trLog('calendar open triggered after selection');
                        }, 100);
                    });

                    $('.hotel_recent_his').removeClass('d-none');
                } else {
                    $('#hotel_loc_list').html('<li>No results found</li>');
                }
            },
            error: function(xhr) { trLog('AJAX list_hotelsloc error', { status: xhr && xhr.status }); }
        });
    } else {
        // If search field is empty, load recent history
        loadHotelRecentHistory();
    }
}


// Functions for SVG icons
function hotelIconSvg() {
    return `<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#clip0_1522_9239)">
            <path d="M17.2159 4.44531H12.7715V5.55642H17.2159V17.2231H12.7715V18.3342H18.327V5.55642C18.327 5.26174 18.21 4.97912 18.0016 4.77075C17.7932 4.56238 17.5106 4.44531 17.2159 4.44531Z" fill="#222222"/>
            <path d="M11.0507 1.66797H3.40629C3.09393 1.66797 2.79435 1.79206 2.57348 2.01293C2.3526 2.23381 2.22852 2.53338 2.22852 2.84575V18.3346H12.2285V2.84575C12.2285 2.53338 12.1044 2.23381 11.8836 2.01293C11.6627 1.79206 11.3631 1.66797 11.0507 1.66797Z" fill="#222222"/>
        </g>
    </svg>`;
}

function locationIconSvg() {
    return `<svg width="15" height="16" viewBox="0 0 15 16" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g clip-path="url(#clip0_441_2146)">
            <path d="M13.125 6.73828C13.125 11.1133 7.5 14.8633 7.5 14.8633C7.5 14.8633 1.875 11.1133 1.875 6.73828C1.875 5.24644 2.46763 3.8157 3.52252 2.76081C4.57742 1.70591 6.00816 1.11328 7.5 1.11328C8.99184 1.11328 10.4226 1.70591 11.4775 2.76081C12.5324 3.8157 13.125 5.24644 13.125 6.73828Z" stroke="#5E5E5E" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M7.5 8.61328C8.53553 8.61328 9.375 7.77382 9.375 6.73828C9.375 5.70275 8.53553 4.86328 7.5 4.86328C6.46447 4.86328 5.625 5.70275 5.625 6.73828C5.625 7.77382 6.46447 8.61328 7.5 8.61328Z" stroke="#5E5E5E" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
    </svg>`;
}

//end recent history search 
function formatHDDate(date) {
    if (!date) return '';
    var d = new Date(date);
    return d.getFullYear() + '-' + 
           ('0' + (d.getMonth() + 1)).slice(-2) + '-' + 
           ('0' + d.getDate()).slice(-2);
}

// Add this event handler for calendar date selection
$(document).on('change', '#checkin, #checkout', function() {
    var checkin = $('#checkin').val();
    var checkout = $('#checkout').val();
    
    // Only format dates for HD type URLs
    if (window.location.pathname.includes('/hd-')) {
        // Convert date format from DD-MMM-YYYY to YYYY-MM-DD
        if (checkin) {
            var parts = checkin.split('-');
            if (parts.length === 3) {
                var day = parts[0];
                var month = parts[1];
                var year = parts[2];
                checkin = year + '-' + month + '-' + day;
            }
        }
        
        if (checkout) {
            var parts = checkout.split('-');
            if (parts.length === 3) {
                var day = parts[0];
                var month = parts[1];
                var year = parts[2];
                checkout = year + '-' + month + '-' + day;
            }
        }
        
        // Update URL with new date format
        var url = window.location.pathname;
        if (checkin && checkout) {
            url += '?checkin=' + checkin + '&checkout=' + checkout;
            
            // Update the URL without reloading the page
            history.pushState({}, '', url);
        }
    }
});

$(document).ready(function() {
    // Show the popup when the search input is focused
    $('#searchhotel').focus(function() {
        $('.hotel_recent_his').removeClass('d-none');
        $('#recentSearchsDestination').css('display', 'block');
        $('#hotel_loc_list').css('display', 'block');
    });

    // Hide the popup when the user clicks outside of it
    $(document).mouseup(function(e) {
        var container = $('.hotel_recent_his');
        var searchInput = $('#searchhotel');
        
        if (!container.is(e.target) && !searchInput.is(e.target) && container.has(e.target).length === 0) {
            $('.hotel_recent_his').addClass('d-none');
            $('#recentSearchsDestination').css('display', 'none');
            $('.tr-recent-searchs-modal').css('display', 'none');
            $('#hotel_loc_list').css('display', 'none');
        }
    });
});

$(document).on('click', '.custom-link', function(event) {
    event.preventDefault(); // Prevent the default link behavior
    var url = $(this).attr('href');
    // Handle the link click, e.g., by navigating to the URL
    window.location.href = url;
});
//end hotel search



//search hotel list
// Function to open the calendar automatically
function openCalendarAutomatically() {
    // Try different methods to open the calendar
    // Method 1: Click on the date input
    $('.t-input-check-in').trigger('click');
    
    // Method 2: Add and remove t-datepicker-open class
    $('.t-datepicker').addClass('t-datepicker-open');
    
    // Method 3: Focus on the date input
    $('.t-input-check-in').focus();
    
    // If there's a specific calendar toggle button, click it
    $('.calendar-toggle, .date-picker-toggle').trigger('click');
}

$(document).on('click', '.filter-chackinout', function () {
    // Store a flag to indicate we should open the calendar after form closes
    window.shouldOpenCalendar = true;
    
    var slug = $('#slug').text().trim();
    var hotel = $('#hotel').text().trim();
    var checkin = $('#checkInInput1').val();
    var checkout = $('.t-input-check-out').val();

    // Function to format dates to YYYY-MM-DD for HD URLs
    function formatHDDate(dateStr) {
        if (!dateStr) return '';
        var dateObj = new Date(dateStr);
        return dateObj.getFullYear() + '-' + 
               ('0' + (dateObj.getMonth() + 1)).slice(-2) + '-' + 
               ('0' + dateObj.getDate()).slice(-2);
    }

    // If it's an HD URL, format dates to YYYY-MM-DD
    if (window.location.pathname.includes('/hd-')) {
        // Format both dates
        checkin = formatHDDate(checkin);
        checkout = formatHDDate(checkout);
    }

    // If dates are still not in YYYY-MM-DD format, try to parse them
    if (!window.location.pathname.includes('/hd-')) {
        if (checkin && !checkin.includes('-')) {
            var dateObj = new Date(checkin);
            checkin = formatHDDate(checkin);
        }
        if (checkout && !checkout.includes('-')) {
            var dateObj = new Date(checkout);
            checkout = formatHDDate(checkout);
        }
    }

    var currentDate = new Date();
    var cin = new Date();
    var cout = new Date();
    
    var sessionData = $('#sessionData').data() || {}; 
    var sessionCheckin = sessionData.checkin || ''; 
    var sessionCheckout = sessionData.checkout || ''; 
    var sessionGuest = sessionData.guest || ''; 
    var sessionRooms = sessionData.rooms || ''; 
    var sessionSlug = sessionData.slug || ''; 

    if (checkin == "") {
        checkin = sessionCheckin;
    }
    if (checkout == "") {
        checkout = sessionCheckout;
    }
    if (checkin == "" || checkin == "null") {
        cin.setDate(currentDate.getDate() + 1);
        checkin = cin.toISOString().split('T')[0];
    }
    if (checkout == "" || checkout == "null") {
        cout.setDate(currentDate.getDate() + 4);
        checkout = cout.toISOString().split('T')[0];
    }

    var rooms = $('#totalRoom').val();
    if (rooms == '' || rooms == undefined) {
        rooms = 1;
    }

    var guest = $('#totalAdultsGuest').val();
    if (guest == undefined || guest == "" || guest == "NaN" || guest == NaN) {
        guest = 2;
    }

    var lid = $('.loc_id').text().trim();
    var locationid = $('#location_id').text().trim();
    if (locationid == '') {
        if (lid == "") {
            alert('Location is required.');
            return;
        }
    }

    let formattedCheckin = checkin.replace(/\s+/g, '-');
    let fcheckout = checkout.replace(/\s+/g, '-');

    // Ensure Correct Slug Format
    if (hotel === "1") {
        // If it's a hotel, ensure correct format: hd-locationId-hotelId-hotelSlug
        slug = slug.replace(/^ho-/, '').replace(/^hd-/, '');
        var url = 'hd-' + slug + '?checkin=' + formattedCheckin + '&checkout=' + fcheckout;
    } else {
        // If it's a location, ensure correct format: ho-locationId-locationSlug
        slug = slug.replace(/^ho-/, '').replace(/^hd-/, '');
        var url = 'ho-' + slug + 
            '?checkin=' + formattedCheckin +
            '&checkout=' + fcheckout +
            '&locationid=' + locationid +
            '&lid=' + lid +
            '&rooms=' + rooms +
            '&guest=' + guest;
    }

    // Check for duplicate 'ho-' in the URL and remove one if found
    if (url.includes('ho-ho-')) {
        url = url.replace('ho-ho-', 'ho-');
    }

    console.log("Redirecting to:", url);
    window.location.href = url;
});
//search hotel list


$(document).on('click', '.exp-search', function () {
    
   
        var slugid = $('.loc-slugid').text();        
        var slug = $('.loc-slug').text();       

        if(slug ==""){
            alert('Location is required.');
            return ;
        }
        var url = 'lo-' + slugid +
            '-' + slug;           
        window.location.href = url;
   
});


//end explore search 


//show more about
 

//second function for show more price hotel listing 
$(document).on('click', '.tr-more-prices.tr', function() {
  const $this = $(this);
  const $priceListsSection = $this.closest('.tr-hotel-price-lists.ls');

  if ($this.hasClass('active')) {
      // Hide the modal and remove active state if already active
      $this.removeClass('active');
      $(".more-options-modals").remove();
  } else {

      $(".tr-hotel-price-lists.ls .tr-more-prices.tr").removeClass('active');
      $(".tr-hotel-price-lists.ls .more-options-modals").remove();

      $this.addClass('active');
      

      const $morePricesContainer = $priceListsSection.find('.more-prices-containers');
      if ($morePricesContainer.length) {
          $morePricesContainer.hide(); // Hide the container
      }

  
      const $priceListsMore = $morePricesContainer.find('.tr-hotel-price-list').clone();


      $priceListsMore.each(function() {
          $(this).css('display', 'block'); // Remove any display: none
      });

 
      const $priceListsModal = $("<div class='more-options-modals'></div>");
      $priceListsSection.append($priceListsModal);

      
      const $modalContent = $("<div class='tr-hotel-price-lists ls'></div>");
      $priceListsModal.append($modalContent);
      $modalContent.append($priceListsMore);

   
      $priceListsModal.show();
  }
});


$(document).on('click', function(event) {
  if ($('.more-options-modals').length) {
      if (!$(event.target).closest('.more-options-modals, .tr-more-prices.tr').length) {
          $('.tr-more-prices.tr').removeClass('active');
          $(".more-options-modals").remove();
      }
  }
});
 //end hotel list more price

//start withoutdate filter

function fetchFilteres_without_date(page){
    fun = 'fetch_without_date';
    $priceFrom = $("#rangeSliderExample5MinResult").text();
    $priceTo = $("#rangeSliderExample5MaxResult").text();
    var locationid=$('#Tplocid').text();

    var Cin=$('#Cin').text();
    var Cout=$('#Cout').text();
    var guest=$('#guest').text();
    var rooms=$('#rooms').text();
    
   
  
    var ut=[]
    $('.user-rating input[name="use_rat"]:checked').each(function() {
      ut.push($(this).val()); 
    });
    var userrating =  ut.join(',');
  
    var st=[]
    var starRating = $('.star-rating input[name="rating"]:checked').each(function(){
      st.push($(this).val());
    })
    var st =  st.join(',');


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
  
  var Smnt=[]
  var s_mnt = $('.mnts input[name="special_mnt"]:checked').each(function(){
    Smnt.push($(this).val());
  })

  var Spec_mnt = Smnt.join(',');

  
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
     //hotel_all_filters
      url: base_url + 'hotel_all_filters_without_date',
      data: { 'priceFrom': $priceFrom,'priceTo':$priceTo,'hoteltype':hotelaa,'neibourhood':neibourhood,'distance':distance,'starRating':st,'userrating':userrating,'locationid':locationid,'mnt':mnts,'address':address,'Cin':Cin,'Cout':Cout,'guest':guest,'rooms':rooms,'Smnt':Spec_mnt,'page': page,
      '_token': $('meta[name="csrf-token"]').attr('content')},  
      success: function(response){
       
        $('.filter-listing').html(response);
       
      }
    });
  
  
  }


//end withoutdate filter

//hotel location search for stays


$(document).on('click', '.filter-chackinouts', function (event) {
  // Prevent the default form submission
  event.preventDefault();

  var hotel = $('#hotel').text();

  if (hotel == 1) {
      var slug = $('#slug').text().trim();
      var url = 'hd-' + slug;
  } else {
      var checkin = $('.t-input-check-in').val();
      var checkout = $('.t-input-check-out').val();
      var currentDate = new Date();
      var cin = new Date();
      var cout = new Date();

      if (checkin == "" || checkin == "null") {
          cin.setDate(currentDate.getDate() + 1);
          var formcin = cin.toISOString().split('T')[0];
          checkin = formcin;
      }
      if (checkout == '' || checkout == 'null') {
          cout.setDate(currentDate.getDate() + 4);
          var formcout = cout.toISOString().split('T')[0];
          checkout = formcout;
      }

      var rooms = $('#totalroom').text().trim();
      if (rooms == '') {
          rooms = $('.totalRoom').text();
      }
       if (rooms == undefined || rooms == ""  || rooms == "NaN" || rooms == NaN) {
          rooms = 1;
      }
      var guest = $('.totalguests').val();
      if (guest != undefined) {
          guest = $('.totalguests').val().trim();
      }
      if (guest == undefined) {
          var totalAdultsGuestVal = $('#totalAdultsGuest').text();
          var totalChildrenGuestVal = $('#totalChildrenGuest').text();
          var totalChildrenInfantsVal = $('#totalChildrenInfants').text();
          guest = Number(totalAdultsGuestVal) + Number(totalChildrenGuestVal) + Number(totalChildrenInfantsVal);
      }
       if (guest == undefined || guest == ""  || guest == "NaN" || guest == NaN) {
          guest = 2;
      }

      var lid = $('.loc_id').text().trim();
      var slug = $('#slug').text().trim();
      var locationid = $('#location_id').text().trim();
      if (locationid == '') {
          if (lid == "") {
              alert('Location is required.');
              return;
          }
      }

      var locname = $('#searchhotel').val().trim();
      let formattedCheckin = checkin.replace(/\s+/g, '-');
      let fcheckout = checkout.replace(/\s+/g, '-');    
           var url = 'ho-' + slug +
          '?checkin=' + formattedCheckin +
          '&checkout=' + fcheckout +
          '&locationid=' + locationid +
          '&lid=' + lid +
          '&rooms=' + rooms +
          '&guest=' + guest;
  }

  // Check for duplicate 'ho-' in the URL and remove one if found
  if (url.includes('ho-ho-')) {
      url = url.replace('ho-ho-', 'ho-');
  }

if (!url.startsWith('ho-') && url.includes('?')) {
      // If URL doesn't start with 'ho-' but has query parameters, add the prefix
      url = 'ho-' + url;
  }
  // Open the URL in a new tab or navigate to it
  // window.open(url, '_blank');
  window.location.href = url;
});

// Also prevent the form from being submitted on submit event
$('#hotelForm3').on('submit', function(event) {
  event.preventDefault();
});

//end hotel location search for stays

$(document).ready(function () {



    const input = $('.inputfield');

    const totalguests = $('.totalguests');



    $(".adults").each(function (index) {
        $('.incdec').on("click", function (event) {

            event.stopImmediatePropagation();
            let value = 0;
            value = $(this).siblings(input).next().val();

            ++value;
            $(this).siblings(input).next().val(value);

            if (event.currentTarget.id.includes("children")) {
                if (value == 1) {
                    $("#childrenDetails").append(`<div class="mb-25" style="border-top:1px solid #707070;">
                        <p class="person px-24" style="margin-top:20px;">AGE</p>
                    </div>`);
                }
                $("#childrenDetails").append(`<div
                    class="adults px-24 counter d-flex justify-content-between align-items-center mb-25">
                    <div>
                        <p class="person">CHILD ${value}</p>
                    </div>
                    <div class="d-flex align-items-center">
                        <select name="age" id="age">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                        </select>
                    </div>
                </div>`);
            }

            var sum = 0;
            $('.inputfield').each(function () {
                sum += +$(this).val();
            });

            $(".totalguests").val(sum);

            $(".guest").text(sum);



        });
    });

    $(".adults").each(function (index) {
        $('.decrement').on("click", function (event) {
            event.stopImmediatePropagation();
            let value = 0;
            value = $(this).siblings(input).val();

            --value;
            $(this).siblings(input).val(value);
            if (event.currentTarget.id.includes("children")) {
                $("#childrenDetails").children("div:last").remove();
                if (value == 0) {
                    $("#childrenDetails").empty();
                }
            }


            var sum = 0;
            $('.inputfield').each(function () {
                sum += +$(this).val();
            });
            $(".totalguests").val(sum);

        });
    });




    $('.dropdown-custom-toggle').click(function (e) {
        e.preventDefault();
        $('.custom-dropdown-menu').toggleClass('show');
        $('.dropdown-custom').addClass('active');
        $('.t-datepicker-day').remove()
        $('.t-datepicker').removeClass('t-datepicker-open');


    });


    $('.search-filter').click(function (e) {
        e.preventDefault();
        $(this).removeClass('remove-highlight');
    });
    $('.dropdown-custom').click(function (e) {
        e.preventDefault();
        $('.search-filter').removeClass('remove-highlight');
    });

    $(window).click(function () {
        $('.custom-dropdown-menu').removeClass('show');
        $('.dropdown-custom').removeClass('active');
    });

    $('.dropdown-custom').click(function (event) {
        event.stopPropagation();
    });

    // $('.search-filter').click(function (event) {
    //     event.stopPropagation();
    // });



});






$(document).click(function (event) {
    var $target = $(event.target);
    if (!$target.closest('.search-filter').length) {
        $('.search-filter').addClass('remove-highlight');
    }
});

// Function to ensure search results container is always visible
function showSearchResultsContainer() {
    $('.recent-his').show();
    $('#recentSearchLocation').css('display', 'block');
    $('#cat-list').css('display', 'block');
}

var sightListingSearchTimeout;
try { window.TR_SIGHT_MODAL_SEARCH_V2 = true; } catch (e) {}
try { window.TR_LAST_SIGHT_SEARCH_TERM = window.TR_LAST_SIGHT_SEARCH_TERM || ''; } catch (e) {}

function trEscapeHtml(text) {
    try {
        return $('<div>').text(String(text || '')).html();
    } catch (e) {
        return '';
    }
}

function getExploreListingLocationName() {
    try {
        var name = ($('#location_name').text() || '').trim();
        if (name) {
            return name;
        }
    } catch (e) {}

    try {
        var raw = ($('#slug').text() || '').trim();
        if (!raw) {
            return '';
        }
        raw = String(raw).replace(/-/g, ' ');
        return raw.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
    } catch (e) {
        return '';
    }
}

function renderSightListingNoResults(searchTerm) {
    var term = (searchTerm || (typeof window !== 'undefined' ? (window.TR_LAST_SIGHT_SEARCH_TERM || '') : '') || ($('#serch_sights').val() || '')).trim();
    var loc = getExploreListingLocationName();

    var safeTerm = trEscapeHtml(term);
    var safeLoc = trEscapeHtml(loc);

    var locIdForSearchlist = ($('#locid').text() || '').trim();
    var slugIdForSearchlist = getSightListingSlugIdFromUrl();
    var q1 = (term ? (term + ' near ' + (loc ? loc : 'me')) : '').trim();
    var q2 = (term ? (term + ' near me') : '').trim();
    var href1 = trBuildBaseUrl('searchlist') + '?q=' + encodeURIComponent(q1) + '&locId=' + encodeURIComponent(locIdForSearchlist) + '&slugid=' + encodeURIComponent(slugIdForSearchlist);
    var href2 = trBuildBaseUrl('searchlist') + '?q=' + encodeURIComponent(q2) + '&locId=' + encodeURIComponent(locIdForSearchlist) + '&slugid=' + encodeURIComponent(slugIdForSearchlist);

    var html = '';
    html += '<a class="tr-search-list" href="' + trEscapeHtml(href1) + '">'
        + '<div class="tr-details">'
        + '<div class="tr-title">“' + safeTerm + '” in ' + (safeLoc ? ('“' + safeLoc + '”') : 'this area') + '</div>'
        + '</div>'
        + '</a>';
    html += '<a class="tr-search-list" href="' + trEscapeHtml(href2) + '">'
        + '<div class="tr-details">'
        + '<div class="tr-title">“' + safeTerm + '” near me</div>'
        + '</div>'
        + '</a>';
    return html;
}

function getSightListingSlugIdFromUrl() {
    try {
        var path = (window.location && window.location.pathname) ? window.location.pathname : '';
        // expected: /lo-129700020031-london-england
        var m = path.match(/\/lo-(\d+)/i);
        return m && m[1] ? String(m[1]) : '';
    } catch (e) {
        return '';
    }
}

function renderSightListingResults(items) {
    var $lists = $('.tr-explore-search-modal .tr-search-lists');
    if (!$lists.length) {
        return;
    }

    var html = '';
    if (!items || !items.length) {
        html = renderSightListingNoResults();
    } else {
        for (var i = 0; i < items.length; i++) {
            var it = items[i] || {};
            var safeValue = trEscapeHtml(it.value || '');
            var safeType = trEscapeHtml(it.type || '');
            var safeSlug = trEscapeHtml(it.slug || '');
            var safeSlugId = trEscapeHtml(it.slugid || '');
            var id = it.id || '';
            if (!safeValue) {
                continue;
            }
            if (safeValue === 'Result not found') {
                html += renderSightListingNoResults();
                continue;
            }
            if (String(it.type || '').toLowerCase() === 'suggestion') {
                var href = trBuildSearchlistUrlFromSuggestionText(it.value || '');
                html += '<a class="tr-search-list" href="' + trEscapeHtml(href) + '" data-id="' + id + '" data-type="' + safeType + '" data-slugid="' + safeSlugId + '" data-slug="' + safeSlug + '">'
                    + '<div class="tr-details">'
                    + '<div class="tr-category">' + safeType + '</div>'
                    + '<div class="tr-title">' + safeValue + '</div>'
                    + '</div>'
                    + '</a>';
            } else {
                html += '<div class="tr-search-list" data-id="' + id + '" data-type="' + safeType + '" data-slugid="' + safeSlugId + '" data-slug="' + safeSlug + '">'
                    + '<div class="tr-details">'
                    + '<div class="tr-category">' + safeType + '</div>'
                    + '<div class="tr-title">' + safeValue + '</div>'
                    + '</div>'
                    + '</div>';
            }
        }
    }

    $lists.html(html);
    $('.tr-explore-search-modal .search-result').css('display', 'block');
    $('.tr-explore-search-modal').addClass('open');
}

function trSlugify(text) {
    return String(text || '')
        .toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^a-z0-9\-]/g, '')
        .replace(/\-\-+/g, '-');
}

function trBuildBaseUrl(path) {
    var base = (typeof base_url !== 'undefined' && base_url) ? base_url : '/';
    if (base.slice(-1) !== '/') {
        base += '/';
    }
    if (path && path.charAt(0) === '/') {
        path = path.substring(1);
    }
    return base + (path || '');
}

function trTryRedirectSearchlistSuggestionFromElement(el) {
    try {
        if (!el) {
            return false;
        }

        // If suggestion is already rendered as a link, force navigation early
        // (other scripts may call preventDefault on bubble phase and block normal <a> navigation)
        try {
            if (String(el.tagName || '').toUpperCase() === 'A' && el.getAttribute('href')) {
                var aType = String(el.getAttribute('data-type') || '').trim().toLowerCase();
                var href = String(el.getAttribute('href') || '').trim();
                if (href && (aType === 'suggestion' || href.indexOf('searchlist') !== -1)) {
                    window.location.assign(href);
                    return true;
                }
                return false;
            }
        } catch (e) {}

        var id = String(el.getAttribute('data-id') || '').trim();
        var type = String(el.getAttribute('data-type') || '').trim();
        var slugid = String(el.getAttribute('data-slugid') || '').trim();
        var titleEl = null;
        try { titleEl = el.querySelector('.tr-title'); } catch (e) { titleEl = null; }
        var title = String(titleEl ? (titleEl.textContent || '') : '').trim();
        var t = String(type || '').toLowerCase();

        if (!title || title === 'Result not found') {
            return false;
        }

        if (!(((!id && !type) || t === 'suggestion'))) {
            return false;
        }

        var locIdEl = null;
        try { locIdEl = document.getElementById('locid'); } catch (e) { locIdEl = null; }
        var locIdForSearchlist = String(locIdEl ? (locIdEl.textContent || '') : '').trim();
        var slugIdForSearchlist = slugid || getSightListingSlugIdFromUrl();

        var cleaned = title.replace(/[“”\"]/g, '').trim();
        var q = '';

        var mNearMe = cleaned.match(/^(.+)\s+near\s+me$/i);
        var mNearLoc = cleaned.match(/^(.+)\s+near\s+(.+)$/i);
        var mInLoc = cleaned.match(/^(.+)\s+in\s+(.+)$/i);

        if (mNearMe && mNearMe[1]) {
            q = (mNearMe[1] + ' near me').trim();
        } else if (mNearLoc && mNearLoc[1] && mNearLoc[2]) {
            q = (mNearLoc[1] + ' near ' + mNearLoc[2]).trim();
        } else if (mInLoc && mInLoc[1] && mInLoc[2]) {
            q = (mInLoc[1] + ' near ' + mInLoc[2]).trim();
        } else {
            q = cleaned;
        }

        if (!q) {
            return false;
        }

        var url = trBuildBaseUrl('searchlist')
            + '?q=' + encodeURIComponent(q)
            + '&locId=' + encodeURIComponent(locIdForSearchlist)
            + '&slugid=' + encodeURIComponent(slugIdForSearchlist);
        window.location.assign(url);
        return true;
    } catch (e) {
        try { console.log('[SightSearch] suggestion redirect failed', e); } catch (e2) {}
        return false;
    }
}

try {
    document.addEventListener('pointerdown', function(e) {
        var el = e && e.target && e.target.closest ? e.target.closest('.tr-explore-search-modal .tr-search-list') : null;
        if (el && trTryRedirectSearchlistSuggestionFromElement(el)) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            if (e && typeof e.stopPropagation === 'function') {
                e.stopPropagation();
            }
        }
    }, true);
    document.addEventListener('click', function(e) {
        var el = e && e.target && e.target.closest ? e.target.closest('.tr-explore-search-modal .tr-search-list') : null;
        if (el && trTryRedirectSearchlistSuggestionFromElement(el)) {
            if (e && typeof e.preventDefault === 'function') {
                e.preventDefault();
            }
            if (e && typeof e.stopPropagation === 'function') {
                e.stopPropagation();
            }
        }
    }, true);
} catch (e) {}

function fetchSightListingResults(searchTerm) {
    try { window.TR_LAST_SIGHT_SEARCH_TERM = String(searchTerm || '').trim(); } catch (e) {}
    var locId = ($('#locid').text() || '').trim();
    if (!locId) {
        var slugIdFallback = getSightListingSlugIdFromUrl();
        if (!slugIdFallback) {
            try { console.log('[SightSearch] missing locId and no slugid fallback. term=', searchTerm); } catch (e) {}
            renderSightListingResults([]);
            return;
        }
        try { console.log('[SightSearch] locId missing; using slugid fallback', { term: searchTerm, slugid: slugIdFallback }); } catch (e) {}
        locId = '';
    }

    try { console.log('[SightSearch] request', { term: searchTerm, locId: locId, url: (typeof base_url !== 'undefined' ? (base_url + 'searchsightlisting') : 'searchsightlisting') }); } catch (e) {}

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: base_url + 'searchsightlisting',
        data: {
            val: searchTerm,
            locId: locId,
            slugid: (!locId ? getSightListingSlugIdFromUrl() : ''),
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            try { console.log('[SightSearch] response', response); } catch (e) {}
            renderSightListingResults(response);
        },
        error: function(xhr, status, err) {
            try { console.log('[SightSearch] error', { status: status, err: err, responseText: (xhr && xhr.responseText) ? xhr.responseText : null }); } catch (e) {}
            renderSightListingResults([]);
        }
    });
}

// Prevent duplicate handlers from other bundles/scripts (they can overwrite results)
$(document).off('click', '#serch_sightsdata');
$(document).off('keyup', '#serch_sights');
$(document).off('focus', '#serch_sights');
$(document).off('click', '#serch_sights');

// Also remove any directly-bound handlers (non-delegated)
$(function() {
    try {
        $('#serch_sightsdata').off('click');
        $('#serch_sights').off('keyup');
        $('#serch_sights').off('focus');
        $('#serch_sights').off('click');
    } catch (e) {}
});

$(document).on('click.trSightsV2 focus.trSightsV2', '#serch_sights', function(e) {
    if (e && typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }
    e.stopPropagation();
    $('.tr-explore-search-modal').addClass('open');
    $('.tr-explore-search-modal .search-result').css('display', 'block');
});

$(document).on('click.trSightsV2', '#serch_sightsdata', function(e) {
    if (e && typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }
    e.preventDefault();
    e.stopPropagation();
    var term = ($('#serch_sights').val() || '').trim();
    if (!term) {
        return;
    }
    try { window.TR_LAST_SIGHT_SEARCH_TERM = term; } catch (e) {}
    fetchSightListingResults(term);
});

$(document).on('click.trSightsV2 pointerdown.trSightsV2 touchstart.trSightsV2', '.tr-explore-search-modal .tr-search-list', function(e) {
    var $it = $(this);
    var id = ($it.attr('data-id') || '').trim();
    var type = ($it.attr('data-type') || '').trim();
    var slugid = ($it.attr('data-slugid') || '').trim();
    var slug = ($it.attr('data-slug') || '').trim();
    var title = ($it.find('.tr-title').text() || '').trim();

    var t = (type || '').toLowerCase();

    // Redirect for:
    // - Inline no-results suggestions (no data attributes)
    // - API suggestions (data-type="Suggestion" and usually no data-id)
    if (((!id && !type) || t === 'suggestion') && title && title !== 'Result not found') {
        // If it's already a link, let the browser follow href
        try {
            if ($it.is('a') && ($it.attr('href') || '').trim()) {
                return;
            }
        } catch (e0) {}

        if (e && typeof e.preventDefault === 'function') {
            e.preventDefault();
        }
        if (e && typeof e.stopPropagation === 'function') {
            e.stopPropagation();
        }

        try {
            var locIdForSearchlist = ($('#locid').text() || '').trim();
            var slugIdForSearchlist = slugid || getSightListingSlugIdFromUrl();
            var q = '';

            // Supports both quoted and unquoted suggestion text:
            //   sdfxcb in London
            //   “pizza” in “London”
            //   sdfxcb near me
            var cleaned = title.replace(/[“”\"]/g, '').trim();
            var mNearMe = cleaned.match(/^(.+)\s+near\s+me$/i);
            var mInLoc = cleaned.match(/^(.+)\s+in\s+(.+)$/i);
            var mNearLoc = cleaned.match(/^(.+)\s+near\s+(.+)$/i);

            if (mNearMe && mNearMe[1]) {
                q = (mNearMe[1] + ' near me').trim();
            } else if (mNearLoc && mNearLoc[1] && mNearLoc[2]) {
                q = (mNearLoc[1] + ' near ' + mNearLoc[2]).trim();
            } else if (mInLoc && mInLoc[1] && mInLoc[2]) {
                // normalize "in" -> "near" so backend parser works
                q = (mInLoc[1] + ' near ' + mInLoc[2]).trim();
            } else {
                q = cleaned;
            }

            if (q) {
                var url = trBuildBaseUrl('searchlist')
                    + '?q=' + encodeURIComponent(q)
                    + '&locId=' + encodeURIComponent(locIdForSearchlist)
                    + '&slugid=' + encodeURIComponent(slugIdForSearchlist);
                try { console.log('[SightSearch] suggestion click -> searchlist', { title: title, type: type, url: url }); } catch (eLog) {}
                try {
                    window.location.assign(url);
                } catch (eNav) {
                    try { window.location.href = url; } catch (eNav2) {}
                }
                try {
                    setTimeout(function() {
                        try {
                            if (String(window.location && window.location.href || '').indexOf('searchlist') === -1) {
                                window.location.href = url;
                            }
                        } catch (eNav3) {}
                    }, 50);
                } catch (eT) {}
            }
        } catch (err) {
            try { console.log('[SightSearch] suggestion redirect failed', err); } catch (e2) {}
        }
        return false;
    }

    if (!id || title === 'Result not found') {
        return;
    }

    t = type.toLowerCase();
    if (t === 'sight') {
        if (!slugid) {
            slugid = getSightListingSlugIdFromUrl();
        }
        if (!slug) {
            slug = trSlugify(title);
        }
        window.location.href = trBuildBaseUrl('at-' + slugid + '-' + id + '-' + slug);
        return;
    }

    if (t === 'restaurant') {
        if (!slugid) {
            slugid = getSightListingSlugIdFromUrl();
        }
        if (!slug) {
            slug = trSlugify(title);
        }
        window.location.href = trBuildBaseUrl('rd-' + slugid + '-' + id + '-' + slug);
        return;
    }

    if (t === 'experience') {
        if (!slugid) {
            slugid = getSightListingSlugIdFromUrl();
        }
        if (!slug) {
            slug = trSlugify(title);
        }
        window.location.href = trBuildBaseUrl('experience-' + slugid + '-' + id + '-' + slug);
        return;
    }

    if (t === 'category') {
        var categorySlug = trSlugify(title);
        if (categorySlug === 'must-see' || categorySlug === 'mustsee') {
            categorySlug = 'mustsee';
        }
        var path = (window.location && window.location.pathname) ? window.location.pathname : '';
        var m = path.match(/(\/lo-[^\/]+)(?:\/[^\/]+)?$/i);
        if (m && m[1]) {
            window.location.href = m[1] + '/' + categorySlug;
            return;
        }
    }
});

$(document).on('keyup.trSightsV2', '#serch_sights', function(e) {
    if (e && typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }
    e.stopPropagation();
    if (e.key === 'Enter') {
        $('#serch_sightsdata').trigger('click');
        return;
    }

    var term = ($(this).val() || '').trim();
    clearTimeout(sightListingSearchTimeout);
    if (!term) {
        return;
    }
    sightListingSearchTimeout = setTimeout(function() {
        try { window.TR_LAST_SIGHT_SEARCH_TERM = term; } catch (e) {}
        fetchSightListingResults(term);
    }, 250);
});

function trOpenExploreListingSearchModal() {
    var $modal = $('.tr-explore-search-modal');
    if (!$modal.length) {
        return;
    }
    $modal.addClass('open active');
    $modal.css('display', 'block');
    $modal.find('.search-result').css('display', 'block');
    setTimeout(function() {
        var $input = $('#serch_sights');
        if ($input.length) {
            $input.trigger('focus');
        }
    }, 30);
}

function trCloseExploreListingSearchModal() {
    var $modal = $('.tr-explore-search-modal');
    if (!$modal.length) {
        return;
    }
    $modal.removeClass('open active');
    $modal.find('.search-result').css('display', 'none');
    $modal.css('display', 'none');
}

$(document).on('click.trSightsV2 touchstart.trSightsV2 pointerdown.trSightsV2', '.tr-explore-search > .tr-explore-search-feild, .tr-explore-search > .tr-explore-search-feild > button.tr-btn', function(e) {
    if (e && typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }
    if (e && typeof e.preventDefault === 'function') {
        e.preventDefault();
    }
    if (e && typeof e.stopPropagation === 'function') {
        e.stopPropagation();
    }
    trOpenExploreListingSearchModal();
});

$(document).on('click.trSightsV2', '.tr-explore-search-modal .btn-close', function(e) {
    if (e && typeof e.stopImmediatePropagation === 'function') {
        e.stopImmediatePropagation();
    }
    if (e && typeof e.preventDefault === 'function') {
        e.preventDefault();
    }
    if (e && typeof e.stopPropagation === 'function') {
        e.stopPropagation();
    }
    trCloseExploreListingSearchModal();
});

$(document).on('click.trSightsV2Close touchstart.trSightsV2Close pointerdown.trSightsV2Close', function(e) {
    var $t = $(e && e.target ? e.target : null);
    if ($t.closest('.tr-explore-search-modal').length || $t.closest('.tr-explore-search').length) {
        return;
    }
    trCloseExploreListingSearchModal();
});

$(document).on('click', '.tr-explore-search-modal, .tr-explore-search-modal .search-result, .tr-explore-search-modal .tr-search-lists', function(e) {
    e.stopPropagation();
});
