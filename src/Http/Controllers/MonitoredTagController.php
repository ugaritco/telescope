<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Http\Request;
use Heritage\Routing\Controller;
use Ugarit\Telescope\Contracts\EntriesRepository;

class MonitoredTagController extends Controller
{
    /**
     * The entry repository implementation.
     *
     * @var \Ugarit\Telescope\Contracts\EntriesRepository
     */
    protected $entries;

    /**
     * Create a new controller instance.
     *
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $entries
     * @return void
     */
    public function __construct(EntriesRepository $entries)
    {
        $this->entries = $entries;
    }

    /**
     * Get all of the tags being monitored.
     *
     * @return \Heritage\Http\JsonResponse
     */
    public function index()
    {
        return response()->json([
            'tags' => $this->entries->monitoring(),
        ]);
    }

    /**
     * Begin monitoring the given tag.
     *
     * @param  \Heritage\Http\Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        $this->entries->monitor([$request->tag]);
    }

    /**
     * Stop monitoring the given tag.
     *
     * @param  \Heritage\Http\Request  $request
     * @return void
     */
    public function destroy(Request $request)
    {
        $this->entries->stopMonitoring([$request->tag]);
    }
}
