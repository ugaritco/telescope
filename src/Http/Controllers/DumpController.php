<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Cache\ArrayStore;
use Heritage\Contracts\Cache\Repository as CacheRepository;
use Heritage\Http\Request;
use Ugarit\Telescope\Contracts\EntriesRepository;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Storage\EntryQueryOptions;
use Ugarit\Telescope\Watchers\DumpWatcher;
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;

class DumpController extends EntryController
{
    /**
     * The cache repository implementation.
     *
     * @var \Heritage\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new controller instance.
     *
     * @param  \Heritage\Contracts\Cache\Repository  $cache
     * @return void
     */
    public function __construct(CacheRepository $cache)
    {
        $this->cache = $cache;
    }

    /**
     * List the entries of the given type.
     *
     * @param  \Heritage\Http\Request  $request
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $storage
     * @return \Heritage\Http\JsonResponse
     */
    public function index(Request $request, EntriesRepository $storage)
    {
        $this->cache->put('telescope:dump-watcher', true, now()->addSeconds(4));

        return response()->json([
            'dump' => (new HtmlDumper())->dump((new VarCloner)->cloneVar(true), true),
            'entries' => $storage->get(
                $this->entryType(),
                EntryQueryOptions::fromRequest($request)
            ),
            'status' => $this->status(),
        ]);
    }

    /**
     * Determine the watcher recording status.
     *
     * @return string
     */
    protected function status()
    {
        if ($this->cache->getStore() instanceof ArrayStore) {
            return 'wrong-cache';
        }

        return parent::status();
    }

    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::DUMP;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return DumpWatcher::class;
    }
}
