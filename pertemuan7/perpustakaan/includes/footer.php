<?php defined('SMARTLIB') or die('Direct access not permitted'); ?>
</div><!-- /container-fluid -->
</main><!-- /main-content -->

<!-- ═══ FOOTER ════════════════════════════════════════════════════════════════ -->
<footer class="footer-bar text-center py-3 mt-auto">
  <small class="text-muted">
    &copy; <?= date('Y') ?> <strong><?= APP_NAME ?></strong> — Sistem Manajemen Perpustakaan Kampus
    &nbsp;|&nbsp; v<?= APP_VERSION ?>
    &nbsp;|&nbsp; <span class="text-muted">SQA POLMAN Bandung</span>
  </small>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js (untuk dashboard) -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<!-- Custom JS -->
<script src="<?= ASSETS_URL ?>js/main.js"></script>
</body>
</html>
