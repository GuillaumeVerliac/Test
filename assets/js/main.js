// assets/js/main.js

document.addEventListener("DOMContentLoaded", () => {
  loadPartials();
});

/* ================================
   CHARGEMENT HEADER + FOOTER
================================= */
function loadPartials() {
  const headerPlaceholder = document.getElementById("site-header");
  const footerPlaceholder = document.getElementById("site-footer");

  // Charger le header
  if (headerPlaceholder) {
    fetch("partials/header.html")
      .then((res) => res.text())
      .then((html) => {
        headerPlaceholder.innerHTML = html;
        initNavToggle();
        initHeaderScrollBehavior();
      })
      .catch((err) => console.error("Erreur chargement header:", err));
  }

  // Charger le footer
  if (footerPlaceholder) {
    fetch("partials/footer.html")
      .then((res) => res.text())
      .then((html) => {
        footerPlaceholder.innerHTML = html;
      })
      .catch((err) => console.error("Erreur chargement footer:", err));
  }
}

/* ================================
   NAV MOBILE
================================= */
function initNavToggle() {
  const header = document.querySelector(".site-header");
  if (!header) return;

  const toggleBtn = header.querySelector(".nav-toggle");
  const nav = header.querySelector(".main-nav");

  if (!toggleBtn || !nav) return;

  toggleBtn.addEventListener("click", () => {
    nav.classList.toggle("nav-open");
  });
}

/* ================================
   HEADER SHOW/HIDE ON SCROLL
================================= */
function initHeaderScrollBehavior() {
  const header = document.querySelector(".site-header");
  if (!header) {
    console.warn("Header non trouvé pour le scroll.");
    return;
  }

  let lastY = window.scrollY;

  window.addEventListener("scroll", () => {
    const currentY = window.scrollY;

    // Toujours visible tout en haut
    if (currentY < 80) {
      header.classList.remove("header-hidden");
    } else if (currentY > lastY) {
      // On descend => cacher
      header.classList.add("header-hidden");
    } else if (currentY < lastY) {
      // On remonte => montrer
      header.classList.remove("header-hidden");
    }

    lastY = currentY;
  });
}
