
// assets/js/includes.js
document.addEventListener('DOMContentLoaded', function () {
  const includeElements = document.querySelectorAll('[data-include-html]');

  includeElements.forEach(function (el) {
    const file = el.getAttribute('data-include-html');
    if (!file) return;

    fetch(file)
      .then(function (response) {
        if (!response.ok) {
          throw new Error('Erreur de chargement : ' + file + ' (' + response.status + ')');
        }
        return response.text();
      })
      .then(function (data) {
        el.innerHTML = data;

        // Met à jour l'année dans le footer si besoin
        const yearSpan = el.querySelector('#current-year');
        if (yearSpan) {
          yearSpan.textContent = new Date().getFullYear();
        }
      })
      .catch(function (error) {
        console.error(error);
      });
  });
});
