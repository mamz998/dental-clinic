@extends('layouts.app')

@section('title', 'إضافة مريض جديد')

@section('content')

<div style="max-width:860px;margin:0 auto">
    <form method="POST" action="{{ route('patients.store') }}" style="display:flex;flex-direction:column;gap:20px">
        @csrf

        {{-- Personal Info --}}
        <div class="card">
            <div class="card-header" style="background:#f8fafc">
                <span class="card-title">👤 البيانات الشخصية</span>
            </div>
            <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:18px">
                <div style="grid-column:1/-1">
                    <label class="form-label">الاسم الكامل <span class="req">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-control @error('name') is-invalid @enderror">
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">التليفون <span class="req">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required class="form-control">
                    @error('phone') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">تليفون بديل</label>
                    <input type="text" name="phone_alt" value="{{ old('phone_alt') }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">الجنس <span class="req">*</span></label>
                    <select name="gender" required class="form-control">
                        <option value="">اختر...</option>
                        <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>♂ ذكر</option>
                        <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>♀ أنثى</option>
                    </select>
                </div>
                <div>
                    <label class="form-label">تاريخ الميلاد</label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="form-control">
                </div>
                <div>
                    <label class="form-label">الرقم القومي</label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}" class="form-control">
                    @error('national_id') <div class="form-error">{{ $message }}</div> @enderror
                </div>
                <div>
                    <label class="form-label">جهة الاتصال في الطوارئ</label>
                    <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="form-control" placeholder="الاسم">
                </div>
                <div>
                    <label class="form-label">تليفون الطوارئ</label>
                    <input type="text" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" class="form-control">
                </div>
                <div style="grid-column:1/-1">
                    <label class="form-label">العنوان</label>
                    <input type="text" name="address" value="{{ old('address') }}" class="form-control">
                </div>
                <div style="grid-column:1/-1">
                    <label class="form-label">ملاحظات</label>
                    <textarea name="notes" class="form-control">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Medical History --}}
        <div class="card" style="border:2px solid #fecaca">
            <div class="card-header" style="background:#fef2f2">
                <span class="card-title" style="color:#dc2626">⚕️ التاريخ الطبي (مهم للسلامة)</span>
            </div>
            <div style="padding:24px;display:flex;flex-direction:column;gap:20px">

                <div>
                    <p style="font-weight:700;font-size:14px;color:#374151;margin:0 0 12px">الحساسية</p>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:10px">
                        <label class="check-label">
                            <input type="checkbox" name="allergy_anesthesia" value="1" {{ old('allergy_anesthesia') ? 'checked' : '' }}>
                            حساسية من التخدير
                        </label>
                        <label class="check-label">
                            <input type="checkbox" name="allergy_penicillin" value="1" {{ old('allergy_penicillin') ? 'checked' : '' }}>
                            حساسية من البنسيلين
                        </label>
                    </div>
                    <input type="text" name="allergies_other" value="{{ old('allergies_other') }}" placeholder="حساسية أخرى..." class="form-control">
                </div>

                <div>
                    <p style="font-weight:700;font-size:14px;color:#374151;margin:0 0 12px">أمراض مزمنة</p>
                    <div style="display:flex;flex-wrap:wrap;gap:16px;margin-bottom:10px">
                        <label class="check-label"><input type="checkbox" name="has_diabetes" value="1" {{ old('has_diabetes') ? 'checked' : '' }}> سكر</label>
                        <label class="check-label"><input type="checkbox" name="has_heart_disease" value="1" {{ old('has_heart_disease') ? 'checked' : '' }}> قلب</label>
                        <label class="check-label"><input type="checkbox" name="has_bleeding_disorder" value="1" {{ old('has_bleeding_disorder') ? 'checked' : '' }}> اضطراب نزيف</label>
                        <label class="check-label"><input type="checkbox" name="is_pregnant" value="1" {{ old('is_pregnant') ? 'checked' : '' }}> 🤰 حمل</label>
                    </div>
                    <input type="text" name="chronic_conditions_other" value="{{ old('chronic_conditions_other') }}" placeholder="أمراض أخرى..." class="form-control">
                </div>

                <div>
                    <label class="form-label">الأدوية الحالية</label>
                    <textarea name="current_medications" class="form-control" placeholder="اكتب أسماء الأدوية...">{{ old('current_medications') }}</textarea>
                </div>

                <div>
                    <label class="form-label">ملاحظات طبية إضافية</label>
                    <textarea name="medical_notes" class="form-control">{{ old('medical_notes') }}</textarea>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:12px">
            <button type="submit" class="btn btn-primary" style="font-size:15px;padding:12px 28px">💾 حفظ المريض</button>
            <a href="{{ route('patients.index') }}" class="btn btn-outline" style="font-size:15px;padding:12px 20px">إلغاء</a>
        </div>
    </form>
</div>

@endsection
