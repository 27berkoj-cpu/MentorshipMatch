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

function AboutpageAnimation(){
    const aboutPage = document.querySelector('.about-page');
    const animatedExcuted = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Skip the animation if motion effects are turned off or this isn't the About page.
    if (!aboutPage || animatedExcuted.matches) {
        return;
    }

    // Wait for the initial paint before showing the About page.
    requestAnimationFrame(() => {
        requestAnimationFrame(() => {
            document.body.classList.add('about-intro-ready');
        });
    });
}

AboutpageAnimation();