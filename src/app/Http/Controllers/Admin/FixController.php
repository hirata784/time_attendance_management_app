<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FixController extends Controller
{
    public function edit($work_id){
        return view('admin/fix', compact('work_id'));
    }
}
