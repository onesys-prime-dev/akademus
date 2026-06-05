<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/global.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/header.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/footer.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/systems/attendance.css") . '?v=' . time(); ?>">
</head>
<body>
    <?= view('landing-pages/header.php') ?>

    <main class="main">
        <section class="system-hero">
            <img class="system-hero__bg" src="<?= base_url("assets/images/systems/attendance/bg.png") ?>" alt="Hero Background" />
        </section>

        <section class="system-content">

            <div class="system-content__banner"></div>

            <div class="system-content__overlay">
                <img class="system-content__overlay-img" src="<?= base_url("assets/images/systems/attendance/1.png") ?>" alt="Content Overlay" />
            </div>

            <div class="system-content__info">
                <div class="system-content__col">
                    
                    <div class="system-content__intro">
                        <span class="system-content__category">Systems</span>

                        <div class="system-content__heading">
                            <span class="system-content__kicker">Step Into</span>
                            <span class="system-content__title">Gate and Attendance System</span>
                        </div>

                        <p class="system-content__description">
                            An automated attendance and access system using AI to enhance security, accuracy, and real-time monitoring.
                        </p>
                    </div>

                    <div class="system-content__paragraph">
                        The Modern Gate and Attendance System is an advanced solution designed to enhance campus security and streamline attendance monitoring. By leveraging AI and image recognition technologies, it automates entry verification and records attendance in real time. This eliminates manual processes and reduces the risk of inaccuracies. The system ensures that only authorized individuals can access campus facilities, improving safety and control. Institutions benefit from faster entry processes, reduced administrative workload, and accurate data collection. With its intelligent automation and seamless integration capabilities, the system provides a reliable and efficient approach to managing both security and attendance in modern educational environments.
                    </div>

                    <div class="system-content__paragraph">
                        The system utilizes AI-powered image recognition to automate gate entry and identity verification. Cameras capture and analyze facial data, allowing authorized students and staff to enter without the need for physical IDs or manual checks. This process ensures speed and accuracy while maintaining high security standards. The system can detect unauthorized access attempts and alert administrators immediately. By replacing traditional gatekeeping methods, it reduces human error and enhances operational efficiency. This intelligent entry system creates a seamless experience for users while ensuring that campus security remains a top priority at all times.
                    </div>

                </div>

                <div class="system-content__col">

                    <div class="system-content__img">
                        <img class="system-content__img-img" src="<?= base_url("assets/images/systems/attendance/2.jpg") ?>" alt="">
                    </div>

                    <div class="system-content__paragraph system-content__paragraph--right">
                        The Modern Gate and Attendance System automates attendance tracking by linking entry data directly to student and staff records. As individuals pass through the gate, their attendance is recorded instantly without additional input. This eliminates the need for manual roll calls and reduces administrative workload. The system ensures accurate and consistent attendance data, which can be accessed in real time. Educators and administrators can monitor attendance patterns and identify issues quickly. This automated approach improves efficiency and ensures that attendance tracking is reliable, transparent, and easy to manage across the institution.
                    </div>

                    <div class="system-content__col-img-container">
                        <img class="system-content__col-img" src="<?= base_url("assets/images/systems/attendance/3.png") ?>" alt="">
                    </div>

                </div>
            </div>

            <div class="system-content__info">
                <div class="system-content__col">
                    
                    <div class="system-content__intro">
                        <div class="system-content__heading">
                            <span class="system-content__title">Uniform Compliance Monitoring</span>
                        </div>
                    </div>

                    <div class="system-content__break">
                        <div class="system-content__paragraph">
                            The system includes uniform compliance monitoring to support institutional policies and maintain discipline. Using image recognition, it can detect whether students meet required dress code standards during entry. Non-compliance can trigger alerts or notifications for further action. This feature helps institutions enforce rules consistently without requiring additional personnel. By integrating compliance checks into the entry process, the system ensures that policies are upheld efficiently. This automated monitoring not only saves time but also reinforces a structured and professional environment within the campus.
                        </div>

                        <div class="system-content__break--line"></div>
                    </div>

                    <div class="system-content__col-container-img">
                        <div class="system-content__col-img-container system-content__col-img-container--break">
                            <img class="system-content__col-img" src="<?= base_url("assets/images/systems/attendance/4.jpg") ?>" alt="">
                        </div>

                        <div class="system-content__col-container--overlay-1">
                            <div class="system-content__col-feature-card--overlay">
                                <h4 class="system-content__col-feature-card-title">AI-Powered Attendance Tracking</h4>
                                <p class="system-content__col-feature-card-desc">
                                    Automatically records attendance using facial recognition for fast, accurate, and secure monitoring.
                                </p>
                            </div>

                            <div class="system-content__col-container--overlay-2">
                                <div class="system-content__col-feature-card--overlay">
                                    <h4 class="system-content__col-feature-card-title">Smart Access Control</h4>
                                    <p class="system-content__col-feature-card-desc">
                                        Controls campus entry by verifying identity and ensuring only authorized individuals access.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    
                </div>
            </div>

            <div class="system-content__info system-content__info--3">
                <div class="system-content__intro system-content__intro--3">
                    <div class="system-content__heading">
                        <span class="system-content__title">Integration with Payroll Systems</span>
                    </div>
                </div>

                <div class="system-content__break--3">
                    <div class="system-content__break--line--3"></div>

                    <div class="system-content__paragraph system-content__paragraph--3">
                        The Modern Gate and Attendance System integrates seamlessly with payroll platforms such as Ecovis, enabling accurate tracking of staff attendance and working hours. Attendance data is automatically synchronized, reducing the need for manual input and minimizing errors. This integration ensures that payroll calculations are based on precise and verified records. Organizations benefit from improved efficiency and reduced administrative overhead. By connecting attendance tracking with payroll systems, the platform creates a unified workflow that enhances accuracy and simplifies operations for institutions and organizations alike.
                    </div>
                </div>

                <div class="system-content__img-container--3">
                    <div class="system-content__img--3--left--overlay">
                        <img class="system-content__img--3--left" src="<?= base_url("assets/images/systems/attendance/5.jpg") ?>" alt="">
                    </div>
                    <div class="system-content__img--3--right--overlay">
                        <img class="system-content__img--3--right" src="<?= base_url("assets/images/systems/attendance/6.jpg") ?>" alt="">
                    </div>
                </div>

                <div class="system-content__paragraph system-content__paragraph--4">
                    The system provides real-time monitoring and analytics that give administrators valuable insights into campus activity. Dashboards display entry logs, attendance trends, and security alerts in an easy-to-understand format. This allows institutions to identify patterns, detect anomalies, and make informed decisions. Real-time data ensures that administrators can respond quickly to any issues. By combining monitoring with analytics, the system enhances both security and operational efficiency. This data-driven approach supports proactive management and continuous improvement in campus operations.
                </div>

                <div class="system-content__paragraph system-content__paragraph--4">
                    The Modern Gate and Attendance System is designed to meet the evolving needs of modern institutions through scalability and innovation. Its flexible architecture allows for integration with emerging technologies and future system enhancements. As institutions grow, the system can adapt to increased demand without compromising performance. Continuous updates ensure that it remains aligned with the latest security standards and technological advancements. By combining automation, intelligence, and adaptability, the system provides a future-ready solution for campus security and attendance management, ensuring long-term reliability and efficiency.
                </div>
            </div>
        </section>
    </main>

</body>

<script src="<?= base_url('assets/js/mobileMenu.js') . '?v=' . time() ?>"></script>
<script src="<?= base_url('assets/js/page-transition-animation.js') . '?v=' . time() ?>"></script>

</html>