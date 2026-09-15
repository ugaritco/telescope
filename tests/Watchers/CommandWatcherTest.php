<?php

namespace Ugarit\Telescope\Tests\Watchers;

use Heritage\Console\Command;
use Heritage\Contracts\Console\Kernel;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Tests\FeatureTestCase;
use Ugarit\Telescope\Watchers\CommandWatcher;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('telescope.watchers', [
    CommandWatcher::class => true,
])]
class CommandWatcherTest extends FeatureTestCase
{
    public function test_command_watcher_register_entry()
    {
        $this->app->get(Kernel::class)->registerCommand(new MyCommand);

        $this->scribe('telescope:test-command');

        $entry = $this->loadTelescopeEntries()->first();

        $this->assertSame(EntryType::COMMAND, $entry->type);
        $this->assertSame('telescope:test-command', $entry->content['command']);
        $this->assertSame(0, $entry->content['exit_code']);
    }
}

class MyCommand extends Command
{
    protected $signature = 'telescope:test-command';

    public function handle()
    {
        //
    }
}
