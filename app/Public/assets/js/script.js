(() => {
  'use strict';

  const forms = document.querySelectorAll('form');
  const allowedExtensions = ['pdf', 'txt', 'pptx', 'xlsx', 'docx', 'csv', 'json'];

  forms.forEach(form => {
    form.addEventListener('submit', event => {
      let isFileValid = true;
      const fileInput = form.querySelector('#document_file');
      if (fileInput) {
        const file = fileInput.files[0];
        
        if (file) {
        const fileName = file.name;
        const extension = fileName.split('.').pop().toLowerCase();
        
        
        if (!allowedExtensions.includes(extension)) {
          isFileValid = false;

          fileInput.classList.add('is-invalid');
          fileInput.classList.remove('is-valid');

          fileInput.value = '';

          const feedback = fileInput.nextElementSibling;
          if (feedback) {
            feedback.textContent = `Format non autorisé : .${extension}. Formats acceptés : ${allowedExtensions.join(', ')}`;
            feedback.style.color = 'red'
          }
        } else {
          fileInput.classList.remove('is-invalid');
          fileInput.classList.add('is-valid');
        }
      } else {
        // Si le fichier est obligatoire
        // isFileValid = false;
        fileInput.classList.remove('is-invalid');
        fileInput.classList.remove('is-valid');
      }

      if (!form.checkValidity() || !isFileValid) {
          event.preventDefault();
          event.stopPropagation();
        }
      }
      
      form.classList.add('was-validated');
    }, false);
  });
})();


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
    if (link.getAttribute('href') == '/documents' || link.getAttribute('href') == '/shareDocuments') {
      document.querySelector('.search-bar').style.display = "block"
    }
    link.classList.add('active')
  } else {
    link.classList.remove('active')
  }
})

// Le modal de modification
const editModal = document.getElementById('editModal');
if (editModal) {
  editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (button.classList.contains('edit-btn')) {
      const currentUrl = location.pathname;

      if (currentUrl == '/documents') {
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
      } else {
        const id = button.getAttribute('data-id')
        const title = button.getAttribute('data-title')
        const description = button.getAttribute('data-description')
        const categoty_id = button.getAttribute('data-category-id')
  
        document.getElementById('doc_id').value = id
        document.getElementById('title').value = title
        document.getElementById('description').value = description
        document.getElementById('category_id').value = categoty_id
      }
      
      document.getElementById('editModalLabel').textContent = "Modifier le document"
    }
  });
}

// Gestion du formulaire d'édition
const editDocumentForm = document.getElementById('editDocumentForm');
if (editDocumentForm) {
    editDocumentForm.addEventListener('submit', function(e) {
    e.preventDefault()
    
    const formData = new FormData(this)

    const currentUrl = location.pathname;

    if (currentUrl == '/documents') {
      fetch('/documents/update', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              const msgBox = document.getElementById('SuccessMsgBox')
              const span = document.querySelector('.successMessage')
              const invalidType = document.getElementById('invalidType')
              span.innerHTML = data.message
              msgBox.style.display = 'block'
              let second
              (invalidType.textContent == '') ? second = 1500 : second = 3500
  
              setInterval(() => {
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide()
                location.reload()
              }, second);
          } else {
              console.log(data.message);
              document.querySelector('.dangerMessage').textContent = data.message || 'Une erreur est survenue.';
              document.getElementById('dangerMsgBox').style.display = 'block';
      
              setTimeout(() => {
                document.getElementById('dangerMsgBox').style.display = 'none';
              }, 5000);
          }
      })
      .catch(error => {
          console.error('Erreur:', error)
          document.querySelector('.dangerMessage').textContent = 'Erreur lors de la modification du document.';
          document.getElementById('dangerMsgBox').style.display = 'block';
  
          setTimeout(() => {
            document.getElementById('dangerMsgBox').style.display = 'none';
          }, 6000);
      })
    } else {
      fetch('/shareDocuments/update', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
          if (data.success) {
              const msgBox = document.getElementById('SuccessMsgBox')
              const span = document.querySelector('.successMessage')
              span.innerHTML = data.message
              msgBox.style.display = 'block'
  
              setInterval(() => {
                bootstrap.Modal.getInstance(document.getElementById('editModal')).hide()
                location.reload()
              }, 1500);
          } else {
              const error = data.message || 'Une erreur est survenue.';
              console.log(error)
          }
      })
      .catch(error => {
          console.error('Erreur:', error)
          document.querySelector('.dangerMessage').textContent = 'Erreur lors de la modification du document.';
          document.getElementById('dangerMsgBox').style.display = 'block';
  
          setTimeout(() => {
            document.getElementById('dangerMsgBox').style.display = 'none';
          }, 6000);
      })
    }
    
  });
}

// Le modal de suppression
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
  deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (button.classList.contains('delete-btn')) {
      const id = button.getAttribute('data-id')
      const title = button.getAttribute('data-title')

      document.getElementById('delete_document_title').innerHTML = title
      const document_id = document.getElementById('delete_document_id') || null
      if (document_id) {
        document_id.value = id
      } else {
        document.getElementById('delete_document_share_id').value = id
      }
      
    }
  });
}

// Gestion de la soumission du formulaire de suppression
const deleteDocumentForm = document.getElementById('deleteDocumentForm');
if (deleteDocumentForm) {
  deleteDocumentForm.addEventListener('submit', function (e) {
    e.preventDefault()

    // if (!confirm('Êtes-vous absolument sûr de vouloir supprimer ce document ?')) {
    //   return;
    // }

    const formData = new FormData(this)

    const currentUrl = location.pathname;

    if (currentUrl == '/documents') {
      fetch('/documents/delete', {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            location.reload();
        } else if (currentUrl == '/shareDocuments') {
            alert('Erreur: ' + (data.message || 'Erreur lors de la suppression'));
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression du document');
      });

    } else {
      fetch('/shareDocuments/delete', {
        method: "POST",
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
            location.reload();
        } else {
            alert('Erreur: ' + (data.message || 'Erreur lors de la suppression'));
        }
      })
      .catch(error => {
        console.error('Erreur:', error);
        alert('Erreur lors de la suppression du document');
      });
    }

  });
}

// Le modal pour voir les details
const viewModal = document.getElementById('viewModal');
if (viewModal) {
  viewModal.addEventListener('show.bs.modal', function(e) {
    const button = e.relatedTarget
    if (button.classList.contains('view-btn')) {
      const id = button.getAttribute('data-id')
      const title = button.getAttribute('data-title')
      const description = button.getAttribute('data-description')
      const category = button.getAttribute('data-category')
      const is_public = button.getAttribute('data-is-public')
      const file_name = button.getAttribute('data-file-name')
      const file_type = button.getAttribute('data-file-type')
      const file_size = button.getAttribute('data-file-size')
      const download_count = button.getAttribute('data-download-count')
      const created_at = button.getAttribute('data-created-at')

      document.getElementById('view_title').textContent = title
      document.getElementById('view_description').textContent = description || 'Aucune description';
      document.getElementById('view_category').textContent = category || 'Non catégorisé'
      document.getElementById('view_filename').textContent = file_name
      document.getElementById('view_type').textContent = file_type
      document.getElementById('view_size').textContent = (file_size / 1024).toFixed(2) + ' KB'
      document.getElementById('view_downloads').textContent = download_count
      document.getElementById('view_date').textContent = created_at

      const currentUrl = location.pathname
      if (currentUrl == '/shareDocuments') {
        const shared_by = button.getAttribute('data-shared-by')
        document.getElementById('view_status').textContent = shared_by
      } else {
        const statusBadge = document.getElementById('view_status')
        statusBadge.textContent = is_public == 1 ? 'Public' : 'Privé'
        statusBadge.className = 'badge ' + (is_public == 1 ? 'bg-success' : 'bg-warning');
      }
      
      // Lien de téléchargement
      document.getElementById('view_download_link').href = `/documents/download/${id}`;
    }
  });
}

// // Telecharger un document partagé
const download_btn = document.querySelectorAll('.download-btn')

if (download_btn) {
  download_btn.forEach(link => {
    link.addEventListener('click', function () {
      const id = link.getAttribute('data-id');
      link.href = `/documents/download/${id}`;
    })
  })
}

// Le modal de partage d'un document
const shareModal = document.getElementById('shareDocumentModal');
if (shareModal) {
  shareModal.addEventListener('show.bs.modal', function (e) {
    const button = e.relatedTarget;
    if (button.classList.contains('share-btn')) {
      const id = button.getAttribute('data-id');
      const title = button.getAttribute('data-title')

      document.getElementById('document_share_id').value = id
      document.getElementById('shareDocumentTitle').value = title
    }
  })
}

// Gestion de partage de document
const shareDocumentForm = document.getElementById('shareDocumentForm');
if (shareDocumentForm) {
  shareDocumentForm.addEventListener('submit', function (e) {
    e.preventDefault()

    const formData = new FormData(this)

    fetch('/documents/shareDocument', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
          const msgBox = document.getElementById('SuccessMsgBoxShare')
          const span = document.querySelector('.successMessageShare')
          span.innerHTML = data.message
          msgBox.style.display = 'block'

          setInterval(() => {
            bootstrap.Modal.getInstance(document.getElementById('shareDocumentModal')).hide()
            location.reload()
          }, 1500);
        }
    })
  })
}

// Gestion du formulaire d’ajout (page upload)
const addForm = document.getElementById('addform');
if (addForm) {
  addForm.addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    fetch('/documents/insert', {
      method: 'POST',
      body: formData
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        document.querySelector('.successMessage').textContent = data.message;
        document.getElementById('SuccessMsgBox').style.display = 'block';
        setTimeout(() => {
          document.getElementById('SuccessMsgBox').style.display = 'none';
          location.reload()
        }, 3000);
      } else {
          document.querySelector('.dangerMessage').textContent = data.message || 'Une erreur est survenue.';
          document.getElementById('dangerMsgBox').style.display = 'block';

          setTimeout(() => {
            document.getElementById('dangerMsgBox').style.display = 'none';
          }, 6000);
        }
    })
    .catch(error => {
      console.error('Erreur:', error);
      document.querySelector('.dangerMessage').textContent = 'Erreur lors de l’envoi du formulaire.';
      document.getElementById('dangerMsgBox').style.display = 'block';

      setTimeout(() => {
        document.getElementById('dangerMsgBox').style.display = 'none';
      }, 4000);
    });
  });
}





