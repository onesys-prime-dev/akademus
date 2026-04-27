/* mobile menu */

function hpOpenMenu() {
    document.getElementById("hpOverlay").classList.add("open");
    document.body.style.overflow = "hidden";
}

function hpCloseMenu() {
    document.getElementById("hpOverlay").classList.remove("open");
    document.body.style.overflow = "";
}

document.getElementById("hpHamburger").addEventListener("click", hpOpenMenu);
document.getElementById("hpOverlayClose").addEventListener("click", hpCloseMenu);

document.getElementById("hpOverlay").addEventListener("click", function (e) {
    if (e.target === this) hpCloseMenu();
});


/* auto closes menu when clicking mobile link */
document.querySelectorAll(".hp-overlay__link").forEach(link => {
    link.addEventListener("click", () => {
        hpCloseMenu();
    });
});


/* smooth scroll */
document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener("click", function (e) {
        const href = this.getAttribute("href");
        if (!href || href === "#") return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            target.scrollIntoView({
                behavior: "smooth"
            });

            hpCloseMenu();
        }
    });
});


/* scroll spy */
const spySections = ["home", "featured", "services", "contact"];

window.addEventListener("scroll", function () {
    let y = window.scrollY + 130;
    spySections.forEach(id => {
        const el = document.getElementById(id);

        if (!el) return;
        if (el.offsetTop <= y && el.offsetTop + el.offsetHeight > y) {
            document.querySelectorAll(".header__link").forEach(link => {
                link.classList.remove("header__link--active");
            });

            const activeLink = document.querySelector(
                '.header__link[href="#' + id + '"]'
            );

            if (activeLink) {
                activeLink.classList.add("header__link--active");
            }
        }
    });
});

