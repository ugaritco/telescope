<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Routing\Controller;
use Ugarit\Telescope\Contracts\ClearableRepository;

class EntriesController extends Controller
{
    /**
     * Delete all of the entries from storage.
     *
     * @param  \Ugarit\Telescope\Contracts\ClearableRepository  $storage
     * @return void
     */
    public function destroy(ClearableRepository $storage)
    {
        $storage->clear();
    }
}
