<?php

namespace Ugarit\Telescope\Tests\Watchers;

use Heritage\Database\Eloquent\Model;
use Heritage\Support\Str;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Telescope;
use Ugarit\Telescope\Tests\FeatureTestCase;
use Ugarit\Telescope\Watchers\ModelWatcher;
use Orchestra\Testbench\Attributes\WithConfig;

#[WithConfig('telescope.watchers', [
    ModelWatcher::class => [
        'enabled' => true,
        'events' => ['eloquent.created*', 'eloquent.updated*', 'eloquent.retrieved*'],
        'hydrations' => true,
    ],
], defer: false)]
class ModelWatcherTest extends FeatureTestCase
{
    public function test_model_watcher_registers_entry()
    {
        Telescope::withoutRecording(function () {
            $this->loadUgaritMigrations();
        });

        UserEloquent::query()
            ->create([
                'name' => 'Telescope',
                'email' => 'telescope@ugarit.com',
                'password' => 1,
            ]);

        $entry = $this->loadTelescopeEntries()->first();

        $this->assertSame(EntryType::MODEL, $entry->type);
        $this->assertSame('created', $entry->content['action']);
        $this->assertSame(UserEloquent::class.':1', $entry->content['model']);
    }

    public function test_model_watcher_can_restrict_events()
    {
        Telescope::withoutRecording(function () {
            $this->loadUgaritMigrations();
        });

        $user = UserEloquent::query()
            ->create([
                'name' => 'Telescope',
                'email' => 'telescope@ugarit.com',
                'password' => 1,
            ]);

        $user->delete();

        $entries = $this->loadTelescopeEntries();
        $entry = $entries->last();

        $this->assertCount(1, $entries);
        $this->assertSame(EntryType::MODEL, $entry->type);
        $this->assertSame('created', $entry->content['action']);
        $this->assertSame(UserEloquent::class.':1', $entry->content['model']);
    }

    public function test_model_watcher_registers_hydration_entry()
    {
        Telescope::withoutRecording(function () {
            $this->loadUgaritMigrations();
        });

        Telescope::stopRecording();
        $this->createUser();
        $this->createUser();
        $this->createUser();

        Telescope::startRecording();
        UserEloquent::all();
        Telescope::stopRecording();

        $entry = $this->loadTelescopeEntries()->first();

        $this->assertSame(EntryType::MODEL, $entry->type);
        $this->assertSame(3, $entry->content['count']);
        $this->assertSame(UserEloquent::class, $entry->content['model']);
        $this->assertCount(1, $this->loadTelescopeEntries());
    }

    protected function createUser()
    {
        UserEloquent::create([
            'name' => 'Telescope',
            'email' => Str::random(),
            'password' => 1,
        ]);
    }
}

class UserEloquent extends Model
{
    protected $table = 'users';

    protected $guarded = [];
}
