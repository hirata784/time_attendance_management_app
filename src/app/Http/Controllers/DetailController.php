<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index($work_id)
    {
        $id = $work_id;
        return view('detail', compact('id'));
    }
}
