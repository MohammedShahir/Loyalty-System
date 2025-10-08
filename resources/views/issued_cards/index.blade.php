@extends('layouts.app')
@section('title', 'البطاقات المصدرة')
@section('content')
    <div class="max-w-7xl mx-auto" dir="rtl">
        <div class="mb-6 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">البطاقات المصدرة</h1>
        </div>

        @if (session('status_msg'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-right">
                {{ session('status_msg') }}
            </div>
        @endif
        @if ($errors->has('revoke'))
            <div class="mb-6 rounded-lg border border-rose-200 bg-rose-50 text-rose-800 px-4 py-3 text-right">
                {{ $errors->first('revoke') }}
            </div>
        @endif

        <form method="GET" action="{{ route('issued_cards.index') }}" class="mb-3">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div class="md:col-span-2">
                    <label for="q" class="block text-sm font-medium text-slate-700 mb-1">بحث بالاسم</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}"
                        placeholder="اكتب اسم الكوافير"
                        class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                </div>
                <div>
                    <label for="card" class="block text-sm font-medium text-slate-700 mb-1">نوع البطاقة</label>
                    <select id="card" name="card"
                        class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                        <option value="">الكل</option>
                        @foreach ($cardTypes as $ct)
                            <option value="{{ $ct->id }}" @selected(request('card') == $ct->id)>{{ $ct->Card_Name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-2">
                    <button type="submit"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">تصفية</button>
                    <a href="{{ route('issued_cards.index') }}"
                        class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">إعادة
                        ضبط</a>
                </div>
            </div>
        </form>

        <div class="flex items-center gap-3 mb-3 flex-wrap">
            <a href="{{ route('issued_cards.create') }}"
                class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">إنشاء
                بطاقة</a>
            <a href="{{ route('issued_cards.index') }}"
                class="px-2 py-1 text-sm {{ !$filter_status ? 'font-semibold' : '' }}">الكل</a>
            <a href="{{ route('issued_cards.index', ['status' => 'active']) }}"
                class="px-2 py-1 text-sm {{ $filter_status === 'active' ? 'font-semibold' : '' }}">فعالة</a>
            <a href="{{ route('issued_cards.index', ['status' => 'revoked']) }}"
                class="px-2 py-1 text-sm {{ $filter_status === 'revoked' ? 'font-semibold' : '' }}">ملغاة</a>
        </div>

        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="min-w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">الكوافير</th>
                        <th class="px-4 py-3">الرمز العام</th>
                        <th class="px-4 py-3">نوع البطاقة</th>
                        <th class="px-4 py-3">تاريخ الإصدار</th>
                        <th class="px-4 py-3">الحالة</th>
                        <th class="px-4 py-3">المُصدر</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cards as $c)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $c->Hairdresser_Name }}</td>
                            <td class="px-4 py-2 font-mono">{{ $c->public_code }}</td>
                            <td class="px-4 py-2">{{ $c->card_name ?? $c->card_id }}</td>
                            <td class="px-4 py-2">{{ $c->issued_at }}</td>
                            <td class="px-4 py-2">
                                @if ($c->is_revoked)
                                    <span class="text-rose-600">ملغاة</span>
                                @else
                                    <span class="text-green-600">فعالة</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $c->issuer_name }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('issued_cards.audit', $c->public_code) }}"
                                        class="text-blue-600 hover:underline">سجل</a>
                                    @if (!$c->is_revoked)
                                        <form id="delete-form-{{ $c->public_code }}" method="POST"
                                            action="{{ route('issued_cards.revoke', $c->public_code) }}">
                                            @csrf
                                            <input type="hidden" name="reason" value="إلغاء عبر الواجهة" />
                                            <input type="hidden" name="confirm_password" value="" />
                                            <button type="button" data-form-id="{{ $c->public_code }}"
                                                class="confirm-delete inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium bg-rose-600 text-white hover:bg-rose-700">إلغاء</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-slate-500">لا توجد نتائج.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $cards->links() }}</div>
    </div>

@endsection

@push('scripts')
    <!-- Password confirmation modal -->
    <div id="passwordModal" class="fixed inset-0 z-50 hidden bg-black/40 p-4">
        <div class="w-full max-w-md bg-white rounded-lg p-6 mx-auto">
            <h3 class="text-lg font-medium mb-3">تأكيد العملية</h3>
            <p class="text-sm text-slate-600 mb-4">أدخل كلمة المرور لتأكيد الإلغاء.</p>
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
            let currentFormId = null;

            document.querySelectorAll('.confirm-delete').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    currentFormId = this.getAttribute('data-form-id');
                    const modal = document.getElementById('passwordModal');
                    modal.dataset.mode = 'delete';
                    document.getElementById('confirmPasswordInput').value = '';
                    document.getElementById('confirmError').classList.add('hidden');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex', 'items-center', 'justify-center');
                });
            });

            document.getElementById('cancelPwd').addEventListener('click', function() {
                const modal = document.getElementById('passwordModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'items-center', 'justify-center');
            });

            document.getElementById('submitPwd').addEventListener('click', function() {
                const pwd = document.getElementById('confirmPasswordInput').value;
                if (!pwd) {
                    const err = document.getElementById('confirmError');
                    err.innerText = 'الرجاء إدخال كلمة المرور';
                    err.classList.remove('hidden');
                    return;
                }

                // send AJAX to verify
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
                            const mode = document.getElementById('passwordModal').dataset.mode || 'delete';
                            if (mode === 'delete') {
                                const form = document.getElementById('delete-form-' + currentFormId);
                                if (form) {
                                    form.querySelector('input[name="confirm_password"]').value = pwd;
                                    form.submit();
                                }
                            }
                        } else {
                            const err = document.getElementById('confirmError');
                            err.innerText = data.message || 'كلمة المرور غير صحيحة';
                            err.classList.remove('hidden');
                        }
                    }).catch(() => {
                        const err = document.getElementById('confirmError');
                        err.innerText = 'حدث خطأ في الاتصال';
                        err.classList.remove('hidden');
                    });
            });
        })();
    </script>
@endpush
