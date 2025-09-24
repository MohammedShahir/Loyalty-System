@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto" dir="rtl">
        <div class="mb-8 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">احتساب النقاط</h1>
            <p class="text-slate-600 mt-1">إضافة عملية بيع وتحديث نقاط الكوافير تلقائياً.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-right">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <form action="{{ route('calculating-points.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-right md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Hairdresser_Id">اختيار
                            الكوافير</label>
                        <select id="Hairdresser_Id" name="Hairdresser_Id" required
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                            <option value="" selected disabled>اختر الكوافير</option>
                            @isset($hairdressers)
                                @foreach ($hairdressers as $h)
                                    <option value="{{ $h->id }}">{{ $h->Hairdresser_Name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Total_Sales">إجمالي
                            المبيعات (لهذه العملية)</label>
                        <input id="Total_Sales" name="Total_Sales" type="number" step="0.01" min="0"
                            placeholder="0.00"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Invoice_Num">رقم الفاتورة</label>
                        <input id="Invoice_Num" name="Invoice_Num" type="number" min="0" placeholder="مثال: 1001"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ url('/') }}"
                        class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">إلغاء</a>
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-400">حفظ</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('extra')
    <div class="max-w-4xl mx-auto mt-10" dir="rtl">
        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 text-right">
            عند كل حفظ، يتم جمع إجمالي المبيعات لجميع فواتير الكوافير المختار ثم قسمة الإجمالي على 10 وتخزين الناتج (عدد
            صحيح) في خانة النقاط.
        </div>
    </div>
@endsection
