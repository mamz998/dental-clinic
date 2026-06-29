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
            margin: 0; padding: 0;
        }

        :root {
            /* Dark palette */
            --bg:           #0a0a0f;
            --bg-2:         #0f0f18;
            --bg-3:         #13131e;
            --card:         #111120;
            --card-2:       #16162a;
            --border:       rgba(255,255,255,0.07);
            --border-2:     rgba(255,255,255,0.12);

            /* Text */
            --text:         #f0f0ff;
            --text-2:       #9090b0;
            --text-3:       #50506a;

            /* Accents */
            --blue:         #4f7cff;
            --blue-dark:    #2952cc;
            --blue-glow:    rgba(79,124,255,0.25);
            --purple:       #8b5cf6;
            --purple-glow:  rgba(139,92,246,0.2);
            --green:        #22d3a0;
            --green-glow:   rgba(34,211,160,0.2);
            --orange:       #f97316;
            --orange-glow:  rgba(249,115,22,0.2);
            --red:          #f43f5e;
            --red-glow:     rgba(244,63,94,0.2);
            --pink:         #ec4899;

            --sidebar-w:    260px;
            --topbar-h:     64px;
            --radius:       10px;
            --radius-lg:    14px;
            --radius-xl:    20px;
        }

        html, body { height: 100%; }
        body {
            background: var(--bg);
            color: var(--text);
            min-height: 100vh;
            font-size: 14px;
            line-height: 1.6;
        }

        /* ═══════════════════════════════
           SIDEBAR
        ═══════════════════════════════ */
        .sidebar {
            position: fixed;
            top: 0; right: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--bg-2);
            border-left: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 100;
        }

        .sidebar-brand {
            padding: 22px 18px 18px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; gap: 12px;
            flex-shrink: 0;
        }
        .brand-icon {
            width: 38px; height: 38px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--blue), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 18px; flex-shrink: 0;
            box-shadow: 0 0 16px var(--blue-glow);
        }
        .brand-name { font-size: 14px; font-weight: 800; color: var(--text); line-height: 1.2; }
        .brand-sub  { font-size: 11px; color: var(--text-3); margin-top: 1px; }

        .sidebar-nav {
            flex: 1; padding: 14px 10px;
            overflow-y: auto;
        }
        .sidebar-nav::-webkit-scrollbar { width: 0; }

        .nav-group-label {
            font-size: 10px; font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase; letter-spacing: 1.2px;
            padding: 16px 10px 6px;
        }

        .nav-link {
            display: flex; align-items: center; gap: 10px;
            padding: 9px 12px;
            border-radius: var(--radius);
            color: var(--text-2);
            font-size: 13.5px; font-weight: 500;
            text-decoration: none;
            transition: all 0.15s;
            margin-bottom: 1px;
            position: relative;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.05);
            color: var(--text);
        }
        .nav-link.active {
            background: rgba(79,124,255,0.12);
            color: var(--blue);
            font-weight: 700;
        }
        .nav-link.active::before {
            content: '';
            position: absolute;
            right: 0; top: 20%; bottom: 20%;
            width: 3px;
            background: var(--blue);
            border-radius: 2px 0 0 2px;
            box-shadow: 0 0 8px var(--blue-glow);
        }
        .nl-icon {
            width: 28px; height: 28px;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px;
            background: rgba(255,255,255,0.05);
            flex-shrink: 0;
            transition: all 0.15s;
        }
        .nav-link:hover .nl-icon { background: rgba(255,255,255,0.09); }
        .nav-link.active .nl-icon { background: rgba(79,124,255,0.2); }

        .sidebar-footer {
            padding: 14px 10px;
            border-top: 1px solid var(--border);
            flex-shrink: 0;
        }
        .sf-user {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px;
            border-radius: var(--radius);
            background: rgba(255,255,255,0.04);
            margin-bottom: 8px;
        }
        .sf-avatar {
            width: 34px; height: 34px;
            border-radius: 9px;
            background: linear-gradient(135deg, var(--blue), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; font-weight: 800; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 0 12px var(--blue-glow);
        }
        .sf-name { font-size: 13px; font-weight: 700; color: var(--text); }
        .sf-role { font-size: 11px; color: var(--text-3); }
        .sf-logout {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 7px;
            padding: 8px 14px;
            border-radius: var(--radius);
            background: rgba(244,63,94,0.08);
            border: 1px solid rgba(244,63,94,0.15);
            color: #f87196;
            font-size: 13px; font-family: 'Cairo', sans-serif;
            cursor: pointer; transition: all 0.15s;
        }
        .sf-logout:hover {
            background: rgba(244,63,94,0.15);
            border-color: rgba(244,63,94,0.3);
        }

        /* ═══════════════════════════════
           MAIN
        ═══════════════════════════════ */
        .main {
            margin-right: var(--sidebar-w);
            min-height: 100vh;
            display: flex; flex-direction: column;
        }

        .topbar {
            position: sticky; top: 0; z-index: 50;
            height: var(--topbar-h);
            background: rgba(10,10,15,0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid var(--border);
            padding: 0 28px;
            display: flex; align-items: center; justify-content: space-between; gap: 16px;
        }
        .topbar-title {
            font-size: 15px; font-weight: 700; color: var(--text);
        }
        .topbar-search {
            display: flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 8px 14px; width: 240px;
            transition: all 0.15s;
        }
        .topbar-search:focus-within {
            border-color: rgba(79,124,255,0.4);
            background: rgba(79,124,255,0.05);
        }
        .topbar-search input {
            background: none; border: none; outline: none;
            font-family: 'Cairo', sans-serif;
            font-size: 13px; color: var(--text); width: 100%;
        }
        .topbar-search input::placeholder { color: var(--text-3); }
        .topbar-right { display: flex; align-items: center; gap: 8px; }

        .page-wrap { flex: 1; padding: 28px 30px; }

        .page-header {
            display: flex; align-items: flex-start; justify-content: space-between;
            margin-bottom: 24px; gap: 16px;
        }
        .page-header h1 { font-size: 22px; font-weight: 800; color: var(--text); }
        .page-header p  { font-size: 13px; color: var(--text-3); margin-top: 3px; }

        /* Alerts */
        .alert {
            display: flex; align-items: center; gap: 10px;
            padding: 12px 16px; border-radius: var(--radius);
            font-size: 13.5px; font-weight: 500; margin-bottom: 16px;
        }
        .alert-success { background: rgba(34,211,160,0.1); border: 1px solid rgba(34,211,160,0.2); color: #22d3a0; }
        .alert-error   { background: rgba(244,63,94,0.1);  border: 1px solid rgba(244,63,94,0.2);  color: #f87196; }

        /* Medical alert */
        .medical-alert {
            background: rgba(244,63,94,0.08);
            border: 1px solid rgba(244,63,94,0.2);
            border-radius: var(--radius-lg);
            padding: 16px 20px; margin-bottom: 16px;
            display: flex; gap: 14px;
        }
        .medical-alert-icon { font-size: 26px; flex-shrink: 0; }
        .medical-alert h4 { color: #f87196; font-size: 14px; font-weight: 800; margin-bottom: 6px; }
        .medical-alert ul { color: #f87196; font-size: 13px; padding-right: 18px; opacity: 0.8; }

        /* ═══════════════════════════════
           CARDS
        ═══════════════════════════════ */
        .card {
            background: var(--card);
            border-radius: var(--radius-lg);
            border: 1px solid var(--border);
        }
        .card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center; justify-content: space-between;
        }
        .card-title { font-size: 14px; font-weight: 700; color: var(--text); }

        /* ── Stat Cards ── */
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 20px;
            position: relative; overflow: hidden;
            transition: border-color 0.2s, transform 0.2s;
            cursor: default;
        }
        .stat-card:hover { transform: translateY(-2px); }

        /* glow top border */
        .stat-card::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 1px;
        }
        .sc-blue::after   { background: linear-gradient(90deg, transparent, var(--blue), transparent); }
        .sc-green::after  { background: linear-gradient(90deg, transparent, var(--green), transparent); }
        .sc-purple::after { background: linear-gradient(90deg, transparent, var(--purple), transparent); }
        .sc-orange::after { background: linear-gradient(90deg, transparent, var(--orange), transparent); }
        .sc-red::after    { background: linear-gradient(90deg, transparent, var(--red), transparent); }
        .sc-pink::after   { background: linear-gradient(90deg, transparent, var(--pink), transparent); }

        .sc-icon-wrap {
            width: 38px; height: 38px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 17px; margin-bottom: 16px;
        }
        .sc-blue   .sc-icon-wrap { background: rgba(79,124,255,0.15); box-shadow: 0 0 12px var(--blue-glow); }
        .sc-green  .sc-icon-wrap { background: rgba(34,211,160,0.15); box-shadow: 0 0 12px var(--green-glow); }
        .sc-purple .sc-icon-wrap { background: rgba(139,92,246,0.15); box-shadow: 0 0 12px var(--purple-glow); }
        .sc-orange .sc-icon-wrap { background: rgba(249,115,22,0.15); box-shadow: 0 0 12px var(--orange-glow); }
        .sc-red    .sc-icon-wrap { background: rgba(244,63,94,0.15);  box-shadow: 0 0 12px var(--red-glow); }
        .sc-pink   .sc-icon-wrap { background: rgba(236,72,153,0.15); }

        .stat-label { font-size: 11.5px; font-weight: 600; color: var(--text-3); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
        .stat-value { font-size: 28px; font-weight: 900; color: var(--text); line-height: 1; }
        .stat-icon  { display: none; }

        /* legacy color classes — kept so old pages don't break */
        .stat-card.c-blue, .stat-card.c-green, .stat-card.c-purple,
        .stat-card.c-pink, .stat-card.c-yellow, .stat-card.c-red { }

        .stat-trend {
            font-size: 11.5px; font-weight: 600;
            margin-top: 10px;
            display: flex; align-items: center; gap: 4px;
        }
        .trend-up   { color: var(--green); }
        .trend-down { color: var(--red); }
        .trend-flat { color: var(--text-3); }

        /* Mini Avatar */
        .mini-avatar {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--blue), var(--purple));
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 800; color: #fff;
            flex-shrink: 0;
        }

        /* ═══════════════════════════════
           BUTTONS
        ═══════════════════════════════ */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 8px 16px;
            border-radius: var(--radius);
            font-size: 13px; font-weight: 700;
            font-family: 'Cairo', sans-serif;
            cursor: pointer; border: none;
            text-decoration: none;
            transition: all 0.15s;
            white-space: nowrap; line-height: 1;
        }
        .btn-primary {
            background: var(--blue);
            color: #fff;
            box-shadow: 0 0 16px var(--blue-glow);
        }
        .btn-primary:hover { background: #6b8fff; box-shadow: 0 0 24px var(--blue-glow); }

        .btn-success {
            background: var(--green);
            color: #0a0a0f;
            box-shadow: 0 0 14px var(--green-glow);
        }
        .btn-success:hover { background: #34ebb5; }

        .btn-outline {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-2);
            color: var(--text-2);
        }
        .btn-outline:hover { border-color: var(--blue); color: var(--blue); background: rgba(79,124,255,0.08); }

        .btn-danger { background: var(--red); color: #fff; box-shadow: 0 0 14px var(--red-glow); }
        .btn-danger:hover { background: #ff5577; }

        .btn-ghost {
            background: none; border: none;
            color: var(--text-2); padding: 8px 10px;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.06); color: var(--text); }

        .btn-pink { background: var(--pink); color: #fff; }
        .btn-sm  { padding: 6px 12px; font-size: 12px; }
        .btn-xs  { padding: 4px 9px; font-size: 11.5px; border-radius: 7px; }

        /* ═══════════════════════════════
           TABLE
        ═══════════════════════════════ */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 13.5px; }
        thead th {
            padding: 10px 16px;
            text-align: right;
            font-size: 10.5px; font-weight: 700;
            color: var(--text-3);
            text-transform: uppercase; letter-spacing: 0.8px;
            border-bottom: 1px solid var(--border);
            background: rgba(255,255,255,0.02);
            white-space: nowrap;
        }
        tbody td {
            padding: 13px 16px;
            border-bottom: 1px solid var(--border);
            vertical-align: middle;
            color: var(--text-2);
        }
        tbody tr:last-child td { border-bottom: none; }
        tbody tr:hover td { background: rgba(255,255,255,0.03); color: var(--text); }
        .td-name {
            font-weight: 700; color: var(--blue);
            text-decoration: none; font-size: 13.5px;
        }
        .td-name:hover { color: #7fa0ff; text-decoration: underline; }

        /* ═══════════════════════════════
           BADGES
        ═══════════════════════════════ */
        .badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11.5px; font-weight: 700;
            white-space: nowrap;
        }
        .badge-blue   { background: rgba(79,124,255,0.15);  color: #7fa0ff; border: 1px solid rgba(79,124,255,0.2); }
        .badge-green  { background: rgba(34,211,160,0.12);  color: #22d3a0; border: 1px solid rgba(34,211,160,0.2); }
        .badge-red    { background: rgba(244,63,94,0.12);   color: #f87196; border: 1px solid rgba(244,63,94,0.2); }
        .badge-yellow { background: rgba(234,179,8,0.12);   color: #facc15; border: 1px solid rgba(234,179,8,0.2); }
        .badge-gray   { background: rgba(255,255,255,0.07); color: var(--text-2); border: 1px solid var(--border); }
        .badge-purple { background: rgba(139,92,246,0.12);  color: #a78bfa; border: 1px solid rgba(139,92,246,0.2); }
        .badge-pink   { background: rgba(236,72,153,0.12);  color: #f472b6; border: 1px solid rgba(236,72,153,0.2); }
        .badge-orange { background: rgba(249,115,22,0.12);  color: #fb923c; border: 1px solid rgba(249,115,22,0.2); }

        /* ═══════════════════════════════
           FORMS
        ═══════════════════════════════ */
        .form-group  { margin-bottom: 16px; }
        .form-label  {
            display: block; font-size: 12.5px; font-weight: 700;
            color: var(--text-2); margin-bottom: 6px;
        }
        .form-label .req { color: var(--red); }
        .form-control {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid var(--border-2);
            border-radius: var(--radius);
            padding: 9px 12px;
            font-size: 13.5px; font-family: 'Cairo', sans-serif;
            color: var(--text); outline: none;
            transition: all 0.15s;
        }
        .form-control:focus {
            border-color: var(--blue);
            background: rgba(79,124,255,0.06);
            box-shadow: 0 0 0 3px rgba(79,124,255,0.1);
        }
        .form-control:disabled { opacity: 0.4; cursor: not-allowed; }
        .form-control option { background: var(--card); color: var(--text); }
        .form-error { color: var(--red); font-size: 11.5px; margin-top: 4px; }
        textarea.form-control { resize: vertical; min-height: 80px; }
        .check-label {
            display: flex; align-items: center; gap: 8px;
            cursor: pointer; font-size: 13.5px; color: var(--text-2);
        }
        .check-label input[type=checkbox] {
            width: 15px; height: 15px;
            accent-color: var(--blue); cursor: pointer;
        }

        /* Search bar */
        .search-bar {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 14px 18px; margin-bottom: 18px;
            display: flex; gap: 10px; flex-wrap: wrap; align-items: center;
        }

        /* Status select */
        select.status-select {
            background: rgba(255,255,255,0.05);
            border: 1px solid var(--border-2);
            border-radius: 7px; padding: 5px 10px;
            font-size: 12px; font-family: 'Cairo', sans-serif;
            cursor: pointer; outline: none; color: var(--text-2);
        }
        select.status-select:focus { border-color: var(--blue); color: var(--text); }
        select.status-select option { background: var(--card); }

        /* Print */
        @media print {
            .sidebar, .topbar, .no-print { display: none !important; }
            .main { margin: 0 !important; }
            body { background: #fff !important; color: #000 !important; }
        }

        [x-cloak] { display: none !important; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.2); }
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
            <span class="nl-icon">📊</span> لوحة التحكم
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
            <button type="submit" class="sf-logout">🚪 تسجيل الخروج</button>
        </form>
    </div>

</aside>

{{-- MAIN --}}
<div class="main">

    <header class="topbar">
        <div style="display:flex;align-items:center;gap:16px">
            <span class="topbar-title">@yield('title', 'لوحة التحكم')</span>
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
