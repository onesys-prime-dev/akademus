<!-- ══ MOBILE OVERLAY ══ -->
<div class="hp-overlay" id="hpOverlay">
    <div class="hp-overlay__panel">
        <button class="hp-overlay__close" id="hpOverlayClose">&#10005;</button>
        <nav class="hp-overlay__nav">
            <a href=<?= base_url('homepage') ?> class="hp-overlay__link page-link" onclick="hpCloseMenu()">Home</a>
            <a href=<?= base_url('about-us') ?> class="hp-overlay__link page-link" onclick="hpCloseMenu()">About Us</a>
            <a href=<?= base_url('features') ?> class="hp-overlay__link page-link" onclick="hpCloseMenu()">Features</a>
            <!-- <a href="services.html" class="hp-overlay__link" onclick="hpCloseMenu()">Services</a> -->
            <a href=<?= base_url('contact') ?> class="hp-overlay__link page-link" onclick="hpCloseMenu()">Contact</a>
        </nav>
        <a href="<?= base_url('/sign-in') ?>" class="hp-overlay__cta page-link">Get Started</a>
    </div>
</div>

<!-- HEADER -->
<header class="header">
    <div class="header__inner">
        <a href=<?= base_url('homepage') ?> class="header__logo page-link">
            <img src=<?= base_url("assets/images/logo_akademus.png") ?> alt="Akademus Logo" class="header__logo-img" />
            <span class="header__brand">AKADEMUS</span>
        </a>
        <nav class="header__nav">
            <a href=<?= base_url('homepage') ?> class="header__link page-link">Home</a>
            <a href=<?= base_url('about-us') ?> class="header__link page-link">About Us</a>
            <a href=<?= base_url('features') ?> class="header__link page-link">Features</a>
            <!-- <a href="services.html" class="header__link page-link">Services</a> -->
            <a href=<?= base_url('contact') ?> class="header__link page-link">Contact</a>
        </nav>
        <a href="<?= base_url('/sign-in') ?>" class="header__cta page-link">Get Started</a>
        <button class="hamburger" id="hpHamburger" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>