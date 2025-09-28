<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
        @hasSection('title')
            @yield('title')
        @else
            أناقة ستور
        @endif
    </title>

    <link rel="icon" type="image/x-icon" href="{{ asset('images/eleg.jpg') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>

<body class="bg-white text-slate-900">
    <header
        class="border-b border-slate-200 bg-white/80 backdrop-blur supports-[backdrop-filter]:bg-white/60 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                <a href="{{ route('reports.stats') }}" class="inline-flex items-center gap-3">
                    <img src="{{ asset('images/eleg.jpg') }}" alt="Logo" class="h-12 w-12 rounded" />
                    <span class="font-semibold text-lg tracking-tight">أناقة ستور</span>
                </a>

                <nav class="hidden md:flex items-center gap-6">
                    <a href="{{ route('reports.index') }}"
                        class="text-sm font-medium text-slate-700 hover:text-slate-900 transition">التقارير</a>
                    <a href="{{ route('calculating-points') }}"
                        class="text-sm font-medium text-slate-700 hover:text-slate-900 transition">احتساب النقاط</a>
                    <a href="{{ route('add-hairdresser') }}"
                        class="text-sm font-medium text-slate-700 hover:text-slate-900 transition">إضافة كوافير</a>
                    <a href="{{ route('control.index') }}"
                        class="text-sm font-medium text-slate-700 hover:text-slate-900 transition">البحث</a>
                </nav>

                @auth
                    <div class="hidden md:flex items-center gap-3">
                        <button id="logoutBtn" title="تسجيل الخروج"
                            class="inline-flex items-center justify-center rounded-md p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                class="h-6 w-6">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H3" />
                            </svg>
                        </button>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="hidden">
                            @csrf
                        </form>
                    </div>
                @endauth

                <button id="mobileMenuButton"
                    class="md:hidden inline-flex items-center justify-center rounded-md p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-slate-400"
                    aria-controls="mobileMenu" aria-expanded="false">
                    <span class="sr-only">فتح القائمة الرئيسية</span>
                    <svg id="iconHamburger" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg id="iconClose" class="h-6 w-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
        </div>

        <div id="mobileMenu" class="md:hidden hidden border-t border-slate-200">
            <div class="space-y-1 px-4 pb-4 pt-2">
                <a href="{{ route('calculating-points') }}"
                    class="block rounded px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">احتساب
                    النقاط</a>
                <a href="{{ route('add-hairdresser') }}"
                    class="block rounded px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">إضافة
                    كوافير</a>
                @auth
                    <button id="logoutBtnMobile"
                        class="w-full text-right block rounded px-3 py-2 text-base font-medium text-slate-700 hover:bg-slate-100">تسجيل
                        الخروج</button>
                @endauth
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
        @yield('extra')
    </main>

    <script>
        (function() {
            const btn = document.getElementById('mobileMenuButton');
            const menu = document.getElementById('mobileMenu');
            const iconHamburger = document.getElementById('iconHamburger');
            const iconClose = document.getElementById('iconClose');
            const logoutBtn = document.getElementById('logoutBtn');
            const logoutBtnMobile = document.getElementById('logoutBtnMobile');
            const logoutForm = document.getElementById('logoutForm');

            if (btn && menu) {
                btn.addEventListener('click', function() {
                    const isHidden = menu.classList.contains('hidden');
                    if (isHidden) {
                        menu.classList.remove('hidden');
                        btn.setAttribute('aria-expanded', 'true');
                        iconHamburger && iconHamburger.classList.add('hidden');
                        iconClose && iconClose.classList.remove('hidden');
                    } else {
                        menu.classList.add('hidden');
                        btn.setAttribute('aria-expanded', 'false');
                        iconHamburger && iconHamburger.classList.remove('hidden');
                        iconClose && iconClose.classList.add('hidden');
                    }
                });
            }

            function submitLogout(e) {
                if (!logoutForm) return;
                e && e.preventDefault();
                logoutForm.submit();
            }

            if (logoutBtn) logoutBtn.addEventListener('click', submitLogout);
            if (logoutBtnMobile) logoutBtnMobile.addEventListener('click', submitLogout);
        })();
    </script>
    @stack('scripts')
</body>

</html>
