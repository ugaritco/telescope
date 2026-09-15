<?php

namespace Ugarit\Telescope\Contracts;

use Heritage\Support\Collection;
use Ugarit\Telescope\EntryResult;
use Ugarit\Telescope\Storage\EntryQueryOptions;

interface EntriesRepository
{
    /**
     * Return an entry with the given ID.
     *
     * @param  mixed  $id
     * @return \Ugarit\Telescope\EntryResult
     */
    public function find($id): EntryResult;

    /**
     * Return all the entries of a given type.
     *
     * @param  string|null  $type
     * @param  \Ugarit\Telescope\Storage\EntryQueryOptions  $options
     * @return \Heritage\Support\Collection|\Ugarit\Telescope\EntryResult[]
     */
    public function get($type, EntryQueryOptions $options);

    /**
     * Store the given entries.
     *
     * @param  \Heritage\Support\Collection|\Ugarit\Telescope\IncomingEntry[]  $entries
     * @return void
     */
    public function store(Collection $entries);

    /**
     * Store the given entry updates and return the failed updates.
     *
     * @param  \Heritage\Support\Collection|\Ugarit\Telescope\EntryUpdate[]  $updates
     * @return \Heritage\Support\Collection|null
     */
    public function update(Collection $updates);

    /**
     * Load the monitored tags from storage.
     *
     * @return void
     */
    public function loadMonitoredTags();

    /**
     * Determine if any of the given tags are currently being monitored.
     *
     * @param  array  $tags
     * @return bool
     */
    public function isMonitoring(array $tags);

    /**
     * Get the list of tags currently being monitored.
     *
     * @return array
     */
    public function monitoring();

    /**
     * Begin monitoring the given list of tags.
     *
     * @param  array  $tags
     * @return void
     */
    public function monitor(array $tags);

    /**
     * Stop monitoring the given list of tags.
     *
     * @param  array  $tags
     * @return void
     */
    public function stopMonitoring(array $tags);
}
