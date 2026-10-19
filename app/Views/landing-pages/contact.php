<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akademus | Contact</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/global.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/header.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/footer.css") . '?v=' . time(); ?>">
    <link rel="stylesheet" href="<?= base_url("assets/css/pages/landing-pages/contact.css") . '?v=' . time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/@emailjs/browser@4.4.1/dist/email.min.js" integrity="sha256-BS9pFqIbpb4UA2ZDnoG4StETY8hN+6KgbyCMe0pvbV8=" crossorigin="anonymous"></script>
    <script>
        (function(){
            emailjs.init({ publicKey: "mOcc2uSM1tqbRywYU" });
        })();
    </script>
</head>
<body>
    <?= view('landing-pages/header.php') ?>
    
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
                <a href="tel:+639741601" class="contact-card__link">+63 974 8491</a>
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
                    referrerpolicy="no-referrer-when-downgrade" title="The Peak Tower, 107 L.P. Leviste Street, Makati City"></iframe>
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

    <?= view('landing-pages/footer.php') ?>

    <script src="<?= base_url("assets/js/page-animation.js") . '?v=' . time(); ?>"></script>
    <script src="<?= base_url("assets/js/mobileMenu.js") . '?v=' . time(); ?>"></script>
    <script src="<?= base_url("assets/js/page-transition-animation.js") . '?v=' . time(); ?>"></script>
    <script>
        document.getElementById("contactForm").addEventListener("submit", function(e) {
            e.preventDefault();

            const btn      = document.getElementById("submitBtn");
            const feedback = document.getElementById("formFeedback");
            const form     = this;

            btn.disabled   = true;
            btn.textContent = "Sending...";
            feedback.className = "form-feedback";
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
                    feedback.className  = "form-feedback form-feedback--success";
                    feedback.textContent = "Message sent! We'll get back to you soon.";
                    form.reset();
                })
                .catch(function(err) {
                    feedback.className  = "form-feedback form-feedback--error";
                    feedback.textContent = "Something went wrong. Please try again.";
                    console.error("EmailJS error:", err);
                })
                .finally(function() {
                    btn.disabled    = false;
                    btn.textContent = "Send Message";
                });
        });
    </script>

</body>
</html>
