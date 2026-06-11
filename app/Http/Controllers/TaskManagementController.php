<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WbsItem;
use App\Models\Project;

use Illuminate\Http\Request;


class TaskManagementController extends Controller
{
    public function index($projectId = null)
    {
        $projects = Project::select('id', 'title')->get();

        if (!$projectId) {
            return view('project-executing.task-management.index', [
                'projects' => $projects,
                'allTasks' => collect(),
                'myTasks' => collect(),
                'project' => null
            ]);
        }

        $project = Project::findOrFail($projectId);
        $user = auth()->user();
        $allTasksRaw = $project->wbsItems()
            ->with('humanResourceItems.teamMember')
            ->get();


        if (strtolower($user->role) === 'project management officer') {
            $myTasks = $allTasksRaw;
        } else {
            $myTasks = $allTasksRaw->filter(function ($task) use ($user) {
                return $task->humanResourceItems->contains(function ($hr) use ($user) {
                    return $hr->teamMember->user_id === $user->id;
                });
            });
        }

        $allTasks = $allTasksRaw->groupBy(function ($task) {
            return match (strtolower($task->status)) {
                'todo', 'to-do' => 'todo',
                'ongoing', 'on-going', 'in_progress' => 'ongoing',
                'done' => 'done',
                'approved' => 'approved',
                default => 'todo'
            };
        });

        return view('project-executing.task-management.index', compact(
            'project',
            'projects',
            'allTasks', 
            'myTasks'
        ));
    }
}