<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChangeRequestController extends Controller
{
    public function index(){
        return view ('project-executing.change-request.index');
    }
}
