(function () {
    if (typeof window === 'undefined' || typeof document === 'undefined') {
        return;
    }

    if (typeof window.adminImageViewer === 'undefined') {
        window.adminImageViewer = {
            overlay: null,
            image: null,
            caption: null,
            ensureViewer: function () {
                if (this.overlay) {
                    return;
                }

                var overlay = document.createElement('div');
                overlay.className = 'admin-image-viewer';
                overlay.setAttribute('hidden', 'hidden');
                overlay.innerHTML =
                    '<div class="admin-image-viewer__backdrop" data-image-viewer-close></div>' +
                    '<div class="admin-image-viewer__dialog" role="dialog" aria-modal="true" aria-label="Просмотр изображения">' +
                    '<button type="button" class="admin-image-viewer__close" aria-label="Закрыть" data-image-viewer-close>&times;</button>' +
                    '<img class="admin-image-viewer__img" src="" alt="">' +
                    '<div class="admin-image-viewer__caption"></div>' +
                    '</div>';

                overlay.addEventListener('click', function () {
                    window.adminImageViewer.close();
                });

                overlay.addEventListener('mousedown', function (event) {
                    event.preventDefault();
                });

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && window.adminImageViewer.overlay && !window.adminImageViewer.overlay.hasAttribute('hidden')) {
                        window.adminImageViewer.close();
                    }
                });

                document.body.appendChild(overlay);
                this.overlay = overlay;
                this.image = overlay.querySelector('.admin-image-viewer__img');
                this.image.setAttribute('draggable', 'false');
                this.caption = overlay.querySelector('.admin-image-viewer__caption');
            },
            clearSelection: function () {
                var selection = window.getSelection ? window.getSelection() : null;

                if (selection && selection.removeAllRanges) {
                    selection.removeAllRanges();
                }
            },
            openFromLink: function (link) {
                this.ensureViewer();
                this.clearSelection();
                var src = link.getAttribute('data-image-full') || link.getAttribute('href');
                var caption = link.getAttribute('data-image-title') || link.getAttribute('title') || '';

                this.image.setAttribute('src', src);
                this.image.setAttribute('alt', caption);
                this.caption.textContent = caption;
                this.caption.hidden = caption === '';
                this.overlay.removeAttribute('hidden');
                document.body.classList.add('admin-image-viewer-open');
            },
            close: function () {
                if (!this.overlay) {
                    return;
                }

                this.overlay.setAttribute('hidden', 'hidden');
                this.image.setAttribute('src', '');
                this.caption.textContent = '';
                this.clearSelection();
                document.body.classList.remove('admin-image-viewer-open');
            }
        };
    }

    window.openAdminImageViewer = function (link) {
        if (!window.adminImageViewer) {
            return false;
        }

        window.adminImageViewer.openFromLink(link);
        return false;
    };

    document.addEventListener('click', function (event) {
        var link = event.target.closest('[data-image-viewer]');

        if (!link) {
            return;
        }

        event.preventDefault();
        window.openAdminImageViewer(link);
    });
})();
