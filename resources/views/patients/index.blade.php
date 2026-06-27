@extends('layouts.app')

@section('title', 'سجل المرضى')

@section('content')

<div class="search-bar">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 بحث بالاسم أو التليفون أو الرقم القومي..."
            class="form-control" style="flex:1;min-width:220px">
        <select name="gender" class="form-control" style="width:140px">
            <option value="">كل الجنس</option>
            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>♂ ذكر</option>
            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>♀ أنثى</option>
        </select>
        <button type="submit" class="btn btn-primary">بحث</button>
        @if(request('search') || request('gender'))
            <a href="{{ route('patients.index') }}" class="btn btn-outline">× مسح</a>
        @endif
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">المرضى ({{ $patients->total() }})</span>
        <a href="{{ route('patients.create') }}" class="btn btn-primary btn-sm">➕ إضافة مريض</a>
    </div>

    @if($patients->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">
            <div style="font-size:60px;margin-bottom:12px">👥</div>
            <div style="font-size:16px;font-weight:600;margin-bottom:6px">لا يوجد مرضى</div>
            <div style="font-size:13px;margin-bottom:20px">ابدأ بإضافة أول مريض في العيادة</div>
            <a href="{{ route('patients.create') }}" class="btn btn-primary">➕ إضافة مريض جديد</a>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المريض</th>
                        <th>التليفون</th>
                        <th>الجنس</th>
                        <th>السن</th>
                        <th>تنبيه طبي</th>
                        <th>تاريخ التسجيل</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($patients as $patient)
                        <tr>
                            <td style="color:#94a3b8;font-size:13px">{{ $patient->id }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:10px">
                                    <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#38bdf8,#818cf8);display:flex;align-items:center;justify-content:center;font-weight:700;color:white;font-size:14px;flex-shrink:0">
                                        {{ mb_substr($patient->name, 0, 1) }}
                                    </div>
                                    <a href="{{ route('patients.show', $patient) }}" style="font-weight:600;color:#1e293b;text-decoration:none;font-size:14px">
                                        {{ $patient->name }}
                                    </a>
                                </div>
                            </td>
                            <td style="color:#64748b;font-size:14px">{{ $patient->phone }}</td>
                            <td>
                                <span class="badge {{ $patient->gender === 'male' ? 'badge-blue' : 'badge-purple' }}">
                                    {{ $patient->gender === 'male' ? '♂ ذكر' : '♀ أنثى' }}
                                </span>
                            </td>
                            <td style="color:#64748b;font-size:14px">{{ $patient->age ?? '—' }}</td>
                            <td>
                                @if($patient->hasRisk())
                                    <span class="badge badge-red">⚠️ تحذير</span>
                                @else
                                    <span style="color:#94a3b8;font-size:13px">—</span>
                                @endif
                            </td>
                            <td style="color:#94a3b8;font-size:13px">{{ $patient->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div style="display:flex;gap:6px">
                                    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline btn-sm">عرض</a>
                                    <a href="{{ route('patients.edit', $patient) }}" class="btn btn-outline btn-sm">تعديل</a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;border-top:1px solid #f1f5f9">
            {{ $patients->links() }}
        </div>
    @endif
</div>

@endsection
