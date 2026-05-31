<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HumanResourceItem extends Model
{
    use HasFactory;

    protected $table = 'human_resource_items';

    protected $fillable = [
        'human_resource_plan_id',
        'wbs_item_id',
        'team_member_id',
        'role_name',
        'required_skill',
        'job_description',
        'person_in_charge',
        'workload_percentage',
        'estimated_work_days',
        'quantity',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'workload_percentage' => 'integer',
        'estimated_work_days' => 'integer',
        'quantity' => 'integer',
        'team_member_id' => 'integer',
    ];

    /**
     * Get the team member associated with this resource item.
     */
    public function teamMember(): BelongsTo
    {
        return $this->belongsTo(TeamMember::class, 'team_member_id');
    }

    /**
     * Get the plan that this item belongs to.
     */
    public function humanResourcePlan(): BelongsTo
    {
        return $this->belongsTo(HumanResourcePlan::class, 'human_resource_plan_id');
    }

    /**
     * Get the WBS item associated with this resource item.
     */
    public function wbsItem(): BelongsTo
    {
        return $this->belongsTo(WbsItem::class, 'wbs_item_id');
    }

    /**
     * Get the user who created this item.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated this item.
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
