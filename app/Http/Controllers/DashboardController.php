<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;
use App\Models\TeamMember;


class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $members = TeamMember::where('is_active', true)
            ->withSum('humanResourceItems', 'workload_percentage')
            ->get()
            ->sortByDesc('current_workload_percentage') // sekarang aman
            ->take(5);

        $data = $dashboardService->getDashboardData(auth()->user());

        // return view('dashboard.index', $data, compact('members'));
        return view('dashboard.index', array_merge($data, [
            'members' => $members
        ]));
    }
}
