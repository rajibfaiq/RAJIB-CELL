document.addEventListener('DOMContentLoaded', () => {
  // Helper function to switch tabs
  function showPage(pageId) {
    const activeItem = document.querySelector(`.nav-item[data-page="${pageId}"]`);
    if (activeItem) {
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      activeItem.classList.add('active');
      document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
      const page = document.getElementById('page-' + pageId);
      if (page) page.style.display = 'block';
      document.getElementById('page-title').textContent = activeItem.dataset.title || 'Dashboard';
      return true;
    }
    return false;
  }

  // Sidebar navigation
  document.querySelectorAll('.nav-item[data-page]').forEach(item => {
    item.addEventListener('click', () => {
      const pageId = item.dataset.page;
      showPage(pageId);
      
      // Update URL query param without refreshing the page
      const newUrl = new URL(window.location.href);
      newUrl.searchParams.set('page', pageId);
      window.history.pushState({ page: pageId }, '', newUrl.toString());
    });
  });

  // Handle browser back/forward buttons
  window.addEventListener('popstate', (event) => {
    if (event.state && event.state.page) {
      showPage(event.state.page);
    } else {
      const urlParams = new URLSearchParams(window.location.search);
      const activePage = urlParams.get('page');
      if (activePage) {
        showPage(activePage);
      } else {
        showPage('dashboard');
      }
    }
  });

  // Check URL parameter on initial load to restore the correct active menu
  const urlParams = new URLSearchParams(window.location.search);
  const activePage = urlParams.get('page');
  if (activePage) {
    showPage(activePage);
  } else {
    // If no page parameter, show the default dashboard
    showPage('dashboard');
  }

  // Modal open/close (Add Data)
  document.querySelectorAll('[data-modal]').forEach(trigger => {
    trigger.addEventListener('click', () => {
      const modal = document.getElementById(trigger.dataset.modal);
      if (modal) {
        const form = modal.querySelector('form');
        if (form) {
          form.reset();
          const isEditInput = form.querySelector('[name="is_edit"]');
          if (isEditInput) isEditInput.value = '0';
          const title = modal.querySelector('.modal-header h3');
          if (title && title.dataset.original) title.innerText = title.dataset.original;
        }
        modal.classList.add('show');
      }
    });
  });
  document.querySelectorAll('.modal-close, .modal-overlay').forEach(el => {
    el.addEventListener('click', (e) => {
      if (e.target === el || e.target.closest('.modal-close')) {
        const overlay = el.closest('.modal-overlay');
        if (overlay) overlay.classList.remove('show');
      }
    });
  });

  // Reset Pendaftaran Wizard when opening
  const pendaftaranTrigger = document.querySelector('[data-modal="modal-pendaftaran"]');
  if (pendaftaranTrigger) {
    pendaftaranTrigger.addEventListener('click', () => {
      if (typeof prevStep === 'function') {
        prevStep();
      }
    });
  }

  // Login form handling removed to allow standard POST submission
});

// Global Edit Function
window.editData = function(type, data) {
  const modal = document.getElementById('modal-' + type);
  if (!modal) return;
  
  // Change title
  const title = modal.querySelector('.modal-header h3');
  if (title) {
      if (!title.dataset.original) title.dataset.original = title.innerText;
      title.innerText = 'Edit ' + title.dataset.original.replace('Tambah ', '');
  }
  
  // Populate form
  const form = modal.querySelector('form');
  if (form) {
      // Find input names that match the keys (case insensitive)
      for (const key in data) {
          const input = form.querySelector(`[name="${key.toLowerCase()}"]`);
          if (input) {
              input.value = data[key];
          }
      }
      
      // Add hidden flag to let the controller know it's an update
      let isEditInput = form.querySelector('[name="is_edit"]');
      if (!isEditInput) {
          isEditInput = document.createElement('input');
          isEditInput.type = 'hidden';
          isEditInput.name = 'is_edit';
          form.appendChild(isEditInput);
      }
      isEditInput.value = '1';
  }
  
  modal.classList.add('show');
};

// Global helper to navigate from pendaftaran to pembayaran and load the bill
window.goToBillingFromPendaftaran = function(noReg) {
  const activeItem = document.querySelector(`.nav-item[data-page="pembayaran"]`);
  if (activeItem) {
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    activeItem.classList.add('active');
    document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
    const page = document.getElementById('page-pembayaran');
    if (page) page.style.display = 'block';
    document.getElementById('page-title').textContent = 'Pembayaran';
    
    // Update URL query param without refreshing the page
    const newUrl = new URL(window.location.href);
    newUrl.searchParams.set('page', 'pembayaran');
    window.history.pushState({ page: 'pembayaran' }, '', newUrl.toString());
  }

  // Pre-select patient and fetch billing details
  setTimeout(() => {
    const selectEl = document.getElementById('cari-pendaftaran-pembayaran');
    if (selectEl) {
      selectEl.value = noReg;
      if (typeof loadBillingByPendaftaran === 'function') {
        loadBillingByPendaftaran();
      }
    }
  }, 150);
};
