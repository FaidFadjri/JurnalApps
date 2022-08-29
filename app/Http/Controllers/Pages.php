<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class Pages extends Controller
{
    function index()
    {
        $items['title'] = 'Dashboard';
        $items['nav']   = 'home';
        return view('pages.dashboard', $items);
    }

    function report()
    {
        $components['title']        = "Report";
        $components['nav']          = "report";
        $components['errorPKB']     = session()->get('errorPKB');
        return view('pages.report', $components);
    }
}
