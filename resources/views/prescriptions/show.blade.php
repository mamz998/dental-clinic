@extends('layouts.app')

@section('title', 'روشتة')

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm overflow-hidden print:shadow-none">

        {{-- Clinic Header --}}
        <div class="px-8 py-6 border-b text-center bg-purple-50 print:bg-white">
            <p class="text-2xl font-bold text-purple-800">🦷 عيادة الأسنان</p>
            <p class="text-sm text-gray-500 mt-1">نظام إدارة المرضى</p>
        </div>

        <div class="px-8 py-6">

            <div class="flex justify-between items-start mb-6">
                <div>
                    <p class="text-sm text-gray-500">المريض</p>
                    <p class="font-bold text-lg text-gray-800">{{ $prescription->patient->name }}</p>
                    <p class="text-sm text-gray-500">{{ $prescription->patient->phone }}</p>
                </div>
                <div class="text-left">
                    <p class="text-sm text-gray-500">التاريخ</p>
                    <p class="font-medium text-gray-800">{{ $prescription->prescription_date->format('d/m/Y') }}</p>
                    <p class="text-sm text-gray-500">د. {{ $prescription->doctor->name }}</p>
                </div>
            </div>

            @if($prescription->diagnosis)
                <div class="mb-5 p-3 bg-gray-50 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-1">التشخيص</p>
                    <p class="text-gray-800">{{ $prescription->diagnosis }}</p>
                </div>
            @endif

            <h3 class="font-bold text-gray-700 mb-3 text-sm uppercase tracking-wide">الأدوية</h3>
            <div class="space-y-3">
                @foreach($prescription->items as $i => $item)
                    <div class="border border-gray-200 rounded-xl p-4">
                        <div class="flex items-start gap-3">
                            <span class="w-7 h-7 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-sm font-bold flex-shrink-0">
                                {{ $i + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="font-bold text-gray-800">{{ $item->medication_name }}</p>
                                <div class="mt-1 flex flex-wrap gap-3 text-sm text-gray-600">
                                    @if($item->dosage)
                                        <span>💊 {{ $item->dosage }}</span>
                                    @endif
                                    @if($item->frequency)
                                        <span>🔄 {{ $item->frequency }}</span>
                                    @endif
                                    @if($item->duration)
                                        <span>⏱️ {{ $item->duration }}</span>
                                    @endif
                                </div>
                                @if($item->instructions)
                                    <p class="text-sm text-blue-700 mt-1">📝 {{ $item->instructions }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($prescription->notes)
                <div class="mt-5 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-xs font-medium text-gray-500 mb-1">ملاحظات</p>
                    <p class="text-sm text-gray-700">{{ $prescription->notes }}</p>
                </div>
            @endif

            <div class="mt-8 border-t pt-4 flex justify-between items-center print:hidden">
                <div class="flex gap-2">
                    <a href="{{ route('prescriptions.edit', $prescription) }}" class="bg-gray-100 text-gray-700 text-sm px-4 py-2 rounded-lg hover:bg-gray-200">✏️ تعديل</a>
                    <button onclick="window.print()" class="bg-purple-600 text-white text-sm px-4 py-2 rounded-lg hover:bg-purple-700">🖨️ طباعة</button>
                </div>
                <form method="POST" action="{{ route('prescriptions.destroy', $prescription) }}" onsubmit="return confirm('حذف الروشتة؟')">
                    @csrf @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm">حذف</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection
