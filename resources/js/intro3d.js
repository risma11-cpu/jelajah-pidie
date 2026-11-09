// Intro 3D "HELLO": Three.js untuk objek, kamera, cahaya, bayangan. GSAP untuk linimasa dan easing.
// Dimuat hanya saat intro tampil (lihat splash.js), jadi halaman lain tidak ikut berat.
//
//   npm install three gsap
//
// Urutan: huruf HELLO muncul satu per satu -> karakter bertopi berjalan dari kiri ->
// berhenti di samping huruf H -> melambaikan tangan -> bersandar di huruf H.

import * as THREE from 'three';
import { RoomEnvironment } from 'three/addons/environments/RoomEnvironment.js';
import { gsap } from 'gsap';

const TAU = Math.PI * 2;
const clamp = (v, a, b) => Math.min(Math.max(v, a), b);
const lerp = (a, b, t) => a + (b - a) * t;

/* ---------- Ukuran (satuan bebas, 1 = kira-kira 1 meter) ---------- */
const LETTER_H = 1.1;      // tinggi huruf
const STEM = 0.32;         // tebal batang tegak huruf
const ARM = 0.24;          // tebal lengan mendatar (E, L, palang H)
const DEPTH = 0.42;        // kedalaman ekstrusi 3D
const GAP = 0.17;          // jarak antar huruf
const HIP_Y = 0.975;       // tinggi pinggul karakter dari lantai
const L1 = 0.4;            // panjang lengan atas
const L2 = 0.41;           // panjang lengan bawah sampai pusat tangan
const STRIDE = 1.44;       // panjang satu siklus langkah (dua langkah)
const WALK_SPEED = 1.55;   // kecepatan jalan (satuan per detik)

const LETTERS = [
    { ch: 'H', w: 0.98 },
    { ch: 'E', w: 0.82 },
    { ch: 'L', w: 0.78 },
    { ch: 'L', w: 0.78 },
    { ch: 'O', w: 1.04 },
];

/* ======================================================================
   HURUF 3D: bentuk tebal dengan sudut membulat, lalu di-ekstrusi + bevel
====================================================================== */

// Poligon dengan sudut dibulatkan (kurva kuadratik di tiap sudut)
function roundedShape(points, radius) {
    const n = points.length;
    const P = points.map(([x, y]) => new THREE.Vector2(x, y));
    const shape = new THREE.Shape();

    for (let i = 0; i < n; i++) {
        const p = P[i];
        const a = P[(i - 1 + n) % n];
        const b = P[(i + 1) % n];
        const va = new THREE.Vector2(a.x - p.x, a.y - p.y);
        const vb = new THREE.Vector2(b.x - p.x, b.y - p.y);
        const r = Math.min(radius, va.length() / 2, vb.length() / 2);
        va.normalize();
        vb.normalize();
        const sx = p.x + va.x * r, sy = p.y + va.y * r;
        const ex = p.x + vb.x * r, ey = p.y + vb.y * r;

        if (i === 0) shape.moveTo(sx, sy);
        else shape.lineTo(sx, sy);
        shape.quadraticCurveTo(p.x, p.y, ex, ey);
    }
    shape.closePath();
    return shape;
}

function letterShape(ch, w) {
    const h = LETTER_H, t = STEM, a = ARM, R = 0.055;

    if (ch === 'H') {
        return roundedShape([
            [0, 0], [t, 0], [t, (h - a) / 2], [w - t, (h - a) / 2], [w - t, 0], [w, 0],
            [w, h], [w - t, h], [w - t, (h + a) / 2], [t, (h + a) / 2], [t, h], [0, h],
        ], R);
    }
    if (ch === 'E') {
        return roundedShape([
            [0, 0], [w, 0], [w, a], [t, a], [t, (h - a) / 2], [w - 0.08, (h - a) / 2],
            [w - 0.08, (h + a) / 2], [t, (h + a) / 2], [t, h - a], [w, h - a], [w, h], [0, h],
        ], R);
    }
    if (ch === 'L') {
        return roundedShape([[0, 0], [w, 0], [w, a], [t, a], [t, h], [0, h]], R);
    }
    // O: elips dengan lubang di tengah
    const shape = new THREE.Shape();
    shape.absellipse(w / 2, h / 2, w / 2, h / 2, 0, TAU, false, 0);
    const hole = new THREE.Path();
    hole.absellipse(w / 2, h / 2, w / 2 - t, h / 2 - a * 0.95, 0, TAU, true, 0);
    shape.holes.push(hole);
    return shape;
}

function makeLetter(spec, material) {
    const geometry = new THREE.ExtrudeGeometry(letterShape(spec.ch, spec.w), {
        depth: DEPTH,
        bevelEnabled: true,
        bevelThickness: 0.055,
        bevelSize: 0.035,
        bevelOffset: 0,
        bevelSegments: 6,
        curveSegments: 32,
    });
    // Pivot di dasar-tengah huruf supaya mudah dianimasikan dan diletakkan di lantai
    geometry.computeBoundingBox();
    const b = geometry.boundingBox;
    geometry.translate(-(b.min.x + b.max.x) / 2, -b.min.y, -(b.min.z + b.max.z) / 2);
    geometry.computeBoundingBox();

    const mesh = new THREE.Mesh(geometry, material);
    mesh.castShadow = true;
    mesh.receiveShadow = true;

    const group = new THREE.Group();
    group.add(mesh);

    return {
        group, mesh, geometry, material,
        width: geometry.boundingBox.max.x - geometry.boundingBox.min.x,
        height: geometry.boundingBox.max.y,
    };
}

/* ======================================================================
   KARAKTER 3D BERTOPI: kerangka bersendi (pinggul, tulang belakang, bahu,
   siku, lutut) dari bentuk dasar. Gerakannya dihitung per frame.
====================================================================== */

function buildCharacter() {
    const mat = (color, roughness = 0.7, extra = {}) =>
        new THREE.MeshStandardMaterial({ color, roughness, metalness: 0, ...extra });

    const M = {
        skin: mat(0xf3c9a5, 0.65),
        hoodie: mat(0xf7f9fc, 0.85),
        pants: mat(0x2f3745, 0.85),
        shoe: mat(0xffffff, 0.5),
        cap: mat(0x78bfff, 0.55),
        capDark: mat(0x57a6ee, 0.55),
        hair: mat(0x2a2128, 0.8, { side: THREE.DoubleSide }),
        dark: mat(0x1b1b1f, 0.4),
        cheek: mat(0xff9fa0, 0.9),
    };

    const mesh = (geometry, material, parent, x = 0, y = 0, z = 0) => {
        const m = new THREE.Mesh(geometry, material);
        m.position.set(x, y, z);
        m.castShadow = true;
        m.receiveShadow = true;
        parent.add(m);
        return m;
    };

    const root = new THREE.Group();
    const pelvis = new THREE.Group();
    pelvis.position.y = HIP_Y;
    root.add(pelvis);

    const spine = new THREE.Group();   // pivot di pinggul: badan atas condong dari sini
    pelvis.add(spine);

    /* pinggul dan badan */
    const hips = mesh(new THREE.SphereGeometry(0.2, 28, 20), M.pants, pelvis, 0, 0.02, 0);
    hips.scale.set(1.15, 0.85, 0.9);
    const torso = mesh(new THREE.CapsuleGeometry(0.2, 0.3, 8, 24), M.hoodie, spine, 0, 0.3, 0);
    torso.scale.set(1, 1, 0.85);

    /* kepala */
    const neck = new THREE.Group();
    neck.position.y = 0.7;
    spine.add(neck);
    const head = new THREE.Group();
    neck.add(head);

    mesh(new THREE.SphereGeometry(0.3, 40, 28), M.skin, head, 0, 0.26, 0);
    const eyeL = mesh(new THREE.SphereGeometry(0.03, 16, 12), M.dark, head, -0.105, 0.25, 0.272);
    const eyeR = mesh(new THREE.SphereGeometry(0.03, 16, 12), M.dark, head, 0.105, 0.25, 0.272);
    const cheekL = mesh(new THREE.SphereGeometry(0.045, 16, 12), M.cheek, head, -0.17, 0.19, 0.235);
    const cheekR = mesh(new THREE.SphereGeometry(0.045, 16, 12), M.cheek, head, 0.17, 0.19, 0.235);
    cheekL.scale.set(1, 0.7, 0.4);
    cheekR.scale.set(1, 0.7, 0.4);
    const mouth = mesh(new THREE.TorusGeometry(0.045, 0.0085, 8, 20, Math.PI), M.dark, head, 0, 0.175, 0.285);
    mouth.rotation.z = Math.PI;   // busur menghadap ke bawah = senyum

    // Rambut: setengah bola bagian belakang saja
    mesh(new THREE.SphereGeometry(0.315, 36, 24, Math.PI, Math.PI), M.hair, head, 0, 0.25, -0.02);

    // Topi: mahkota setengah bola + lidah topi
    const crown = mesh(new THREE.SphereGeometry(0.322, 44, 22, 0, TAU, 0, Math.PI / 2), M.cap, head, 0, 0.33, 0);
    crown.scale.y = 0.85;
    mesh(new THREE.SphereGeometry(0.028, 12, 10), M.capDark, head, 0, 0.33 + 0.322 * 0.85, 0);
    const brim = mesh(
        new THREE.CylinderGeometry(0.27, 0.27, 0.024, 36, 1, false, -Math.PI / 2, Math.PI),
        M.capDark, head, 0, 0.325, 0.2,
    );
    brim.rotation.x = 0.14;

    /* lengan: bahu -> siku -> tangan */
    const makeArm = (side) => {
        const shoulder = new THREE.Group();
        shoulder.position.set(side * 0.27, 0.5, 0);
        spine.add(shoulder);
        mesh(new THREE.SphereGeometry(0.1, 20, 14), M.hoodie, shoulder);
        mesh(new THREE.CapsuleGeometry(0.082, 0.24, 6, 16), M.hoodie, shoulder, 0, -0.2, 0);
        const elbow = new THREE.Group();
        elbow.position.y = -L1;
        shoulder.add(elbow);
        mesh(new THREE.CapsuleGeometry(0.07, 0.23, 6, 16), M.hoodie, elbow, 0, -0.19, 0);
        mesh(new THREE.SphereGeometry(0.075, 20, 14), M.skin, elbow, 0, -L2, 0);
        return { shoulder, elbow };
    };

    /* kaki: pinggul -> lutut -> sepatu */
    const makeLeg = (side) => {
        const hip = new THREE.Group();
        hip.position.set(side * 0.11, -0.02, 0);
        pelvis.add(hip);
        mesh(new THREE.CapsuleGeometry(0.095, 0.3, 6, 16), M.pants, hip, 0, -0.23, 0);
        const knee = new THREE.Group();
        knee.position.y = -0.45;
        hip.add(knee);
        mesh(new THREE.CapsuleGeometry(0.08, 0.27, 6, 16), M.pants, knee, 0, -0.215, 0);
        const shoe = mesh(new THREE.CapsuleGeometry(0.075, 0.14, 6, 16), M.shoe, knee, 0, -0.43, 0.06);
        shoe.rotation.x = Math.PI / 2;
        return { hip, knee };
    };

    return {
        root, pelvis, spine, neck, head, eyes: [eyeL, eyeR],
        armP: makeArm(1),    // lengan di sisi +x (dekat huruf H)
        armM: makeArm(-1),   // lengan di sisi -x (untuk melambai)
        legP: makeLeg(1),
        legM: makeLeg(-1),
    };
}

/* ======================================================================
   IK lengan 2 ruas pada bidang XY: menghitung sudut bahu dan siku supaya
   tangan tepat mendarat di titik target (bagian atas huruf H).
   Sudut diukur dari arah "lurus ke bawah", berlawanan arah jarum jam.
====================================================================== */
export function solveArm(S, T, l1, l2, out) {
    const dx = T.x - S.x;
    const dy = T.y - S.y;
    const d = clamp(Math.hypot(dx, dy), Math.abs(l1 - l2) + 0.001, l1 + l2 - 0.001);
    const phi = Math.atan2(dx, -dy);
    const a = Math.acos(clamp((l1 * l1 + d * d - l2 * l2) / (2 * l1 * d), -1, 1));
    const b = Math.acos(clamp((l2 * l2 + d * d - l1 * l1) / (2 * l2 * d), -1, 1));
    out.upper = phi - a;   // sudut lengan atas (dunia); siku berada di bawah garis bahu-tangan
    out.fore = a + b;      // sudut lengan bawah relatif terhadap lengan atas
    return out;
}

/* ======================================================================
   PEMUTAR INTRO
====================================================================== */
function buildIntro(canvas, { caption = null, onComplete = () => {}, onError = () => {} } = {}) {
    let renderer;
    try {
        renderer = new THREE.WebGLRenderer({ canvas, antialias: true, alpha: true, powerPreference: 'high-performance' });
    } catch (e) {
        // Perangkat tanpa WebGL (atau akselerasi perangkat keras browser dimatikan)
        console.warn('[intro3d] WebGL tidak tersedia:', e);
        queueMicrotask(() => onError(e));
        return { destroy() {} };
    }

    renderer.setPixelRatio(Math.min(window.devicePixelRatio || 1, 2));
    renderer.setClearColor(0x000000, 0);
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFSoftShadowMap;
    renderer.toneMapping = THREE.NeutralToneMapping ?? THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1;

    const scene = new THREE.Scene();
    const camera = new THREE.PerspectiveCamera(24, 1, 0.1, 120);

    /* ---------- cahaya, lingkungan, dan lantai penangkap bayangan ---------- */
    const pmrem = new THREE.PMREMGenerator(renderer);
    const envTexture = pmrem.fromScene(new RoomEnvironment(), 0.04).texture;
    scene.environment = envTexture;
    if ('environmentIntensity' in scene) scene.environmentIntensity = 0.55;

    scene.add(new THREE.HemisphereLight(0xffffff, 0xcfe6ff, 0.6));

    const key = new THREE.DirectionalLight(0xfff3e4, 2.6);
    key.position.set(-4, 5.5, 2.8);
    key.castShadow = true;
    key.shadow.mapSize.set(2048, 2048);
    key.shadow.camera.left = -8;
    key.shadow.camera.right = 8;
    key.shadow.camera.top = 6;
    key.shadow.camera.bottom = -4;
    key.shadow.camera.near = 0.5;
    key.shadow.camera.far = 24;
    key.shadow.camera.updateProjectionMatrix();
    key.shadow.bias = -0.0004;
    key.shadow.normalBias = 0.03;
    scene.add(key, key.target);

    const rim = new THREE.DirectionalLight(0x8fd0ff, 1.7);   // pantulan biru muda di tepi huruf
    rim.position.set(4, 2.5, -4.5);
    scene.add(rim);

    const floor = new THREE.Mesh(new THREE.PlaneGeometry(60, 60), new THREE.ShadowMaterial({ opacity: 0.17 }));
    floor.rotation.x = -Math.PI / 2;
    floor.receiveShadow = true;
    scene.add(floor);

    /* ---------- huruf HELLO ---------- */
    const letters = LETTERS.map((spec) => {
        const material = new THREE.MeshPhysicalMaterial({
            color: 0x0c0d10, roughness: 0.32, metalness: 0.15,
            clearcoat: 0.7, clearcoatRoughness: 0.22,
            transparent: true, opacity: 0,
        });
        return makeLetter(spec, material);
    });

    let cursor = 0;
    letters.forEach((l) => {
        l.x = cursor + l.width / 2;
        l.group.position.set(l.x, 0, 0);
        scene.add(l.group);
        cursor += l.width + GAP;
    });
    const wordLeft = 0;
    const wordRight = cursor - GAP;

    // Titik mendaratnya tangan: permukaan atas batang kiri huruf H
    const H = letters[0];
    const target = new THREE.Vector3(H.x - H.width / 2 + 0.035 + STEM / 2, H.height + 0.055, 0);

    /* ---------- karakter ---------- */
    const rig = buildCharacter();
    scene.add(rig.root);
    const { armP, armM, legP, legM, spine, pelvis, head } = rig;

    // State yang dianimasikan GSAP (bobot 0..1 untuk tiap fase gerak)
    const state = { walk: 0, wave: 0, lean: 0, cam: 0, look: 0 };
    const ik = { upper: 0, fore: 0 };
    const vShoulder = new THREE.Vector3();
    const vHead = new THREE.Vector3();
    let phase = 0, prevX = 0, speed = 0, originX = 0;

    function applyPose(time, dt) {
        const x = rig.root.position.x;
        speed = dt > 0 ? lerp(speed, Math.abs(x - prevX) / dt, 1 - Math.exp(-dt * 12)) : speed;
        prevX = x;
        phase = ((x - originX) / STRIDE) * TAU;

        const w = state.walk * clamp(speed / WALK_SPEED, 0, 1);   // ayunan mengecil saat melambat
        const s = Math.sin(phase), c = Math.cos(phase);
        const wv = state.wave, ln = state.lean;

        // Pinggul naik-turun halus mengikuti langkah, plus napas saat diam
        pelvis.position.y = HIP_Y - 0.06 * w * Math.abs(s);
        pelvis.position.x = 0.07 * ln;
        pelvis.rotation.y = 0.12 * w * s;
        spine.rotation.y = -0.1 * w * s;
        spine.rotation.z = -0.11 * ln + 0.03 * w * c;
        spine.scale.y = 1 + 0.008 * Math.sin(time * 2.2);

        // Kaki: ayun depan-belakang, lutut menekuk saat kaki terangkat
        legP.hip.rotation.x = -0.42 * w * s;
        legM.hip.rotation.x = 0.42 * w * s;
        // Lutut menekuk paling dalam di tengah ayunan dan lurus kembali saat kaki sudah di depan
        legP.knee.rotation.x = 0.8 * w * Math.max(0, c) * clamp(1 - s, 0, 1);
        legM.knee.rotation.x = 0.8 * w * Math.max(0, -c) * clamp(1 + s, 0, 1);
        legP.hip.rotation.z = 0.05;
        legM.hip.rotation.z = -0.05 - 0.1 * ln;

        // Lengan: ayunan berlawanan dengan kaki, siku sedikit menekuk
        armP.shoulder.rotation.set(0.5 * w * s, 0, 0.07);
        armM.shoulder.rotation.set(-0.5 * w * s, 0, -0.07);
        armP.elbow.rotation.set(-0.45 * w, 0, 0);
        armM.elbow.rotation.set(-0.45 * w, 0, 0);

        // Melambai: lengan sisi -x terangkat, lengan bawah bergoyang
        if (wv > 0.001) {
            armM.shoulder.rotation.x = lerp(armM.shoulder.rotation.x, 0, wv);
            armM.shoulder.rotation.z = lerp(-0.07, -2.35, wv);
            armM.elbow.rotation.x = lerp(armM.elbow.rotation.x, 0, wv);
            armM.elbow.rotation.z = wv * (-0.78 + 0.42 * Math.sin(time * 9.5));
        }

        // Bersandar: lengan sisi +x memakai IK supaya tangan tepat di atas huruf H
        if (ln > 0.001) {
            rig.root.updateMatrixWorld(true);
            armP.shoulder.getWorldPosition(vShoulder);
            solveArm(vShoulder, target, L1, L2, ik);
            armP.shoulder.rotation.x = lerp(armP.shoulder.rotation.x, 0, ln);
            armP.shoulder.rotation.z = lerp(0.07, ik.upper - spine.rotation.z, ln);
            armP.elbow.rotation.x = lerp(armP.elbow.rotation.x, 0, ln);
            armP.elbow.rotation.z = lerp(0, ik.fore, ln);
        }

        // Kepala: miring sedikit saat melambai dan bersandar, lalu menoleh menatap kamera (penonton)
        head.rotation.z = 0.07 * wv + 0.1 * ln;
        head.rotation.x = 0.03 * Math.sin(time * 1.7) * (1 - w);
        head.rotation.y = 0;
        if (state.look > 0.001) {
            head.getWorldPosition(vHead);
            const toCam = Math.atan2(camera.position.x - vHead.x, camera.position.z - vHead.z);
            head.rotation.y = clamp(toCam - rig.root.rotation.y, -0.9, 0.9) * state.look;
            // sedikit mendongak/menunduk mengikuti tinggi kamera
            const flat = Math.hypot(camera.position.x - vHead.x, camera.position.z - vHead.z);
            head.rotation.x -= clamp(Math.atan2(camera.position.y - vHead.y, flat), -0.3, 0.3) * state.look;
        }
        const blink = (time % 3.4) > 3.28 ? 0.1 : 1;
        rig.eyes.forEach((e) => { e.scale.y = blink; });
    }

    // Posisi berdiri: dihitung agar jarak bahu ke tangan pas saat bersandar penuh
    function computeStandX() {
        const saved = { x: rig.root.position.x, ry: rig.root.rotation.y, sz: spine.rotation.z, px: pelvis.position.x };
        rig.root.position.x = 0;
        rig.root.rotation.y = 0;
        spine.rotation.z = -0.11;     // kemiringan badan saat bersandar penuh
        pelvis.position.x = 0.07;
        rig.root.updateMatrixWorld(true);
        armP.shoulder.getWorldPosition(vShoulder);
        const shoulderX = vShoulder.x;
        rig.root.position.x = saved.x;
        rig.root.rotation.y = saved.ry;
        spine.rotation.z = saved.sz;
        pelvis.position.x = saved.px;
        const reachDx = 0.52;         // jarak mendatar bahu -> tangan
        return target.x - reachDx - shoulderX;
    }
    const xStand = computeStandX();

    /* ---------- kamera: muat di semua rasio layar ---------- */
    const view = { dist: 8, visibleHalfW: 4, cx: 0 };
    const compLeft = Math.min(xStand - 0.5, wordLeft);
    const compRight = wordRight;
    view.cx = (compLeft + compRight) / 2;

    function fit() {
        const w = canvas.clientWidth || window.innerWidth;
        const h = canvas.clientHeight || window.innerHeight;
        renderer.setSize(w, h, false);
        camera.aspect = w / h;
        const tanH = Math.tan(THREE.MathUtils.degToRad(camera.fov / 2));
        const halfW = (compRight - compLeft) / 2 + 0.8;
        const halfH = 1.5;
        view.dist = Math.max(halfW / (tanH * camera.aspect), halfH / tanH) + 0.9;
        view.visibleHalfW = tanH * camera.aspect * view.dist;
        camera.updateProjectionMatrix();
        key.target.position.set(view.cx, 0, 0);
        key.target.updateMatrixWorld();
    }
    fit();

    function updateCamera() {
        const k = state.cam;
        const dist = view.dist * (1 - 0.05 * k);                 // mendekat pelan
        // Kamera tetap di tengah komposisi (supaya tulisan HELLO tidak terlihat miring), hanya bergoyang halus
        camera.position.set(view.cx + lerp(-0.03, 0.03, k) * view.dist, 1.5 + view.dist * 0.02, dist);   // geser sekitar 2 derajat saja
        camera.lookAt(view.cx, 1.0, 0);
    }

    /* ---------- linimasa GSAP ---------- */
    const xStart = view.cx - view.visibleHalfW - 2.2;
    originX = xStart;
    rig.root.position.set(xStart, 0, 0);
    rig.root.rotation.y = Math.PI / 2;          // menghadap +x saat berjalan
    prevX = xStart;

    const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });

    // 1) Huruf muncul satu per satu: fade-in + scale-up + gerak posisi (stagger)
    letters.forEach((l, i) => {
        const at = 0.3 + i * 0.15;
        tl.fromTo(l.group.scale, { x: 0.55, y: 0.55, z: 0.55 }, { x: 1, y: 1, z: 1, duration: 0.95, ease: 'back.out(1.7)' }, at)
          .fromTo(l.group.position, { y: -0.5, z: -1.1 }, { y: 0, z: 0, duration: 0.95, ease: 'power3.out' }, at)
          .fromTo(l.group.rotation, { x: 0.55 }, { x: 0, duration: 1.0, ease: 'power3.out' }, at)
          .fromTo(l.material, { opacity: 0 }, { opacity: 1, duration: 0.6, ease: 'power2.out' }, at);
    });
    tl.call(() => letters.forEach((l) => { l.material.transparent = false; l.material.opacity = 1; l.material.needsUpdate = true; }), null, 0.3 + 4 * 0.15 + 1.1);

    // 2) Karakter masuk dari kiri dan berjalan menuju huruf H
    const tWalk = 0.8;
    const x1 = xStand - 0.9;
    const t1 = (x1 - xStart) / WALK_SPEED;
    tl.set(state, { walk: 1 }, tWalk)
      .to(rig.root.position, { x: x1, duration: t1, ease: 'none' }, tWalk)
      .to(rig.root.position, { x: xStand, duration: 0.9, ease: 'power2.out' }, tWalk + t1);

    // 3) Berhenti, menghadap kamera
    const tStop = tWalk + t1 + 0.9;
    tl.to(state, { walk: 0, duration: 0.3, ease: 'power1.out' }, tStop - 0.3)
      .to(rig.root.rotation, { y: 0, duration: 0.75, ease: 'power2.inOut' }, tStop - 0.5);

    // Kepala menoleh menatap penonton begitu karakter berhenti dan menghadap kamera
    tl.to(state, { look: 1, duration: 0.7, ease: 'power2.inOut' }, tStop - 0.2);

    // 4) Melambaikan tangan
    const tWave = tStop + 0.3;
    tl.to(state, { wave: 1, duration: 0.55, ease: 'power2.out' }, tWave)
      .to(state, { wave: 0, duration: 0.6, ease: 'power2.inOut' }, tWave + 0.55 + 1.0);

    // 5) Bersandar di huruf H (huruf H sedikit "terima" beban)
    const tLean = tWave + 0.55 + 0.95;
    tl.to(state, { lean: 1, duration: 1.0, ease: 'power2.inOut' }, tLean)
      .to(H.group.scale, { x: 1.012, y: 0.988, duration: 0.18, ease: 'power1.out', yoyo: true, repeat: 1 }, tLean + 0.8);

    // Kamera bergerak pelan sepanjang adegan supaya terasa ada kedalaman 3D
    const tEnd = tLean + 1.0 + 1.0;
    tl.to(state, { cam: 1, duration: tEnd, ease: 'sine.inOut' }, 0);

    if (caption) {
        tl.fromTo(caption, { opacity: 0, y: 10 }, { opacity: 1, y: 0, duration: 0.8, ease: 'power2.out' }, tLean + 0.3);
    }

    tl.call(() => onComplete(), null, tEnd);

    /* ---------- render loop (disinkronkan dengan ticker GSAP) ---------- */
    let failed = false;
    const tick = (time, deltaTime) => {
        if (failed) return;
        try {
            applyPose(time, (deltaTime || 16) / 1000);
            updateCamera();
            renderer.render(scene, camera);
        } catch (err) {
            failed = true;
            console.error('[intro3d] error saat menggambar frame:', err);
            gsap.ticker.remove(tick);
            tl.kill();
            onError(err);
        }
    };
    gsap.ticker.add(tick);

    const onResize = () => fit();
    window.addEventListener('resize', onResize);

    return {
        _debug: { rig, state, target, letters, xStand, camera },   // dipakai untuk pengujian
        destroy() {
            tl.kill();
            gsap.ticker.remove(tick);
            window.removeEventListener('resize', onResize);
            scene.traverse((o) => {
                if (o.geometry) o.geometry.dispose();
                if (o.material) (Array.isArray(o.material) ? o.material : [o.material]).forEach((m) => m.dispose());
            });
            envTexture.dispose();
            pmrem.dispose();
            renderer.dispose();
        },
    };
}

// Pintu masuk: semua kegagalan dilaporkan lewat onError (dan console), tidak pernah diam-diam
export function playIntro(canvas, opts = {}) {
    const fail = opts.onError || opts.onComplete || (() => {});
    try {
        return buildIntro(canvas, { ...opts, onError: fail });
    } catch (err) {
        console.error('[intro3d] gagal menyiapkan adegan 3D:', err);
        fail(err);
        return { destroy() {} };
    }
}