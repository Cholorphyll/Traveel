var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window
    .location.port : '');
    var base_url = baseURL + '/';
    
    
    
    
    
    
     
//      var fun;
//      var func;
    
     
//     //search hotel list
//     $(document).ready(function() {
        
//       function debounce(func, wait) {
//           let timeout;
//           return function() {
//               const context = this, args = arguments;
//               clearTimeout(timeout);
//               timeout = setTimeout(function() {
//                   func.apply(context, args);
//               }, wait);
//           };
//       }
    
    
//       const debouncedFetchFiltered = debounce(function() {
//           fetchFiltered(1); 
//       }, 800); 
    
//       $('.min-range, .max-range').on('input', function() {
//           updatePriceValues();
//           debouncedFetchFiltered(); 
//       });
    
//       $('.hl-filter').change(function() {
//           fetchFiltered(1); 
//       });
    
      
   
    
    
     
//     $('.select-items div').on('click', function() {   
//         const selectedValue = $(this).text();    
//         $('#sort_by').val(selectedValue).trigger('change');
//     });
    
    
    
//     // Function to update the displayed min and max price
//     function updatePriceValues() {
//       var minPrice = $('.min-range').val();
//       var maxPrice = $('.max-range').val();
      
//       $('.min-price-title').text('$' + minPrice);
//       $('.max-price-title').text('$' + maxPrice);
//     }
    
//     // Function to fetch filtered data
//     function fetchFiltered(page) {
//       var func = 'filter';

//       var filters = {
//           locationid: $('#Tplocid').text(),
//           Cin: $('#Cin').text(),
//           Cout: $('#Cout').text(),
//           guest: $('#guest').text(),
//           rooms: $('#rooms').text(),
//           priceFrom: $('.min-range').val(),
//           priceTo: $('.max-range').val(),
//           mnt: getCheckedValues('mnt'),
//           smnt: getCheckedValues('smnt'),
//           rating: getCheckedValues('rating'),
//           hoteltypes: getCheckedValues('hoteltypes'),
//           agency: getCheckedValues('agency'),
//           sort_by: $('.custom-select .same-as-selected').text(),
//           page: page,
//           _token: $('meta[name="csrf-token"]').attr('content')
//       };

//       $.ajax({
//           type: 'post',
//           headers: {'X-CSRF-TOKEN': filters._token},
//           url: base_url + 'hotel_all_filters',
//           data: filters,
//           success: function(response) {
//               $('.hotel_count').text(response.resultcount + ' properties found');
//               $('.filter-listing').html(response.result);
//           }
//       });
//     }

//     // Helper function to get checked values by name
//     function getCheckedValues(name) {
//         var values = [];
//         $('input[name="' + name + '"]:checked').each(function() {
//             values.push($(this).val());
//         });
//         return values.join(',');
//     }
    
//     // Handle all filter changes including Popular Filters
//     $('.filter').change(function() {
//         fetchFiltered(1);
//     });
//     //end search hotel list
    
    
    
    
    
//     //show selected filtes 
    
    
    
//     document.addEventListener('DOMContentLoaded', function() {
//       initializeFilterListeners();
    
//       // Add click event listener for .tr-filter-selected to handle cross click
//       document.addEventListener('click', function(event) {
//           if (event.target.closest('.tr-filter-selected')) {
//               handleSelectedFilterClick(event);
//           }
//       });
//     });
    
//     function initializeFilterListeners() {
//       const filterSections = document.querySelectorAll('.tr-filters-section');
    
//       filterSections.forEach(function(section) {
//           const checkboxes = section.querySelectorAll('.filter');
//           const sectionId = section.getAttribute('data-section');
    
//           checkboxes.forEach(function(checkbox) {
//               checkbox.removeEventListener('change', handleCheckboxChange); // Remove previous handler
//               checkbox.addEventListener('change', function() {
//                   handleCheckboxChange(checkbox, sectionId);
//               });
//           });
//       });
//     }
    
//     function handleCheckboxChange(checkbox, sectionId) {
//       // Update selected data
//       updateSelectedData(sectionId);
    
//       // Fetch filtered results
//       fetchFiltered(1);
//     }
//     function updateSelectedData(sectionId) {
//         const sections = document.querySelectorAll('.tr-filters-section');
        
//         sections.forEach(function(section) {
//             const checkboxes = section.querySelectorAll('.filter');
//             const sectionId = section.getAttribute('data-section');
//             const selectedDataElement = document.querySelector(`.selected-data[data-section="${sectionId}"]`);
//             if (selectedDataElement) {
//                 selectedDataElement.innerHTML = ''; // Clear previous data
      
//                 // Track already added filters to prevent duplicates
//                 const addedFilters = new Set();
                
//                 checkboxes.forEach(function(checkbox) {
//                     if (checkbox.checked && !addedFilters.has(checkbox.value)) {
//                         const labelText = checkbox.closest('label').textContent.trim();
//                         const div = document.createElement('div');
//                         div.classList.add('tr-filter-selected');
//                         div.textContent = labelText;
//                         div.setAttribute('data-value', checkbox.value); // Store the value for reference
//                         selectedDataElement.appendChild(div);
//                         addedFilters.add(checkbox.value);
//                     }
//                 });
//             }
//         });
//       }
      
//       function handleSelectedFilterClick(event) {
//         const clickedElement = event.target.closest('.tr-filter-selected');
        
//         if (clickedElement) {
//             const filterValue = clickedElement.getAttribute('data-value');
//             const sectionId = clickedElement.closest('.selected-data').getAttribute('data-section');
            
//             // Uncheck the filter in all sections
//             $('.filter').each(function() {
//                 if ($(this).val() === filterValue) {
//                     $(this).prop('checked', false); // Uncheck the checkbox
//                 }
//             });

//             // Remove the clicked filter item from the selected data
//             clickedElement.remove();
            
//             // Update the filter data and fetch new results
//             updateSelectedData(sectionId);
//             fetchFiltered(1);
//         }
//       }
      
      
//       //end selected filters
//     //pagination
//     $(document).on('click', '.pagination a', function(e) {
//         e.preventDefault();
//         var page = $(this).attr('href').split('page=')[1];
//         var ptype = $('.page_type').text();
      
//         if (ptype == 'filter') {
//         //     updateSearchResults(page);
//         // }else if(ptype == 'filter'){
//           fetchFiltered(page);
//         }
//         $('html, body').animate({ scrollTop: $('.filter-listing').offset().top }, 'slow');
//       });

//     //end pagination

// });
// Global flags to track state
let isRequestInProgress = false;
let isUpdatingFromMapModal = false;
let mapModalInitialized = false;

// Function to initialize the map modal with proper filters and event handlers
function initializeMapModal() {
    if (mapModalInitialized) return;
    
    // Clear existing content in map modal filters section
    $('#mapModal .tr-filters-section').empty();
    
    // Clone all filter lists from main view to map modal
    $('.tr-filters-section[data-section="1"] .tr-filter-lists').each(function() {
        const filterListClone = $(this).clone(true);
        $('#mapModal .tr-filters-section').append(filterListClone);
    });
    
    // Fix IDs for price range sliders in map modal to prevent conflicts
    $('#mapModal .min-range').attr('id', 'mapMinRange');
    $('#mapModal .max-range').attr('id', 'mapMaxRange');
    $('#mapModal .min-price-title').attr('id', 'mapMinPrice');
    $('#mapModal .max-price-title').attr('id', 'mapMaxPrice');
    
    // Initialize selected filters section in map modal
    updateSelectedData(2);
    
    // Mark as initialized
    mapModalInitialized = true;
}

// Function to synchronize filters between main view and map modal
function syncFilters(fromMapModal = false) {
    isUpdatingFromMapModal = true;
    
    // Determine source and target sections based on which view triggered the sync
    const sourceSection = fromMapModal ? '#mapModal .tr-filters-section' : '.tr-filters-section[data-section="1"]';
    const targetSection = fromMapModal ? '.tr-filters-section[data-section="1"]' : '#mapModal .tr-filters-section';
    
    // Sync checkboxes between views
    $(sourceSection).find('input.filter').each(function() {
        const filterValue = $(this).val();
        const filterName = $(this).attr('name');
        const isChecked = $(this).prop('checked');
        
        // Find and set the corresponding checkbox in the target section
        $(targetSection).find(`input.filter[value="${filterValue}"][name="${filterName}"]`).prop('checked', isChecked);
    });
    
    // Sync price range sliders
    if (fromMapModal) {
        const minPrice = $('#mapModal .min-range').val();
        const maxPrice = $('#mapModal .max-range').val();
        $('.tr-filters-section[data-section="1"] .min-range').val(minPrice);
        $('.tr-filters-section[data-section="1"] .max-range').val(maxPrice);
        $('.tr-filters-section[data-section="1"] .min-price-title').text('$' + minPrice);
        $('.tr-filters-section[data-section="1"] .max-price-title').text('$' + maxPrice);
    } else {
        const minPrice = $('.tr-filters-section[data-section="1"] .min-range').val();
        const maxPrice = $('.tr-filters-section[data-section="1"] .max-range').val();
        $('#mapModal .min-range').val(minPrice);
        $('#mapModal .max-range').val(maxPrice);
        $('#mapModal .min-price-title').text('$' + minPrice);
        $('#mapModal .max-price-title').text('$' + maxPrice);
    }
    
    // Update selected filters display in both views
    updateSelectedData(1);
    updateSelectedData(2);
    
    isUpdatingFromMapModal = false;
}

// Function to fetch filtered hotel data
function fetchFiltered(page = 1) {
    if (isRequestInProgress) return; // Prevent multiple AJAX requests
    isRequestInProgress = true;

    // Fetch required values from the DOM
    const locationid = $('#Tplocid').text();
    const Cin = $('#Cin').text();
    const Cout = $('#Cout').text();
    const guest = $('#guest').text();
    const rooms = $('#rooms').text();
    const priceFrom = $('.min-range').val();
    const priceTo = $('.max-range').val();
    const sortBy = $('.custom-select .same-as-selected').text();


 $('.filter-listing').html('<div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div><div class="tr-hotel-deatils" data-type="no-data"><div class="tr-hotal-image"><div class="tr-no-data-text animated-bg-1 w-100 h-230"></div></div><div class="tr-hotel-deatil"><div class="tr-heading-with-rating"><div class="tr-no-data-text animated-bg-1 w-50 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-25 h-24 mb-12 ml-20"></div></div><div class="tr-no-data-text animated-bg-1 w-25 h-15 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-40 h-24 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-100 h-20 mt-41 mb-12"></div><div class="tr-no-data-text animated-bg-1 w-80 h-20 mb-12"></div></div><div class="tr-hotel-price-section"><div class="tr-hotel-price-lists"><div class="tr-hotel-price-list"><div class="tr-row mb-12"><div class="tr-no-data-text animated-bg-1 w-50 h-15"></div><div class="tr-no-data-text animated-bg-1 w-25 h-15"></div></div><div class="tr-no-data-text animated-bg-1 w-50 h-15 mb-12"></div><div class="tr-row"><div class="tr-no-data-text animated-bg-1 w-30 h-20"></div><div class="tr-no-data-text animated-bg-1 w-50 h-20"></div></div></div></div></div></div>');
    // Fetch checked filter values
    const amenities = $('input[name="mnt"]:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

    const spamenities = $('input[name="smnt"]:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

    const starRating = $('input[name="rating"]:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

    const hotelType = $('input[name="hoteltypes"]:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

    const agencyData = $('input[name="agency"]:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

	const guestRatings = $('input[name="guest_rating"]:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

    // Get checked nearby places from both main list and modal
    const nearbyPlaces = $('.nearby-main-list .filter:checked, .tr-nearby-grid .modal-filter:checked')
        .map(function() { return $(this).val(); })
        .get().join(',');

    // AJAX request to fetch filtered results
    $.ajax({
        url: `${base_url}hotel_all_filters`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            priceFrom,
            priceTo,
            mnt: amenities,
            locationid,
            Cin,
            Cout,
            guest,
            rooms,
            starRating,
            hoteltype: hotelType,
            Smnt: spamenities,
            agency: agencyData,
        	guest_rating: guestRatings,
            sort_by: sortBy,
            nearby: nearbyPlaces,
            page,
            '_token': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            console.log('AJAX Response - Result Count:', data.resultcount);
            
            // Update the hotel count and filtered results
            $('.hotel_count').text(`${data.resultcount} properties found`);
            $('.filter-listing').html(data.result);
            
            // Check if any filters are applied
            const filtersApplied = $('.filter:checked').length > 0 || 
                                 $('.min-range').val() != 0 || 
                                 $('.max-range').val() != 5000;
            
            // Get button reference
            const clearBtn = $('#clearAllFiltersBtn');
            
            // Show button only if filters are applied AND results are 0
            if (filtersApplied && data.resultcount == 0) {
                console.log('Showing clear button (filters applied & 0 results)');
                clearBtn.css('display', 'block');
            } else {
                console.log('Hiding clear button');
                clearBtn.css('display', 'none');
            }
            
            // Fix image quality issues in the main view
            fixImageQuality();
            
            // Update map modal content with the filtered results
            updateMapModalContent();
            
            // Reinitialize filter listeners for both views
            initializeFilterListeners();
        },
        complete: function() {
            isRequestInProgress = false; // Reset flag
        }
    });
}

// Function to update price range values and trigger filtering
function updatePriceValues() {
    const minPrice = $('.min-range').val();
    const maxPrice = $('.max-range').val();
    $('.min-price').text(`$${minPrice}`);
    $('.max-price').text(`$${maxPrice}`);
}

// Event handler for nearby modal
$(document).ready(function() {
    // Open modal when clicking show more
    $('.show-more-nearby').on('click', function() {
        $('#nearbyModal').css('display', 'flex');
    });

    // Close modal when clicking the back arrow or outside the modal
    $('.tr-modal-close, .tr-modal-overlay').on('click', function(e) {
        if (e.target === this) {
            $('#nearbyModal').css('display', 'none');
        }
    });

    // Handle Apply button click
    $('.tr-modal-apply').on('click', function() {
        // Transfer checked states from modal to main list
        $('.modal-filter:checked').each(function() {
            let value = $(this).val();
            let label = $(this).siblings('.form-check-label').text().trim();
            let mainCheckbox = $('.nearby-main-list .filter[value="' + value + '"]');
            mainCheckbox.prop('checked', true);
            
            // Add to selected filters display
            let selectedDataElement = $('.selected-data[data-section="1"]');
            if (!selectedDataElement.find('.tr-filter-selected[data-value="' + value + '"]').length) {
                selectedDataElement.append(
                    $('<div>', {
                        class: 'tr-filter-selected',
                        text: label,
                        'data-value': value
                    })
                );
            }
        });
        
        // Remove unchecked items
        $('.modal-filter:not(:checked)').each(function() {
            let value = $(this).val();
            $('.nearby-main-list .filter[value="' + value + '"]').prop('checked', false);
            $('.selected-data[data-section="1"] .tr-filter-selected[data-value="' + value + '"]').remove();
        });
        
        // Close modal and trigger filter update
        $('#nearbyModal').css('display', 'none');
        fetchFiltered(1);
    });

    // Handle Reset button click
    $('.tr-modal-reset').on('click', function() {
        $('.modal-filter').prop('checked', false);
    });

    // Search functionality
    $('#nearbySearch').on('input', function() {
        let searchText = $(this).val();
        let locationId = $('.slugid').text();

        $.ajax({
            url: base_url + 'search-nearby-places',
            method: 'GET',
            data: {
                search: searchText,
                location_id: locationId
            },
            success: function(response) {
                if (response.success) {
                    $('.tr-nearby-grid').empty();
                    response.places.forEach(function(place) {
                        $('.tr-nearby-grid').append(`
                            <div class="tr-nearby-item" style="padding: 5px 15px;">
                                <label class="tr-check-box">
                                    <input type="checkbox" name="nearby[]" class="filter modal-filter" value="${place.SightId}">
                                    <span class="form-check-label">${place.Title}</span>
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                        `);
                    });
                }
            }
        });
    });
});

// Function to update map modal content with the filtered results
function updateMapModalContent() {
    // Clear existing content in map modal
    $('#mapModal .tr-room-section-2').empty();
    
    // Clone the filtered hotel details to the map modal
    $('.tr-room-section-2 .tr-hotel-deatils').each(function() {
        const hotelClone = $(this).clone();
        $('#mapModal .tr-room-section-2').append(hotelClone);
    });
    
    // Fix carousel IDs in the map modal to prevent conflicts
    $('#mapModal .carousel').each(function() {
        const currentId = $(this).attr('id');
        if (currentId) {
            const newId = currentId + '-map';
            $(this).attr('id', newId);
            
            // Update corresponding controls
            $(this).find('[data-bs-target="#' + currentId + '"]').each(function() {
                $(this).attr('data-bs-target', '#' + newId);
            });
        }
    });
    
    // Update buttons and links in the map modal
    $('#mapModal .tr-btn, #mapModal a.tr-hotel-detail-link').each(function() {
        $(this).on('click', function(e) {
            // If it's a button with class tr-btn, let it behave normally
            if ($(this).hasClass('tr-btn')) {
                return true;
            }
            
            // For other elements, open in a new tab
            if ($(this).attr('href')) {
                window.open($(this).attr('href'), '_blank');
                e.preventDefault();
                return false;
            }
        });
    });
    
    // Fix image quality issues
    fixImageQuality();
}

// Initialize filter listeners
$(document).ready(function() {
    // Initialize main filters (checkboxes)
    initializeFilterListeners();

    // Add event listener for sort by dropdown
    $('.select-items div').on('click', function() {
        const selectedValue = $(this).text();
        $('#sort_by').val(selectedValue);
        fetchFiltered(1);
    });

    // Global click event listener to handle filter removal
    $(document).on('click', function(event) {
        if ($(event.target).closest('.tr-filter-selected').length) {
            handleSelectedFilterClick(event);
        }
    });
    
    // Set up map modal event handlers
    $('#mapModal').on('show.bs.modal', function() {
        // Initialize the map modal before it's shown
        initializeMapModal();
    });
    
    $('#mapModal').on('shown.bs.modal', function() {
        // Sync the current filter state to the map modal
        syncFilters(false);
        
        // Update the hotel listings in the map modal
        updateMapModalContent();
    });
    
    // Handle filter button clicks in map view
    $(document).on('click', '#onMapFilterModal', function() {
        // Toggle the filter section visibility in map modal
        $('#mapModal .tr-filters-section').toggleClass('show');
    });
    
    // Handle hide list button in map view
    $(document).on('click', '.tr-hide-list', function() {
        $('#mapModal .tr-room-section-2, #mapModal .tr-filters-section').toggleClass('hidden');
        $(this).text($(this).text() === 'Hide Hotel List' ? 'Show Hotel List' : 'Hide Hotel List');
    });
});

// Function to clear all filters
function clearAllFilters() {
    // Uncheck all filter checkboxes
    $('.filter').prop('checked', false);
    
    // Reset price range sliders
    $('.min-range').val(0);
    $('.max-range').val(5000);
    $('.min-price-title').text('$0');
    $('.max-price-title').text('$5000');
    
    // Clear all selected filters display
    $('.selected-data').empty();
    
    // Trigger filter update
    fetchFiltered(1);
}

// Initialize filter listeners
function initializeFilterListeners() {
    // Select all filter sections (both in main view and map modal)
    $('.tr-filters-section').each(function() {
        const section = $(this);
        const checkboxes = section.find('.filter');
        const sectionId = section.data('section');

        // Remove existing event handlers to prevent duplicates
        checkboxes.each(function() {
            $(this).off('change').on('change', function() {
                handleCheckboxChange($(this), sectionId);
            });
        });
        
        // Add event handlers for price range sliders
        section.find('.min-range, .max-range').off('input').on('input', function() {
            // Only update if not already updating from another view
            if (!isUpdatingFromMapModal) {
                // Update price display
                const minRange = section.find('.min-range').val();
                const maxRange = section.find('.max-range').val();
                
                // Update price labels in this section
                section.find('.min-price-title').text('$' + minRange);
                section.find('.max-price-title').text('$' + maxRange);
                
                // Sync with other view
                const isMapModal = section.closest('#mapModal').length > 0;
                syncFilters(isMapModal);
                
                // Fetch filtered results
                fetchFiltered(1);
            }
        });
    });
}

// Handle checkbox change event
function handleCheckboxChange(checkbox, sectionId) {
    // Only process if not already updating from another view
    if (!isUpdatingFromMapModal) {
        // Update selected data
        updateSelectedData(sectionId);
        
        // Sync with other view
        const isMapModal = checkbox.closest('#mapModal').length > 0;
        syncFilters(isMapModal);
        
        // Fetch filtered results
        fetchFiltered(1);
    }
}

// Update selected filter data
function updateSelectedData(sectionId) {
    // Find the specific filter section by data-section attribute
    const section = $(`.tr-filters-section[data-section="${sectionId}"]`);
    const selectedDataElement = $(`.selected-data[data-section="${sectionId}"]`);
    
    if (section.length && selectedDataElement.length) {
        selectedDataElement.empty(); // Clear previous data
      
        // Track already added filters to prevent duplicates
        const addedFilters = new Set();
        
        // Process all filter lists in this section
        section.find('.tr-filter-lists').each(function() {
            // Determine filter type from class
            let filterType = '';
            if ($(this).hasClass('star-rating')) filterType = 'star-rating';
            else if ($(this).hasClass('hoteltype')) filterType = 'hoteltype';
            else if ($(this).hasClass('agencies')) filterType = 'agencies';
            else if ($(this).hasClass('nearby')) filterType = 'nearby';
        	else if ($(this).hasClass('guest-rating')) filterType = 'guest-rating';
            else filterType = 'mnt';
            
            // Add selected filters to the display
            $(this).find('.filter:checked').each(function() {
                const labelText = $(this).closest('label').text().trim();
                const filterValue = $(this).val();
                const filterName = $(this).attr('name');
                
                if (!addedFilters.has(filterValue)) {
                    const div = $('<div>', {
                        class: 'tr-filter-selected',
                        text: labelText,
                        'data-value': filterValue,
                        'data-name': filterName,
                        'data-type': filterType
                    });
                    selectedDataElement.append(div);
                    addedFilters.add(filterValue);
                }
            });
        });
        
        // Add price range if it's not default
        const minRange = section.find('.min-range').val();
        const maxRange = section.find('.max-range').val();
        if (minRange > 0 || maxRange < 5000) {
            const priceText = `$${minRange} - $${maxRange}`;
            const div = $('<div>', {
                class: 'tr-filter-selected',
                text: priceText,
                'data-type': 'price-range',
                'data-min': minRange,
                'data-max': maxRange
            });
            selectedDataElement.append(div);
        }
    }
}

// Handle selected filter click to remove filter
function handleSelectedFilterClick(event) {
    // Prevent multiple updates
    if (isUpdatingFromMapModal) return;
    isUpdatingFromMapModal = true;
    
    const clickedElement = $(event.target).closest('.tr-filter-selected');
    
    if (clickedElement.length) {
        const filterValue = clickedElement.data('value');
        const filterType = clickedElement.data('type');
        const sectionId = clickedElement.closest('.selected-data').data('section');
        const isMapModal = clickedElement.closest('#mapModal').length > 0;
        
        if (filterType === 'price-range') {
            // Reset price range sliders in both views
            $('.min-range').val(0);
            $('.max-range').val(5000);
            $('.min-price-title').text('$0');
            $('.max-price-title').text('$5000');
        } else {
            // Uncheck the specific filter in both views
            $(`.tr-filters-section input.filter[value="${filterValue}"]`).prop('checked', false);
        }
        
        // Remove the clicked filter item from both selected data sections
        $(`.tr-filter-selected[data-value="${filterValue}"]`).remove();
        
        // Update the filter data in both views
        updateSelectedData(1);
        updateSelectedData(2);
        
        // Fetch filtered results
        fetchFiltered(1);
    }
    
    isUpdatingFromMapModal = false;
}

// Function to fix image quality issues after filtering
function fixImageQuality() {
    // Process all hotel images in both main view and map modal
    $('.tr-room-section-2 .tr-hotel-deatils, #mapModal .tr-hotel-deatils').each(function() {
        const hotelId = $(this).data('id');
        
        // Fix carousel images
        $(this).find('.carousel-item img').each(function(index) {
            const imgSrc = $(this).attr('src');
            
            // Check if the image source is from hotellook.com and needs fixing
            if (imgSrc && imgSrc.includes('hotellook.com')) {
                // Extract the hotel ID from the image URL if available
                let extractedHotelId = '';
                const match = imgSrc.match(/h([0-9]+)_([0-9]+)/i);
                if (match && match.length > 1) {
                    extractedHotelId = match[1];
                }
                
                // If we have a hotel ID (either from data attribute or extracted), use it to construct a high-quality image URL
                const actualHotelId = extractedHotelId || $(this).closest('.tr-hotel-deatils').data('hotelid');
                if (actualHotelId) {
                    // Construct a high-quality image URL with proper dimensions
                    const newImgSrc = `https://photo.hotellook.com/image_v2/crop/h${actualHotelId}_${index}/520/460.jpg`;
                    $(this).attr('src', newImgSrc);
                    
                    // Force image reload
                    $(this).attr('src', newImgSrc + '?' + new Date().getTime());
                }
            }
        });
    });
    
    // Initialize carousels if needed
    $('.carousel').each(function() {
        if (!$(this).hasClass('initialized')) {
            $(this).addClass('initialized');
        }
    });
}

// Event listeners for price range sliders
$('.min-range, .max-range').on('input', function() {
    updatePriceValues();
    fetchFiltered(1);
});

// Show more/less functionality for filters
$(document).on('click', '.show-more-filters', function() {
  $(this).closest('.filter-items-container').find('.additional-filters').show();
  $(this).closest('li').hide();
});

$(document).on('click', '.show-less-filters', function() {
  $(this).closest('.filter-items-container').find('.additional-filters').hide();
  $(this).closest('.filter-items-container').find('.show-more-container').show();
});

//start get all hotel code
    
$(document).ready(function() { 
    
    var locationid = $('#Tplocid').text();      
    var checkin = $('#Cin').text();  
    var checkout = $('#Cout').text();  
    var rooms = $('#rooms').text();0
    var guest = $('#guest').text();  
    var Tid = $('.Tid').text();  
   var type = $('.ptype').text();
    if(type == 'withdate'){
    function updateSearchResults(page) {
      var func = 'withdate';
    
        $.ajax({
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: base_url + 'getfilteredhotellist',
            data: {
                'locationid': locationid,
                'checkin': checkin,
                'checkout': checkout,
                'rooms': rooms,
                'guest': guest,
                'page': page,
                'Tid': Tid
            },
            success: function(response) {
                $('.filter-listing').html(response.html);
                $('.hotel_count').html(response.count_result + ' properties found');
          
           if (response.uniqueAgencies !=null) {        
            var agenciesHtml = '<h5>Booking providers</h5><ul>';        
            $.each(response.uniqueAgencies, function(index, agency) {
              agenciesHtml += '<li class="tr-filter-list hl-filter">' +
                        '<label class="tr-check-box">' +
                        '<input type="checkbox" name="agency" class="filter" value="' + agency + '">' +
                        agency +
                        '<span class="checkmark"></span>' +
                      '</label>' +
                    '</li>';
            });
            agenciesHtml += '</ul>';          
            $('#agencies').html(agenciesHtml);        
           }
          
                  // $('.hl-filter').change(function() {
                  //   fetchFiltered(1); 
                  // });
  
                // Reinitialize filter listeners
                initializeFilterListeners();
             //start new code
                   
                    $('#mapModal .tr-filters-section').empty(); 
                    $('#mapModal .tr-room-section-2').empty(); 
                    $('.tr-filters-section .tr-filter-lists').each(function() {
                        var FilterLists = $(this).clone();
                        $('#mapModal .tr-filters-section').append(FilterLists);
                    });
                    $('.tr-room-section-2 .tr-hotel-deatils').each(function() {
                        var HotelLists = $(this).clone();
                        $('#mapModal .tr-room-section-2').append(HotelLists);
                    });
                    $('#mapModal .carousel').each(function() {
              var currentId = $(this).attr('id');
              if (currentId) {
              var newId = currentId + '-1';
              $(this).attr('id', newId);
              $('#mapModal button[data-bs-target="#' + currentId + '"]').each(function() {
                $(this).attr('data-bs-target', '#' + newId);
              });
              }
            });
  
                //end new code
            },
            error: function() {
             //   alert('Failed to load results. Please try again.');
            }
        });
    }
  
    
    updateSearchResults(1);
  
  
    $(document).on('click', '.pagination a', function(e) {
      e.preventDefault();
      var page = $(this).attr('href').split('page=')[1];
      var ptype = $('.page_type').text();
    
      if (ptype == 'getlist') {
          updateSearchResults(page);
      }else if(ptype == 'filter'){
        fetchFiltered(page);
      }
      $('html, body').animate({ scrollTop: $('.filter-listing').offset().top }, 'slow');
    });
    
      }
  });
//end all hotel code
    
    
    
    $(document).ready(function() {
      var locationid =  $('#Tplocid').text();  
        var slugid =  $('#slugid').text();  
      
      $.ajax({
        type: 'Post',
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        url: base_url + 'addHotleListingFaq',
        data: { 'locationid': locationid,'slugid':slugid},
        success: function(response) {
      
          var hotelfaq= response.html
          
          $('#faqdata').html(hotelfaq);
      
        },
      
       });
      });
    
    
    
    
    
     //hotel list data
     function chechavail(){
            if(window.matchMedia('(min-width: 769px)').matches){
                    $('.tr-view-availability-btn').on('click', function() {
                        $('html, body').animate({ scrollTop: $('#checkInInput3').offset().top - 160 }, 500);
                        calendarsBox3();
            });
            };
        }
                 
        function mobilecheckavail(){
            $(document).ready(function() {
            $('.tr-mobile-when, .tr-view-availability-btn').click(function() {
                $('html, body').animate({ scrollTop: $('.tr-search-hotel').offset().top - 75 }, 500);
                $(".tr-form-booking-date").addClass("open");
                setTimeout(function() {
                if($(".tr-form-booking-date").hasClass('open')){
                    $('#checkInInput3').click();
                    $('#checkInInput3').focus();
                    $('.custom-calendar').show();
                }
                }, 100);
            });
            });     
        }
    
        $(document).ready(function() { 
          var locationid = $('.slugid').text(); 
          var lname = $('.lname').text();       
          var type = $('.ptype').text();
          var st = $('.filter-st').text();   	
          var amenity = $('.filter-amenity').text();
		  var rs = $('.filter-rs').text();   	
          var price = $('.filter-price').text();

        
          if (type == 'withoutdate'){
              function hotellisting(page) {
                  $.ajax({
                      type: 'POST',
                      headers: {
                          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },
                      url: base_url + 'getwithoutdatedata',
                       data: {
                        'locationid': locationid,
                        'lname': lname,
                        'amenity':amenity,
                        'starrating':st,
                        'price':price,
                        'rs':rs,
                        'page': page,
                        'amenity_ids': window.amenityIds || [],
                        'neighborhood_info': window.neighborhood_info || [],
						'sight_info': window.sight_info || [],
                    },
                      success: function(response) {
                          $('.filter-listing').html(response);
                chechavail();
                      // Read More, 3 list show - Common
                      $('.list-content').each(function () {
                        var $container = $(this);
                        var $content = $container.find('ul li');
                        var $toggleButton = $container.find('.toggle-list');
                        var limit = 2;
                        $content.slice(0, limit).addClass('visible');
                        $toggleButton.on('click', function () {
                          if ($toggleButton.text() === 'Read More') {
                            $content.addClass('visible');
                            $toggleButton.text('Read Less');
                          } else {
                            $content.slice(limit).removeClass('visible');
                            $toggleButton.text('Read More');
                          }
                        });
                        if ($content.length <= limit) {
                          $toggleButton.hide();
                        }
                      });
                      // Read More, Paragraph - Common Code
                      $(document).ready(function () {
                        $(".paragraph-content").each(function () {
                          var $contentPara = $(this).find(".para-content");
                          var $toggleParaButton = $(this).find(".toggle-para");
                          var originalContent = $contentPara.html();
                          var wordsArray = originalContent.split(" ");
                          var limit = 70;
                          if (wordsArray.length > limit) {
                            var visibleText = wordsArray.slice(0, limit).join(" ") + "...";
                            $contentPara.html(visibleText);
                            $toggleParaButton.on("click", function () {
                              if ($toggleParaButton.text() === "Read More") {
                                $contentPara.html(originalContent);
                                $toggleParaButton.text("Read Less");
                              } else {
                                $contentPara.html(visibleText);
                                $toggleParaButton.text("Read More");
                              }
                            });
                          } else {
                            $toggleParaButton.hide();
                          }
                        });
                      });
                          mobilecheckavail();
                      },
                      error: function() {
                          // Handle error here
                      }
                  });
              }
          
              // Initial load
              hotellisting(1);
          
              $(document).on('click', '.pagination a', function(e) {
                  e.preventDefault();
                  var page = $(this).attr('href').split('page=')[1];
                  hotellisting(page);
                  $('html, body').animate({ scrollTop: $('.filter-listing').offset().top }, 'slow');
              });
          }
      });
    
