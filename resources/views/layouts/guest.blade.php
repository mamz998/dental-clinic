<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — تسجيل الدخول</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        *{font-family:'Cairo',sans-serif;box-sizing:border-box;margin:0;padding:0}
        body{
            min-height:100vh;
            background:linear-gradient(135deg,#dbeafe 0%,#eff6ff 50%,#e0e7ff 100%);
            display:flex;align-items:center;justify-content:center;
            overflow:hidden;position:relative;
        }

        /* Decorative circles like reference */
        .deco{position:fixed;border-radius:50%;pointer-events:none}
        .d1{width:220px;height:220px;border:2px solid rgba(59,130,246,0.2);top:-60px;left:-60px}
        .d2{width:140px;height:140px;border:2px solid rgba(59,130,246,0.15);bottom:80px;left:30px}
        .d3{width:90px;height:90px;border:2px solid rgba(236,72,153,0.25);bottom:-10px;left:300px}
        .d4{width:160px;height:160px;border:2px solid rgba(59,130,246,0.1);top:80px;right:-50px}
        .dot{position:fixed;border-radius:50%;pointer-events:none}
        .dp1{width:16px;height:16px;background:#ec4899;top:50px;left:130px}
        .dp2{width:10px;height:10px;background:#ec4899;bottom:80px;right:60px}
        .db1{width:12px;height:12px;background:#3b82f6;bottom:220px;left:70px;opacity:0.5}

        /* Main layout: sidebar blue + white form */
        .login-wrap{
            display:flex;width:900px;max-width:95vw;
            border-radius:28px;overflow:hidden;
            box-shadow:0 24px 64px rgba(0,0,0,0.12);
            position:relative;z-index:10;
        }

        /* Left panel — blue */
        .login-left{
            width:340px;flex-shrink:0;
            background:linear-gradient(160deg,#1a56db 0%,#1e429f 60%,#102a50 100%);
            padding:48px 36px;display:flex;flex-direction:column;
            align-items:center;justify-content:center;text-align:center;
        }
        .ll-tooth{
            width:80px;height:80px;
            background:rgba(255,255,255,0.15);
            border-radius:24px;display:flex;align-items:center;justify-content:center;
            font-size:42px;margin-bottom:24px;
            backdrop-filter:blur(8px);
            border:1px solid rgba(255,255,255,0.2);
        }
        .ll-title{color:#fff;font-size:22px;font-weight:800;margin-bottom:8px}
        .ll-sub{color:rgba(255,255,255,0.55);font-size:13px;line-height:1.7}
        .ll-divider{width:48px;height:3px;background:rgba(255,255,255,0.25);border-radius:2px;margin:28px auto}
        .ll-features{text-align:right;width:100%}
        .ll-feature{display:flex;align-items:center;gap:10px;color:rgba(255,255,255,0.75);font-size:13px;margin-bottom:14px}
        .ll-feature-icon{width:30px;height:30px;background:rgba(255,255,255,0.1);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;flex-shrink:0}

        /* Right panel — white */
        .login-right{
            flex:1;background:#fff;padding:48px 44px;
            display:flex;flex-direction:column;justify-content:center;
        }
        .login-right h2{font-size:24px;font-weight:800;color:#111827;margin-bottom:6px}
        .login-right .welcome-sub{font-size:14px;color:#6b7280;margin-bottom:36px}

        .form-group{margin-bottom:20px}
        .form-label{display:block;font-size:13px;font-weight:700;color:#374151;margin-bottom:8px}
        .form-input{
            width:100%;border:1.5px solid #e5e7eb;border-radius:12px;
            padding:13px 16px;font-size:14px;font-family:'Cairo',sans-serif;
            color:#111827;outline:none;transition:all 0.2s;background:#f9fafb;
        }
        .form-input:focus{border-color:#3b82f6;background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,0.1)}
        .form-error{color:#ef4444;font-size:12px;margin-top:5px}

        .remember-row{display:flex;align-items:center;gap:8px;margin-bottom:28px}
        .remember-row input{width:16px;height:16px;accent-color:#3b82f6;cursor:pointer}
        .remember-row label{font-size:13px;color:#6b7280;cursor:pointer}

        .btn-login{
            width:100%;background:linear-gradient(135deg,#2563eb,#1d4ed8);
            border:none;border-radius:12px;padding:14px;
            color:#fff;font-size:15px;font-weight:800;font-family:'Cairo',sans-serif;
            cursor:pointer;transition:all 0.2s;
            box-shadow:0 6px 20px rgba(37,99,235,0.35);
        }
        .btn-login:hover{box-shadow:0 10px 28px rgba(37,99,235,0.45);transform:translateY(-1px)}

        .login-footer{text-align:center;margin-top:20px;font-size:12px;color:#9ca3af}
    </style>
</head>
<body>
    <div class="deco d1"></div>
    <div class="deco d2"></div>
    <div class="deco d3"></div>
    <div class="deco d4"></div>
    <div class="dot dp1"></div>
    <div class="dot dp2"></div>
    <div class="dot db1"></div>

    <div class="login-wrap">
        {{-- Left blue panel --}}
        <div class="login-left">
            <div class="ll-tooth">🦷</div>
            <div class="ll-title">{{ config('app.name') }}</div>
            <div class="ll-sub">نظام إدارة عيادة الأسنان المتكامل</div>
            <div class="ll-divider"></div>
            <div class="ll-features">
                <div class="ll-feature">
                    <div class="ll-feature-icon">👥</div>
                    إدارة سجلات المرضى
                </div>
                <div class="ll-feature">
                    <div class="ll-feature-icon">🦷</div>
                    خريطة الأسنان التفاعلية
                </div>
                <div class="ll-feature">
                    <div class="ll-feature-icon">📅</div>
                    جدولة المواعيد
                </div>
                <div class="ll-feature">
                    <div class="ll-feature-icon">💰</div>
                    الفواتير والمدفوعات
                </div>
            </div>
        </div>

        {{-- Right white form --}}
        <div class="login-right">
            <h2>أهلاً بك 👋</h2>
            <p class="welcome-sub">سجّل دخولك للوصول إلى لوحة التحكم</p>

            {{ $slot }}

            <div class="login-footer">
                جميع البيانات محمية ومشفرة 🔒
            </div>
        </div>
    </div>
</body>
</html>
