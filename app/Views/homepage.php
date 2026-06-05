<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/base/reset.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/pages/homepage.css') . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
</head>
<body>

    <!-- ══ MOBILE OVERLAY ══ -->
    <div class="hp-overlay" id="hpOverlay">
        <div class="hp-overlay__panel">
            <button class="hp-overlay__close" id="hpOverlayClose">&#10005;</button>
            <nav class="hp-overlay__nav">
                <a href="#home"     class="hp-overlay__link" onclick="hpCloseMenu()">Home</a>
                <a href="<?= base_url('about') ?>"    class="hp-overlay__link">About Us</a>
                <a href="<?= base_url('features') ?>" class="hp-overlay__link">Features</a>
                <a href="<?= base_url('contact') ?>"  class="hp-overlay__link">Contact</a>
            </nav>
            <a href="<?= base_url('sign-in') ?>" class="hp-overlay__cta">Get Started</a>
        </div>
    </div>

    <div class="hp" id="home">

        <!-- ══ HEADER ══ -->
        <header class="hp-header">
            <div class="hp-header__inner">
                <a href="<?= base_url('/') ?>" class="hp-header__logo">
                    <img src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="Akademus Logo" class="hp-header__logo-img" />
                    <span class="hp-header__brand">AKADEMUS</span>
                </a>
                <nav class="hp-header__nav">
                    <a href="#home"                           class="hp-header__link hp-header__link--active">Home</a>
                    <a href="<?= base_url('about') ?>"        class="hp-header__link">About Us</a>
                    <a href="<?= base_url('features') ?>"     class="hp-header__link">Features</a>
                    <a href="<?= base_url('contact') ?>"      class="hp-header__link">Contact</a>
                </nav>
                <a href="<?= base_url('sign-in') ?>" class="hp-header__cta">Get Started</a>
                <button class="hp-hamburger" id="hpHamburger" aria-label="Open menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </header>

        <!-- ══ HERO ══ -->
        <section class="hp-hero">
            <img class="hp-hero__bg" src="<?= base_url('assets/images/bg.jpg') ?>" alt="Hero Background" />
            <div class="hp-hero__card">
                <p class="hp-hero__subtitle">WELCOME TO AKADEMUS</p>
                <h1 class="hp-hero__title">Where Smart Learning Begins and Grows</h1>
                <button class="hp-hero__btn"
                        onclick="document.getElementById('featured').scrollIntoView({behavior:'smooth'})">
                    Learn More
                </button>
            </div>
        </section>

        <!-- ══ MIDDLE BAND (featured + other, clips deco shapes) ══ -->
        <div class="hp-middle">

        <!-- ══ DECORATIVE BACKGROUND SHAPES ══ -->
        <div class="hp-deco hp-deco--yellow" aria-hidden="true"></div>
        <div class="hp-deco hp-deco--blue"   aria-hidden="true"></div>

        <!-- ══ FEATURED SYSTEMS ══ -->
        <section class="hp-featured" id="featured">
            <div class="hp-section-header">
                <div class="hp-section-header__line"></div>
                <h2 class="hp-section-header__title">Featured Systems</h2>
            </div>

            <div class="hp-featured__cards">
                <!-- Akademus -->
                <div class="hp-feat-card">
                    <img class="hp-feat-card__img" src="<?= base_url('assets/images/pillars.jpg') ?>" alt="Akademus" />
                    <div class="hp-feat-card__overlay"></div>
                    <div class="hp-feat-card__content">
                        <img class="hp-feat-card__logo" src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="" />
                        <h3 class="hp-feat-card__title">Akademus</h3>
                        <p class="hp-feat-card__body">Akademos is a modern learning platform designed to provide a structured, reliable, and convenient environment where learners can access high-quality lessons, courses, and academic materials. By organizing a clear content from basic concepts to advanced topics.</p>
                    </div>
                </div>

                <!-- Mentora -->
                <div class="hp-feat-card">
                    <img class="hp-feat-card__img" src="<?= base_url('assets/images/book.jpg') ?>" alt="Mentora" />
                    <div class="hp-feat-card__overlay"></div>
                    <div class="hp-feat-card__content">
                        <img class="hp-feat-card__logo" src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="" />
                        <h3 class="hp-feat-card__title">Mentora</h3>
                        <p class="hp-feat-card__body">Mentora is a smart Learning Management System (LMS) designed to help teachers, and students in managing their classes. Mentora allows teachers to upload lessons, create activities and track student progress in one platform.</p>
                    </div>
                </div>

                <!-- Learnix -->
                <div class="hp-feat-card">
                    <img class="hp-feat-card__img" src="<?= base_url('assets/images/comp.jpg') ?>" alt="Learnix" />
                    <div class="hp-feat-card__overlay"></div>
                    <div class="hp-feat-card__content">
                        <img class="hp-feat-card__logo" src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="" />
                        <h3 class="hp-feat-card__title">Learnix</h3>
                        <p class="hp-feat-card__body">Learnix is the game-based learning portal under Mentora. It is designed to make learning more engaging, and motivating through educational games and challenges that support different subjects.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- ══ OTHER SYSTEMS ══ -->
        <section class="hp-other" id="services">
            <div class="hp-section-header">
                <div class="hp-section-header__line hp-section-header__line--blue"></div>
                <h2 class="hp-section-header__title">Other Systems</h2>
            </div>

            <div class="hp-other__grid">
                <?php
                $systems = [
                    'OTIS'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem velit, at posuere augue tincidunt eget.',
                    'Akademus' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem velit, at posuere augue tincidunt eget.',
                    'Learnix'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem velit, at posuere augue tincidunt eget.',
                    'Mentora'  => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem velit, at posuere augue tincidunt eget.',
                    'ONESYS'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem velit, at posuere augue tincidunt eget.',
                    'Sample'   => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem velit, at posuere augue tincidunt eget.',
                ];
                foreach ($systems as $name => $body): ?>
                <div class="hp-sys-card">
                    <div class="hp-sys-card__line"></div>
                    <h3 class="hp-sys-card__title"><?= $name ?></h3>
                    <p class="hp-sys-card__body"><?= $body ?></p>
                    <div class="hp-sys-card__explore">
                        <span class="hp-sys-card__explore-text">Explore</span>
                        <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                            <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.3431 14.435L19.7071 8.07107ZM0 7.36396V8.36396H19V7.36396V6.36396H0V7.36396Z" fill="white" />
                        </svg>
                        <img class="hp-sys-card__icon" src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="" />
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </section>

        </div><!-- /.hp-middle -->

        <!-- ══ FOOTER ══ -->
        <footer class="hp-footer" id="contact">
            <div class="hp-footer__inner">
                <div class="hp-footer__brand">
                    <div class="hp-footer__logo-wrap">
                        <img class="hp-footer__logo-img" src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="Akademus Logo" />
                        <span class="hp-footer__logo-name">Akademus</span>
                    </div>
                    <p class="hp-footer__desc">Akademos is a modern learning platform that provides structured and reliable access to courses and academic resources.</p>
                </div>

                <div class="hp-footer__col">
                    <h4 class="hp-footer__col-title">Resources</h4>
                    <a href="#" class="hp-footer__col-link">About</a>
                    <a href="#" class="hp-footer__col-link">Career</a>
                    <a href="#" class="hp-footer__col-link">Contact</a>
                    <a href="#" class="hp-footer__col-link">Partners</a>
                </div>

                <div class="hp-footer__col">
                    <h4 class="hp-footer__col-title">Product</h4>
                    <a href="#" class="hp-footer__col-link">OTIS</a>
                    <a href="#" class="hp-footer__col-link">Learnix</a>
                    <a href="#" class="hp-footer__col-link">Mentora</a>
                    <a href="#" class="hp-footer__col-link">Akademos</a>
                </div>

                <div class="hp-footer__col">
                    <h4 class="hp-footer__col-title">Company</h4>
                    <a href="#" class="hp-footer__col-link">About</a>
                    <a href="#" class="hp-footer__col-link">Career</a>
                    <a href="#" class="hp-footer__col-link">Contact</a>
                    <a href="#" class="hp-footer__col-link">Partners</a>
                </div>
            </div>

            <div class="hp-footer__divider"></div>

            <div class="hp-footer__bottom">
                <p class="hp-footer__copy">2026 Akademus. All rights reserved.</p>
                <div class="hp-footer__legal">
                    <a href="#" class="hp-footer__legal-link">Privacy Policy</a>
                    <a href="#" class="hp-footer__legal-link">Terms of Service</a>
                    <a href="#" class="hp-footer__legal-link">Cookie Settings</a>
                </div>
            </div>
        </footer>

    </div><!-- .hp -->

    <script>
        /* ── Mobile menu ── */
        function hpOpenMenu() {
            document.getElementById('hpOverlay').classList.add('open');
            document.body.style.overflow = 'hidden';
        }
        function hpCloseMenu() {
            document.getElementById('hpOverlay').classList.remove('open');
            document.body.style.overflow = '';
        }
        document.getElementById('hpHamburger').addEventListener('click', hpOpenMenu);
        document.getElementById('hpOverlayClose').addEventListener('click', hpCloseMenu);
        document.getElementById('hpOverlay').addEventListener('click', function(e) {
            if (e.target === this) hpCloseMenu();
        });

        /* ── Smooth scroll ── */
        document.querySelectorAll('a[href^="#"]').forEach(function(a) {
            a.addEventListener('click', function(e) {
                var t = document.querySelector(this.getAttribute('href'));
                if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
            });
        });

        /* ── Scroll spy ── */
        var spySections = ['home', 'featured', 'services', 'contact'];
        window.addEventListener('scroll', function() {
            var y = window.scrollY + 120;
            spySections.forEach(function(id) {
                var el = document.getElementById(id);
                if (!el) return;
                if (el.offsetTop <= y && el.offsetTop + el.offsetHeight > y) {
                    document.querySelectorAll('.hp-header__link').forEach(function(l) {
                        l.classList.remove('hp-header__link--active');
                    });
                    var link = document.querySelector('.hp-header__link[href="#' + id + '"]');
                    if (link) link.classList.add('hp-header__link--active');
                }
            });
        });
    </script>
    <script src="<?= base_url('assets/js/page-animation.js') ?>"></script>
    <script src="<?= base_url('assets/js/page-transition-animation.js') ?>"></script>
</body>
</html>
