<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    use HasFactory;

    protected $table = 'budget_items';

    protected $fillable = [
        'budget_plan_id',
        'category',
        'description',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost',
        'notes',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Get the budget plan this item belongs to.
     */
    public function budgetPlan(): BelongsTo
    {
        return $this->belongsTo(BudgetPlan::class, 'budget_plan_id');
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
