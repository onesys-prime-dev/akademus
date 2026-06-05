<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Contact</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('assets/css/global.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/header.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/footer.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/contact.css') ?>">
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4/dist/email.min.js"></script>
    <script>
        (function(){
            emailjs.init({ publicKey: "mOcc2uSM1tqbRywYU" });
        })();
    </script>
</head>
<body>

    <!-- MOBILE OVERLAY -->
    <div class="hp-overlay" id="hpOverlay">
        <div class="hp-overlay__panel">
            <button class="hp-overlay__close" id="hpOverlayClose">&#10005;</button>
            <nav class="hp-overlay__nav">
                <a href="<?= base_url('/') ?>" class="hp-overlay__link">Home</a>
                <a href="<?= base_url('about') ?>" class="hp-overlay__link">About Us</a>
                <a href="<?= base_url('features') ?>" class="hp-overlay__link">Features</a>
                <a href="<?= base_url('contact') ?>" class="hp-overlay__link">Contact</a>
            </nav>
            <a href="<?= base_url('sign-in') ?>" class="hp-overlay__cta">Get Started</a>
        </div>
    </div>

    <!-- HEADER -->
    <header class="header">
        <div class="header__inner">
            <a href="<?= base_url('/') ?>" class="header__logo">
                <img src="<?= base_url('assets/images/logo_akademus.png') ?>" class="header__logo-img" alt="Akademus Logo">
                <span class="header__brand">AKADEMUS</span>
            </a>
            <nav class="header__nav">
                <a href="<?= base_url('/') ?>" class="header__link">Home</a>
                <a href="<?= base_url('about') ?>" class="header__link">About Us</a>
                <a href="<?= base_url('features') ?>" class="header__link">Features</a>
                <a href="<?= base_url('contact') ?>" class="header__link header__link--active">Contact</a>
            </nav>
            <a href="<?= base_url('sign-in') ?>" class="header__cta">Get Started</a>
            <button class="hamburger" id="hpHamburger">
                <span></span><span></span><span></span>
            </button>
        </div>
    </header>


    <!-- HERO -->
    <section class="contact-hero">
        <div class="contact-hero__overlay"></div>
        <div class="contact-hero__content">
            <h1 class="contact-hero__title">CONTACT US</h1>
            <div class="contact-hero__dots">
                <span></span><span></span><span></span><span></span><span></span><span></span><span></span>
            </div>
            <p class="contact-hero__desc">
                At Akademus, communication is important to us. Feel free to contact our team for
                inquiries, support, or collaboration opportunities. We look forward to hearing from you.
            </p>
        </div>
    </section>


    <!-- INFO CARDS -->
    <section class="contact-info">
        <div class="contact-info__grid">
            <div class="contact-card">
                <div class="contact-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                    </svg>
                </div>
                <h3 class="contact-card__title">VISIT US</h3>
                <p class="contact-card__desc">Feel free to visit us during our office hours to discuss collaborations, services, or any inquiries about our platform.</p>
                <a href="#" class="contact-card__link">Lavisite St., Makati City, PH</a>
            </div>
            <div class="contact-card">
                <div class="contact-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M6.62 10.79a15.05 15.05 0 0 0 6.59 6.59l2.2-2.2a1 1 0 0 1 1.01-.24c1.12.37 2.33.57 3.58.57a1 1 0 0 1 1 1V20a1 1 0 0 1-1 1C10.61 21 3 13.39 3 4a1 1 0 0 1 1-1h3.5a1 1 0 0 1 1 1c0 1.25.2 2.46.57 3.58a1 1 0 0 1-.25 1.01l-2.2 2.2z"/>
                    </svg>
                </div>
                <h3 class="contact-card__title">CALL US</h3>
                <p class="contact-card__desc">Reach out to us through our contact number and we will gladly assist you with your concerns.</p>
                <a href="tel:+639748491" class="contact-card__link">+63 974 8491</a>
            </div>
            <div class="contact-card">
                <div class="contact-card__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4-8 5-8-5V6l8 5 8-5v2z"/>
                    </svg>
                </div>
                <h3 class="contact-card__title">CONTACT US</h3>
                <p class="contact-card__desc">We aim to respond as soon as possible.</p>
                <a href="mailto:noreply@kicloud.com" class="contact-card__link">noreply@kicloud.com</a>
            </div>
        </div>
    </section>


    <!-- CONTACT FORM SECTION -->
    <section class="contact-form-section">
        <div class="contact-form-section__inner">
            <!-- MAP -->
            <div class="contact-map">
                <iframe
                    src="https://maps.google.com/maps?q=The+Peak+Tower+107+LP+Leviste+Street+Salcedo+Village+Makati+City+Metro+Manila&output=embed"
                    width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade"
                    title="The Peak Tower, 107 L.P. Leviste Street, Makati City"></iframe>
            </div>
            <!-- FORM -->
            <div class="contact-form">
                <h2 class="contact-form__title">Get in Touch</h2>
                <p class="contact-form__sub">You can reach us anytime</p>
                <form class="contact-form__body" id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <input type="text" name="first_name" class="form-input" placeholder="First Name" required>
                        </div>
                        <div class="form-group">
                            <input type="text" name="last_name" class="form-input" placeholder="Last Name" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <input type="email" name="email" class="form-input" placeholder="Email Address" required>
                    </div>
                    <div class="form-group form-group--phone">
                        <div class="form-phone-prefix">
                            <span class="form-phone-flag">🇵🇭</span>
                            <select class="form-phone-code" name="phone_code">
                                <option value="+63">+63</option>
                            </select>
                        </div>
                        <input type="tel" name="phone" class="form-input form-input--phone" placeholder="Phone Number">
                    </div>
                    <div class="form-group">
                        <textarea class="form-input form-textarea" name="message" placeholder="How can we help?" required></textarea>
                    </div>
                    <label class="form-checkbox">
                        <input type="checkbox" id="agreeCheck" required>
                        <span>I acknowledge the information above is complete, understand messages to the <a href="#">Privacy Policy</a></span>
                    </label>
                    <div id="formFeedback" class="form-feedback"></div>
                    <button type="submit" class="form-submit" id="submitBtn">Send Message</button>
                </form>
            </div>
        </div>
    </section>


    <!-- FOOTER -->
    <footer class="footer">
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

<script src="<?= base_url('assets/js/page-animation.js') ?>"></script>
<script src="<?= base_url('assets/js/mobileMenu.js') ?>"></script>
<script src="<?= base_url('assets/js/page-transition-animation.js') ?>"></script>
<script>
    document.getElementById("contactForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const btn      = document.getElementById("submitBtn");
        const feedback = document.getElementById("formFeedback");
        const form     = this;

        btn.disabled    = true;
        btn.textContent = "Sending...";
        feedback.className   = "form-feedback";
        feedback.textContent = "";

        const now = new Date();
        const params = {
            name    : form.first_name.value.trim() + " " + form.last_name.value.trim(),
            email   : form.email.value.trim(),
            phone   : (form.phone_code.value || "+63") + " " + form.phone.value.trim(),
            message : form.message.value.trim(),
            time    : now.toLocaleString("en-PH", { dateStyle: "long", timeStyle: "short" })
        };

        emailjs.send("service_x2sso7e", "template_1ovitbk", params)
            .then(function() {
                feedback.className   = "form-feedback form-feedback--success";
                feedback.textContent = "Message sent! We'll get back to you soon.";
                form.reset();
            })
            .catch(function(err) {
                feedback.className   = "form-feedback form-feedback--error";
                feedback.textContent = "Something went wrong. Please try again.";
                console.error("EmailJS error:", err);
            })
            .finally(function() {
                btn.disabled    = false;
                btn.textContent = "Send Message";
            });
    });
</script>

</html>
