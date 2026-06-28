@extends('layouts.app')
@section('title','لوحة الدكتور')
@section('content')

{{-- ترحيب --}}
<div style="margin-bottom:24px">
    <div style="font-size:22px;font-weight:800;color:#111827">أهلاً، د. {{ $user->name }} 👨‍⚕️</div>
    <div style="font-size:13px;color:#6b7280;margin-top:4px">{{ now()->format('l، j F Y') }}</div>
</div>

{{-- Stats --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px">
    <div class="stat-card c-blue">
        <div class="stat-icon">👥</div>
        <div class="stat-label">مرضاي</div>
        <div class="stat-value">{{ $stats['my_patients'] }}</div>
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
        <div class="stat-icon">📆</div>
        <div class="stat-label">مواعيد الشهر</div>
        <div class="stat-value">{{ $stats['appointments_month'] }}</div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 380px;gap:20px">

    {{-- مواعيد اليوم --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">📅 مواعيدي اليوم</span>
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
    </div>

    {{-- مرضاي الأخيرون --}}
    <div class="card">
        <div class="card-header">
            <span class="card-title">👥 مرضاي الأخيرون</span>
            <a href="{{ route('patients.index') }}" style="font-size:12px;color:#3b82f6;text-decoration:none;font-weight:600">عرض الكل</a>
        </div>
        @forelse($recentPatients as $pt)
            <div style="padding:12px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f9fafb">
                <div style="display:flex;align-items:center;gap:10px">
                    <div class="mini-avatar">{{ mb_substr($pt->name,0,1) }}</div>
                    <div>
                        <a href="{{ route('patients.show',$pt) }}" style="font-weight:700;font-size:13px;color:#111827;text-decoration:none">{{ $pt->name }}</a>
                        <div style="font-size:11px;color:#9ca3af">{{ $pt->phone }}</div>
                    </div>
                </div>
                <div style="display:flex;gap:6px;align-items:center">
                    @if($pt->hasRisk())
                        <span class="badge badge-red" title="مريض خطر">⚠️</span>
                    @endif
                    <a href="{{ route('patients.show',$pt) }}" style="font-size:11px;color:#3b82f6;text-decoration:none;font-weight:600">ملف ←</a>
                </div>
            </div>
        @empty
            <div style="padding:30px;text-align:center;color:#9ca3af;font-size:13px">لا يوجد مرضى بعد</div>
        @endforelse
    </div>

</div>

@endsection
