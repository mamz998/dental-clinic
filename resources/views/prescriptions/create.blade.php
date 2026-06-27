@extends('layouts.app')

@section('title', 'روشتة جديدة')

@section('content')

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('prescriptions.store') }}" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-gray-800">💊 بيانات الروشتة</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المريض <span class="text-red-500">*</span></label>
                    <select name="patient_id" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="">اختر المريض...</option>
                        @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ (old('patient_id') == $p->id || ($selectedPatient && $selectedPatient->id == $p->id)) ? 'selected' : '' }}>
                                {{ $p->name }} — {{ $p->phone }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ <span class="text-red-500">*</span></label>
                    <input type="date" name="prescription_date" value="{{ old('prescription_date', now()->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">التشخيص</label>
                    <input type="text" name="diagnosis" value="{{ old('diagnosis') }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">الأدوية</h2>
                <button type="button" onclick="addMed()" class="bg-purple-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-purple-700">
                    ➕ إضافة دواء
                </button>
            </div>
            <div class="p-6 space-y-3" id="meds-container">
                <div class="med-row bg-gray-50 rounded-xl p-4 border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">اسم الدواء <span class="text-red-500">*</span></label>
                            <input type="text" name="items[0][medication_name]" required placeholder="اسم الدواء..."
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">الجرعة</label>
                            <input type="text" name="items[0][dosage]" placeholder="مثال: 500mg"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">التكرار</label>
                            <input type="text" name="items[0][frequency]" placeholder="مثال: 3 مرات يومياً"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-600 mb-1">المدة</label>
                            <input type="text" name="items[0][duration]" placeholder="مثال: 5 أيام"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-medium text-gray-600 mb-1">تعليمات</label>
                            <input type="text" name="items[0][instructions]" placeholder="مثال: بعد الأكل"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-purple-600 text-white px-8 py-2.5 rounded-lg hover:bg-purple-700 transition font-medium">
                💾 حفظ الروشتة
            </button>
            <a href="{{ route('prescriptions.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let medCount = 1;
function addMed() {
    const idx = medCount++;
    const div = document.createElement('div');
    div.className = 'med-row bg-gray-50 rounded-xl p-4 border border-gray-200 relative';
    div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-3 left-3 text-red-400 hover:text-red-600 text-sm">✕</button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">اسم الدواء *</label>
                <input type="text" name="items[${idx}][medication_name]" required placeholder="اسم الدواء..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">الجرعة</label>
                <input type="text" name="items[${idx}][dosage]" placeholder="مثال: 500mg"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">التكرار</label>
                <input type="text" name="items[${idx}][frequency]" placeholder="مثال: 3 مرات يومياً"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">المدة</label>
                <input type="text" name="items[${idx}][duration]" placeholder="مثال: 5 أيام"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
            </div>
            <div class="md:col-span-2">
                <label class="block text-xs font-medium text-gray-600 mb-1">تعليمات</label>
                <input type="text" name="items[${idx}][instructions]" placeholder="مثال: بعد الأكل"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
            </div>
        </div>
    `;
    document.getElementById('meds-container').appendChild(div);
}
</script>
@endpush

@endsection
