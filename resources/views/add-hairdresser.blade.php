@extends('layouts.app')

@section('title', 'إضافة كوافير | أناقة ستور')

@section('content')
    <div class="max-w-4xl mx-auto" dir="rtl">
        <div class="mb-8 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">{{ isset($item) ? 'تعديل كوافير' : 'إضافة كوافير' }}</h1>
            <p class="text-slate-600 mt-1">
                {{ isset($item) ? 'تحديث بيانات الكوافير.' : 'إنشاء ملف كوافير جديد' }}</p>
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-right">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white border border-slate-200 rounded-xl shadow-sm">
            <form action="{{ isset($item) ? route('hairdresser.update', $item->id) : route('hairdresser.store') }}"
                method="POST" class="p-6 sm:p-8 space-y-8">
                @csrf
                @if (isset($item))
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Hairdresser_Name">الاسم</label>
                        <input id="Hairdresser_Name" name="Hairdresser_Name" type="text" required
                            placeholder="مثال: صالون أناقة"
                            value="{{ old('Hairdresser_Name', $item->Hairdresser_Name ?? '') }}"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                        @error('Hairdresser_Name')
                            <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Hairdresser_Owner">صاحب الصالون
                            (أسم)</label>
                        <input id="Hairdresser_Owner" name="Hairdresser_Owner" type="text" inputmode="numeric"
                            min="0" placeholder="أسم المالك"
                            value="{{ old('Hairdresser_Owner', $item->Hairdresser_Owner ?? '') }}"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                        @error('Hairdresser_Owner')
                            <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Call_Num">الهاتف</label>
                        <input id="Call_Num" name="Call_Num" type="tel" inputmode="numeric" minlength="9"
                            maxlength="9" placeholder="05XXXXXXXX" value="{{ old('Call_Num', $item->Call_Num ?? '') }}"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                        @error('Call_Num')
                            <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Whats_Num">واتساب</label>
                        <input id="Whats_Num" name="Whats_Num" type="tel" inputmode="numeric" minlength="9"
                            maxlength="9" placeholder="05XXXXXXXX" value="{{ old('Whats_Num', $item->Whats_Num ?? '') }}"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                        @error('Whats_Num')
                            <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="md:col-span-2 text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Address">العنوان</label>
                        <input id="Address" name="Address" type="text" placeholder="الشارع، المدينة"
                            value="{{ old('Address', $item->Address ?? '') }}"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                        @error('Address')
                            <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="text-right">
                        <label class="block text-sm font-medium text-slate-700 mb-1" for="Type_of_Activity">نوع
                            النشاط</label>
                        <select id="Type_of_Activity" name="Type_of_Activity"
                            class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                            <option value="" disabled
                                {{ old('Type_of_Activity', $item->Type_of_Activity ?? '') === '' ? 'selected' : '' }}>اختر
                                النشاط</option>
                            @isset($activities)
                                @foreach ($activities as $activity)
                                    <option value="{{ $activity->id }}"
                                        {{ (string) old('Type_of_Activity', $item->Type_of_Activity ?? '') === (string) $activity->id ? 'selected' : '' }}>
                                        {{ $activity->Activity_Name }}
                                    </option>
                                @endforeach
                            @endisset
                        </select>
                        @error('Type_of_Activity')
                            <div class="text-rose-600 text-xs mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Type_of_Card removed per request: card assignment/issuance is handled from the Issued Cards UI -->
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('control.index') }}"
                        class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">إلغاء</a>
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-400">{{ isset($item) ? 'تحديث' : 'حفظ' }}</button>
                </div>
            </form>
        </div>
    </div>
@endsection

{{-- @section('extra')
    <div class="max-w-4xl mx-auto mt-10" dir="rtl">
        <div class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-slate-500 text-right">
            ملاحظة: تم ربط قائمتَي "نوع النشاط" و"نوع البطاقة" بقاعدة البيانات.
        </div>
    </div>
@endsection --}}
