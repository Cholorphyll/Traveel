let serchInput = document.querySelector("header .search-box input");
let serchBox = document.querySelector("header .search-box");
let serchClose = document.querySelector("header  .close");
let serchinfo = document.querySelector("header .search-info");
let serchBoxinfo = document.querySelector("header .search-box-info");

if (serchClose.classList.contains("d-none")) {
  serchBox.addEventListener("click", () => {
    serchInput.focus();
    serchBox.classList.add("active");
    serchinfo.classList.add("d-none");
    serchBoxinfo.classList.remove("d-none");
    serchInput.classList.remove("fs-22");
    serchInput.classList.add("fs-16");
    serchClose.classList.remove("d-none");
    serchBox.classList.remove("justify-content-center");
    serchInput.placeholder ="Where are you going?";

  });
} else {
  serchBox.addEventListener("click", () => {
    serchInput.blur();
  });
}
serchInput.addEventListener("blur", () => {
  serchBox.classList.add("justify-content-center");
  serchInput.placeholder ="Delhi";

  serchBox.classList.remove("active");
  serchInput.classList.add("fs-22");
  serchInput.classList.remove("fs-16");
  serchBoxinfo.classList.add("d-none");
  serchinfo.classList.remove("d-none");
  serchClose.classList.add("d-none");
});
serchClose.addEventListener("click", () => {
  serchInput.blur();
  serchBox.classList.remove("active");
});

// end search