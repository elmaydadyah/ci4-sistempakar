        <header class="site-header">
            <nav class="hero-nav" aria-label="Navigasi utama">
                <a class="brand" href="<?= base_url('/') ?>" aria-label="Sistem Pakar Stunting">
                    <img class="landing-brand-logo" src="<?= base_url('assets/images/logo/logo_puskesmas.png') ?>" alt="Logo Puskesmas" width="52" height="52">
                    <span class="brand-text">
                        <strong>Puskesmas Cileungsi</strong>
                        <small>Deteksi Dini Risiko Stunting</small>
                    </span>
                </a>

                <ul class="nav-links">
                    <li><a data-nav-target="home" href="<?= base_url('/#home') ?>">Beranda</a></li>
                    <li><a data-nav-target="about" href="<?= base_url('/#about') ?>">Tentang</a></li>
                    <li><a data-nav-target="konseling" href="<?= base_url('/konsultasi') ?>">Konseling</a></li>
                    <li><a data-nav-target="blog" href="<?= base_url('/#blog') ?>">Artikel</a></li>
                    <li><a data-nav-target="contact" href="<?= base_url('/#contact') ?>">Kontak</a></li>
                    <li><a data-nav-target="faq" href="<?= base_url('/#faq') ?>">FAQ</a></li>
                    <li class="mobile-login-link"><a href="<?= base_url('/login') ?>">Masuk</a></li>
                </ul>

                <div class="nav-actions">
                    <a class="btn-start" href="<?= base_url('/login') ?>">Masuk</a>
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

                    var sectionTargets = ['home', 'about', 'layanan', 'blog', 'contact', 'faq'];
                    var sections = sectionTargets
                        .map(function (target) {
                            return document.getElementById(target);
                        })
                        .filter(Boolean);
                    var ticking = false;

                    function activateNav(target) {
                        navLinks.forEach(function (link) {
                            link.classList.toggle('is-active', link.getAttribute('data-nav-target') === target);
                        });
                    }

                    function getStaticActiveTarget() {
                        var path = window.location.pathname.replace(/\/+$/, '');
                        var hash = window.location.hash.replace('#', '');

                        if (path.indexOf('/konseling') !== -1 || path.indexOf('/konsultasi') !== -1) {
                            return 'konseling';
                        }

                        return hash || 'home';
                    }

                    function setActiveNav() {
                        activateNav(getStaticActiveTarget());
                    }

                    function setActiveNavOnScroll() {
                        if (!sections.length) {
                            setActiveNav();
                            return;
                        }

                        var headerHeight = document.querySelector('.site-header')?.offsetHeight || 0;
                        var activeTarget = sections[0].id;
                        var marker = headerHeight + Math.round(window.innerHeight * 0.32);

                        sections.forEach(function (section) {
                            if (section.getBoundingClientRect().top <= marker) {
                                activeTarget = section.id;
                            }
                        });

                        activateNav(activeTarget);
                    }

                    function requestScrollSpy() {
                        if (ticking) {
                            return;
                        }

                        ticking = true;
                        window.requestAnimationFrame(function () {
                            setActiveNavOnScroll();
                            ticking = false;
                        });
                    }

                    toggle.addEventListener('click', function () {
                        var isOpen = navbar.classList.toggle('is-open');
                        toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
                    });

                    setActiveNavOnScroll();
                    window.addEventListener('scroll', requestScrollSpy, { passive: true });
                    window.addEventListener('resize', requestScrollSpy);
                    window.addEventListener('hashchange', function () {
                        window.setTimeout(setActiveNavOnScroll, 80);
                    });
                });
            </script>
        </header>
