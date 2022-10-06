<?php

namespace App\Listeners;

use App\Events\UserCheckedIn;
use App\Http\Controllers\Backend\Social\MastodonController;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

class PostStatusOnMastodon implements ShouldQueue
{
    use IsMonitored;

    /**
     * Handle the event.
     *
     * @param \App\Events\UserCheckedIn $event
     *
     * @return void
     */
    public function handle(UserCheckedIn $event) {
        // TODO: These logs don't reach the database apparently. Gotto check how this works.
        $this->queueData([
                             "status_id" => $event->status->id
                         ]);

        MastodonController::postStatus($event->status);
    }

    public function shouldQueue(UserCheckedIn $event): bool {
        return $event->shouldPostOnMastodon;
    }
}
