@extends('layouts.app')

@section('title', 'الروشتات')

@section('content')

<div class="search-bar">
    <form method="GET" style="display:flex;gap:10px;width:100%">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="🔍 بحث باسم المريض..." class="form-control" style="flex:1">
        <button type="submit" class="btn btn-primary">بحث</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">الروشتات</span>
        <a href="{{ route('prescriptions.create') }}" class="btn btn-purple btn-sm">➕ روشتة جديدة</a>
    </div>
    @if($prescriptions->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">
            <div style="font-size:60px;margin-bottom:12px">💊</div>
            <div>لا يوجد روشتات</div>
        </div>
    @else
        <div>
            @foreach($prescriptions as $rx)
                <div style="padding:14px 20px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #f8fafc">
                    <div style="display:flex;align-items:center;gap:14px">
                        <div style="width:40px;height:40px;background:#faf5ff;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0">💊</div>
                        <div>
                            <div style="display:flex;align-items:center;gap:8px">
                                <a href="{{ route('prescriptions.show', $rx) }}" style="font-weight:700;color:#38bdf8;text-decoration:none">
                                    روشتة {{ $rx->prescription_date->format('d/m/Y') }}
                                </a>
                                <span style="color:#94a3b8">·</span>
                                <a href="{{ route('patients.show', $rx->patient) }}" style="font-weight:600;color:#1e293b;text-decoration:none">{{ $rx->patient->name }}</a>
                            </div>
                            @if($rx->diagnosis) <div style="font-size:12px;color:#64748b;margin-top:2px">{{ $rx->diagnosis }}</div> @endif
                        </div>
                    </div>
                    <div style="display:flex;align-items:center;gap:10px">
                        <span class="badge badge-purple">{{ $rx->items->count() }} دواء</span>
                        <a href="{{ route('prescriptions.show', $rx) }}" class="btn btn-outline btn-sm">عرض</a>
                    </div>
                </div>
            @endforeach
        </div>
        <div style="padding:16px 20px;border-top:1px solid #f1f5f9">{{ $prescriptions->links() }}</div>
    @endif
</div>

@endsection
