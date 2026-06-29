<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'لوحة التحكم')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        *, *::before, *::after {
            font-family: 'Cairo', sans-serif;
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --bg:          #f4f6f9;
            --card:        #ffffff;
            --border:      #e8eaed;
            --border-soft: #f0f2f5;
            --text:        #0f172a;
            --text-2:      #475569;
            --text-3:      #94a3b8;
            --accent:      #2563eb;
            --accent-soft: #eff6ff;
            --green:       #16a34a;
            --green-soft:  #f0fdf4;
            --red:         #dc2626;
            --red-soft:    #fef2f2;
            --orange:      #ea580c;
            --orange-soft: #fff7ed;
            --purple:      #7c3aed;
            --purple-soft: #f5f3ff;
            --sidebar-w:   260px;
            --topbar-h:    60px;
            --radius:      12px;
            --radius-lg:   16px;
            --shadow-sm:   0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04);
            --shadow:      0 4px 12px rgba(0,0,0,0.06);
            --shadow-md:   0 8px 24px rgba(0,0,0,0.08);
        }

        html, body { height: 100%; }
        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.5;
        }

        /* ═══════════════════════════════════════
           SIDEBAR
        ═══════════════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--card);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
            overflow: hidden;
        }

        /* Logo / Brand */
        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid var(--border-soft);
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .brand-icon {
            width: 40px; height: 40px;
            border-radius: 10px;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-size: 20px;
            flex-shrink: 0;
        }
        .brand-name {
            font-size: 14px;
            font-weight: 800;
            color: var(--text);
            line-height: 1.2;
        }
        .brand-sub {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 1px;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 12px 12px;
            overflow-y: auto;
        }
        .sidebar-nav::-webkit-scrollbar { width: 0; }

        .nav-group-label {
            font-size: 10px;
            font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 16px 10px 6px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            color: var(--text-2);
            font-size: 13.5px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.12s;
            margin-bottom: 1px;
        }
        .nav-link:hover {
            background: var(--bg);
            color: var(--text);
        }
        .nav-link.active {
            background: var(--accent-soft);
            color: var(--accent);
            font-weight: 700;
        }
        .nav-link .nl-icon {
            width: 30px; height: 30px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px;
            background: var(--border-soft);
            flex-shrink: 0;
            transition: background 0.12s;
        }
        .nav-link:hover .nl-icon { background: #e2e8f0; }
        .nav-link.active .nl-icon { background: #dbeafe; }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 14px 12px;
            border-top: 1px solid var(--border-soft);
            flex-shrink: 0;
        }
        .sf-user {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius);
            background: var(--bg);
            margin-bottom: 8px;
        }
        .sf-avatar {
            width: 34px; height: 34px;
            border-radius: 8px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }
        .sf-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .sf-role { font-size: 11px; color: var(--text-3); margin-top: 1px; }
        .sf-logout {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 8px 14px;
            border-radius: var(--radius);
            background: none;
            border: 1px solid var(--border);
            color: var(--text-2);
            font-size: 13px; font-family: 'Cairo', sans-serif;
            cursor: pointer;
            transition: all 0.12s;
        }
        .sf-logout:hover {
            background: var(--red-soft);
            border-color: #fca5a5;
            color: var(--red);
        }

        /* ═══════════════════════════════════════
           MAIN AREA
        ═══════════════════════════════════════ */
        .main {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Topbar */
        .topbar {
            position: sticky; top: 0; z-index: 50;
            height: var(--topbar-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }
        .topbar-left {
            display: flex; align-items: center; gap: 16px;
        }
        .topbar-page-title {
            font-size: 15px;
            font-weight: 700;
            color: var(--text);
        }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 7px 14px;
            width: 240px;
            transition: all 0.12s;
        }
        .topbar-search:focus-within {
            border-color: var(--accent);
            background: #fff;
        }
        .topbar-search input {
            background: none; border: none; outline: none;
            font-family: 'Cairo', sans-serif;
            font-size: 13px; color: var(--text); width: 100%;
        }
        .topbar-search input::placeholder { color: var(--text-3); }
        .topbar-right {
            display: flex; align-items: center; gap: 8px;
        }

        /* Page content */
        .page-wrap {
            flex: 1;
            padding: 24px 28px;
        }

        /* Page header (optional inside pages) */
        .page-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 16px;
        }
        .page-header h1 {
            font-size: 20px;
            font-weight: 800;
            color: var(--text);
        }
        .page-header p {
            font-size: 13px;
            color: var(--text-2);
            margin-top: 3px;
        }

        /* Alerts */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px;
            border-radius: var(--radius);
            font-size: 13.5px; font-weight: 500;
            margin-bottom: 16px;
        }
        .alert-success { background: var(--green-soft); border: 1px solid #bbf7d0; color: #14532d; }
        .alert-error   { background: var(--red-soft);   border: 1px solid #fecaca; color: #7f1d1d; }

        /* Medical alert */
        .medical-alert {
            background: var(--red-soft);
            border: 1px solid #fca5a5;
            border-radius: var(--radius-lg);
            padding: 16px 20px;
            margin-bottom: 16px;
            display: flex; gap: 14px;
        }
        .medical-alert-icon { font-size: 28px; flex-shrink: 0; }
        .medical-alert h4 { color: #991b1b; font-size: 14px; font-weight: 800; margin-bottom: 6px; }
        .medical-alert ul  { color: #b91c1c; font-size: 13px; padding-right: 18px; }

        /* ═══════════════════════════════════════
           CARDS
        ═══════════════════════════════════════ */
        .card {
            background: var(--card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-soft);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text); }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px 20px 16px;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
            transition: box-shadow 0.15s, transform 0.15s;
        }
        .stat-card:hover {
            box-shadow: var(--shadow);
            transform: translateY(-1px);
        }
        .sc-icon-wrap {
            width: 40px; height: 40px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
            margin-bottom: 14px;
        }
        .stat-label {
            font-size: 12px;
            font-weight: 600;
            color: var(--text-2);
            margin-bottom: 6px;
        }
        .stat-value {
            font-size: 26px;
            font-weight: 900;
            color: var(--text);
            line-height: 1;
        }
        .stat-trend {
            font-size: 11.5px;
            font-weight: 600;
            margin-top: 8px;
            display: flex; align-items: center; gap: 4px;
        }
        .trend-up   { color: var(--green); }
        .trend-down { color: var(--red); }
        .trend-flat { color: var(--text-3); }

        /* color variants */
        .sc-blue   .sc-icon-wrap { background: #dbeafe; }
        .sc-green  .sc-icon-wrap { background: #dcfce7; }
        .sc-purple .sc-icon-wrap { background: #ede9fe; }
        .sc-orange .sc-icon-wrap { background: #ffedd5; }
        .sc-red    .sc-icon-wrap { background: #fee2e2; }
        .sc-pink   .sc-icon-wrap { background: #fce7f3; }

        /* legacy aliases so existing pages don't break */
        .stat-card.c-blue   { } .stat-icon { display:none; }
        .stat-card.c-green  { }
        .stat-card.c-purple { }
        .stat-card.c-pink   { }
        .stat-card.c-yellow { }
        .stat-card.c-red    { }

        /* ── Mini Avatar ── */
        .mini-avatar {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, #2563eb, #7c3aed);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }

        /* ═══════════════════════════════════════
           BUTTONS
        ═══════════════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius);
            font-size: 13px; font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all 0.12s;
            white-space: nowrap;
            line-height: 1;
        }
        .btn-primary {
            background: var(--accent);
            color: #fff;
            box-shadow: 0 1px 3px rgba(37,99,235,0.3);
        }
        .btn-primary:hover { background: #1d4ed8; box-shadow: 0 4px 12px rgba(37,99,235,0.35); }
        .btn-success {
            background: var(--green);
            color: #fff;
            box-shadow: 0 1px 3px rgba(22,163,74,0.25);
        }
        .btn-success:hover { background: #15803d; }
        .btn-outline {
            background: var(--card);
            border: 1px solid var(--border);
            color: var(--text-2);
        }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-soft); }
        .btn-danger { background: var(--red); color: #fff; }
        .btn-danger:hover { background: #b91c1c; }
        .btn-ghost {
            background: none; border: none;
            color: var(--text-2);
            padding: 8px 10px;
        }
        .btn-ghost:hover { background: var(--bg); color: var(--text); }
        .btn-pink { background: #db2777; color: #fff; }
        .btn-sm  { padding: 6px 12px; font-size: 12px; }
        .btn-xs  { padding: 4px 9px; font-size: 11.5px; border-radius: 8px; }

        /* ═══════════════════════════════════════
           TABLE
        ═══════════════════════════════════════ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            padding: 10px 16px;
            text-align: right;
            font-size: 11px;
            font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase;
            letter-spacing: 0.6px;
            border-bottom: 1px solid var(--border);
            white-space: nowrap;
            background: var(--bg);
        }
        thead th:first-child { border-radius: 0; }
        tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border-soft);
            vertical-align: middle;
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover { background: #fafbfd; }
        .td-name {
            font-weight: 700;
            color: var(--accent);
            text-decoration: none;
            font-size: 13.5px;
        }
        .td-name:hover { text-decoration: underline; }

        /* ═══════════════════════════════════════
           BADGES
        ═══════════════════════════════════════ */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px;
            font-weight: 700;
            white-space: nowrap;
        }
        .badge-blue   { background: #dbeafe; color: #1d4ed8; }
        .badge-green  { background: #dcfce7; color: #15803d; }
        .badge-red    { background: #fee2e2; color: #dc2626; }
        .badge-yellow { background: #fef9c3; color: #854d0e; }
        .badge-gray   { background: #f1f5f9; color: #475569; }
        .badge-purple { background: #ede9fe; color: #6d28d9; }
        .badge-pink   { background: #fce7f3; color: #9d174d; }
        .badge-orange { background: #ffedd5; color: #c2410c; }

        /* ═══════════════════════════════════════
           FORMS
        ═══════════════════════════════════════ */
        .form-group  { margin-bottom: 16px; }
        .form-label  {
            display: block;
            font-size: 12.5px; font-weight: 700;
            color: var(--text-2);
            margin-bottom: 6px;
        }
        .form-label .req { color: var(--red); }
        .form-control {
            width: 100%;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 9px 12px;
            font-size: 13.5px;
            font-family: 'Cairo', sans-serif;
            color: var(--text);
            background: var(--card);
            transition: all 0.12s;
            outline: none;
        }
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }
        .form-control:disabled { background: var(--bg); color: var(--text-3); }
        .form-error { color: var(--red); font-size: 11.5px; margin-top: 4px; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .check-label {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; font-size: 13.5px; color: var(--text);
        }
        .check-label input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: var(--accent);
            cursor: pointer;
        }

        /* Search/filter bar */
        .search-bar {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 14px 18px;
            margin-bottom: 18px;
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
        }

        /* Status select */
        select.status-select {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 5px 10px;
            font-size: 12px;
            font-family: 'Cairo', sans-serif;
            cursor: pointer;
            outline: none;
            background: var(--card);
            color: var(--text);
        }
        select.status-select:focus { border-color: var(--accent); }

        /* Print */
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main { margin: 0 !important; }
        }

        [x-cloak] { display: none !important; }
    </style>
    @stack('styles')
</head>
<body>

{{-- SIDEBAR --}}
<aside class="sidebar">

    <div class="sidebar-brand">
        <div class="brand-icon">🦷</div>
        <div>
            <div class="brand-name">{{ config('app.name') }}</div>
            <div class="brand-sub">نظام إدارة العيادة</div>
        </div>
    </div>

    <nav class="sidebar-nav">

        <a href="{{ route('dashboard') }}"
           class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nl-icon">📊</span>
            لوحة التحكم
        </a>

        <div class="nav-group-label">المرضى</div>
        <a href="{{ route('patients.index') }}"
           class="nav-link {{ request()->routeIs('patients.index') ? 'active' : '' }}">
            <span class="nl-icon">👥</span> سجل المرضى
        </a>
        <a href="{{ route('patients.create') }}"
           class="nav-link {{ request()->routeIs('patients.create') ? 'active' : '' }}">
            <span class="nl-icon">➕</span> مريض جديد
        </a>

        <div class="nav-group-label">العيادة</div>
        <a href="{{ route('appointments.index') }}"
           class="nav-link {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <span class="nl-icon">📅</span> المواعيد
        </a>
        @if(!auth()->user()->isDoctor())
        <a href="{{ route('invoices.index') }}"
           class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <span class="nl-icon">💰</span> الفواتير
        </a>
        @endif
        <a href="{{ route('prescriptions.index') }}"
           class="nav-link {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">
            <span class="nl-icon">💊</span> الروشتات
        </a>

        @if(auth()->user()->isAdmin())
        <div class="nav-group-label">الإدارة</div>
        <a href="{{ route('reports.index') }}"
           class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="nl-icon">📈</span> التقارير
        </a>
        <a href="{{ route('users.index') }}"
           class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
            <span class="nl-icon">👤</span> المستخدمون
        </a>
        @endif

    </nav>

    <div class="sidebar-footer">
        <div class="sf-user">
            <div class="sf-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <div class="sf-name">{{ Str::limit(auth()->user()->name, 18) }}</div>
                <div class="sf-role">{{ auth()->user()->role_name }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="sf-logout">
                <span>🚪</span> تسجيل الخروج
            </button>
        </form>
    </div>

</aside>

{{-- MAIN --}}
<div class="main">

    <header class="topbar">
        <div class="topbar-left">
            <span class="topbar-page-title">@yield('title', 'لوحة التحكم')</span>
            <div class="topbar-search">
                <span style="color:var(--text-3);font-size:13px">🔍</span>
                <input type="text" placeholder="بحث عن مريض...">
            </div>
        </div>
        <div class="topbar-right">
            @yield('topbar-actions')
            <a href="{{ route('patients.create') }}" class="btn btn-outline btn-sm">➕ مريض جديد</a>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">📅 موعد جديد</a>
        </div>
    </header>

    <div class="page-wrap">
        @if(session('success'))
            <div class="alert alert-success">✅ {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">❌ {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>

</div>

@stack('scripts')
</body>
</html>
