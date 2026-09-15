<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Bus\BatchRepository;
use Ugarit\Telescope\Contracts\EntriesRepository;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\EntryUpdate;
use Ugarit\Telescope\Storage\EntryQueryOptions;
use Ugarit\Telescope\Watchers\BatchWatcher;

class QueueBatchesController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::BATCH;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return BatchWatcher::class;
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
        $batch = app(BatchRepository::class)->find($id);

        $storage->update(collect([
            new EntryUpdate($id, EntryType::BATCH,
                $batch?->toArray() ?: []
            ),
        ]));

        $entry = $storage->find($id)->generateAvatar();

        return response()->json([
            'entry' => $entry,
            'batch' => $storage->get(null, EntryQueryOptions::forBatchId($entry->batchId)->limit(-1)),
        ]);
    }
}
