<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($pageTitle ?? 'Gestion des Contacts', ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=Space+Mono:wght@400;700&display=swap" rel="stylesheet" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet" />
  <link href="public/css/style.css" rel="stylesheet" />
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark sticky-top" id="mainNav">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="index.php">
      <div class="brand-icon">
        <i class="bi bi-people-fill"></i>
      </div>
      <div>
        <span class="brand-title">ContactsPro</span>
        <span class="brand-sub d-none d-sm-block">Gestion Personnelle</span>
      </div>
    </a>

    <button class="navbar-toggler border-0" type="button"
            data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item">
          <a class="nav-link <?= (!isset($_GET['action']) || $_GET['action'] === 'index') ? 'active' : '' ?>"
             href="index.php">
            <i class="bi bi-house me-1"></i>Accueil
          </a>
        </li>
        <li class="nav-item">
          <a class="nav-link <?= (($_GET['action'] ?? '') === 'create') ? 'active' : '' ?>"
             href="index.php?action=create">
            <i class="bi bi-person-plus me-1"></i>Ajouter
          </a>
        </li>
        <li class="nav-item">
          <div class="nav-divider d-none d-lg-block"></div>
        </li>
        <li class="nav-item ms-1">
          <a class="btn btn-export d-flex align-items-center gap-2"
             href="index.php?action=pdf" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i>
            <span>Export PDF</span>
          </a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- FLASH MESSAGES -->
<?php if (isset($_GET['success'])): ?>
<div class="container mt-3">
  <div class="flash-bar alert-dismissible fade show" role="alert">
    <i class="bi bi-check-circle-fill"></i>
    <div>
      <?php if ($_GET['success'] === 'created'): ?>
        Contact ajouté avec succès !
      <?php elseif ($_GET['success'] === 'updated'): ?>
        Contact mis à jour avec succès !
      <?php elseif ($_GET['success'] === 'deleted'): ?>
        Contact supprimé avec succès.
      <?php endif; ?>
    </div>
    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Fermer"></button>
  </div>
</div>
<?php endif; ?>

<!-- MAIN CONTENT -->
<main class="main-content">