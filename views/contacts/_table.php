<?php
// views/contacts/_table.php  — reusable contacts table partial
?>

<?php if (empty($contacts)): ?>
  <div class="empty-state text-center py-5">
    <div class="empty-illustration mb-3">
      <i class="bi bi-person-x display-2 text-muted"></i>
    </div>
    <h4 class="text-muted">Aucun contact trouvé</h4>
    <p class="text-muted mb-4">Commencez par ajouter votre premier contact.</p>
    <a href="index.php?action=create" class="btn btn-primary-custom">
      <i class="bi bi-person-plus-fill me-2"></i>Ajouter un contact
    </a>
  </div>

<?php else: ?>

  <div class="table-card">
    <div class="table-responsive">
      <table class="table contacts-table mb-0">
        <thead>
          <tr>
            <th style="width:70px">Photo</th>
            <th>Nom & Prénom</th>
            <th class="d-none d-md-table-cell">Téléphone</th>
            <th class="d-none d-lg-table-cell">Email</th>
            <th class="d-none d-xl-table-cell">Créé le</th>
            <th class="text-center">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($contacts as $c): ?>
          <tr class="contact-row">
            <td>
              <?php if ($c['photo'] && file_exists(ROOT . '/public/' . $c['photo'])): ?>
                <img src="public/<?= htmlspecialchars($c['photo'], ENT_QUOTES, 'UTF-8') ?>"
                     alt="<?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?>"
                     class="contact-avatar" />
              <?php else: ?>
                <div class="avatar-placeholder">
                  <span><?= strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1)) ?></span>
                </div>
              <?php endif; ?>
            </td>
            <td>
              <div class="contact-name">
                <?= htmlspecialchars($c['nom'],    ENT_QUOTES, 'UTF-8') ?>
                <?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?>
              </div>
              <div class="contact-email-mobile d-block d-lg-none text-muted small">
                <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
              </div>
            </td>
            <td class="d-none d-md-table-cell">
              <a href="tel:<?= htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8') ?>"
                 class="tel-link">
                <i class="bi bi-telephone me-1"></i>
                <?= htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </td>
            <td class="d-none d-lg-table-cell">
              <a href="mailto:<?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>"
                 class="email-link">
                <i class="bi bi-envelope me-1"></i>
                <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
              </a>
            </td>
            <td class="d-none d-xl-table-cell">
              <span class="date-badge">
                <?= date('d/m/Y', strtotime($c['created_at'])) ?>
              </span>
            </td>
            <td class="text-center">
              <div class="action-btns d-flex justify-content-center gap-2">
                <a href="index.php?action=edit&id=<?= $c['id'] ?>"
                   class="btn btn-sm btn-edit"
                   title="Modifier">
                  <i class="bi bi-pencil-fill"></i>
                </a>
                <button class="btn btn-sm btn-delete"
                        title="Supprimer"
                        onclick="confirmDelete(<?= $c['id'] ?>, '<?= htmlspecialchars($c['prenom'] . ' ' . $c['nom'], ENT_QUOTES, 'UTF-8') ?>')">
                  <i class="bi bi-trash3-fill"></i>
                </button>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- PAGINATION -->
  <?php if ($pages > 1): ?>
  <nav class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
    <div class="pagination-info text-muted small">
      Page <strong><?= $page ?></strong> sur <strong><?= $pages ?></strong>
    </div>
    <ul class="pagination mb-0">
      <?php if ($page > 1): ?>
      <li class="page-item">
        <a class="page-link page-link-custom" href="index.php?sort=<?= $sort ?>&page=<?= $page - 1 ?>">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>
      <?php endif; ?>

      <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link page-link-custom" href="index.php?sort=<?= $sort ?>&page=<?= $i ?>">
          <?= $i ?>
        </a>
      </li>
      <?php endfor; ?>

      <?php if ($page < $pages): ?>
      <li class="page-item">
        <a class="page-link page-link-custom" href="index.php?sort=<?= $sort ?>&page=<?= $page + 1 ?>">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

<?php endif; ?>
