/* page transition animation*/

document.addEventListener("DOMContentLoaded", () => {

    /* load animation */
    const heroBg = document.querySelector(".hp-hero__bg");
    const heroCard = document.querySelector(".hp-hero__card");

    if(heroBg){
        heroBg.classList.add("anim-hero-bg");
    }

    if(heroCard){
        heroCard.classList.add("anim-hero-card");
    }


    /* scroll reveal */
    const reveals = document.querySelectorAll(
        ".hp-feat-card, .hp-sys-card, .story__card, .mission__box, .value-card, .history__row, .contact-card, .contact-map, .contact-form, .contact-hero__content"
    );

    const observer = new IntersectionObserver((entries)=>{
        entries.forEach((entry,index)=>{

            if(entry.isIntersecting){

                setTimeout(()=>{
                    entry.target.classList.add("show");
                }, index * 120);

            }

        });
    },{
        threshold:0.15
    });

    reveals.forEach(el=>{
        el.classList.add("reveal");
        observer.observe(el);
    });


    /* header shrink when user scrolling */
    const header = document.querySelector(".header");

    window.addEventListener("scroll", ()=>{

        if(window.scrollY > 60){
            header.classList.add("header-scroll");
        }else{
            header.classList.remove("header-scroll");
        }

    });

});