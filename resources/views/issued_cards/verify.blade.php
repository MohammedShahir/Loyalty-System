@extends('layouts.app')
@section('title', 'تحقق من البطاقة')
@section('content')
    <div class="max-w-md mx-auto p-4" dir="rtl">
        <h1 class="text-xl mb-4">التحقق من رمز البطاقة</h1>
        @if ($errors->any())
            <div class="text-rose-600 mb-2">{{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('issued_cards.verify') }}">
            @csrf
            <div class="mb-3">
                <label class="text-sm">رمز البطاقة العام</label>
                <input name="public_code" class="w-full border rounded p-2" />
            </div>
            <button class="px-3 py-2 bg-slate-800 text-white rounded">تحقق</button>
        </form>
    </div>
@endsection
