@extends('layouts.app')

@section('title', 'التقارير | أناقة ستور')

@section('content')
    <div class="max-w-6xl mx-auto" dir="rtl">
        <div class="mb-6 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">التقارير</h1>
            <p class="text-slate-600 mt-1">ملخص البطاقات ونقاط الكوافيرات</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <h2 class="text-lg font-medium mb-3">البطاقات المنتهية أو القريبة من الانتهاء</h2>
                @if ($expiring->isEmpty())
                    <div class="text-slate-500">لا توجد بطاقات منتهية أو على وشك الانتهاء.</div>
                @else
                    <div class="mb-3 flex gap-4">
                        <div class="p-3 bg-red-50 rounded-md">
                            <div class="text-sm text-slate-600">البطاقات منتهية</div>
                            <div class="text-xl font-semibold text-red-700">{{ $expired_count ?? 0 }}</div>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-md">
                            <div class="text-sm text-slate-600">قريبة من الانتهاء (30 يوم)</div>
                            <div class="text-xl font-semibold text-yellow-700">{{ $near_expiring_count ?? 0 }}</div>
                        </div>
                    </div>

                    <table class="w-full text-sm text-right">
                        <thead class="text-slate-700">
                            <tr>
                                <th class="px-3 py-2">الكوافير</th>
                                <th class="px-3 py-2">البطاقة</th>
                                <th class="px-3 py-2">تاريخ الانتهاء</th>
                                <th class="px-3 py-2">باقي الأيام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($expiring as $e)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ $e->Hairdresser_Name }}</td>
                                    <td class="px-3 py-2">{{ $e->Card_Name }}</td>
                                    <td class="px-3 py-2">
                                        {{ \Illuminate\Support\Carbon::parse($e->Expiration_Date)->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">{{ $e->Days_Until_Expiry }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            <div class="bg-white border border-slate-200 rounded-xl p-4">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-medium">تقرير البطاقات والنقاط</h2>
                    <button id="exportPdf"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">تصدير
                        PDF</button>
                </div>

                <div id="reportTableWrapper" class="overflow-x-auto">
                    <table id="reportTable" class="w-full text-sm text-right">
                        <thead class="text-slate-700 bg-slate-50">
                            <tr>
                                <th class="px-3 py-2">#</th>
                                <th class="px-3 py-2">الكوافير</th>
                                <th class="px-3 py-2">النشاط</th>
                                <th class="px-3 py-2">النقاط</th>
                                <th class="px-3 py-2">نوع البطاقة</th>
                                <th class="px-3 py-2">تاريخ الإصدار</th>
                                <th class="px-3 py-2">تاريخ الانتهاء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rows as $r)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2">{{ $r->Hairdresser_Name }}</td>
                                    <td class="px-3 py-2">{{ $r->Activity_Name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $r->Total_Points }}</td>
                                    <td class="px-3 py-2">{{ $r->Card_Name ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        {{ $r->Card_Issued_At ? \Illuminate\Support\Carbon::parse($r->Card_Issued_At)->format('Y-m-d') : '-' }}
                                    </td>
                                    <td class="px-3 py-2">
                                        {{ $r->Card_Issued_At ? \Illuminate\Support\Carbon::parse($r->Card_Issued_At)->addYear()->format('Y-m-d') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto mt-6" dir="rtl">
        <div class="bg-white border border-slate-200 rounded-xl p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium">تقارير الفواتير</h2>
                <div class="flex items-center gap-2">
                    <button id="exportInvoicesPdf"
                        class="inline-flex items-center rounded-lg bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-black">تصدير
                        PDF</button>
                </div>
            </div>

            <form method="GET" action="{{ url()->current() }}" class="mb-4 flex gap-2 items-center justify-end">
                <div class="flex items-center gap-2">
                    <label class="text-sm">الكوافير</label>
                    <select id="hairdresserSelect" name="hairdresser_id" class="border rounded px-2 py-1">
                        <option value="">-- كل الكوافيرات --</option>
                        @if (!empty($hairdressersList) && is_iterable($hairdressersList))
                            @foreach ($hairdressersList as $h)
                                @php
                                    $hid = is_object($h) ? $h->id ?? '' : (is_array($h) ? $h['id'] ?? '' : '');
                                    $hname = is_object($h)
                                        ? $h->Hairdresser_Name ?? ($h->name ?? '-')
                                        : (is_array($h)
                                            ? $h['Hairdresser_Name'] ?? ($h['name'] ?? '-')
                                            : '-');
                                @endphp
                                <option value="{{ $hid }}" @if (request('hairdresser_id') == $hid) selected @endif>
                                    {{ $hname }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <label class="text-sm">رقم الفاتورة</label>
                    <input type="text" name="invoice" value="{{ request('invoice') }}" class="border rounded px-2 py-1"
                        placeholder="بحث برقم الفاتورة">
                    <button type="submit" class="px-3 py-1 bg-slate-800 text-white rounded">بحث</button>
                </div>
            </form>

            <div id="invoicesTableWrapper" class="overflow-x-auto">
                @if (!request('hairdresser_id'))
                    <div class="text-slate-500">اختر كوافيراً لعرض الفواتير.</div>
                @elseif (empty($invoices) || count($invoices) === 0)
                    <div class="text-slate-500">لا توجد فواتير للعرض لهذا الكوافير.</div>
                @else
                    <table id="invoicesTable" class="w-full text-sm text-right">
                        <thead class="text-slate-700 bg-slate-50">
                            <tr>
                                <th class="px-3 py-2">#</th>
                                <th class="px-3 py-2">رقم الفاتورة</th>
                                <th class="px-3 py-2">المبلغ</th>
                                <th class="px-3 py-2">التاريخ</th>
                                <th class="px-3 py-2">الكوافير</th>
                                <th class="px-3 py-2">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $inv)
                                <tr class="border-t">
                                    <td class="px-3 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-3 py-2">{{ $inv->Invoice_Num }}</td>
                                    <td class="px-3 py-2">{{ number_format($inv->Total_Sales, 2) }}</td>
                                    <td class="px-3 py-2">
                                        {{ \Illuminate\Support\Carbon::parse($inv->Sale_Date)->format('Y-m-d') }}</td>
                                    <td class="px-3 py-2">{{ $inv->Hairdresser_Name ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <button type="button" data-edit-url="{{ route('sales.edit', $inv->id) }}"
                                            class="confirm-edit inline-block px-2 py-1 text-sm bg-amber-600 text-white rounded">تعديل</button>

                                        <form id="delete-invoice-{{ $inv->id }}" method="POST"
                                            action="{{ route('sales.destroy', $inv->id) }}" style="display:inline"
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="confirm_password" value="" />
                                            <button type="button" data-form-id="{{ $inv->id }}"
                                                class="confirm-delete inline-block px-2 py-1 text-sm bg-red-600 text-white rounded">حذف</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>

    <!-- Password confirmation modal (copied from control.blade.php) -->
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
        // cards report export (same as before)
        document.getElementById('exportPdf')?.addEventListener('click', function() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                unit: 'pt',
                format: 'a4',
                putOnlyUsedFonts: true
            });
            const headers = [
                ['#', 'الكوافير', 'النشاط', 'النقاط', 'نوع البطاقة', 'تاريخ الإصدار', 'تاريخ الانتهاء']
            ];
            const body = Array.from(document.querySelectorAll('#reportTable tbody tr')).map(tr => Array.from(tr
                .querySelectorAll('td')).slice(0, 7).map(td => td.innerText.trim()));
            doc.autoTable({
                head: headers,
                body: body,
                styles: {
                    font: 'helvetica',
                    fontSize: 10
                },
                headStyles: {
                    fillColor: [241, 245, 249],
                    textColor: 50
                },
                theme: 'grid',
                startY: 40,
                margin: {
                    left: 40,
                    right: 40
                }
            });
            doc.save('reports.pdf');
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Tom Select (searchable)
            try {
                new TomSelect('#hairdresserSelect', {
                    create: false,
                    allowEmptyOption: true,
                    placeholder: 'اختر الكوافير',
                    dropdownDirection: 'auto'
                });
            } catch (e) {
                console.error(e);
            }

            // load html2canvas for invoices export
            const scr = document.createElement('script');
            scr.src = 'https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js';
            scr.onload = function() {
                const invBtn = document.getElementById('exportInvoicesPdf');
                if (!invBtn) return;
                invBtn.addEventListener('click', async function() {
                    const wrapper = document.getElementById('invoicesTableWrapper');
                    if (!wrapper) return alert('لا توجد فواتير للعرض');
                    const table = wrapper.querySelector('#invoicesTable');
                    if (!table) return alert('لا توجد فواتير للعرض');
                    const orig = wrapper.style.backgroundColor;
                    wrapper.style.backgroundColor = '#fff';
                    try {
                        const canvas = await html2canvas(wrapper, {
                            scale: 2,
                            useCORS: true,
                            allowTaint: false
                        });
                        const img = canvas.toDataURL('image/png');
                        const {
                            jsPDF
                        } = window.jspdf;
                        const pdf = new jsPDF('p', 'pt', 'a4');
                        const w = pdf.internal.pageSize.getWidth();
                        const props = pdf.getImageProperties(img);
                        const iw = w - 40;
                        const ih = (props.height * iw) / props.width;
                        pdf.addImage(img, 'PNG', 20, 20, iw, ih);
                        pdf.save('invoices.pdf');
                    } catch (err) {
                        console.error('Invoices PDF export error:', err);
                        // Fallback: try to generate a simple text-based PDF using jsPDF + autoTable
                        try {
                            const {
                                jsPDF
                            } = window.jspdf;
                            const doc = new jsPDF({
                                unit: 'pt',
                                format: 'a4'
                            });
                            const headers = Array.from(table.querySelectorAll('thead th')).map(th =>
                                th.innerText.trim());
                            const body = Array.from(table.querySelectorAll('tbody tr')).map(tr =>
                                Array.from(tr.querySelectorAll('td')).map(td => td.innerText
                                    .trim()));
                            doc.autoTable({
                                head: [headers],
                                body: body,
                                startY: 40,
                                margin: {
                                    left: 40,
                                    right: 40
                                },
                                styles: {
                                    fontSize: 10
                                }
                            });
                            doc.save('invoices_fallback.pdf');
                            alert('تم تنزيل PDF بديل (نص) بسبب قيود تصدير الصورة.');
                        } catch (fbErr) {
                            console.error('Fallback PDF generation failed:', fbErr);
                            alert('حدث خطأ أثناء إنشاء ملف PDF: ' + (err && err.message ? err
                                .message : 'خطأ غير معروف'));
                        }
                    } finally {
                        wrapper.style.backgroundColor = orig;
                    }
                });
            };
            document.head.appendChild(scr);

            // password modal logic (copied from control)
            (function() {
                let currentFormId = null;

                document.querySelectorAll('.confirm-delete').forEach(btn => {
                    btn.addEventListener('click', function() {
                        currentFormId = this.getAttribute('data-form-id');
                        const modal = document.getElementById('passwordModal');
                        modal.dataset.mode = 'delete';
                        document.getElementById('confirmPasswordInput').value = '';
                        document.getElementById('confirmError').classList.add('hidden');
                        modal.classList.remove('hidden');
                        modal.classList.add('flex', 'items-center', 'justify-center');
                    });
                });

                document.querySelectorAll('.confirm-edit').forEach(btn => {
                    btn.addEventListener('click', function() {
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
                    fetch("{{ route('confirm.password') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                password: pwd
                            })
                        })
                        .then(res => res.json()).then(data => {
                            if (data.ok) {
                                const mode = document.getElementById('passwordModal').dataset
                                    .mode || 'delete';
                                if (mode === 'delete') {
                                    const form = document.getElementById('delete-invoice-' +
                                        currentFormId);
                                    if (form) {
                                        form.querySelector('input[name="confirm_password"]').value =
                                            pwd;
                                        form.submit();
                                    }
                                } else if (mode === 'edit') {
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
        });
    </script>
@endpush
