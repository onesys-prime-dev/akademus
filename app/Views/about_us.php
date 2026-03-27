<?php
// about_us.php – Akademus About Us Page
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Akademus &#8212; About Us</title>
  <!-- Same CSS files as homepage -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css') . '?v=' . time(); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/base/reset.css') . '?v=' . time(); ?>">
  <link rel="stylesheet" href="<?= base_url('assets/css/pages/homepage.css') . '?v=' . time(); ?>">
  <style>
    /* ── About Us page-specific styles only ── */
    body { background: #0040a6; overflow-x: hidden; }
    img { display: block; max-width: 100%; }

    /* ---- HERO ---- */
    #hero {
      width: 100%;
      position: relative;
      overflow: hidden;
    }
    .hero-bg {
      width: 100%;
      height: 725px;
      object-fit: cover;
      display: block;
    }
    .hero-overlay {
      position: absolute;
      inset: 0;
      padding: 120px 70px 0;
      display: flex;
      flex-direction: column;
    }
    .hero-top {
      display: flex;
      flex-direction: row;
      align-items: flex-start;
      width: 100%;
    }
    .hero-headings { display: flex; flex-direction: column; line-height: 1.1; }
    .hero-headings span {
      font-weight: 500;
      font-size: 120px;
      color: #ffb200;
      line-height: 1.1;
    }
    .hero-desc {
      display: flex;
      align-items: center;
      align-self: stretch;
      margin-left: auto;
      padding-left: 32px;
    }
    .hero-desc p {
      font-weight: 500;
      font-size: 23px;
      color: #fff;
      text-align: right;
      line-height: normal;
    }

    /* ---- ABOUT CONTENT ROW ---- */
    #about {
      width: 100%;
      display: flex;
      flex-direction: row;
      padding: 40px 70px;
      gap: 60px;
    }
    .about-left {
      display: flex;
      flex-direction: column;
      gap: 66px;
      width: 846px;
      flex-shrink: 0;
    }
    .about-left > p {
      font-weight: 500;
      font-size: 35px;
      color: #fff;
      text-align: justify;
      line-height: normal;
    }
    .about-thumbnail {
      width: 100%;
      background: #a6a6a6;
      border-radius: 40px;
      overflow: hidden;
    }
    .about-thumbnail img {
      width: 100%;
      height: 223px;
      object-fit: cover;
    }
    .about-card {
      flex: 1;
      border-radius: 40px;
      background: linear-gradient(180deg, #1f75ff 0%, #00296a 100%);
      padding: 62px 89px;
      display: flex;
      flex-direction: column;
    }
    .about-card h2 {
      align-self: flex-end;
      font-weight: 500;
      font-size: 30px;
      color: #fff;
      margin-bottom: 38px;
    }
    .about-card p {
      font-weight: 500;
      font-size: 21px;
      color: #fff;
      text-align: justify;
      line-height: normal;
    }
    .about-card p + p { margin-top: 37px; }

    /* ---- MISSION & VISION ---- */
    #mission {
      width: 100%;
      background: linear-gradient(0deg, #1f75ff 0%, #00296a 100%);
      box-shadow: 0 17px 13.7px rgba(0,0,0,.15);
      padding: 92.5px 0;
      display: flex;
      flex-direction: column;
    }
    .section-header {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3px;
      margin-bottom: 64px;
    }
    /* [IMAGE-LABEL: section-divider-line] — decorative SVG line above section titles */
    .section-header img { height: 3px; width: 342px; }
    .section-header h2 {
      font-weight: 500;
      font-size: 80px;
      color: #ffb200;
      text-align: center;
      line-height: normal;
    }
    .mv-block {
      width: 100%;
      padding: 0 70px;
      display: flex;
      flex-direction: row;
      align-items: flex-start;
      gap: 32px;
    }
    .mv-block.vision-block { flex-direction: row-reverse; padding: 0 68px; }
    .mv-block + .mv-block { margin-top: 126px; }
    .mv-block img {
      width: 878px;
      flex-shrink: 0;
      height: 556px;
      border-radius: 40px;
      object-fit: cover;
    }
    .vision-block img { width: 874px; }
    .mv-text {
      display: flex;
      flex-direction: column;
      padding-top: 48px;
      flex: 1;
    }
    .mv-text h3 {
      font-weight: 500;
      font-size: 50px;
      color: #fff;
      text-align: justify;
      margin-bottom: 16px;
    }
    .mv-text p {
      font-weight: 400;
      font-size: 27px;
      color: #fff;
      text-align: justify;
      line-height: normal;
    }

    /* ---- CORE VALUES ---- */
    #core-values {
      width: 100%;
      background: linear-gradient(180deg, rgba(0,35,91,1) 0%, rgba(0,74,193,1) 100%);
      box-shadow: 8px 13px 8px rgba(0,0,0,.25);
      padding: 116px 68px;
      display: flex;
      flex-direction: column;
    }
    #core-values .section-header { margin-bottom: 32px; }
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 60px;
    }
    .cv-card { position: relative; height: 420px; }
    .cv-card .deco-box {
      position: absolute;
      top: 0;
      left: 40px;
      width: 100px;
      height: 86px;
    }
    .cv-card .deco-box.dark  { background: #002c73; }
    .cv-card .deco-box.light { background: #003c9c; }
    .cv-card .border-box {
      position: absolute;
      top: 63px;
      left: 0; right: 0; bottom: 0;
      border: 3px solid #fff;
    }
    .cv-card .card-title {
      position: absolute;
      top: 110px;
      left: 32px;
      font-weight: 500;
      font-size: 20px;
      color: #fff;
      text-align: justify;
    }
    .cv-card .card-desc {
      position: absolute;
      top: 152px;
      left: 32px;
      right: 16px;
      font-weight: 400;
      font-size: 17px;
      color: #fff;
      text-align: justify;
      line-height: normal;
    }

    /* ---- HISTORY ---- */
    #history {
      width: 100%;
      display: flex;
      flex-direction: column;
      gap: 64px;
      padding: 32px 16px;
    }
    .history-header { position: relative; width: 100%; padding: 0 16px; }
    .history-header h2 {
      font-weight: 500;
      font-size: 80px;
      color: #ffb200;
      line-height: normal;
    }
    /* [IMAGE-LABEL: history-divider-line] — decorative SVG line below History heading */
    .history-header img { width: 100%; height: 3px; margin-top: 8px; }
    .history-block {
      position: relative;
      width: 100%;
      display: flex;
      flex-direction: row;
      align-items: stretch;
    }
    .history-img {
      position: relative;
      z-index: 2;
      width: 47%;
      flex-shrink: 0;
    }
    .history-img img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      min-height: 320px;
      max-height: 550px;
    }
    .history-text {
      flex: 1;
      background: linear-gradient(180deg, #1f75ff 0%, #00296a 100%);
      display: flex;
      align-items: center;
      padding: 40px;
      z-index: 1;
    }
    .history-block.img-left  .history-text { margin-left: -8%; }
    .history-block.img-right .history-text { margin-right: -8%; }
    .history-block.img-right { flex-direction: row-reverse; }
    .history-text p {
      font-weight: 500;
      font-size: 28px;
      color: #fff;
      text-align: justify;
      line-height: normal;
    }

    /* ---- FOOTER ---- */
    footer {
      width: 100%;
      background: #000c5d;
      box-shadow: 0 4px 4px rgba(0,0,0,.25);
      padding: 40px 70px;
    }
    .footer-main {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      gap: 32px;
    }
    .footer-brand { display: flex; flex-direction: column; max-width: 400px; }
    .footer-brand-top { display: flex; align-items: center; gap: 16px; margin-bottom: 24px; }
    /* [IMAGE-LABEL: footer-logo] — Akademus logo in footer */
    .footer-brand-top img { width: 60px; height: 60px; border-radius: 14px; object-fit: cover; }
    .footer-brand-top span {
      font-weight: 500;
      font-size: 28px;
      color: #ffb200;
      line-height: normal;
    }
    .footer-brand p {
      font-weight: 400;
      font-size: 16px;
      color: #fff;
      text-align: justify;
      line-height: normal;
    }
    .footer-nav-cols { display: flex; flex-direction: row; gap: 80px; flex-wrap: wrap; }
    .footer-nav-col { display: flex; flex-direction: column; min-width: 80px; }
    .footer-nav-col .col-title {
      font-weight: 500;
      font-size: 20px;
      color: #ffb200;
      margin-bottom: 16px;
    }
    .footer-nav-col a {
      font-weight: 400;
      font-size: 16px;
      color: #fff;
      margin-top: 12px;
    }
    .footer-nav-col a:hover { text-decoration: underline; }
    .footer-separator {
      width: 100%;
      height: 1px;
      background: rgba(255,255,255,.3);
      margin: 24px 0;
    }
    .footer-bottom {
      display: flex;
      flex-direction: row;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
    }
    .footer-bottom span {
      font-weight: 400;
      font-size: 16px;
      color: #fff;
    }
    .footer-bottom .legal-links { display: flex; flex-direction: row; gap: 32px; flex-wrap: wrap; }
    .footer-bottom .legal-links a {
      font-weight: 400;
      font-size: 16px;
      color: #fff;
      text-decoration: underline;
    }
    .footer-bottom .legal-links a:hover { color: #ffb200; }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 1024px) {
      .header { padding: 0 16px; }
      .navbar, .CREATE-ACCOUNT { display: none; }
      .hamburger { display: flex; }

      .hero-bg { height: 400px; }
      .hero-overlay { padding: 60px 16px 0; }
      .hero-headings span { font-size: 60px; }
      .hero-desc p { font-size: 16px; }

      #about { flex-direction: column; padding: 32px 16px; gap: 32px; }
      .about-left { width: 100%; gap: 32px; }
      .about-left > p { font-size: 18px; }
      .about-thumbnail img { height: 180px; }
      .about-card { padding: 40px 24px; }
      .about-card p { font-size: 16px; }

      #mission { padding: 50px 0; }
      .section-header h2 { font-size: 48px; }
      .section-header img { width: 200px; }
      .mv-block, .mv-block.vision-block { flex-direction: column; padding: 0 16px; }
      .mv-block img { width: 100%; height: 280px; border-radius: 24px; }
      .mv-block + .mv-block { margin-top: 40px; }
      .mv-text { padding-top: 16px; }
      .mv-text h3 { font-size: 36px; }
      .mv-text p { font-size: 17px; }

      #core-values { padding: 60px 16px; }
      .cards-grid { grid-template-columns: 1fr; gap: 40px; }

      #history { gap: 40px; padding: 32px 8px; }
      .history-header h2 { font-size: 48px; }
      .history-block, .history-block.img-right { flex-direction: column; }
      .history-img { width: 100%; }
      .history-img img { height: 240px; min-height: unset; max-height: unset; }
      .history-block.img-left  .history-text { margin-left: 0; }
      .history-block.img-right .history-text { margin-right: 0; }
      .history-text p { font-size: 17px; }

      footer { padding: 40px 16px; }
      .footer-main { flex-direction: column; }
      .footer-nav-cols { gap: 32px; }
      .footer-bottom { flex-direction: column; align-items: flex-start; }
    }

    @media (max-width: 640px) {
      .hero-headings span { font-size: 40px; }
      .hero-desc { display: none; }
      .cards-grid { grid-template-columns: 1fr; }
      .footer-nav-cols { gap: 24px; }
    }
  </style>
</head>
<body>

<!-- ==================== MOBILE OVERLAY ==================== -->
<div class="hp-overlay" id="hpOverlay">
    <div class="hp-overlay__panel">
        <button class="hp-overlay__close" id="hpOverlayClose">&#10005;</button>
        <nav class="hp-overlay__nav">
            <a href="<?= base_url('/') ?>"      class="hp-overlay__link">Home</a>
            <a href="<?= base_url('about') ?>"  class="hp-overlay__link">About Us</a>
            <a href="<?= base_url('/') ?>#featured" class="hp-overlay__link">Features</a>
            <a href="<?= base_url('/') ?>#services" class="hp-overlay__link">Services</a>
            <a href="<?= base_url('/') ?>#contact"  class="hp-overlay__link">Contact</a>
        </nav>
        <a href="<?= base_url('sign-in') ?>" class="hp-overlay__cta">Get Started</a>
    </div>
</div>

<!-- ==================== NAVBAR ==================== -->
<header class="hp-header">
    <div class="hp-header__inner">
        <a href="<?= base_url('/') ?>" class="hp-header__logo">
            <img src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="Akademus Logo" class="hp-header__logo-img" />
            <span class="hp-header__brand">AKADEMUS</span>
        </a>
        <nav class="hp-header__nav">
            <a href="<?= base_url('/') ?>"          class="hp-header__link">Home</a>
            <a href="<?= base_url('about') ?>"       class="hp-header__link hp-header__link--active">About Us</a>
            <a href="<?= base_url('/') ?>#featured"  class="hp-header__link">Features</a>
            <a href="<?= base_url('/') ?>#services"  class="hp-header__link">Services</a>
            <a href="<?= base_url('/') ?>#contact"   class="hp-header__link">Contact</a>
        </nav>
        <a href="<?= base_url('sign-in') ?>" class="hp-header__cta">Get Started</a>
        <button class="hp-hamburger" id="hpHamburger" aria-label="Open menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</header>

<!-- ==================== HERO ==================== -->
<div id="hero" class="hero-wrapper" style="position:relative;width:100%;">
  <!-- [IMAGE-LABEL: hero-bg] — Full-width hero background image -->
  <img class="hero-bg" src="assets/images/about_hero_bg.png" alt="Hero background" />
  <div class="hero-overlay">
    <div class="hero-top">
      <div class="hero-headings">
        <span>Get to Know US</span>
        <span>Better</span>
      </div>
      <div class="hero-desc">
        <p>
          Learn about our commitment to excellence, innovation and<br />
          the principles that guide our work every day.
        </p>
      </div>
    </div>
  </div>
</div>

<!-- ==================== ABOUT ==================== -->
<div id="about">
  <!-- Left column -->
  <div class="about-left">
    <p>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc,
      in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec
      turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius
      sem velit, at posuere augue tincidunt eget.
    </p>
    <div class="about-thumbnail">
      <!-- [IMAGE-LABEL: about-thumbnail] — Photo/image in the About section left column -->
      <img src="assets/images/about_thumbnail.png" alt="About Akademus" />
    </div>
  </div>

  <!-- Right: About Us card -->
  <div class="about-card">
    <h2>ABOUT US</h2>
    <p>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc,
      in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec
      turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius
      sem velit, at posuere augue tincidunt eget. Etiam auctor cursus pellentesque. Aenean
      accumsan quis arcu ac mattis. Curabitur justo quam, porttitor ut sagittis non,
      malesuada ut ipsum. In vel convallis nulla. Ut lacinia aliquet suscipit.
    </p>
    <p>
      Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc,
      in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec
      turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius
      sem velit, at posuere augue tincidunt eget.
    </p>
  </div>
</div>

<!-- ==================== MISSION & VISION ==================== -->
<section id="mission">
  <div class="section-header">
    <!-- [IMAGE-LABEL: section-divider-line] — Decorative SVG/PNG horizontal line above section title -->
    <img src="assets/images/line_divider.svg" alt="" />
    <h2>Mission &amp; Vision</h2>
  </div>

  <!-- Mission -->
  <div class="mv-block img-left">
    <!-- [IMAGE-LABEL: mission-image] — Illustration/photo for the Mission block -->
    <img src="assets/images/mission_img.png" alt="Mission" />
    <div class="mv-text">
      <h3>Mission</h3>
      <p>
        To provide a reliable and user-friendly learning platform that supports students and educators in achieving academic success through organized and accessible digital education.
      </p>
    </div>
  </div>

  <!-- Vision -->
  <div class="mv-block vision-block img-right">
    <!-- [IMAGE-LABEL: vision-image] — Illustration/photo for the Vision block -->
    <img src="assets/images/vision_img.png" alt="Vision" />
    <div class="mv-text">
      <h3>Vision</h3>
      <p>
        To become a leading learning platform that empowers every student to learn anytime, anywhere, and reach their full potential
      </p>
    </div>
  </div>
</section>

<!-- ==================== CORE VALUES ==================== -->
<section id="core-values">
  <div class="section-header">
    <!-- [IMAGE-LABEL: section-divider-line] — Same decorative line used above Core Values title -->
    <img src="assets/images/line_divider.svg" alt="" />
    <h2>Core Values</h2>
  </div>

  <div class="cards-grid">
    <?php
    $coreValues = [
      ["title" => "Excellence", "deco" => "dark",
       "desc"  => "We aim to provide high-quality learning experiences."],
      ["title" => "LOREM IPSUM", "deco" => "dark",
       "desc"  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis."],
      ["title" => "LOREM IPSUM", "deco" => "dark",
       "desc"  => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis."],
    ];
    foreach ($coreValues as $card):
    ?>
    <div class="cv-card">
      <div class="deco-box <?= htmlspecialchars($card['deco']) ?>"></div>
      <div class="border-box"></div>
      <div class="card-title"><?= htmlspecialchars($card['title']) ?></div>
      <p class="card-desc"><?= htmlspecialchars($card['desc']) ?></p>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ==================== HISTORY ==================== -->
<section id="history">
  <div class="history-header">
    <h2>History</h2>
    <!-- [IMAGE-LABEL: history-divider-line] — Decorative line below History heading -->
    <img src="assets/images/line_divider.svg" alt="" />
  </div>

  <?php
  $historyBlocks = [
    [
      "imgLeft" => true,
      "img"     => "assets/images/history_img_1.png",
      "text"    => "Akademus was officially launched in 2026 as the flagship intellectual division of ONESYS. The name — inspired by the classical roots of the \"Academy\" — was chosen to signal a return to deep learning, critical thinking, and structured excellence.",
    ],
    [
      "imgLeft" => false,
      "img"     => "assets/images/history_img_2.png",
      "text"    => "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc, in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius sem vel.",
    ],
  ];
  foreach ($historyBlocks as $block):
    $cls = $block['imgLeft'] ? 'img-left' : 'img-right';
  ?>
  <div class="history-block <?= $cls ?>">
    <div class="history-img">
      <!-- [IMAGE-LABEL: history-block-image] — Photo for each history entry -->
      <img src="<?= htmlspecialchars($block['img']) ?>" alt="History" />
    </div>
    <div class="history-text">
      <p><?= htmlspecialchars($block['text']) ?></p>
    </div>
  </div>
  <?php endforeach; ?>
</section>

<!-- ==================== FOOTER ==================== -->
<footer>
  <div class="footer-main">
    <!-- Brand -->
    <div class="footer-brand">
      <div class="footer-brand-top">
        <!-- [IMAGE-LABEL: footer-logo] — Akademus logo in footer -->
        <img class="image" src="assets/images/logo_akademus.png" alt="Akademus Logo" />
        <span>Akademus</span>
      </div>
      <p>Akademos is a modern learning platform that provides structured and reliable access to courses and academic resources.</p>
    </div>

    <!-- Nav columns -->
    <div class="footer-nav-cols">
      <?php
      $footerCols = [
        "Product"   => ["OTIS", "Learnix", "Mentora", "Akademos"],
        "Resources" => ["About", "Career", "Contact", "Partners"],
        "Company"   => ["About", "Career", "Contact", "Partners"],
      ];
      foreach ($footerCols as $colTitle => $links):
      ?>
      <nav class="footer-nav-col">
        <span class="col-title"><?= htmlspecialchars($colTitle) ?></span>
        <?php foreach ($links as $link): ?>
          <a href="#"><?= htmlspecialchars($link) ?></a>
        <?php endforeach; ?>
      </nav>
      <?php endforeach; ?>
    </div>
  </div>

  <div class="footer-separator"></div>

  <div class="footer-bottom">
    <span>&copy; 2026 Akademus. All rights reserved.</span>
    <div class="legal-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
      <a href="#">Cookie Settings</a>
    </div>
  </div>
</footer>

<!-- ==================== SCRIPTS ==================== -->
<script>
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
</script>

</body>
</html>