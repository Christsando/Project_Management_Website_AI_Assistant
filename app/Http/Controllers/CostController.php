<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\BudgetPlan;
use App\Models\BudgetItem;

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
                'description' => $items->pluck('description')->implode(', '),
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

        $categories = [
            'software' => ['label' => 'Software',],
            'hardware' => ['label' => 'Hardware',],
            'operational' => ['label' => 'Operational',],
            'transportation' => ['label' => 'Transportation',],
            'training' => ['label' => 'Training',],
            'other' => ['label' => 'Other',],
        ];

        return view('project-monitoring.cost-control.show', compact(
            'project',
            'planned',
            'actual',
            'remaining',
            'usage',
            'breakdown',
            'alerts',
            'categories'
        ));
    }

    public function storeItem(Request $request, Project $project)
    {
        $validated = $request->validate([
            'category'    => 'required|string',
            'description' => 'required|string|max:255',
            'quantity'    => 'required|integer|min:1',
            'unit'        => 'required|string|max:50',
            'unit_cost'   => 'required|numeric|min:0',
            'notes'       => 'nullable|string',
        ]);

        $budgetPlan = BudgetPlan::where('project_id', $project->id)->where('status', 'finalized')->first();

        if (!$budgetPlan) {
            return back()->with(
                'error',
                'Budget Plan belum tersedia atau belum Finalized.'
            );
        }

        BudgetItem::create([
            'budget_plan_id' => $budgetPlan->id,
            'category'       => $validated['category'],
            'description'    => $validated['description'],
            'quantity'       => $validated['quantity'],
            'unit'           => $validated['unit'],
            'unit_cost'      => $validated['unit_cost'],
            'total_cost'     => $validated['quantity'] * $validated['unit_cost'],
            'notes'          => $validated['notes'],
            'created_by'     => auth()->id(),
            'updated_by'     => auth()->id(),
        ]);

        return back()->with('success', 'Budget item berhasil ditambahkan.');
    }
}
