@extends('layouts.app')
@section('title', 'سجل البطاقة')
@section('content')
    <div class="max-w-7xl mx-auto" dir="rtl">
        <div class="mb-6 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">سجل البطاقة {{ $card->public_code }}</h1>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <div class="p-4 text-right">
                <form id="undo-form" method="POST" action="{{ route('issued_cards.undo_audit', $card->public_code) }}"
                    class="inline-block">
                    @csrf
                    <input type="hidden" name="confirm_password" id="undo-confirm-password" value="" />
                    <button type="button" id="undoBtn"
                        class="inline-flex items-center rounded-md px-3 py-1.5 text-sm font-medium bg-amber-500 text-white hover:bg-amber-600">تراجع</button>
                </form>
                <a href="{{ url()->previous() }}" id="backLink"
                    class="inline-flex items-center mr-3 rounded-md px-3 py-1.5 text-sm font-medium bg-slate-100 text-slate-800 hover:bg-slate-200">عودة</a>
            </div>
            <table class="min-w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">الوقت</th>
                        <th class="px-4 py-3">الإجراء</th>
                        <th class="px-4 py-3">المستخدم</th>
                        <th class="px-4 py-3">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($audit as $a)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $a->created_at }}</td>
                            <td class="px-4 py-2">{{ $a->action }}</td>
                            <td class="px-4 py-2">{{ $a->actor_name ?? $a->actor_id }}</td>
                            <td class="px-4 py-2">{{ $a->notes }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-slate-500">لا يوجد سجل</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Password confirmation modal (reused from issued_cards.index) -->
    <div id="passwordModal" class="fixed inset-0 z-50 hidden bg-black/40 p-4">
        <div class="w-full max-w-md bg-white rounded-lg p-6 mx-auto">
            <h3 class="text-lg font-medium mb-3">تأكيد العملية</h3>
            <p class="text-sm text-slate-600 mb-4">أدخل كلمة المرور لتأكيد التراجع.</p>
            <div class="mb-3">
                <input id="confirmPasswordInput" type="password" placeholder="كلمة المرور"
                    class="w-full rounded-md border px-3 py-2" />
                <div id="confirmError" class="text-sm text-rose-600 mt-2 hidden"></div>
            </div>
            <div class="flex justify-end gap-2">
                <button id="cancelPwd" class="px-3 py-2 rounded-md border">إلغاء</button>
                <button id="submitPwd" class="px-3 py-2 rounded-md bg-rose-600 text-white">تأكيد</button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            // Single-mode modal for confirming the undo action on this page
            const undoBtn = document.getElementById('undoBtn');
            const modal = document.getElementById('passwordModal');
            const pwdInput = document.getElementById('confirmPasswordInput');
            const errBox = document.getElementById('confirmError');

            undoBtn?.addEventListener('click', function() {
                // open modal
                pwdInput.value = '';
                errBox.classList.add('hidden');
                modal.classList.remove('hidden');
                modal.classList.add('flex', 'items-center', 'justify-center');
            });

            document.getElementById('cancelPwd').addEventListener('click', function() {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'items-center', 'justify-center');
            });

            document.getElementById('submitPwd').addEventListener('click', function() {
                const pwd = pwdInput.value;
                if (!pwd) {
                    errBox.innerText = 'الرجاء إدخال كلمة المرور';
                    errBox.classList.remove('hidden');
                    return;
                }

                fetch("{{ route('confirm.password') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            password: pwd
                        })
                    }).then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            // submit the undo form with the confirmed password
                            const form = document.getElementById('undo-form');
                            if (form) {
                                form.querySelector('input[name="confirm_password"]').value = pwd;
                                form.submit();
                            }
                        } else {
                            errBox.innerText = data.message || 'كلمة المرور غير صحيحة';
                            errBox.classList.remove('hidden');
                        }
                    }).catch(() => {
                        errBox.innerText = 'حدث خطأ في الاتصال';
                        errBox.classList.remove('hidden');
                    });
            });

            // Make backLink use history.back() as a fallback when previous URL equals current
            const backLink = document.getElementById('backLink');
            if (backLink) {
                const prev = backLink.getAttribute('href');
                try {
                    const current = window.location.href;
                    if (!prev || prev === current) {
                        backLink.addEventListener('click', function(e) {
                            e.preventDefault();
                            history.back();
                        });
                    }
                } catch (e) {
                    // ignore
                }
            }
        })();
    </script>
@endpush
