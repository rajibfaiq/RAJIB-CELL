document.addEventListener('DOMContentLoaded', () => {
  // Sidebar navigation
  document.querySelectorAll('.nav-item[data-page]').forEach(item => {
    item.addEventListener('click', () => {
      document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
      item.classList.add('active');
      document.querySelectorAll('.page-section').forEach(s => s.style.display = 'none');
      const page = document.getElementById('page-' + item.dataset.page);
      if (page) page.style.display = 'block';
      document.getElementById('page-title').textContent = item.dataset.title || 'Dashboard';
    });
  });

  // Modal open/close
  document.querySelectorAll('[data-modal]').forEach(trigger => {
    trigger.addEventListener('click', () => {
      const modal = document.getElementById(trigger.dataset.modal);
      if (modal) modal.classList.add('show');
    });
  });
  document.querySelectorAll('.modal-close, .modal-overlay').forEach(el => {
    el.addEventListener('click', (e) => {
      if (e.target === el) {
        const overlay = el.closest('.modal-overlay');
        if (overlay) overlay.classList.remove('show');
      }
    });
  });

  // Login form handling removed to allow standard POST submission
});
