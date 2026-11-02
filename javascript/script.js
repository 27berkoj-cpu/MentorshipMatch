function HomepageAnimation() {
    const homePage = document.querySelector('.home-page');
    const animatedExcuted = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Skip the animation if motion effects are turned off or this isn't the home page.
    if (!homePage || animatedExcuted.matches) {
        return;
    }

    // Wait for the initial paint so the page-wide entrance animation can be seen.
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            homePage.classList.add('intro-ready');
        });
    });
}

HomepageAnimation();

function OtherMainPagesAnimation(){
    const mainPage = document.querySelector('.about-page, .contact-page, .container');
    const animatedExcuted = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Skip the animation if motion effects are turned off or this isn't a main page.
    if (!mainPage || animatedExcuted.matches) {
        return;
    }

    // Wait for the initial paint before showing the page.
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.body.classList.add('othermainpages-intro-ready');
        });
    });
}

OtherMainPagesAnimation();