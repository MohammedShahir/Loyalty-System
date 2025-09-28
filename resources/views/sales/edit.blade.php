@extends('layouts.app')

@section('title', 'تعديل الفاتورة')

@section('content')
    <div class="max-w-3xl mx-auto" dir="rtl">
        <h1 class="text-2xl font-semibold mb-4">تعديل الفاتورة</h1>

        <form action="{{ route('sales.update', $sale->id) }}" method="POST" class="bg-white p-6 rounded-lg border">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="block text-sm mb-1">رقم الفاتورة</label>
                <input name="Invoice_Num" type="number" value="{{ old('Invoice_Num', $sale->Invoice_Num) }}"
                    class="w-full rounded-md border px-3 py-2" />
            </div>

            <div class="mb-3">
                <label class="block text-sm mb-1">المبلغ</label>
                <input name="Total_Sales" type="number" step="0.01" value="{{ old('Total_Sales', $sale->Total_Sales) }}"
                    class="w-full rounded-md border px-3 py-2" required />
            </div>

            <div class="mb-3">
                <label class="block text-sm mb-1">الكوافير</label>
                <select name="Hairdresser_Id" class="w-full rounded-md border px-3 py-2">
                    @foreach ($hairdressers as $h)
                        <option value="{{ $h->id }}" @selected(old('Hairdresser_Id', $sale->Hairdresser_Id) == $h->id)>{{ $h->Hairdresser_Name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('reports.index') }}" class="px-4 py-2 rounded-md border">إلغاء</a>
                <button class="px-4 py-2 rounded-md bg-slate-900 text-white">حفظ</button>
            </div>
        </form>
    </div>
@endsection
