@extends('layouts.app')

@section('title', 'تعديل موعد')

@section('content')

<div style="max-width:640px;margin:0 auto">
    <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="card">
        @csrf @method('PUT')
        <div class="card-header" style="background:#f8fafc">
            <span class="card-title">✏️ تعديل الموعد</span>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:18px">
            <div>
                <label class="form-label">المريض</label>
                <select name="patient_id" required class="form-control">
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ old('patient_id', $appointment->patient_id) == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} — {{ $p->phone }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">وصف الموعد</label>
                <input type="text" name="title" value="{{ old('title', $appointment->title) }}" class="form-control">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <label class="form-label">البداية</label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $appointment->starts_at->format('Y-m-d\TH:i')) }}" required class="form-control">
                </div>
                <div>
                    <label class="form-label">النهاية</label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $appointment->ends_at->format('Y-m-d\TH:i')) }}" required class="form-control">
                </div>
            </div>
            <div>
                <label class="form-label">الحالة</label>
                <select name="status" class="form-control">
                    @foreach(['scheduled'=>'محدد','confirmed'=>'مؤكد','completed'=>'مكتمل','cancelled'=>'ملغي','no_show'=>'لم يحضر'] as $val=>$lbl)
                        <option value="{{ $val }}" {{ old('status', $appointment->status) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $appointment->notes) }}</textarea>
            </div>
            <div style="display:flex;gap:10px;padding-top:4px">
                <button type="submit" class="btn btn-primary" style="font-size:15px;padding:12px 28px">💾 حفظ</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-outline" style="font-size:15px;padding:12px 20px">إلغاء</a>
            </div>
        </div>
    </form>
</div>

@endsection
