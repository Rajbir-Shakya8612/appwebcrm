<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Task;
use App\Models\Attendance;
use Carbon\Carbon;

class SalespersonDashboardController extends Controller
{
    /**
     * Display salesperson dashboard
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // Today's attendance status
        $todayAttendance = $user->attendances()
            ->whereDate('created_at', today())
            ->first();

        // This month's leads
        $monthlyLeads = $user->leads()
            ->whereMonth('created_at', now()->month)
            ->count();
        $lastMonthLeads = $user->leads()
            ->whereMonth('created_at', now()->subMonth())
            ->count();
        $leadChange = $lastMonthLeads > 0 ? (($monthlyLeads - $lastMonthLeads) / $lastMonthLeads) * 100 : 0;

        // This month's sales
        $monthlySales = $user->sales()
            ->whereMonth('created_at', now()->month)
            ->sum('amount');
        $lastMonthSales = $user->sales()
            ->whereMonth('created_at', now()->subMonth())
            ->sum('amount');
        $salesChange = $lastMonthSales > 0 ? (($monthlySales - $lastMonthSales) / $lastMonthSales) * 100 : 0;

        // Tasks
        $tasks = Task::where('assignee_id', $user->id)
            ->whereIn('status', ['todo', 'in_progress'])
            ->with('assignee')
            ->get()
            ->groupBy('status');

        // Performance data
        $performanceData = $this->getPerformanceData($user);

        // Recent activities
        $recentActivities = $this->getRecentActivities($user);

        if ($request->wantsJson()) {
            return response()->json([
                'user' => $user,
                'attendance' => $todayAttendance,
                'monthlyLeads' => $monthlyLeads,
                'leadChange' => round($leadChange, 1),
                'monthlySales' => $monthlySales,
                'salesChange' => round($salesChange, 1),
                'tasks' => $tasks,
                'performanceData' => $performanceData,
                'recentActivities' => $recentActivities,
                'targetProgress' => [
                    'leads' => [
                        'current' => $monthlyLeads,
                        'target' => $user->target_leads,
                        'percentage' => $user->target_leads > 0 ? ($monthlyLeads / $user->target_leads) * 100 : 0
                    ],
                    'sales' => [
                        'current' => $monthlySales,
                        'target' => $user->target_amount,
                        'percentage' => $user->target_amount > 0 ? ($monthlySales / $user->target_amount) * 100 : 0
                    ]
                ]
            ]);
        }

        return view('dashboard.salesperson-dashboard', compact(
            'user',
            'todayAttendance',
            'monthlyLeads',
            'leadChange',
            'monthlySales',
            'salesChange',
            'tasks',
            'performanceData',
            'recentActivities'
        ));
    }

    /**
     * Get performance data for charts
     */
    private function getPerformanceData($user)
    {
        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        $dates = collect();

        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dates->push($date->format('Y-m-d'));
        }

        $leads = $user->leads()
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn($lead) => $lead->created_at->format('Y-m-d'));

        $sales = $user->sales()
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn($sale) => $sale->created_at->format('Y-m-d'));

        return [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('d M')),
            'leads' => $dates->map(fn($date) => $leads->get($date)?->count() ?? 0),
            'sales' => $dates->map(fn($date) => $sales->get($date)?->sum('amount') ?? 0),
        ];
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities($user)
    {
        $activities = collect();

        // Get recent leads
        $leads = $user->leads()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($lead) {
                return [
                    'type' => 'lead',
                    'description' => "Created new lead: {$lead->name}",
                    'details' => $lead->status,
                    'created_at' => $lead->created_at,
                ];
            });
        $activities = $activities->concat($leads);

        // Get recent sales
        $sales = $user->sales()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($sale) {
                return [
                    'type' => 'sale',
                    'description' => "Recorded new sale",
                    'details' => "₹" . number_format($sale->amount),
                    'created_at' => $sale->created_at,
                ];
            });
        $activities = $activities->concat($sales);

        // Get recent attendance
        $attendance = $user->attendances()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($attendance) {
                return [
                    'type' => 'attendance',
                    'description' => "Marked {$attendance->status}",
                    'details' => $attendance->created_at->format('h:i A'),
                    'created_at' => $attendance->created_at,
                ];
            });
        $activities = $activities->concat($attendance);

        return $activities->sortByDesc('created_at')->take(10)->values();
    }
}
