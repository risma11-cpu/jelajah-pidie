// Mengatur intro 3D: memuat adegan Three.js hanya saat dibutuhkan, tombol Lewati, klik/Esc,
// menutup dengan halus, dan mengingat per sesi. Adegannya ada di resources/js/intro3d.js.
// Diagnosa: buka halaman dengan ?splash=1&introdebug=1 untuk melihat status dan pesan error di layar.
(function () {
    'use strict';

    var root = document.documentElement;
    var splash = document.querySelector('.splash');

    // Tidak aktif (sudah pernah tampil di sesi ini, atau pengguna memilih "kurangi gerakan")
    if (!splash || !root.classList.contains('splash-active')) return;

    var canvas = splash.querySelector('.intro-canvas');
    var caption = splash.querySelector('.intro-caption');
    var debug = /[?&]introdebug=1/.test(location.search);
    var finished = false;
    var controller = null;
    var statusEl = null;

    function status(msg) {
        if (!debug || !splash) return;
        if (!statusEl) {
            statusEl = document.createElement('div');
            statusEl.className = 'splash-debug';
            splash.appendChild(statusEl);
        }
        statusEl.textContent = msg;
    }

    function webglInfo() {
        try {
            var c = document.createElement('canvas');
            return (c.getContext('webgl2') || c.getContext('webgl')) ? 'WebGL: tersedia' : 'WebGL: TIDAK tersedia';
        } catch (e) { return 'WebGL: error'; }
    }

    function cleanup() {
        if (!splash) return;
        if (controller) { try { controller.destroy(); } catch (e) { /* abaikan */ } controller = null; }
        splash.remove();
        splash = null;
        root.classList.remove('splash-active');
        window.dispatchEvent(new Event('splash:done'));   // dipakai reveal.js untuk memulai animasi halaman
    }

    function close() {
        if (finished) return;
        finished = true;
        try { sessionStorage.setItem('jp_splash', '1'); } catch (e) { /* abaikan */ }

        splash.classList.add('is-leaving');
        splash.addEventListener('animationend', function (e) { if (e.target === splash) cleanup(); });
        setTimeout(cleanup, 1100);                          // jaring pengaman
    }

    // Kegagalan intro: tercatat di console; mode diagnosa menampilkannya di layar, mode biasa menutup intro
    function fail(err) {
        console.error('[splash] intro 3D gagal:', err);
        if (debug && splash) {
            var box = document.createElement('pre');
            box.className = 'splash-error';
            box.textContent = 'Intro 3D gagal:\n' + ((err && (err.stack || err.message)) || String(err));
            splash.appendChild(box);
            status('GAGAL. Screenshot layar ini lalu kirim ke Claude.');
            return;
        }
        close();
    }

    splash.addEventListener('click', function (e) {
        if (e.target.closest && e.target.closest('.splash-error')) return;   // boleh memilih/menyalin teks error
        close();
    });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    var skip = splash.querySelector('.splash-skip');
    if (skip) skip.focus({ preventScroll: true });

    status(webglInfo() + ' | memuat intro3d.js ...');

    // Adegan 3D dimuat belakangan (code-splitting Vite), jadi halaman lain tidak ikut menanggung beratnya
    import('./intro3d.js')
        .then(function (m) {
            if (finished) return;
            window.__introStarted = true;
            status(webglInfo() + ' | intro3d.js termuat, adegan dimulai');
            controller = m.playIntro(canvas, { caption: caption, onComplete: close, onError: fail });
        })
        .catch(fail);
})();