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
    <script>
        document.getElementById('undoBtn')?.addEventListener('click', function() {
            const pwd = prompt('أدخل كلمة المرور لتأكيد التراجع');
            if (!pwd) return;
            document.getElementById('undo-confirm-password').value = pwd;
            document.getElementById('undo-form').submit();
        });
    </script>
@endpush
