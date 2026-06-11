<?php

namespace App\Services;

class HrSummaryService
{
    public function calculate($hrPlan, $hrItems)
    {
        $avgWorkload = 0;
        $overloadCount = 0;
        $optimalCount = 0;
        $underloadCount = 0;

        if ($hrPlan && $hrItems->count() > 0) {
            $grouped = $hrItems->groupBy('team_member_id');
            $totalWorkloads = [];

            foreach ($grouped as $items) {
                $wl = $items->sum('workload_percentage');
                $totalWorkloads[] = $wl;

                // kategori
                if ($wl > 85) $overloadCount++;
                elseif ($wl >= 60) $optimalCount++;
                else $underloadCount++;
            }

            if (count($totalWorkloads) > 0) {
                $avgWorkload = round(array_sum($totalWorkloads) / count($totalWorkloads));
            }
        }

        return compact(
            'avgWorkload',
            'overloadCount',
            'optimalCount',
            'underloadCount'
        );
    }
}
