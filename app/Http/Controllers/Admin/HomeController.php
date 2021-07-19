<?php

namespace App\Http\Controllers\Admin;

use Illuminate\View\View;

class HomeController
{
    /**
     * The dashboard route
     *
     * @return View
     */
    public function index()
    {
        return view('admin/dashboard/index');
    }
}
