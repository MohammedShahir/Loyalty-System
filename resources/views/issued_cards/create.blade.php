@extends('layouts.app')

@section('title', 'إنشاء بطاقة')

@section('content')
    <div class="max-w-4xl mx-auto p-4" dir="rtl">
        <h1 class="text-xl font-semibold mb-4">إنشاء بطاقات</h1>

        @if (session('created_codes'))
            <div class="bg-green-50 border-l-4 border-green-400 p-3 mb-4">
                <div class="font-semibold">تم إنشاء البطاقات التالية:</div>
                <ul class="list-disc list-inside">
                    @foreach (session('created_codes') as $c)
                        <li>{{ $c }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('issue_errors'))
            <div class="bg-rose-50 border-l-4 border-rose-400 p-3 mb-4">
                <div class="font-semibold">بعض الأخطاء حدثت أثناء إنشاء البطاقات:</div>
                <ul class="list-disc list-inside text-sm">
                    @foreach (session('issue_errors') as $err)
                        <li>كوافير #{{ $err['hairdresser_id'] }}: {{ $err['error'] }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('issued_cards.store') }}">
            @csrf
            <div class="mb-3">
                <label class="text-sm block mb-2">اختر الكوافير</label>

                <div class="flex gap-3 mb-2">
                    <input id="searchName" type="text" placeholder="بحث بالاسم" class="border rounded p-2 w-1/2" />
                    <select id="filterActivity" class="border rounded p-2 w-1/2">
                        <option value="">الكل - نوع النشاط</option>
                        @foreach ($hairdressers->pluck('Type_of_Activity')->unique() as $act)
                            <option value="{{ $act }}">
                                {{ $activities->firstWhere('id', $act)->Activity_Name ?? $act }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="overflow-x-auto border rounded">
                    <table id="hairdresserTable" class="min-w-full text-sm text-right">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="px-4 py-3"><input id="selectAll" type="checkbox" /></th>
                                <th class="px-4 py-3">#</th>
                                <th class="px-4 py-3">الاسم</th>
                                <th class="px-4 py-3">المالك</th>
                                <th class="px-4 py-3">الهاتف</th>
                                <th class="px-4 py-3">نوع النشاط</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($hairdressers as $h)
                                <tr class="border-t" data-name="{{ strtolower($h->Hairdresser_Name) }}"
                                    data-activity="{{ $h->Type_of_Activity }}">
                                    <td class="px-4 py-2 text-center"><input type="checkbox" name="hairdresser_ids[]"
                                            value="{{ $h->id }}" class="row-check" /></td>
                                    <td class="px-4 py-2">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-2">{{ $h->Hairdresser_Name }}</td>
                                    <td class="px-4 py-2">{{ $h->Hairdresser_Owner ?? '-' }}</td>
                                    <td class="px-4 py-2">{{ $h->Call_Num ?? '-' }}</td>
                                    <td class="px-4 py-2">
                                        {{ $activities->firstWhere('id', $h->Type_of_Activity)->Activity_Name ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs text-slate-500 mt-1">استخدم مربع البحث أو فلتر نوع النشاط لايجاد الكوافير، وضع علامة
                    لاختيار من تريد إصدار بطاقة له.</p>
            </div>

            <div class="mb-3">
                <label class="text-sm">نوع البطاقة</label>
                <select name="card_id" class="w-full border rounded p-2">
                    @foreach ($cardTypes as $c)
                        <option value="{{ $c->id }}">{{ $c->Card_Name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Owner name is read from hairdresser table (Hairdresser_Owner) -->

            <div class="flex gap-2">
                <button class="px-4 py-2 bg-slate-800 text-white rounded">إنشاء</button>
                <a href="{{ route('issued_cards.index') }}" class="px-4 py-2 border rounded">عرض البطاقات</a>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        (function() {
            const search = document.getElementById('searchName');
            const filter = document.getElementById('filterActivity');
            const table = document.getElementById('hairdresserTable');
            const rows = Array.from(table.querySelectorAll('tbody tr'));
            const selectAll = document.getElementById('selectAll');

            function applyFilter() {
                const q = (search.value || '').trim().toLowerCase();
                const activity = filter.value || '';
                rows.forEach(r => {
                    const name = r.dataset.name || '';
                    const act = r.dataset.activity || '';
                    const matches = (q === '' || name.includes(q)) && (activity === '' || act === activity);
                    r.style.display = matches ? '' : 'none';
                });
            }

            search.addEventListener('input', applyFilter);
            filter.addEventListener('change', applyFilter);

            selectAll.addEventListener('change', function() {
                const checked = this.checked;
                rows.forEach(r => {
                    if (r.style.display !== 'none') {
                        const cb = r.querySelector('.row-check');
                        if (cb) cb.checked = checked;
                    }
                });
            });
        })();
    </script>
@endpush
