function HomepageAnimation() {
    const homePage = document.querySelector('.home-page');
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    // Leave the page untouched when motion is reduced or this script is used elsewhere.
    if (!homePage || prefersReducedMotion.matches) {
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