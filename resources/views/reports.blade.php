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
                                        {{ $r->Expiration_Date ? \Illuminate\Support\Carbon::parse($r->Expiration_Date)->format('Y-m-d') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <script>
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
                ['#', 'الكوافير', 'النشاط', 'النقاط', 'نوع البطاقة', 'تاريخ الانتهاء']
            ];
            const body = Array.from(document.querySelectorAll('#reportTable tbody tr')).map((tr) => {
                return Array.from(tr.querySelectorAll('td')).slice(0, 6).map(td => td.innerText.trim());
            });

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
    </script>
@endpush
