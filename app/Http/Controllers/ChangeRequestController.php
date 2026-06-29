<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\ChangeRequest;
use App\Models\WbsItem;
use App\Models\TimelineItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
        return view('project-executing.change-request.index', [
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
            'requested_deadline' => 'required|date',
        ]);

        $task = WbsItem::findOrFail($validated['wbs_item_id']);

        $cr = ChangeRequest::create([
            'project_id' => $task->project_id,
            'wbs_item_id' => $task->id,
            'field_changed' => $request->field_changed,
            'old_value' => $request->old_value,
            'new_value' => $validated['new_value'],
            'reason' => $validated['reason'],
            'requested_deadline' => $validated['requested_deadline'],
            'status' => 'pending',
            'requested_by' => auth()->id(),
        ]);

        return response()->json(['success' => true, 'data' => $cr]);
    }

    public function approve(ChangeRequest $changeRequest)
    {
        if ($changeRequest->status !== 'pending') {
            return back()->with(
                'error',
                'Change Request sudah diproses'
            );
        }

        DB::transaction(function () use ($changeRequest) {

            $wbsItem = $changeRequest->wbsItem;

            // update task
            $wbsItem->update([
                'description' => $changeRequest->new_value,
                'kanban_status' => 'todo',
            ]);

            // update timeline
            $timeline = TimelineItem::where('wbs_item_id',$wbsItem->id)->first();

            if ($timeline) {

                $duration = Carbon::parse($timeline->start_date)
                    ->diffInDays(
                        Carbon::parse($changeRequest->requested_deadline)
                    ) + 1;

                $timeline->update([
                    'end_date' => $changeRequest->requested_deadline,
                    'duration_days' => $duration,
                ]);
            }

            // approve CR
            $changeRequest->update([
                'status' => 'approved',
                'approved_at' => now(),
            ]);
        });

        return back()->with(
            'success',
            'Change Request berhasil disetujui'
        );
    }
}
