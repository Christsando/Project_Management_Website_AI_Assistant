<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WbsItem;
use App\Models\Project;

use Illuminate\Http\Request;


class TaskManagementController extends Controller
{
    public function index(Request $request, $projectId = null)
    {
        $projects = Project::select('id', 'title')->get();

        if (!$projectId) {
            return view('project-executing.task-management.index', [
                'projects' => $projects,
                'allTasks' => collect(),
                'project' => null
            ]);
        }

        $project = Project::findOrFail($projectId);

        $query = $project->wbsItems()
            ->with(['humanResourceItems.teamMember', 'timelineItem']);

        // FILTER ASSIGNED
        if ($request->assigned === 'me') {
            $user = auth()->user();

            $query->whereHas('humanResourceItems.teamMember', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        // FILTER PRIORITY
        if ($request->priority && $request->priority !== 'all') {
            $query->where('priority', $request->priority);
        }

        // FILTER DUE DATE
        if ($request->due === 'today') {
            $query->whereHas('timelineItem', function ($q) {
                $q->whereDate('end_date', now());
            });
        }

        if ($request->due === 'overdue') {
            $query->whereHas('timelineItem', function ($q) {
                $q->whereDate('end_date', '<', now());
            });
        }

        $allTasksRaw = $query->get();

        $allTasks = $allTasksRaw->groupBy(function ($task) {
            return match (strtolower($task->kanban_status ?? 'todo')) {
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
            'allTasksRaw'
        ));
    }

    public function updateStatus(Request $request, $id)
    {
        \Log::info("ID MASUK: " . $id);

        $task = WbsItem::find($id);

        if (!$task) {
            return response()->json([
                'error' => 'Task tidak ditemukan',
                'id' => $id
            ], 404);
        }

        $task->kanban_status = $request->status;
        $task->save();

        return response()->json([
            'success' => true
        ]);
    }
}
