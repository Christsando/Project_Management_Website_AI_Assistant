<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class CostController extends Controller
{
    public function index(Request $request, $projectId = null)
    {
        $projectId = $request->project_id;
        // dropdown project
        $projects = Project::select('id', 'title')->get();

        // kalau belum pilih project
        if (!$projectId) {
            return view('project-monitoring.cost-control.index', [
                'projects' => $projects,
                'project' => null,
                'planned' => 0,
                'actual' => 0,
                'remaining' => 0,
                'usage' => 0,
                'breakdown' => collect(),
                'alerts' => []
            ]);
        }

        // ambil project
        $project = Project::findOrFail($projectId);

        // ambil budget plan (final)
        $budgetPlan = \App\Models\BudgetPlan::where('project_id', $projectId)
            ->where('status', 'finalized')
            ->first();

        // kalau belum ada budget plan
        if (!$budgetPlan) {
            return view('project-monitoring.cost-control.index', [
                'projects' => $projects,
                'project' => $project,
                'planned' => 0,
                'actual' => 0,
                'remaining' => 0,
                'usage' => 0,
                'breakdown' => collect(),
                'alerts' => ['Belum ada budget plan finalized']
            ]);
        }

        // ambil item
        $budgetItems = \App\Models\BudgetItem::where('budget_plan_id', $budgetPlan->id)->get();

        // KPI
        $planned = $budgetPlan->total_budget;
        $actual = $budgetItems->sum('actual_cost');
        $remaining = $planned - $actual;
        $usage = $planned > 0 ? ($actual / $planned) * 100 : 0;

        // Breakdown per kategori
        $breakdown = $budgetItems->groupBy('category')->map(function ($items) {
            return [
                'planned' => $items->sum('total_cost'),
                'actual' => $items->sum('actual_cost'),
                'variance' => $items->sum('total_cost') - $items->sum('actual_cost'),
            ];
        });

        // Alerts
        $alerts = [];

        foreach ($breakdown as $category => $data) {
            if ($data['actual'] > $data['planned']) {
                $alerts[] = "$category melebihi budget";
            }

            if ($data['planned'] > 0 && ($data['actual'] / $data['planned']) > 0.8) {
                $alerts[] = "$category telah menggunakan lebih dari 80% budget";
            }
        }

        return view('project-monitoring.cost-control.index', compact(
            'projects',
            'project',
            'planned',
            'actual',
            'remaining',
            'usage',
            'breakdown',
            'alerts'
        ));
    }
}
