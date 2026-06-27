<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'لوحة التحكم')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        *{font-family:'Cairo',sans-serif;box-sizing:border-box;margin:0;padding:0}
        :root{
            --blue-dark:#1a56db;
            --blue-mid:#1e429f;
            --blue-sidebar:#1a3a6e;
            --blue-sidebar-dark:#102a50;
            --blue-light:#eff6ff;
            --blue-accent:#3b82f6;
            --pink:#ec4899;
            --green:#10b981;
            --red:#ef4444;
            --yellow:#f59e0b;
            --bg:#f0f5ff;
            --card:#ffffff;
            --text:#111827;
            --muted:#6b7280;
            --border:#e5e7eb;
            --sidebar-w:240px;
        }
        body{background:var(--bg);color:var(--text);min-height:100vh}

        /* ─── SIDEBAR ─── */
        .sidebar{
            position:fixed;top:0;right:0;bottom:0;width:var(--sidebar-w);
            background:linear-gradient(180deg,#1a3a6e 0%,#102a50 100%);
            display:flex;flex-direction:column;z-index:100;
            box-shadow:4px 0 24px rgba(0,0,0,0.15);
        }
        .sidebar-logo{
            padding:24px 20px 20px;
            display:flex;align-items:center;gap:12px;
            border-bottom:1px solid rgba(255,255,255,0.08);
        }
        .sidebar-logo .icon{
            width:44px;height:44px;
            background:linear-gradient(135deg,#3b82f6,#1d4ed8);
            border-radius:14px;display:flex;align-items:center;justify-content:center;
            font-size:22px;flex-shrink:0;
            box-shadow:0 4px 14px rgba(59,130,246,0.4);
        }
        .sidebar-logo .name{color:#fff;font-weight:800;font-size:14px;line-height:1.3}
        .sidebar-logo .sub{color:rgba(255,255,255,0.4);font-size:11px;margin-top:1px}

        .sidebar-nav{flex:1;padding:12px 12px;overflow-y:auto}
        .sidebar-nav::-webkit-scrollbar{width:0}
        .nav-section{font-size:10px;font-weight:700;color:rgba(255,255,255,0.25);
            text-transform:uppercase;letter-spacing:1.2px;padding:14px 10px 6px}
        .nav-item{
            display:flex;align-items:center;gap:10px;
            padding:11px 14px;border-radius:12px;
            color:rgba(255,255,255,0.55);font-size:13.5px;font-weight:500;
            text-decoration:none;transition:all 0.15s;margin-bottom:2px;
        }
        .nav-item:hover{background:rgba(255,255,255,0.08);color:rgba(255,255,255,0.9)}
        .nav-item.active{
            background:linear-gradient(135deg,rgba(59,130,246,0.3),rgba(29,78,216,0.2));
            color:#fff;font-weight:700;
            border:1px solid rgba(59,130,246,0.3);
        }
        .nav-item .nav-icon{font-size:17px;width:22px;text-align:center;flex-shrink:0}

        .sidebar-footer{padding:16px 12px;border-top:1px solid rgba(255,255,255,0.08)}
        .user-card{
            display:flex;align-items:center;gap:10px;
            padding:10px 12px;border-radius:12px;
            background:rgba(255,255,255,0.06);margin-bottom:10px;
        }
        .user-avatar{
            width:36px;height:36px;border-radius:10px;
            background:linear-gradient(135deg,#ec4899,#8b5cf6);
            display:flex;align-items:center;justify-content:center;
            font-weight:800;font-size:15px;color:white;flex-shrink:0;
        }
        .user-name{color:#fff;font-size:12.5px;font-weight:700}
        .user-role{color:rgba(255,255,255,0.35);font-size:11px}
        .btn-logout{
            width:100%;display:flex;align-items:center;justify-content:center;gap:8px;
            padding:9px;border-radius:10px;
            background:rgba(239,68,68,0.12);border:1px solid rgba(239,68,68,0.2);
            color:#fca5a5;font-size:13px;font-family:'Cairo',sans-serif;
            cursor:pointer;transition:all 0.15s;
        }
        .btn-logout:hover{background:rgba(239,68,68,0.22)}

        /* ─── MAIN ─── */
        .main-wrap{margin-right:var(--sidebar-w);min-height:100vh;display:flex;flex-direction:column}

        /* Topbar */
        .topbar{
            background:#fff;border-bottom:1px solid var(--border);
            height:66px;padding:0 28px;
            display:flex;align-items:center;justify-content:space-between;
            position:sticky;top:0;z-index:50;
        }
        .topbar-left{display:flex;align-items:center;gap:14px}
        .topbar-title{font-size:18px;font-weight:800;color:var(--text)}
        .topbar-search{
            display:flex;align-items:center;gap:8px;
            background:#f3f4f6;border-radius:10px;padding:8px 14px;min-width:260px;
        }
        .topbar-search input{background:none;border:none;outline:none;font-family:'Cairo',sans-serif;font-size:13px;color:var(--text);width:100%}
        .topbar-search input::placeholder{color:#9ca3af}
        .topbar-right{display:flex;align-items:center;gap:10px}

        /* Decorative circles (like reference) */
        .deco-circle{
            position:fixed;border-radius:50%;
            pointer-events:none;z-index:0;
        }
        .deco-1{width:180px;height:180px;border:2px solid rgba(59,130,246,0.15);top:-40px;left:-40px}
        .deco-2{width:120px;height:120px;border:2px solid rgba(59,130,246,0.12);bottom:60px;left:20px}
        .deco-3{width:80px;height:80px;border:2px solid rgba(236,72,153,0.2);bottom:0;right:260px}
        .deco-dot{position:fixed;border-radius:50%;pointer-events:none;z-index:0}
        .dot-pink{width:14px;height:14px;background:#ec4899;top:40px;left:110px;opacity:0.7}
        .dot-blue{width:10px;height:10px;background:#3b82f6;bottom:180px;left:60px;opacity:0.5}

        /* Page content */
        .page-content{flex:1;padding:24px 28px;position:relative;z-index:1}

        /* Alerts */
        .alert{padding:12px 16px;border-radius:10px;margin-bottom:18px;display:flex;align-items:center;gap:10px;font-size:14px;font-weight:500}
        .alert-success{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46}
        .alert-error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}

        /* Medical alert box */
        .medical-alert{
            background:linear-gradient(135deg,#fef2f2,#fff5f5);
            border:2px solid #fca5a5;border-radius:16px;padding:18px 20px;
            margin-bottom:20px;display:flex;gap:14px;
        }
        .medical-alert-icon{font-size:30px;flex-shrink:0}
        .medical-alert h4{color:#991b1b;font-size:15px;font-weight:800;margin-bottom:6px}
        .medical-alert ul{color:#b91c1c;font-size:13px;padding-right:18px}

        /* Cards */
        .card{background:#fff;border-radius:16px;box-shadow:0 1px 4px rgba(0,0,0,0.06),0 4px 16px rgba(0,0,0,0.04)}
        .card-header{padding:18px 22px;border-bottom:1px solid #f3f4f6;display:flex;align-items:center;justify-content:space-between}
        .card-title{font-size:15px;font-weight:700;color:var(--text)}

        /* Stat cards */
        .stat-card{
            background:#fff;border-radius:16px;padding:22px;
            box-shadow:0 2px 8px rgba(0,0,0,0.06);
            position:relative;overflow:hidden;
        }
        .stat-card::before{content:'';position:absolute;top:0;right:0;width:4px;height:100%}
        .stat-card.c-blue::before{background:#3b82f6}
        .stat-card.c-green::before{background:#10b981}
        .stat-card.c-purple::before{background:#8b5cf6}
        .stat-card.c-pink::before{background:#ec4899}
        .stat-card.c-yellow::before{background:#f59e0b}
        .stat-card.c-red::before{background:#ef4444}
        .stat-label{font-size:12px;color:var(--muted);font-weight:600;margin-bottom:8px;text-transform:uppercase;letter-spacing:0.4px}
        .stat-value{font-size:28px;font-weight:800;color:var(--text);line-height:1}
        .stat-icon{font-size:32px;position:absolute;top:18px;left:18px;opacity:0.08}

        /* Buttons */
        .btn{display:inline-flex;align-items:center;gap:7px;padding:9px 18px;border-radius:10px;font-size:13.5px;font-weight:700;font-family:'Cairo',sans-serif;cursor:pointer;border:none;text-decoration:none;transition:all 0.15s;white-space:nowrap}
        .btn-primary{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 4px 12px rgba(37,99,235,0.3)}
        .btn-primary:hover{box-shadow:0 6px 18px rgba(37,99,235,0.4);transform:translateY(-1px)}
        .btn-success{background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 12px rgba(16,185,129,0.3)}
        .btn-success:hover{box-shadow:0 6px 18px rgba(16,185,129,0.4);transform:translateY(-1px)}
        .btn-pink{background:linear-gradient(135deg,#ec4899,#db2777);color:#fff}
        .btn-outline{background:#fff;border:1.5px solid var(--border);color:var(--muted)}
        .btn-outline:hover{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
        .btn-danger{background:#ef4444;color:#fff}
        .btn-sm{padding:7px 14px;font-size:12.5px}
        .btn-xs{padding:5px 10px;font-size:11.5px;border-radius:8px}

        /* Table */
        .table-wrap{overflow-x:auto}
        table{width:100%;border-collapse:collapse;font-size:14px}
        thead th{padding:11px 18px;text-align:right;font-size:11.5px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:0.5px;border-bottom:2px solid #f3f4f6}
        tbody td{padding:14px 18px;border-bottom:1px solid #f9fafb;vertical-align:middle}
        tbody tr:hover{background:#fafbff}
        tbody tr:last-child td{border-bottom:none}
        .td-name{font-weight:700;color:#1d4ed8;text-decoration:none;font-size:14px}
        .td-name:hover{text-decoration:underline}

        /* Badges */
        .badge{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:700;white-space:nowrap}
        .badge-blue{background:#dbeafe;color:#1d4ed8}
        .badge-green{background:#d1fae5;color:#065f46}
        .badge-red{background:#fee2e2;color:#dc2626}
        .badge-yellow{background:#fef3c7;color:#92400e}
        .badge-gray{background:#f3f4f6;color:#4b5563}
        .badge-purple{background:#ede9fe;color:#5b21b6}
        .badge-pink{background:#fce7f3;color:#9d174d}

        /* Form */
        .form-group{margin-bottom:18px}
        .form-label{display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:7px}
        .form-label .req{color:#ef4444}
        .form-control{width:100%;border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:14px;font-family:'Cairo',sans-serif;color:var(--text);background:#fff;transition:all 0.15s;outline:none}
        .form-control:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.1)}
        .form-error{color:#ef4444;font-size:12px;margin-top:5px}
        textarea.form-control{resize:vertical;min-height:80px}
        .check-label{display:flex;align-items:center;gap:9px;cursor:pointer;font-size:14px;color:#374151}
        .check-label input[type=checkbox]{width:16px;height:16px;accent-color:#3b82f6;cursor:pointer}

        /* Search bar */
        .search-bar{background:#fff;border-radius:14px;padding:16px 20px;margin-bottom:22px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;box-shadow:0 1px 4px rgba(0,0,0,0.06)}

        /* Status dropdown */
        select.status-select{border:1.5px solid var(--border);border-radius:8px;padding:6px 10px;font-size:12.5px;font-family:'Cairo',sans-serif;cursor:pointer;outline:none;background:#fff}
        select.status-select:focus{border-color:#3b82f6}

        /* Mini avatar */
        .mini-avatar{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#8b5cf6);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;color:#fff;flex-shrink:0}

        /* Print */
        @media print{.sidebar,.topbar,.no-print{display:none!important}.main-wrap{margin:0!important}}

        [x-cloak]{display:none!important}
    </style>
    @stack('styles')
</head>
<body>

{{-- Decorative circles like reference --}}
<div class="deco-circle deco-1"></div>
<div class="deco-circle deco-2"></div>
<div class="deco-circle deco-3"></div>
<div class="deco-dot dot-pink"></div>
<div class="deco-dot dot-blue"></div>

{{-- SIDEBAR --}}
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="icon">🦷</div>
        <div>
            <div class="name">{{ config('app.name') }}</div>
            <div class="sub">نظام إدارة العيادة</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="nav-icon">📊</span> لوحة التحكم
        </a>

        <div class="nav-section">المرضى</div>
        <a href="{{ route('patients.index') }}" class="nav-item {{ request()->routeIs('patients.index') ? 'active' : '' }}">
            <span class="nav-icon">👥</span> سجل المرضى
        </a>
        <a href="{{ route('patients.create') }}" class="nav-item {{ request()->routeIs('patients.create') ? 'active' : '' }}">
            <span class="nav-icon">➕</span> مريض جديد
        </a>

        <div class="nav-section">العيادة</div>
        <a href="{{ route('appointments.index') }}" class="nav-item {{ request()->routeIs('appointments.*') ? 'active' : '' }}">
            <span class="nav-icon">📅</span> المواعيد
        </a>
        <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
            <span class="nav-icon">💰</span> الفواتير
        </a>
        <a href="{{ route('prescriptions.index') }}" class="nav-item {{ request()->routeIs('prescriptions.*') ? 'active' : '' }}">
            <span class="nav-icon">💊</span> الروشتات
        </a>

        <div class="nav-section">التحليل</div>
        <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
            <span class="nav-icon">📈</span> التقارير
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">{{ mb_substr(auth()->user()->name, 0, 1) }}</div>
            <div>
                <div class="user-name">{{ Str::limit(auth()->user()->name, 18) }}</div>
                <div class="user-role">طبيب أسنان</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">🚪 تسجيل الخروج</button>
        </form>
    </div>
</aside>

{{-- MAIN --}}
<div class="main-wrap">
    <header class="topbar">
        <div class="topbar-left">
            <div class="topbar-search">
                <span style="color:#9ca3af">🔍</span>
                <input type="text" placeholder="بحث عن مريض...">
            </div>
        </div>
        <div style="font-weight:800;font-size:17px;color:var(--text);position:absolute;right:270px">
            @yield('title', 'لوحة التحكم')
        </div>
        <div class="topbar-right">
            @yield('topbar-actions')
            <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">➕ مريض جديد</a>
            <a href="{{ route('appointments.create') }}" class="btn btn-success btn-sm">📅 موعد جديد</a>
        </div>
    </header>

    <div class="page-content">
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
