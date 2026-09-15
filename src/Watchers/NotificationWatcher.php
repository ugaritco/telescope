<?php

namespace Ugarit\Telescope\Watchers;

use Heritage\Contracts\Queue\ShouldQueue;
use Heritage\Database\Eloquent\Model;
use Heritage\Notifications\AnonymousNotifiable;
use Heritage\Notifications\Events\NotificationSent;
use Ugarit\Telescope\ExtractTags;
use Ugarit\Telescope\FormatModel;
use Ugarit\Telescope\IncomingEntry;
use Ugarit\Telescope\Telescope;

class NotificationWatcher extends Watcher
{
    /**
     * Register the watcher.
     *
     * @param  \Heritage\Contracts\Foundation\Application  $app
     * @return void
     */
    public function register($app)
    {
        $app['events']->listen(NotificationSent::class, [$this, 'recordNotification']);
    }

    /**
     * Record a new notification message was sent.
     *
     * @param  \Heritage\Notifications\Events\NotificationSent  $event
     * @return void
     */
    public function recordNotification(NotificationSent $event)
    {
        if (! Telescope::isRecording()) {
            return;
        }

        Telescope::recordNotification(IncomingEntry::make([
            'notification' => get_class($event->notification),
            'queued' => in_array(ShouldQueue::class, class_implements($event->notification)),
            'notifiable' => $this->formatNotifiable($event->notifiable),
            'channel' => $event->channel,
            'response' => $event->response,
        ])->tags($this->tags($event)));
    }

    /**
     * Extract the tags for the given event.
     *
     * @param  \Heritage\Notifications\Events\NotificationSent  $event
     * @return array
     */
    private function tags($event)
    {
        return array_merge([
            $this->formatNotifiable($event->notifiable),
        ], ExtractTags::from($event->notification));
    }

    /**
     * Format the given notifiable into a tag.
     *
     * @param  mixed  $notifiable
     * @return string
     */
    private function formatNotifiable($notifiable)
    {
        if ($notifiable instanceof Model) {
            return FormatModel::given($notifiable);
        } elseif ($notifiable instanceof AnonymousNotifiable) {
            $routes = array_map(function ($route) {
                return is_array($route) ? implode(',', $route) : $route;
            }, $notifiable->routes);

            return 'Anonymous:'.implode(',', $routes);
        }

        return get_class($notifiable);
    }
}
