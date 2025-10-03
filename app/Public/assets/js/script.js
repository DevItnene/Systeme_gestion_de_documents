// Animation pour les inputs
document.addEventListener("DOMContentLoaded", function () {
  const inputs = document.querySelectorAll(".form-control");
  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("focused");
    });

    input.addEventListener("blur", function () {
      if (this.value === "") {
        this.parentElement.classList.remove("focused");
      }
    });
  });
});

// Animation pour les cartes
document.addEventListener('DOMContentLoaded', function() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.animationDelay = (index * 0.1) + 's';
        card.classList.add('animate__animated', 'animate__fadeInUp');
    });
});

// Activer un lien du sidebar
links = document.querySelectorAll('.sidebar-item')
currentUrl = location.pathname;

links.forEach(link => {
  if (link.getAttribute('href') === currentUrl) {
    link.classList.add('active')
  } else {
    link.classList.remove('active')
  }
})

// Manipulations des actions des bouttons tables
links_action = document.querySelectorAll('.links-action a')
console.log(links_action);

// links_action.forEach(link => {
//   link.addEventListener('click', () => {
    
//   })
// })




