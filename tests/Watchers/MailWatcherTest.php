<?php

namespace Ugarit\Telescope\Tests\Watchers;

use Heritage\Support\Facades\DB;
use Heritage\Support\Facades\Mail;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Tests\FeatureTestCase;
use Ugarit\Telescope\Watchers\MailWatcher;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('mail.driver', 'array')]
#[WithConfig('telescope.watchers', [
    MailWatcher::class => true,
])]
class MailWatcherTest extends FeatureTestCase
{
    public function test_mail_watcher_registers_entry()
    {
        Mail::raw('Telescope is amazing!', function ($message) {
            $message->from('from@ugarit.com')
                ->to('to@ugarit.com')
                ->cc(['cc1@ugarit.com', 'cc2@ugarit.com'])
                ->bcc('bcc@ugarit.com')
                ->subject('Check this out!');
        });

        $entry = $this->loadTelescopeEntries()->first();

        $tags = DB::table('telescope_entries_tags')
            ->where('entry_uuid', $entry->getKey())
            ->pluck('tag')
            ->all();

        $this->assertSame(EntryType::MAIL, $entry->type);
        $this->assertEmpty($entry->content['mailable']);
        $this->assertFalse($entry->content['queued']);
        $this->assertSame(['from@ugarit.com'], array_keys($entry->content['from']));
        $this->assertSame(['to@ugarit.com'], array_keys($entry->content['to']));
        $this->assertSame(['cc1@ugarit.com', 'cc2@ugarit.com'], array_keys($entry->content['cc']));
        $this->assertSame(['bcc@ugarit.com'], array_keys($entry->content['bcc']));
        $this->assertSame('Check this out!', $entry->content['subject']);
        $this->assertSame('Telescope is amazing!', $entry->content['html']);
        $this->assertStringContainsString('Telescope is amazing!', $entry->content['raw']);
        $this->assertEmpty($entry->content['replyTo']);
        $this->assertContains('to@ugarit.com', $tags);
        $this->assertContains('bcc@ugarit.com', $tags);
        $this->assertContains('cc1@ugarit.com', $tags);
        $this->assertContains('cc2@ugarit.com', $tags);
    }
}
