@extends('layouts.app')

@section('title', 'المواعيد')

@section('content')

<div class="search-bar">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%">
        <div style="display:flex;background:#f1f5f9;border-radius:10px;padding:3px;gap:3px">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'day']) }}"
                style="padding:6px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.15s;{{ $view==='day' ? 'background:white;color:#38bdf8;box-shadow:0 1px 4px rgba(0,0,0,0.08)' : 'color:#64748b' }}">
                يومي
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'week']) }}"
                style="padding:6px 16px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all 0.15s;{{ $view==='week' ? 'background:white;color:#38bdf8;box-shadow:0 1px 4px rgba(0,0,0,0.08)' : 'color:#64748b' }}">
                أسبوعي
            </a>
        </div>
        <input type="date" name="date" value="{{ $date->format('Y-m-d') }}" onchange="this.form.submit()"
            class="form-control" style="width:180px">
        <input type="hidden" name="view" value="{{ $view }}">
        <div style="display:flex;gap:6px">
            <a href="{{ request()->fullUrlWithQuery(['date' => $date->copy()->subDay()->format('Y-m-d')]) }}" class="btn btn-outline btn-sm">◀ السابق</a>
            <a href="{{ request()->fullUrlWithQuery(['date' => now()->format('Y-m-d')]) }}" class="btn btn-sm" style="background:#eff6ff;color:#3b82f6;font-weight:700">اليوم</a>
            <a href="{{ request()->fullUrlWithQuery(['date' => $date->copy()->addDay()->format('Y-m-d')]) }}" class="btn btn-outline btn-sm">التالي ▶</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <div>
            <span class="card-title">
                @if($view==='day')
                    📅 {{ $date->format('d/m/Y') }}
                    @if($date->isToday()) <span style="color:#38bdf8;font-size:13px;font-weight:600">(اليوم)</span> @endif
                @else
                    📅 مواعيد الأسبوع
                @endif
            </span>
            <span style="font-size:13px;color:#94a3b8;margin-right:8px">{{ $appointments->count() }} موعد</span>
        </div>
        <a href="{{ route('appointments.create', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-success btn-sm">+ موعد جديد</a>
    </div>

    @if($appointments->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">
            <div style="font-size:60px;margin-bottom:12px">📅</div>
            <div style="font-size:16px;font-weight:600;margin-bottom:6px">لا يوجد مواعيد</div>
            <a href="{{ route('appointments.create', ['date' => $date->format('Y-m-d')]) }}" class="btn btn-success" style="margin-top:12px;display:inline-flex">+ إضافة موعد</a>
        </div>
    @else
        @foreach($appointments as $apt)
            <div style="padding:16px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f8fafc;gap:12px">
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-radius:12px;padding:10px 12px;text-align:center;min-width:64px;flex-shrink:0">
                        <div style="font-size:15px;font-weight:800;color:#1d4ed8">{{ $apt->starts_at->format('H:i') }}</div>
                        <div style="font-size:11px;color:#93c5fd">{{ $apt->ends_at->format('H:i') }}</div>
                    </div>
                    <div>
                        <a href="{{ route('patients.show', $apt->patient) }}" style="font-weight:700;color:#1e293b;text-decoration:none;font-size:15px;display:block">
                            {{ $apt->patient->name }}
                        </a>
                        <span style="font-size:13px;color:#64748b">{{ $apt->title ?? 'موعد عادي' }}</span>
                        @if($apt->notes)
                            <span style="font-size:12px;color:#94a3b8;display:block">{{ $apt->notes }}</span>
                        @endif
                    </div>
                </div>
                <div style="display:flex;align-items:center;gap:10px;flex-shrink:0">
                    <form method="POST" action="{{ route('appointments.status', $apt) }}">
                        @csrf @method('PATCH')
                        <select name="status" onchange="this.form.submit()" class="form-control" style="width:auto;padding:6px 12px;font-size:13px">
                            @foreach(['scheduled'=>'محدد','confirmed'=>'مؤكد','completed'=>'مكتمل','cancelled'=>'ملغي','no_show'=>'لم يحضر'] as $val=>$lbl)
                                <option value="{{ $val }}" {{ $apt->status===$val ? 'selected' : '' }}>{{ $lbl }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('appointments.edit', $apt) }}" class="btn btn-outline btn-sm">✏️</a>
                </div>
            </div>
        @endforeach
    @endif
</div>

@endsection
