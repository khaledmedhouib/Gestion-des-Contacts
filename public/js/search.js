// public/js/search.js

'use strict';

(function () {
  const searchInput   = document.getElementById('searchInput');
  if (!searchInput) return;

  const tableWrapper  = document.getElementById('contactsTableWrapper');
  const resultsPanel  = document.getElementById('searchResults');
  const searchBody    = document.getElementById('searchBody');
  const resultsCount  = document.getElementById('resultsCount');
  const resultsPlural = document.getElementById('resultsPlural');
  const resultsFr     = document.getElementById('resultsPluralFr');
  const noResults     = document.getElementById('noResults');
  const spinner       = document.getElementById('searchSpinner');

  let debounceTimer = null;

  searchInput.addEventListener('input', () => {
    const q = searchInput.value.trim();
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => doSearch(q), 280);
  });

  async function doSearch(q) {
    if (q === '') {
      showTable();
      return;
    }

    if (spinner) spinner.classList.remove('d-none');

    try {
      const res  = await fetch(`index.php?action=search&q=${encodeURIComponent(q)}`);
      const data = await res.json();
      renderResults(data.results, data.count, q);
    } catch (err) {
      console.error('Search error:', err);
    } finally {
      if (spinner) spinner.classList.add('d-none');
    }
  }

  function renderResults(contacts, count, q) {
    hideTable();

    if (resultsCount) resultsCount.textContent = count;
    if (resultsPlural) resultsPlural.textContent = count > 1 ? 's' : '';
    if (resultsFr)     resultsFr.textContent     = count > 1 ? 's' : '';

    if (count === 0) {
      searchBody.innerHTML = '';
      noResults?.classList.remove('d-none');
    } else {
      noResults?.classList.add('d-none');
      searchBody.innerHTML = contacts.map(c => buildRow(c, q)).join('');
    }
  }

  function buildRow(c, q) {
    const photoHtml = c.photo
      ? `<img src="public/${escHtml(c.photo)}" alt="" class="contact-avatar" />`
      : `<div class="avatar-placeholder"><span>${initials(c.prenom, c.nom)}</span></div>`;

    return `
      <tr class="contact-row">
        <td>${photoHtml}</td>
        <td>
          <div class="contact-name">
            ${highlight(c.nom, q)} ${highlight(c.prenom, q)}
          </div>
          <div class="d-block d-lg-none text-muted small">${escHtml(c.email)}</div>
        </td>
        <td class="d-none d-md-table-cell">
          <a href="tel:${escHtml(c.telephone)}" class="tel-link">
            <i class="bi bi-telephone me-1"></i>${escHtml(c.telephone)}
          </a>
        </td>
        <td class="d-none d-lg-table-cell">
          <a href="mailto:${escHtml(c.email)}" class="email-link">
            <i class="bi bi-envelope me-1"></i>${highlight(c.email, q)}
          </a>
        </td>
        <td class="text-center">
          <div class="action-btns d-flex justify-content-center gap-2">
            <a href="index.php?action=edit&id=${c.id}" class="btn btn-sm btn-edit" title="Modifier">
              <i class="bi bi-pencil-fill"></i>
            </a>
            <button class="btn btn-sm btn-delete" title="Supprimer"
              onclick="confirmDelete(${c.id}, '${escAttr(c.prenom + ' ' + c.nom)}')">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </div>
        </td>
      </tr>`;
  }

  function highlight(text, q) {
    if (!q) return escHtml(text);
    const regex = new RegExp(`(${escRegex(q)})`, 'gi');
    return escHtml(text).replace(regex, '<mark class="search-highlight">$1</mark>');
  }

  function initials(prenom, nom) {
    return ((prenom[0] ?? '') + (nom[0] ?? '')).toUpperCase();
  }

  function escHtml(str) {
    return String(str)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function escAttr(str) {
    return String(str).replace(/'/g, "\\'").replace(/"/g, '\\"');
  }

  function escRegex(str) {
    return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  }

  function hideTable() {
    tableWrapper?.classList.add('d-none');
    resultsPanel?.classList.remove('d-none');
  }

  function showTable() {
    tableWrapper?.classList.remove('d-none');
    resultsPanel?.classList.add('d-none');
    if (searchBody) searchBody.innerHTML = '';
  }

  // Expose for navbar reset button
  window.resetSearch = function () {
    searchInput.value = '';
    showTable();
  };
})();
