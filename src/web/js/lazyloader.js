const lazyloader = {
    init(root = document) {
        root.querySelectorAll('img:not([data-lazy-inited])').forEach(img => {
            img.setAttribute('data-lazy-inited', '1');

            // ne trogaem yavno eager/high
            if (img.getAttribute('loading') === 'eager') return;
            if (img.getAttribute('fetchpriority') === 'high') return;

            if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
            if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');

            // esli uzhe zagruzhena - nichego ne delaem
            if (img.complete && img.naturalWidth > 0) return;

            // placeholder vsem, krome otmechennykh
            if (!img.hasAttribute('data-no-placeholder')) {
                img.classList.add('lazy-img');
            }

            const done = () => img.classList.remove('lazy-img');
            img.addEventListener('load', done, {once: true});
            img.addEventListener('error', done, {once: true});
        });
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => lazyloader.init());
} else {
    lazyloader.init();
}
