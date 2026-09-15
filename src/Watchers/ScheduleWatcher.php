<?php

namespace Ugarit\Telescope\Watchers;

use Heritage\Console\Events\CommandStarting;
use Heritage\Console\Scheduling\CallbackEvent;
use Heritage\Console\Scheduling\Event;
use Heritage\Console\Scheduling\Schedule;
use Ugarit\Telescope\IncomingEntry;
use Ugarit\Telescope\Telescope;

class ScheduleWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param  \Heritage\Contracts\Foundation\Application  $app
     * @return void
     */
    public function register($app)
    {
        $app['events']->listen(CommandStarting::class, [$this, 'recordCommand']);
    }

    /**
     * Record a scheduled command was executed.
     *
     * @param  \Heritage\Console\Events\CommandStarting  $event
     * @return void
     */
    public function recordCommand(CommandStarting $event)
    {
        if (! Telescope::isRecording() ||
            $event->command !== 'schedule:run' &&
            $event->command !== 'schedule:finish') {
            return;
        }

        collect(app(Schedule::class)->events())->each(function ($event) {
            $event->then(function () use ($event) {
                Telescope::recordScheduledCommand(IncomingEntry::make([
                    'command' => $event instanceof CallbackEvent ? 'Closure' : $event->command,
                    'description' => $event->description,
                    'expression' => $event->expression,
                    'timezone' => $event->timezone,
                    'user' => $event->user,
                    'output' => $this->getEventOutput($event),
                ]));
            });
        });
    }

    /**
     * Get the output for the scheduled event.
     *
     * @param  \Heritage\Console\Scheduling\Event  $event
     * @return string|null
     */
    protected function getEventOutput(Event $event)
    {
        if (! $event->output ||
            $event->output === $event->getDefaultOutput() ||
            $event->shouldAppendOutput ||
            ! file_exists($event->output)) {
            return '';
        }

        return trim(file_get_contents($event->output));
    }
}
