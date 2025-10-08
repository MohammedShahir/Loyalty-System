@extends('layouts.app')

@section('title', 'البحث | أناقة ستور')

@section('content')
    <div class="max-w-7xl mx-auto" dir="rtl">
        <div class="mb-6 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">البحث</h1>
            {{-- <p class="text-slate-600 mt-1">إدارة الكوافيرات: بحث، تصفية، تعديل، حذف.</p> --}}
        </div>

        @if (session('success'))
            <div class="mb-6 rounded-lg border border-green-200 bg-green-50 text-green-800 px-4 py-3 text-right">
                {{ session('success') }}
            </div>
        @endif

        <form method="GET" action="{{ route('control.index') }}" class="mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                <div class="md:col-span-2">
                    <label for="q" class="block text-sm font-medium text-slate-700 mb-1">بحث بالاسم أو
                        العنوان</label>
                    <input type="text" id="q" name="q" value="{{ request('q') }}" placeholder="اكتب للبحث"
                        class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right" />
                </div>
                <div>
                    <label for="activity" class="block text-sm font-medium text-slate-700 mb-1">نوع النشاط</label>
                    <select id="activity" name="activity"
                        class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                        <option value="">الكل</option>
                        @foreach ($activities as $a)
                            <option value="{{ $a->id }}" @selected(request('activity') == $a->id)>{{ $a->Activity_Name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="card" class="block text-sm font-medium text-slate-700 mb-1">نوع البطاقة</label>
                    <select id="card" name="card"
                        class="block w-full h-8 pr-1.5 shadow-xs shadow-gray-500/50 rounded-md border-2 border-stone-200 focus:border-slate-500 focus:ring-slate-500 text-right">
                        <option value="">الكل</option>
                        @foreach ($cards as $c)
                            <option value="{{ $c->id }}" @selected(request('card') == $c->id)>{{ $c->Card_Name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-2 mt-3">
                <button type="submit"
                    class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">تطبيق</button>
                <a href="{{ route('control.index') }}"
                    class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">إعادة
                    ضبط</a>
            </div>
        </form>

        <div class="overflow-x-auto border border-slate-200 rounded-xl">
            <table class="min-w-full text-sm text-right">
                <thead class="bg-slate-50 text-slate-700">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">الاسم</th>
                        <th class="px-4 py-3">تاريخ البطاقة</th>
                        <th class="px-4 py-3">انتهاء البطاقة</th>
                        <th class="px-4 py-3">المالك</th>
                        <th class="px-4 py-3">الهاتف</th>
                        <th class="px-4 py-3">العنوان</th>
                        <th class="px-4 py-3">النشاط</th>
                        <th class="px-4 py-3">البطاقة</th>
                        <th class="px-4 py-3">النقاط</th>
                        <th class="px-4 py-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($items as $item)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $loop->iteration }}</td>
                            <td class="px-4 py-2">{{ $item->Hairdresser_Name }}</td>
                            <td class="px-4 py-2">
                                @if (!empty($item->Card_Issued_At))
                                    {{ \Illuminate\Support\Carbon::parse($item->Card_Issued_At)->format('Y-m-d') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if (!empty($item->Card_Issued_At))
                                    {{ \Illuminate\Support\Carbon::parse($item->Card_Issued_At)->addYear()->format('Y-m-d') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-4 py-2">{{ $item->Hairdresser_Owner }}</td>
                            <td class="px-4 py-2">{{ $item->Call_Num }}</td>
                            <td class="px-4 py-2">{{ $item->Address }}</td>
                            <td class="px-4 py-2">
                                {{ $activities->firstWhere('id', $item->Type_of_Activity)->Activity_Name ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @php
                                    // Some rows may not have Assigned_Card_Id; avoid accessing undefined properties.
                                    $cardName = null;
                                    if (!empty($item->Card_Name)) {
                                        $cardName = $item->Card_Name;
                                    } elseif (isset($item->Assigned_Card_Id) && $item->Assigned_Card_Id) {
                                        $found = $cards->firstWhere('id', $item->Assigned_Card_Id);
                                        $cardName = $found->Card_Name ?? null;
                                    }
                                @endphp
                                {{ $cardName ?? '-' }}
                            </td>
                            <td class="px-4 py-2">{{ $item->Total_Points }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2 justify-end">
                                    <button type="button" data-edit-url="{{ route('hairdresser.edit', $item->id) }}"
                                        class="confirm-edit inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium bg-amber-500 text-white hover:bg-amber-600">تعديل</button>
                                    <form id="delete-form-{{ $item->id }}"
                                        action="{{ route('control.destroy', $item->id) }}" method="POST"
                                        class="delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="confirm_password" value="" />
                                        <button type="button" data-form-id="{{ $item->id }}"
                                            class="confirm-delete inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium bg-rose-600 text-white hover:bg-rose-700">حذف</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-6 text-center text-slate-500">لا توجد نتائج مطابقة.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Password confirmation modal -->
    <div id="passwordModal" class="fixed inset-0 z-50 hidden bg-black/40 p-4">
        <div class="w-full max-w-md bg-white rounded-lg p-6 mx-auto">
            <h3 class="text-lg font-medium mb-3">تأكيد العملية</h3>
            <p class="text-sm text-slate-600 mb-4">أدخل كلمة المرور لتأكيد الحذف.</p>
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
                    // mark mode as delete
                    const modal = document.getElementById('passwordModal');
                    modal.dataset.mode = 'delete';
                    document.getElementById('confirmPasswordInput').value = '';
                    document.getElementById('confirmError').classList.add('hidden');
                    modal.classList.remove('hidden');
                    modal.classList.add('flex', 'items-center', 'justify-center');
                });
            });

            // edit buttons
            document.querySelectorAll('.confirm-edit').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    currentFormId = this.getAttribute('data-edit-url');
                    const modal = document.getElementById('passwordModal');
                    modal.dataset.mode = 'edit';
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
                            } else if (mode === 'edit') {
                                // currentFormId contains the edit URL in this flow
                                window.location.href = currentFormId;
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
