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
        $query = DB::table('hairdresser');

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

        $hairdressers = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $activities = DB::table('activity')->select('id', 'Activity_Name')->orderBy('Activity_Name')->get();
        $cards = DB::table('cards')->select('id', 'Card_Name')->orderBy('Card_Name')->get();

        return view('control', [
            'items' => $hairdressers,
            'activities' => $activities,
            'cards' => $cards,
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
