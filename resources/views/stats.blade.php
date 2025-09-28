@extends('layouts.app')

@section('title', 'الإحصائيات | أناقة ستور')

@section('content')
    <div class="max-w-6xl mx-auto p-4" dir="rtl">
        <div class="mb-6 text-right">
            <h1 class="text-2xl font-semibold tracking-tight">لوحة الإحصائيات</h1>
            <p class="text-slate-600 mt-1">ملخص الأداء والبيانات الرئيسية</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-sm text-slate-500">إجمالي الكوافيرات</div>
                <div class="text-2xl font-bold">{{ number_format($totalHairdressers) }}</div>
            </div>
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-sm text-slate-500">عدد البطاقات المخصصة</div>
                <div class="text-2xl font-bold">{{ number_format($totalCards) }}</div>
            </div>
            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <div class="text-sm text-slate-500">إجمالي المبيعات</div>
                <div class="text-2xl font-bold">{{ number_format($totalSales) }} فاتورة</div>
                <div class="text-sm text-slate-500">إجمالي الإيرادات: {{ number_format($totalRevenue, 2) }} </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
            <div class="lg:col-span-2 bg-white border rounded-lg p-4 shadow-sm">
                <canvas id="monthlyChart" height="160"></canvas>
            </div>

            <div class="bg-white border rounded-lg p-4 shadow-sm">
                <h2 class="text-lg font-medium mb-3">حالة البطاقات</h2>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center justify-between"><span>منتهية</span><span
                            class="font-semibold">{{ $expired }}</span></div>
                    <div class="flex items-center justify-between"><span>قريبة الانتهاء (30 يوم)</span><span
                            class="font-semibold">{{ $nearExpiring }}</span></div>
                </div>

                <h3 class="mt-4 text-md font-medium">أعلى الكوافيرات بالنقاط</h3>
                <ol class="list-decimal list-inside mt-2 text-sm">
                    @foreach ($top as $t)
                        <li class="flex justify-between"><span>{{ $t->Hairdresser_Name }}</span><span
                                class="font-semibold">{{ $t->Total_Points }}</span></li>
                    @endforeach
                </ol>
            </div>
        </div>

        <div class="bg-white border rounded-lg p-4 shadow-sm">
            <h2 class="text-lg font-medium mb-3">تفاصيل أعلى 10 كوافيرات</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-right">
                    <thead class="bg-slate-50 text-slate-700">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th class="px-3 py-2">الكوافير</th>
                            <th class="px-3 py-2">النقاط</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($top as $i => $t)
                            <tr class="border-t">
                                <td class="px-3 py-2">{{ $i + 1 }}</td>
                                <td class="px-3 py-2">{{ $t->Hairdresser_Name }}</td>
                                <td class="px-3 py-2">{{ $t->Total_Points }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const monthly = @json($monthly->pluck('total', 'ym'));
            const labels = Object.keys(monthly);
            const data = Object.values(monthly).map(v => +v);

            const ctx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'إجمالي المبيعات',
                        data: data,
                        borderColor: '#2563eb',
                        backgroundColor: 'rgba(37,99,235,0.08)',
                        tension: 0.3,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    locale: 'ar',
                    scales: {
                        x: {
                            ticks: {
                                maxRotation: 0,
                                autoSkip: true
                            }
                        },
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        });
    </script>
@endpush
