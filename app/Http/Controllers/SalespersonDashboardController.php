<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lead;
use App\Models\Sale;
use App\Models\Task;
use App\Models\Attendance;
use App\Models\Meeting;
use App\Models\LeadStatus;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SalespersonDashboardController extends Controller
{
    /**
     * Display salesperson dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $now = Carbon::now();
        
        // Get total leads
        $totalLeads = Lead::where('user_id', $user->id)->count();
        
        // Get monthly sales
        $monthlySales = Sale::where('user_id', $user->id)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount');
            
        // Get today's meetings
        $todayMeetings = Meeting::where('user_id', $user->id)
            ->whereDate('meeting_date', $now)
            ->count();
            
        // Calculate target achievement
        $currentPlan = $user->getCurrentMonthPlan();
        $targetAchievement = 0;
        if ($currentPlan) {
            $leadPercentage = $currentPlan->getLeadAchievementPercentage();
            $salesPercentage = $currentPlan->getSalesAchievementPercentage();
            $targetAchievement = round(($leadPercentage + $salesPercentage) / 2);
        }
        
        // Get lead statuses with their leads
        $leadStatuses = LeadStatus::with(['leads' => function($query) use ($user) {
            $query->where('user_id', $user->id)
                ->orderBy('created_at', 'desc');
        }])->get();
        
        // Get meetings for calendar
        $meetings = Meeting::where('user_id', $user->id)
            ->where('meeting_date', '>=', $now)
            ->get()
            ->map(function($meeting) {
                return [
                    'id' => $meeting->id,
                    'title' => $meeting->title,
                    'start' => $meeting->meeting_date->format('Y-m-d H:i:s'),
                    'end' => $meeting->meeting_date->addHour()->format('Y-m-d H:i:s'),
                    'description' => $meeting->description,
                    'location' => $meeting->location,
                    'backgroundColor' => $meeting->status === 'completed' ? '#10B981' : '#3B82F6',
                    'borderColor' => $meeting->status === 'completed' ? '#059669' : '#2563EB'
                ];
            });
            
        return view('dashboard.salesperson.salesperson-dashboard', compact(
            'totalLeads',
            'monthlySales',
            'todayMeetings',
            'targetAchievement',
            'leadStatuses',
            'meetings'
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
