        <header class="site-header">
            <nav class="hero-nav" aria-label="Navigasi utama">
                <a class="brand" href="<?= base_url('/') ?>" aria-label="StuntCare">
                    <img class="landing-brand-logo" src="<?= base_url('assets/images/logo/logo.png') ?>" alt="Logo StuntCare" width="42" height="42">
                    <span class="brand-text">
                        <strong>StuntCare</strong>
                        <small>Sistem Deteksi Dini Stunting</small>
                    </span>
                </a>

                <ul class="nav-links">
                    <li><a data-nav-target="home" href="<?= base_url('/#home') ?>">Home</a></li>
                    <li><a data-nav-target="about" href="<?= base_url('/#about') ?>">About</a></li>
                    <li><a data-nav-target="layanan" href="<?= base_url('/#layanan') ?>">Layanan</a></li>
                    <li><a data-nav-target="konseling" href="<?= base_url('/konsultasi') ?>">Konseling</a></li>
                    <li><a data-nav-target="contact" href="<?= base_url('/#contact') ?>">Contact</a></li>
                    <li><a data-nav-target="faq" href="<?= base_url('/#faq') ?>">FAQ</a></li>
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
                    var navLinks = document.querySelectorAll('[data-nav-target]');

                    if (!navbar || !toggle) {
                        return;
                    }

                    function setActiveNav() {
                        var path = window.location.pathname.replace(/\/+$/, '');
                        var hash = window.location.hash.replace('#', '');
                        var activeTarget = (path.indexOf('/konseling') !== -1 || path.indexOf('/konsultasi') !== -1) ? 'konseling' : (hash || 'home');

                        navLinks.forEach(function (link) {
                            link.classList.toggle('is-active', link.getAttribute('data-nav-target') === activeTarget);
                        });
                    }

                    toggle.addEventListener('click', function () {
                        var isOpen = navbar.classList.toggle('is-open');
                        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    });

                    setActiveNav();
                    window.addEventListener('hashchange', setActiveNav);
                });
            </script>
        </header>
