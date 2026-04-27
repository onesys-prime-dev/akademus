<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Features</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/global.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/header.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/footer.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/features.css") . '?v=' . time(); ?>">
</head>

<body>
    <?= view('landing-pages/header.php') ?>

    <!-- HERO -->
    <section class="features-hero">

        <div class="features-hero__left">
            <p class="features-hero__mini">SMART TOOLS FOR EDUCATION</p>

            <h1>
                Akademus - Modern School ERP
            </h1>

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

            <button class="read-more-btn" id="readMoreBtn">
                Read More →
            </button>
            <br>
            <a href="https://testing.srv1404227.hstgr.cloud/sign-in" class="features-hero__btn">Get Started</a>
        </div>

        <div class="features-hero__right">
            <img src=<?= base_url("assets/images/comp.jpg") ?>>
        </div>

    </section>


    <!-- CATEGORY -->
    <section class="feature-groups">

        <div class="section-title">
            <span></span>
            <h2>Solutions for Everyone</h2>
        </div>

        <div class="groups-grid">

            <div class="group-card">
                <div class="group-card__top">
                    <h3>Student Information System (SIS)</h3>
                    <span class="group-card__icon">></span>
                </div>

                <div class="group-card__content">
                    <p>
                        Our <strong>AI-powered Smart Information System (SIS)</strong> transforms how academic
                        institutions manage
                        scheduling and operations. Designed to eliminate conflicts and maximize efficiency, SIS
                        intelligently generates optimized schedules by analyzing multiple critical factors.
                        <br><br>
                        Using advanced machine learning and rule-based intelligence, the system considers:
                    </p>

                    <ul>
                        <li>- Historical scheduling data</li>
                        <li>- Faculty specialization and teaching loads</li>
                        <li>- Student section availability</li>
                        <li>- Room capacity and availability</li>
                        <li>- Faculty preferences and availability</li>
                        <li>- Curriculum-based course requirements</li>
                    </ul>

                    <p>
                        The result is a <strong>conflict-free, data-driven</strong> schedule that adapts to the
                        institution’s
                        unique needs—saving time, reducing manual errors, and improving overall academic operations.
                    </p>
                </div>

            </div>
            <div class="group-card">
                <div class="group-card__top">
                    <h3>Customer-Centric Admissions System</h3>
                    <span class="group-card__icon">></span>
                </div>

                <div class="group-card__content">
                    <p>
                        Delivering a seamless and modern admissions
                        experience, our platform is built with applicants in mind.
                        <br><br>
                        Key Features:
                    </p>

                    <ul>
                        <li><strong>AI-Powered Companion</strong><br>- Applicants can interact with an intelligent
                            assistant to ask questions, receive guidance, and track their application status—including
                            missing requirements.</li>
                        <li><strong>Online Exam Scheduling</strong><br>- Easily book and manage entrance exam schedules
                            through an intuitive online interface.</li>
                        <li><strong>Fast-Track Admissions Option</strong><br>- For applicants who need expedited
                            assistance, a premium fast-track service is available. Applicants can submit basic details
                            and partial documents, and a dedicated admissions officer will provide personalized support
                            to complete the process.</li>
                        <li><strong>Auto-Save & Sync Technology</strong><br>- No need to worry about interruptions.
                            Similar to cloud-based tools like Google Docs, all data entries and uploads are
                            automatically saved and synchronized—even with slow or unstable internet connections.</li>
                        <li><strong>AI-Based Workload Distribution</strong><br>- Applications are dynamically assigned
                            to admissions officers using intelligent workload balancing, ensuring faster processing and
                            better service.</li>
                        <li><strong>Applicant Dashboard</strong><br>- Each applicant receives a personal account with a
                            centralized dashboard to monitor progress, upload documents, and manage their application in
                            real time.</li>
                        <li><strong>Real-Time Notifications</strong><br>- Stay informed with instant updates via email
                            and SMS for status changes, reminders, and important communications</li>
                    </ul>
                </div>

            </div>
            <div class="group-card">
                <div class="group-card__top">
                    <h3>Administrator-Focused Management System</h3>
                    <span class="group-card__icon">></span>
                </div>

                <div class="group-card__content">
                    <p>
                        Empower administrators with tools designed for
                        efficiency, visibility, and data-driven decision-making.
                    </p>

                    <ul>
                        <li>- <strong>Modern Custom Dashboards</strong> for real-time monitoring of departments and
                            units</li>
                        <li>- <strong>Advanced Data Analytics</strong> with intuitive visualizations and actionable
                            insights</li>
                        <li>- <strong>Real-Time Alerts & Notifications</strong> to track transactions and system
                            activities</li>
                        <li>- <strong>Predictive Analytics</strong> to forecast trends, monitor deadlines, and
                            anticipate critical needs
                        </li>
                    </ul>
                </div>

            </div>
            <div class="group-card">
                <div class="group-card__top">
                    <h3>Integrated Accounting & Finance System</h3>
                    <span class="group-card__icon">></span>
                </div>

                <div class="group-card__content">
                    <p>
                        A key differentiator of our platform is its integration with
                        <strong>Ecovis</strong>, a globally recognized accounting and auditing firm.
                    </p>

                    <ul>
                        <li>- Seamless alignment with professional financial and audit standards</li>
                        <li>- Trusted methodologies used by organizations worldwide</li>
                        <li>- Enhanced transparency, compliance, and financial accuracy</li>
                    </ul>

                    <p>
                        This makes it one of the <strong>few academic systems with enterprise-grade financial
                            integration.</strong>
                    </p>
                </div>

            </div>
            <div class="group-card">
                <div class="group-card__top">
                    <h3>Modern Gate & Attendance System</h3>
                    <span class="group-card__icon">></span>
                </div>

                <div class="group-card__content">
                    <p>
                        Leverage cutting-edge technologies to enhance campus security and attendance monitoring.
                    </p>

                    <ul>
                        <li>- <strong>AI & Image Recognition</strong> for automated gate entry and attendance tracking
                        </li>
                        <li>- <strong></strong>Uniform Compliance Monitoring</strong> to support institutional policies
                        </li>
                        <li>- <strong>Integration with Ecovis Payroll System</strong>, trusted by leading organizations
                            locally and
                            globally</li>
                    </ul>
                </div>

            </div>

            <div class="group-card">
                <div class="group-card__top">
                    <h3>Gradebook & Academic Management (Akademus)</h3>
                    <span class="group-card__icon">></span>
                </div>

                <div class="group-card__content">
                    <p>
                        More than just a grading system, <strong>Akademus</strong> is a comprehensive academic
                        companion designed to support students throughout their journey—and beyond.
                    </p>

                    <ul>
                        <li>- <strong>Real-Time Academic Tracking</strong> of grades and performance</li>
                        <li>- <strong>Personalized Academic Advising</strong> to guide course decisions and progress
                        </li>
                        <li>- <strong>Integrated Learning Support</strong> through Mentora</li>
                        <li>- <strong>Student Well-Being Services</strong>, including guidance and counseling support
                        </li>
                    </ul>

                    <p>
                        Akademus ensures that students receive <strong>holistic support—academically, personally, and
                            professionally</strong>—from enrollment to graduation and beyond. </p>
                </div>

            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="feature-cta">

        <h2>Ready to Transform Education?</h2>

        <p>
            Join Akademus and empower learning through technology.
        </p>

        <a href="https://testing.srv1404227.hstgr.cloud/sign-in" class="feature-cta__btn">Get Started</a>

    </section>


    <?= view('landing-pages/footer.php') ?>

    <script src="<?= base_url('assets/js/mobileMenu.js') . '?v=' . time() ?>"></script>
    <script src="<?= base_url('assets/js/page-animation.js') . '?v=' . time() ?>"></script>
    <script src="<?= base_url('assets/js/page-transition-animation.js') . '?v=' . time() ?>"></script>
    <script>
        const btn = document.getElementById("readMoreBtn");
        const desc = document.getElementById("featureDesc");

        btn.addEventListener("click", function() {

            desc.classList.toggle("expanded");

            if (desc.classList.contains("expanded")) {
                btn.innerHTML = "Read Less ↑";
            } else {
                btn.innerHTML = "Read More →";
            }

        });

        document.querySelectorAll(".group-card").forEach(card => {

            card.addEventListener("click", function() {

                if (window.innerWidth <= 768) {
                    this.classList.toggle("open");
                }

            });

        });
    </script>

</body>


</html>