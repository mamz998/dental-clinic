@extends('layouts.app')
@section('title','إدارة المستخدمين')
@section('content')

<div class="card">
    <div class="card-header">
        <span class="card-title">👥 المستخدمون والأطباء</span>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">+ مستخدم جديد</a>
    </div>

    @if(session('success'))
        <div style="margin:16px 20px;padding:12px 16px;background:#ecfdf5;border:1px solid #a7f3d0;border-radius:10px;color:#065f46;font-size:13px">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="margin:16px 20px;padding:12px 16px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;color:#991b1b;font-size:13px">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد</th>
                    <th>التليفون</th>
                    <th>الدور</th>
                    <th>التخصص / النسبة</th>
                    <th>الحالة</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="mini-avatar" style="background:{{ $user->isAdmin() ? '#7c3aed' : ($user->isDoctor() ? '#0891b2' : '#0d9488') }}">
                                {{ mb_substr($user->name,0,1) }}
                            </div>
                            <div>
                                <div style="font-weight:700;font-size:13.5px">{{ $user->name }}</div>
                                @if($user->id === auth()->id())
                                    <div style="font-size:11px;color:#3b82f6">(أنت)</div>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td style="font-size:13px;color:#6b7280">{{ $user->email }}</td>
                    <td style="font-size:13px;color:#6b7280">{{ $user->phone ?? '—' }}</td>
                    <td>
                        <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;
                            background:{{ $user->isAdmin() ? '#f3e8ff' : ($user->isDoctor() ? '#e0f2fe' : '#f0fdf4') }};
                            color:{{ $user->isAdmin() ? '#7c3aed' : ($user->isDoctor() ? '#0369a1' : '#15803d') }}">
                            {{ $user->role_name }}
                        </span>
                    </td>
                    <td style="font-size:13px;color:#374151">
                        @if($user->isDoctor() && $user->doctor)
                            {{ $user->doctor->specialty ?? '—' }}
                            @if($user->doctor->commission_rate > 0)
                                <span style="color:#3b82f6;font-weight:700"> · {{ $user->doctor->commission_rate }}%</span>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($user->is_active)
                            <span style="color:#15803d;font-size:12px;font-weight:700">● نشط</span>
                        @else
                            <span style="color:#dc2626;font-size:12px;font-weight:700">● موقوف</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;gap:8px">
                            <a href="{{ route('users.edit',$user) }}" class="btn btn-sm" style="background:#f3f4f6;color:#374151;padding:6px 14px">تعديل</a>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy',$user) }}" onsubmit="return confirm('حذف {{ $user->name }}؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm" style="background:#fef2f2;color:#dc2626;border:none;padding:6px 14px;cursor:pointer">حذف</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection
