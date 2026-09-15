<?php

namespace Ugarit\Telescope\Http\Controllers;

use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Watchers\CacheWatcher;

class CacheController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::CACHE;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return CacheWatcher::class;
    }
}
