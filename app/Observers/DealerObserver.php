<?php

namespace App\Observers;

use App\Models\Dealer;

class DealerObserver
{
    /**
     * Handle the Dealer "created" event.
     */
    public function created(Dealer $dealer): void
    {
        $dealer->dealer_unique_id = 'DLR' . (10000 + $dealer->id);
        $dealer->saveQuietly();
    }

    /**
     * Handle the Dealer "updated" event.
     */
    public function updated(Dealer $dealer): void
    {
        //
    }

    /**
     * Handle the Dealer "deleted" event.
     */
    public function deleted(Dealer $dealer): void
    {
        //
    }

    /**
     * Handle the Dealer "restored" event.
     */
    public function restored(Dealer $dealer): void
    {
        //
    }

    /**
     * Handle the Dealer "force deleted" event.
     */
    public function forceDeleted(Dealer $dealer): void
    {
        //
    }
}
