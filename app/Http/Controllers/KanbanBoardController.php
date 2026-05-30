<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KanbanBoardController extends Controller
{
    public function index(){
        return view('project-executing.kanban-board.index');
    }
}
