<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'OceanBI') }} — @yield('title', 'Dashboard')</title>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            /* Ocean palette */
            --navy:   #040d1a;
            --navy2:  #071428;
            --navy3:  #0a1e3c;
            --sea:    #0c2a4a;
            --sea2:   #0f3460;
            --teal:   #0891b2;
            --teal2:  #06b6d4;
            --teal3:  #67e8f9;
            --foam:   #e0f2fe;
            --foam2:  #f0f9ff;

            /* Text */
            --text-primary:   #e2f7ff;
            --text-secondary: #94d5e8;
            --text-muted:     #5ba8be;

            /* Accent */
            --accent:       #38bdf8;
            --accent-hover: #7dd3fc;
            --accent-dim:   rgba(56,189,248,0.12);
            --accent-glow:  rgba(56,189,248,0.25);

            /* Status */
            --success: #10b981;
            --danger:  #ef4444;
            --warning: #f59e0b;
            --purple:  #8b5cf6;

            /* Surface */
            --surface:  rgba(10,30,60,0.92);
            --surface2: rgba(13,40,75,0.88);
            --border:   rgba(56,189,248,0.14);
            --border2:  rgba(56,189,248,0.07);

            /* Font size scale — raised 5px from before */
            --fs-xs:   11px;
            --fs-sm:   13px;
            --fs-base: 15px;
            --fs-md:   17px;
            --fs-lg:   20px;
            --fs-xl:   24px;

            /* Radius */
            --r:    8px;
            --r-lg: 14px;
            --r-xl: 18px;

            /* Shadow */
            --shadow-sm: 0 1px 8px rgba(0,0,0,0.35);
            --shadow:    0 4px 24px rgba(0,0,0,0.45);
        }

        html { font-size: 15px; }

        body {
            font-family: 'Space Grotesk', system-ui, sans-serif;
            font-size: var(--fs-base);
            background: var(--navy);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
            overflow-x: hidden;
        }

        /* ─── OCEAN BACKGROUND ─── */
        .ocean-bg {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(180deg,#020e1a 0%,#04203a 45%,#082e4a 80%,#0a3550 100%);
            z-index: 0;
            overflow: hidden;
            pointer-events: none;
        }
        .wave {
            position: absolute; width: 220%; height: 220px;
            background: rgba(8,145,178,0.055);
            border-radius: 40%;
            animation: waveFloat linear infinite;
        }
        .wave:nth-child(1){ bottom: 0; animation-duration: 20s; }
        .wave:nth-child(2){ bottom: 18px; animation-duration: 26s; animation-delay: -7s; background: rgba(6,182,212,0.035); }
        .wave:nth-child(3){ bottom: 36px; animation-duration: 34s; animation-delay: -14s; background: rgba(56,189,248,0.025); }
        @keyframes waveFloat {
            0%   { transform: translateX(-55%) rotate(0deg); }
            100% { transform: translateX(-5%)  rotate(360deg); }
        }
        .bubble {
            position: absolute; border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, rgba(56,189,248,0.45), rgba(56,189,248,0.08));
            animation: bubbleRise linear infinite;
        }
        @keyframes bubbleRise {
            0%   { transform: translateY(110vh) scale(0); opacity: 0; }
            8%   { opacity: 0.7; }
            92%  { opacity: 0.3; }
            100% { transform: translateY(-80px) scale(1.2); opacity: 0; }
        }

        /* ─── LAYOUT ─── */
        .wrapper { position: relative; z-index: 1; display: flex; width: 100%; min-height: 100vh; }

        /* ─── SIDEBAR ─── */
        .sidebar {
            width: 245px; min-height: 100vh; flex-shrink: 0;
            background: rgba(7,20,40,0.92);
            border-right: 1px solid var(--border);
            display: flex; flex-direction: column;
            position: sticky; top: 0; height: 100vh;
            backdrop-filter: blur(24px);
            overflow: hidden;
        }
        .sidebar-logo {
            padding: 22px 20px 18px;
            border-bottom: 1px solid var(--border2);
        }
        .logo-row {
            display: flex; align-items: center; gap: 11px; margin-bottom: 10px;
        }
        .logo-icon {
            width: 40px; height: 40px; border-radius: 11px;
            background: linear-gradient(135deg, #0891b2 0%, #06b6d4 100%);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px; flex-shrink: 0;
            box-shadow: 0 0 20px rgba(6,182,212,0.4);
        }
        .logo-text {
            font-size: 17px; font-weight: 700; color: #e0f2fe;
            letter-spacing: -0.5px; line-height: 1.2;
        }
        .logo-text span { color: var(--teal2); }
        .logo-sub {
            font-size: 10px; color: var(--text-muted);
            letter-spacing: 1.2px; text-transform: uppercase; font-weight: 500;
        }

        .sidebar-meta {
            font-size: 10px; color: var(--text-muted);
            padding: 7px 20px;
            background: rgba(56,189,248,0.06);
            border-bottom: 1px solid var(--border2);
            line-height: 1.5;
        }

        .sidebar-nav { padding: 14px 10px; flex: 1; overflow-y: auto; }
        .sidebar-nav::-webkit-scrollbar { width: 3px; }
        .sidebar-nav::-webkit-scrollbar-track { background: transparent; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

        .nav-label {
            font-size: 10px; font-weight: 700; letter-spacing: 1px;
            text-transform: uppercase; color: var(--text-muted);
            padding: 10px 10px 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: var(--r);
            text-decoration: none; font-size: var(--fs-sm); font-weight: 500;
            color: var(--text-secondary);
            transition: all 0.18s; margin-bottom: 2px; cursor: pointer;
        }
        .nav-item:hover { background: var(--accent-dim); color: var(--accent); }
        .nav-item.active { background: rgba(56,189,248,0.16); color: var(--accent); }
        .nav-item .nav-ico { font-size: 16px; width: 20px; text-align: center; flex-shrink: 0; }
        .nav-item .nav-badge {
            margin-left: auto; font-size: 9px; font-weight: 700;
            padding: 1px 6px; border-radius: 10px;
            background: rgba(239,68,68,0.2); color: #f87171;
        }

        .sidebar-footer {
            padding: 14px; border-top: 1px solid var(--border2);
        }
        .user-pill {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: var(--r);
            background: rgba(56,189,248,0.07); border: 1px solid var(--border);
        }
        .user-ava {
            width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
            background: linear-gradient(135deg, #0891b2, #10b981);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700; color: #fff;
        }
        .user-name { font-size: var(--fs-sm); font-weight: 600; color: var(--text-primary); }
        .user-email { font-size: 11px; color: var(--text-muted); margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 140px; }

        /* ─── MAIN ─── */
        .main { flex: 1; display: flex; flex-direction: column; min-width: 0; overflow: hidden; }

        .topbar {
            height: 58px; padding: 0 28px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid var(--border2);
            background: rgba(7,20,40,0.78);
            backdrop-filter: blur(24px);
            position: sticky; top: 0; z-index: 50; flex-shrink: 0;
        }
        .page-title {
            font-size: var(--fs-md); font-weight: 600; color: var(--text-primary);
        }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }

        .content { padding: 28px 32px; flex: 1; overflow-y: auto; }
        .content::-webkit-scrollbar { width: 4px; }
        .content::-webkit-scrollbar-track { background: transparent; }
        .content::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex; align-items: center; gap: 7px;
            padding: 9px 18px; border-radius: var(--r);
            font-size: var(--fs-sm); font-weight: 600;
            font-family: 'Space Grotesk', sans-serif;
            cursor: pointer; transition: all 0.18s;
            border: 1px solid transparent; text-decoration: none;
        }
        .btn-primary {
            background: var(--teal); color: #fff; border-color: var(--teal);
        }
        .btn-primary:hover { background: var(--teal2); box-shadow: 0 0 18px rgba(6,182,212,0.35); }
        .btn-secondary {
            background: var(--accent-dim); color: var(--accent); border-color: var(--border);
        }
        .btn-secondary:hover { background: rgba(56,189,248,0.18); border-color: var(--accent); }
        .btn-danger {
            background: rgba(239,68,68,0.12); color: #f87171; border-color: rgba(239,68,68,0.25);
        }
        .btn-danger:hover { background: rgba(239,68,68,0.2); }
        .btn-sm { padding: 6px 12px; font-size: var(--fs-xs); }

        /* ─── CARDS ─── */
        .card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--r-lg); box-shadow: var(--shadow-sm);
            backdrop-filter: blur(16px);
        }
        .card-header {
            padding: 18px 22px; border-bottom: 1px solid var(--border2);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: var(--fs-md); font-weight: 600; color: var(--text-primary); }
        .card-body { padding: 22px; }

        /* ─── STAT CARDS ─── */
        .stat-card {
            background: var(--surface); border: 1px solid var(--border);
            border-radius: var(--r-lg); padding: 20px 22px;
            box-shadow: var(--shadow-sm); backdrop-filter: blur(16px);
            position: relative; overflow: hidden;
        }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
        }
        .stat-card.blue::before  { background: linear-gradient(90deg,#0891b2,#06b6d4); }
        .stat-card.green::before { background: linear-gradient(90deg,#10b981,#34d399); }
        .stat-card.amber::before { background: linear-gradient(90deg,#f59e0b,#fbbf24); }
        .stat-card.red::before   { background: linear-gradient(90deg,#ef4444,#f87171); }
        .stat-card.purple::before{ background: linear-gradient(90deg,#8b5cf6,#a78bfa); }
        .stat-label {
            font-size: var(--fs-xs); font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.7px; color: var(--text-muted); margin-bottom: 8px;
        }
        .stat-value {
            font-size: 28px; font-weight: 700;
            font-family: 'JetBrains Mono', monospace;
            color: var(--text-primary); letter-spacing: -1px; line-height: 1;
        }
        .stat-sub { font-size: var(--fs-xs); color: var(--text-muted); margin-top: 6px; }

        /* ─── TABLE ─── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: var(--fs-sm); }
        thead th {
            padding: 11px 16px; text-align: left;
            font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px;
            color: var(--text-muted); background: rgba(56,189,248,0.05);
            border-bottom: 1px solid var(--border);
        }
        tbody td {
            padding: 13px 16px; border-bottom: 1px solid var(--border2);
            color: var(--text-secondary);
            font-family: 'JetBrains Mono', monospace; font-size: 13px;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(56,189,248,0.04); }

        /* ─── BADGE ─── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 3px 9px; border-radius: 100px;
            font-size: var(--fs-xs); font-weight: 600;
        }
        .badge-teal   { background: rgba(6,182,212,0.15); color: #67e8f9; }
        .badge-green  { background: rgba(16,185,129,0.15); color: #34d399; }
        .badge-amber  { background: rgba(245,158,11,0.15); color: #fbbf24; }
        .badge-red    { background: rgba(239,68,68,0.15); color: #f87171; }
        .badge-purple { background: rgba(139,92,246,0.15); color: #a78bfa; }

        /* ─── FORM ─── */
        .form-group { margin-bottom: 22px; }
        .form-label {
            display: block; font-size: var(--fs-sm); font-weight: 600;
            color: var(--text-secondary); margin-bottom: 7px;
        }
        .form-control {
            width: 100%; padding: 10px 14px;
            background: rgba(56,189,248,0.06);
            border: 1px solid var(--border); border-radius: var(--r);
            font-size: var(--fs-base); font-family: 'Space Grotesk', sans-serif;
            color: var(--text-primary); outline: none;
            transition: border-color 0.18s, box-shadow 0.18s;
        }
        .form-control:focus {
            border-color: var(--teal2);
            box-shadow: 0 0 0 3px rgba(6,182,212,0.15);
        }
        .form-control::placeholder { color: var(--text-muted); }
        textarea.form-control { resize: vertical; min-height: 90px; }
        .form-hint { font-size: var(--fs-xs); color: var(--text-muted); margin-top: 5px; }

        /* ─── ALERT ─── */
        .alert {
            padding: 13px 18px; border-radius: var(--r);
            font-size: var(--fs-sm); margin-bottom: 22px;
            display: flex; align-items: center; gap: 9px;
        }
        .alert-success { background: rgba(16,185,129,0.12); color: #34d399; border: 1px solid rgba(16,185,129,0.25); }
        .alert-error   { background: rgba(239,68,68,0.12); color: #f87171; border: 1px solid rgba(239,68,68,0.25); }

        /* ─── HELPERS ─── */
        .flex { display: flex; }
        .items-center { align-items: center; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .mb-4 { margin-bottom: 18px; }
        .mb-6 { margin-bottom: 26px; }
        .mt-6 { margin-top: 26px; }
        .text-muted { color: var(--text-muted); font-size: var(--fs-sm); }
        .logout-btn { background: none; border: none; width: 100%; text-align: left; cursor: pointer; font-family: 'Space Grotesk', sans-serif; }
    </style>
    @stack('styles')
</head>
<body>

<!-- Ocean BG -->
<div class="ocean-bg">
    <div class="wave"></div>
    <div class="wave"></div>
    <div class="wave"></div>
</div>
<script>
// Inject bubbles
(function(){
    const bg = document.querySelector('.ocean-bg');
    for(let i=0;i<14;i++){
        const b = document.createElement('div');
        b.className = 'bubble';
        const s = Math.random()*5+2;
        b.style.cssText = `width:${s}px;height:${s}px;left:${Math.random()*100}%;`
            +`animation-duration:${Math.random()*14+10}s;`
            +`animation-delay:-${Math.random()*14}s;`
            +`opacity:${Math.random()*0.5+0.15}`;
        bg.appendChild(b);
    }
})();
</script>

<div class="wrapper">
    <!-- ── SIDEBAR ── -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="logo-row">
                <div class="logo-icon">🌊</div>
                <div>
                    <div class="logo-text">Ocean<span>BI</span></div>
                    <div class="logo-sub">Intelligence System</div>
                </div>
            </div>
        </div>
        <div class="sidebar-meta">
            UAS Business Intelligence · Kelompok 6<br>
            Sistem Informasi · Universitas Mulawarman · 2026
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-ico">🏠</span> Dashboard
            </a>
            <a href="{{ route('datasets.index') }}" class="nav-item {{ request()->routeIs('datasets.*') ? 'active' : '' }}">
                <span class="nav-ico">🗄️</span> Data Explorer
            </a>
            <a href="{{ route('datasets.create') }}" class="nav-item">
                <span class="nav-ico">⬆️</span> Upload Dataset
            </a>
            <div class="nav-label">Visualisasi</div>
            <div class="nav-item">
                <span class="nav-ico">🌍</span> Globe Map
            </div>
            <div class="nav-item">
                <span class="nav-ico">📊</span> Pollution Charts
            </div>
            <div class="nav-item">
                <span class="nav-ico">⚠️</span> Risk Analysis (DSS)
            </div>
            <div class="nav-label">Info</div>
            <div class="nav-item">
                <span class="nav-ico">ℹ️</span> About Project
            </div>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}" class="logout-btn">
                @csrf
                <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;font-family:'Space Grotesk',sans-serif;color:var(--text-muted);font-size:var(--fs-sm);">
                    <span class="nav-ico">🚪</span> Logout
                </button>
            </form>
            <div class="user-pill">
                <div class="user-ava">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                <div style="flex:1;min-width:0;">
                    <div class="user-name">{{ auth()->user()->name }}</div>
                    <div class="user-email">{{ auth()->user()->email }}</div>
                </div>
            </div>
        </div>
    </aside>

    <!-- ── MAIN ── -->
    <div class="main">
        <header class="topbar">
            <div class="page-title">@yield('title', 'Dashboard')</div>
            <div class="topbar-actions">@yield('topbar-actions')</div>
        </header>

        <main class="content">
            @if(session('success'))
                <div class="alert alert-success">✓ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-error">✗ {{ session('error') }}</div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

@stack('scripts')
</body>
</html>