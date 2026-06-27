@extends('layouts.app')

@section('title', 'تعديل الروشتة')

@section('content')

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('prescriptions.update', $prescription) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-gray-800">تعديل الروشتة</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                    <input type="date" name="prescription_date" value="{{ old('prescription_date', $prescription->prescription_date->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">التشخيص</label>
                    <input type="text" name="diagnosis" value="{{ old('diagnosis', $prescription->diagnosis) }}"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes', $prescription->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">الأدوية</h2>
                <button type="button" onclick="addMed()" class="bg-purple-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-purple-700">➕ إضافة</button>
            </div>
            <div class="p-6 space-y-3" id="meds-container">
                @foreach($prescription->items as $i => $item)
                    <div class="med-row bg-gray-50 rounded-xl p-4 border border-gray-200 relative">
                        <button type="button" onclick="this.parentElement.remove()" class="absolute top-3 left-3 text-red-400 hover:text-red-600 text-sm">✕</button>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">اسم الدواء *</label>
                                <input type="text" name="items[{{ $i }}][medication_name]" value="{{ $item->medication_name }}" required
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">الجرعة</label>
                                <input type="text" name="items[{{ $i }}][dosage]" value="{{ $item->dosage }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">التكرار</label>
                                <input type="text" name="items[{{ $i }}][frequency]" value="{{ $item->frequency }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-600 mb-1">المدة</label>
                                <input type="text" name="items[{{ $i }}][duration]" value="{{ $item->duration }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-xs font-medium text-gray-600 mb-1">تعليمات</label>
                                <input type="text" name="items[{{ $i }}][instructions]" value="{{ $item->instructions }}"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-purple-600 text-white px-8 py-2.5 rounded-lg hover:bg-purple-700 transition font-medium">💾 حفظ</button>
            <a href="{{ route('prescriptions.show', $prescription) }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let medCount = {{ $prescription->items->count() }};
function addMed() {
    const idx = medCount++;
    const div = document.createElement('div');
    div.className = 'med-row bg-gray-50 rounded-xl p-4 border border-gray-200 relative';
    div.innerHTML = `
        <button type="button" onclick="this.parentElement.remove()" class="absolute top-3 left-3 text-red-400 hover:text-red-600 text-sm">✕</button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div><label class="block text-xs font-medium text-gray-600 mb-1">اسم الدواء *</label><input type="text" name="items[${idx}][medication_name]" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">الجرعة</label><input type="text" name="items[${idx}][dosage]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">التكرار</label><input type="text" name="items[${idx}][frequency]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400"></div>
            <div><label class="block text-xs font-medium text-gray-600 mb-1">المدة</label><input type="text" name="items[${idx}][duration]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400"></div>
            <div class="md:col-span-2"><label class="block text-xs font-medium text-gray-600 mb-1">تعليمات</label><input type="text" name="items[${idx}][instructions]" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-purple-400"></div>
        </div>
    `;
    document.getElementById('meds-container').appendChild(div);
}
</script>
@endpush

@endsection
