<?php
$pageTitle = '404 — Page introuvable';
if (!defined('ROOT')) define('ROOT', __DIR__ . '/../..');
require_once ROOT . '/views/layout/header.php';
?>

<div class="container py-5 text-center">
  <div class="error-page py-5">
    <div class="error-code">404</div>
    <h2 class="error-title mt-3">Page introuvable</h2>
    <p class="text-muted mb-4">Le contact ou la page que vous cherchez n'existe pas ou a été supprimé.</p>
    <a href="index.php" class="btn btn-primary-custom">
      <i class="bi bi-arrow-left me-2"></i>Retour à l'accueil
    </a>
  </div>
</div>

<?php require_once ROOT . '/views/layout/footer.php'; ?>
