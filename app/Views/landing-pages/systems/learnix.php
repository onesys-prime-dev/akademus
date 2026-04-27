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
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/systems/learnix.css") . '?v=' . time(); ?>">
</head>
<body>
    <?= view('landing-pages/header.php') ?>

    <main class="main">
        <section class="system-hero">
            <img class="system-hero__bg" src="<?= base_url("assets/images/systems/learnix/bg.png") ?>" alt="Hero Background" />
        </section>

        <section class="system-content">

            <div class="system-content__banner"></div>

            <div class="system-content__overlay">
                <img class="system-content__overlay-img" src="<?= base_url("assets/images/systems/learnix/1.png") ?>" alt="Content Overlay" />
            </div>

            <div class="system-content__info">
                <div class="system-content__col">
                    
                    <div class="system-content__intro">
                        <span class="system-content__category">Systems</span>

                        <div class="system-content__heading">
                            <span class="system-content__kicker">Step Into</span>
                            <span class="system-content__title">Learnix</span>
                        </div>

                        <p class="system-content__description">
                            A gamified learning platform that boosts student engagement through interactive challenges, rewards, and integrated educational experiences.
                        </p>
                    </div>

                    <div class="system-content__paragraph">
                        Learnix is a gamified learning system designed to enhance student engagement by transforming traditional lessons into interactive and rewarding experiences. Integrated seamlessly with Mentora, it adds a dynamic layer to structured learning by incorporating challenges, achievements, and progress-based rewards. Students are motivated to participate actively, while educators gain tools to encourage consistent engagement. By blending education with game mechanics, Learnix creates an environment where learning feels less like a task and more like an experience. This approach helps maintain student interest, improves retention, and promotes continuous participation, making it an essential extension of modern digital education platforms.
                    </div>

                    <div class="system-content__paragraph">
                        Learnix introduces game mechanics such as points, levels, and achievements to make learning more interactive and enjoyable. Students earn rewards as they complete lessons, participate in activities, and achieve milestones within their courses. These elements create a sense of accomplishment and progression, encouraging learners to stay consistent and motivated. Leaderboards and challenges foster friendly competition, pushing students to improve their performance while staying engaged. By turning educational tasks into rewarding experiences, Learnix helps reduce boredom and increases participation. This gamified approach ensures that students remain focused and invested in their learning journey over time.
                    </div>

                </div>

                <div class="system-content__col">

                    <div class="system-content__img">
                        <img class="system-content__img-img" src="<?= base_url("assets/images/systems/learnix/2.jpg") ?>" alt="">
                    </div>

                    <div class="system-content__paragraph system-content__paragraph--right">
                        Learnix is deeply integrated with Mentora, allowing it to enhance existing learning workflows without disrupting the core system. Lessons, activities, and assessments from Mentora can be transformed into gamified experiences within Learnix. This seamless connection ensures that all progress, achievements, and performance data remain synchronized across both platforms. Educators can easily incorporate gamification into their teaching strategies without additional complexity. Students benefit from a unified experience where learning and engagement are interconnected. By working alongside Mentora, Learnix strengthens the overall educational ecosystem and provides a more engaging and cohesive learning environment.
                    </div>

                    <div class="system-content__col-img-container">
                        <img class="system-content__col-img" src="<?= base_url("assets/images/systems/learnix/3.jpg") ?>" alt="">
                    </div>

                </div>
            </div>

            <div class="system-content__info">
                <div class="system-content__col">
                    
                    <div class="system-content__intro">
                        <div class="system-content__heading">
                            <span class="system-content__title">Student Motivation</span>
                        </div>
                    </div>

                    <div class="system-content__break">
                        <div class="system-content__paragraph">
                            Learnix is designed to boost student motivation by rewarding effort, consistency, and achievement. Through badges, streaks, and progress tracking, students receive immediate feedback that reinforces positive learning behaviors. This system encourages them to complete tasks, revisit lessons, and strive for improvement. The sense of progression keeps learners engaged and helps build confidence as they see their accomplishments grow over time. By making learning interactive and rewarding, Learnix transforms passive participation into active involvement. This motivational structure is especially effective in maintaining long-term engagement and ensuring that students remain committed to their educational goals.
                        </div>

                        <div class="system-content__break--line"></div>
                    </div>

                    <div class="system-content__col-container-img">
                        <div class="system-content__col-img-container system-content__col-img-container--break">
                            <img class="system-content__col-img" src="<?= base_url("assets/images/systems/learnix/4.jpg") ?>" alt="">
                        </div>

                        <div class="system-content__col-container--overlay-1">
                            <div class="system-content__col-feature-card--overlay">
                                <h4 class="system-content__col-feature-card-title">Gamified Learning Experience</h4>
                                <p class="system-content__col-feature-card-desc">
                                    Engages students through points, challenges, and rewards to enhance motivation and participation.
                                </p>
                            </div>

                            <div class="system-content__col-container--overlay-2">
                                <div class="system-content__col-feature-card--overlay">
                                    <h4 class="system-content__col-feature-card-title">Seamless Mentora Integration</h4>
                                    <p class="system-content__col-feature-card-desc">
                                        Integrates with Mentora to transform lessons into interactive, engaging, and trackable learning experiences.
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
                        <span class="system-content__title">Personalized Engagement</span>
                    </div>
                </div>

                <div class="system-content__break--3">
                    <div class="system-content__break--line--3"></div>

                    <div class="system-content__paragraph system-content__paragraph--3">
                        Learnix supports personalized engagement by adapting challenges and rewards based on student performance and activity. Learners can progress at their own pace while still being encouraged to improve through tailored tasks and milestones. This flexibility ensures that both advanced and struggling students remain engaged without feeling overwhelmed or unchallenged. Educators can monitor participation and adjust gamified elements to suit different learning needs. By aligning engagement strategies with individual progress, Learnix creates a balanced and inclusive environment. This personalization helps maximize student involvement and ensures that every learner benefits from the system.
                    </div>
                </div>

                <div class="system-content__img-container--3">
                    <div class="system-content__img--3--left--overlay">
                        <img class="system-content__img--3--left" src="<?= base_url("assets/images/systems/learnix/5.jpg") ?>" alt="">
                    </div>
                    <div class="system-content__img--3--right--overlay">
                        <img class="system-content__img--3--right" src="<?= base_url("assets/images/systems/learnix/6.jpg") ?>" alt="">
                    </div>
                </div>

                <div class="system-content__paragraph system-content__paragraph--4">
                    Learnix fosters both collaboration and healthy competition among students through team-based challenges and leaderboards. Students can work together to achieve shared goals while also competing individually for rewards and recognition. This balance encourages social interaction, teamwork, and communication, which are essential skills in modern education. Group activities promote knowledge sharing, while competitive elements push students to improve their performance. By combining these dynamics, Learnix creates a vibrant and interactive learning community. This approach not only enhances engagement but also builds a supportive and motivating environment for learners.
                </div>

                <div class="system-content__paragraph system-content__paragraph--4">
                    Learnix represents the future of gamified learning by combining innovation, engagement, and adaptability. Its scalable design allows institutions to expand features and integrate new technologies as educational needs evolve. With the potential for AI-driven personalization and advanced analytics, Learnix can continuously improve the learning experience. It empowers educators to create more engaging lessons while helping students stay motivated and focused. As education becomes increasingly digital, Learnix ensures that engagement remains at the center of learning. By bridging the gap between structure and interaction, it sets a new standard for modern educational platforms.
                </div>
            </div>
        </section>
    </main>

</body>

<script src="<?= base_url('assets/js/mobileMenu.js') . '?v=' . time() ?>"></script>
<script src="<?= base_url('assets/js/page-transition-animation.js') . '?v=' . time() ?>"></script>

</html>