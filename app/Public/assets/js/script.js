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

// Activer un lien du sidebar et afficher ou non la barre de recherche
const links = document.querySelectorAll('.sidebar-item')
const currentUrl = location.pathname;

links.forEach(link => {
  if (link.getAttribute('href') === currentUrl) {
    if (
          link.getAttribute('href') == '/documents' ||
          link.getAttribute('href') == '/shareDocuments' ||
          link.getAttribute('href') == '/publicDocuments' ||
          link.getAttribute('href') == '/Categories'
      ) {
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

      if (currentUrl == '/documents' || currentUrl == '/publicDocuments' || currentUrl == '/shareDocuments' ) {
        const id = button.getAttribute('data-id')
        const title = button.getAttribute('data-title')
        const description = button.getAttribute('data-description')
        const categoty_id = button.getAttribute('data-category-id')
  
        document.getElementById('doc_id').value = id
        document.getElementById('title').value = title
        document.getElementById('description').value = description
        document.getElementById('category_id').value = categoty_id

        if (currentUrl == '/documents') {
          const is_public = button.getAttribute('data-is-public')
          const selectedElement = document.getElementById('is_public')
    
          selectedElement.value = is_public
        } 
      } else if (currentUrl == '/Categories') {
        const id = button.getAttribute('data-id')
        const category_name = button.getAttribute('data-name')
        const description = button.getAttribute('data-description')

        document.getElementById('category_id').value = id
        document.getElementById('category_name').value = category_name
        document.getElementById('description').value = description
      }
      
      document.getElementById('editModalLabel').textContent = "Modifier le document"
    }
  });
}

// Gestion de la soumission du formulaire d'edition
const editDocumentForm = document.getElementById('editDocumentForm');
if (editDocumentForm) {
  editDocumentForm.addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);
    const currentUrl = location.pathname;
    let endpoint = ''
    if (currentUrl == '/Categories') {
      endpoint = '/Categories/update'
    } else {
      const isDocumentsPage = currentUrl === '/documents';
      endpoint = isDocumentsPage ? '/documents/update' : '/shareDocuments/update';
    }

    const submitBtn = document.querySelector('#editDocumentForm button[type="submit"]');
    const loader = document.getElementById('loader');

    submitBtn.disabled = true;
    if (loader) loader.style.display = 'inline-block';

    fetch(endpoint, {
      method: 'POST',
      body: formData
    })
      .then(async response => {
        if (!response.ok) {
          const text = await response.text();
          throw new Error(`Erreur HTTP ${response.status} : ${text}`);
        }

        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
          const text = await response.text();
          throw new Error(`Réponse non JSON : ${text}`);
        }

        return response.json();
      })
      .then(data => {
        if (data.success) {
          const msgBox = document.getElementById('SuccessMsgBox');
          const span = document.querySelector('.successMessage');
          const invalidType = document.getElementById('invalidType');

          span.innerHTML = data.message;
          msgBox.style.display = 'block';

          const delay = invalidType?.textContent === '' ? 1500 : 2500;

          setTimeout(() => {
            bootstrap.Modal.getInstance(document.getElementById('editModal')).hide();
            location.reload();
          }, delay);
        } else {
          const error = data.message || 'Une erreur est survenue.';
          document.querySelector('.dangerMessage').textContent = error;
          document.getElementById('dangerMsgBox').style.display = 'block';

          setTimeout(() => {
            document.getElementById('dangerMsgBox').style.display = 'none';
          }, 6000);
        }
      })
      .catch(error => {
        console.error('Erreur:', error.message);
        // document.querySelector('.dangerMessage').textContent = error.message
        document.querySelector('.dangerMessage').textContent = "Une erreur est survenue lors de la modification"
        document.getElementById('dangerMsgBox').style.display = 'block';

        setTimeout(() => {
          document.getElementById('dangerMsgBox').style.display = 'none';
        }, 6000);
      })
      .finally(() => {
        submitBtn.disabled = false;
        if (loader) loader.style.display = 'none';
      });
  });
}

// Le modal de suppression
const deleteModal = document.getElementById('deleteModal');
if (deleteModal) {
  deleteModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    if (button.classList.contains('delete-btn')) {
      const id = button.getAttribute('data-id')
      const title = button.getAttribute('data-title') || null
      const name = button.getAttribute('data-name') || null

      title ? document.getElementById('delete_document_title').innerHTML = title || null
            : document.getElementById('delete_category_title').innerHTML = name || null

      const document_id = document.getElementById('delete_document_id') || null
      const category_id = document.getElementById('delete_category_id') || null

      if (document_id) {
        document_id.value = id
      } else if (category_id) {
        category_id.value = id
      } else {
        document.getElementById('delete_document_share_id').value = id
      }
      
    }
  });
}

// Gestion de la soumission du formulaire de suppression
const deleteForm = document.getElementById('deleteForm');
if (deleteForm) {
  deleteForm.addEventListener('submit', function (e) {
    e.preventDefault()

    // if (!confirm('Êtes-vous absolument sûr de vouloir supprimer ce document ?')) {
    //   return;
    // }

    const formData = new FormData(this)

    const currentUrl = location.pathname;
    const endpoint = currentUrl + "/delete"    

    if (currentUrl == '/documents'  || currentUrl == '/publicDocuments' || currentUrl == '/shareDocuments' || currentUrl == '/Categories') {
      fetch(endpoint, {
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
      if (currentUrl == '/shareDocuments' || currentUrl == '/publicDocuments') {
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

// Telecharger un document partagé
const download_btn = document.querySelectorAll('.download-btn')
if (download_btn) {
  download_btn.forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault()

      const id = btn.getAttribute('data-id');
      
      fetch(`/documents/canDownload/${id}`)
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          window.location.href = `/documents/download/${id}`;
        } else {
          console.log(data.message);

          const msgBox = document.getElementById('dangerMsgShare');
          const msgText = document.querySelector('.dangerMessageShare');
          const closeBtn = document.querySelector('.custom-close');

          closeBtn.addEventListener('click', () => {
            msgBox.classList.add('d-none');
          });

          msgText.textContent = data.message || "Téléchargement non autorisé.";

          msgBox.classList.remove('d-none');
          msgBox.classList.add('show');

          if (msgBox.hideTimeout) {
            clearTimeout(msgBox.hideTimeout);
          }

          msgBox.hideTimeout = setTimeout(() => {
            msgBox.classList.remove('show');
            msgBox.classList.add('d-none');
          }, 5000);

        }
      })
      .catch(error => {
        console.error('Erreur :', error);
      })
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
        } else {
          console.log(data.message);
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
    fetch('/upload/insert', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
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
      document.querySelector('.dangerMessage').textContent = 'Erreur lors de la soumission du formulaire.';
      document.getElementById('dangerMsgBox').style.display = 'block';

      setTimeout(() => {
        document.getElementById('dangerMsgBox').style.display = 'none';
      }, 4000);
    });
  });
}

// Gestion du modal d'ajout d'une categorie
const addCategory = document.getElementById('addCategoryForm')
if (addCategory) {
  addCategory.addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this)

    fetch('/Categories/insert', {
      method: 'POST',
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        document.querySelector('.successMsg').textContent = data.message;
        document.getElementById('SuccessCatMsg').style.display = 'block';
        setTimeout(() => {
          document.getElementById('SuccessCatMsg').style.display = 'none';
          location.reload()
        }, 3000);
      } else {
        if (data.message == 'Échec, le nom de la catégorie existe déjà.') {
          document.querySelector('.dangerMsg').textContent = data.message || 'Une erreur est survenue.';
          document.getElementById('dangerCatMsg').style.display = 'block';

        }

        setTimeout(() => {
          document.getElementById('dangerCatMsg').style.display = 'none';
        }, 6000);
      }
    })
  })
}





