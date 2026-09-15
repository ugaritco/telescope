<?php

namespace Ugarit\Telescope\Http\Controllers;

use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Watchers\RedisWatcher;

class RedisController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::REDIS;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return RedisWatcher::class;
    }
}
