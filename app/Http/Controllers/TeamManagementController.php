<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class TeamManagementController extends Controller
{
    public function index(){
        $totalUser = User::getTotalUser();
        $userData = User::getAllUser();
        return view('project-executing.team-management.index', compact('totalUser', 'userData'));
    }
}
