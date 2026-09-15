<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Routing\Controller;
use Ugarit\Telescope\Contracts\EntriesRepository;

class MailEmlController extends Controller
{
    /**
     * Download the Eml content of the email.
     *
     * @param  \Ugarit\Telescope\Contracts\EntriesRepository  $storage
     * @param  int  $id
     * @return mixed
     */
    public function show(EntriesRepository $storage, $id)
    {
        return response($storage->find($id)->content['raw'], 200, [
            'Content-Type' => 'message/rfc822',
            'Content-Disposition' => 'attachment; filename="mail-'.$id.'.eml"',
        ]);
    }
}
