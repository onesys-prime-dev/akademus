<!-- about-us.html -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | About Us</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url("assets/css/landing-pages/global.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/landing-pages/header.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/landing-pages/footer.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/landing-pages/about-us.css") . '?v=' . time(); ?>">
</head>

<body>
    <?= view('landing-pages/header.php') ?>
    <!-- HERO -->
    <section class="about-hero">

        <div class="about-hero__left">
            <!-- <p class="about-hero__small">WHO WE ARE</p> -->
            <h1 class="about-hero__title">
                Get to Know Us Better
            </h1>

            <p class="about-hero__desc">
                Learn about our commitment to excellence,
                innovation, and the principles that guide
                our work every day.
            </p>
        </div>

        <div class="about-hero__right">
            <img src="<?= base_url("assets/images/spaceship.jpg") ?>" alt="Spaceship">
        </div>

    </section>


    <!-- STORY -->
    <section class="story">

        <div class="story__card story__card--light">
            <h2>Our Story</h2>

            <p>
                Our team is composed of passionate individuals who share a common vision:
                to transform the way people learn, collaborate, and grow. By integrating technology
                with education, Akademus strives to create a community where ideas are shared,
                knowledge is expanded, and learners are empowered to reach their full potential.
            </p>
        </div>

        <div class="story__card story__card--blue">
            <h2>About Us</h2>
            <p>
                Akademus is a platform dedicated to empowering students and educators by providing accessible,
                innovative, and collaborative digital solutions for learning. Our mission is to create an environment
                where knowledge, technology, and opportunity come together to support academic growth and success.
                <br><br>
                At Akademus, we believe that education should be engaging, efficient, and inclusive. Through our
                platform,
                we aim to bridge the gap between traditional learning and modern technology by offering tools and
                resources
                that enhance the educational experience. Whether it is supporting students in their academic journey or
                helping
                educators deliver more effective learning experiences, Akademus is designed to make education smarter
                and more
                connected.
            </p>
        </div>

    </section>


    <!-- MISSION -->
    <section class="mission">

        <div class="section-title">
            <span></span>
            <h2>Mission & Vision</h2>
        </div>

        <div class="mission__grid">

            <div class="mission__box">
                <img src="<?= base_url("assets/images/mission.jpg") ?>" alt="Mission">
                <h3>Mission</h3>
                <p>
                    Our mission is to engineer precise, ethical, and intuitive systems that transform fragmented data
                    into actionable insights within the ONESYS network. By prioritizing architectural integrity and
                    intellectual empowerment, we provide the essential framework for users to navigate the landscape
                    of 2026, turning the pursuit of knowledge into a streamlined and impactful experience.
                </p>
            </div>

            <div class="mission__box">
                <img src="<?= base_url("assets/images/vision.jpg") ?>" alt="Vision">
                <h3>Vision</h3>
                <p>
                    Our vision is to become the global gold standard for unified knowledge and architectural
                    excellence within the ONESYS ecosystem. We envision a future where complex information is
                    transformed into intuitive, accessible, and masterfully structured digital experiences.
                    As we navigate the landscape of 2026, we see a world where data silos are replaced by a seamless
                    and interconnected intelligence network.
                </p>
            </div>

        </div>

    </section>


    <!-- VALUES -->
    <!-- CORE VALUES -->
    <section class="values">

        <div class="section-title section-title--white values-title">
            <span></span>
            <h2>Core Values</h2>
        </div>

        <div class="values__grid">

            <div class="value-card">
                <div class="value-card__icon">
                    <img src="<?= base_url("assets/images/lightbulb.png") ?>" alt="Lightbulb Icon">
                </div>
                <h3>Unified Intelligence</h3>
                <p>
                    We believe knowledge is most powerful when it is connected.
                    We prioritize seamless integration of data across the ONESYS network
                    to provide a single, cohesive source of truth.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <img src="<?= base_url("assets/images/lightbulb.png") ?>" alt="Lightbulb Icon">
                </div>
                <h3>Architectural Integrity</h3>
                <p>
                    We are committed to building systems that are as robust as they are elegant.
                    Every solution we develop is rooted in precise design and structural excellence.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <img src="<?= base_url("assets/images/lightbulb.png") ?>" alt="Lightbulb Icon">
                </div>
                <h3>Adaptive Innovation</h3>
                <p>
                    We remain agile. We don’t just anticipate change; we build tools that are designed
                    to evolve, ensuring our community is always at the cutting edge of innovation.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <img src="<?= base_url("assets/images/lightbulb.png") ?>" alt="Lightbulb Icon">
                </div>
                <h3>Ethical Clarity</h3>
                <p>
                    Technology must serve humanity with transparency. We maintain the highest standards
                    of data security and ethical AI, ensuring that our intelligence is always used for the
                    collective good.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <img src="<?= base_url("assets/images/lightbulb.png") ?>" alt="Lightbulb Icon">
                </div>
                <h3>Purposeful Precision</h3>
                <p>
                    We value depth over noise. Our mission is to filter out the digital chaos and
                    provide the exact insights needed to drive meaningful action and deep learning.
                </p>
            </div>

            <div class="value-card">
                <div class="value-card__icon">
                    <img src="<?= base_url("assets/images/lightbulb.png") ?>" alt="Lightbulb Icon">
                </div>
                <h3>User-Centric Intuition</h3>
                <p>
                    Technology should feel natural. We focus on creating interfaces and experiences that are intuitive
                    and human-focused, ensuring that even the most complex systems feel accessible and purposeful.
                </p>
            </div>

        </div>

    </section>

    <!-- HISTORY -->
    <section class="history">

        <div class="section-title">
            <span></span>
            <h2>History</h2>
        </div>

        <div class="history__wrap">

            <!-- Row 1 -->
            <div class="history__row">

                <div class="history__image">
                    <img src="<?= base_url("assets/images/city.jpg") ?>" alt="History Image">
                </div>

                <div class="history__content">
                    <h3>Founded in 2026</h3>
                    <p>
                        Founded in the early months of 2026 as a strategic flagship under the ONESYS umbrella, Akademus
                        was born from a vision to revolutionize how we interact with structured knowledge in an
                        increasingly
                        complex digital landscape. By integrating the robust technological infrastructure of ONESYS with
                        a commitment to intellectual depth and unified intelligence, the platform was established to
                        bridge
                        the gap between simple data retrieval and true, adaptive learning.
                    </p>
                </div>

            </div>

            <!-- Row 2 -->
            <div class="history__row history__row--reverse">

                <div class="history__image">
                    <img src="<?= base_url("assets/images/city.jpg") ?>" alt="Growth Image">
                </div>

                <div class="history__content">
                    <h3>Growing the Future</h3>
                    <p>
                        As a 2026 standard-bearer for the ONESYS ecosystem, Akademus represents a pivotal shift
                        toward a more connected and efficient future, serving as a sophisticated hub for the world’s
                        collective intelligence while maintaining the highest levels of security, innovation, and
                        ethical design from its very inception.
                    </p>
                </div>

            </div>

        </div>

    </section>

    <?= view('landing-pages/footer.php') ?>

    <script src="<?= base_url('assets/js/page-animation.js') . '?v=' . time() ?>"></script>
    <script src="<?= base_url('assets/js/mobileMenu.js') . '?v=' . time() ?>"></script>
    <script src="<?= base_url('assets/js/page-transition-animation.js') . '?v=' . time() ?>"></script>
</body>

</html>