// assets/js/main.js

// Inject header & footer
document.addEventListener("DOMContentLoaded", () => {
  const headerPlaceholder = document.getElementById("site-header");
  const footerPlaceholder = document.getElementById("site-footer");

  if (headerPlaceholder) {
    fetch("partials/header.html")
      .then((response) => response.text())
      .then((html) => {
        headerPlaceholder.innerHTML = html;
        initNavToggle();
      });
  }

  if (footerPlaceholder) {
    fetch("partials/footer.html")
      .then((response) => response.text())
      .then((html) => {
        footerPlaceholder.innerHTML = html;
        setCurrentYear();
      });
  }
});

function setCurrentYear() {
  const yearSpan = document.getElementById("current-year");
  if (yearSpan) {
    yearSpan.textContent = new Date().getFullYear();
  }
}

function initNavToggle() {
  const header = document.querySelector(".site-header");
  if (!header) return;
  const toggleBtn = header.querySelector(".nav-toggle");
  const nav = header.querySelector(".main-nav");

  if (toggleBtn && nav) {
    toggleBtn.addEventListener("click", () => {
      nav.classList.toggle("nav-open");
    });
  }
}
