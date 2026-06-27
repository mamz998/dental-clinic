@extends('layouts.app')

@section('title', 'الفواتير والمدفوعات')

@section('content')

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:16px;margin-bottom:20px">
    <div class="stat-card red">
        <div class="stat-icon">⏳</div>
        <div class="stat-label">إجمالي المبالغ المعلقة</div>
        <div class="stat-value" style="color:#ef4444">{{ number_format($totalPending, 0) }} ج</div>
    </div>
</div>

<div class="search-bar">
    <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;width:100%">
        <input type="text" name="search" value="{{ request('search') }}"
            placeholder="🔍 بحث برقم الفاتورة أو اسم المريض..."
            class="form-control" style="flex:1;min-width:220px">
        <select name="status" class="form-control" style="width:160px">
            <option value="">كل الفواتير</option>
            <option value="unpaid" {{ request('status')==='unpaid' ? 'selected' : '' }}>غير مدفوعة</option>
            <option value="paid" {{ request('status')==='paid' ? 'selected' : '' }}>مدفوعة</option>
        </select>
        <button type="submit" class="btn btn-primary">بحث</button>
    </form>
</div>

<div class="card">
    <div class="card-header">
        <span class="card-title">الفواتير</span>
        <a href="{{ route('invoices.create') }}" class="btn btn-sm" style="background:#f59e0b;color:white">➕ فاتورة جديدة</a>
    </div>
    @if($invoices->isEmpty())
        <div style="padding:60px;text-align:center;color:#94a3b8">
            <div style="font-size:60px;margin-bottom:12px">💰</div>
            <div>لا يوجد فواتير</div>
        </div>
    @else
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>رقم الفاتورة</th>
                        <th>المريض</th>
                        <th>التاريخ</th>
                        <th>الإجمالي</th>
                        <th>المدفوع</th>
                        <th>المتبقي</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $inv)
                        <tr>
                            <td>
                                <a href="{{ route('invoices.show', $inv) }}" style="font-weight:700;color:#38bdf8;text-decoration:none;font-family:monospace">
                                    {{ $inv->invoice_number }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('patients.show', $inv->patient) }}" style="font-weight:600;color:#1e293b;text-decoration:none">{{ $inv->patient->name }}</a>
                            </td>
                            <td style="color:#64748b;font-size:13px">{{ $inv->invoice_date->format('d/m/Y') }}</td>
                            <td style="font-weight:700">{{ number_format($inv->total_amount, 0) }} ج</td>
                            <td style="color:#22c55e;font-weight:600">{{ number_format($inv->paid_amount, 0) }} ج</td>
                            <td style="color:{{ $inv->remaining>0 ? '#ef4444' : '#94a3b8' }};font-weight:{{ $inv->remaining>0 ? '700' : '400' }}">
                                {{ $inv->remaining>0 ? number_format($inv->remaining,0).' ج' : '—' }}
                            </td>
                            <td>
                                @if($inv->is_paid)
                                    <span class="badge badge-green">✅ مدفوع</span>
                                @else
                                    <span class="badge badge-red">⏳ معلق</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('invoices.show', $inv) }}" class="btn btn-outline btn-sm">عرض</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div style="padding:16px 20px;border-top:1px solid #f1f5f9">{{ $invoices->links() }}</div>
    @endif
</div>

@endsection
