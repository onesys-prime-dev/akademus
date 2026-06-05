<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Features</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/footer.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/features.css') ?>">
</head>

<body>
    <!-- MOBILE OVERLAY -->
    <div class="hp-overlay" id="hpOverlay">
        <div class="hp-overlay__panel">
            <button class="hp-overlay__close" id="hpOverlayClose">&#10005;</button>
            <nav class="hp-overlay__nav">
                <a href="<?= base_url('/') ?>" class="hp-overlay__link" onclick="hpCloseMenu()">Home</a>
                <a href="<?= base_url('about') ?>" class="hp-overlay__link" onclick="hpCloseMenu()">About Us</a>
                <a href="<?= base_url('features') ?>" class="hp-overlay__link" onclick="hpCloseMenu()">Features</a>
                <a href="<?= base_url('contact') ?>" class="hp-overlay__link" onclick="hpCloseMenu()">Contact</a>
            </nav>
            <a href="<?= base_url('sign-in') ?>" class="hp-overlay__cta">Get Started</a>
        </div>
    </div>

    <!-- HEADER -->
    <header class="header">
        <div class="header__inner">
            <a href="<?= base_url('/') ?>" class="header__logo">
                <img src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="Akademus Logo" class="header__logo-img">
                <span class="header__brand">AKADEMUS</span>
            </a>
            <nav class="header__nav">
                <a href="<?= base_url('/') ?>" class="header__link">Home</a>
                <a href="<?= base_url('about') ?>" class="header__link">About Us</a>
                <a href="<?= base_url('features') ?>" class="header__link header__link--active">Features</a>
                <a href="<?= base_url('contact') ?>" class="header__link">Contact</a>
            </nav>
            <a href="<?= base_url('sign-in') ?>" class="header__cta">Get Started</a>
            <button class="hamburger" id="hpHamburger" aria-label="Open menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>

    <!-- HERO -->
    <section class="features-hero">
        <div class="features-hero__left">
            <p class="features-hero__mini">SMART TOOLS FOR EDUCATION</p>
            <h1>Akademus - Modern School ERP</h1>
            <p class="features-hero__desc" id="featureDesc">
                Akademus is a fully integrated, AI-powered school ERP designed to modernize and streamline academic,
                administrative, and student services.
                <span class="more-text">
                    From intelligent scheduling and admissions automation to real-time analytics, finance integration,
                    and student support systems, Akademus delivers a seamless digital experience for institutions,
                    administrators, faculty, and students. By combining machine learning, predictive analytics, and
                    customer-centric design, it enhances efficiency, improves decision-making, and ensures a more
                    responsive and future-ready educational environment.
                </span>
            </p>
            <button class="read-more-btn" id="readMoreBtn">Read More →</button>
            <br>
            <a href="<?= base_url('sign-in') ?>" class="features-hero__btn">Get Started</a>
        </div>
        <div class="features-hero__right">
            <img src="<?= base_url('assets/images/comp.jpg') ?>" alt="Features">
        </div>
    </section>


    <!-- FEATURE GROUPS -->
    <section class="feature-groups">
        <div class="section-title">
            <span></span>
            <h2>Solutions for Everyone</h2>
        </div>
        <div class="groups-grid">
            <div class="group-card">
                <h3>Smart Information System (SIS)</h3>
                <p class="group-preview">
                    Our AI-powered Smart Information System (SIS) transforms how academic institutions manage
                    scheduling and operations. Designed to eliminate conflicts and maximize efficiency,
                    SIS intelligently generates optimized schedules by analyzing multiple critical factors.
                </p>
                <div class="group-more">
                    <p>Using advanced machine learning and rule-based intelligence, the system considers:</p>
                    <ul>
                        <li>- Historical scheduling data</li>
                        <li>- Faculty specialization and teaching loads</li>
                        <li>- Student section availability</li>
                        <li>- Room capacity and availability</li>
                        <li>- Faculty preferences and availability</li>
                        <li>- Curriculum-based course requirements</li>
                    </ul>
                    <p>The result is a conflict-free, data-driven schedule that adapts to the institution's unique
                        needs—saving time, reducing manual errors, and improving overall academic operations.</p>
                </div>
            </div>
            <div class="group-card">
                <h3>Customer-Centric Admissions</h3>
                <p class="group-preview">
                    Delivering a seamless and modern admissions experience, our platform is built with applications in mind.
                </p>
                <div class="group-more">
                    <p>Key Features</p>
                    <ul>
                        <li>AI-Powered Companion<br>- Applicants can interact with an intelligent assistant to ask questions, receive guidance, and track their application status—including missing requirements.</li>
                        <li>Online exam scheduling<br>- Easily book and manage entrance exam schedules through an intuitive online interface.</li>
                        <li>Fast-Track Admissions Option<br>- For applicants who need expedited assistance, a premium fast-track service is available.</li>
                        <li>Auto-Save &amp; Sync Technology<br>- No need to worry about interruptions. Similar to cloud-based tools like Google Docs, all data entries and uploads are automatically saved and synchronized.</li>
                        <li>AI-Based Workload Distribution<br>- Applications are dynamically assigned to admissions officers using intelligent workload balancing.</li>
                        <li>Applicant Dashboard<br>- Each applicant receives a personal account with a centralized dashboard to monitor progress, upload documents, and manage their application in real time.</li>
                        <li>Real-Time Notifications<br>- Stay informed with instant updates via email and SMS for status changes, reminders, and important communications.</li>
                    </ul>
                </div>
            </div>
            <div class="group-card">
                <h3>Administrator Management System</h3>
                <p class="group-preview">
                    Empower administrators with tools designed for efficiency, visibility, and data-driven decision-making.
                </p>
                <div class="group-more">
                    <ul>
                        <li>- Modern Custom Dashboards for real-time monitoring of departments and units</li>
                        <li>- Advanced Data Analytics with intuitive visualizations and actionable insights</li>
                        <li>- Real-Time Alerts &amp; Notifications to track transactions and system activities</li>
                        <li>- Predictive Analytics to forecast trends, monitor deadlines, and anticipate critical needs</li>
                    </ul>
                </div>
            </div>
            <div class="group-card">
                <h3>Integrated Accounting &amp; Finance System</h3>
                <p class="group-preview">
                    A key differentiator of our platform is its integration with Ecovis, a globally recognized accounting and auditing firm.
                </p>
                <div class="group-more">
                    <ul>
                        <li>- Seamless alignment with professional financial and audit standards</li>
                        <li>- Trusted methodologies used by organizations worldwide</li>
                        <li>- Enhanced transparency, compliance, and financial accuracy</li>
                    </ul>
                    <p>This makes it one of the few academic systems with enterprise-grade financial integration.</p>
                </div>
            </div>
            <div class="group-card">
                <h3>Modern Gate &amp; Attendance System</h3>
                <p class="group-preview">
                    Leverage cutting-edge technologies to enhance campus security and attendance monitoring.
                </p>
                <div class="group-more">
                    <ul>
                        <li>- AI &amp; Image Recognition for automated gate entry and attendance tracking</li>
                        <li>- Uniform Compliance Monitoring to support institutional policies</li>
                        <li>- Integration with Ecovis Payroll System, trusted by leading organizations locally and globally</li>
                    </ul>
                </div>
            </div>
            <div class="group-card">
                <h3>Gradebook &amp; Academic Management</h3>
                <p class="group-preview">
                    More than just a grading system, Akademus is a comprehensive academic companion
                    designed to support students throughout their journey—and beyond.
                </p>
                <div class="group-more">
                    <p>What It Offers:</p>
                    <ul>
                        <li>- Real-Time Academic Tracking of grades and performance</li>
                        <li>- Personalized Academic Advising to guide course decisions and progress</li>
                        <li>- Integrated Learning Support through Mentora</li>
                        <li>- Student Well-Being Services, including guidance and counseling support</li>
                    </ul>
                    <p>Akademus ensures that students receive holistic support—academically,
                        personally, and professionally—from enrollment to graduation and beyond.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- FOOTER -->
    <footer class="footer" id="contact">
        <div class="footer__inner">
            <div class="footer__brand">
                <div class="footer__logo-wrap">
                    <img class="footer__logo-img" src="<?= base_url('assets/images/logo_akademus.png') ?>" alt="Akademus Logo">
                    <span class="footer__logo-name">Akademus</span>
                </div>
                <p class="footer__desc">Akademus is a modern learning platform that provides structured and reliable access to courses and academic resources.</p>
            </div>
            <div class="footer__col">
                <h4 class="footer__col-title">Resources</h4>
                <a href="<?= base_url('about') ?>" class="footer__col-link">About</a>
                <a href="#" class="footer__col-link">Career</a>
                <a href="<?= base_url('contact') ?>" class="footer__col-link">Contact</a>
                <a href="#" class="footer__col-link">Partners</a>
            </div>
            <div class="footer__col">
                <h4 class="footer__col-title">Product</h4>
                <a href="#" class="footer__col-link">Akademus</a>
                <a href="#" class="footer__col-link">Mentora</a>
                <a href="#" class="footer__col-link">Learnix</a>
                <a href="#" class="footer__col-link">OTIS</a>
            </div>
            <div class="footer__col">
                <h4 class="footer__col-title">Company</h4>
                <a href="<?= base_url('about') ?>" class="footer__col-link">About</a>
                <a href="#" class="footer__col-link">Career</a>
                <a href="<?= base_url('contact') ?>" class="footer__col-link">Contact</a>
                <a href="#" class="footer__col-link">Partners</a>
            </div>
        </div>
        <div class="footer__divider"></div>
        <div class="footer__bottom">
            <p class="footer__copy">2026 Akademus. All rights reserved.</p>
            <div class="footer__legal">
                <a href="#" class="footer__legal-link">Privacy Policy</a>
                <a href="#" class="footer__legal-link">Terms of Service</a>
                <a href="#" class="footer__legal-link">Cookie Settings</a>
            </div>
        </div>
    </footer>

</body>

<script src="<?= base_url('assets/js/mobileMenu.js') ?>"></script>
<script src="<?= base_url('assets/js/page-animation.js') ?>"></script>
<script src="<?= base_url('assets/js/page-transition-animation.js') ?>"></script>
<script>
    const readMoreBtn = document.getElementById("readMoreBtn");
    const featureDesc = document.getElementById("featureDesc");

    readMoreBtn.addEventListener("click", function () {
        featureDesc.classList.toggle("expanded");
        readMoreBtn.innerHTML = featureDesc.classList.contains("expanded") ? "Read Less ↑" : "Read More →";
    });

    document.querySelectorAll(".group-card").forEach(card => {
        const btn = document.createElement("button");
        btn.className = "expand-btn";
        btn.innerHTML = "Learn More →";
        card.appendChild(btn);
        btn.addEventListener("click", () => {
            card.classList.toggle("open");
            btn.innerHTML = card.classList.contains("open") ? "Show Less ↑" : "Learn More →";
        });
    });
</script>

</html>
