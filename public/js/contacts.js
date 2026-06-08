// public/js/contacts.js

'use strict';

// ── DELETE MODAL ───────────────────────────────────────────────

let deleteTargetId = null;
let deleteModal    = null;

document.addEventListener('DOMContentLoaded', () => {
  const modalEl = document.getElementById('deleteModal');
  if (modalEl) {
    deleteModal = new bootstrap.Modal(modalEl);
  }

  const confirmBtn = document.getElementById('confirmDeleteBtn');
  if (confirmBtn) {
    confirmBtn.addEventListener('click', () => {
      if (deleteTargetId) {
        performDelete(deleteTargetId);
      }
    });
  }
});

function confirmDelete(id, name) {
  deleteTargetId = id;

  const nameEl = document.getElementById('deleteContactName');
  if (nameEl) nameEl.textContent = name;

  if (deleteModal) deleteModal.show();
}

async function performDelete(id) {
  const btn   = document.getElementById('confirmDeleteBtn');
  const token = document.getElementById('csrfToken')?.value ?? '';

  btn.disabled    = true;
  btn.innerHTML   = '<span class="spinner-border spinner-border-sm me-2"></span>Suppression…';

  try {
    const res  = await fetch('index.php?action=delete', {
      method:  'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body:    `id=${id}&csrf_token=${encodeURIComponent(token)}`,
    });

    const data = await res.json();

    if (data.success) {
      deleteModal.hide();
      showToast('Contact supprimé avec succès.', 'success');

      // Animate row out then reload
      const rows = document.querySelectorAll('.contact-row');
      rows.forEach(r => {
        const btn = r.querySelector(`[onclick*="confirmDelete(${id},"]`);
        if (btn) {
          r.style.transition = 'opacity .3s, transform .3s';
          r.style.opacity    = '0';
          r.style.transform  = 'translateX(-20px)';
          setTimeout(() => location.reload(), 400);
        }
      });
      // Fallback if row not found
      setTimeout(() => location.reload(), 600);
    } else {
      showToast(data.message ?? 'Une erreur est survenue.', 'danger');
      deleteModal.hide();
    }
  } catch (err) {
    showToast('Erreur réseau. Réessayez.', 'danger');
    deleteModal.hide();
  } finally {
    btn.disabled  = false;
    btn.innerHTML = '<i class="bi bi-trash3-fill me-1"></i>Supprimer';
  }
}

// ── SORT ───────────────────────────────────────────────────────

function applySort(value) {
  const url = new URL(window.location.href);
  url.searchParams.set('sort', value);
  url.searchParams.set('page', '1');
  window.location.href = url.toString();
}

// ── TOAST NOTIFICATION ─────────────────────────────────────────

function showToast(message, type = 'success') {
  const colors = {
    success: { bg: '#10B981', icon: 'bi-check-circle-fill' },
    danger:  { bg: '#EF4444', icon: 'bi-x-circle-fill' },
    warning: { bg: '#F59E0B', icon: 'bi-exclamation-triangle-fill' },
  };
  const c = colors[type] ?? colors.success;

  const toast = document.createElement('div');
  toast.style.cssText = `
    position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
    background:${c.bg}; color:#fff; padding:.85rem 1.25rem;
    border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,.18);
    display:flex; align-items:center; gap:.6rem; font-size:.875rem;
    font-weight:500; max-width:340px; animation:slideInToast .3s ease;
  `;
  toast.innerHTML = `<i class="bi ${c.icon}"></i>${message}`;

  // CSS animation
  const style = document.createElement('style');
  style.textContent = `
    @keyframes slideInToast {
      from { opacity:0; transform:translateX(30px); }
      to   { opacity:1; transform:translateX(0); }
    }
  `;
  document.head.appendChild(style);
  document.body.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'opacity .3s, transform .3s';
    toast.style.opacity    = '0';
    toast.style.transform  = 'translateX(30px)';
    setTimeout(() => toast.remove(), 300);
  }, 3200);
}

// ── PHOTO PREVIEW ──────────────────────────────────────────────

document.addEventListener('DOMContentLoaded', () => {
  const photoInput   = document.getElementById('photo');
  const photoPreview = document.getElementById('photoPreview');
  const placeholder  = document.getElementById('photoPlaceholder');

  if (!photoInput) return;

  photoInput.addEventListener('change', (e) => {
    const file = e.target.files[0];
    if (!file) return;

    // Client-side size check (2MB)
    if (file.size > 2 * 1024 * 1024) {
      showToast('Photo trop lourde (max 2 MB).', 'danger');
      photoInput.value = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = (ev) => {
      if (photoPreview) {
        photoPreview.src = ev.target.result;
        photoPreview.classList.remove('d-none');
      }
      if (placeholder) placeholder.classList.add('d-none');
    };
    reader.readAsDataURL(file);
  });
});
