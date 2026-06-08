</main>

<!-- FOOTER -->
<footer class="site-footer mt-auto">
  <div class="container">

    <!-- Footer Top Row -->
    <div class="footer-top d-flex flex-wrap align-items-start justify-content-between gap-3 pb-3 mb-3">

      <!-- Brand -->
      <div class="d-flex align-items-center gap-2">
        <div class="brand-icon" style="width:32px;height:32px;font-size:.85rem;">
          <i class="bi bi-people-fill"></i>
        </div>
        <div>
          <div class="footer-brand">ContactsPro</div>
          <div class="footer-brand-sub">Gestion des Contacts</div>
        </div>
      </div>



      <!-- Live Clock -->
      <div class="footer-clock d-flex align-items-center gap-2">
        <i class="bi bi-clock" style="opacity:.4"></i>
        <span id="liveClock" style="font-variant-numeric:tabular-nums"></span>
      </div>

    </div>

    <!-- Footer Bottom Row -->
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2">

      <div class="footer-copy d-flex align-items-center gap-2 flex-wrap">
        <i class="bi bi-code-slash"></i>
        <span>Projet Universitaire &copy; <?= date('Y') ?></span>
        <span class="footer-heart">♥</span>
      </div>

      <div class="d-flex align-items-center gap-2">
        <span class="tech-badge tech-php">PHP</span>
        <span class="tech-badge tech-mysql">MySQL</span>
        <span class="tech-badge tech-bs">Bootstrap 5</span>
      </div>

      <div class="footer-status d-flex align-items-center gap-2">
        <span class="status-dot" title="Système opérationnel"></span>
        <span>Système actif</span>
      </div>

    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="public/js/contacts.js"></script>
<script src="public/js/search.js"></script>
<script src="public/js/validation.js"></script>

<script>
  // Live clock
  (function tick() {
    document.getElementById('liveClock').textContent =
      new Date().toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    setTimeout(tick, 1000);
  })();
</script>
</body>
</html>