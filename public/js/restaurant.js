var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';

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










$(document).ready(function () {
    $('input').val('')
});

$('.bloglistingcarousel').each(function () {
    var slider = $(this);
    slider.slick({
        dots: true,
        autoplay: true,
        autoplaySpeed: 5000,
        mobileFirst: true,
        arrows: false,
        responsive: [{
            breakpoint: 480,
            settings: "unslick"
        }]
    });

});












const minus = $('.roomsdecrement');
const input = $('.roominputfield');

// const adultincrement = $('.adultincrement')
// const childreincrement = $('.childreincrement')
// var totalPoints = childronnum + adultrnum;
// var childronnum = 0;
// var adultrnum = 0;
// const totalguests = $('.totalguests');
// // const plus = $('.increment');
// // const reset = $('.Reset');
// // const apply = $('.apply');
// // const totalguests = $('.totalguests');
const roomincrement = $('.roomincrement')
const inputrooms = $('#inputRooms');
const roomcount = $('.roomcount');

roomincrement.click(function () {
    var value = $(this).siblings(input).val();
    value++;
    console.log(value);

    $(this).siblings(input).val(value);
    $('.roomcount').text(value)
})

// input.text(0);

minus.click(function () {

    var value = $(this).siblings(input).val();
    if (value > 1) {
        value--;
    }
    $(this).siblings(input).val(value);
    $('.roomcount').text(value)
});


// adultincrement.click(function () {
//     var value = $(this).siblings(input).val();
//     value++;
//     $(this).siblings(input).val(value);
//     adultrnum = value;
//     totalPoints = childronnum + adultrnum
//     totalguests.text(totalPoints);
// })
// childreincrement.click(function () {
//     var value = $(this).siblings(input).val();
//     value++;
//     $(this).siblings(input).val(value);
//     childronnum = value;
//     totalPoints = childronnum + adultrnum
//     totalguests.text(totalPoints);
// })


// $(reset).click(function (e) {
//     e.preventDefault();
//     input.val(0)
//     $('#inputRooms').val(0)
//     childronnum.parseFloat($(this).val()) + totalPoints
// });


// $(apply).click(function (e) {
//     e.preventDefault();
//     var totalPoints = 0;
//     $('.counter .inputfield').each(function () {
//         totalPoints = parseFloat($(this).val()) + totalPoints;
//     });
//     totalguests.text(totalPoints - 1);
//     roomcount.text(inputrooms.val())
// });// $('.checkin').click(function (e) {
//     e.preventDefault();
//     $(this).addClass('active-border');
//     $('.checkout').removeClass('active-border');

// });
// $('.checkout').click(function (e) {
//     e.preventDefault();
//     $('.checkin').removeClass('active-border');
//     $(this).removeClass('disabled');
//     $(this).addClass('active-border');
// });


// $('.view-more').click(function (e) {
//     e.preventDefault();
//     $('input[name="datefilter"]').data('daterangepicker').show();

// });




if ($(window).width() < 606) {
    $('.typeahed').click(function (e) {
        e.preventDefault();
        $('.other-sections').hide();
        $(this).addClass('unstyletypehead');
    });

    $(document).click(function (event) {
        var $target = $(event.target);
        if (!$target.closest('.typeahed').length) {
            $('.other-sections').show();
            $('.typeahed').removeClass('unstyletypehead');
        }
    });
}


jQuery(document).ready(function () {

    jQuery('.more').readmore({
        speed: 300,
        collapsedHeight: 140,
        moreLink: '<a href="#" class="text-decoration-none d-inline-block mt-34"><b class="fw-500 fs12">View more <img src="images/arrow-down.svg" class="ml-10" alt=""></b></a>',
        lessLink: '<a href="#" class="text-decoration-none d-inline-block mt-34"><b class="fw-500 fs12">View less <img src="images/uparrow.png" class="ml-10" alt=""></b></a>',
        heightMargin: 16
    });

});

$(document).on('click', '.add-rest-review', function () {
    $('.loadResult').removeClass('hide');

    var desc = $('.review_desc').val();
    if (desc == "") {
        var activeElement = $(".rev-desc.active");
        desc = activeElement.text();
    }
    var isValid = true;
    var name = $('.name').val();
    if(name==""){
        $('.name-error').text('Name is required.').css('color', 'red');
        isValid = false;
    }
    var email = $('.email').val();
    if(email==""){
        $('.email-error').text('Email is required.').css('color', 'red');
        isValid = false;
    } else {      
        var emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;    
        if (!email.match(emailPattern)) {
            $('.email-error').text('Invalid email format.').css('color', 'red');
            isValid = false;
        } else {      
            $('.email-error').text(''); 
        }
    }

    if(!isValid){
        return;
    }
    $('.email-error').text('');

    $('.name-error').text('');

    $('.name-error').text('Please wait..').css('color', 'green');
    var formData = new FormData();
    var files = $('#files')[0].files;
    var restid = $('.rest_id').text();
    for (var i = 0; i < files.length; i++) {
        formData.append('files[]', files[i]);
    }
    var rating = $('.page-item.active').text();

    formData.append('name',name);
    formData.append('email',email);
    formData.append('desc', desc);
    formData.append('rating',rating);
    formData.append('restid',restid)

    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'post',
        url: base_url + 'add_rest_review',
        data: formData, 
        contentType: false,
        processData: false, 
        success: function (response) {
            $('#exampleModal').modal('hide');
            $('.name').val('');
            $('.email').val('');
            $(".pip").text('');
            $('.review-update').html(response);
        },
        error: function (error) {
            console.error('Error:', error);
        }
    });
});

$(document).ready(function() {
    $('.pagination.star-rating .page-item a.page-link').on('click', function() {  

        $('.pagination.star-rating .page-item').removeClass('active');       
    
        $(this).closest('.page-item').addClass('active');
    });
});





function updateMapWithFilteredData(getrest) {
  
    for (var key in markers) {
      map.removeLayer(markers[key]);
    }
    markers = {};   
 
    var newLocations = [];
    getrest.forEach(function(data) {
      if (data.Latitude && data.Longitude) {
        var marker = new L.Marker([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
        marker.addTo(map);
        markers[data.RestaurantId] = marker;
        newLocations.push([parseFloat(data.Latitude), parseFloat(data.Longitude)]);
      }
    });
     
    var group = new L.featureGroup(newLocations);
    map.fitBounds(group.getBounds());
  }
  
  
  // Event handler for the click event
  $(document).on('click', '.filter_restbycat', function() {
    var cat = $(this).text();
    var locid = $(this).data('id');
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'post',
      url: base_url + 'filterrestbycat',
      data: {
        'catid': cat,
        'locationId': locid,
        '_token': $('meta[name="csrf-token"]').attr('content')
      },
      success: function(response) {
        $('.get_result').html(response.htmlView); // Update HTML view
        var mapData = JSON.parse(response.mapData); // Parse JSON data for map
        updateMapWithFilteredData(mapData);
      },
      error: function(error) {
        console.error('Error fetching filtered data:', error);
      }
    });
  });

//listing view more hide and show
  const viewMore = document.getElementById('viewMore');
  const hiddenResults = document.getElementById('hiddenResults');

  if (viewMore) {
      viewMore.addEventListener('click', function () {
          if (hiddenResults.style.display === 'none') {
              hiddenResults.style.display = 'block';           
              viewMore.querySelector('u').textContent = 'View Less'; 
          } else {
              hiddenResults.style.display = 'none';            
              viewMore.querySelector('u').textContent = 'View More';
          }
      });
  }


// inf scroll
let page = 1; // Start with page 2 since page 1 is loaded initially
let loading = false;
var locid = $('#locid').text();
var slug = $('#slug').text();

// Get all shown IDs from the hidden input
let shownIds = [];
if (document.getElementById('shown-attraction-ids')) {
    const shownIdsValue = document.getElementById('shown-attraction-ids').value;
    if (shownIdsValue) {
        shownIds = shownIdsValue.split(',');
    }
}
console.log('Initially shown IDs:', shownIds);
   
// Function to load more content
function loadMoreContent() {
    if (!loading) {
        loading = true; 
        document.getElementById('loading').style.display = 'block';

        console.log(`Loading more attractions: page=${page}, locid=${locid}, slug=${slug}, shownIds count=${shownIds.length}`);

        // Make an AJAX request to load more data
        fetch(base_url + `load-more-attractions?page=${page}&locid=${locid}&slug=${slug}&shownIds=${shownIds.join(',')}`)
        .then((response) => response.json())
        .then((data) => {
            console.log('Load more response:', data);
            
            // Check if there's HTML content to add
            if (data.html && data.html.trim() !== '') {
                // Get the container and the load more button
                const container = document.getElementById('getcatfilterdata');
                const loadMoreButton = document.querySelector('.tr-load-more');
                
                // Insert new content before the load more button
                loadMoreButton.insertAdjacentHTML('beforebegin', data.html);
                
                // Add new IDs to the shown IDs array
                if (data.newIds && Array.isArray(data.newIds)) {
                    shownIds = shownIds.concat(data.newIds);
                    console.log('Updated shown IDs count:', shownIds.length);
                }

                // Increment the page number
                page++;
                
                // If no more data, hide the load more button
                if (!data.hasMore) {
                    loadMoreButton.style.display = 'none';
                }
            } else {
                // If no HTML content, hide the load more button
                document.querySelector('.tr-load-more').style.display = 'none';
                console.log('No more attractions to load');
            }

            // Hide the loading indicator
            document.getElementById('loading').style.display = 'none';
            loading = false;
            
            // Update map if map data is available
            if (data.mapData) {
                try {
                    var mapData = JSON.parse(data.mapData); // Parse JSON data for map
                    updateMapWithFilteredDataas(mapData);
                } catch (e) {
                    console.error('Error parsing map data:', e);
                }
            } else {
                console.log('No mapData in the response');
            }
        })
        .catch((error) => {
            console.error('Error loading more content:', error);
            document.getElementById('loading').style.display = 'none';
            loading = false;
        });
    }
}

// Function to check if the user has scrolled to the bottom of the page
function checkScroll() {
    const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    const clientHeight = document.documentElement.clientHeight;
    const scrollHeight = document.documentElement.scrollHeight;

    if (scrollTop + clientHeight >= scrollHeight - 200) {
        loadMoreContent();
    }
}



window.addEventListener('scroll', checkScroll);

document.querySelector('.tr-load-more').addEventListener('click', function() {
    loadMoreContent();
});

loadMoreContent();
