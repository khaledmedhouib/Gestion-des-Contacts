<?php
$pageTitle = 'ContactsPro — Liste des Contacts';
require_once ROOT . '/views/layout/header.php';
?>

<div class="container py-4">

  <!-- PAGE HEADER -->
  <div class="page-header d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
    <div>
      <h1 class="page-title mb-1">
        <i class="bi bi-people me-2 text-primary"></i>Mes Contacts
      </h1>
      <p class="page-sub mb-0">
        <span class="badge-count"><?= $total ?></span>
        contact<?= $total > 1 ? 's' : '' ?> enregistré<?= $total > 1 ? 's' : '' ?>
      </p>
    </div>
    <div class="d-flex align-items-center gap-2 flex-wrap">
      <a href="index.php?action=create" class="btn btn-primary-custom d-flex align-items-center gap-2">
        <i class="bi bi-person-plus-fill"></i>
        Nouveau contact
      </a>
      <a href="index.php?action=pdf" target="_blank" class="btn btn-outline-custom d-flex align-items-center gap-2">
        <i class="bi bi-file-earmark-pdf-fill text-danger"></i>
        Export PDF
      </a>
    </div>
  </div>

  <!-- SEARCH + SORT BAR -->
  <div class="card search-card mb-4">
    <div class="card-body py-3 px-4">
      <div class="row g-2 align-items-center">
        <div class="col-md-7">
          <div class="search-wrapper">
            <i class="bi bi-search search-icon"></i>
            <input type="text"
                   id="searchInput"
                   class="form-control search-input"
                   placeholder="Rechercher par nom, prénom, email…"
                   autocomplete="off" />
            <div id="searchSpinner" class="search-spinner d-none">
              <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            </div>
          </div>
        </div>
        <div class="col-md-3">
          <select id="sortSelect" class="form-select sort-select" onchange="applySort(this.value)">
            <option value="nom_asc"  <?= $sort === 'nom_asc'  ? 'selected' : '' ?>>Nom A → Z</option>
            <option value="nom_desc" <?= $sort === 'nom_desc' ? 'selected' : '' ?>>Nom Z → A</option>
            <option value="recent"   <?= $sort === 'recent'   ? 'selected' : '' ?>>Plus récents</option>
            <option value="ancien"   <?= $sort === 'ancien'   ? 'selected' : '' ?>>Plus anciens</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100 d-flex align-items-center justify-content-center gap-1"
                  onclick="resetSearch()">
            <i class="bi bi-arrow-counterclockwise"></i>
            <span>Reset</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- CONTACTS TABLE -->
  <div id="contactsTableWrapper">
    <?php include __DIR__ . '/_table.php'; ?>
  </div>

  <!-- SEARCH RESULTS (hidden by default) -->
  <div id="searchResults" class="d-none">
    <div class="results-header mb-3">
      <span id="resultsCount" class="badge-count"></span>
      résultat<span id="resultsPlural"></span> trouvé<span id="resultsPluralFr"></span>
    </div>
    <div class="table-card">
      <div class="table-responsive">
        <table class="table contacts-table mb-0">
          <thead>
            <tr>
              <th style="width:70px">Photo</th>
              <th>Nom & Prénom</th>
              <th class="d-none d-md-table-cell">Téléphone</th>
              <th class="d-none d-lg-table-cell">Email</th>
              <th class="text-center">Actions</th>
            </tr>
          </thead>
          <tbody id="searchBody"></tbody>
        </table>
      </div>
      <div id="noResults" class="empty-state d-none py-5">
        <i class="bi bi-search display-4 text-muted mb-3 d-block"></i>
        <h5 class="text-muted">Aucun résultat trouvé</h5>
        <p class="text-muted small">Essayez d'autres mots-clés.</p>
      </div>
    </div>
  </div>

</div>

<!-- DELETE CONFIRMATION MODAL -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content delete-modal">
      <div class="modal-header border-0 pb-0">
        <div class="delete-icon-wrap mx-auto mt-2">
          <i class="bi bi-exclamation-triangle-fill text-danger fs-2"></i>
        </div>
      </div>
      <div class="modal-body text-center px-4 pt-2">
        <h5 class="fw-700 mb-2">Supprimer ce contact ?</h5>
        <p class="text-muted mb-0">
          Vous êtes sur le point de supprimer
          <strong id="deleteContactName" class="text-danger"></strong>.
          <br>Cette action est <strong>irréversible</strong>.
        </p>
      </div>
      <div class="modal-footer border-0 justify-content-center gap-3 pt-0 pb-4">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
          <i class="bi bi-x-circle me-1"></i>Annuler
        </button>
        <button type="button" class="btn btn-danger px-4" id="confirmDeleteBtn">
          <i class="bi bi-trash3-fill me-1"></i>Supprimer
        </button>
      </div>
    </div>
  </div>
</div>

<!-- CSRF token for JS -->
<input type="hidden" id="csrfToken" value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

<?php require_once ROOT . '/views/layout/footer.php'; ?>
