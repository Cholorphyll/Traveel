var baseURL = window.location.protocol + "//" + window.location.hostname + (window.location.port ? ':' + window.location.port : '');
var base_url = baseURL + '/';



 

var searchTimeout;

$(document).on('keyup', '.searchlocations', function() {
    var value = $(this).val();    

    clearTimeout(searchTimeout);
    
    searchTimeout = setTimeout(function() {
        performSearch(value);
    }, 500);
});

function performSearch(value) {
    if (value.length >= 1) {
	
        $("#cat-list").css("display", "block");
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
                $('#loc-lists').html("");
                $('#recent-search').addClass('d-none');
                $('#loc-lists').html(response);
            }
        });
    } else {
        $(".recent-his2").removeClass("d-none");
        $("#cat-list").css("display", "block");
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
				
                $('#loc-lists').html("");
                $('#loc-lists').html(response);
            }
        });
    }
}








$(document).ready(function() {
  
  $('.searchlocations').on('click', function() {
   
    var value = $(this).val();
    if (value.length <= 0) {
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
          $(".recent-his2").removeClass("d-none");
          var resultList = $('#searchResults');
          resultList.empty();
          $('#loc-lists').html(response);
          
        }
      });
    }
  });
});





$(document).ready(function() {
  var searchResultsContainer = $('.recent-his2');

  $(document).on('click', function(event) {
    var targetElement = $(event.target); 

 
    if (!targetElement.closest('.explore-search').length) {
      searchResultsContainer.hide(); 
    }
  });

  $('.searchlocations').on('click', function() {
    searchResultsContainer.show(); 
  });
});