import Lenis from 'lenis';

/**
 * Landing page: buttery smooth scroll + parallax.
 *
 * Strategy:
 *  - Lenis drives a single rAF loop and exposes `scroll` + `on('scroll')`.
 *  - Parallax: any element with [data-parallax-speed] gets a CSS custom property
 *    --parallax-y = (elCenter - viewportCenter) * speed. The CSS uses that
 *    to translate the layer. Faster speed = more visible movement.
 *  - Reveal: any [data-reveal] element fades + slides in when it intersects
 *    the viewport, using IntersectionObserver.
 *  - Hash links (#features) are intercepted so Lenis can smooth-scroll.
 *  - Respects prefers-reduced-motion.
 */

const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

const initSmoothScroll = () => {
    if (reducedMotion) return null;

    const lenis = new Lenis({
        duration: 1.15,
        easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
        smoothWheel: true,
        wheelMultiplier: 1,
        touchMultiplier: 1.4,
    });

    const raf = (time) => {
        lenis.raf(time);
        requestAnimationFrame(raf);
    };
    requestAnimationFrame(raf);

    return lenis;
};

const setupParallax = () => {
    if (reducedMotion) return;

    const layers = document.querySelectorAll('[data-parallax-speed]');
    if (!layers.length) return;

    const update = () => {
        const vh = window.innerHeight;
        const center = vh / 2;
        for (const el of layers) {
            const speed = parseFloat(el.dataset.parallaxSpeed) || 0;
            const rect = el.getBoundingClientRect();
            const elCenter = rect.top + rect.height / 2;
            const distance = (elCenter - center) * speed;
            el.style.setProperty('--parallax-y', `${distance.toFixed(2)}px`);
        }
    };

    let ticking = false;
    const onScroll = () => {
        if (ticking) return;
        ticking = true;
        requestAnimationFrame(() => {
            update();
            ticking = false;
        });
    };

    update();
    window.addEventListener('scroll', onScroll, { passive: true });
    window.addEventListener('resize', onScroll, { passive: true });
};

const setupReveal = () => {
    const items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;

    if (reducedMotion) {
        items.forEach((el) => el.classList.add('is-revealed'));
        return;
    }

    const io = new IntersectionObserver(
        (entries) => {
            for (const entry of entries) {
                if (entry.isIntersecting) {
                    const delay = parseInt(entry.target.dataset.revealDelay || '0', 10);
                    setTimeout(() => entry.target.classList.add('is-revealed'), delay);
                    io.unobserve(entry.target);
                }
            }
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' },
    );

    items.forEach((el) => io.observe(el));
};

const setupAnchorLinks = (lenis) => {
    if (!lenis) return;
    document.querySelectorAll('a[href^="#"]').forEach((a) => {
        a.addEventListener('click', (e) => {
            const id = a.getAttribute('href');
            if (!id || id === '#') return;
            const target = document.querySelector(id);
            if (!target) return;
            e.preventDefault();
            lenis.scrollTo(target, { offset: -72, lock: false });
        });
    });
};

const init = () => {
    const lenis = initSmoothScroll();
    setupParallax();
    setupReveal();
    setupAnchorLinks(lenis);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
