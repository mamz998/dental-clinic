@extends('layouts.app')
@section('title', 'لوحتي')
@section('content')

{{-- Page header --}}
<div class="page-header">
    <div>
        <h1>أهلاً، {{ $user->name }}</h1>
        <p>{{ now()->translatedFormat('l، j F Y') }}</p>
    </div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px">

    <div class="stat-card sc-blue">
        <div class="sc-icon-wrap">👥</div>
        <div class="stat-label">مرضاي</div>
        <div class="stat-value">{{ $stats['my_patients'] }}</div>
        <div class="stat-trend trend-flat">إجمالي</div>
    </div>

    <div class="stat-card sc-green">
        <div class="sc-icon-wrap">🆕</div>
        <div class="stat-label">جدد هذا الشهر</div>
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
        <div class="sc-icon-wrap">📆</div>
        <div class="stat-label">مواعيد الشهر</div>
        <div class="stat-value">{{ $stats['appointments_month'] }}</div>
        <div class="stat-trend trend-flat">هذا الشهر</div>
    </div>

</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:18px">

    {{-- مواعيد اليوم --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">مواعيدي اليوم</span>
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
                                <span style="font-weight:700;color:var(--accent);font-size:13px;
                                    background:var(--accent-soft);padding:3px 8px;border-radius:6px">
                                    {{ $apt->starts_at->format('H:i') }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('patients.show', $apt->patient) }}" class="td-name">
                                    {{ $apt->patient->name }}
                                </a>
                            </td>
                            <td style="color:var(--text-2);font-size:12.5px">{{ $apt->patient->phone }}</td>
                            <td style="color:var(--text-2);font-size:12.5px">{{ $apt->title ?? 'موعد عادي' }}</td>
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
                            <td colspan="5" style="text-align:center;padding:48px 16px;color:var(--text-3)">
                                <div style="font-size:36px;margin-bottom:8px;opacity:0.4">📅</div>
                                لا يوجد مواعيد اليوم
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- مرضاي الأخيرون --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">مرضاي الأخيرون</span>
            <a href="{{ route('patients.index') }}"
               style="font-size:12px;color:var(--accent);text-decoration:none;font-weight:600">
                عرض الكل
            </a>
        </div>
        @forelse($recentPatients as $pt)
            <div style="padding:11px 18px;display:flex;align-items:center;
                justify-content:space-between;border-bottom:1px solid var(--border-soft)">
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
                       style="font-size:11px;color:var(--accent);text-decoration:none;font-weight:600">←</a>
                </div>
            </div>
        @empty
            <div style="padding:32px;text-align:center;color:var(--text-3);font-size:13px">
                لا يوجد مرضى بعد
            </div>
        @endforelse
    </div>

</div>

@endsection
