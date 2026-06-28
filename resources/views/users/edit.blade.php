@extends('layouts.app')
@section('title','تعديل مستخدم')
@section('content')

<div class="card" style="max-width:620px;margin:0 auto">
    <div class="card-header">
        <span class="card-title">✏️ تعديل: {{ $user->name }}</span>
        <a href="{{ route('users.index') }}" class="btn btn-sm" style="background:#f3f4f6;color:#374151">رجوع</a>
    </div>
    <div style="padding:28px 32px">
        <form method="POST" action="{{ route('users.update',$user) }}">
            @csrf @method('PUT')

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label class="form-label">الاسم *</label>
                    <input class="form-input" name="name" value="{{ old('name',$user->name) }}" required>
                </div>
                <div>
                    <label class="form-label">التليفون</label>
                    <input class="form-input" name="phone" value="{{ old('phone',$user->phone) }}">
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label class="form-label">البريد الإلكتروني *</label>
                <input class="form-input" type="email" name="email" value="{{ old('email',$user->email) }}" required>
                @error('email')<p class="form-error">{{ $message }}</p>@enderror
            </div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">
                <div>
                    <label class="form-label">كلمة المرور الجديدة</label>
                    <input class="form-input" type="password" name="password" placeholder="اتركها فارغة للإبقاء على القديمة">
                    @error('password')<p class="form-error">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label">تأكيد كلمة المرور</label>
                    <input class="form-input" type="password" name="password_confirmation">
                </div>
            </div>

            <div style="margin-bottom:16px">
                <label class="form-label">الدور *</label>
                <select class="form-input" name="role" id="role-select" onchange="toggleDoctorFields()" required>
                    <option value="admin" {{ old('role',$user->role)==='admin'?'selected':'' }}>مدير</option>
                    <option value="doctor" {{ old('role',$user->role)==='doctor'?'selected':'' }}>دكتور</option>
                    <option value="receptionist" {{ old('role',$user->role)==='receptionist'?'selected':'' }}>استقبال</option>
                </select>
            </div>

            <div id="doctor-fields" style="display:none;background:#f0f9ff;border:1px solid #bae6fd;border-radius:12px;padding:16px;margin-bottom:16px">
                <div style="font-size:13px;font-weight:700;color:#0369a1;margin-bottom:12px">⚕️ بيانات الدكتور</div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                    <div>
                        <label class="form-label">التخصص</label>
                        <input class="form-input" name="specialty" value="{{ old('specialty',$user->doctor?->specialty) }}" placeholder="مثال: تقويم، تركيبات...">
                    </div>
                    <div>
                        <label class="form-label">نسبة العمولة %</label>
                        <input class="form-input" type="number" name="commission_rate" value="{{ old('commission_rate',$user->doctor?->commission_rate ?? 0) }}" min="0" max="100" step="0.5">
                    </div>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:10px;margin-bottom:24px">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ $user->is_active?'checked':'' }} style="width:16px;height:16px;accent-color:#3b82f6">
                <label for="is_active" class="form-label" style="margin:0">حساب نشط</label>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;padding:13px">حفظ التعديلات</button>
        </form>
    </div>
</div>

<script>
function toggleDoctorFields() {
    var role = document.getElementById('role-select').value;
    document.getElementById('doctor-fields').style.display = role === 'doctor' ? 'block' : 'none';
}
toggleDoctorFields();
</script>

@endsection
