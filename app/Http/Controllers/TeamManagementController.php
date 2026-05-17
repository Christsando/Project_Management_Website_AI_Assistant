<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeamManagementController extends Controller
{
    public function index(){
        return view('project-executing.team-management.index');
    }
}
