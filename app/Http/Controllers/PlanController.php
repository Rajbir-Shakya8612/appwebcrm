<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Lead;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PlanController extends Controller
{
    use AuthorizesRequests;

    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        $plans = Plan::where('user_id', $user->id)
            ->where('year', $now->year)
            ->orderBy('month', 'desc')
            ->get();
            
        return view('dashboard.salesperson.plans.index', compact('plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2024',
            'type' => 'required|in:monthly,quarterly,yearly',
            'lead_target' => 'required|integer|min:0',
            'sales_target' => 'required|numeric|min:0',
            'description' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        // Check if plan already exists
        $existingPlan = Plan::where('user_id', Auth::id())
            ->where('month', $request->month)
            ->where('year', $request->year)
            ->first();
            
        if ($existingPlan) {
            return response()->json([
                'success' => false,
                'message' => 'Plan already exists for this month'
            ], 400);
        }

        $plan = Plan::create([
            'user_id' => Auth::id(),
            'month' => $request->month,
            'year' => $request->year,
            'type' => $request->type,
            'lead_target' => $request->lead_target,
            'sales_target' => $request->sales_target,
            'description' => $request->description,
            'notes' => $request->notes,
            'status' => 'active',
            'achievements' => [
                'leads' => 0,
                'sales' => 0,
                'updated_at' => now()
            ]
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Plan created successfully',
            'plan' => $plan
        ]);
    }

    public function show(Plan $plan)
    {
        $this->authorize('view', $plan);
        
        // Get actual achievements
        $startDate = Carbon::create($plan->year, $plan->month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();
        
        $leads = Lead::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $sales = Sale::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $plan->updateAchievements($leads, $sales);
        
        return view('dashboard.salesperson.plans.show', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $this->authorize('update', $plan);
        
        $request->validate([
            'lead_target' => 'required|integer|min:0',
            'sales_target' => 'required|numeric|min:0',
            'description' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $plan->update($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Plan updated successfully',
            'plan' => $plan
        ]);
    }

    public function getCurrentMonthPlan()
    {
        $now = Carbon::now();
        
        $plan = Plan::where('user_id', Auth::id())
            ->where('month', $now->month)
            ->where('year', $now->year)
            ->first();
            
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'No plan found for current month'
            ], 404);
        }
        
        // Get actual achievements
        $startDate = $now->copy()->startOfMonth();
        $endDate = $now->copy()->endOfMonth();
        
        $leads = Lead::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();
            
        $sales = Sale::where('user_id', Auth::id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('amount');
            
        $plan->updateAchievements($leads, $sales);
        
        return response()->json([
            'success' => true,
            'plan' => $plan
        ]);
    }

    public function getQuarterlyPlan()
    {
        $now = Carbon::now();
        $quarter = ceil($now->month / 3);
        
        $plans = Plan::where('user_id', Auth::id())
            ->where('year', $now->year)
            ->where('type', 'quarterly')
            ->whereIn('month', [($quarter - 1) * 3 + 1, ($quarter - 1) * 3 + 2, ($quarter - 1) * 3 + 3])
            ->get();
            
        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }

    public function getYearlyPlan()
    {
        $now = Carbon::now();
        
        $plans = Plan::where('user_id', Auth::id())
            ->where('year', $now->year)
            ->where('type', 'yearly')
            ->get();
            
        return response()->json([
            'success' => true,
            'plans' => $plans
        ]);
    }
} 