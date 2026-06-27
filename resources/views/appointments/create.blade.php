@extends('layouts.app')

@section('title', 'موعد جديد')

@section('content')

<div style="max-width:640px;margin:0 auto">
    <form method="POST" action="{{ route('appointments.store') }}" class="card">
        @csrf
        <div class="card-header" style="background:#f0fdf4">
            <span class="card-title" style="color:#15803d">📅 بيانات الموعد</span>
        </div>
        <div style="padding:24px;display:flex;flex-direction:column;gap:18px">
            <div>
                <label class="form-label">المريض <span class="req">*</span></label>
                <select name="patient_id" required class="form-control">
                    <option value="">اختر المريض...</option>
                    @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ (old('patient_id', $selectedPatient) == $p->id) ? 'selected' : '' }}>
                            {{ $p->name }} — {{ $p->phone }}
                        </option>
                    @endforeach
                </select>
                @error('patient_id') <div class="form-error">{{ $message }}</div> @enderror
            </div>
            <div>
                <label class="form-label">وصف الموعد</label>
                <input type="text" name="title" value="{{ old('title') }}" class="form-control" placeholder="مثال: حشو، كشف، تنظيف...">
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                <div>
                    <label class="form-label">البداية <span class="req">*</span></label>
                    <input type="datetime-local" name="starts_at" value="{{ old('starts_at', $selectedDate.'T09:00') }}" required class="form-control">
                    @error('starts_at') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">النهاية <span class="req">*</span></label>
                    <input type="datetime-local" name="ends_at" value="{{ old('ends_at', $selectedDate.'T09:30') }}" required class="form-control">
                    @error('ends_at') <div class="form-error">{{ $message }}</div> @enderror
                </div>
            </div>
            <div>
                <label class="form-label">ملاحظات</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <div style="display:flex;gap:10px;padding-top:4px">
                <button type="submit" class="btn btn-success" style="font-size:15px;padding:12px 28px">💾 حفظ الموعد</button>
                <a href="{{ route('appointments.index') }}" class="btn btn-outline" style="font-size:15px;padding:12px 20px">إلغاء</a>
            </div>
        </div>
    </form>
</div>

@endsection
