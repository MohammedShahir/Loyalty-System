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
                            <td class="px-4 py-2">{{ $item->Hairdresser_Owner }}</td>
                            <td class="px-4 py-2">{{ $item->Call_Num }}</td>
                            <td class="px-4 py-2">{{ $item->Address }}</td>
                            <td class="px-4 py-2">
                                {{ $activities->firstWhere('id', $item->Type_of_Activity)->Activity_Name ?? '-' }}</td>
                            <td class="px-4 py-2">{{ $cards->firstWhere('id', $item->Type_of_Card)->Card_Name ?? '-' }}
                            </td>
                            <td class="px-4 py-2">{{ $item->Total_Points }}</td>
                            <td class="px-4 py-2">
                                <div class="flex gap-2 justify-end">
                                    <a href="{{ route('hairdresser.edit', $item->id) }}"
                                        class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium bg-amber-500 text-white hover:bg-amber-600">تعديل</a>
                                    <form action="{{ route('control.destroy', $item->id) }}" method="POST"
                                        onsubmit="return confirm('تأكيد الحذف؟');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center rounded-md px-3 py-1.5 text-xs font-medium bg-rose-600 text-white hover:bg-rose-700">حذف</button>
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

        <div class="mt-4">
            {{ $items->links() }}
        </div>
    </div>
@endsection
