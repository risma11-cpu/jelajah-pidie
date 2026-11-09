import '../css/mobile-nav.css';

(function () {
    'use strict';

    var MQ = '(max-width: 820px)';

    function init() {
        var body = document.body;
        if (!body || !body.classList.contains('site')) return; // hanya website publik

        var bar = document.querySelector('.nav-pill') || document.querySelector('.site-header-inner');
        if (!bar) return;

        var nav = bar.querySelector('.nav');
        if (!nav) return;
        if (bar.querySelector('.nav-toggle')) return; // sudah dibuat

        if (!nav.id) nav.id = 'site-nav';

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'nav-toggle';
        btn.setAttribute('aria-label', 'Buka menu');
        btn.setAttribute('aria-expanded', 'false');
        btn.setAttribute('aria-controls', nav.id);
        btn.innerHTML =
            '<span class="nav-toggle-bar"></span>' +
            '<span class="nav-toggle-bar"></span>' +
            '<span class="nav-toggle-bar"></span>';

        bar.appendChild(btn);
        bar.classList.add('has-toggle');

        function setOpen(open) {
            nav.classList.toggle('is-open', open);
            btn.classList.toggle('is-open', open);
            btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            btn.setAttribute('aria-label', open ? 'Tutup menu' : 'Buka menu');
        }

        // Buka / tutup lewat tombol
        btn.addEventListener('click', function () {
            setOpen(!nav.classList.contains('is-open'));
        });

        // Tutup setelah salah satu link diketuk
        nav.addEventListener('click', function (e) {
            if (e.target.closest('a')) setOpen(false);
        });

        // Tutup saat mengetuk di luar navbar
        document.addEventListener('click', function (e) {
            if (!bar.contains(e.target)) setOpen(false);
        });

        // Tutup dengan tombol Esc
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') setOpen(false);
        });

        // Tutup otomatis saat layar diperlebar ke ukuran laptop
        var mq = window.matchMedia(MQ);
        function onChange(e) { if (!e.matches) setOpen(false); }
        if (mq.addEventListener) mq.addEventListener('change', onChange);
        else if (mq.addListener) mq.addListener(onChange);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();