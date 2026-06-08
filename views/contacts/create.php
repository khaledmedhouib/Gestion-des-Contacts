<?php
$pageTitle = 'ContactsPro — Ajouter un Contact';
require_once ROOT . '/views/layout/header.php';
?>

<div class="container py-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="index.php"><i class="bi bi-house me-1"></i>Accueil</a></li>
      <li class="breadcrumb-item active">Nouveau Contact</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">

      <!-- Form Card -->
      <div class="form-card">
        <div class="form-card-header">
          <div class="form-icon-wrap">
            <i class="bi bi-person-plus-fill"></i>
          </div>
          <div>
            <h2 class="form-card-title">Nouveau Contact</h2>
            <p class="form-card-sub">Remplissez les informations ci-dessous</p>
          </div>
        </div>

        <div class="form-card-body">

          <!-- Error summary -->
          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div>
              <strong>Veuillez corriger les erreurs suivantes :</strong>
              <ul class="mb-0 mt-1">
                <?php foreach ($errors as $e): ?>
                  <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>

          <form method="POST" action="index.php?action=create"
                enctype="multipart/form-data" id="contactForm" novalidate>

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Photo Upload -->
            <div class="photo-upload-section mb-4">
              <div class="photo-preview-wrap" id="photoPreviewWrap">
                <div class="photo-placeholder" id="photoPlaceholder">
                  <i class="bi bi-camera fs-2"></i>
                  <span class="mt-1 small">Ajouter une photo</span>
                </div>
                <img id="photoPreview" src="#" alt="Aperçu" class="photo-preview-img d-none" />
                <label for="photo" class="photo-upload-overlay">
                  <i class="bi bi-camera-fill"></i>
                </label>
              </div>
              <input type="file" name="photo" id="photo" class="d-none"
                     accept="image/jpeg,image/png,image/webp" />
              <div class="text-center mt-2">
                <small class="text-muted">JPG, PNG, WEBP — max 2 MB</small>
              </div>
            </div>

            <!-- Name row -->
            <div class="row g-3">
              <div class="col-sm-6">
                <label for="nom" class="form-label-custom">
                  Nom <span class="required-star">*</span>
                </label>
                <input type="text" name="nom" id="nom"
                       class="form-control form-control-custom <?= isset($errors['nom']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($old['nom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="ex. Dupont"
                       required maxlength="50" />
                <?php if (isset($errors['nom'])): ?>
                  <div class="invalid-feedback"><?= htmlspecialchars($errors['nom'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>

              <div class="col-sm-6">
                <label for="prenom" class="form-label-custom">
                  Prénom <span class="required-star">*</span>
                </label>
                <input type="text" name="prenom" id="prenom"
                       class="form-control form-control-custom <?= isset($errors['prenom']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($old['prenom'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="ex. Jean"
                       required maxlength="50" />
                <?php if (isset($errors['prenom'])): ?>
                  <div class="invalid-feedback"><?= htmlspecialchars($errors['prenom'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Phone -->
            <div class="mt-3">
              <label for="telephone" class="form-label-custom">
                Téléphone <span class="required-star">*</span>
              </label>
              <div class="input-group-custom">
                <span class="input-group-icon"><i class="bi bi-telephone"></i></span>
                <input type="tel" name="telephone" id="telephone"
                       class="form-control form-control-custom with-icon <?= isset($errors['telephone']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($old['telephone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="+33 6 12 34 56 78"
                       required />
                <?php if (isset($errors['telephone'])): ?>
                  <div class="invalid-feedback"><?= htmlspecialchars($errors['telephone'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Email -->
            <div class="mt-3">
              <label for="email" class="form-label-custom">
                Email <span class="required-star">*</span>
              </label>
              <div class="input-group-custom">
                <span class="input-group-icon"><i class="bi bi-envelope"></i></span>
                <input type="email" name="email" id="email"
                       class="form-control form-control-custom with-icon <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                       value="<?= htmlspecialchars($old['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       placeholder="jean.dupont@email.com"
                       required maxlength="100" />
                <?php if (isset($errors['email'])): ?>
                  <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-3 mt-4 pt-2">
              <a href="index.php" class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-arrow-left me-1"></i>Annuler
              </a>
              <button type="submit" class="btn btn-primary-custom flex-fill" id="submitBtn">
                <i class="bi bi-person-check-fill me-2"></i>Enregistrer
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once ROOT . '/views/layout/footer.php'; ?>
