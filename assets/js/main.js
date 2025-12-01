// assets/js/main.js

document.addEventListener("DOMContentLoaded", () => {
  const headerPlaceholder = document.getElementById("site-header");
  const footerPlaceholder = document.getElementById("site-footer");

  // Injecte le header
  if (headerPlaceholder) {
    fetch("partials/header.html")
      .then((response) => response.text())
      .then((html) => {
        headerPlaceholder.innerHTML = html;
        initNavToggle();
        initHeaderScrollBehavior();
      })
      .catch((err) => console.error("Erreur chargement header:", err));
  }

  // Injecte le footer
  if (footerPlaceholder) {
    fetch("partials/footer.html")
      .then((response) => response.text())
      .then((html) => {
        footerPlaceholder.innerHTML = html;
      })
      .catch((err) => console.error("Erreur chargement footer:", err));
  }
});

// Gestion du burger / nav mobile
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

// Comportement du header selon le scroll
function initHeaderScrollBehavior() {
  const header = document.querySelector(".site-header");
  if (!header) return;

  let lastScrollY = window.scrollY;
  let ticking = false;

  const updateHeader = () => {
    const currentScrollY = window.scrollY;
    const delta = currentScrollY - lastScrollY;

    // Toujours visible près du haut de page
    if (currentScrollY < 80) {
      header.classList.remove("header-hidden");
    } else {
      if (delta > 0) {
        // on descend -> cacher le header
        header.classList.add("header-hidden");
      } else if (delta < 0) {
        // on remonte -> montrer le header
        header.classList.remove("header-hidden");
      }
    }

    lastScrollY = currentScrollY;
    ticking = false;
  };

  window.addEventListener("scroll", () => {
    if (!ticking) {
      window.requestAnimationFrame(updateHeader);
      ticking = true;
    }
  });
}

