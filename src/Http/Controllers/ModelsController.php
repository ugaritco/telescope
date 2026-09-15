<?php

namespace Ugarit\Telescope\Http\Controllers;

use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Watchers\ModelWatcher;

class ModelsController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::MODEL;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return ModelWatcher::class;
    }
}
