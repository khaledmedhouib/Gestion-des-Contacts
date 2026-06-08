<?php
$pageTitle = 'ContactsPro — Modifier le Contact';
require_once ROOT . '/views/layout/header.php';
?>

<div class="container py-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb custom-breadcrumb">
      <li class="breadcrumb-item"><a href="index.php"><i class="bi bi-house me-1"></i>Accueil</a></li>
      <li class="breadcrumb-item active">Modifier le Contact</li>
    </ol>
  </nav>

  <div class="row justify-content-center">
    <div class="col-lg-7 col-xl-6">

      <div class="form-card">
        <div class="form-card-header">
          <div class="form-icon-wrap edit-icon">
            <i class="bi bi-pencil-square"></i>
          </div>
          <div>
            <h2 class="form-card-title">
              Modifier :
              <?= htmlspecialchars($contact['prenom'] . ' ' . $contact['nom'], ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <p class="form-card-sub">Modifiez les informations du contact</p>
          </div>
        </div>

        <div class="form-card-body">

          <?php if (!empty($errors)): ?>
          <div class="alert alert-danger d-flex gap-2 align-items-start" role="alert">
            <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
            <div>
              <strong>Veuillez corriger les erreurs :</strong>
              <ul class="mb-0 mt-1">
                <?php foreach ($errors as $e): ?>
                  <li><?= htmlspecialchars($e, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
          <?php endif; ?>

          <form method="POST"
                action="index.php?action=edit&id=<?= $contact['id'] ?>"
                enctype="multipart/form-data"
                id="contactForm"
                novalidate>

            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <!-- Photo -->
            <div class="photo-upload-section mb-4">
              <div class="photo-preview-wrap" id="photoPreviewWrap">
                <?php if ($contact['photo'] && file_exists(ROOT . '/public/' . $contact['photo'])): ?>
                  <img id="photoPreview"
                       src="public/<?= htmlspecialchars($contact['photo'], ENT_QUOTES, 'UTF-8') ?>"
                       alt="Photo"
                       class="photo-preview-img" />
                  <div class="photo-placeholder d-none" id="photoPlaceholder">
                    <i class="bi bi-camera fs-2"></i>
                  </div>
                <?php else: ?>
                  <div class="photo-placeholder" id="photoPlaceholder">
                    <div class="avatar-initials-large">
                      <?= strtoupper(substr($contact['prenom'], 0, 1) . substr($contact['nom'], 0, 1)) ?>
                    </div>
                  </div>
                  <img id="photoPreview" src="#" alt="Aperçu" class="photo-preview-img d-none" />
                <?php endif; ?>
                <label for="photo" class="photo-upload-overlay">
                  <i class="bi bi-camera-fill"></i>
                </label>
              </div>
              <input type="file" name="photo" id="photo" class="d-none"
                     accept="image/jpeg,image/png,image/webp" />
              <div class="text-center mt-2">
                <small class="text-muted">Cliquez pour changer la photo (optionnel)</small>
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
                       value="<?= htmlspecialchars($contact['nom'], ENT_QUOTES, 'UTF-8') ?>"
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
                       value="<?= htmlspecialchars($contact['prenom'], ENT_QUOTES, 'UTF-8') ?>"
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
                       value="<?= htmlspecialchars($contact['telephone'], ENT_QUOTES, 'UTF-8') ?>"
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
                       value="<?= htmlspecialchars($contact['email'], ENT_QUOTES, 'UTF-8') ?>"
                       required maxlength="100" />
                <?php if (isset($errors['email'])): ?>
                  <div class="invalid-feedback"><?= htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Info box -->
            <div class="info-box mt-4">
              <i class="bi bi-info-circle me-2 text-primary"></i>
              <small>
                Contact créé le
                <strong><?= date('d/m/Y à H:i', strtotime($contact['created_at'])) ?></strong>
              </small>
            </div>

            <!-- Buttons -->
            <div class="d-flex gap-3 mt-4 pt-2">
              <a href="index.php" class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-arrow-left me-1"></i>Annuler
              </a>
              <button type="submit" class="btn btn-primary-custom flex-fill">
                <i class="bi bi-save2-fill me-2"></i>Enregistrer
              </button>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<?php require_once ROOT . '/views/layout/footer.php'; ?>
