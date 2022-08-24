<?php

namespace App\Http\Controllers;

class Pages extends Controller
{
    function index()
    {
        $items['title'] = 'Dashboard';
        $items['nav']   = 'home';
        return view('pages.dashboard', $items);
    }
}
