<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SalespersonDashboardController extends Controller
{
    public function index()
    {
        return view('dashboard.salesperson-dashboard');
    }
}
