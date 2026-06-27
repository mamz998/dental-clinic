@extends('layouts.app')

@section('title', 'تعديل الفاتورة')

@section('content')

<div class="max-w-3xl mx-auto">
    <form method="POST" action="{{ route('invoices.update', $invoice) }}" class="space-y-5">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b bg-gray-50">
                <h2 class="font-bold text-gray-800">تعديل الفاتورة {{ $invoice->invoice_number }}</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">التاريخ</label>
                    <input type="date" name="invoice_date" value="{{ old('invoice_date', $invoice->invoice_date->format('Y-m-d')) }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">ملاحظات</label>
                    <textarea name="notes" rows="1" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">{{ old('notes', $invoice->notes) }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="font-bold text-gray-800">البنود</h2>
                <button type="button" onclick="addItem()" class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-blue-700">➕ إضافة</button>
            </div>
            <div class="p-6">
                <div id="items-container" class="space-y-2">
                    @foreach($invoice->items as $i => $item)
                    <div class="item-row grid grid-cols-12 gap-2 items-center">
                        <div class="col-span-5">
                            <input type="text" name="items[{{ $i }}][description]" value="{{ $item->description }}" required
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div class="col-span-2">
                            <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" min="1" required
                                oninput="calcRow(this)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div class="col-span-3">
                            <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" min="0" step="0.01" required
                                oninput="calcRow(this)"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div class="col-span-1 text-sm font-medium text-gray-700 row-total text-center">{{ number_format($item->total, 0) }}</div>
                        <div class="col-span-1 text-center">
                            <button type="button" onclick="removeItem(this)" class="text-red-400 hover:text-red-600">✕</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t flex justify-end">
                    <div class="text-left">
                        <p class="text-sm text-gray-500">الإجمالي:</p>
                        <p class="text-2xl font-bold text-gray-800" id="grand-total">{{ number_format($invoice->total_amount, 0) }} ج</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-blue-600 text-white px-8 py-2.5 rounded-lg hover:bg-blue-700 transition font-medium">💾 حفظ</button>
            <a href="{{ route('invoices.show', $invoice) }}" class="bg-gray-100 text-gray-700 px-6 py-2.5 rounded-lg hover:bg-gray-200 transition">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
let itemCount = {{ $invoice->items->count() }};
function addItem() {
    const idx = itemCount++;
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-2 items-center';
    row.innerHTML = `
        <div class="col-span-5"><input type="text" name="items[${idx}][description]" placeholder="وصف..." required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"></div>
        <div class="col-span-2"><input type="number" name="items[${idx}][quantity]" value="1" min="1" required oninput="calcRow(this)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"></div>
        <div class="col-span-3"><input type="number" name="items[${idx}][unit_price]" placeholder="0" min="0" step="0.01" required oninput="calcRow(this)" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-blue-400"></div>
        <div class="col-span-1 text-sm font-medium text-gray-700 row-total text-center">0</div>
        <div class="col-span-1 text-center"><button type="button" onclick="removeItem(this)" class="text-red-400 hover:text-red-600">✕</button></div>
    `;
    document.getElementById('items-container').appendChild(row);
}
function removeItem(btn) {
    if (document.querySelectorAll('.item-row').length === 1) return;
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
