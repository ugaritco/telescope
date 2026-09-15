<?php

namespace Ugarit\Telescope\Http\Controllers;

use Ugarit\Telescope\Contracts\EntriesRepository;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\Storage\EntryQueryOptions;
use Ugarit\Telescope\Watchers\JobWatcher;

class QueueController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::JOB;
    }

    /**
     * Get an entry with the given ID.
     *
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $storage
     * @param  int  $id
     * @return \Heritage\Http\JsonResponse
     */
    public function show(EntriesRepository $storage, $id)
    {
        $entry = $storage->find($id);

        return response()->json([
            'entry' => $entry,
            'batch' => isset($entry->content['updated_batch_id'])
                ? $storage->get(null, EntryQueryOptions::forBatchId($entry->content['updated_batch_id'])->limit(-1))
                : null,
        ]);
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return JobWatcher::class;
    }
}
