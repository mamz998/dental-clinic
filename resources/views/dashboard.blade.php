@extends('layouts.app')
@section('title','لوحة التحكم')
@section('content')

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(6,1fr);gap:16px;margin-bottom:24px">
    <div class="stat-card c-blue">
        <div class="stat-icon">👥</div>
        <div class="stat-label">إجمالي المرضى</div>
        <div class="stat-value">{{ number_format($stats['total_patients']) }}</div>
    </div>
    <div class="stat-card c-green">
        <div class="stat-icon">🆕</div>
        <div class="stat-label">جدد هذا الشهر</div>
        <div class="stat-value">{{ $stats['new_patients_month'] }}</div>
    </div>
    <div class="stat-card c-purple">
        <div class="stat-icon">📅</div>
        <div class="stat-label">مواعيد اليوم</div>
        <div class="stat-value">{{ $stats['appointments_today'] }}</div>
    </div>
    <div class="stat-card c-pink">
        <div class="stat-icon">💵</div>
        <div class="stat-label">إيراد اليوم</div>
        <div class="stat-value" style="font-size:20px">{{ number_format($stats['income_today'],0) }}<span style="font-size:13px;font-weight:600"> ج</span></div>
    </div>
    <div class="stat-card c-yellow">
        <div class="stat-icon">💰</div>
        <div class="stat-label">إيراد الشهر</div>
        <div class="stat-value" style="font-size:20px">{{ number_format($stats['income_month'],0) }}<span style="font-size:13px;font-weight:600"> ج</span></div>
    </div>
    <div class="stat-card c-red">
        <div class="stat-icon">⏳</div>
        <div class="stat-label">مبالغ معلقة</div>
        <div class="stat-value" style="font-size:20px;color:#ef4444">{{ number_format($stats['pending_balance'],0) }}<span style="font-size:13px"> ج</span></div>
    </div>
</div>

{{-- Main grid --}}
<div style="display:grid;grid-template-columns:1fr 380px;gap:20px">

    {{-- Today appointments table (like reference) --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 مواعيد اليوم</span>
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
                            <td style="font-weight:700;color:#1d4ed8;font-size:14px">{{ $apt->starts_at->format('H:i') }}</td>
                            <td>
                                <a href="{{ route('patients.show',$apt->patient) }}" class="td-name">{{ $apt->patient->name }}</a>
                            </td>
                            <td style="color:#6b7280;font-size:13px">{{ $apt->patient->phone }}</td>
                            <td style="color:#374151;font-size:13px">{{ $apt->title ?? 'موعد عادي' }}</td>
                            <td>
                                <form method="POST" action="{{ route('appointments.status',$apt) }}">
                                    @csrf @method('PATCH')
                                    <select name="status" onchange="this.form.submit()" class="status-select">
                                        @foreach(['scheduled'=>'محدد','confirmed'=>'مؤكد','completed'=>'مكتمل','cancelled'=>'ملغي','no_show'=>'لم يحضر'] as $v=>$l)
                                            <option value="{{ $v }}" {{ $apt->status===$v?'selected':'' }}>{{ $l }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;padding:48px;color:#9ca3af">
                            <div style="font-size:40px;margin-bottom:10px">📅</div>
                            لا يوجد مواعيد اليوم
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($todayAppointments->count())
        <div style="padding:14px 22px;border-top:1px solid #f3f4f6;text-align:center">
            <a href="{{ route('appointments.index') }}" style="color:#3b82f6;font-size:13px;font-weight:600;text-decoration:none">عرض الكل ←</a>
        </div>
        @endif
    </div>

    {{-- Right column --}}
    <div style="display:flex;flex-direction:column;gap:16px">

        {{-- Complete / Urgent circles (like reference) --}}
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
            <div class="card" style="padding:18px;text-align:center">
                <div style="font-size:12px;color:#6b7280;font-weight:600;margin-bottom:12px">مواعيد مكتملة</div>
                <div style="position:relative;width:64px;height:64px;margin:0 auto">
                    <svg viewBox="0 0 64 64" style="width:64px;height:64px;transform:rotate(-90deg)">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#f3f4f6" stroke-width="6"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#3b82f6" stroke-width="6"
                            stroke-dasharray="{{ $stats['appointments_today']>0 ? round(163.4 * ($todayAppointments->where('status','completed')->count() / max($stats['appointments_today'],1))) : 0 }} 163.4"
                            stroke-linecap="round"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#1d4ed8">
                        {{ $todayAppointments->where('status','completed')->count() }}/{{ $stats['appointments_today'] }}
                    </div>
                </div>
            </div>
            <div class="card" style="padding:18px;text-align:center">
                <div style="font-size:12px;color:#6b7280;font-weight:600;margin-bottom:12px">مبالغ معلقة</div>
                <div style="position:relative;width:64px;height:64px;margin:0 auto">
                    <svg viewBox="0 0 64 64" style="width:64px;height:64px;transform:rotate(-90deg)">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#f3f4f6" stroke-width="6"/>
                        <circle cx="32" cy="32" r="26" fill="none" stroke="#ec4899" stroke-width="6"
                            stroke-dasharray="100 163.4" stroke-linecap="round"/>
                    </svg>
                    <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:800;color:#db2777">
                        معلق
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent patients --}}
        <div class="card" style="flex:1">
            <div class="card-header">
                <span class="card-title">👥 آخر المرضى</span>
                <a href="{{ route('patients.index') }}" style="font-size:12px;color:#3b82f6;text-decoration:none;font-weight:600">عرض الكل</a>
            </div>
            @forelse($recentPatients as $pt)
                <div style="padding:13px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb">
                    <div style="display:flex;align-items:center;gap:10px">
                        <div class="mini-avatar">{{ mb_substr($pt->name,0,1) }}</div>
                        <div>
                            <a href="{{ route('patients.show',$pt) }}" style="font-weight:700;font-size:13.5px;color:#111827;text-decoration:none">{{ $pt->name }}</a>
                            <div style="font-size:11px;color:#9ca3af">{{ $pt->phone }}</div>
                        </div>
                    </div>
                    @if($pt->hasRisk())
                        <span class="badge badge-red" style="font-size:11px">⚠️</span>
                    @endif
                </div>
            @empty
                <div style="padding:30px;text-align:center;color:#9ca3af;font-size:13px">لا يوجد مرضى بعد</div>
            @endforelse
        </div>
    </div>
</div>

@endsection
