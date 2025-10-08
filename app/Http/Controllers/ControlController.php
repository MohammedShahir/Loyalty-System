<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\View\View;

class ControlController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        // Note: legacy `Type_of_Card` usage removed. Card assignments are maintained in `hairdresser_cards` and
        // via the Issued Cards flow.

        // include latest non-revoked issued card (if any) so search shows issued card and dates
        $lastIssued = DB::table('issued_cards')
            ->select(DB::raw('MAX(id) as last_id'), 'hairdresser_id')
            ->where('is_revoked', 0)
            ->groupBy('hairdresser_id');

        $query = DB::table('hairdresser as h')
            ->leftJoinSub($lastIssued, 'last_ic', function ($join) {
                $join->on('last_ic.hairdresser_id', '=', 'h.id');
            })
            ->leftJoin('issued_cards as ic', 'ic.id', '=', 'last_ic.last_id')
            ->leftJoin('cards as c', 'ic.card_id', '=', 'c.id')
            ->select('h.*', 'c.Card_Name as Card_Name', 'ic.issued_at as Card_Issued_At');

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
            // filter by assigned card (hairdresser_cards.card_id) or issued card
            $query->where(function ($q2) use ($card) {
                $q2->where('hc.card_id', (int) $card)
                    ->orWhere('ic.card_id', (int) $card);
            });
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

        // Combined rows for the exportable report: hairdresser, activity, points, card from issued_cards (latest non-revoked), issue date
        // Build a subquery that finds the latest non-revoked issued_cards.id per hairdresser
        $lastIssued = DB::table('issued_cards')
            ->select(DB::raw('MAX(id) as last_id'), 'hairdresser_id')
            ->where('is_revoked', 0)
            ->groupBy('hairdresser_id');

        $rows = DB::table('hairdresser as h')
            ->leftJoinSub($lastIssued, 'last_ic', function ($join) {
                $join->on('last_ic.hairdresser_id', '=', 'h.id');
            })
            ->leftJoin('issued_cards as ic', 'ic.id', '=', 'last_ic.last_id')
            ->leftJoin('cards as c', 'ic.card_id', '=', 'c.id')
            ->leftJoin('activity as a', 'h.Type_of_Activity', '=', 'a.id')
            ->select(
                'h.id',
                'h.Hairdresser_Name',
                'a.Activity_Name',
                'h.Total_Points',
                'c.Card_Name',
                'ic.issued_at as Card_Issued_At'
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

        // invoices for selected hairdresser or by invoice search
        $hairdressersList = DB::table('hairdresser')->select('id', 'Hairdresser_Name')->orderBy('Hairdresser_Name')->get();

        $invoicesQuery = DB::table('sales as s')
            ->leftJoin('hairdresser as h', 's.Hairdresser_Id', '=', 'h.id')
            ->select('s.*', 'h.Hairdresser_Name')
            ->orderByDesc('s.Sale_Date');

        if ($hid = $request->input('hairdresser_id')) {
            $invoicesQuery->where('s.Hairdresser_Id', (int)$hid);
        }

        if ($inv = $request->input('invoice')) {
            $invoicesQuery->where('s.Invoice_Num', (int)$inv);
        }

        $invoices = $invoicesQuery->get();

        return view('reports', [
            'expiring' => $expiring,
            'points' => $points,
            'rows' => $rows,
            'expired_count' => $expired,
            'near_expiring_count' => $nearExpiring,
            'hairdressersList' => $hairdressersList,
            'invoices' => $invoices,
        ]);
    }

    /**
     * Server-side PDF of the reports page using dompdf. Make sure barryvdh/laravel-dompdf is installed
     * and an Arabic font (e.g., Amiri-Regular.ttf) is placed in public/fonts and referenced in the view.
     */
    public function reportsPdf(Request $request)
    {
        $data = [
            'expiring' => DB::table('hairdresser_cards as hc')
                ->join('hairdresser as h', 'hc.hairdresser_id', '=', 'h.id')
                ->join('cards as c', 'hc.card_id', '=', 'c.id')
                ->select('h.Hairdresser_Name', 'c.Card_Name', 'hc.Expiration_Date', DB::raw("DATEDIFF(hc.Expiration_Date, CURRENT_DATE()) as Days_Until_Expiry"))
                ->orderBy('Days_Until_Expiry')
                ->get(),
            'rows' => DB::table('hairdresser as h')
                ->leftJoin('hairdresser_cards as hc', 'hc.hairdresser_id', '=', 'h.id')
                ->leftJoin('cards as c', 'hc.card_id', '=', 'c.id')
                ->leftJoin('activity as a', 'h.Type_of_Activity', '=', 'a.id')
                ->select('h.Hairdresser_Name', 'a.Activity_Name', 'h.Total_Points', 'c.Card_Name', 'hc.Release_Date', 'hc.Expiration_Date')
                ->orderBy('h.Hairdresser_Name')
                ->get(),
        ];

        // If the dompdf wrapper is bound (barryvdh/laravel-dompdf installed), use it to generate a PDF
        if (app()->bound('dompdf.wrapper')) {
            $pdf = app('dompdf.wrapper');
            $pdf->loadView('reports_pdf', $data);
            return $pdf->download('reports_ar.pdf');
        }

        // Fallback: return the HTML view so the user can save as PDF from the browser
        return view('reports_pdf', $data);
    }

    /**
     * Statistics dashboard
     */
    public function stats()
    {
        // Totals
        $totalHairdressers = DB::table('hairdresser')->count();
        // Count issued/personal cards instead of hairdresser_cards
        $totalCards = DB::table('issued_cards')->count();
        $totalSales = DB::table('sales')->count();
        $totalRevenue = DB::table('sales')->sum('Total_Sales');

        // Expiring counts
        $expired = DB::table('hairdresser_cards')->where('Expiration_Date', '<', DB::raw('CURRENT_DATE()'))->count();
        $nearExpiring = DB::table('hairdresser_cards')->whereBetween('Expiration_Date', [DB::raw('CURRENT_DATE()'), DB::raw("DATE_ADD(CURRENT_DATE(), INTERVAL 30 DAY)")])->count();

        // Top hairdressers by points
        $top = DB::table('hairdresser')->select('Hairdresser_Name', 'Total_Points')->orderByDesc('Total_Points')->limit(8)->get();

        // Monthly sales (last 12 months)
        $monthly = DB::table('sales')
            ->select(DB::raw("DATE_FORMAT(Sale_Date, '%Y-%m') as ym"), DB::raw('SUM(Total_Sales) as total'))
            ->where('Sale_Date', '>=', DB::raw("DATE_FORMAT(DATE_SUB(CURRENT_DATE(), INTERVAL 11 MONTH),'%Y-%m-01')"))
            ->groupBy('ym')
            ->orderBy('ym')
            ->get();

        return view('stats', [
            'totalHairdressers' => $totalHairdressers,
            'totalCards' => $totalCards,
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'expired' => $expired,
            'nearExpiring' => $nearExpiring,
            'top' => $top,
            'monthly' => $monthly,
        ]);
    }

    /**
     * AJAX endpoint: verify the current user's password. Returns JSON { ok: true }
     */
    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = Auth::user();
        if (! $user) {
            return response()->json(['ok' => false, 'message' => 'غير مصرح'], 403);
        }

        if (Hash::check($request->input('password'), $user->password)) {
            return response()->json(['ok' => true]);
        }

        return response()->json(['ok' => false, 'message' => 'كلمة المرور غير صحيحة'], 422);
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $password = $request->input('confirm_password');
        $user = Auth::user();
        if (! $user || ! Hash::check($password, $user->password)) {
            return back()->withErrors(['password' => 'كلمة المرور غير صحيحة أو مفقودة']);
        }

        DB::transaction(function () use ($id) {
            DB::table('sales')->where('Hairdresser_Id', $id)->delete();
            DB::table('hairdresser')->where('id', $id)->delete();
        });

        return back()->with('success', 'تم حذف السجل بنجاح');
    }
}
