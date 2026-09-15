<?php

namespace Ugarit\Telescope\Tests\Watchers;

use Heritage\Bus\Queueable;
use Heritage\Notifications\Messages\MailMessage;
use Heritage\Notifications\Notification as BaseNotification;
use Heritage\Support\Facades\Notification;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Tests\FeatureTestCase;
use Ugarit\Telescope\Watchers\MailWatcher;
use Ugarit\Telescope\Watchers\NotificationWatcher;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('mail.driver', 'array')]
#[WithConfig('telescope.watchers', [
    MailWatcher::class => true,
    NotificationWatcher::class => true,
])]
class MailNotificationTest extends FeatureTestCase
{
    public function test_mail_watcher_registers_valid_html()
    {
        Notification::route('mail', 'to@ugarit.com')
                    ->notify(new TestMailNotification());

        $entry = $this->loadTelescopeEntries()->firstWhere('type', EntryType::MAIL);

        $this->assertSame(EntryType::MAIL, $entry->type);
        $this->assertStringStartsWith('<!DOCTYPE html', $entry->content['html']);
    }
}

class TestMailNotification extends BaseNotification
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage())
            ->subject('Check out this awesome HTML and raw email!')
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }
}
