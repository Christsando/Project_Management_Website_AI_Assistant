<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ChangeRequest;
use App\Models\WbsItem;

class ChangeRequestController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->project_id;
        $changeRequests = collect();

        if ($projectId) {
            $query = ChangeRequest::with(['wbsItem', 'requestedBy'])
                ->where('project_id', $projectId);

            if ($request->status) {
                $query->where('status', $request->status);
            }

            $changeRequests = $query->latest()->get();
        }

        $projects = Project::select('id', 'title')->get();
        return view('project-executing.change-request.index',[
            'projects' => $projects,
            'changeRequests' => $changeRequests,
            'selectedProjectId' => $projectId,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'wbs_item_id' => 'required|exists:wbs_items,id',
            'new_value' => 'required|string',
            'reason' => 'required|string',
        ]);

        $task = WbsItem::findOrFail($validated['wbs_item_id']);

        $cr = ChangeRequest::create([
            'project_id' => $task->project_id,
            'wbs_item_id' => $task->id,
            'field_changed' => $request->field_changed,
            'old_value' => $request->old_value,
            'new_value' => $validated['new_value'],
            'reason' => $validated['reason'],
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $cr]);
    }
}
