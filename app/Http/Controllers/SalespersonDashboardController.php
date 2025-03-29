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
use App\Models\Leave;

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

        $monthlyLeads = $user->leads()
            ->whereMonth('created_at', now()->month)
            ->count();
        $lastMonthLeads = $user->leads()
            ->whereMonth('created_at', now()->subMonth())
            ->count();
        $leadChange = $lastMonthLeads > 0 ? (($monthlyLeads - $lastMonthLeads) / $lastMonthLeads) * 100 : 0;

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

        // Get user's tasks
        $tasks = Task::where('assignee_id', $user->id)
            ->where('status', '!=', 'completed')
            ->orderBy('due_date', 'asc')
            ->get();

        // Get meetings for calendar
        $meetings = Meeting::where('user_id', $user->id)
            ->where('meeting_date', '>=', $now)
            ->get()
            ->map(function($meeting) {
                return [
                    'id' => "meeting-{$meeting->id}",
                    'title' => $meeting->title,
                    'start' => $meeting->meeting_date->format('Y-m-d H:i:s'),
                    'end' => $meeting->meeting_date->addHour()->format('Y-m-d H:i:s'),
                    'description' => $meeting->description,
                    'location' => $meeting->location,
                    'status' => $meeting->status,
                    'attendees' => $meeting->attendees,
                    'notes' => $meeting->notes,
                    'backgroundColor' => $meeting->status === 'completed' ? '#10B981' : '#3B82F6',
                    'borderColor' => $meeting->status === 'completed' ? '#059669' : '#2563EB'
                ];
            });

        // Get leads for calendar
        $leads = Lead::where('user_id', $user->id)
            ->with('status')
            ->get()
            ->map(function($lead) {
                return [
                    'id' => "lead-{$lead->id}",
                    'title' => $lead->name,
                    'start' => $lead->created_at->format('Y-m-d'),
                    'end' => $lead->created_at->format('Y-m-d'),
                    'description' => $lead->description,
                    'company' => $lead->company,
                    'phone' => $lead->phone,
                    'email' => $lead->email,
                    'expected_amount' => $lead->expected_value,
                    'status' => $lead->status?->name ?? 'Unknown',
                    'notes' => $lead->notes,
                    'backgroundColor' => $lead->status->color ?? '#3B82F6',
                    'borderColor' => $lead->status->color ?? '#2563EB'
                ];
            });

        // Get today's attendance
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('date', $now->toDateString())
            ->first();

        // Get today's attendance
        $attendanceShowCalender = Attendance::where('user_id', $user->id)
            ->whereDate('date', $now->toDateString())
            ->first();

        // Convert attendance to array if it exists
        $attendanceArray = [];
        if ($attendanceShowCalender) {
            // Format the check-in time in 12-hour format with AM/PM
            $checkInTime = $attendanceShowCalender->check_in_time ? $attendanceShowCalender->check_in_time->format('h:i A') : 'N/A';

            // Create the attendance event
            $attendanceArray[] = [
                'id' => $attendanceShowCalender->id,
                'title' => "{$attendanceShowCalender->status} at {$checkInTime}",
                'start' => $attendanceShowCalender->date->format('Y-m-d H:i:s'),
                'end' => $attendanceShowCalender->date->addHour()->format('Y-m-d H:i:s'),
                'description' => 'Checked in for the day',
                'location' => $attendanceShowCalender->check_in_location,
                'backgroundColor' => '#28a745',
                'borderColor' => '#059669'
            ];
        }

        // Get leaves for calendar
        $leaves = Leave::where('user_id', $user->id)
            ->whereMonth('start_date', $now->month)
            ->get()
            ->map(function($leave) {
                return [
                    'id' => "leave-{$leave->id}",
                    'title' => "{$leave->type} Leave",
                    'start' => $leave->start_date->format('Y-m-d'),
                    'end' => $leave->end_date->format('Y-m-d'),
                    'type' => $leave->type,
                    'status' => $leave->status,
                    'reason' => $leave->reason,
                    'notes' => $leave->notes,
                    'backgroundColor' => $leave->status === 'approved' ? '#10B981' : 
                                    ($leave->status === 'pending' ? '#F59E0B' : '#EF4444'),
                    'borderColor' => $leave->status === 'approved' ? '#059669' : 
                                  ($leave->status === 'pending' ? '#D97706' : '#DC2626')
                ];
            });

        // Merge all events for calendar
        $events = array_merge(
            $meetings->toArray(),
            $leads->toArray(),
            $attendanceArray,
            $leaves->toArray()
        );

        // Performance data
        $performanceData = $this->getPerformanceData($user);

        // Recent activities
        $recentActivities = $this->getRecentActivities($user);


        return view('dashboard.salesperson.salesperson-dashboard', compact(
            'totalLeads',
            'monthlySales',
            'todayMeetings',
            'targetAchievement',
            'leadStatuses',
            'meetings',
            'attendance',
            'tasks',
            'performanceData',
            'recentActivities',
            'events'
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

        // Get leads data
        $leads = $user->leads()
            ->whereMonth('created_at', now()->month)
            ->get()
            ->groupBy(fn($lead) => $lead->created_at->format('Y-m-d'));

        // Get attendance data
        $attendance = $user->attendances()
            ->whereMonth('date', now()->month)
            ->get()
            ->groupBy(fn($attendance) => $attendance->date->format('Y-m-d'));

        $present = [];
        $late = [];
        $absent = [];

        foreach ($dates as $date) {
            $dayAttendance = $attendance->get($date);
            if ($dayAttendance) {
                $present[] = $dayAttendance->where('status', 'present')->count();
                $late[] = $dayAttendance->where('status', 'late')->count();
                $absent[] = $dayAttendance->where('status', 'absent')->count();
            } else {
                $present[] = 0;
                $late[] = 0;
                $absent[] = 0;
            }
        }

        // Get current plan and achievements
        $currentPlan = $user->getCurrentMonthPlan();
        $achievements = $currentPlan ? [
            'leads' => $currentPlan->getLeadAchievementPercentage(),
            'sales' => $currentPlan->getSalesAchievementPercentage()
        ] : null;

        return [
            'labels' => $dates->map(fn($date) => Carbon::parse($date)->format('d M')),
            'leads' => $dates->map(fn($date) => $leads->get($date)?->count() ?? 0),
            'attendance' => [
                'present' => $present,
                'late' => $late,
                'absent' => $absent
            ],
            'achievements' => $achievements
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
