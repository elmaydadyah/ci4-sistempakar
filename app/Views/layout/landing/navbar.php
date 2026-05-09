            <nav class="hero-nav" aria-label="Navigasi utama">
                <a class="brand" href="<?= base_url('/') ?>">
                    <img class="landing-brand-logo" src="<?= base_url('assets/images/logo/logo.png') ?>" alt="Logo StuntCare" width="40" height="40">
                    <span class="brand-text">
                        <strong>StuntCare</strong>
                        <small>Sistem Deteksi Dini Stunting</small>
                    </span>
                </a>

                <ul class="nav-links">
                    <li><a href="<?= base_url('/') ?>">Home</a></li>
                    <li><a href="#about">About</a></li>
                    <li><a href="<?= base_url('/konsultasi') ?>">Konsultasi</a></li>
                    <li><a href="#faq">FAQ</a></li>
                    <li><a href="#contact">Contact</a></li>
                    <li class="mobile-login-link"><a href="<?= base_url('/login') ?>">Login</a></li>
                </ul>

                <div class="nav-actions">
                    <a class="btn-start" href="<?= base_url('/login') ?>">Login</a>
                    <button class="nav-toggle" type="button" aria-label="Buka menu" aria-expanded="false">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                </div>
            </nav>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    var navbar = document.querySelector('.hero-nav');
                    var toggle = document.querySelector('.nav-toggle');

                    if (!navbar || !toggle) {
                        return;
                    }

                    toggle.addEventListener('click', function () {
                        var isOpen = navbar.classList.toggle('is-open');
                        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    });
                });
            </script>
