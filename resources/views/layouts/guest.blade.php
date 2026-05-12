<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'OceanBI') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --teal:  #0891b2;
            --teal2: #06b6d4;
            --teal3: #67e8f9;
            --navy:  #020e1a;
            --navy2: #041628;
            --accent: #38bdf8;
            --text:   #e2f7ff;
            --text2:  #94d5e8;
            --text3:  #5ba8be;
            --border: rgba(56,189,248,0.18);
            --border2: rgba(56,189,248,0.09);
            --glass: rgba(4,22,40,0.72);
            --glass2: rgba(7,30,55,0.60);
        }

        html, body {
            height: 100%; width: 100%;
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: 15px;
            background: var(--navy);
            color: var(--text);
            overflow: hidden;
        }

        /* ── CANVAS LAYER ── */
        #ocean-canvas {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
        }

        /* ── FLOATING PARTICLES ── */
        .particles {
            position: fixed; top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 1; pointer-events: none;
            overflow: hidden;
        }
        .particle {
            position: absolute; border-radius: 50%;
            background: radial-gradient(circle at 35% 35%,
                rgba(56,189,248,0.55), rgba(56,189,248,0.05));
            animation: floatUp linear infinite;
        }
        @keyframes floatUp {
            0%   { transform: translateY(105vh) translateX(0) scale(0); opacity: 0; }
            6%   { opacity: 0.8; }
            50%  { transform: translateY(50vh) translateX(20px) scale(1); }
            94%  { opacity: 0.3; }
            100% { transform: translateY(-60px) translateX(-10px) scale(0.6); opacity: 0; }
        }

        /* ── JELLYFISH DECORATION ── */
        .jelly {
            position: fixed; pointer-events: none; z-index: 1;
            animation: jellyFloat ease-in-out infinite;
            opacity: 0.13;
        }
        @keyframes jellyFloat {
            0%, 100% { transform: translateY(0px) rotate(-3deg); }
            50%       { transform: translateY(-30px) rotate(3deg); }
        }
        .jelly svg { display: block; }

        /* ── LIGHT RAYS ── */
        .rays {
            position: fixed; top: -10%; left: 50%;
            transform: translateX(-50%);
            width: 140%; height: 70%;
            pointer-events: none; z-index: 1;
        }
        .ray {
            position: absolute; top: 0;
            width: 60px; height: 100%;
            background: linear-gradient(180deg,
                rgba(56,189,248,0.10) 0%,
                rgba(56,189,248,0.02) 60%,
                transparent 100%);
            transform-origin: top center;
            animation: rayWave ease-in-out infinite;
            border-radius: 0 0 100% 100%;
        }
        @keyframes rayWave {
            0%, 100% { opacity: 0.7; transform: rotate(var(--r)) scaleX(1); }
            50%       { opacity: 1;   transform: rotate(calc(var(--r) + 2deg)) scaleX(1.08); }
        }

        /* ── MAIN LAYOUT ── */
        .page {
            position: relative; z-index: 10;
            min-height: 100vh; width: 100%;
            display: flex; align-items: center; justify-content: center;
            padding: 24px;
        }

        /* ── BRAND STRIP ── */
        .brand {
            position: fixed; top: 28px; left: 50%;
            transform: translateX(-50%);
            display: flex; align-items: center; gap: 12px;
            z-index: 20;
        }
        .brand-icon {
            width: 44px; height: 44px; border-radius: 12px;
            background: linear-gradient(135deg, #0891b2, #06b6d4);
            display: flex; align-items: center; justify-content: center;
            font-size: 22px;
            box-shadow: 0 0 28px rgba(6,182,212,0.55);
        }
        .brand-name {
            font-size: 22px; font-weight: 700; color: #e0f2fe;
            letter-spacing: -0.5px;
        }
        .brand-name span { color: var(--teal2); }
        .brand-sub {
            font-size: 10px; color: var(--text3);
            letter-spacing: 1.4px; text-transform: uppercase;
            display: block; margin-top: -1px;
        }

        /* ── GLASS CARD ── */
        .glass-card {
            width: 100%; max-width: 430px;
            background: var(--glass);
            border: 1px solid var(--border);
            border-radius: 22px;
            box-shadow:
                0 8px 48px rgba(0,0,0,0.6),
                inset 0 1px 0 rgba(56,189,248,0.12),
                0 0 80px rgba(6,182,212,0.06);
            backdrop-filter: blur(32px) saturate(160%);
            padding: 42px 40px 36px;
            position: relative;
            overflow: hidden;
            margin-top: 50px;
        }
        /* top glow line */
        .glass-card::before {
            content: '';
            position: absolute; top: 0; left: 0; right: 0; height: 1px;
            background: linear-gradient(90deg,
                transparent 0%, rgba(56,189,248,0.5) 40%,
                rgba(6,182,212,0.7) 60%, transparent 100%);
        }
        /* inner shimmer */
        .glass-card::after {
            content: '';
            position: absolute; top: -60%; left: -40%;
            width: 80%; height: 80%;
            background: radial-gradient(ellipse at center,
                rgba(56,189,248,0.07) 0%, transparent 70%);
            pointer-events: none;
        }

        .card-header-block {
            text-align: center; margin-bottom: 30px;
        }
        .card-title {
            font-size: 24px; font-weight: 700;
            color: var(--text); letter-spacing: -0.5px;
            margin-bottom: 6px;
        }
        .card-subtitle {
            font-size: 13px; color: var(--text3); line-height: 1.5;
        }

        /* ── FORM ELEMENTS ── */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block; font-size: 12px; font-weight: 600;
            color: var(--text2); margin-bottom: 7px;
            letter-spacing: 0.3px; text-transform: uppercase;
        }
        .input-wrap { position: relative; }
        .input-icon {
            position: absolute; left: 13px; top: 50%;
            transform: translateY(-50%);
            font-size: 15px; color: var(--text3);
            pointer-events: none; z-index: 1;
        }
        .form-control {
            width: 100%;
            background: rgba(56,189,248,0.07);
            border: 1px solid var(--border2);
            border-radius: 10px;
            padding: 12px 14px 12px 40px;
            font-size: 15px;
            font-family: 'Space Grotesk', sans-serif;
            color: var(--text);
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            -webkit-appearance: none;
        }
        .form-control:focus {
            border-color: var(--teal2);
            background: rgba(56,189,248,0.11);
            box-shadow: 0 0 0 3px rgba(6,182,212,0.18);
        }
        .form-control::placeholder { color: var(--text3); font-size: 14px; }
        .form-control:-webkit-autofill,
        .form-control:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px rgba(4,22,48,0.95) inset;
            -webkit-text-fill-color: var(--text);
            transition: background-color 9999s ease-in-out 0s;
        }

        /* checkbox row */
        .check-row {
            display: flex; align-items: center; gap: 8px;
            font-size: 13px; color: var(--text2);
            cursor: pointer;
        }
        .check-row input[type=checkbox] {
            width: 15px; height: 15px; accent-color: var(--teal2); cursor: pointer;
        }

        /* ── SUBMIT BUTTON ── */
        .btn-submit {
            width: 100%; padding: 13px;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            border: none; border-radius: 11px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: 15px; font-weight: 700; color: #fff;
            cursor: pointer; letter-spacing: 0.3px;
            transition: all 0.22s;
            box-shadow: 0 4px 20px rgba(6,182,212,0.35);
            margin-top: 6px;
            position: relative; overflow: hidden;
        }
        .btn-submit::before {
            content: '';
            position: absolute; top: 0; left: -100%;
            width: 100%; height: 100%;
            background: linear-gradient(90deg,
                transparent, rgba(255,255,255,0.12), transparent);
            transition: left 0.4s;
        }
        .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 28px rgba(6,182,212,0.48); }
        .btn-submit:hover::before { left: 100%; }
        .btn-submit:active { transform: translateY(0); }

        /* ── DIVIDER & LINKS ── */
        .form-divider {
            display: flex; align-items: center; gap: 10px;
            margin: 18px 0; color: var(--text3); font-size: 12px;
        }
        .form-divider::before, .form-divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border2);
        }
        .link {
            color: var(--teal2); text-decoration: none; font-weight: 600;
            font-size: 13px; transition: color 0.18s;
        }
        .link:hover { color: var(--teal3); }

        /* ── ERROR / STATUS ── */
        .alert {
            padding: 11px 16px; border-radius: 9px;
            font-size: 13px; margin-bottom: 18px;
            display: flex; align-items: center; gap: 8px;
        }
        .alert-success {
            background: rgba(16,185,129,0.12); color: #34d399;
            border: 1px solid rgba(16,185,129,0.22);
        }
        .alert-error {
            background: rgba(239,68,68,0.12); color: #f87171;
            border: 1px solid rgba(239,68,68,0.22);
        }
        .field-error { font-size: 12px; color: #f87171; margin-top: 5px; }

        /* ── ROW HELPERS ── */
        .row-between {
            display: flex; align-items: center; justify-content: space-between;
            margin-bottom: 18px;
        }
        .center-text { text-align: center; }
        .mt-4 { margin-top: 16px; }

        /* ── BOTTOM BADGE ── */
        .sea-badge {
            position: fixed; bottom: 18px; left: 50%;
            transform: translateX(-50%);
            font-size: 11px; color: var(--text3);
            display: flex; align-items: center; gap: 5px;
            z-index: 20; white-space: nowrap;
        }
        .sea-badge span { color: var(--teal2); }
    </style>
</head>
<body>

<!-- ── UNDERWATER CANVAS ── -->
<canvas id="ocean-canvas"></canvas>

<!-- ── PARTICLES ── -->
<div class="particles" id="particles"></div>

<!-- ── LIGHT RAYS ── -->
<div class="rays" id="rays"></div>

<!-- ── JELLYFISH ── -->
<div class="jelly" style="right:8%;top:15%;animation-duration:8s;width:90px;">
    <svg width="90" height="90" viewBox="0 0 90 90" fill="none">
        <ellipse cx="45" cy="34" rx="32" ry="26" fill="rgba(6,182,212,0.7)"/>
        <ellipse cx="45" cy="30" rx="20" ry="14" fill="rgba(103,232,249,0.25)"/>
        <line x1="30" y1="60" x2="28" y2="88" stroke="rgba(6,182,212,0.5)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="38" y1="60" x2="35" y2="90" stroke="rgba(56,189,248,0.4)" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="45" y1="60" x2="45" y2="86" stroke="rgba(6,182,212,0.5)" stroke-width="1.5" stroke-linecap="round"/>
        <line x1="52" y1="60" x2="55" y2="90" stroke="rgba(56,189,248,0.4)" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="60" y1="60" x2="62" y2="88" stroke="rgba(6,182,212,0.5)" stroke-width="1.5" stroke-linecap="round"/>
    </svg>
</div>
<div class="jelly" style="left:5%;bottom:22%;animation-duration:11s;animation-delay:-4s;width:60px;opacity:0.09;">
    <svg width="60" height="60" viewBox="0 0 60 60" fill="none">
        <ellipse cx="30" cy="22" rx="21" ry="17" fill="rgba(56,189,248,0.7)"/>
        <line x1="20" y1="39" x2="18" y2="58" stroke="rgba(6,182,212,0.5)" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="30" y1="39" x2="30" y2="58" stroke="rgba(6,182,212,0.5)" stroke-width="1.2" stroke-linecap="round"/>
        <line x1="40" y1="39" x2="42" y2="58" stroke="rgba(6,182,212,0.5)" stroke-width="1.2" stroke-linecap="round"/>
    </svg>
</div>

<!-- ── BRAND ── -->
<div class="brand">
    <div class="brand-icon">🌊</div>
    <div>
        <div class="brand-name">Ocean<span>BI</span></div>
        <span class="brand-sub">Plastic Intelligence System</span>
    </div>
</div>

<!-- ── MAIN PAGE ── -->
<div class="page">
    <div class="glass-card">
        {{ $slot }}
    </div>
</div>

<!-- ── BOTTOM BADGE ── -->
<div class="sea-badge">
    UAS Business Intelligence · Kelompok 6 · Sistem Informasi ·
    <span>Universitas Mulawarman 2026</span>
</div>

<script>
/* ── UNDERWATER CANVAS ── */
(function(){
    const canvas = document.getElementById('ocean-canvas');
    const ctx = canvas.getContext('2d');
    let W, H;

    function resize(){
        W = canvas.width  = window.innerWidth;
        H = canvas.height = window.innerHeight;
    }
    resize();
    window.addEventListener('resize', resize);

    // Fish
    const fish = Array.from({length:9}, () => ({
        x: Math.random() * W,
        y: Math.random() * H * 0.8 + H * 0.1,
        vx: (Math.random() * 0.6 + 0.25) * (Math.random()<0.5 ? 1 : -1),
        vy: (Math.random() - 0.5) * 0.15,
        size: Math.random() * 8 + 5,
        alpha: Math.random() * 0.18 + 0.06,
        phase: Math.random() * Math.PI * 2
    }));

    // Seaweed anchors
    const weeds = Array.from({length: 7}, () => ({
        x: Math.random() * W,
        height: Math.random() * 80 + 60,
        phase: Math.random() * Math.PI * 2,
        width: Math.random() * 4 + 3
    }));

    let t = 0;

    function drawFish(f) {
        const dir = f.vx > 0 ? 1 : -1;
        ctx.save();
        ctx.translate(f.x, f.y);
        ctx.scale(dir, 1);
        ctx.globalAlpha = f.alpha;
        ctx.fillStyle = '#06b6d4';
        ctx.beginPath();
        ctx.ellipse(0, 0, f.size, f.size * 0.45, 0, 0, Math.PI * 2);
        ctx.fill();
        // Tail
        ctx.beginPath();
        ctx.moveTo(-f.size, 0);
        ctx.lineTo(-f.size * 1.7, -f.size * 0.5);
        ctx.lineTo(-f.size * 1.7, f.size * 0.5);
        ctx.closePath();
        ctx.fill();
        ctx.restore();
    }

    function drawWeed(w, t) {
        const segs = 8;
        ctx.strokeStyle = 'rgba(6,182,212,0.15)';
        ctx.lineWidth = w.width;
        ctx.lineCap = 'round';
        ctx.beginPath();
        let px = w.x, py = H;
        ctx.moveTo(px, py);
        for (let i = 1; i <= segs; i++) {
            const frac = i / segs;
            const wave = Math.sin(t * 0.8 + w.phase + frac * 2) * 18 * frac;
            px = w.x + wave;
            py = H - (w.height * frac);
            ctx.lineTo(px, py);
        }
        ctx.stroke();
    }

    function frame() {
        t += 0.012;
        ctx.clearRect(0, 0, W, H);

        // Background gradient
        const grad = ctx.createLinearGradient(0, 0, 0, H);
        grad.addColorStop(0,   '#010b18');
        grad.addColorStop(0.3, '#031522');
        grad.addColorStop(0.7, '#042232');
        grad.addColorStop(1,   '#062a3c');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, W, H);

        // Seaweed
        weeds.forEach(w => drawWeed(w, t));

        // Fish
        fish.forEach(f => {
            f.x += f.vx;
            f.y += f.vy + Math.sin(t + f.phase) * 0.08;
            if (f.x > W + 40)  f.x = -40;
            if (f.x < -40)     f.x = W + 40;
            f.y = Math.max(H * 0.05, Math.min(H * 0.9, f.y));
            drawFish(f);
        });

        // Sand bottom
        const sandGrad = ctx.createLinearGradient(0, H - 50, 0, H);
        sandGrad.addColorStop(0, 'rgba(4,36,58,0)');
        sandGrad.addColorStop(1, 'rgba(6,42,62,0.9)');
        ctx.fillStyle = sandGrad;
        ctx.fillRect(0, H - 50, W, 50);

        requestAnimationFrame(frame);
    }
    frame();
})();

/* ── LIGHT RAYS ── */
(function(){
    const c = document.getElementById('rays');
    const positions = [-38,-22,-10,2,14,26,40];
    positions.forEach((deg, i) => {
        const r = document.createElement('div');
        r.className = 'ray';
        const delay = i * 0.7;
        const dur   = 4 + i * 0.5;
        r.style.cssText = `left:${28 + i*7}%;--r:${deg}deg;`
            + `animation-duration:${dur}s;animation-delay:-${delay}s;`
            + `width:${50 + i*4}px;opacity:${0.5 + i*0.07};`;
        c.appendChild(r);
    });
})();

/* ── BUBBLES ── */
(function(){
    const p = document.getElementById('particles');
    for (let i = 0; i < 20; i++) {
        const b = document.createElement('div');
        b.className = 'particle';
        const s = Math.random() * 6 + 2;
        b.style.cssText = `width:${s}px;height:${s}px;left:${Math.random()*100}%;`
            + `animation-duration:${Math.random()*16+10}s;`
            + `animation-delay:-${Math.random()*16}s;`;
        p.appendChild(b);
    }
})();
</script>
</body>
</html>