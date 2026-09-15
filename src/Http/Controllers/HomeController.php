<?php

namespace Ugarit\Telescope\Http\Controllers;

use Heritage\Routing\Controller;
use Ugarit\Telescope\Telescope;

class HomeController extends Controller
{
    /**
     * Display the Telescope view.
     *
     * @return \Heritage\Http\Response
     */
    public function index()
    {
        return view('telescope::layout');
    }
}
