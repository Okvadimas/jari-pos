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
        $data           = DashboardService::getDashboardData();
        $data['title']  = $this->pageTitle;
        $data['js']     = 'resources/js/pages/dashboard/index.js';
        $data['css']    = 'resources/css/pages/dashboard/index.css';

        return view('dashboard.index', $data);
    }
}