$(document).ready(function () {
  $('.tr-budget-hotels-near-you .tr-hotel-facilities').each(function () {
    var $container = $(this);
    var $content = $container.find('li');
    var $toggleButton = $container.find('.toggle-list');
    
    var targetLength = 91; // Target total character length
    var accumulatedLength = 0;
    var limit = 0;

    // Loop through each <li> to accumulate character lengths
    $content.each(function(index) {
      var currentItemLength = $(this).text().length;
      accumulatedLength += currentItemLength;

      // If accumulated length reaches or exceeds the target length, stop
      if (accumulatedLength >= targetLength) {
        limit = index + 1;  // Show this <li> and stop
        return false;  // Exit the each loop
      }
    });

    // Apply initial visibility to the content based on the calculated limit
    $content.slice(0, limit).addClass('visible');
    
    // Toggle functionality for Read More/Read Less
    $toggleButton.on('click', function () {
      if ($toggleButton.text() === 'Read More') {
        $content.addClass('visible');
        $toggleButton.text('Read Less');
        $toggleButton.attr('title', 'Read Less');
        $container.addClass('showing-all');
      } else {
        $content.slice(limit).removeClass('visible');
        $toggleButton.text('Read More');
        $toggleButton.attr('title', 'Read More');
        $container.removeClass('showing-all');
      }
    });

    // Hide the button if the content length is less than or equal to the limit
    if ($content.length <= limit) {
      $toggleButton.hide();
    }
  });
});

