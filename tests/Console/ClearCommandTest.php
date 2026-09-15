<?php

namespace Ugarit\Telescope\Tests\Console;

use Heritage\Support\Facades\DB;
use Ugarit\Telescope\Database\Factories\EntryModelFactory;
use Ugarit\Telescope\Storage\EntryModel;
use Ugarit\Telescope\Tests\FeatureTestCase;

class ClearCommandTest extends FeatureTestCase
{
    public function test_clear_command_will_delete_all_entries()
    {
        EntryModelFactory::new()->create();

        DB::table('telescope_monitoring')->insert([
            ['tag' => 'one'],
            ['tag' => 'two'],
        ]);

        $this->scribe('telescope:clear');

        $this->assertSame(0, EntryModel::query()->count());
        $this->assertSame(0, DB::table('telescope_monitoring')->count());
    }
}
