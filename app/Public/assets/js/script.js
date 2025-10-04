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
const links = document.querySelectorAll('.sidebar-item')
const currentUrl = location.pathname;

links.forEach(link => {
  if (link.getAttribute('href') === currentUrl) {
    link.classList.add('active')
  } else {
    link.classList.remove('active')
  }
})

// Le modal de modification
document.getElementById('editModal').addEventListener('show.bs.modal', function (event) {
  const button = event.relatedTarget;
  if (button.classList.contains('edit-btn')) {
    const id = button.getAttribute('data-id')
    const title = button.getAttribute('data-title')
    const description = button.getAttribute('data-description')
    const categoty_id = button.getAttribute('data-category-id')
    const is_public = button.getAttribute('data-is-public')

    document.getElementById('doc_id').value = id
    document.getElementById('title').value = title
    document.getElementById('description').value = description
    document.getElementById('category_id').value = categoty_id

    const selectedElement = document.getElementById('is_public')

    selectedElement.value = is_public

    document.getElementById('editModalLabel').textContent = "Modifier le document"
  }
})

// Gestion de la soumission du formulaire d'édition
document.getElementById('editDocumentForm').addEventListener('submit', function(e) {
    e.preventDefault()
    
    const formData = new FormData(this)
    
    fetch('/documents/update', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fermer le modal et recharger la page            
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide()
            location.reload()
        } else {
            alert('Erreur: ' + (data.message || 'Erreur lors de la modification'))
        }
    })
    .catch(error => {
        console.error('Erreur:', error)
        alert('Erreur lors de la modification du document')
    })
})





