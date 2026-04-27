/* AKADEMUS PAGE TRANSITION ANIMATION
   homepage <-> about-us */

document.addEventListener("DOMContentLoaded", () => {

    const links = document.querySelectorAll(".page-link");

    links.forEach(link => {

        link.addEventListener("click", function(e){

            const target = this.href;

            if(!target) return;

            e.preventDefault();

            runPageTransition(target);

        });

    });

});


function runPageTransition(targetPage){

    const overlay = createTransitionOverlay();

    document.body.appendChild(overlay);

    requestAnimationFrame(()=>{

        overlay.classList.add("pt-open");

        animateCurrentPageOut();

        animateSpaceship();

    });

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