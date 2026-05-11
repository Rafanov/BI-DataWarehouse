<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'DataWarehouse') }} — @yield('title', 'Home')</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg: #f8f9fb;
            --surface: #ffffff;
            --border: #e4e7ec;
            --border-strong: #d0d5dd;
            --text-primary: #101828;
            --text-secondary: #475467;
            --text-muted: #98a2b3;
            --accent: #2563eb;
            --accent-light: #eff4ff;
            --accent-hover: #1d4ed8;
            --success: #12b76a;
            --danger: #f04438;
            --warning: #f79009;
            --shadow-sm: 0 1px 2px rgba(16,24,40,0.05);
            --shadow: 0 1px 3px rgba(16,24,40,0.1), 0 1px 2px rgba(16,24,40,0.06);
            --shadow-md: 0 4px 8px rgba(16,24,40,0.08), 0 2px 4px rgba(16,24,40,0.04);
            --radius: 8px;
            --radius-lg: 12px;
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--surface);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            padding: 0;
            position: fixed;
            top: 0; left: 0;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .logo-icon {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white;
            font-size: 16px;
        }

        .logo-text {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.3px;
        }

        .logo-text span {
            color: var(--accent);
        }

        .sidebar-nav {
            padding: 12px 12px;
            flex: 1;
        }

        .nav-label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: var(--text-muted);
            padding: 8px 8px 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius);
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 500;
            transition: all 0.15s;
            margin-bottom: 2px;
        }

        .nav-item:hover { background: var(--bg); color: var(--text-primary); }
        .nav-item.active { background: var(--accent-light); color: var(--accent); }
        .nav-item svg { width: 16px; height: 16px; flex-shrink: 0; }

        .sidebar-footer {
            padding: 12px;
            border-top: 1px solid var(--border);
        }

        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--radius);
            cursor: pointer;
            transition: background 0.15s;
        }

        .user-card:hover { background: var(--bg); }

        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--accent-light);
            color: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px;
            font-weight: 600;
        }

        .user-name { font-size: 13px; font-weight: 500; color: var(--text-primary); }
        .user-email { font-size: 11px; color: var(--text-muted); }

        /* Main content */
        .main {
            margin-left: 240px;
            flex: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            padding: 0 32px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .page-title {
            font-size: 15px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }

        .content { padding: 32px; flex: 1; }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border-radius: var(--radius);
            font-size: 13px;
            font-weight: 500;
            font-family: 'DM Sans', sans-serif;
            cursor: pointer;
            transition: all 0.15s;
            border: 1px solid transparent;
            text-decoration: none;
        }

        .btn-primary {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        .btn-primary:hover { background: var(--accent-hover); }

        .btn-secondary {
            background: var(--surface);
            color: var(--text-secondary);
            border-color: var(--border-strong);
        }
        .btn-secondary:hover { background: var(--bg); color: var(--text-primary); }

        .btn-danger {
            background: white;
            color: var(--danger);
            border-color: #fda29b;
        }
        .btn-danger:hover { background: #fff5f5; }

        .btn-sm { padding: 5px 10px; font-size: 12px; }

        /* Cards */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
        }

        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .card-body { padding: 20px; }

        /* Stat cards */
        .stat-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            box-shadow: var(--shadow-sm);
        }

        .stat-label {
            font-size: 12px;
            font-weight: 500;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 600;
            color: var(--text-primary);
            font-family: 'DM Mono', monospace;
            letter-spacing: -1px;
        }

        .stat-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 12px;
        }

        /* Table */
        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        thead th {
            padding: 10px 14px;
            text-align: left;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            background: var(--bg);
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
        }

        tbody td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text-secondary);
            font-family: 'DM Mono', monospace;
            font-size: 12px;
        }

        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: var(--bg); }

        /* Badge */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            border-radius: 100px;
            font-size: 11px;
            font-weight: 500;
        }

        .badge-blue { background: var(--accent-light); color: var(--accent); }
        .badge-green { background: #ecfdf3; color: #027a48; }

        /* Form */
        .form-group { margin-bottom: 20px; }

        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-primary);
            margin-bottom: 6px;
        }

        .form-control {
            width: 100%;
            padding: 9px 13px;
            border: 1px solid var(--border-strong);
            border-radius: var(--radius);
            font-size: 14px;
            font-family: 'DM Sans', sans-serif;
            color: var(--text-primary);
            background: var(--surface);
            transition: border-color 0.15s, box-shadow 0.15s;
            outline: none;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        textarea.form-control { resize: vertical; min-height: 80px; }

        .form-hint { font-size: 12px; color: var(--text-muted); margin-top: 4px; }

        /* Alert */
        .alert {
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-success { background: #ecfdf3; color: #027a48; border: 1px solid #a9efc5; }
        .alert-error { background: #fef3f2; color: #b42318; border: 1px solid #fda29b; }

        /* AI Panel */
        .ai-panel {
            background: linear-gradient(135deg, #f0f4ff 0%, #fafafa 100%);
            border: 1px solid #c7d7fd;
            border-radius: var(--radius-lg);
            padding: 20px;
            margin-top: 24px;
        }

        .ai-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }

        .ai-badge {
            background: var(--accent);
            color: white;
            font-size: 10px;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 100px;
            letter-spacing: 0.5px;
        }

        .ai-title {
            font-size: 14px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .ai-output {
            font-size: 13px;
            color: var(--text-secondary);
            line-height: 1.7;
            background: white;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 14px;
            min-height: 80px;
        }

        /* Grid helpers */
        .grid { display: grid; gap: 20px; }
        .grid-3 { grid-template-columns: repeat(3, 1fr); }
        .grid-2 { grid-template-columns: repeat(2, 1fr); }
        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .gap-2 { gap: 8px; }
        .gap-3 { gap: 12px; }
        .mb-4 { margin-bottom: 16px; }
        .mb-6 { margin-bottom: 24px; }
        .mt-6 { margin-top: 24px; }
        .text-muted { color: var(--text-muted); font-size: 13px; }

        /* Logout form */
        .logout-btn {
            background: none;
            border: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
        }
    </style>
    @stack('styles')
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">⬡</div>
        <div class="logo-text">Data<span>Vault</span></div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('datasets.index') }}" class="nav-item {{ request()->routeIs('datasets.*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582 4 8 4s8 1.79 8 4"/></svg>
            Datasets
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}" class="logout-btn">
            @csrf
            <button type="submit" class="nav-item" style="width:100%; background:none; border:none; cursor:pointer; font-family:'DM Sans',sans-serif;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:16px;height:16px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Logout
            </button>
        </form>
        <div class="user-card">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div>
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-email">{{ auth()->user()->email }}</div>
            </div>
        </div>
    </div>
</aside>

<!-- Main -->
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

@stack('scripts')
</body>
</html>