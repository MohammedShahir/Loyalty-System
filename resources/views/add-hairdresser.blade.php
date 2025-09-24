@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto" dir="rtl">
        <div class="mb-8 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">إضافة كوافير</h1>
            <p class="text-slate-600 mt-1">إنشاء ملف كوافير جديد لنظام الولاء.</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-right">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <form action="{{ route('hairdresser.store') }}" method="POST" class="p-6 sm:p-8 space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Hairdresser_Name">الاسم</label>
                        <input id="Hairdresser_Name" name="Hairdresser_Name" type="text" required
                            placeholder="مثال: صالون أناقة"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Hairdresser_Owner">صاحب الصالون
                            (أسم)</label>
                        <input id="Hairdresser_Owner" name="Hairdresser_Owner" type="text" inputmode="numeric"
                            min="0" placeholder="أسم المالك"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Call_Num">الهاتف</label>
                        <input id="Call_Num" name="Call_Num" type="tel" inputmode="numeric" minlength="9"
                            maxlength="9" placeholder="05XXXXXXXX"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Whats_Num">واتساب</label>
                        <input id="Whats_Num" name="Whats_Num" type="tel" inputmode="numeric" minlength="9"
                            maxlength="9" placeholder="05XXXXXXXX"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>

                    <div class="md:col-span-2 text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Address">العنوان</label>
                        <input id="Address" name="Address" type="text" placeholder="الشارع، المدينة"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Type_of_Activity">نوع
                            النشاط</label>
                        <select id="Type_of_Activity" name="Type_of_Activity"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                            <option value="" selected disabled>اختر النشاط</option>
                            @isset($activities)
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}">{{ $activity->Activity_Name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Type_of_Card">نوع البطاقة</label>
                        <select id="Type_of_Card" name="Type_of_Card"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                            <option value="" selected disabled>اختر البطاقة</option>
                            @isset($cards)
                                @foreach ($cards as $card)
                                    <option value="{{ $card->id }}">{{ $card->Card_Name }}</option>
                                @endforeach
                            @endisset
                        </select>
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
            ملاحظة: تم ربط قائمتَي "نوع النشاط" و"نوع البطاقة" بقاعدة البيانات.
        </div>
    </div>
@endsection
