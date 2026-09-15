<?php

namespace Ugarit\Telescope\Watchers;

use Heritage\Bus\Events\BatchDispatched;
use Ugarit\Telescope\IncomingEntry;
use Ugarit\Telescope\Telescope;

class BatchWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param  \Heritage\Contracts\Foundation\Application  $app
     * @return void
     */
    public function register($app)
    {
        $app['events']->listen(BatchDispatched::class, [$this, 'recordBatch']);
    }

    /**
     * Record a batch being dispatched.
     *
     * @param  \Heritage\Bus\Events\BatchDispatched  $event
     * @return \Ugarit\Telescope\IncomingEntry|null
     */
    public function recordBatch(BatchDispatched $event)
    {
        if (! Telescope::isRecording()) {
            return;
        }

        $content = array_merge($event->batch->toArray(), [
            'queue' => $event->batch->options['queue'] ?? 'default',
            'connection' => $event->batch->options['connection'] ?? 'default',
            'allowsFailures' => $event->batch->allowsFailures(),
        ]);

        Telescope::recordBatch(
            $entry = IncomingEntry::make(
                $content,
                $event->batch->id
            )->withFamilyHash($event->batch->id)
        );

        return $entry;
    }
}
