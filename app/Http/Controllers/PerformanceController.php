<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerformanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        // Get current month's data
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        
        // Sales data
        $monthlySales = Sale::where('user_id', $user->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');
            
        $monthlyTarget = $user->monthly_sales_target ?? 0;
        
        // Leads data
        $monthlyLeads = Lead::where('user_id', $user->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();
            
        $monthlyLeadTarget = $user->monthly_leads_target ?? 0;
        
        // Conversion rate
        $convertedLeads = Lead::where('user_id', $user->id)
            ->where('status', 'converted')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();
            
        $conversionRate = $monthlyLeads > 0 ? ($convertedLeads / $monthlyLeads) * 100 : 0;
        
        // Performance rating
        $performanceRating = $this->calculatePerformanceRating(
            $monthlySales,
            $monthlyTarget,
            $monthlyLeads,
            $monthlyLeadTarget,
            $conversionRate
        );
        
        // Historical data for charts
        $salesHistory = Sale::where('user_id', $user->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->groupBy('date')
            ->get();
            
        $leadsHistory = Lead::where('user_id', $user->id)
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->get();
            
        return view('dashboard.salesperson.performance.index', compact(
            'monthlySales',
            'monthlyTarget',
            'monthlyLeads',
            'monthlyLeadTarget',
            'conversionRate',
            'performanceRating',
            'salesHistory',
            'leadsHistory'
        ));
    }
    
    private function calculatePerformanceRating($sales, $target, $leads, $leadTarget, $conversionRate)
    {
        $salesAchievement = $target > 0 ? ($sales / $target) * 100 : 0;
        $leadsAchievement = $leadTarget > 0 ? ($leads / $leadTarget) * 100 : 0;
        
        // Weighted average of achievements
        $rating = ($salesAchievement * 0.6) + ($leadsAchievement * 0.3) + ($conversionRate * 0.1);
        
        if ($rating >= 90) return 'Excellent';
        if ($rating >= 80) return 'Good';
        if ($rating >= 70) return 'Average';
        if ($rating >= 60) return 'Fair';
        return 'Poor';
    }
    
    public function getPerformanceData(Request $request)
    {
        $user = Auth::user();
        $period = $request->period ?? 'month';
        $now = Carbon::now();
        
        switch ($period) {
            case 'week':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'quarter':
                $start = $now->copy()->startOfQuarter();
                $end = $now->copy()->endOfQuarter();
                break;
            case 'year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
        }
        
        $data = [
            'sales' => Sale::where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount'),
            'leads' => Lead::where('user_id', $user->id)
                ->whereBetween('created_at', [$start, $end])
                ->count(),
            'converted_leads' => Lead::where('user_id', $user->id)
                ->where('status', 'converted')
                ->whereBetween('created_at', [$start, $end])
                ->count()
        ];
        
        return response()->json($data);
    }
} 