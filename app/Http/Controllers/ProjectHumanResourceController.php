<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\HumanResourcePlan;
use App\Models\HumanResourceItem;
use App\Models\WbsItem;
use App\Services\HrSummaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectHumanResourceController extends Controller
{
    /**
     * Check if the authenticated user has access to HR Planning.
     */
    protected function checkBaseAccess(): string
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

    /**
     * Check if the project is in planning status and has finalized preceding tasks.
     */
    protected function checkPlanningAccess(Project $project): void
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

    /**
     * Display a listing of projects in planning status and their HR status.
     */
    public function index()
    {
        $this->checkBaseAccess();

        // Managers and PMO see all planning projects
        $projects = Project::where('status', 'planning')
            ->with(['scope', 'wbsItems', 'timelineItems', 'budgetPlan', 'humanResourcePlan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('project-planning.human-resource.index', compact('projects'));
    }

    /**
     * Display the specified project's HR plan and items.
     */
    public function show(Project $project, HrSummaryService $summaryService)
    {
        $role = $this->checkBaseAccess();
        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;

        if (!$hrPlan) {
            if ($role === 'project management officer' || $role === 'pmo') {
                return redirect()->route('projects.human-resource.create', $project->id)
                    ->with('info', 'HR Plan belum dibuat. Silakan inisialisasi terlebih dahulu.');
            }
        }

        $hrItems = $hrPlan ? $hrPlan->humanResourceItems()->with(['wbsItem', 'teamMember'])->orderBy('created_at', 'desc')->get() : collect();
        $isHrFinalized = $hrPlan && $hrPlan->status === 'finalized';

        // Calculate summary aggregates
        $totalResources = $hrItems->sum('quantity');
        $roleCount = $hrItems->pluck('role_name')->unique()->count();
        $picCount = $hrItems->pluck('person_in_charge')->filter()->unique()->count();
        $summary = $summaryService->calculate($hrPlan, $hrItems);

        $userRole = strtolower(Auth::user()->role);
        $isPmo = in_array($userRole, ['pmo', 'project management officer']);
        $isDraft = $hrPlan && $hrPlan->status === 'draft';
        $isEditable = $isPmo && $isDraft;

        return view('project-planning.human-resource.show', compact(
            'project', 
            'hrPlan', 
            'hrItems', 
            'isHrFinalized', 
            'totalResources', 
            'roleCount', 
            'picCount',
            'summary',
            'isPmo',
            'isDraft',
            'isEditable',
            ));
    }

    /**
     * Show the form for creating a new HR plan.
     */
    public function create(Project $project)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat membuat Human Resource Plan.');
        }

        $this->checkPlanningAccess($project);

        if ($project->humanResourcePlan) {
            return redirect()->route('projects.human-resource.edit', $project->id)
                ->with('info', 'HR Plan sudah diinisialisasi. Anda dialihkan ke halaman edit.');
        }

        return view('project-planning.human-resource.create', compact('project'));
    }

    /**
     * Store a newly initialized HR plan.
     */
    public function store(Request $request, Project $project)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat membuat Human Resource Plan.');
        }

        $this->checkPlanningAccess($project);

        if ($project->humanResourcePlan) {
            return redirect()->route('projects.human-resource.edit', $project->id)
                ->with('info', 'HR Plan sudah diinisialisasi.');
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $hrPlan = new HumanResourcePlan();
        $hrPlan->project_id = $project->id;
        $hrPlan->status = 'draft';
        $hrPlan->notes = $request->notes;
        $hrPlan->created_by = Auth::id();
        $hrPlan->updated_by = Auth::id();
        $hrPlan->save();

        return redirect()->route('projects.human-resource.edit', $project->id)
            ->with('success', 'HR Plan berhasil diinisialisasi. Silakan tambahkan item perencanaan SDM.');
    }

    /**
     * Show the dashboard to manage HR plan items.
     */
    public function edit(Project $project, HrSummaryService $summaryService)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat mengedit Human Resource Plan.');
        }

        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;
        if (!$hrPlan) {
            return redirect()->route('projects.human-resource.create', $project->id);
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi dan tidak dapat diedit lagi.');
        }

        $hrItems = $hrPlan->humanResourceItems()->with(['wbsItem', 'teamMember'])->orderBy('created_at', 'desc')->get();
        
        // Fetch project WBS items for dropdown selection (only finalized WBS tasks)
        $wbsItems = $project->wbsItems()->orderBy('title')->get();

        // Fetch active team members for assignment
        $teamMembers = \App\Models\TeamMember::where('is_active', true)->orderBy('name')->get();

        // Calculate summary aggregates
        $totalResources = $hrItems->sum('quantity');
        $roleCount = $hrItems->pluck('role_name')->unique()->count();
        $picCount = $hrItems->pluck('person_in_charge')->filter()->unique()->count();
        $summary = $summaryService->calculate($hrPlan, $hrItems);

        $userRole = strtolower(Auth::user()->role);
        $isPmo = in_array($userRole, ['pmo', 'project management officer']);
        $isDraft = $hrPlan && $hrPlan->status === 'draft';
        $isEditable = $isPmo && $isDraft;

        return view('project-planning.human-resource.edit', compact(
            'project', 
            'hrPlan', 
            'hrItems', 
            'wbsItems', 
            'teamMembers', 
            'totalResources', 
            'roleCount', 
            'picCount',
            'summary',
            'isPmo',
            'isDraft',
            'isEditable',
            ));
    }

    /**
     * Update the HR plan general metadata.
     */
    public function update(Request $request, Project $project)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat memperbarui Human Resource Plan.');
        }

        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;
        if (!$hrPlan) {
            abort(404, 'HR Plan tidak ditemukan.');
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi dan tidak dapat diperbarui.');
        }

        $request->validate([
            'notes' => 'nullable|string',
        ]);

        $hrPlan->notes = $request->notes;
        $hrPlan->updated_by = Auth::id();
        $hrPlan->save();

        return redirect()->route('projects.human-resource.edit', $project->id)
            ->with('success', 'Catatan HR Plan berhasil diperbarui.');
    }

    /**
     * Add an HR item to the plan.
     */
    public function addItem(Request $request, Project $project)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat menambahkan item perencanaan SDM.');
        }

        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;
        if (!$hrPlan) {
            abort(404, 'HR Plan tidak ditemukan.');
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi.');
        }

        $request->validate([
            'role_name' => 'required|string|max:255',
            'required_skill' => 'required|string',
            'job_description' => 'required|string',
            'team_member_id' => 'nullable|exists:team_members,id',
            'person_in_charge' => 'nullable|string|max:255',
            'workload_percentage' => 'nullable|numeric|min:0|max:100',
            'estimated_work_days' => 'nullable|integer|min:1',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'wbs_item_id' => [
                'nullable',
                'exists:wbs_items,id',
                function ($attribute, $value, $fail) use ($project) {
                    if ($value) {
                        $wbs = WbsItem::find($value);
                        if (!$wbs || $wbs->project_id !== $project->id) {
                            $fail('Item WBS harus berasal dari proyek yang sama.');
                        }
                    }
                }
            ],
        ], [
            'role_name.required' => 'Nama Peran (Role) wajib diisi.',
            'required_skill.required' => 'Keahlian yang dibutuhkan wajib diisi.',
            'job_description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal adalah 1.',
            'workload_percentage.min' => 'Beban kerja minimal 0%.',
            'workload_percentage.max' => 'Beban kerja maksimal 100%.',
            'estimated_work_days.min' => 'Estimasi hari kerja minimal 1 hari.',
        ]);

        $item = new HumanResourceItem();
        $item->human_resource_plan_id = $hrPlan->id;
        $item->wbs_item_id = $request->wbs_item_id;
        $item->role_name = $request->role_name;
        $item->required_skill = $request->required_skill;
        $item->job_description = $request->job_description;

        $item->team_member_id = $request->team_member_id;
        $item->workload_percentage = $request->workload_percentage;

        if ($request->team_member_id) {
            $teamMember = \App\Models\TeamMember::findOrFail($request->team_member_id);
            $newWorkload = $request->workload_percentage ?: 0;
            $totalWorkload = $teamMember->current_workload_percentage + $newWorkload;
            
            if ($totalWorkload > $teamMember->default_capacity_percentage) {
                return redirect()->back()->withInput()->with('error', "Beban kerja untuk {$teamMember->name} melebihi kapasitas default ({$teamMember->default_capacity_percentage}%). Sisa kapasitas tersedia: {$teamMember->remaining_capacity_percentage}%.");
            }
            
            // Auto fill name
            $item->person_in_charge = $teamMember->name;
        } else {
            $item->person_in_charge = $request->person_in_charge;
        }

        $item->estimated_work_days = $request->estimated_work_days;
        $item->quantity = $request->input('quantity', 1) ?? 1;
        $item->notes = $request->notes;
        $item->created_by = Auth::id();
        $item->updated_by = Auth::id();
        $item->save();

        return redirect()->route('projects.human-resource.edit', $project->id)
            ->with('success', 'Item perencanaan SDM berhasil ditambahkan.');
    }

    /**
     * Update an existing HR item.
     */
    public function updateItem(Request $request, Project $project, HumanResourceItem $humanResourceItem)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat memperbarui item perencanaan SDM.');
        }

        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;
        if (!$hrPlan || $humanResourceItem->human_resource_plan_id !== $hrPlan->id) {
            abort(404, 'Item perencanaan SDM tidak sesuai dengan proyek ini.');
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi.');
        }

        $request->validate([
            'role_name' => 'required|string|max:255',
            'required_skill' => 'required|string',
            'job_description' => 'required|string',
            'team_member_id' => 'nullable|exists:team_members,id',
            'person_in_charge' => 'nullable|string|max:255',
            'workload_percentage' => 'nullable|numeric|min:0|max:100',
            'estimated_work_days' => 'nullable|integer|min:1',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
            'wbs_item_id' => [
                'nullable',
                'exists:wbs_items,id',
                function ($attribute, $value, $fail) use ($project) {
                    if ($value) {
                        $wbs = WbsItem::find($value);
                        if (!$wbs || $wbs->project_id !== $project->id) {
                            $fail('Item WBS harus berasal dari proyek yang sama.');
                        }
                    }
                }
            ],
        ], [
            'role_name.required' => 'Nama Peran (Role) wajib diisi.',
            'required_skill.required' => 'Keahlian yang dibutuhkan wajib diisi.',
            'job_description.required' => 'Deskripsi pekerjaan wajib diisi.',
            'quantity.integer' => 'Jumlah harus berupa angka bulat.',
            'quantity.min' => 'Jumlah minimal adalah 1.',
            'workload_percentage.min' => 'Beban kerja minimal 0%.',
            'workload_percentage.max' => 'Beban kerja maksimal 100%.',
            'estimated_work_days.min' => 'Estimasi hari kerja minimal 1 hari.',
        ]);

        $humanResourceItem->wbs_item_id = $request->wbs_item_id;
        $humanResourceItem->role_name = $request->role_name;
        $humanResourceItem->required_skill = $request->required_skill;
        $humanResourceItem->job_description = $request->job_description;
        
        $humanResourceItem->team_member_id = $request->team_member_id;
        $humanResourceItem->workload_percentage = $request->workload_percentage;

        if ($request->team_member_id) {
            $teamMember = \App\Models\TeamMember::findOrFail($request->team_member_id);
            $newWorkload = $request->workload_percentage ?: 0;
            // Exclude current item workload to avoid double count
            $currentWorkloadExcludingThis = $teamMember->humanResourceItems()->where('id', '!=', $humanResourceItem->id)->sum('workload_percentage');
            $totalWorkload = $currentWorkloadExcludingThis + $newWorkload;
            
            if ($totalWorkload > $teamMember->default_capacity_percentage) {
                return redirect()->back()->withInput()->with('error', "Beban kerja untuk {$teamMember->name} melebihi kapasitas default ({$teamMember->default_capacity_percentage}%). Sisa kapasitas tersedia: " . ($teamMember->default_capacity_percentage - $currentWorkloadExcludingThis) . "%.");
            }
            
            // Auto fill name
            $humanResourceItem->person_in_charge = $teamMember->name;
        } else {
            $humanResourceItem->person_in_charge = $request->person_in_charge;
        }

        $humanResourceItem->estimated_work_days = $request->estimated_work_days;
        $humanResourceItem->quantity = $request->input('quantity', 1) ?? 1;
        $humanResourceItem->notes = $request->notes;
        $humanResourceItem->updated_by = Auth::id();
        $humanResourceItem->save();

        return redirect()->route('projects.human-resource.edit', $project->id)
            ->with('success', 'Item perencanaan SDM berhasil diperbarui.');
    }

    /**
     * Delete an HR item (only draft allowed).
     */
    public function deleteItem(Project $project, HumanResourceItem $humanResourceItem)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat menghapus item perencanaan SDM.');
        }

        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;
        if (!$hrPlan || $humanResourceItem->human_resource_plan_id !== $hrPlan->id) {
            abort(404, 'Item perencanaan SDM tidak sesuai dengan proyek ini.');
        }

        if ($hrPlan->status === 'finalized') {
            abort(403, 'HR Plan sudah difinalisasi.');
        }

        $humanResourceItem->delete();

        return redirect()->route('projects.human-resource.edit', $project->id)
            ->with('success', 'Item perencanaan SDM berhasil dihapus.');
    }

    /**
     * Finalize the project HR plan.
     */
    public function finalize(Project $project)
    {
        $role = $this->checkBaseAccess();
        if ($role !== 'project management officer' && $role !== 'pmo') {
            abort(403, 'Hanya PMO yang dapat memfinalisasi Human Resource Plan.');
        }

        $this->checkPlanningAccess($project);

        $hrPlan = $project->humanResourcePlan;
        if (!$hrPlan) {
            abort(404, 'HR Plan tidak ditemukan.');
        }

        if ($hrPlan->status === 'finalized') {
            return redirect()->route('projects.human-resource.show', $project->id)
                ->with('info', 'HR Plan sudah berstatus finalized.');
        }

        // PMO tidak boleh finalize jika belum ada HR item
        if ($hrPlan->humanResourceItems()->count() === 0) {
            return redirect()->route('projects.human-resource.edit', $project->id)
                ->with('error', 'HR Plan tidak dapat difinalisasi karena belum memiliki item perencanaan SDM.');
        }

        $hrPlan->status = 'finalized';
        $hrPlan->updated_by = Auth::id();
        $hrPlan->save();

        return redirect()->route('projects.human-resource.show', $project->id)
            ->with('success', 'HR Plan berhasil difinalisasi.');
    }
}
