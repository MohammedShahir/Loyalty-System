{{-- @extends('layouts.app')

@section('title', 'تسجيل الدخول | أناقة ستور') --}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>تسجيل الدخول | أناقة ستور</title>

    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white text-slate-900">
    <header
        class="border-b border-slate-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('images/eleg.jpg') }}" alt="Logo" class="h-12 w-12 rounded" />
                    <span class="font-semibold text-lg tracking-tight">أناقة ستور</span>
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- @yield('content') --}}
        <div class="max-w-md mx-auto" dir="rtl">
            <div class="text-right mb-6">
                <h1 class="text-2xl font-semibold">تسجيل الدخول</h1>
                <p class="text-slate-600 mt-1">ادخل اسم المستخدم وكلمة المرور للمتابعة.</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <form action="{{ route('login.attempt') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="text-right">
                        <label for="name" class="block text-sm font-medium text-slate-700 mb-1">اسم المستخدم</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required
                            autocomplete="username"
                            class="block w-full h-9 pr-2 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>
                    <div class="text-right">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-1">كلمة المرور</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password"
                            class="block w-full h-9 pr-2 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                            class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-slate-400">دخول</button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</body>

</html>
