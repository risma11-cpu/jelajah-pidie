// Animasi muncul dari kiri / kanan saat elemen masuk layar.
// Gaya awal dan arah gerak diatur di resources/css/app.css (bagian 10).
(function () {
    'use strict';

    var root = document.documentElement;

    // Kalau animasi tidak aktif (kurangi gerakan, atau halaman admin), tidak ada yang perlu dilakukan.
    if (!root.classList.contains('js')) {
        window.__revealReady = true;
        return;
    }

    // Harus sama dengan daftar di CSS bagian 10.
    var SELECTORS = [
        '.hero-copy', '.hero-card', '.about-img', '.about-text', '.finder', '.stat', '.section-head',
        '.dest-card', '.detail-hero', '.detail-grid .card', '.gallery figure',
        '.grid-cards > .card', '.page > .card'
    ].join(',');

    function init() {
        var items = document.querySelectorAll(SELECTORS);

        if (!('IntersectionObserver' in window)) {
            items.forEach(function (el) { el.classList.add('is-in'); });
            window.__revealReady = true;
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            var n = 0;
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                // Elemen yang muncul bersamaan dibuat berurutan, maksimal 5 langkah
                entry.target.style.setProperty('--reveal-delay', (Math.min(n, 5) * 0.09) + 's');
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
                n++;
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

        items.forEach(function (el) { observer.observe(el); });
        window.__revealReady = true;
    }

    function start() {
        // Kalau intro sedang tampil, tunggu sampai selesai supaya animasi halaman tidak terbuang di belakangnya
        if (root.classList.contains('splash-active')) {
            window.__revealReady = true;
            window.addEventListener('splash:done', init, { once: true });
            return;
        }
        init();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();