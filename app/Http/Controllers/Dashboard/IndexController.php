<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// Load Service
use App\Services\Dashboard\DashboardService;

class IndexController extends Controller
{
    private $pageTitle = 'Dashboard';

    public function index(Request $request)
    {
        $data = DashboardService::getDashboardData();
        $data['title'] = $this->pageTitle;

        return view('dashboard.index', $data);
    }
}