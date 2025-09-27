<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        // include assigned card dates from hairdresser_cards (if any)
        $query = DB::table('hairdresser as h')
            ->leftJoin('hairdresser_cards as hc', 'hc.hairdresser_id', '=', 'h.id')
            ->select('h.*', 'hc.card_id as Assigned_Card_Id', 'hc.Release_Date', 'hc.Expiration_Date');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('Hairdresser_Name', 'like', "%{$search}%")
                    ->orWhere('Address', 'like', "%{$search}%");
            });
        }

        if ($activity = $request->input('activity')) {
            $query->where('Type_of_Activity', (int) $activity);
        }

        if ($card = $request->input('card')) {
            $query->where('Type_of_Card', (int) $card);
        }

        $hairdressers = $query->orderBy('h.id', 'desc')->paginate(10)->withQueryString();

        $activities = DB::table('activity')->select('id', 'Activity_Name')->orderBy('Activity_Name')->get();
        $cards = DB::table('cards')->select('id', 'Card_Name')->orderBy('Card_Name')->get();

        return view('control', [
            'items' => $hairdressers,
            'activities' => $activities,
            'cards' => $cards,
        ]);
    }

    /**
     * Reports page: expiring cards and points summary.
     */
    public function reports(Request $request): View
    {
        // Expiring cards (using the view if available). Provide graceful fallback if view missing.
        try {
            $expiring = DB::table('expired_cards_view')->orderBy('Days_Until_Expiry')->get();
        } catch (\Exception $e) {
            // If the DB view doesn't exist, build a fallback query joining hairdresser_cards
            $expiring = DB::table('hairdresser as h')
                ->join('hairdresser_cards as hc', 'hc.hairdresser_id', '=', 'h.id')
                ->join('cards as c', 'hc.card_id', '=', 'c.id')
                ->select('h.Hairdresser_Name', 'c.Card_Name', 'hc.Expiration_Date', DB::raw("DATEDIFF(hc.Expiration_Date, CURRENT_DATE()) as Days_Until_Expiry"))
                ->orderBy('Days_Until_Expiry')
                ->get();
        }

        // Points summary per hairdresser
        $points = DB::table('hairdresser')
            ->select('id', 'Hairdresser_Name', 'Total_Points')
            ->orderByDesc('Total_Points')
            ->get();

        // Combined rows for the exportable report: hairdresser, activity, points, card, expiration
        $rows = DB::table('hairdresser as h')
            ->leftJoin('hairdresser_cards as hc', 'hc.hairdresser_id', '=', 'h.id')
            ->leftJoin('cards as c', 'hc.card_id', '=', 'c.id')
            ->leftJoin('activity as a', 'h.Type_of_Activity', '=', 'a.id')
            ->select(
                'h.id',
                'h.Hairdresser_Name',
                'a.Activity_Name',
                'h.Total_Points',
                'c.Card_Name',
                'hc.Expiration_Date'
            )
            ->orderBy('h.Hairdresser_Name')
            ->get();

        // Summary counts
        $now = DB::raw('CURRENT_DATE()');
        $expired = DB::table('hairdresser_cards')
            ->where('Expiration_Date', '<', DB::raw('CURRENT_DATE()'))
            ->count();

        // Near-expiring: within next 30 days
        $nearExpiring = DB::table('hairdresser_cards')
            ->whereBetween('Expiration_Date', [DB::raw('CURRENT_DATE()'), DB::raw("DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)")])
            ->count();

        return view('reports', [
            'expiring' => $expiring,
            'points' => $points,
            'rows' => $rows,
            'expired_count' => $expired,
            'near_expiring_count' => $nearExpiring,
        ]);
    }

    public function destroy(int $id): RedirectResponse
    {
        DB::transaction(function () use ($id) {
            DB::table('sales')->where('Hairdresser_Id', $id)->delete();
            DB::table('hairdresser')->where('id', $id)->delete();
        });

        return back()->with('success', 'تم حذف السجل بنجاح');
    }
}
