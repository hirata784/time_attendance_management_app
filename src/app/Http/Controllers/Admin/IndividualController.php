<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class IndividualController extends Controller
{
    public function index($user_id)
    {
        return view('admin/individual', compact('user_id'));
    }
}
