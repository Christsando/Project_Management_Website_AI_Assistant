<?php

namespace App\Services;

use App\Models\Project;
use App\Models\HumanResourcePlan;
use App\Models\TeamMember;
use App\Models\TimeLineItem;
use App\Models\WbsItem;
use App\Models\HumanResourceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumanResourceService
{
    // chrvk user access role
    public function checkBaseAccess(): string
    {
        if (!Auth::check()) {
            abort(401);
        }

        $role = strtolower(Auth::user()->role);
        if (!in_array($role, ['manager', 'project management officer', 'pmo'])) {
            abort(403, 'Akses ditolak. Peran Anda tidak diizinkan mengakses Human Resource Planning.');
        }

        return $role;
    }

    // check project status
    public function checkPlanningAccess(Project $project): void
    {
        // 1. Project status must be planning
        if ($project->status !== 'planning') {
            abort(403, 'Human Resource Planning hanya dapat diakses jika status proyek adalah Planning.');
        }

        // 2. Scope must be finalized
        if (!$project->scope || $project->scope->status !== 'finalized') {
            abort(403, 'Human Resource Planning hanya dapat diakses jika Project Scope proyek ini sudah finalized.');
        }

        // 3. WBS must be finalized
        $wbsCount = $project->wbsItems()->count();
        $wbsDraftCount = $project->wbsItems()->where('status', 'draft')->count();
        $isWbsFinalized = ($wbsCount > 0 && $wbsDraftCount === 0);
        if (!$isWbsFinalized) {
            abort(403, 'Human Resource Planning hanya dapat diakses jika WBS proyek ini sudah finalized.');
        }

        // 4. Timeline must be finalized
        $timelineCount = $project->timelineItems()->count();
        $timelineDraftCount = $project->timelineItems()->where('status', 'draft')->count();
        $isTimelineFinalized = ($timelineCount > 0 && $timelineDraftCount === 0 && $timelineCount === $wbsCount);
        if (!$isTimelineFinalized) {
            abort(403, 'Human Resource Planning hanya dapat diakses jika Timeline proyek ini sudah finalized.');
        }

        // 5. Budget must be finalized
        if (!$project->budgetPlan || $project->budgetPlan->status !== 'finalized') {
            abort(403, 'Human Resource Planning hanya dapat diakses jika Budget Planning proyek ini sudah finalized.');
        }
    }

    public function getProjectDuration(Project $project)
    {
        $timelineItems = TimelineItem::where('project_id', $project->id)->get();

        $minDate = $timelineItems->min('start_date');
        $maxDate = $timelineItems->max('end_date');

        $projectDurationDays = ($minDate && $maxDate)
            ? \Carbon\Carbon::parse($minDate)->diffInDays($maxDate) + 1
            : 0;

        return [
            'minDate' => $minDate,
            'projectDurationDays' => $projectDurationDays,
        ];
    }

    public function getHrStatistics($hrItems)
    {
        return [
            'totalResources' => $hrItems->pluck('team_member_id')->unique()->count(),
            'roleCount' => $hrItems->pluck('role_name')->unique()->count(),
            'picCount' => $hrItems->pluck('person_in_charge')->filter()->unique()->count(),
        ];
    }

    public function getEditPermission($hrPlan)
    {
        $userRole = strtolower(Auth::user()->role);

        $isPmo = in_array($userRole, [
            'pmo',
            'project management officer'
        ]);

        $isDraft = $hrPlan && $hrPlan->status === 'draft';

        return [
            'isPmo' => $isPmo,
            'isDraft' => $isDraft,
            'isEditable' => $isPmo && $isDraft,
        ];
    }

    public function getMemberWorkloads($hrItems)
    {
        $teamMembers = TeamMember::whereIn(
            'id',
            $hrItems->pluck('team_member_id')->filter()
        )->get();

        $groupedItems = $hrItems->groupBy('team_member_id');

        $memberWorkloads = $groupedItems->map(function ($items) {
            return [
                'total_workload' => $items->sum('workload_percentage'),
                'total_days' => $items->sum('estimated_work_days'),
            ];
        });

        return [
            'teamMembers' => $teamMembers,
            'groupedItems' => $groupedItems,
            'memberWorkloads' => $memberWorkloads,
        ];
    }

    public function getAllChildWbsIds($wbsId)
    {
        $ids = [$wbsId];
        $children = WbsItem::where('parent_id', $wbsId)->get();

        foreach ($children as $child) {
            $ids = array_merge($ids, $this->getAllChildWbsIds($child->id));
        }

        return $ids;
    }

    private function assignMemberToWbs(HumanResourcePlan $hrPlan, int $wbsId, int $memberId, float $workload)
    {
        $member = TeamMember::findOrFail($memberId);

        $template = HumanResourceItem::where('human_resource_plan_id', $hrPlan->id)
            ->where('team_member_id', $memberId)
            ->whereNull('wbs_item_id')
            ->first();

        if ($template) {

            $template->update([
                'wbs_item_id' => $wbsId,
                'job_description' => null,
                'workload_percentage' => $workload,
                'estimated_work_days' => 1,
                'person_in_charge' => $member->name,
                'role_name' => $member->role_name,
                'required_skill' => $member->skills,
                'updated_by' => Auth::id(),
            ]);
        } else {

            HumanResourceItem::create([
                'human_resource_plan_id' => $hrPlan->id,
                'wbs_item_id' => $wbsId,
                'team_member_id' => $memberId,
                'person_in_charge' => $member->name,
                'role_name' => $member->role_name,
                'required_skill' => $member->skills,
                'job_description' => null,
                'workload_percentage' => $workload,
                'estimated_work_days' => 1,
                'quantity' => 1,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }
    }

    public function assignMembers(Project $project, int $wbsId, array $memberIds, array $workloads)
    {
        $hrPlan = $project->humanResourcePlan;

        if (!$hrPlan) {
            throw new \Exception('HR Plan belum ada.');
        }

        $wbsIds = $this->getAllChildWbsIds($wbsId);

        foreach ($wbsIds as $id) {
            foreach ($memberIds as $index => $memberId) {

                $this->assignMemberToWbs(
                    $hrPlan,
                    $id,
                    $memberId,
                    $workloads[$index] ?? 0
                );
            }
        }
    }

    public function getAvailableTeamMembers($hrPlan)
    {
        $assignedMemberIds = $hrPlan->humanResourceItems()
            ->whereNotNull('team_member_id')
            ->pluck('team_member_id');

        return TeamMember::where('is_active', true)
            ->whereNotIn('id', $assignedMemberIds)
            ->orderBy('role_name')
            ->get();
    }

    public function addItem(Project $project, Request $request)
    {
        $hrPlan = $project->humanResourcePlan;

        if (!$hrPlan) {
            abort(404, 'HR Plan tidak ditemukan.');
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi.');
        }

        $this->validateDuplicateMember($hrPlan, $request);
        $this->validateRequiredSelection($request);

        $wbs = $request->wbs_item_id
            ? WbsItem::find($request->wbs_item_id)
            : null;

        $teamMember = $request->team_member_id
            ? TeamMember::with('user')->find($request->team_member_id)
            : null;

        $this->validateMemberCapacity($teamMember, $request);
        $this->createHrItem(
            $hrPlan,
            $teamMember,
            $wbs,
            $request
        );
    }

    private function validateDuplicateMember($hrPlan, Request $request)
    {
        $exists = HumanResourceItem::where('human_resource_plan_id', $hrPlan->id)
            ->where('team_member_id', $request->team_member_id)
            ->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Anggota tim tersebut sudah menjadi bagian dari proyek ini.'
                )
                ->throwResponse();
        }
    }

    private function validateRequiredSelection(Request $request)
    {
        if (!$request->team_member_id && !$request->wbs_item_id) {
            return back()
                ->withInput()
                ->with(
                    'error',
                    'Minimal pilih Team Member atau WBS terlebih dahulu.'
                )
                ->throwResponse();
        }
    }

    private function validateMemberCapacity($teamMember, Request $request)
    {
        if (!$teamMember) {
            return;
        }

        $newWorkload = $request->workload_percentage ?: 0;
        $totalWorkload = $teamMember->current_workload_percentage + $newWorkload;

        if ($totalWorkload > $teamMember->default_capacity_percentage) {

            return back()
                ->withInput()
                ->with(
                    'error',
                    "Beban kerja untuk {$teamMember->name} melebihi kapasitas default ({$teamMember->default_capacity_percentage}%). Sisa kapasitas tersedia: {$teamMember->remaining_capacity_percentage}%."
                )
                ->throwResponse();
        }
    }

    private function createHrItem(HumanResourcePlan $hrPlan, ?TeamMember $teamMember, ?WbsItem $wbs, Request $request)
    {
        $item = new HumanResourceItem();

        $item->human_resource_plan_id = $hrPlan->id;
        $item->wbs_item_id = $request->wbs_item_id;
        $item->job_description = $wbs?->description;
        $item->required_skill = $teamMember?->skills;
        $item->role_name = $teamMember?->user->role ?? $teamMember?->role_name;
        $item->team_member_id = $request->team_member_id;
        $item->workload_percentage = $request->workload_percentage;
        $item->person_in_charge = $teamMember?->name;
        $item->estimated_work_days = $request->estimated_work_days;
        $item->quantity = $request->input('quantity', 1);
        $item->notes = $request->notes;
        $item->created_by = Auth::id();
        $item->updated_by = Auth::id();

        $item->save();
    }

    public function updateItem(Project $project, HumanResourceItem $humanResourceItem, Request $request)
    {
        $hrPlan = $project->humanResourcePlan;

        if (!$hrPlan || $humanResourceItem->human_resource_plan_id !== $hrPlan->id) {
            abort(404, 'Item perencanaan SDM tidak sesuai dengan proyek ini.');
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi.');
        }

        $wbs = $request->wbs_item_id
            ? WbsItem::find($request->wbs_item_id)
            : null;

        $teamMember = $request->team_member_id
            ? TeamMember::with('user')->find($request->team_member_id)
            : null;

        $this->validateUpdateMemberCapacity(
            $humanResourceItem,
            $request
        );

        $this->saveUpdatedItem(
            $humanResourceItem,
            $teamMember,
            $wbs,
            $request
        );
    }

    private function validateUpdateMemberCapacity(HumanResourceItem $humanResourceItem, Request $request)
    {

        if (!$request->team_member_id) {
            return;
        }

        $teamMember = TeamMember::findOrFail($request->team_member_id);
        $newWorkload = $request->workload_percentage ?: 0;

        $currentWorkload = $teamMember
            ->humanResourceItems()
            ->where('id', '!=', $humanResourceItem->id)
            ->sum('workload_percentage');

        $totalWorkload = $currentWorkload + $newWorkload;

        if ($totalWorkload > $teamMember->default_capacity_percentage) {

            return redirect()->back()
                ->withInput()
                ->with(
                    'error',
                    "Beban kerja untuk {$teamMember->name} melebihi kapasitas default ({$teamMember->default_capacity_percentage}%). Sisa kapasitas tersedia: " .
                        ($teamMember->default_capacity_percentage - $currentWorkload) .
                        "%."
                )
                ->throwResponse();
        }
    }

    private function saveUpdatedItem(HumanResourceItem $humanResourceItem, ?TeamMember $teamMember, ?WbsItem $wbs, Request $request) 
    {
        $humanResourceItem->wbs_item_id = $request->wbs_item_id;
        $humanResourceItem->team_member_id = $request->team_member_id;
        $humanResourceItem->workload_percentage = $request->workload_percentage;

        if ($teamMember) {
            $humanResourceItem->person_in_charge = $teamMember->name;
        } else {
            $humanResourceItem->person_in_charge = $request->person_in_charge;
        }

        $humanResourceItem->job_description = $wbs?->description;
        $humanResourceItem->required_skill = $teamMember?->skills;
        $humanResourceItem->role_name = $teamMember?->user->role ?? $teamMember?->role_name;

        $humanResourceItem->estimated_work_days = $request->estimated_work_days;
        $humanResourceItem->quantity = $request->input('quantity', 1);
        $humanResourceItem->notes = $request->notes;
        $humanResourceItem->updated_by = Auth::id();

        $humanResourceItem->save();
    }
}
