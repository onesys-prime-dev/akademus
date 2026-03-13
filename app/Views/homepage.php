<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta charset="UTF-8"/>
        <link rel="stylesheet" href="<?= base_url('assets/css/base/global.css') . '?v=' . time(); ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/base/reset.css') . '?v=' . time(); ?>">
        <link rel="stylesheet" href="<?= base_url('assets/css/pages/homepage.css') . '?v=' . time(); ?>">
    </head>
    <body>
        <div class="homepage">

            <!-- ══ HEADER / NAV ══ -->
            <div class="header-navigation">
                <div class="header">
                    <!-- Logo + Brand -->
                    <div class="left-content">
                        <img class="image" src="assets/images/logo_akademus.png" alt="Logo"/>
                        <div class="text-wrapper-3">AKADEMUS</div>
                    </div>

                    <!-- Desktop Nav -->
                    <div class="navbar">
                        <div class="item">Home</div>
                        <div class="text-wrapper">About Us</div>
                        <div class="div">Features</div>
                        <div class="item-2">Services</div>
                        <div class="item-3">Contact</div>
                    </div>

                    <!-- Desktop CTA -->
                    <div class="CREATE-ACCOUNT">
                        <div class="CREATE-ACCOUNT-2"></div>
                        <a class="text-wrapper-2" href="<?= base_url('sign-in') ?>">Get Started</a>
                    </div>

                    <!-- Mobile Hamburger -->
                    <div class="hamburger" onclick="toggleMobileNav(this)">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </div>

                <!-- Mobile Nav Dropdown -->
                <div class="mobile-nav" id="mobileNav">
                    <div class="nav-item">Home</div>
                    <div class="nav-item">About Us</div>
                    <div class="nav-item">Features</div>
                    <div class="nav-item">Services</div>
                    <div class="nav-item">Contact</div>
                    <a class="nav-cta" href="<?= base_url('sign_in') ?>">Get Started</a>
                </div>

                <!-- Hero BG Image -->
                <img class="frame" src="assets/images/bg.jpg" alt="Hero Background"/>

                <!-- Hero Overlay Card -->
                <div class="container">
                    <div class="container-2"></div>
                    <div class="text-wrapper-4">Welcome to Akademus</div>
                    <p class="p">Cultivating Excellence Through Guided Learning.</p>
                    <div class="learn-more">
                        <div class="rectangle"></div>
                        <div class="text-wrapper-5">Learn More</div>
                    </div>
                </div>
            </div>

            <!-- ══ FEATURED SYSTEMS HEADER ══ -->
            <div class="FEATURED-SYSTEMS">
                <div class="header-3">
                    <img class="line-3" src="assets/images/line.png" alt=""/>
                    <div class="text-wrapper-13">Featured Systems</div>
                </div>
            </div>

            <!-- ══ AKADEMUS CONTENT ══ -->
            <div class="akademus-content">
                <div class="rectangle-4"></div>
                <div class="feature-inner">
                    <img class="feature-img" src="assets/images/pillars.jpg" alt="Akademus Preview"/>
                    <div class="feature-text">
                        <div class="feature-title">Akademus</div>
                        <p class="feature-body">
                            Akademos serves as the central hub of learning, providing a structured and reliable source of
                            knowledge. It organizes lessons, courses, and academic materials in a clear progression from basic
                            to advanced topics, ensuring that learners acquire information in a consistent and goal-oriented
                            manner. For example, a learner studying Data Analysis can follow a sequence of lessons in Akademos,
                            starting with Excel basics and gradually progressing to advanced statistical concepts. In this way,
                            Akademos forms the foundation of serious learning by combining credibility, structure, and depth.
                        </p>
                        <div class="feature-btn learn-more-2">
                            <div class="rectangle-3"></div>
                            <div class="btn-label text-wrapper-16">READ MORE</div>
                            <img class="arrow" src="assets/images/arrow.png" alt=""/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ MENTORA CONTENT ══ -->
            <div class="mentora-contents">
                <div class="rectangle-5"></div>
                <div class="feature-inner reverse">
                    <img class="feature-img" src="assets/images/book.jpg" alt="Mentora Preview"/>
                    <div class="feature-text">
                        <div class="feature-title">Mentora</div>
                        <p class="feature-body">
                            Akademos serves as the central hub of learning, providing a structured and reliable source of
                            knowledge. It organizes lessons, courses, and academic materials in a clear progression from basic
                            to advanced topics, ensuring that learners acquire information in a consistent and goal-oriented
                            manner. For example, a learner studying Data Analysis can follow a sequence of lessons in Akademos,
                            starting with Excel basics and gradually progressing to advanced statistical concepts. In this way,
                            Akademos forms the foundation of serious learning by combining credibility, structure, and depth.
                        </p>
                        <div class="feature-btn learn-more-3">
                            <div class="rectangle-3"></div>
                            <div class="btn-label text-wrapper-17-btn">READ MORE</div>
                            <img class="arrow" src="assets/images/arrow.png" alt=""/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ LEARNIX CONTENT ══ -->
            <div class="learnix-contents">
                <div class="rectangle-2"></div>
                <div class="feature-inner">
                    <img class="feature-img" src="assets/images/comp.jpg" alt="Learnix Preview"/>
                    <div class="feature-text">
                        <div class="feature-title">Learnix</div>
                        <p class="feature-body">
                            Akademos serves as the central hub of learning, providing a structured and reliable source of
                            knowledge. It organizes lessons, courses, and academic materials in a clear progression from basic
                            to advanced topics, ensuring that learners acquire information in a consistent and goal-oriented
                            manner. For example, a learner studying Data Analysis can follow a sequence of lessons in Akademos,
                            starting with Excel basics and gradually progressing to advanced statistical concepts. In this way,
                            Akademos forms the foundation of serious learning by combining credibility, structure, and depth.
                        </p>
                        <div class="feature-btn learn-more-2">
                            <div class="rectangle-3"></div>
                            <div class="btn-label text-wrapper-6">READ MORE</div>
                            <img class="arrow" src="assets/images/arrow.png" alt=""/>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ══ FUTURE SYSTEMS HEADER ══ -->
            <div class="FUTURE-SYSTEMS">
                <div class="header-2">
                    <img class="line-2" src="assets/images/line.png" alt=""/>
                    <div class="text-wrapper-12">Future Systems</div>
                </div>
            </div>

            <!-- ══ CARDS SECTION ══ -->
            <div class="group">
                <div class="top">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="group-2">
                        <div class="line"></div>
                        <div class="div-wrapper"><div class="text-wrapper-9">Akademus</div></div>
                        <div class="body">
                            <p class="text-wrapper-10">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc,
                                in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec
                                turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius
                                sem velit, at posuere augue tincidunt eget.
                            </p>
                        </div>
                        <div class="explore-and-icon">
                            <div class="text-wrapper-11">Explore</div>
                            <img class="img" src="assets/images/arrow_white.png" alt=""/>
                            <img class="image-2" src="assets/images/logo_akademus.png" alt=""/>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>

                <div class="bottom">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                    <div class="group-2">
                        <div class="line"></div>
                        <div class="div-wrapper"><div class="text-wrapper-9">Akademus</div></div>
                        <div class="body">
                            <p class="text-wrapper-10">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ornare tempus nunc,
                                in feugiat purus ullamcorper vitae. In ante ex, dapibus vitae ornare eget, rutrum nec
                                turpis. Fusce vitae velit tincidunt, malesuada erat sed, maximus felis. Mauris varius
                                sem velit, at posuere augue tincidunt eget.
                            </p>
                        </div>
                        <div class="explore-and-icon">
                            <div class="text-wrapper-11">Explore</div>
                            <img class="img" src="assets/images/arrow_white.png" alt=""/>
                            <img class="image-2" src="assets/images/logo_akademus.png" alt=""/>
                        </div>
                    </div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- ══ FOOTER ══ -->
            <footer class="site-footer">
                <div class="footer-inner">
                    <img class="footer-logo" src="assets/images/logo_akademus.png" alt="Akademus Logo"/>
                    <p class="footer-text">&copy; 2026 Akademus. All Rights Reserved.</p>
                </div>
            </footer>

        </div>

        <script>
            function toggleMobileNav(btn) {
                const nav = document.getElementById('mobileNav');
                nav.classList.toggle('open');
                // Animate hamburger → X
                const spans = btn.querySelectorAll('span');
                btn.classList.toggle('active');
                if (btn.classList.contains('active')) {
                    spans[0].style.transform = 'translateY(7px) rotate(45deg)';
                    spans[1].style.opacity = '0';
                    spans[2].style.transform = 'translateY(-7px) rotate(-45deg)';
                } else {
                    spans[0].style.transform = '';
                    spans[1].style.opacity = '';
                    spans[2].style.transform = '';
                }
            }
        </script>
    </body>
</html>