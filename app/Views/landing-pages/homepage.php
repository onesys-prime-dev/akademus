<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Home </title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/global.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/header.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/footer.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/homepage.css") . '?v=' . time(); ?>">
</head>

<body>
    <?= view('landing-pages/header.php') ?>

    <main class="main">
        <!-- ══ HERO ══ -->
        <section class="hp-hero">
            <img class="hp-hero__bg" src="<?= base_url("assets/images/bg.jpg") ?>" alt="Hero Background" />
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
            <div class="hp-deco hp-deco--blue" aria-hidden="true"></div>

            <!-- ══ FEATURED SYSTEMS ══ -->
            <section class="hp-featured" id="featured">
                <div class="hp-section-header">
                    <div class="hp-section-header__line"></div>
                    <h2 class="hp-section-header__title">Featured Systems</h2>
                </div>

                <div class="hp-featured__cards">
                    <!-- Akademus -->
                    <a class="hp-feat-card" href="<?= base_url('systems/akademus') ?>">
                        <img class="hp-feat-card__img" src="<?= base_url("assets/images/pillars.jpg") ?>" alt="Akademus" />
                        <div class="hp-feat-card__overlay"></div>
                        <div class="hp-feat-card__content">
                            <img class="hp-feat-card__logo" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                            <h3 class="hp-feat-card__title">Akademus</h3>
                            <p class="hp-feat-card__body">Akademus is a modern learning platform designed to provide a structured, reliable, and convenient environment where learners can access high-quality lessons, courses, and academic materials. By organizing a clear content from basic concepts to advanced topics.</p>
                        </div>
                    </a>

                    <!-- Mentora -->
                    <a class="hp-feat-card" href="<?= base_url('systems/mentora') ?>">
                        <img class="hp-feat-card__img" src="<?= base_url("assets/images/book.jpg") ?>" alt="Mentora" />
                        <div class="hp-feat-card__overlay"></div>
                        <div class="hp-feat-card__content">
                            <img class="hp-feat-card__logo" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                            <h3 class="hp-feat-card__title">Mentora</h3>
                            <p class="hp-feat-card__body">Mentora is a smart Learning Management System (LMS) designed to help teachers, and students in managing their classes. Mentora allows teachers to upload lessons, create activities and track student progress in one platform.</p>
                        </div>
                    </a>

                    <!-- Learnix -->
                    <a class="hp-feat-card" href="<?= base_url('systems/learnix') ?>">
                        <img class="hp-feat-card__img" src="<?= base_url("assets/images/comp.jpg") ?>" alt="Learnix" />
                        <div class="hp-feat-card__overlay"></div>
                        <div class="hp-feat-card__content">
                            <img class="hp-feat-card__logo" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                            <h3 class="hp-feat-card__title">Learnix</h3>
                            <p class="hp-feat-card__body">Learnix is the game-based learning portal under Mentora. It is designed to make learning more engaging, and motivating through educational games and challenges that support different subjects.</p>
                        </div>
                    </a>
                </div>
            </section>

            <!-- ══ OTHER SYSTEMS ══ -->
            <section class="hp-other" id="services">
                <div class="hp-section-header">
                    <div class="hp-section-header__line hp-section-header__line--blue"></div>
                    <h2 class="hp-section-header__title">Other Systems</h2>
                </div>

                <div class="hp-other__grid">

                    <!-- System Card -->
                    <a class="hp-sys-card" href="<?= base_url('systems/akademus') ?>">
                        <div class="hp-sys-card__line"></div>
                        <h3 class="hp-sys-card__title">Akademus</h3>
                        <p class="hp-sys-card__body">Akademus is an AI-powered school ERP that centralizes academic, administrative, and student services into one intelligent platform. It streamlines operations, automates workflows, and provides real-time insights to help institutions operate efficiently and make better decisions.</p>
                        <div class="hp-sys-card__explore">
                            <span class="hp-sys-card__explore-text">Explore</span>
                            <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                                <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.3431 14.435L19.7071 8.07107ZM0 7.36396V8.36396H19V7.36396V6.36396H0V7.36396Z" fill="white" />
                            </svg>
                            <img class="hp-sys-card__icon" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                        </div>
                    </a>

                    <!-- System Card -->
                    <a class="hp-sys-card" href="<?= base_url('systems/mentora') ?>">
                        <div class="hp-sys-card__line"></div>
                        <h3 class="hp-sys-card__title">Mentora</h3>
                        <p class="hp-sys-card__body">Mentora is a modern Learning Management System designed to help educators deliver structured lessons, manage activities, and monitor student progress. It simplifies teaching workflows while creating an engaging and organized learning experience for students.</p>
                        <div class="hp-sys-card__explore">
                            <span class="hp-sys-card__explore-text">Explore</span>
                            <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                                <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.3431 14.435L19.7071 8.07107ZM0 7.36396V8.36396H19V7.36396V6.36396H0V7.36396Z" fill="white" />
                            </svg>
                            <img class="hp-sys-card__icon" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                        </div>
                    </a>

                    <!-- System Card -->
                    <a class="hp-sys-card" href="<?= base_url('systems/learnix') ?>">
                        <div class="hp-sys-card__line"></div>
                        <h3 class="hp-sys-card__title">Learnix</h3>
                        <p class="hp-sys-card__body">Learnix is a game-based learning platform that transforms education into an interactive experience. Through challenges, rewards, and educational games, it increases student engagement while reinforcing key concepts across different subjects.</p>
                        <div class="hp-sys-card__explore">
                            <span class="hp-sys-card__explore-text">Explore</span>
                            <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                                <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.3431 14.435L19.7071 8.07107ZM0 7.36396V8.36396H19V7.36396V6.36396H0V7.36396Z" fill="white" />
                            </svg>
                            <img class="hp-sys-card__icon" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                        </div>
                    </a>

                    <!-- System Card -->
                    <a class="hp-sys-card" href="<?= base_url('systems/sis') ?>">
                        <div class="hp-sys-card__line"></div>
                        <h3 class="hp-sys-card__title">SIS</h3>
                        <p class="hp-sys-card__body">The Student Information System (SIS) manages student records, enrollment, academic history, and personal data in a secure and organized environment. It ensures accurate data tracking and easy access for administrators and faculty.</p>
                        <div class="hp-sys-card__explore">
                            <span class="hp-sys-card__explore-text">Explore</span>
                            <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                                <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.3431 14.435L19.7071 8.07107ZM0 7.36396V8.36396H19V7.36396V6.36396H0V7.36396Z" fill="white" />
                            </svg>
                            <img class="hp-sys-card__icon" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                        </div>
                    </a>

                    <!-- System Card -->
                    <a class="hp-sys-card" href="<?= base_url('systems/attendance') ?>">
                        <div class="hp-sys-card__line"></div>
                        <h3 class="hp-sys-card__title">Attendance</h3>
                        <p class="hp-sys-card__body">The Attendance System provides real-time tracking of student and staff attendance through automated and digital methods. It improves accuracy, reduces manual recording, and enables quick reporting for better monitoring and accountability.</p>
                        <div class="hp-sys-card__explore">
                            <span class="hp-sys-card__explore-text">Explore</span>
                            <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                                <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.343₁ ₁4.435₁9.707₁ 8.07₁07ZM₀ 7.36₃96V8.36₃96H₁₉V7.36₃96V6.36₃96H₀V7.36₃96Z" fill="white" />
                            </svg>
                            <img class="hp-sys-card__icon" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                        </div>
                    </a>

                    <!-- System Card -->
                    <a class="hp-sys-card" href="<?= base_url('systems/otis') ?>">
                        <div class="hp-sys-card__line"></div>
                        <h3 class="hp-sys-card__title">OTIS</h3>
                        <p class="hp-sys-card__body">OTIS is an operations and tracking system designed to manage internal workflows, tasks, and institutional processes. It enhances coordination, improves transparency, and ensures that daily operations run smoothly across departments.</p>
                        <div class="hp-sys-card__explore">
                            <span class="hp-sys-card__explore-text">Explore</span>
                            <svg width="20" height="15" viewBox="0 0 20 14.7279" fill="none" aria-hidden="true">
                                <path d="M19.7071 8.07107C20.0976 7.68054 20.0976 7.04738 19.7071 6.65685L13.3431 0.292893C12.9526 -0.097631 12.3195 -0.097631 11.9289 0.292893C11.5384 0.683418 11.5384 1.31658 11.9289 1.70711L17.5858 7.36396L11.9289 13.0208C11.5384 13.4113 11.5384 14.0445 11.9289 14.435C12.3195 14.8256 12.9526 14.8256 13.3431 14.435L19.7071 8.07107ZM0 7.36396V8.36396H19V7.36396V6.36396H0V7.36396Z" fill="white" />
                            </svg>
                            <img class="hp-sys-card__icon" src="<?= base_url("assets/images/logo_akademus.png") ?>" alt="" />
                        </div>
                    </a>

                </div>
            </section>
    </main>

    <?= view('landing-pages/footer.php') ?>

    <script src="<?= base_url("assets/js/page-animation.js") . '?v=' . time(); ?>"></script>
    <script src="<?= base_url("assets/js/mobileMenu.js") . '?v=' . time(); ?>"></script>
    <script src="<?= base_url("assets/js/page-transition-animation.js") . '?v=' . time(); ?>"></script>

</body>



</html>