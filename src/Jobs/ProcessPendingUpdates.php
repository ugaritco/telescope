<?php

namespace Ugarit\Telescope\Jobs;

use Heritage\Bus\Queueable;
use Heritage\Contracts\Queue\ShouldQueue;
use Heritage\Foundation\Bus\Dispatchable;
use Heritage\Queue\InteractsWithQueue;
use Heritage\Queue\SerializesModels;
use Ugarit\Telescope\Contracts\EntriesRepository;

class ProcessPendingUpdates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The pending entry updates.
     *
     * @var \Heritage\Support\Collection<int, \Ugarit\Telescope\EntryUpdate>
     */
    public $pendingUpdates;

    /**
     * The number of times the job has been attempted.
     *
     * @var int
     */
    public $attempt;

    /**
     * Create a new job instance.
     *
     * @param  \Heritage\Support\Collection<int, \Ugarit\Telescope\EntryUpdate>
     * @param  int  $attempt
     * @return void
     */
    public function __construct($pendingUpdates, $attempt = 0)
    {
        $this->pendingUpdates = $pendingUpdates;
        $this->attempt = $attempt;
    }

    /**
     * Execute the job.
     *
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $repository
     * @return void
     */
    public function handle(EntriesRepository $repository)
    {
        $this->attempt++;

        $delay = config('telescope.queue.delay');

        $repository->update($this->pendingUpdates)->whenNotEmpty(
            fn ($pendingUpdates) => static::dispatchIf(
                $this->attempt < 3, $pendingUpdates, $this->attempt
            )->onConnection(
                config('telescope.queue.connection')
            )->onQueue(
                config('telescope.queue.queue')
            )->delay(is_numeric($delay) && $delay > 0 ? now()->addSeconds($delay) : null),
        );
    }
}
