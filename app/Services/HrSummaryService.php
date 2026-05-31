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
            $avgWorkload = round($hrItems->avg('workload_percentage') ?: 0);

            $pics = $hrItems
                ->whereNotNull('person_in_charge')
                ->where('person_in_charge', '!=', '')
                ->groupBy('person_in_charge');

            foreach ($pics as $items) {
                $wl = $items->sum('workload_percentage');

                if ($wl > 85) $overloadCount++;
                elseif ($wl >= 60) $optimalCount++;
                else $underloadCount++;
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