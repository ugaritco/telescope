<?php

namespace Ugarit\Telescope\Tests\Console;

use Heritage\Support\Facades\Scribe;
use Heritage\Support\Facades\DB;
use Heritage\Support\Str;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Tests\FeatureTestCase;

class ListCommandTest extends FeatureTestCase
{
    use CreatesTelescopeEntries;

    /**
     * Indicates if console output should be mocked.
     *
     * Disabled so Scribe::output() captures the real command output.
     *
     * @var bool
     */
    public $mockConsoleOutput = false;

    public function test_list_filters_by_type()
    {
        $this->createRequest();
        $this->createException(['message' => 'fail']);

        Scribe::call('telescope:list', ['type' => 'request']);
        $output = Scribe::output();

        $this->assertStringContainsString('/test', $output);
        $this->assertStringNotContainsString('fail', $output);
        $this->assertStringContainsString('Showing 1 entry', $output);
    }

    public function test_list_validates_type_argument()
    {
        $this->assertSame(1, $this->scribe('telescope:list', ['type' => 'foobar']));
        $this->assertStringContainsString('Invalid entry type: foobar', Scribe::output());
    }

    public function test_list_filters_by_batch()
    {
        $batchId = (string) Str::uuid();

        $this->createRequest(['uri' => '/a'], ['batch_id' => $batchId]);
        $this->createRequest(['uri' => '/b']);

        Scribe::call('telescope:list', ['--batch' => $batchId]);
        $output = Scribe::output();

        $this->assertStringContainsString('/a', $output);
        $this->assertStringNotContainsString('/b', $output);
    }

    public function test_list_filters_by_tag()
    {
        $tagged = $this->createRequest(['uri' => '/tagged']);
        $this->createRequest(['uri' => '/untagged']);

        DB::table('telescope_entries_tags')->insert(['entry_uuid' => $tagged->uuid, 'tag' => 'Auth:42']);

        Scribe::call('telescope:list', ['type' => 'request', '--tag' => 'Auth:42']);
        $output = Scribe::output();

        $this->assertStringContainsString('/tagged', $output);
        $this->assertStringNotContainsString('/untagged', $output);
    }

    public function test_list_pages_backwards_with_the_before_cursor()
    {
        $this->createRequest(['uri' => '/older'], ['sequence' => 1]);
        $this->createRequest(['uri' => '/newer'], ['sequence' => 2]);

        Scribe::call('telescope:list', ['type' => 'request', '--before' => 2]);
        $output = Scribe::output();

        $this->assertStringContainsString('/older', $output);
        $this->assertStringNotContainsString('/newer', $output);
    }

    public function test_list_prints_a_cursor_when_the_page_is_full()
    {
        $last = null;

        foreach (range(1, 3) as $sequence) {
            $last = $this->createRequest([], ['sequence' => $sequence]);
        }

        Scribe::call('telescope:list', ['type' => 'request', '--limit' => 2]);

        $this->assertStringContainsString('Showing 2 entries - Use --before=2 for next page', Scribe::output());

        Scribe::call('telescope:list', ['type' => 'request', '--limit' => 20]);

        $this->assertStringContainsString('No more entries', Scribe::output());
    }

    public function test_list_rejects_a_non_positive_limit()
    {
        $this->createRequest();

        foreach (['abc', '0', '-1'] as $limit) {
            $this->assertSame(1, $this->scribe('telescope:list', ['type' => 'request', '--limit' => $limit]));
            $this->assertStringContainsString('--limit option must be a positive integer', Scribe::output());
        }
    }

    public function test_list_shows_warning_when_empty()
    {
        $this->assertSame(0, $this->scribe('telescope:list', ['type' => 'request']));
        $this->assertStringContainsString('No entries found.', Scribe::output());
    }

    public function test_list_summarizes_mixed_entry_types_when_no_type_is_given()
    {
        $this->createRequest();
        $this->createEntry(EntryType::CACHE, ['type' => 'hit', 'key' => 'user:1']);

        Scribe::call('telescope:list');
        $output = Scribe::output();

        $this->assertStringContainsString('Showing 2 entries', $output);
        $this->assertStringContainsString('GET /test -> 200', $output);
        $this->assertStringContainsString('hit user:1', $output);
    }

    public function test_list_outputs_json()
    {
        $entry = $this->createRequest();

        Scribe::call('telescope:list', ['type' => 'request', '--json' => true]);
        $json = json_decode(Scribe::output(), true);

        $this->assertSame($entry->uuid, $json[0]['id']);
        $this->assertSame('/test', $json[0]['content']['uri']);
    }

    public function test_list_outputs_an_empty_json_array_when_empty()
    {
        Scribe::call('telescope:list', ['type' => 'request', '--json' => true]);

        $this->assertSame([], json_decode(Scribe::output(), true));
    }
}
