@extends('layouts.app')

@section('title', 'فاتورة ' . $invoice->invoice_number)

@section('content')

<div class="max-w-3xl mx-auto space-y-5">

    {{-- Invoice Header --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <p class="text-gray-500 text-sm">رقم الفاتورة</p>
                <p class="text-2xl font-bold text-gray-800">{{ $invoice->invoice_number }}</p>
                <p class="text-sm text-gray-500">{{ $invoice->invoice_date->format('d/m/Y') }}</p>
            </div>
            <div class="text-left">
                @if($invoice->is_paid)
                    <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full font-bold">✅ مدفوعة بالكامل</span>
                @else
                    <span class="bg-red-100 text-red-700 px-4 py-2 rounded-full font-bold">⏳ غير مكتملة الدفع</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 py-4 border-y">
            <div>
                <p class="text-xs text-gray-500 mb-1">المريض</p>
                <a href="{{ route('patients.show', $invoice->patient) }}" class="font-medium text-blue-600 hover:underline">
                    {{ $invoice->patient->name }}
                </a>
                <p class="text-sm text-gray-500">{{ $invoice->patient->phone }}</p>
            </div>
            <div class="text-left">
                <p class="text-xs text-gray-500 mb-1">الدكتور</p>
                <p class="font-medium text-gray-800">{{ $invoice->doctor->name }}</p>
            </div>
        </div>

        {{-- Items --}}
        <table class="w-full text-sm mt-4">
            <thead>
                <tr class="text-xs text-gray-500 border-b">
                    <th class="py-2 text-right">الخدمة</th>
                    <th class="py-2 text-center">الكمية</th>
                    <th class="py-2 text-left">السعر</th>
                    <th class="py-2 text-left">الإجمالي</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @foreach($invoice->items as $item)
                    <tr>
                        <td class="py-3 text-gray-800">{{ $item->description }}</td>
                        <td class="py-3 text-center text-gray-600">{{ $item->quantity }}</td>
                        <td class="py-3 text-left text-gray-600">{{ number_format($item->unit_price, 0) }} ج</td>
                        <td class="py-3 text-left font-medium text-gray-800">{{ number_format($item->total, 0) }} ج</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="border-t-2">
                <tr>
                    <td colspan="3" class="py-3 text-left font-bold text-gray-700">الإجمالي</td>
                    <td class="py-3 text-left font-bold text-gray-800 text-lg">{{ number_format($invoice->total_amount, 0) }} ج</td>
                </tr>
                <tr>
                    <td colspan="3" class="py-1 text-left text-green-600">المدفوع</td>
                    <td class="py-1 text-left text-green-600 font-medium">{{ number_format($invoice->paid_amount, 0) }} ج</td>
                </tr>
                @if($invoice->remaining > 0)
                    <tr>
                        <td colspan="3" class="py-1 text-left text-red-600 font-bold">المتبقي</td>
                        <td class="py-1 text-left text-red-600 font-bold text-lg">{{ number_format($invoice->remaining, 0) }} ج</td>
                    </tr>
                @endif
            </tfoot>
        </table>

        {{-- Notes --}}
        @if($invoice->notes)
            <div class="mt-4 p-3 bg-gray-50 rounded-lg text-sm text-gray-600">{{ $invoice->notes }}</div>
        @endif

        <div class="mt-5 flex gap-2 print:hidden">
            <a href="{{ route('invoices.edit', $invoice) }}" class="bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200">
                ✏️ تعديل
            </a>
            <button onclick="window.print()" class="bg-blue-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-blue-700">
                🖨️ طباعة
            </button>
        </div>
    </div>

    {{-- Payments History --}}
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
            <h3 class="font-bold text-gray-800">سجل المدفوعات</h3>
        </div>
        <div class="divide-y">
            @forelse($invoice->payments as $pay)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="font-medium text-sm text-gray-800">{{ number_format($pay->amount, 0) }} ج</p>
                        <p class="text-xs text-gray-500">{{ $pay->payment_date->format('d/m/Y') }} · {{ $pay->payment_method === 'cash' ? 'نقدي' : ($pay->payment_method === 'card' ? 'بطاقة' : 'أخرى') }}</p>
                    </div>
                    @if($pay->notes) <p class="text-xs text-gray-400">{{ $pay->notes }}</p> @endif
                </div>
            @empty
                <p class="px-5 py-6 text-gray-400 text-sm text-center">لا يوجد مدفوعات بعد</p>
            @endforelse
        </div>
    </div>

    {{-- Add Payment --}}
    @if(!$invoice->is_paid)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b bg-green-50">
            <h3 class="font-bold text-gray-800">💵 تسجيل دفعة جديدة</h3>
        </div>
        <form method="POST" action="{{ route('invoices.payment', $invoice) }}" class="p-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">المبلغ (أقصى {{ number_format($invoice->remaining, 0) }} ج)</label>
                    <input type="number" name="amount" max="{{ $invoice->remaining }}" min="0.01" step="0.01" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                    @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">طريقة الدفع</label>
                    <select name="payment_method" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <option value="cash">نقدي</option>
                        <option value="card">بطاقة</option>
                        <option value="other">أخرى</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الدفع</label>
                    <input type="date" name="payment_date" value="{{ now()->format('Y-m-d') }}" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400">
                </div>
            </div>
            <div class="mt-3">
                <input type="text" name="notes" placeholder="ملاحظات..." class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>
            <button type="submit" class="mt-3 bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition text-sm font-medium">
                ✅ تسجيل الدفعة
            </button>
        </form>
    </div>
    @endif

</div>

@endsection
