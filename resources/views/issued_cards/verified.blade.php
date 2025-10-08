@extends('layouts.app')
@section('title', 'نتيجة التحقق')
@section('content')
    <div class="max-w-md mx-auto p-4" dir="rtl">
        <h1 class="text-xl mb-4">التحقق</h1>
        <div class="bg-green-50 border-l-4 border-green-400 p-3">الرمز صالح للبطاقة: <strong
                class="font-mono">{{ $row->public_code }}</strong></div>
        <dl class="mt-3">
            <div class="flex justify-between">
                <dt class="font-semibold text-gray-700">الكوافير</dt>
                <dd class="text-gray-900">{{ $row->hairdresser_name ?? '— #' . $row->hairdresser_id }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="font-semibold text-gray-700">نوع البطاقة</dt>
                <dd class="text-gray-900">{{ $row->card_name ?? '— #' . $row->card_id }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="font-semibold text-gray-700">اسم المالك (إن وجد)</dt>
                <dd class="text-gray-900">{{ $row->owner_name ?? '—' }}</dd>
            </div>
            <div class="flex justify-between">
                <dt class="font-semibold text-gray-700">تاريخ الإصدار</dt>
                <dd class="text-gray-900">
                    {{ optional($row->issued_at)->format ? \Carbon\Carbon::parse($row->issued_at)->format('Y-m-d H:i') : $row->issued_at }}
                </dd>
            </div>
        </dl>
    </div>
@endsection
