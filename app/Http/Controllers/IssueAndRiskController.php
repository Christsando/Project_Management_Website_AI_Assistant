<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Issue;
use App\Models\User;
// use App\Models\RiskManagementPlan;


class IssueAndRiskController extends Controller
{
    public function index(Request $request)
    {
        $projectId = $request->project_id;
        $users = User::all();
        $projects = Project::select('id', 'title')->get();

        $issues = null;

        if ($projectId) {
            $query = Issue::with(['assignee', 'reporter'])
                ->where('project_id', $projectId);

            if ($request->priority) {
                $query->where('priority', $request->priority);
            }

            if ($request->assigned === 'me') {
                $query->where('assignee_id', auth()->id());
            }

            if ($request->due) {
                if ($request->due === 'today') {
                    $query->whereDate('due_date', today());
                }

                if ($request->due === 'overdue') {
                    $query->whereDate('due_date', '<', today())
                        ->where('status', '!=', 'done');
                }

                if ($request->due === 'done') {
                    $query->where('status', 'done');
                }

                if ($request->due === 'approved') {
                    $query->where('status', 'approved');
                }
            }

            $issues = $query->get();
        }

        // $risks = RiskManagementPlan::where('project_id', $projectId)->get();

        return view('project-executing.issue-risk-management.index', [
            'projects' => $projects,
            'issues' => $issues,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'priority' => 'required',
        ]);

        Issue::create([
            'project_id' => $request->project_id,
            'title' => $request->title,
            'description' => $request->description,
            'assignee_id' => $request->assignee_id,
            'priority' => $request->priority,
            'due_date' => $request->due_date,
            'status' => 'open',
            'reported_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Issue berhasil dibuat');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Issue::STATUSES),
        ]);

        $issue = Issue::findOrFail($id);

        $issue->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Status berhasil diupdate',
            'status' => $issue->status,
        ]);
    }
}
