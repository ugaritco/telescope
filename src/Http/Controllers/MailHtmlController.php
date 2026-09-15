<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Routing\Controller;
use Ugarit\Telescope\Contracts\EntriesRepository;

class MailHtmlController extends Controller
{
    /**
     * Get the HTML content of the given email.
     *
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $storage
     * @param  int  $id
     * @return mixed
     */
    public function show(EntriesRepository $storage, $id)
    {
        return $storage->find($id)->content['html'];
    }
}
