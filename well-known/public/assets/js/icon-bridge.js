/* Font Awesome compatibility bridge for legacy RemixIcon markup.
   Existing ri-* classes in older templates are converted at runtime so
   frontend/admin can use one icon system without broken legacy icons. */
(function () {
    'use strict';

    const map = {
        'ri-fire-fill':['fa-solid','fa-fire'],
        'ri-arrow-right-line':['fa-solid','fa-arrow-right'],
        'ri-arrow-left-line':['fa-solid','fa-arrow-left'],
        'ri-arrow-down-s-line':['fa-solid','fa-chevron-down'],
        'ri-arrow-up-s-line':['fa-solid','fa-chevron-up'],
        'ri-arrow-right-s-line':['fa-solid','fa-chevron-right'],
        'ri-arrow-left-s-line':['fa-solid','fa-chevron-left'],
        'ri-calendar-line':['fa-regular','fa-calendar'],
        'ri-calendar-event-line':['fa-regular','fa-calendar-days'],
        'ri-eye-line':['fa-regular','fa-eye'],
        'ri-eye-fill':['fa-solid','fa-eye'],
        'ri-file-search-line':['fa-solid','fa-file-lines'],
        'ri-folder-2-line':['fa-regular','fa-folder'],
        'ri-folder-3-fill':['fa-solid','fa-folder'],
        'ri-folder-open-fill':['fa-solid','fa-folder-open'],
        'ri-image-line':['fa-regular','fa-image'],
        'ri-search-line':['fa-solid','fa-magnifying-glass'],
        'ri-search-2-line':['fa-solid','fa-magnifying-glass'],
        'ri-article-fill':['fa-solid','fa-file-lines'],
        'ri-facebook-circle-fill':['fa-brands','fa-facebook'],
        'ri-list-ordered-2':['fa-solid','fa-list-ol'],
        'ri-price-tag-3-fill':['fa-solid','fa-tag'],
        'ri-question-answer-fill':['fa-solid','fa-comments'],
        'ri-question-answer-line':['fa-solid','fa-comments'],
        'ri-time-fill':['fa-solid','fa-clock'],
        'ri-twitter-x-line':['fa-brands','fa-x-twitter'],
        'ri-whatsapp-line':['fa-brands','fa-whatsapp'],
        'ri-instagram-line':['fa-brands','fa-instagram'],
        'ri-youtube-line':['fa-brands','fa-youtube'],
        'ri-tiktok-line':['fa-brands','fa-tiktok'],
        'ri-map-pin-fill':['fa-solid','fa-location-dot'],
        'ri-mail-send-fill':['fa-solid','fa-paper-plane'],
        'ri-phone-fill':['fa-solid','fa-phone'],
        'ri-send-plane-fill':['fa-solid','fa-paper-plane'],
        'ri-message-3-line':['fa-regular','fa-message'],
        'ri-information-line':['fa-solid','fa-circle-info'],
        'ri-checkbox-circle-line':['fa-regular','fa-circle-check'],
        'ri-error-warning-line':['fa-solid','fa-triangle-exclamation'],
        'ri-close-line':['fa-solid','fa-xmark'],
        'ri-add-line':['fa-solid','fa-plus'],
        'ri-delete-bin-line':['fa-regular','fa-trash-can'],
        'ri-edit-line':['fa-regular','fa-pen-to-square'],
        'ri-settings-3-line':['fa-solid','fa-gear'],
        'ri-user-line':['fa-regular','fa-user'],
        'ri-group-line':['fa-solid','fa-users'],
        'ri-home-line':['fa-solid','fa-house'],
        'ri-shopping-cart-2-line':['fa-solid','fa-cart-shopping'],
        'ri-bank-card-line':['fa-regular','fa-credit-card'],
        'ri-global-line':['fa-solid','fa-globe'],
        'ri-shield-line':['fa-solid','fa-shield-halved'],
        'ri-customer-service-2-line':['fa-solid','fa-headset'],
        'ri-upload-cloud-2-line':['fa-solid','fa-cloud-arrow-up'],
        'ri-download-cloud-2-line':['fa-solid','fa-cloud-arrow-down'],
        'ri-save-3-line':['fa-regular','fa-floppy-disk'],
        'ri-database-2-line':['fa-solid','fa-database'],
        'ri-layout-grid-line':['fa-solid','fa-grip'],
        'ri-bar-chart-box-line':['fa-solid','fa-chart-column'],
        'ri-flashlight-line':['fa-solid','fa-bolt'],
        'ri-star-fill':['fa-solid','fa-star'],
        'ri-star-line':['fa-regular','fa-star']
    };

    function convert(root) {
        const scope = root && root.querySelectorAll ? root : document;
        const nodes = scope.querySelectorAll('i[class*="ri-"]');

        nodes.forEach(function (el) {
            const legacy = Array.from(el.classList).find(function (c) {
                return c.indexOf('ri-') === 0;
            });
            if (!legacy) return;

            const target = map[legacy] || ['fa-regular','fa-circle'];
            Array.from(el.classList).forEach(function (c) {
                if (c.indexOf('ri-') === 0) el.classList.remove(c);
            });
            el.classList.add('icon', 'fa-fw', target[0], target[1]);
            el.setAttribute('aria-hidden', 'true');
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { convert(document); });
    } else {
        convert(document);
    }

    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) convert(node);
            });
        });
    });

    observer.observe(document.documentElement, { childList: true, subtree: true });
})();
