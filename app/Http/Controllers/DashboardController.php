<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DashboardService;

class DashboardController extends Controller
{
    public function index(DashboardService $dashboardService)
    {
        $data = $dashboardService->getDashboardData(auth()->user());
        
        return view('dashboard.index', $data);
    }
}
