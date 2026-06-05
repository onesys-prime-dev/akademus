/* AKADEMUS PAGE TRANSITION ANIMATION
   homepage <-> about-us */

document.addEventListener("DOMContentLoaded", () => {

    const links = document.querySelectorAll(
        '.header__link, .hp-header__link, .hp-overlay__link, .header__cta, .hp-header__cta, .hp-overlay__cta'
    );

    links.forEach(link => {

        const href = link.getAttribute("href");
        if (!href || href.startsWith("#")) return;

        link.addEventListener("click", function(e){

            const target = this.getAttribute("href");

            if(!target || target.startsWith("#")) return;

            e.preventDefault();

            runPageTransition(target);

        });

    });

});


function runPageTransition(targetPage){

    const overlay = createTransitionOverlay();

    document.body.appendChild(overlay);

    // Force reflow so the browser registers opacity:0 before transitioning to opacity:1
    void overlay.offsetHeight;

    overlay.classList.add("pt-open");

    animateCurrentPageOut();

    animateSpaceship();

    setTimeout(()=>{
        window.location.href = targetPage;
    }, 950);
}


/* PAGE EXIT */
function animateCurrentPageOut(){

    document.body.classList.add("page-leave");

}


/* SPACESHIP TRANSITION */
function animateSpaceship(){

    const ship =
        document.querySelector(".about-hero__right img") ||
        document.querySelector(".hp-hero__bg");

    if(ship){
        ship.classList.add("ship-flight");
    }

}


/* overlay */
function createTransitionOverlay(){

    const div = document.createElement("div");
    div.className = "page-transition-overlay";

    div.innerHTML = `
        <div class="page-transition-logo">
            AKADEMUS
        </div>
    `;

    return div;
}