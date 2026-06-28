@extends('layouts.app')

@section('title', 'التقارير')

@section('content')

<div class="search-bar" style="margin-bottom:24px">
    <form method="GET" style="display:flex;align-items:center;gap:12px">
        <label style="font-size:14px;font-weight:600;color:#374151">الشهر:</label>
        <input type="month" name="month" value="{{ $month }}" onchange="this.form.submit()"
            class="form-control" style="width:180px">
    </form>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:16px;margin-bottom:24px">
    <div class="stat-card green">
        <div class="stat-icon">💰</div>
        <div class="stat-label">إيراد الشهر</div>
        <div class="stat-value" style="font-size:22px">{{ number_format($totalIncome, 0) }} ج</div>
    </div>
    <div class="stat-card blue">
        <div class="stat-icon">🆕</div>
        <div class="stat-label">مرضى جدد</div>
        <div class="stat-value">{{ $newPatients }}</div>
    </div>
    <div class="stat-card purple">
        <div class="stat-icon">🔄</div>
        <div class="stat-label">مرضى عائدون</div>
        <div class="stat-value">{{ $returningPatients }}</div>
    </div>
    <div class="stat-card red">
        <div class="stat-icon">⏳</div>
        <div class="stat-label">مبالغ معلقة</div>
        <div class="stat-value" style="color:#ef4444;font-size:22px">{{ number_format($pendingBalance, 0) }} ج</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">

    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 الإيراد اليومي</span>
        </div>
        @if($dailyIncome->isEmpty())
            <div style="padding:40px;text-align:center;color:#94a3b8">لا يوجد إيرادات</div>
        @else
            @php $maxDay = $dailyIncome->max('total') ?: 1; @endphp
            <div style="padding:16px;overflow-y:auto;max-height:360px">
                @foreach($dailyIncome as $day)
                    <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px">
                        <div style="font-size:12px;color:#64748b;white-space:nowrap;width:70px">{{ \Carbon\Carbon::parse($day->day)->format('d/m') }}</div>
                        <div style="flex:1;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden">
                            <div style="height:100%;background:linear-gradient(90deg,#38bdf8,#818cf8);border-radius:4px;width:{{ ($day->total/$maxDay)*100 }}%"></div>
                        </div>
                        <div style="font-size:13px;font-weight:700;color:#1e293b;white-space:nowrap;width:80px;text-align:left">{{ number_format($day->total, 0) }} ج</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="card">
        <div class="card-header">
            <span class="card-title">📈 الإيراد الشهري (آخر 6 أشهر)</span>
        </div>
        <div style="padding:20px">
            @php $maxMonth = $monthlyIncome->max('total') ?: 1; @endphp
            @foreach($monthlyIncome as $m)
                <div style="margin-bottom:14px">
                    <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:5px">
                        <span style="color:#64748b">{{ $m['month'] }}</span>
                        <span style="font-weight:700;color:#1e293b">{{ number_format($m['total'], 0) }} ج</span>
                    </div>
                    <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden">
                        <div style="height:100%;background:linear-gradient(90deg,#22c55e,#16a34a);border-radius:4px;width:{{ ($m['total']/$maxMonth)*100 }}%;transition:width 0.5s"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>


{{-- ════════════════════════════════════════════
     تقرير الدكاترة
════════════════════════════════════════════ --}}
<div style="margin-top:28px">
    <div style="font-size:18px;font-weight:800;color:#111827;margin-bottom:16px;display:flex;align-items:center;gap:8px">
        <span>👨‍⚕️</span> تقرير الدكاترة — {{ \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y') }}
    </div>

    @forelse($doctors as $dr)
    <div class="card" style="margin-bottom:20px">

        {{-- رأس الدكتور --}}
        <div style="padding:18px 20px;border-bottom:1px solid #f1f5f9;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px">
            <div style="display:flex;align-items:center;gap:14px">
                <div style="width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#3b82f6,#6366f1);display:flex;align-items:center;justify-content:center;color:#fff;font-size:18px;font-weight:800">
                    {{ mb_substr($dr['name'], 0, 1) }}
                </div>
                <div>
                    <div style="font-size:16px;font-weight:800;color:#111827">{{ $dr['name'] }}</div>
                    <div style="font-size:12px;color:#6b7280;margin-top:2px">{{ $dr['specialty'] }}</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
                <span style="background:#eff6ff;color:#1d4ed8;font-size:13px;font-weight:700;padding:6px 14px;border-radius:20px">
                    نسبة {{ $dr['commission'] }}%
                </span>
                <a href="{{ route('patients.index') }}?doctor={{ $dr['id'] }}"
                   style="background:#f0fdf4;color:#16a34a;font-size:12px;font-weight:600;padding:6px 12px;border-radius:8px;text-decoration:none">
                    عرض المرضى ←
                </a>
            </div>
        </div>

        {{-- الأرقام --}}
        <div style="padding:20px;display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:16px">

            <div style="text-align:center;padding:14px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#9ca3af;font-weight:600;margin-bottom:6px">حالات الشهر</div>
                <div style="font-size:26px;font-weight:900;color:#1d4ed8">{{ $dr['cases'] }}</div>
                <div style="font-size:11px;color:#9ca3af">فاتورة</div>
            </div>

            <div style="text-align:center;padding:14px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#9ca3af;font-weight:600;margin-bottom:6px">إجمالي الفواتير</div>
                <div style="font-size:20px;font-weight:900;color:#374151">{{ number_format($dr['total_billed'],0) }}</div>
                <div style="font-size:11px;color:#9ca3af">جنيه</div>
            </div>

            <div style="text-align:center;padding:14px;background:#f0fdf4;border-radius:10px">
                <div style="font-size:11px;color:#9ca3af;font-weight:600;margin-bottom:6px">المحصّل فعلياً</div>
                <div style="font-size:20px;font-weight:900;color:#16a34a">{{ number_format($dr['total_collected'],0) }}</div>
                <div style="font-size:11px;color:#9ca3af">جنيه</div>
            </div>

            <div style="text-align:center;padding:14px;background:#fff7ed;border-radius:10px">
                <div style="font-size:11px;color:#9ca3af;font-weight:600;margin-bottom:6px">متبقي غير محصّل</div>
                <div style="font-size:20px;font-weight:900;color:#ea580c">{{ number_format($dr['pending'],0) }}</div>
                <div style="font-size:11px;color:#9ca3af">جنيه</div>
            </div>

            <div style="text-align:center;padding:14px;background:#fdf4ff;border-radius:10px;border:2px solid #d946ef22">
                <div style="font-size:11px;color:#9ca3af;font-weight:600;margin-bottom:6px">نصيب الدكتور ({{ $dr['commission'] }}%)</div>
                <div style="font-size:20px;font-weight:900;color:#9333ea">{{ number_format($dr['dr_share'],0) }}</div>
                <div style="font-size:11px;color:#9ca3af">جنيه</div>
            </div>

            <div style="text-align:center;padding:14px;background:#f8fafc;border-radius:10px">
                <div style="font-size:11px;color:#9ca3af;font-weight:600;margin-bottom:6px">المواعيد</div>
                <div style="font-size:20px;font-weight:900;color:#374151">{{ $dr['appt_total'] }}</div>
                <div style="display:flex;justify-content:center;gap:8px;margin-top:4px;font-size:11px">
                    <span style="color:#16a34a">✔ {{ $dr['appt_completed'] }}</span>
                    <span style="color:#ef4444">✘ {{ $dr['appt_cancelled'] }}</span>
                </div>
            </div>

        </div>

        {{-- شريط التحصيل --}}
        @if($dr['total_billed'] > 0)
        <div style="padding:0 20px 18px">
            @php $pct = round(($dr['total_collected']/$dr['total_billed'])*100); @endphp
            <div style="display:flex;justify-content:space-between;font-size:12px;color:#6b7280;margin-bottom:6px">
                <span>نسبة التحصيل</span>
                <span style="font-weight:700;color:{{ $pct>=80?'#16a34a':($pct>=50?'#ea580c':'#dc2626') }}">{{ $pct }}%</span>
            </div>
            <div style="height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden">
                <div style="height:100%;width:{{ $pct }}%;border-radius:4px;
                    background:{{ $pct>=80?'linear-gradient(90deg,#22c55e,#16a34a)':($pct>=50?'linear-gradient(90deg,#fb923c,#ea580c)':'linear-gradient(90deg,#f87171,#dc2626)') }};
                    transition:width 0.6s"></div>
            </div>
        </div>
        @endif

        {{-- إجمالي كل الوقت --}}
        <div style="padding:12px 20px;background:#f8fafc;border-top:1px solid #f1f5f9;display:flex;gap:24px;flex-wrap:wrap">
            <div style="font-size:12px;color:#6b7280">
                إجمالي كل الوقت:
                <strong style="color:#374151;margin-right:4px">{{ number_format($dr['all_billed'],0) }} ج</strong>
                فواتير —
                <strong style="color:#16a34a;margin-right:4px">{{ number_format($dr['all_collected'],0) }} ج</strong>
                محصّل —
                <strong style="color:#9333ea;margin-right:4px">{{ number_format($dr['all_collected']*($dr['commission']/100),0) }} ج</strong>
                نصيب الدكتور
            </div>
        </div>

    </div>
    @empty
        <div style="padding:40px;text-align:center;color:#9ca3af">لا يوجد دكاترة مسجلين</div>
    @endforelse
</div>

@endsection
