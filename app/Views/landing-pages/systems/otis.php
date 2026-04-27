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
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/systems/otis.css") . '?v=' . time(); ?>">
</head>
<body>
    <?= view('landing-pages/header.php') ?>

    <main class="main">
        <section class="system-hero">
            <img class="system-hero__bg" src="<?= base_url("assets/images/systems/otis/bg.png") ?>" alt="Hero Background" />
        </section>

        <section class="system-content">

            <div class="system-content__banner"></div>

            <div class="system-content__overlay">
                <img class="system-content__overlay-img" src="<?= base_url("assets/images/systems/otis/1.png") ?>" alt="Content Overlay" />
            </div>

            <div class="system-content__info">
                <div class="system-content__col">
                    
                    <div class="system-content__intro">
                        <span class="system-content__category">Systems</span>

                        <div class="system-content__heading">
                            <span class="system-content__kicker">Step Into</span>
                            <span class="system-content__title">OTIS</span>
                        </div>

                        <p class="system-content__description">
                            A unified transaction system delivering accurate, compliant, and enterprise-grade financial management for institutions.
                        </p>
                    </div>

                    <div class="system-content__paragraph">
                        OTIS, or the OneSys Transaction Integrated System, is a comprehensive platform designed to unify and streamline institutional transactions across academic and administrative operations. It centralizes financial processes, ensuring that data flows seamlessly between departments while maintaining accuracy and consistency. By integrating enterprise-grade financial standards, OTIS provides institutions with a reliable system for managing transactions, reporting, and compliance. Its connection with Ecovis, a globally recognized accounting and auditing firm, strengthens its credibility and ensures adherence to international practices. This combination of integration, accuracy, and transparency makes OTIS a powerful solution for institutions seeking efficient and modern financial management systems.
                    </div>

                    <div class="system-content__paragraph">
                        OTIS stands out through its deep integration with Ecovis, enabling institutions to align their financial processes with globally recognized accounting standards. This integration ensures that all transactions are recorded, processed, and reported using trusted methodologies. Institutions benefit from a system that supports accurate bookkeeping, auditing readiness, and regulatory compliance. By embedding professional financial practices into its core, OTIS eliminates inconsistencies and reduces the risk of errors. This seamless integration allows administrators to manage finances with confidence, knowing that their systems are aligned with industry standards used by organizations worldwide.
                    </div>

                </div>

                <div class="system-content__col">

                    <div class="system-content__img">
                        <img class="system-content__img-img" src="<?= base_url("assets/images/systems/otis/2.png") ?>" alt="">
                    </div>

                    <div class="system-content__paragraph system-content__paragraph--right">
                        OTIS provides a structured and efficient approach to transaction management by centralizing all financial activities within a single system. Payments, fees, and financial records are processed in real time, ensuring accuracy and transparency. The system reduces reliance on manual processes, minimizing errors and improving efficiency. Administrators can easily track transactions, generate reports, and monitor financial performance. By consolidating all financial data, OTIS ensures consistency across departments. This centralized approach simplifies operations and allows institutions to maintain a clear and organized financial structure.
                    </div>

                    <div class="system-content__col-img-container">
                        <img class="system-content__col-img" src="<?= base_url("assets/images/systems/otis/3.png") ?>" alt="">
                    </div>

                </div>
            </div>

            <div class="system-content__info">
                <div class="system-content__col">
                    
                    <div class="system-content__intro">
                        <div class="system-content__heading">
                            <span class="system-content__title">Transparency and Compliance</span>
                        </div>
                    </div>

                    <div class="system-content__break">
                        <div class="system-content__paragraph">
                            OTIS enhances transparency and compliance by ensuring that all financial transactions are recorded and accessible in a clear and organized manner. Its integration with professional auditing standards allows institutions to maintain accurate and verifiable records. Administrators can generate detailed reports that support internal reviews and external audits. This level of transparency builds trust and ensures accountability across all financial operations. By aligning with global standards, OTIS helps institutions meet regulatory requirements while maintaining consistency and reliability in their financial processes.
                        </div>

                        <div class="system-content__break--line"></div>
                    </div>

                    <div class="system-content__col-container-img">
                        <div class="system-content__col-img-container system-content__col-img-container--break">
                            <img class="system-content__col-img" src="<?= base_url("assets/images/systems/otis/4.png") ?>" alt="">
                        </div>

                        <div class="system-content__col-container--overlay-1">
                            <div class="system-content__col-feature-card--overlay">
                                <h4 class="system-content__col-feature-card-title">Financial Integration</h4>
                                <p class="system-content__col-feature-card-desc">
                                    Seamlessly connects with Ecovis for accurate, compliant, and globally aligned financial processes.
                                </p>
                            </div>

                            <div class="system-content__col-container--overlay-2">
                                <div class="system-content__col-feature-card--overlay">
                                    <h4 class="system-content__col-feature-card-title">Transaction Management</h4>
                                    <p class="system-content__col-feature-card-desc">
                                        Centralizes institutional transactions, ensuring transparency, accuracy, and efficient financial record handling.
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
                        <span class="system-content__title">Accuracy and Reliability</span>
                    </div>
                </div>

                <div class="system-content__break--3">
                    <div class="system-content__break--line--3"></div>

                    <div class="system-content__paragraph system-content__paragraph--3">
                        OTIS is designed to deliver high levels of accuracy and reliability in financial operations. By automating transaction processes and integrating with established accounting frameworks, the system reduces the likelihood of human error. Real-time data processing ensures that financial information is always up to date and consistent. This accuracy is essential for effective decision-making and long-term planning. Institutions can rely on OTIS to provide dependable financial data that supports operational efficiency and strategic growth. Its robust design ensures that financial processes remain stable and reliable over time.
                    </div>
                </div>

                <div class="system-content__img-container--3">
                    <div class="system-content__img--3--left--overlay">
                        <img class="system-content__img--3--left" src="<?= base_url("assets/images/systems/otis/5.png") ?>" alt="">
                    </div>
                    <div class="system-content__img--3--right--overlay">
                        <img class="system-content__img--3--right" src="<?= base_url("assets/images/systems/otis/6.png") ?>" alt="">
                    </div>
                </div>

                <div class="system-content__paragraph system-content__paragraph--4">
                    OTIS brings enterprise-grade financial capabilities into the academic environment, bridging the gap between education systems and professional financial management. Its integration with Ecovis ensures that institutions operate using methodologies trusted by global organizations. This level of capability allows institutions to handle complex financial processes with ease and confidence. By adopting enterprise-level standards, OTIS elevates institutional operations and ensures that financial systems are scalable and future-ready. This makes it a unique solution among academic platforms, offering advanced functionality typically found in large-scale enterprise systems.
                </div>

                <div class="system-content__paragraph system-content__paragraph--4">
                    OTIS is built to support the future of institutional financial management through scalability and continuous innovation. Its flexible architecture allows for integration with additional systems and technologies as institutional needs evolve. By combining automation, data accuracy, and global financial standards, OTIS ensures long-term sustainability and efficiency. Institutions can adapt to changing requirements without compromising performance or reliability. This forward-thinking design positions OTIS as a future-ready solution that supports growth, enhances operational efficiency, and maintains alignment with global financial practices.
                </div>
            </div>
        </section>
    </main>

</body>

<script src="<?= base_url('assets/js/mobileMenu.js') . '?v=' . time() ?>"></script>
<script src="<?= base_url('assets/js/page-transition-animation.js') . '?v=' . time() ?>"></script>

</html>