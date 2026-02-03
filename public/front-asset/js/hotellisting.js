let closeFilter = document.querySelector(".colse-filter");
let filtersSection = document.querySelector(".filters");
let openFilter = document.querySelector(".open-filter");
closeFilter.addEventListener("click", () => {
  filtersSection.classList.remove("show");
  closeFilter.classList.add("d-none");
});
openFilter.addEventListener("click", () => {
  filtersSection.classList.add("show");
  closeFilter.classList.remove("d-none");
});

// multi slider
$(function () {
  $("#slider-range").slider({
    range: true,
    min: 0,
    max: 250,
    values: [0, 140],
    slide: function (event, ui) {
      $("#amount").val("$" + ui.values[0] + " - $" + ui.values[1]);
    },
  });
  $("#amount").val(
    "$" +
      $("#slider-range").slider("values", 0) +
      " - $" +
      $("#slider-range").slider("values", 1)
  );
});

document.querySelector(".rooms-select").addEventListener("click", () => {
  // document.querySelector("#");
  document.querySelector(".rooms-num").classList.toggle("d-none");
});

// collapse
document.querySelectorAll(".fa-angle-up").forEach((item) =>
  item.addEventListener("click", function () {
    this.classList.toggle("fa-rotate-180");
  })
);
$(function () {
  $("#checkindate").daterangepicker(
    {
      autoApply: true,
    },
    function (start, end, label) {
      $("#checkindate").val(start.format("DD-MM-YYYY"));
      $("#checkoutdate").val(end.format("DD-MM-YYYY"));
    }
  );
});
document
.querySelector("#checkindate")
.addEventListener("focus", function () {
  document.querySelector(".check-in .check-hover").classList.add("active");
});
document
    .querySelector("#checkindate")
    .addEventListener("blur", function () {
      document.querySelector(".check-in .check-hover").classList.remove("active");
    });

