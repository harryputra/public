/* SmartLib – Main JS */

document.addEventListener('DOMContentLoaded', function () {

    // ── Flash message auto-dismiss ──────────────────────────────────────
    document.querySelectorAll('.alert-dismissible.auto-dismiss').forEach(function (el) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        }, 5000);
    });

    // ── Mobile sidebar toggle ───────────────────────────────────────────
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
        document.addEventListener('click', function (e) {
            if (sidebar.classList.contains('show') &&
                !sidebar.contains(e.target) &&
                e.target !== sidebarToggle) {
                sidebar.classList.remove('show');
            }
        });
    }

    // ── Search input debounce ───────────────────────────────────────────
    var searchInputs = document.querySelectorAll('input[name="q"][data-auto-submit]');
    searchInputs.forEach(function (input) {
        var timer;
        input.addEventListener('input', function () {
            clearTimeout(timer);
            timer = setTimeout(function () {
                input.closest('form').submit();
            }, 500);
        });
    });

    // ── Confirm Actions with SweetAlert2 ───────────────────────────────
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            e.preventDefault();
            const message = el.dataset.confirm || 'Apakah Anda yakin ingin melakukan tindakan ini?';
            const type = el.dataset.confirmType || 'warning';
            const url = el.getAttribute('href');

            Swal.fire({
                title: 'Konfirmasi Tindakan',
                text: message,
                icon: type,
                showCancelButton: true,
                confirmButtonColor: '#2563eb',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    if (url && url !== '#') {
                        window.location.href = url;
                    } else if (el.tagName === 'BUTTON' && el.type === 'submit') {
                        el.closest('form').submit();
                    }
                }
            });
        });
    });


    // ── Password show/hide toggle ───────────────────────────────────────
    document.querySelectorAll('[data-toggle-password]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var targetId = btn.dataset.togglePassword;
            var input = document.getElementById(targetId);
            if (!input) return;
            var isText = input.type === 'text';
            input.type = isText ? 'password' : 'text';
            var icon = btn.querySelector('i');
            if (icon) {
                icon.classList.toggle('bi-eye', isText);
                icon.classList.toggle('bi-eye-slash', !isText);
            }
        });
    });

    // ── Active nav link highlight ───────────────────────────────────────
    var currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-nav .nav-link').forEach(function (link) {
        if (link.getAttribute('href') && currentPath.endsWith(link.getAttribute('href').split('?')[0])) {
            link.classList.add('active');
        }
    });

});
