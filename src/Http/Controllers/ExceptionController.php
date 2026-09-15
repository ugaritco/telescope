<?php

namespace Ugarit\Telescope\Http\Controllers;

use Carbon\Carbon;
use Heritage\Http\Request;
use Ugarit\Telescope\Contracts\EntriesRepository;
use Ugarit\Telescope\EntryType;
use Ugarit\Telescope\EntryUpdate;
use Ugarit\Telescope\Storage\EntryQueryOptions;
use Ugarit\Telescope\Watchers\ExceptionWatcher;

class ExceptionController extends EntryController
{
    /**
     * The entry type for the controller.
     *
     * @return string
     */
    protected function entryType()
    {
        return EntryType::EXCEPTION;
    }

    /**
     * The watcher class for the controller.
     *
     * @return string
     */
    protected function watcher()
    {
        return ExceptionWatcher::class;
    }

    /**
     * Update an entry with the given ID.
     *
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $storage
     * @param  int  $id
     * @return \Heritage\Http\JsonResponse
     */
    public function update(EntriesRepository $storage, Request $request, $id)
    {
        $entry = $storage->find($id);

        if ($request->input('resolved_at') === 'now') {
            $update = new EntryUpdate($entry->id, $entry->type, [
                'resolved_at' => Carbon::now()->toDateTimeString(),
            ]);

            $storage->update(collect([$update]));

            // Reload entry
            $entry = $storage->find($id);
        }

        return response()->json([
            'entry' => $entry,
            'batch' => $storage->get(null, EntryQueryOptions::forBatchId($entry->batchId)->limit(-1)),
        ]);
    }
}
