<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectScope;
use App\Models\User;
use App\Models\WbsItem;
use App\Models\TimelineItem;
use App\Models\BudgetPlan;
use App\Models\BudgetItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectBudgetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test guest redirection on Budget routes.
     */
    public function test_guests_cannot_access_budget(): void
    {
        $project = Project::factory()->create(['status' => 'planning']);
        $budgetPlan = BudgetPlan::factory()->create(['project_id' => $project->id]);
        $budgetItem = BudgetItem::factory()->create(['budget_plan_id' => $budgetPlan->id]);

        $this->get(route('project-planning.budget.index'))->assertRedirect('/login');
        $this->get(route('projects.budget.show', $project->id))->assertRedirect('/login');
        $this->get(route('projects.budget.create', $project->id))->assertRedirect('/login');
        $this->post(route('projects.budget.store', $project->id))->assertRedirect('/login');
        $this->get(route('projects.budget.edit', $project->id))->assertRedirect('/login');
        $this->put(route('projects.budget.update', $project->id))->assertRedirect('/login');
        $this->post(route('projects.budget.items.add', $project->id))->assertRedirect('/login');
        $this->put(route('projects.budget.items.update', [$project->id, $budgetItem->id]))->assertRedirect('/login');
        $this->delete(route('projects.budget.items.delete', [$project->id, $budgetItem->id]))->assertRedirect('/login');
        $this->post(route('projects.budget.finalize', $project->id))->assertRedirect('/login');
    }

    /**
     * Test Project Manager is blocked from all Budget Planning routes.
     */
    public function test_project_manager_is_blocked_from_budget(): void
    {
        $pm = User::factory()->create(['role' => 'Project Manager']);
        $project = Project::factory()->create(['owner_id' => $pm->id, 'status' => 'planning']);
        $budgetPlan = BudgetPlan::factory()->create(['project_id' => $project->id]);
        $budgetItem = BudgetItem::factory()->create(['budget_plan_id' => $budgetPlan->id]);

        $this->actingAs($pm)->get(route('project-planning.budget.index'))->assertStatus(403);
        $this->actingAs($pm)->get(route('projects.budget.show', $project->id))->assertStatus(403);
        $this->actingAs($pm)->get(route('projects.budget.create', $project->id))->assertStatus(403);
        $this->actingAs($pm)->post(route('projects.budget.store', $project->id))->assertStatus(403);
        $this->actingAs($pm)->get(route('projects.budget.edit', $project->id))->assertStatus(403);
        $this->actingAs($pm)->put(route('projects.budget.update', $project->id))->assertStatus(403);
        $this->actingAs($pm)->post(route('projects.budget.items.add', $project->id))->assertStatus(403);
        $this->actingAs($pm)->put(route('projects.budget.items.update', [$project->id, $budgetItem->id]))->assertStatus(403);
        $this->actingAs($pm)->delete(route('projects.budget.items.delete', [$project->id, $budgetItem->id]))->assertStatus(403);
        $this->actingAs($pm)->post(route('projects.budget.finalize', $project->id))->assertStatus(403);
    }

    /**
     * Test PMO is restricted to read-only access on Budget Planning.
     */
    public function test_pmo_restricted_to_view_only_budget(): void
    {
        $pmo = User::factory()->create(['role' => 'Project Management Officer']);
        $project = Project::factory()->create(['status' => 'planning']);
        
        $scope = ProjectScope::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        $wbs = WbsItem::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        TimelineItem::factory()->create(['project_id' => $project->id, 'wbs_item_id' => $wbs->id, 'status' => 'finalized']);

        $budgetPlan = BudgetPlan::factory()->create(['project_id' => $project->id]);
        $budgetItem = BudgetItem::factory()->create(['budget_plan_id' => $budgetPlan->id]);

        // PMO can see index and show detail
        $this->actingAs($pmo)->get(route('project-planning.budget.index'))->assertStatus(200);
        $this->actingAs($pmo)->get(route('projects.budget.show', $project->id))->assertStatus(200);

        // PMO blocked from write operations (403)
        $this->actingAs($pmo)->get(route('projects.budget.create', $project->id))->assertStatus(403);
        $this->actingAs($pmo)->post(route('projects.budget.store', $project->id))->assertStatus(403);
        $this->actingAs($pmo)->get(route('projects.budget.edit', $project->id))->assertStatus(403);
        $this->actingAs($pmo)->put(route('projects.budget.update', $project->id))->assertStatus(403);
        $this->actingAs($pmo)->post(route('projects.budget.items.add', $project->id))->assertStatus(403);
        $this->actingAs($pmo)->put(route('projects.budget.items.update', [$project->id, $budgetItem->id]))->assertStatus(403);
        $this->actingAs($pmo)->delete(route('projects.budget.items.delete', [$project->id, $budgetItem->id]))->assertStatus(403);
        $this->actingAs($pmo)->post(route('projects.budget.finalize', $project->id))->assertStatus(403);
    }

    /**
     * Test Manager has full CRUD access to Budget and its items.
     */
    public function test_manager_can_crud_and_finalize_budget(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        $project = Project::factory()->create(['status' => 'planning']);
        
        $scope = ProjectScope::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        $wbs = WbsItem::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        TimelineItem::factory()->create(['project_id' => $project->id, 'wbs_item_id' => $wbs->id, 'status' => 'finalized']);

        // 1. Inisialisasi Budget Plan
        $this->actingAs($manager)->get(route('projects.budget.create', $project->id))->assertStatus(200);
        $response = $this->actingAs($manager)->post(route('projects.budget.store', $project->id), [
            'notes' => 'Catatan Awal',
        ]);
        $response->assertRedirect(route('projects.budget.edit', $project->id));
        $this->assertDatabaseHas('budget_plans', [
            'project_id' => $project->id,
            'notes' => 'Catatan Awal',
            'status' => 'draft',
            'total_budget' => 0.00,
        ]);

        $budgetPlan = BudgetPlan::where('project_id', $project->id)->first();

        // 2. Add budget item and verify math (quantity x unit_cost)
        $response = $this->actingAs($manager)->post(route('projects.budget.items.add', $project->id), [
            'category' => 'human_resource',
            'description' => 'Developer',
            'quantity' => 2,
            'unit' => 'Orang',
            'unit_cost' => 5000000,
            'notes' => 'Pekerjaan dev',
        ]);
        $response->assertRedirect(route('projects.budget.edit', $project->id));
        
        $this->assertDatabaseHas('budget_items', [
            'budget_plan_id' => $budgetPlan->id,
            'category' => 'human_resource',
            'quantity' => 2,
            'unit_cost' => 5000000.00,
            'total_cost' => 10000000.00, // 2 * 5.000.000
        ]);

        // Verify parent plan total_budget recalculated
        $this->assertEquals(10000000.00, $budgetPlan->fresh()->total_budget);

        $item = BudgetItem::where('budget_plan_id', $budgetPlan->id)->first();

        // 3. Update budget item and verify total cost & plan total_budget updates
        $response = $this->actingAs($manager)->put(route('projects.budget.items.update', [$project->id, $item->id]), [
            'category' => 'human_resource',
            'description' => 'Developer Senior',
            'quantity' => 3,
            'unit' => 'Orang',
            'unit_cost' => 6000000,
            'notes' => 'Updated notes',
        ]);
        $response->assertRedirect(route('projects.budget.edit', $project->id));
        $this->assertDatabaseHas('budget_items', [
            'id' => $item->id,
            'quantity' => 3,
            'unit_cost' => 6000000.00,
            'total_cost' => 18000000.00, // 3 * 6.000.000
        ]);
        $this->assertEquals(18000000.00, $budgetPlan->fresh()->total_budget);

        // 4. Update general notes
        $response = $this->actingAs($manager)->put(route('projects.budget.update', $project->id), [
            'notes' => 'Catatan Terkini',
        ]);
        $this->assertEquals('Catatan Terkini', $budgetPlan->fresh()->notes);

        // 5. Finalize budget
        $response = $this->actingAs($manager)->post(route('projects.budget.finalize', $project->id));
        $response->assertRedirect(route('projects.budget.show', $project->id));
        $this->assertEquals('finalized', $budgetPlan->fresh()->status);
    }

    /**
     * Test Manager cannot finalize empty budget (without items).
     */
    public function test_manager_cannot_finalize_empty_budget(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        $project = Project::factory()->create(['status' => 'planning']);
        
        ProjectScope::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        $wbs = WbsItem::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        TimelineItem::factory()->create(['project_id' => $project->id, 'wbs_item_id' => $wbs->id, 'status' => 'finalized']);

        $budgetPlan = BudgetPlan::factory()->create(['project_id' => $project->id, 'status' => 'draft']);

        // Finalize while budget items count is 0 should redirect back to edit page with error
        $response = $this->actingAs($manager)->post(route('projects.budget.finalize', $project->id));
        $response->assertRedirect(route('projects.budget.edit', $project->id));
        $response->assertSessionHas('error');
        $this->assertEquals('draft', $budgetPlan->fresh()->status);
    }

    /**
     * Test finalized budget locks all write actions.
     */
    public function test_finalized_budget_locks_write_actions(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        $project = Project::factory()->create(['status' => 'planning']);
        
        ProjectScope::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        $wbs = WbsItem::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        TimelineItem::factory()->create(['project_id' => $project->id, 'wbs_item_id' => $wbs->id, 'status' => 'finalized']);

        $budgetPlan = BudgetPlan::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        $item = BudgetItem::factory()->create(['budget_plan_id' => $budgetPlan->id]);

        // Attempting to edit/update/delete should throw 403
        $this->actingAs($manager)->get(route('projects.budget.edit', $project->id))->assertStatus(403);
        $this->actingAs($manager)->put(route('projects.budget.update', $project->id), ['notes' => 'Hacker'])->assertStatus(403);
        
        $this->actingAs($manager)->post(route('projects.budget.items.add', $project->id), [
            'category' => 'tools',
            'description' => 'Pro tools',
            'quantity' => 1,
            'unit' => 'Unit',
            'unit_cost' => 1000000,
        ])->assertStatus(403);

        $this->actingAs($manager)->put(route('projects.budget.items.update', [$project->id, $item->id]), [
            'category' => 'tools',
            'description' => 'Pro tools',
            'quantity' => 2,
            'unit' => 'Unit',
            'unit_cost' => 1000000,
        ])->assertStatus(403);

        $this->actingAs($manager)->delete(route('projects.budget.items.delete', [$project->id, $item->id]))->assertStatus(403);
    }

    /**
     * Test predecessor guards check on Budget routes (Scope, WBS, Timeline must be finalized).
     */
    public function test_budget_blocked_if_timeline_not_finalized(): void
    {
        $manager = User::factory()->create(['role' => 'Manager']);
        $project = Project::factory()->create(['status' => 'planning']);
        
        // Scope and WBS finalized, but Timeline is NOT finalized (it's draft or missing)
        ProjectScope::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);
        WbsItem::factory()->create(['project_id' => $project->id, 'status' => 'finalized']);

        // Accessing budget routes should abort with 403
        $this->actingAs($manager)->get(route('projects.budget.create', $project->id))->assertStatus(403);
        $this->actingAs($manager)->post(route('projects.budget.store', $project->id), [])->assertStatus(403);
    }
}
