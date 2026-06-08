<?php
// views/contacts/_table.php
?>

<?php if (empty($contacts)): ?>
<div class="empty-state text-center py-5">
  <i class="bi bi-person-x display-2 text-muted d-block mb-3" style="opacity:.25"></i>
  <h5 class="text-muted fw-semibold mb-1">Aucun contact trouvé</h5>
  <p class="text-muted small mb-4">Commencez par ajouter votre premier contact.</p>
  <a href="index.php?action=create" class="btn btn-primary-custom">
    <i class="bi bi-person-plus-fill me-2"></i>Ajouter un contact
  </a>
</div>

<?php else: ?>

<div class="table-shell">
  <div class="table-responsive">
    <table class="contacts-table">
      <thead>
        <tr>
          <th style="width:60px">Photo</th>
          <th>
            <a href="index.php?sort=nom&page=1" class="sort-header">
              Nom &amp; Prénom
              <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
            </a>
          </th>
          <th class="d-none d-md-table-cell">Téléphone</th>
          <th class="d-none d-lg-table-cell">Email</th>
          <th class="d-none d-xl-table-cell">
            <a href="index.php?sort=created_at&page=1" class="sort-header">
              Créé le <i class="bi bi-arrow-down-up ms-1 sort-icon"></i>
            </a>
          </th>
          <th class="text-center">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $c): ?>
        <?php
          $initials = strtoupper(substr($c['prenom'], 0, 1) . substr($c['nom'], 0, 1));
          $colors   = ['blue','green','purple','amber','rose','teal'];
          $color    = $colors[crc32($c['id']) % count($colors)];
        ?>
        <tr class="contact-row">

          <td>
            <?php if ($c['photo'] && file_exists(ROOT . '/public/' . $c['photo'])): ?>
              <img src="public/<?= htmlspecialchars($c['photo'], ENT_QUOTES, 'UTF-8') ?>"
                   alt="<?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?>"
                   class="contact-avatar" />
            <?php else: ?>
              <div class="avatar-placeholder avatar-<?= $color ?>">
                <?= $initials ?>
              </div>
            <?php endif; ?>
          </td>

          <td>
            <div class="contact-name">
              <?= htmlspecialchars($c['nom'],    ENT_QUOTES, 'UTF-8') ?>
              <?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?>
            </div>
            <div class="contact-sub d-block d-lg-none">
              <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
            </div>
          </td>

          <td class="d-none d-md-table-cell">
            <a href="tel:<?= htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8') ?>"
               class="tel-link">
              <i class="bi bi-telephone"></i>
              <?= htmlspecialchars($c['telephone'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>

          <td class="d-none d-lg-table-cell">
            <a href="mailto:<?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>"
               class="email-link">
              <i class="bi bi-envelope"></i>
              <?= htmlspecialchars($c['email'], ENT_QUOTES, 'UTF-8') ?>
            </a>
          </td>

          <td class="d-none d-xl-table-cell">
            <span class="date-badge">
              <i class="bi bi-calendar3 me-1"></i>
              <?= date('d/m/Y', strtotime($c['created_at'])) ?>
            </span>
          </td>

          <td class="text-center">
            <div class="row-actions d-flex justify-content-center gap-2">
              <a href="index.php?action=edit&id=<?= $c['id'] ?>"
                 class="btn-act btn-edit"
                 title="Modifier <?= htmlspecialchars($c['prenom'], ENT_QUOTES, 'UTF-8') ?>">
                <i class="bi bi-pencil-fill"></i>
              </a>
              <button class="btn-act btn-delete"
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

  <!-- TABLE FOOTER WITH PAGINATION -->
  <div class="table-footer d-flex align-items-center justify-content-between flex-wrap gap-2">
    <?php $perPage = $perPage ?? 8; ?>
    <div class="pagination-info small text-muted">
      Affichage de
      <strong><?= (($page - 1) * $perPage) + 1 ?>–<?= min($page * $perPage, $total) ?></strong>
      sur <strong><?= $total ?></strong> contacts
    </div>

    <?php if ($pages > 1): ?>
    <ul class="pagination mb-0">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link page-link-custom"
           href="index.php?sort=<?= $sort ?>&page=<?= $page - 1 ?>">
          <i class="bi bi-chevron-left"></i>
        </a>
      </li>

      <?php for ($i = max(1, $page - 2); $i <= min($pages, $page + 2); $i++): ?>
      <li class="page-item <?= $i === $page ? 'active' : '' ?>">
        <a class="page-link page-link-custom"
           href="index.php?sort=<?= $sort ?>&page=<?= $i ?>">
          <?= $i ?>
        </a>
      </li>
      <?php endfor; ?>

      <?php if ($page + 2 < $pages): ?>
      <li class="page-item disabled">
        <span class="page-link page-link-custom" style="border:none">…</span>
      </li>
      <li class="page-item">
        <a class="page-link page-link-custom"
           href="index.php?sort=<?= $sort ?>&page=<?= $pages ?>">
          <?= $pages ?>
        </a>
      </li>
      <?php endif; ?>

      <li class="page-item <?= $page >= $pages ? 'disabled' : '' ?>">
        <a class="page-link page-link-custom"
           href="index.php?sort=<?= $sort ?>&page=<?= $page + 1 ?>">
          <i class="bi bi-chevron-right"></i>
        </a>
      </li>
    </ul>
    <?php endif; ?>
  </div>
</div>

<?php endif; ?>