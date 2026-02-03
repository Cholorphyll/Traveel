



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


