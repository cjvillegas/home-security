<?php

namespace App\Http\Controllers;

use App\Models\User;

class SbgBaseController extends Controller
{
    /**
     * @var User
     */
    protected $user;

    /**
     * SbgBaseController constructor
     *
     * Initialize base data.
     */
    public function __construct()
    {
        $this->user = User::find(auth()->user()->id);
    }
}
