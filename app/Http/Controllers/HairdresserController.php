<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class HairdresserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(): View
    {
        $activities = DB::table('activity')->select('id', 'Activity_Name')->orderBy('Activity_Name')->get();
        $cards = DB::table('cards')->select('id', 'Card_Name')->orderBy('Card_Name')->get();
        return view('add-hairdresser', compact('activities', 'cards'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'Hairdresser_Name' => ['required', 'string', 'max:100'],
            'Hairdresser_Owner' => ['nullable', 'string', 'max:120'],
            'Call_Num' => ['nullable', 'integer', 'digits_between:1,9'],
            'Whats_Num' => ['nullable', 'integer', 'digits_between:1,9'],
            'Address' => ['nullable', 'string', 'max:255'],
            'Type_of_Activity' => ['nullable', 'integer'],
            'Type_of_Card' => ['nullable', 'integer'],
            'Total_Sales' => ['nullable', 'numeric', 'min:0'],
            'Invoice_Num' => ['nullable', 'integer', 'min:0'],
            'Total_Points' => ['nullable', 'integer', 'min:0'],
        ]);

        return DB::transaction(function () use ($validated) {
            $hairdresserId = DB::table('hairdresser')->insertGetId([
                'Hairdresser_Name' => $validated['Hairdresser_Name'],
                'Hairdresser_Owner' => $validated['Hairdresser_Owner'] ?? 0,
                'Call_Num' => $validated['Call_Num'] ?? 0,
                'Whats_Num' => $validated['Whats_Num'] ?? 0,
                'Address' => $validated['Address'] ?? '',
                'Type_of_Activity' => $validated['Type_of_Activity'] ?? 0,
                'Type_of_Card' => $validated['Type_of_Card'] ?? 0,
                'Total_Points' => $validated['Total_Points'] ?? 0,
            ]);

            DB::table('sales')->insert([
                'Invoice_Num' => $validated['Invoice_Num'] ?? 0,
                'Total_Sales' => $validated['Total_Sales'] ?? 0,
                'Hairdresser_Id' => $hairdresserId,
            ]);

            return redirect()->route('add-hairdresser')->with('success', 'تم حفظ الكوافير وسجل المبيعات بنجاح');
        });
    }

    public function edit(int $id): View
    {
        $item = DB::table('hairdresser')->where('id', $id)->firstOrFail();
        $activities = DB::table('activity')->select('id', 'Activity_Name')->orderBy('Activity_Name')->get();
        $cards = DB::table('cards')->select('id', 'Card_Name')->orderBy('Card_Name')->get();
        return view('add-hairdresser', compact('item', 'activities', 'cards'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'Hairdresser_Name' => ['required', 'string', 'max:100'],
            'Hairdresser_Owner' => ['nullable', 'string', 'max:120'],
            'Call_Num' => ['nullable', 'integer', 'digits_between:1,9'],
            'Whats_Num' => ['nullable', 'integer', 'digits_between:1,9'],
            'Address' => ['nullable', 'string', 'max:255'],
            'Type_of_Activity' => ['nullable', 'integer'],
            'Type_of_Card' => ['nullable', 'integer'],
            'Total_Points' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::table('hairdresser')->where('id', $id)->update([
            'Hairdresser_Name' => $validated['Hairdresser_Name'],
            'Hairdresser_Owner' => $validated['Hairdresser_Owner'] ?? 0,
            'Call_Num' => $validated['Call_Num'] ?? 0,
            'Whats_Num' => $validated['Whats_Num'] ?? 0,
            'Address' => $validated['Address'] ?? '',
            'Type_of_Activity' => $validated['Type_of_Activity'] ?? 0,
            'Type_of_Card' => $validated['Type_of_Card'] ?? 0,
            'Total_Points' => $validated['Total_Points'] ?? 0,
        ]);

        return redirect()->route('control.index')->with('success', 'تم تحديث البيانات بنجاح');
    }
}
