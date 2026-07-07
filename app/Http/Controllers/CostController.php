<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

class CostController extends Controller
{
    public function index()
    {
        $projects = Project::with(['owner', 'projectManager'])
            ->select(
                'id',
                'title',
                'owner_id',
                'manager_id',
                'start_date',
                'end_date'
            )
            ->get();

        return view('project-monitoring.cost-control.index', compact('projects'));
    }

    public function show(Project $project)
    {
        $projects = Project::with(['owner', 'projectManager'])
            ->select(
                'id',
                'title',
                'owner_id',
                'manager_id',
                'start_date',
                'end_date'
            )
            ->get();

        $budgetPlan = \App\Models\BudgetPlan::where('project_id', $project->id)
            ->where('status', 'finalized')
            ->first();

        if (!$budgetPlan) {
            return view('project-monitoring.cost-control.show', [
                'project' => $project,
                'planned' => 0,
                'actual' => 0,
                'remaining' => 0,
                'usage' => 0,
                'breakdown' => collect(),
                'alerts' => ['Belum ada budget plan finalized'],
            ]);
        }

        $budgetItems = \App\Models\BudgetItem::where('budget_plan_id', $budgetPlan->id)->get();
        $planned = $budgetPlan->total_budget;
        $actual = $budgetItems->sum('actual_cost');
        $remaining = $planned - $actual;
        $usage = $planned > 0 ? ($actual / $planned) * 100 : 0;

        $breakdown = $budgetItems->groupBy('category')->map(function ($items) {
            return [
                'planned' => $items->sum('total_cost'),
                'actual' => $items->sum('actual_cost'),
                'variance' => $items->sum('total_cost') - $items->sum('actual_cost'),
            ];
        });

        $alerts = [];

        foreach ($breakdown as $category => $data) {
            if ($data['actual'] > $data['planned']) {
                $alerts[] = "$category melebihi budget";
            }

            if ($data['planned'] > 0 && ($data['actual'] / $data['planned']) > 0.8) {
                $alerts[] = "$category telah menggunakan lebih dari 80% budget";
            }
        }

        return view('project-monitoring.cost-control.show', compact(
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
