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

@endsection
