<?php

namespace App\Http\Controllers;

use App\Application\Services\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(Request $request)
    {
        $stats = $this->dashboardService->getStats($request->user()->id);

        return view('dashboard.index', compact('stats'));
    }
}
