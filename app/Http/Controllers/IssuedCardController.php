<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class IssuedCardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $qb = DB::table('issued_cards')
            ->leftJoin('hairdresser as h', 'issued_cards.hairdresser_id', '=', 'h.id')
            ->leftJoin('users as u', 'issued_cards.issued_by', '=', 'u.id')
            ->leftJoin('cards as c', 'issued_cards.card_id', '=', 'c.id')
            ->select('issued_cards.*', 'h.Hairdresser_Name', 'u.name as issuer_name', 'c.Card_Name as card_name');

        $statusFilter = request('status');
        if ($statusFilter === 'revoked') {
            $qb->where('issued_cards.is_revoked', 1);
        } elseif ($statusFilter === 'active') {
            $qb->where('issued_cards.is_revoked', 0);
        }

        // filters: q = hairdresser name, card = card id
        if ($q = request('q')) {
            $qb->where('h.Hairdresser_Name', 'like', "%{$q}%");
        }

        if ($card = request('card')) {
            $qb->where('issued_cards.card_id', (int)$card);
        }

        $cards = $qb->orderByDesc('issued_cards.issued_at')->paginate(20)->appends(request()->only('status', 'q', 'card'));

        $cardTypes = DB::table('cards')->select('id', 'Card_Name')->orderBy('Card_Name')->get();

        return view('issued_cards.index', [
            'cards' => $cards,
            'filter_status' => request('status'),
            'cardTypes' => $cardTypes,
        ]);
    }

    public function create()
    {
        // load hairdressers with extra fields used in the create view
        $hairdressers = DB::table('hairdresser')
            ->select('id', 'Hairdresser_Name', 'Hairdresser_Owner', 'Call_Num', 'Type_of_Activity')
            ->orderBy('Hairdresser_Name')
            ->get();

        $cardTypes = DB::table('cards')->select('id', 'Card_Name')->orderBy('Card_Name')->get();

        // load activities for the activity filter/dropdown
        $activities = DB::table('activity')->select('id', 'Activity_Name')->orderBy('Activity_Name')->get();

        return view('issued_cards.create', [
            'hairdressers' => $hairdressers,
            'cardTypes' => $cardTypes,
            'activities' => $activities
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'hairdresser_ids' => 'required|array|min:1',
            'card_id' => 'required|integer',
        ]);

        $ids = $request->input('hairdresser_ids');
        $cardId = (int)$request->input('card_id');

        $created = [];
        $failed = [];

        foreach ($ids as $hid) {
            $hid = (int)$hid;
            try {
                // Attempt to call stored procedure if present; if it fails (e.g. DB proc still references removed columns)
                // fall back to the PHP generator instead of failing the whole issuance.
                $existsProc = DB::select("SELECT ROUTINE_NAME FROM information_schema.routines WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = 'issue_card'");
                if (!empty($existsProc)) {
                    $public = null;
                    // ensure connection collation matches procedure/table collation to avoid collation-mix errors
                    try {
                        DB::statement("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                        DB::statement("SET collation_connection = 'utf8mb4_unicode_ci'");
                    } catch (\Exception $e) {
                        // ignore if cannot set; we'll still try the CALL and catch any error
                    }
                    try {
                        // pass NULL for owner to avoid sending owner_name from PHP (column removed from issued_cards)
                        DB::statement('CALL issue_card(?,?,?,@out_public_code)', [$hid, $cardId, null]);
                        $res = DB::select('SELECT @out_public_code as public_code');
                        $public = $res[0]->public_code ?? null;
                        // Update issued_by if column exists (defensive: log if update did not affect rows)
                        if ($public) {
                            try {
                                $uid = Auth::id();
                                if ($uid) {
                                    $updated = DB::table('issued_cards')->where('public_code', $public)->update(['issued_by' => $uid]);
                                    if ($updated === 0) {
                                        Log::warning('issue_card: unable to set issued_by for public_code ' . $public . ' (no rows updated)');
                                    }
                                }
                            } catch (\Exception $ex) {
                                Log::warning('issue_card: failed to set issued_by for ' . $public . ': ' . $ex->getMessage());
                            }
                        }
                        if ($public) {
                            $created[] = $public;
                            continue; // success using proc
                        }
                        // if proc didn't return a code, fall through to PHP fallback
                        Log::warning('issue_card procedure returned no public_code; falling back to PHP generation for hairdresser ' . $hid);
                    } catch (\Exception $e) {
                        // Stored procedure failed (likely references removed column); log and fall back
                        Log::warning('issue_card procedure call failed for hairdresser ' . $hid . ': ' . $e->getMessage() . ' — falling back to PHP generator');
                    }
                }

                // Fallback: generate secure code in PHP
                $attempts = 0;
                do {
                    $public_code = 'HD-' . str_pad($hid, 3, '0', STR_PAD_LEFT) . '-' . strtoupper(Str::random(6));
                    $exists = DB::table('issued_cards')->where('public_code', $public_code)->exists();
                    $attempts++;
                    if ($attempts > 10) throw new \Exception('Unable to generate unique public code');
                } while ($exists);

                $salt = strtoupper(bin2hex(random_bytes(8)));
                $secret = $public_code . ':' . $salt . ':' . time();
                $code_hash = strtoupper(hash('sha512', $secret));

                DB::table('issued_cards')->insert([
                    'hairdresser_id' => $hid,
                    'card_id' => $cardId,
                    'public_code' => $public_code,
                    'code_hash' => $code_hash,
                    'salt' => $salt,
                    'expires_at' => DB::raw("DATE_ADD(NOW(), INTERVAL 1 YEAR)"),
                    'issued_by' => Auth::id(),
                    'issued_at' => now(),
                ]);

                $created[] = $public_code;
            } catch (\Exception $e) {
                Log::error('Issue card failed for ' . $hid . ': ' . $e->getMessage());
                $failed[] = ['hairdresser_id' => $hid, 'error' => $e->getMessage()];
            }
        }

        $flash = [];
        if (!empty($created)) $flash['created_codes'] = $created;
        if (!empty($failed)) $flash['issue_errors'] = $failed;

        return back()->with($flash);
    }

    public function verifyForm()
    {
        return view('issued_cards.verify');
    }

    public function verify(Request $request)
    {
        $request->validate(['public_code' => 'required|string']);
        $public = trim($request->input('public_code'));

        // Do a collation-safe lookup first so we can give better diagnostics.
        $row = DB::table('issued_cards')
            ->whereRaw("public_code COLLATE utf8mb4_unicode_ci = ?", [$public])
            ->first();

        if (!$row) {
            return back()->withErrors(['public_code' => 'غير موجود — تحقق من المسافات أو الأحرف']);
        }

        if (!empty($row->is_revoked) && $row->is_revoked) {
            return back()->withErrors(['public_code' => 'البطاقة ملغاة']);
        }

        // Try stored procedures (prefer one that accepts an issue timestamp)
        $hasVerifyTs = DB::select("SELECT ROUTINE_NAME FROM information_schema.routines WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME='verify_card_code'");
        if (!empty($hasVerifyTs)) {
            $ts = $row->issued_at ? strtotime($row->issued_at) : time();
            try {
                DB::statement("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                DB::statement("SET collation_connection = 'utf8mb4_unicode_ci'");
            } catch (\Exception $e) {
            }
            try {
                DB::statement('CALL verify_card_code(?,?,@out_valid)', [$public, $ts]);
                $res = DB::select('SELECT @out_valid as is_valid');
                if (!empty($res) && $res[0]->is_valid == 1) {
                    // Load related human-friendly names for the view
                    $rowWithNames = DB::table('issued_cards as ic')
                        ->leftJoin('hairdresser as h', 'ic.hairdresser_id', '=', 'h.id')
                        ->leftJoin('cards as c', 'ic.card_id', '=', 'c.id')
                        ->where('ic.id', $row->id)
                        ->select('ic.*', 'h.Hairdresser_Name as hairdresser_name', 'h.Hairdresser_Owner as owner_name', 'c.Card_Name as card_name')
                        ->first();
                    return view('issued_cards.verified', ['row' => $rowWithNames ?? $row]);
                }
            } catch (\Exception $e) {
                // Stored proc failed; we'll fallback to PHP verification below
                Log::warning('verify_card_code procedure call failed: ' . $e->getMessage());
            }
        }

        // Try simpler stored procedure that uses issue time directly
        $hasVerifySimple = DB::select("SELECT ROUTINE_NAME FROM information_schema.routines WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME='verify_card_code_by_issue_time'");
        if (!empty($hasVerifySimple)) {
            try {
                DB::statement("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                DB::statement("SET collation_connection = 'utf8mb4_unicode_ci'");
            } catch (\Exception $e) {
            }
            try {
                DB::statement('CALL verify_card_code_by_issue_time(?,@out_valid)', [$public]);
                $res = DB::select('SELECT @out_valid as is_valid');
                if (!empty($res) && $res[0]->is_valid == 1) {
                    // Enrich with names
                    $rowWithNames = DB::table('issued_cards as ic')
                        ->leftJoin('hairdresser as h', 'ic.hairdresser_id', '=', 'h.id')
                        ->leftJoin('cards as c', 'ic.card_id', '=', 'c.id')
                        ->where('ic.id', $row->id)
                        ->select('ic.*', 'h.Hairdresser_Name as hairdresser_name', 'h.Hairdresser_Owner as owner_name', 'c.Card_Name as card_name')
                        ->first();
                    return view('issued_cards.verified', ['row' => $rowWithNames ?? $row]);
                }
            } catch (\Exception $e) {
                Log::warning('verify_card_code_by_issue_time procedure call failed: ' . $e->getMessage());
            }
        }

        // Fallback: pure PHP recompute using the stored issued_at timestamp
        $timestamp = $row->issued_at ? strtotime($row->issued_at) : null;
        if ($timestamp === null) {
            return back()->withErrors(['public_code' => 'تعذر تحديد وقت الإصدار؛ لا يمكن التحقق محلياً']);
        }
        $hash = strtoupper(hash('sha512', $public . ':' . $row->salt . ':' . $timestamp));
        if ($hash === $row->code_hash) {
            $rowWithNames = DB::table('issued_cards as ic')
                ->leftJoin('hairdresser as h', 'ic.hairdresser_id', '=', 'h.id')
                ->leftJoin('cards as c', 'ic.card_id', '=', 'c.id')
                ->where('ic.id', $row->id)
                ->select('ic.*', 'h.Hairdresser_Name as hairdresser_name', 'h.Hairdresser_Owner as owner_name', 'c.Card_Name as card_name')
                ->first();
            return view('issued_cards.verified', ['row' => $rowWithNames ?? $row]);
        }

        // If we reach here nothing matched
        return back()->withErrors(['public_code' => 'رمز غير صالح أو ملغى (فشل التحقق)']);
    }

    public function revoke(Request $request, $public_code)
    {
        $request->validate([
            'reason' => 'required|string|min:3'
        ]);

        $actor = Auth::id();

        // Try stored procedure revoke_card if exists
        $proc = DB::select("SELECT ROUTINE_NAME FROM information_schema.routines WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_NAME = 'revoke_card'");
        if (!empty($proc)) {
            try {
                DB::statement("SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
                DB::statement("SET collation_connection = 'utf8mb4_unicode_ci'");
            } catch (\Exception $e) {
            }
            DB::statement('CALL revoke_card(?,?,?,@out_result)', [$public_code, $actor, $request->input('reason')]);
            $res = DB::select('SELECT @out_result as result');
            if (!empty($res) && $res[0]->result == 1) {
                return back()->with('status_msg', 'تم إلغاء البطاقة');
            }
            return back()->withErrors(['revoke' => 'تعذر إلغاء البطاقة']);
        }

        // Fallback manual revoke
        $row = DB::table('issued_cards')->where('public_code', $public_code)->first();
        if (!$row) return back()->withErrors(['revoke' => 'غير موجود']);
        if ($row->is_revoked) return back()->with('status_msg', 'البطاقة ملغاة مسبقاً');

        DB::transaction(function () use ($row, $request, $actor) {
            DB::table('issued_cards')->where('id', $row->id)->update([
                'is_revoked' => 1,
                'revoked_reason' => $request->input('reason'),
                'status' => 0,
                'updated_at' => now()
            ]);
            // Audit if table exists
            try {
                DB::table('card_audit')->insert([
                    'issued_card_id' => $row->id,
                    'action' => 'revoke',
                    'actor_id' => $actor,
                    'notes' => $request->input('reason')
                ]);
            } catch (\Exception $e) {
                // ignore if audit table missing
            }
        });

        return back()->with('status_msg', 'تم إلغاء البطاقة');
    }

    public function audit($public_code)
    {
        $card = DB::table('issued_cards')->where('public_code', $public_code)->first();
        if (!$card) abort(404);
        $audit = [];
        try {
            $audit = DB::table('card_audit')
                ->leftJoin('users as u', 'card_audit.actor_id', '=', 'u.id')
                ->select('card_audit.*', 'u.name as actor_name')
                ->where('issued_card_id', $card->id)
                ->orderByDesc('card_audit.created_at')
                ->get();
        } catch (\Exception $e) {
        }
        return view('issued_cards.audit', compact('card', 'audit'));
    }

    /**
     * Undo the last reversible audit action for the card (e.g., un-revoke a revoked card).
     * Requires a POST with confirm_password (the existing confirm-password flow should be used client-side).
     */
    public function undoAudit(Request $request, $public_code)
    {
        $request->validate([
            'confirm_password' => ['required', 'string']
        ]);

        $user = Auth::user();
        if (! $user) return back()->withErrors(['undo' => 'غير مصرح']);

        if (! \Illuminate\Support\Facades\Hash::check($request->input('confirm_password'), $user->password)) {
            return back()->withErrors(['undo' => 'كلمة المرور غير صحيحة']);
        }

        $card = DB::table('issued_cards')->where('public_code', $public_code)->first();
        if (! $card) return back()->withErrors(['undo' => 'البطاقة غير موجودة']);

        // Find last audit entry for this card
        $last = null;
        try {
            $last = DB::table('card_audit')->where('issued_card_id', $card->id)->orderByDesc('created_at')->first();
        } catch (\Exception $e) {
        }

        if (! $last) return back()->withErrors(['undo' => 'لا يوجد إجراء يمكن التراجع عنه']);

        // Only allow undo for specific actions (e.g., revoke)
        if ($last->action === 'revoke') {
            DB::transaction(function () use ($card, $user, $last) {
                DB::table('issued_cards')->where('id', $card->id)->update([
                    'is_revoked' => 0,
                    'revoked_reason' => null,
                    'status' => 1,
                    'updated_at' => now()
                ]);
                // record audit
                try {
                    DB::table('card_audit')->insert([
                        'issued_card_id' => $card->id,
                        'action' => 'undo_revoke',
                        'actor_id' => $user->id,
                        'notes' => 'تراجع عن إلغاء البطاقة (استرجاع) — رجع: ' . ($last->actor_name ?? $last->actor_id ?? 'N/A')
                    ]);
                } catch (\Exception $e) {
                }
            });
            return back()->with('status_msg', 'تم التراجع عن الإلغاء');
        }

        return back()->withErrors(['undo' => 'النوع الأخير من الإجراءات غير قابل للتراجع']);
    }
}
