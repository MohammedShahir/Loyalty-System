<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SalesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit(int $id): View
    {
        $sale = DB::table('sales')->where('id', $id)->firstOrFail();
        $hairdressers = DB::table('hairdresser')->select('id', 'Hairdresser_Name')->orderBy('Hairdresser_Name')->get();
        return view('sales.edit', compact('sale', 'hairdressers'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'Invoice_Num' => ['nullable', 'integer'],
            'Total_Sales' => ['required', 'numeric', 'min:0'],
            'Hairdresser_Id' => ['required', 'integer'],
        ]);

        DB::transaction(function () use ($id, $validated) {
            DB::table('sales')->where('id', $id)->update([
                'Invoice_Num' => $validated['Invoice_Num'] ?? 0,
                'Total_Sales' => $validated['Total_Sales'],
                'Hairdresser_Id' => $validated['Hairdresser_Id'],
            ]);

            // recalc points for the hairdresser
            $totalBills = (float) DB::table('sales')->where('Hairdresser_Id', $validated['Hairdresser_Id'])->sum('Total_Sales');
            $points = (int) floor($totalBills / 10);
            DB::table('hairdresser')->where('id', $validated['Hairdresser_Id'])->update(['Total_Points' => $points]);
        });

        return redirect()->route('reports.index', ['hairdresser_id' => $validated['Hairdresser_Id']])->with('success', 'تم تحديث الفاتورة');
    }

    public function destroy(int $id): RedirectResponse
    {
        $sale = DB::table('sales')->where('id', $id)->first();
        if (! $sale) {
            return back()->withErrors(['msg' => 'الفاتورة غير موجودة']);
        }

        DB::transaction(function () use ($sale) {
            DB::table('sales')->where('id', $sale->id)->delete();

            // recalc points for related hairdresser
            $totalBills = (float) DB::table('sales')->where('Hairdresser_Id', $sale->Hairdresser_Id)->sum('Total_Sales');
            $points = (int) floor($totalBills / 10);
            DB::table('hairdresser')->where('id', $sale->Hairdresser_Id)->update(['Total_Points' => $points]);
        });

        return back()->with('success', 'تم حذف الفاتورة');
    }
}
