<?php

namespace Ugarit\Telescope\Http\Controllers;

use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Watchers\RequestWatcher;

class RequestsController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::REQUEST;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return RequestWatcher::class;
    }
}
