<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountDeletionRefund;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * The queue of wallet balances left behind by deleted accounts.
 *
 * Deleting an account is never blocked by a balance, because a customer cannot
 * withdraw one themselves - they can only spend it on a featured listing, and
 * the only refund path is admin-side. The money is recorded here instead of
 * being quietly absorbed, and this screen is where it gets returned.
 *
 * Without this screen the rows would accumulate unseen, which would make the
 * promise on sahigadi.com/account-deletion untrue.
 */
class AccountDeletionRefundController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'pending');

        $query = AccountDeletionRefund::query()
            ->when(in_array($status, ['pending', 'refunded', 'cancelled'], true),
                fn ($q) => $q->where('status', $status))
            ->orderByRaw("FIELD(status, 'pending', 'refunded', 'cancelled')")
            ->orderBy('requested_at');

        $refunds = $query->paginate(25)->withQueryString();

        $totals = AccountDeletionRefund::query()
            ->selectRaw("
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
                COALESCE(SUM(CASE WHEN status = 'pending' THEN balance END), 0) as pending_total,
                COUNT(CASE WHEN status = 'refunded' THEN 1 END) as refunded_count,
                COALESCE(SUM(CASE WHEN status = 'refunded' THEN balance END), 0) as refunded_total
            ")
            ->first();

        return Inertia::render('Admin/AccountDeletionRefunds/Index', [
            // Mapped explicitly rather than serialised: contact_phone is in the
            // model's $hidden, and it is needed here and only here.
            'refunds' => $refunds->through(fn (AccountDeletionRefund $refund) => [
                'id' => $refund->id,
                'customer_id' => $refund->customer_id,
                'contact_name' => $refund->contact_name,
                'contact_phone' => $refund->contact_phone,
                'balance' => (float) $refund->balance,
                'status' => $refund->status,
                'requested_at' => optional($refund->requested_at)->format('d M Y'),
                'refunded_at' => optional($refund->refunded_at)->format('d M Y'),
                'reference' => $refund->reference,
                'notes' => $refund->notes,
                'is_pending' => $refund->status === AccountDeletionRefund::STATUS_PENDING,
                'markRefunded' => route('admin.account-deletion-refunds.refunded', $refund->id),
            ]),
            'filters' => ['status' => $status],
            'totals' => [
                'pendingCount' => (int) ($totals->pending_count ?? 0),
                'pendingTotal' => (float) ($totals->pending_total ?? 0),
                'refundedCount' => (int) ($totals->refunded_count ?? 0),
                'refundedTotal' => (float) ($totals->refunded_total ?? 0),
            ],
        ]);
    }

    /**
     * Record that the money has been returned.
     *
     * This does not move any money - the refund happens in your payment
     * gateway or bank. Marking it here closes the record and drops the contact
     * details, which were only ever kept in order to make this payment.
     */
    public function markRefunded(Request $request, AccountDeletionRefund $refund)
    {
        if ($refund->status !== AccountDeletionRefund::STATUS_PENDING) {
            return back()->with('error', 'That refund is not pending.');
        }

        $validated = $request->validate([
            'reference' => 'nullable|string|max:255',
        ]);

        $refund->markRefunded($validated['reference'] ?? null);

        return back()->with(
            'success',
            'Refund recorded. The stored contact details for this customer have been removed.'
        );
    }
}
