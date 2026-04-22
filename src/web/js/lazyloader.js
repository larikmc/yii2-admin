const lazyloader = {
    observer: null,

    init(root = document) {
        root.querySelectorAll('img:not([data-lazy-inited])').forEach(img => {
            img.setAttribute('data-lazy-inited', '1');

            if (img.getAttribute('loading') === 'eager') return;
            if (img.getAttribute('fetchpriority') === 'high') return;

            if (!img.hasAttribute('loading')) img.setAttribute('loading', 'lazy');
            if (!img.hasAttribute('decoding')) img.setAttribute('decoding', 'async');

            if (!img.hasAttribute('data-no-placeholder')) {
                img.classList.add('lazy-img');
            }

            const done = () => img.classList.remove('lazy-img');
            img.addEventListener('load', done, {once: true});
            img.addEventListener('error', done, {once: true});

            if (img.hasAttribute('data-src') || img.hasAttribute('data-srcset')) {
                this.observe(img);
                return;
            }

            if (img.complete && img.naturalWidth > 0) {
                done();
            }
        });
    },

    observe(img) {
        if (!('IntersectionObserver' in window)) {
            this.load(img);
            return;
        }

        if (!this.observer) {
            this.observer = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;

                    this.load(entry.target);
                    this.observer.unobserve(entry.target);
                });
            }, {
                rootMargin: '100px 0px 300px 0px'
            });
        }

        this.observer.observe(img);
    },

    load(img) {
        const src = img.getAttribute('data-src');
        const srcset = img.getAttribute('data-srcset');

        if (srcset) {
            img.setAttribute('srcset', srcset);
            img.removeAttribute('data-srcset');
        }

        if (src) {
            img.setAttribute('src', src);
            img.removeAttribute('data-src');
        }
    }
};

window.lazyloader = lazyloader;

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => lazyloader.init());
} else {
    lazyloader.init();
}

document.addEventListener('pjax:end', event => {
    lazyloader.init(event.target || document);
});

if (window.jQuery) {
    window.jQuery(document).on('pjax:end', function (event) {
        lazyloader.init(event.target || document);
    });
}
