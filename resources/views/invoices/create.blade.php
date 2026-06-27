@extends('layouts.app')

@section('title', 'فاتورة جديدة')

@section('content')

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('invoices.store') }}" id="invoice-form" class="space-y-5">
        @csrf

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-gray-800">💰 بيانات الفاتورة</h2>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الفاتورة <span class="text-red-500">*</span></label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', now()->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="2" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Items --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">بنود الفاتورة</h2>
                <button type="button" onclick="addItem()" class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-blue-700">
                    ➕ إضافة بند
                </button>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-12 gap-2 text-xs font-medium text-gray-500 mb-2 px-1">
                    <div class="col-span-5">الوصف</div>
                    <div class="col-span-2">الكمية</div>
                    <div class="col-span-3">السعر</div>
                    <div class="col-span-1">الإجمالي</div>
                    <div class="col-span-1"></div>
                </div>
                <div id="items-container" class="space-y-2">
                    <div class="item-row grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-5">
                            <input type="text" name="items[0][description]" placeholder="وصف الخدمة..." required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[0][quantity]" value="1" min="1" required
                                oninput="calcRow(this)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div class="col-span-3">
                            <input type="number" name="items[0][unit_price]" placeholder="0" min="0" step="0.01" required
                                oninput="calcRow(this)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div class="col-span-1 text-sm font-medium text-gray-700 row-total text-center">0</div>
                        <div class="col-span-1 text-center">
                            <button type="button" onclick="removeItem(this)" class="text-red-400 hover:text-red-600">✕</button>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t flex justify-end">
                    <div class="text-left">
                        <p class="text-sm text-gray-500">الإجمالي:</p>
                        <p class="text-2xl font-bold text-gray-800" id="grand-total">0 ج</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Payment --}}
        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50">
                <h2 class="font-bold text-gray-800">💵 دفعة أولى (اختياري)</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ المدفوع</label>
                    <input type="number" name="initial_payment" min="0" step="0.01" placeholder="0"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الدفع</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="cash">نقدي</option>
                        <option value="card">بطاقة</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-yellow-600 text-white px-8 py-2.5 rounded-lg hover:bg-yellow-700 transition font-medium">
                💾 إنشاء الفاتورة
            </button>
            <a href="{{ route('invoices.index') }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition">
                إلغاء
            </a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemCount = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const idx = itemCount++;
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-2 items-center';
    row.innerHTML = `
        <div class="col-span-5">
            <input type="text" name="items[${idx}][description]" placeholder="وصف الخدمة..." required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        </div>
        <div class="col-span-2">
            <input type="number" name="items[${idx}][quantity]" value="1" min="1" required
                oninput="calcRow(this)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        </div>
        <div class="col-span-3">
            <input type="number" name="items[${idx}][unit_price]" placeholder="0" min="0" step="0.01" required
                oninput="calcRow(this)"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
        </div>
        <div class="col-span-1 text-sm font-medium text-gray-700 row-total text-center">0</div>
        <div class="col-span-1 text-center">
            <button type="button" onclick="removeItem(this)" class="text-red-400 hover:text-red-600">✕</button>
        </div>
    `;
    container.appendChild(row);
}

function removeItem(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length === 1) return;
    btn.closest('.item-row').remove();
    calcTotal();
}

function calcRow(input) {
    const row = input.closest('.item-row');
    const qty = parseFloat(row.querySelector('[name*="quantity"]').value) || 0;
    const price = parseFloat(row.querySelector('[name*="unit_price"]').value) || 0;
    row.querySelector('.row-total').textContent = (qty * price).toLocaleString('ar-EG');
    calcTotal();
}

function calcTotal() {
    let total = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('[name*="quantity"]').value) || 0;
        const price = parseFloat(row.querySelector('[name*="unit_price"]').value) || 0;
        total += qty * price;
    });
    document.getElementById('grand-total').textContent = total.toLocaleString('ar-EG') + ' ج';
}
</script>
@endpush

@endsection
