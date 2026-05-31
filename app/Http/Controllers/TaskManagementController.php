<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WbsItem;

use Illuminate\Http\Request;


class TaskManagementController extends Controller
{
    public function index()
    {
        $tasks = WbsItem::with('users')->get()->groupBy('status');

        return view('project-executing.task-management.index', [
            'tasks' => $tasks
        ]);
    }
}
