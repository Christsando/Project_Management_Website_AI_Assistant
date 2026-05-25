<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TeamManagementController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectProposalController;
use App\Http\Controllers\ProjectCharterController;
use App\Http\Controllers\ProjectScopeController;
use App\Http\Controllers\ProjectWbsController;
use App\Http\Controllers\ProjectTimelineController;
use App\Http\Controllers\ProjectBudgetController;
use App\Http\Controllers\ProjectHumanResourceController;
use App\Http\Controllers\ProjectRiskManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth/login');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Project Initiation (Project Manager & Manager)
    Route::middleware('role:project_manager,manager')->group(function () {
        Route::get('/project-initiation', function () {
            return view('project-initiation.index');
        })->name('project-initiation');
    });

    // Project Planning (Manager & PMO)
    Route::middleware('role:manager,pmo')->group(function () {
        Route::get('/project-planning', function () {
            return view('project-planning.index');
        })->name('project-planning');
    });

    Route::get('/team-management', [TeamManagementController::class, 'index'])->name('teamManagement');

    Route::middleware('role:project_manager,manager,pmo')->group(function () {
        Route::resource('projects', ProjectController::class);

        // Project Proposal nested routes
        Route::get('/projects/{project}/proposal', [ProjectProposalController::class, 'show'])->name('projects.proposal.show');
        Route::get('/projects/{project}/proposal/create', [ProjectProposalController::class, 'create'])->name('projects.proposal.create');
        Route::post('/projects/{project}/proposal', [ProjectProposalController::class, 'store'])->name('projects.proposal.store');
        Route::get('/projects/{project}/proposal/edit', [ProjectProposalController::class, 'edit'])->name('projects.proposal.edit');
        Route::put('/projects/{project}/proposal', [ProjectProposalController::class, 'update'])->name('projects.proposal.update');
        Route::post('/projects/{project}/proposal/generate-ai', [ProjectProposalController::class, 'generateAi'])->name('projects.proposal.generate_ai');

        // Project Charter nested routes
        Route::get('/projects/{project}/charter', [ProjectCharterController::class, 'show'])->name('projects.charter.show');
        Route::get('/projects/{project}/charter/create', [ProjectCharterController::class, 'create'])->name('projects.charter.create');
        Route::post('/projects/{project}/charter', [ProjectCharterController::class, 'store'])->name('projects.charter.store');
        Route::get('/projects/{project}/charter/edit', [ProjectCharterController::class, 'edit'])->name('projects.charter.edit');
        Route::put('/projects/{project}/charter', [ProjectCharterController::class, 'update'])->name('projects.charter.update');
        Route::post('/projects/{project}/charter/generate-ai', [ProjectCharterController::class, 'generateAi'])->name('projects.charter.generate_ai');

        // Project Scope routes
        Route::get('/project-planning/scope', [ProjectScopeController::class, 'index'])->name('project-planning.scope.index');
        Route::get('/projects/{project}/scope', [ProjectScopeController::class, 'show'])->name('projects.scope.show');
        Route::get('/projects/{project}/scope/create', [ProjectScopeController::class, 'create'])->name('projects.scope.create');
        Route::post('/projects/{project}/scope', [ProjectScopeController::class, 'store'])->name('projects.scope.store');
        Route::get('/projects/{project}/scope/edit', [ProjectScopeController::class, 'edit'])->name('projects.scope.edit');
        Route::put('/projects/{project}/scope', [ProjectScopeController::class, 'update'])->name('projects.scope.update');
        Route::post('/projects/{project}/scope/finalize', [ProjectScopeController::class, 'finalize'])->name('projects.scope.finalize');

        // Project WBS routes
        Route::get('/project-planning/wbs', [ProjectWbsController::class, 'index'])->name('project-planning.wbs.index');
        Route::get('/projects/{project}/wbs', [ProjectWbsController::class, 'show'])->name('projects.wbs.show');
        Route::get('/projects/{project}/wbs/create', [ProjectWbsController::class, 'create'])->name('projects.wbs.create');
        Route::post('/projects/{project}/wbs', [ProjectWbsController::class, 'store'])->name('projects.wbs.store');
        Route::get('/projects/{project}/wbs/{wbsItem}/edit', [ProjectWbsController::class, 'edit'])->name('projects.wbs.edit');
        Route::put('/projects/{project}/wbs/{wbsItem}', [ProjectWbsController::class, 'update'])->name('projects.wbs.update');
        Route::delete('/projects/{project}/wbs/{wbsItem}', [ProjectWbsController::class, 'destroy'])->name('projects.wbs.destroy');
        Route::post('/projects/{project}/wbs/finalize', [ProjectWbsController::class, 'finalize'])->name('projects.wbs.finalize');

        // Project Timeline routes
        Route::get('/project-planning/timeline', [ProjectTimelineController::class, 'index'])->name('project-planning.timeline.index');
        Route::get('/projects/{project}/timeline', [ProjectTimelineController::class, 'show'])->name('projects.timeline.show');
        Route::get('/projects/{project}/timeline/create', [ProjectTimelineController::class, 'create'])->name('projects.timeline.create');
        Route::post('/projects/{project}/timeline', [ProjectTimelineController::class, 'store'])->name('projects.timeline.store');
        Route::get('/projects/{project}/timeline/{timelineItem}/edit', [ProjectTimelineController::class, 'edit'])->name('projects.timeline.edit');
        Route::put('/projects/{project}/timeline/{timelineItem}', [ProjectTimelineController::class, 'update'])->name('projects.timeline.update');
        Route::delete('/projects/{project}/timeline/{timelineItem}', [ProjectTimelineController::class, 'destroy'])->name('projects.timeline.destroy');
        Route::post('/projects/{project}/timeline/finalize', [ProjectTimelineController::class, 'finalize'])->name('projects.timeline.finalize');

        // Project Budget routes
        Route::get('/project-planning/budget', [ProjectBudgetController::class, 'index'])->name('project-planning.budget.index');
        Route::get('/projects/{project}/budget', [ProjectBudgetController::class, 'show'])->name('projects.budget.show');
        Route::get('/projects/{project}/budget/create', [ProjectBudgetController::class, 'create'])->name('projects.budget.create');
        Route::post('/projects/{project}/budget', [ProjectBudgetController::class, 'store'])->name('projects.budget.store');
        Route::get('/projects/{project}/budget/edit', [ProjectBudgetController::class, 'edit'])->name('projects.budget.edit');
        Route::put('/projects/{project}/budget', [ProjectBudgetController::class, 'update'])->name('projects.budget.update');
        Route::post('/projects/{project}/budget/items', [ProjectBudgetController::class, 'addItem'])->name('projects.budget.items.add');
        Route::put('/projects/{project}/budget/items/{budgetItem}', [ProjectBudgetController::class, 'updateItem'])->name('projects.budget.items.update');
        Route::delete('/projects/{project}/budget/items/{budgetItem}', [ProjectBudgetController::class, 'deleteItem'])->name('projects.budget.items.delete');
        Route::post('/projects/{project}/budget/finalize', [ProjectBudgetController::class, 'finalize'])->name('projects.budget.finalize');

        // Project Human Resource routes
        Route::get('/project-planning/human-resource', [ProjectHumanResourceController::class, 'index'])->name('project-planning.human-resource.index');
        Route::get('/projects/{project}/human-resource', [ProjectHumanResourceController::class, 'show'])->name('projects.human-resource.show');
        Route::get('/projects/{project}/human-resource/create', [ProjectHumanResourceController::class, 'create'])->name('projects.human-resource.create');
        Route::post('/projects/{project}/human-resource', [ProjectHumanResourceController::class, 'store'])->name('projects.human-resource.store');
        Route::get('/projects/{project}/human-resource/edit', [ProjectHumanResourceController::class, 'edit'])->name('projects.human-resource.edit');
        Route::put('/projects/{project}/human-resource', [ProjectHumanResourceController::class, 'update'])->name('projects.human-resource.update');
        Route::post('/projects/{project}/human-resource/items', [ProjectHumanResourceController::class, 'addItem'])->name('projects.human-resource.items.add');
        Route::put('/projects/{project}/human-resource/items/{humanResourceItem}', [ProjectHumanResourceController::class, 'updateItem'])->name('projects.human-resource.items.update');
        Route::delete('/projects/{project}/human-resource/items/{humanResourceItem}', [ProjectHumanResourceController::class, 'deleteItem'])->name('projects.human-resource.items.delete');
        Route::post('/projects/{project}/human-resource/finalize', [ProjectHumanResourceController::class, 'finalize'])->name('projects.human-resource.finalize');

        // Project Risk Management routes
        Route::get('/project-planning/risk-management', [ProjectRiskManagementController::class, 'index'])->name('project-planning.risk-management.index');
        Route::get('/projects/{project}/risk-management', [ProjectRiskManagementController::class, 'show'])->name('projects.risk-management.show');
        Route::get('/projects/{project}/risk-management/create', [ProjectRiskManagementController::class, 'create'])->name('projects.risk-management.create');
        Route::post('/projects/{project}/risk-management', [ProjectRiskManagementController::class, 'store'])->name('projects.risk-management.store');
        Route::get('/projects/{project}/risk-management/edit', [ProjectRiskManagementController::class, 'edit'])->name('projects.risk-management.edit');
        Route::put('/projects/{project}/risk-management', [ProjectRiskManagementController::class, 'update'])->name('projects.risk-management.update');
        Route::post('/projects/{project}/risk-management/items', [ProjectRiskManagementController::class, 'addItem'])->name('projects.risk-management.items.add');
        Route::put('/projects/{project}/risk-management/items/{riskItem}', [ProjectRiskManagementController::class, 'updateItem'])->name('projects.risk-management.items.update');
        Route::delete('/projects/{project}/risk-management/items/{riskItem}', [ProjectRiskManagementController::class, 'deleteItem'])->name('projects.risk-management.items.delete');
        Route::post('/projects/{project}/risk-management/generate-ai', [ProjectRiskManagementController::class, 'generateAi'])->name('projects.risk-management.generate_ai');
        Route::post('/projects/{project}/risk-management/finalize', [ProjectRiskManagementController::class, 'finalize'])->name('projects.risk-management.finalize');
    });

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
