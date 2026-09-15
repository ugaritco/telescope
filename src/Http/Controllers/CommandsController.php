<?php

namespace Ugarit\Telescope\Http\Controllers;

use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Watchers\CommandWatcher;

class CommandsController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::COMMAND;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return CommandWatcher::class;
    }
}
