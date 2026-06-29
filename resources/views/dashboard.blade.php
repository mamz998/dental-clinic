@extends('layouts.app')
@section('title', 'لوحة التحكم')
@section('content')

<div class="page-header">
    <div>
        <h1>لوحة التحكم</h1>
        <p style="color:var(--text-3)">{{ now()->translatedFormat('l، j F Y') }}</p>
    </div>
</div>

{{-- ── Stat Cards ── --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:14px;margin-bottom:24px">

    <div class="stat-card sc-blue">
        <div class="sc-icon-wrap">👥</div>
        <div class="stat-label">إجمالي المرضى</div>
        <div class="stat-value">{{ number_format($stats['total_patients']) }}</div>
        <div class="stat-trend trend-flat">إجمالي</div>
    </div>

    <div class="stat-card sc-green">
        <div class="sc-icon-wrap">🆕</div>
        <div class="stat-label">جدد الشهر</div>
        <div class="stat-value">{{ $stats['new_patients_month'] }}</div>
        <div class="stat-trend trend-up">↑ هذا الشهر</div>
    </div>

    <div class="stat-card sc-purple">
        <div class="sc-icon-wrap">📅</div>
        <div class="stat-label">مواعيد اليوم</div>
        <div class="stat-value">{{ $stats['appointments_today'] }}</div>
        <div class="stat-trend trend-flat">اليوم</div>
    </div>

    <div class="stat-card sc-orange">
        <div class="sc-icon-wrap">💵</div>
        <div class="stat-label">إيراد اليوم</div>
        <div class="stat-value" style="font-size:20px">
            {{ number_format($stats['income_today'] ?? 0) }}
            <span style="font-size:12px;color:var(--text-3)">ج</span>
        </div>
        <div class="stat-trend trend-up">↑ اليوم</div>
    </div>

    <div class="stat-card sc-blue">
        <div class="sc-icon-wrap">💰</div>
        <div class="stat-label">إيراد الشهر</div>
        <div class="stat-value" style="font-size:20px">
            {{ number_format($stats['income_month'] ?? 0) }}
            <span style="font-size:12px;color:var(--text-3)">ج</span>
        </div>
        <div class="stat-trend trend-up">↑ الشهر</div>
    </div>

    <div class="stat-card sc-red">
        <div class="sc-icon-wrap">⏳</div>
        <div class="stat-label">مبالغ معلقة</div>
        <div class="stat-value" style="font-size:20px;color:var(--red)">
            {{ number_format($stats['pending_balance'] ?? 0) }}
            <span style="font-size:12px">ج</span>
        </div>
        <div class="stat-trend trend-down">↓ غير محصّل</div>
    </div>

</div>

{{-- ── Main Grid ── --}}
<div style="display:grid;grid-template-columns:1fr 320px;gap:16px">

    {{-- مواعيد اليوم --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">مواعيد اليوم</span>
            <a href="{{ route('appointments.create') }}" class="btn btn-primary btn-sm">+ موعد جديد</a>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>الوقت</th>
                        <th>المريض</th>
                        <th>التليفون</th>
                        <th>الموعد</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($todayAppointments as $apt)
                        <tr>
                            <td>
                                <span style="font-weight:700;color:var(--blue);font-size:13px;
                                    background:rgba(79,124,255,0.1);padding:3px 8px;border-radius:6px;
                                    border:1px solid rgba(79,124,255,0.2)">
                                    {{ $apt->starts_at->format('H:i') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('patients.show', $apt->patient) }}" class="td-name">
                                    {{ $apt->patient->name }}
                                </a>
                            </td>
                            <td style="font-size:12.5px">{{ $apt->patient->phone }}</td>
                            <td style="font-size:12.5px">{{ $apt->title ?? 'موعد عادي' }}</td>
                            <td>
                                <form method="POST" action="{{ route('appointments.status', $apt) }}">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                        @foreach(['scheduled'=>'محدد','confirmed'=>'مؤكد','completed'=>'مكتمل','cancelled'=>'ملغي','no_show'=>'لم يحضر'] as $v => $l)
                                            <option value="{{ $v }}" {{ $apt->status === $v ? 'selected' : '' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center;padding:52px 16px">
                                <div style="font-size:36px;margin-bottom:8px;opacity:0.2">📅</div>
                                <div style="color:var(--text-3);font-size:13px">لا يوجد مواعيد اليوم</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($todayAppointments->count())
        <div style="padding:12px 20px;border-top:1px solid var(--border)">
            <a href="{{ route('appointments.index') }}"
               style="font-size:12.5px;font-weight:600;color:var(--blue);text-decoration:none">
                عرض كل المواعيد ←
            </a>
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:14px">

        {{-- دوائر الإنجاز --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px">

            <div class="card" style="padding:18px;text-align:center">
                <div style="font-size:10px;font-weight:700;color:var(--text-3);margin-bottom:12px;text-transform:uppercase;letter-spacing:.8px">مكتملة</div>
                @php
                    $completed = $todayAppointments->where('status','completed')->count();
                    $total     = max($stats['appointments_today'], 1);
                    $pct       = round(($completed / $total) * 100);
                    $dash      = round(163.4 * ($completed / $total));
                @endphp
                <div style="position:relative;width:64px;height:64px;margin:0 auto">
                    <svg viewBox="0 0 64 64" style="width:64px;height:64px;transform:rotate(-90deg)">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="5"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#22d3a0" stroke-width="5"
                            stroke-dasharray="{{ $dash }} 163.4" stroke-linecap="round"
                            style="filter:drop-shadow(0 0 4px rgba(34,211,160,0.5))"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                        font-size:13px;font-weight:800;color:#22d3a0">{{ $pct }}%</div>
                </div>
                <div style="font-size:12px;color:var(--text-3);margin-top:8px">{{ $completed }}/{{ $stats['appointments_today'] }}</div>
            </div>

            <div class="card" style="padding:18px;text-align:center">
                <div style="font-size:10px;font-weight:700;color:var(--text-3);margin-bottom:12px;text-transform:uppercase;letter-spacing:.8px">معلق</div>
                @php
                    $pendingAmt = $stats['pending_balance'] ?? 0;
                    $totalAmt   = max(($stats['income_month'] ?? 0) + $pendingAmt, 1);
                    $pctPend    = min(round(($pendingAmt / $totalAmt) * 100), 100);
                    $dashPend   = round(163.4 * ($pctPend / 100));
                @endphp
                <div style="position:relative;width:64px;height:64px;margin:0 auto">
                    <svg viewBox="0 0 64 64" style="width:64px;height:64px;transform:rotate(-90deg)">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="rgba(255,255,255,0.06)" stroke-width="5"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#f43f5e" stroke-width="5"
                            stroke-dasharray="{{ $dashPend }} 163.4" stroke-linecap="round"
                            style="filter:drop-shadow(0 0 4px rgba(244,63,94,0.5))"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
                        font-size:12px;font-weight:800;color:#f43f5e">{{ $pctPend }}%</div>
                </div>
                <div style="font-size:11.5px;color:var(--text-3);margin-top:8px">{{ number_format($pendingAmt, 0) }} ج</div>
            </div>

        </div>

        {{-- آخر المرضى --}}
        <div class="card" style="flex:1">
            <div class="card-header">
                <span class="card-title">آخر المرضى</span>
                <a href="{{ route('patients.index') }}"
                   style="font-size:12px;color:var(--blue);text-decoration:none;font-weight:600">عرض الكل</a>
            </div>
            @forelse($recentPatients as $pt)
                <div style="padding:11px 18px;display:flex;align-items:center;
                    justify-content:space-between;border-bottom:1px solid var(--border)">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="mini-avatar">{{ mb_substr($pt->name, 0, 1) }}</div>
                        <div>
                            <a href="{{ route('patients.show', $pt) }}"
                               style="font-weight:700;font-size:13px;color:var(--text);text-decoration:none">
                                {{ $pt->name }}
                            </a>
                            <div style="font-size:11px;color:var(--text-3)">{{ $pt->phone }}</div>
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:6px">
                        @if($pt->hasRisk())
                            <span class="badge badge-red" style="font-size:10px">⚠️</span>
                        @endif
                        <a href="{{ route('patients.show', $pt) }}"
                           style="font-size:11px;color:var(--blue);text-decoration:none;font-weight:600">←</a>
                    </div>
                </div>
            @empty
                <div style="padding:32px;text-align:center;color:var(--text-3);font-size:13px">
                    لا يوجد مرضى بعد
                </div>
            @endforelse
        </div>

    </div>
</div>

@endsection
