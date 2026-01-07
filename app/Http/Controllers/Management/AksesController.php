<?php

namespace App\Http\Controllers\Management;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AksesController extends Controller
{
    public function index(Request $request) {
        return view('management.akses.index');
    }

}