<?php

namespace App\Http\Controllers;

use App\Models\Work;
use App\Models\User;


use Illuminate\Http\Request;

class DetailController extends Controller
{
    public function index($work_id)
    {

        $user = User::all();
        $work = Work::all();
        $id = $work_id;
        return view('detail', compact('user', 'work', 'id'));
    }
}
