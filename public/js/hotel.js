
// Add event listeners to stars
// const stars = document.querySelectorAll('.star');

// stars.forEach((star, index) => {
//     star.addEventListener('click', function() {
//         for (let i = 0; i <= 5; i++) {
//              stars[i].classList.replace('far', 'fas');
//              if (i <= index ) {
//                stars[i].classList.replace('far', 'fas');
//              } else {
//                  stars[i].classList.replace('fas', 'far');
//              }
//         }

//     });
// });
$(document).ready(function() {
  $('.reviewLink').click(function() {
    var value = $(this).text().replace(/\n/g, '').replace(/\s+/g, ' ').trim();
    $('#review').val(value);
    $('.add_review').removeClass("d-none");
  });
});



$(document).on('click', '#addReview', function() {
  var sightId = $('#sightId').val();
  var countrt = 0;
  $('.ratings .fas').each(function() {
    countrt++;
  });
  var name = $('#name').val();
  var email = $('#email').val();
  var review = $('#review').val();
  var hotelid = $('#hotelid').val();
  
  // Field validation
  var isValid = true;

  if (name == '') {
    $('#name-error').text('Name is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#name-error').text('');
  }

  if (email == '') {
    $('#email-error').text('Email is required.').css('color', 'red');
    isValid = false;
  } else if (!isValidEmail(email)) {
    $('#email-error').text('Please enter a valid email.').css('color', 'red');
    isValid = false;
  } else {
    $('#email-error').text('').css('color', 'red');
  }

  if (review == '') {
    $('#review-error').text('Review Description is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#review-error').text('');
  }

  if (countrt == 0) {
    $('#rating-error').text('Rating is required.').css('color', 'red');
    isValid = false;
  } else {
    $('#rating-error').text('');
  }
  if (!isValid) {
    return;
  }
  $('.add_review').addClass("d-none");

  $.ajax({
    type: 'Post',
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    url: base_url + 'add_hotelreview',
    data: { 'rating': countrt, 'name': name, 'email': email, 'review': review, 'hotelid':hotelid},
    success: function(response) {
      $('.add_review').addClass("d-none");
      $('.star').removeClass('fas');
      $('.star').addClass('far');
      $('#name').val('');
      $('#email').val('');
      $('#review').val('');
    
      $('#msg').html('<div class="alert alert-success" role="alert">' + response + '</div>');

      var alertTimeout = setTimeout(function() {
          $('#msg').empty(); // Clear the message after one minute
      }, 60000); // 60000 milliseconds = 1 minute
      
     //  alert(response);
    },
    error: function(xhr, status, error) {
      // Handle the error
      console.log(error);
    }
  });
});

function isValidEmail(email) {
  // Regular expression for email validation
  var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailPattern.test(email);
}
 

 // Add event listeners to stars
//  const stars = document.querySelectorAll('.add_review .star');

//  stars.forEach((star, index) => {
//      star.addEventListener('click', function() {
//          for (let i = 0; i <= 5; i++) {
//               stars[i].classList.replace('far', 'fas');
//               if (i <= index ) {
//                 stars[i].classList.replace('far', 'fas');
//               } else {
//                   stars[i].classList.replace('fas', 'far');
//               }
//          }

//      });
//  });


let add_review = document.querySelector(".add_review");
let aadrev = document.querySelector(".aadrev");

aadrev.addEventListener("click", () => {
  add_review.classList.remove("d-none");
});

document.addEventListener("click", (event) => {
  if (event.target.classList.contains("close-box")) {
    add_review.classList.add("d-none");
  }
});

document.querySelectorAll(".expand").forEach((el) =>
  el.addEventListener("click", function () {
    if (el.querySelector("span").innerHTML=="Expand") {
      el.innerHTML=`<span>Collapse</span> <i class="fas fa-angle-up mx-1 "></i>`
    }else{
      el.innerHTML=` <span>Expand</span><i class="fas fa-angle-down mx-1"></i>`

    }
  })
);


if (window.innerWidth < 767) {
  $(function () {
    $("#checkindate").daterangepicker(
      {
        autoApply: false,
      },
      function (start, end, label) {
        $("#checkindate").val(start.format("MM/DD/YYYY"));
        $("#checkoutdate").val(end.format("MM/DD/YYYY"));
      }
    );
  });
} else {
  $(function () {
    $("#checkindate").daterangepicker(
      {
        autoApply: true,
        opens: "left",
      },
      function (start, end, label) {
        $("#checkindate").val(start.format("MM/DD/YYYY"));
        $("#checkoutdate").val(end.format("MM/DD/YYYY"));
      }
    );
  });
}

// light box

document.querySelector(".lightbox .close").addEventListener("click", () => {
  document.querySelector(".lightbox ").classList.add("d-none");
  document.querySelector(".lightbox ").classList.remove("position-fixed");
});
document.querySelector(".all-photos").addEventListener("click", () => {
  document.querySelector(".lightbox ").classList.remove("d-none");
  document.querySelector(".lightbox ").classList.add("position-fixed");
});

document
  .querySelector(".lightbox .like")
  .addEventListener("click", function () {
    this.classList.toggle("text-primary");
  });

$(".owl-carousel").owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
  dots: false,
  navText: [
    '<i class="fa fa-angle-left" aria-hidden="true"></i>',
    '<i class="fa fa-angle-right" aria-hidden="true"></i>',
  ],

  responsive: {
    0: {
      items: 4,
    },
    600: {
      items: 6,
    },
    1000: {
      items: 7,
    },
  },
});

//   end light box



//new code
document.querySelector(".lightbox .close").addEventListener("click", () => {
  document.querySelector(".lightbox").classList.add("d-none");
  document.querySelector(".lightbox").classList.remove("position-fixed");
});

document.querySelector(".all-photos").addEventListener("click", () => {  
  document.querySelector(".lightbox").classList.remove("d-none");
  document.querySelector(".lightbox").classList.add("position-fixed");
});

document.querySelector(".lightbox .like").addEventListener("click", function () {
  this.classList.toggle("text-primary");
});

$(".owl-carousel").owlCarousel({
  loop: true,
  margin: 10,
  nav: true,
  dots: false,
  navText: [
      '<i class="fa fa-angle-left" aria-hidden="true"></i>',
      '<i class="fa fa-angle-right" aria-hidden="true"></i>',
  ],
  responsive: {
      0: {
          items: 4,
      },
      600: {
          items: 6,
      },
      1000: {
          items: 7,
      },
  },
});

const showAllBtn = document.querySelector(".show-all-images");
const hiddenImages = document.querySelector(".hidden-images");

showAllBtn.addEventListener("click", function () {
  hiddenImages.classList.remove("d-none");
  hiddenImages.classList.add("position-fixed");
  document.querySelector(".lightbox").classList.add("d-none");
  document.querySelector(".lightbox").classList.remove("position-fixed");
});

