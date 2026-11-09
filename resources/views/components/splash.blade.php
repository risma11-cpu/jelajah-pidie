{{-- Intro 3D "HELLO": huruf 3D muncul satu per satu, lalu karakter bertopi berjalan, melambai, dan bersandar di huruf H.
     Adegannya digambar di <canvas> oleh resources/js/intro3d.js (Three.js + GSAP). Dipakai di beranda lewat <x-splash />.
     Tampil sekali per sesi browser. Tambahkan ?splash=1 di URL untuk memaksa tampil lagi (uji coba). --}}
<div class="splash" role="dialog" aria-modal="true" aria-label="Selamat datang di Jelajah Pidie">
    <canvas class="intro-canvas" aria-hidden="true"></canvas>
    <p class="intro-caption">Selamat datang di Jelajah Pidie</p>
    <button type="button" class="splash-skip">Lewati</button>
</div>
<script>
    // Dijalankan langsung setelah elemen splash supaya tidak ada "kedipan" konten di belakangnya.
    (function () {
        var d = document.documentElement, s = document.querySelector('.splash');
        if (!s) return;
        var seen = false, force = /[?&]splash=1/.test(location.search), skip = /[?&]nointro=1/.test(location.search);
        try { seen = !!sessionStorage.getItem('jp_splash'); } catch (e) {}
        var calm = window.matchMedia && matchMedia('(prefers-reduced-motion: reduce)').matches;
        // Perangkat lemah, hemat data, atau koneksi lambat: lewati intro 3D supaya halaman langsung terbuka
        var weak = false;
        try {
            var c = navigator.connection || {};
            weak = !!(c.saveData || /(^|-)(2g|3g)$/.test(c.effectiveType || '') ||
                (navigator.deviceMemory && navigator.deviceMemory <= 2) ||
                (navigator.hardwareConcurrency && navigator.hardwareConcurrency <= 2));
        } catch (e) {}
        // ?nointro=1 melewati intro (berguna untuk mengukur performa halaman); ?splash=1 memaksa tampil
        if (skip || calm || (!force && (seen || weak))) { s.remove(); return; }
        d.classList.add('splash-active');
        // Pengaman: kalau adegan 3D tidak sempat dimulai (JS gagal / koneksi sangat lambat), tutup intro.
        // Mode diagnosa (?introdebug=1) tidak ditutup paksa supaya pesan error sempat terbaca.
        if (!/[?&]introdebug=1/.test(location.search)) {
            setTimeout(function () {
                if (!window.__introStarted) { s.remove(); d.classList.remove('splash-active'); window.dispatchEvent(new Event('splash:done')); }
            }, 15000);
        }
        if (!/[?&]introdebug=1/.test(location.search)) setTimeout(function () { d.classList.remove('splash-active'); }, 25000);
    })();
</script>