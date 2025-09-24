<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CalculatingPointsController extends Controller
{
    public function create(): View
    {
        $hairdressers = DB::table('hairdresser')
            ->select('id', 'Hairdresser_Name')
            ->orderBy('Hairdresser_Name')
            ->get();

        return view('calculating-points', [
            'hairdressers' => $hairdressers,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'Hairdresser_Id' => ['required', 'integer', 'min:1'],
            'Total_Sales' => ['required', 'numeric', 'min:0'],
            'Invoice_Num' => ['nullable', 'integer', 'min:0'],
        ]);

        DB::transaction(function () use ($validated) {
            DB::table('sales')->insert([
                'Invoice_Num' => $validated['Invoice_Num'] ?? 0,
                'Total_Sales' => $validated['Total_Sales'],
                'Hairdresser_Id' => $validated['Hairdresser_Id'],
            ]);

            $totalBills = (float) DB::table('sales')
                ->where('Hairdresser_Id', $validated['Hairdresser_Id'])
                ->sum('Total_Sales');

            $points = (int) floor($totalBills / 10);

            DB::table('hairdresser')
                ->where('id', $validated['Hairdresser_Id'])
                ->update(['Total_Points' => $points]);
        });

        return redirect()->route('calculating-points')->with('success', 'تم حساب النقاط تلقائياً بناءً على إجمالي الفواتير.');
    }
}
