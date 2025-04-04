<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Sale;
use App\Models\Task;
use App\Models\User;
use App\Models\Setting;
use App\Models\Activity;
use App\Models\Location;
use App\Models\Attendance;
use Illuminate\Http\Request;
use App\Models\LocationTrack;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Yajra\DataTables\Facades\DataTables;

class AdminDashboardController extends Controller
{
   /**
    * Show admin dashboard
    */
   public function index()
   {
      \Log::info('Admin Dashboard accessed by user: ' . Auth::user()->id);
      // Get current date and previous periods
      $today = Carbon::today();
      $yesterday = Carbon::yesterday();
      $thisMonth = Carbon::now()->startOfMonth();
      $lastMonth = Carbon::now()->subMonth()->startOfMonth();

      // **Salesperson Role ID लाना**
      $salespersonRole = Role::where('slug', 'salesperson')->first();
      $salespersonRoleId = $salespersonRole ? $salespersonRole->id : null;

      // Overview Stats
      $totalSalespersons = User::where('role_id', $salespersonRoleId)->count();
      $newSalespersons = User::where('role_id', $salespersonRoleId)
         ->whereMonth('created_at', $today->month)
         ->count();

      // Attendance Stats
      $todayAttendance = $this->calculateAttendancePercentage($today, $salespersonRoleId);
      $yesterdayAttendance = $this->calculateAttendancePercentage($yesterday, $salespersonRoleId);
      $attendanceChange = $todayAttendance - $yesterdayAttendance;

      // Leads Stats
      $totalLeads = Lead::count();
      $thisMonthLeads = Lead::whereMonth('created_at', $today->month)->count();
      $lastMonthLeads = Lead::whereMonth('created_at', $lastMonth->month)->count();
      $leadChange = $lastMonthLeads > 0
         ? (($thisMonthLeads - $lastMonthLeads) / $lastMonthLeads) * 100
         : 0;

      // Sales Stats
      $totalSales = Sale::sum('amount');
      $thisMonthSales = Sale::whereMonth('created_at', $today->month)->sum('amount');
      $lastMonthSales = Sale::whereMonth('created_at', $lastMonth->month)->sum('amount');
      $salesChange = $lastMonthSales > 0
         ? (($thisMonthSales - $lastMonthSales) / $lastMonthSales) * 100
         : 0;

      // Task Board
      $todoTasks = Task::where('status', 'pending')
         ->with('assignee')
         ->orderBy('due_date')
         ->get();

      $inProgressTasks = Task::where('status', 'in_progress')
         ->with('assignee')
         ->orderBy('due_date')
         ->get();

      $doneTasks = Task::where('status', 'completed')
         ->with('assignee')
         ->orderBy('created_at', 'desc')
         ->limit(5)
         ->get();

      // Recent Activities
      $recentActivities = Activity::with('user')
         ->orderBy('created_at', 'desc')
         ->limit(10)
         ->get();

      // Attendance Chart Data
      $attendanceData = $this->getAttendanceData();

      // Performance Chart Data
      $performanceData = $this->getPerformanceData();

      $salespersons  = User::where('role_id', $salespersonRoleId)->get();

      $data = [
         'totalSalespersons' => $totalSalespersons,
         'newSalespersons' => $newSalespersons,
         'todayAttendance' => $todayAttendance,
         'attendanceChange' => $attendanceChange,
         'totalLeads' => $totalLeads,
         'leadChange' => $leadChange,
         'totalSales' => $totalSales,
         'salesChange' => $salesChange,
         'todoTasks' => $todoTasks,
         'inProgressTasks' => $inProgressTasks,
         'doneTasks' => $doneTasks,
         'recentActivities' => $recentActivities,
         'attendanceData' => $attendanceData,
         'performanceData' => $performanceData,
         'salespersons' => $salespersons,
      ];

      if (request()->wantsJson()) {
         return response()->json([
            'success' => true,
            'data' => $data
         ]);
      }

      return view('dashboard.admin.admin-dashboard', $data);
   }

   private function calculateAttendancePercentage($date, $salespersonRoleId)
   {
      $totalSalespersons = User::where('role_id', $salespersonRoleId)->count();

      if ($totalSalespersons === 0) return 0;

      $presentCount = DB::table('attendances')
         ->whereDate('date', $date)
         ->where('status', 'present')
         ->count();

      return round(($presentCount / $totalSalespersons) * 100);
   }

   private function getAttendanceData()
   {
      $days = 30;
      $present = [];
      $absent = [];
      $late = [];
      $labels = [];

      for ($i = $days - 1; $i >= 0; $i--) {
         $date = Carbon::now()->subDays($i);
         $labels[] = $date->format('M d');

         // Get attendance counts for this date
         $attendance = DB::table('attendances')
            ->whereDate('date', $date)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

         $present[] = $attendance['present'] ?? 0;
         $absent[] = $attendance['absent'] ?? 0;
         $late[] = $attendance['late'] ?? 0;
      }

      return (object)[
         'labels' => $labels,
         'present' => $present,
         'absent' => $absent,
         'late' => $late
      ];
   }

   private function getPerformanceData()
   {
      $months = 12;
      $data = [];
      $labels = [];

      for ($i = $months - 1; $i >= 0; $i--) {
         $date = Carbon::now()->subMonths($i);
         $labels[] = $date->format('M Y');
         $data[] = Sale::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->sum('amount');
      }

      return (object)[
         'labels' => $labels,
         'data' => $data
      ];
   }

   /**
    * List all users
    */
   public function users(Request $request)
   {
      $users = User::when($request->search, function ($query, $search) {
         $query->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%");
      })
         ->when($request->role, function ($query, $role) {
            $query->where('role_id', $role); // Use role_id instead of role
         })
         ->latest()
         ->paginate(10);

      if ($request->wantsJson()) {
         return response()->json($users);
      }
      $roles = Role::all();

      return view('admin.users.index', compact('users','roles'));
   }

   /**
    * Create a new user
    */
   public function createUser(Request $request)
   {
      $validated = $request->validate([
         'name' => ['required', 'string', 'max:255'],
         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
         'password' => ['required', 'string', 'min:8'],
         'role' => ['required', 'string', 'in:admin,salesperson,dealer,carpenter'],
         'phone' => ['required', 'string', 'max:20'],
         'whatsapp_number' => ['required', 'string', 'max:20'],
         'address' => ['required', 'string'],
         'pincode' => ['required', 'string', 'max:10'],
         'date_of_joining' => ['required', 'date'],
      ]);

      $validated['password'] = Hash::make($validated['password']);
      $user = User::create($validated);

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'User  created successfully',
            'user' => $user
         ], 201);
      }

      return redirect()->route('admin.users')->with('success', 'User  created successfully');
   }

   /**
    * Show user details
    */
   public function showUser(Request $request, User $user)
   {
      $user->load(['attendances' => function ($query) {
         $query->latest()->take(10);
      }, 'leads' => function ($query) {
         $query->latest()->take(10);
      }, 'sales' => function ($query) {
         $query->latest()->take(10);
      }]);

      if ($request->wantsJson()) {
         return response()->json($user);
      }

      dd($user);
      return view('admin.users.show', compact('user'));
   }

   /**
    * Update user details
    */
   public function updateUser(Request $request, User $user)
   {
      $validated = $request->validate([
         'name' => ['required', 'string', 'max:255'],
         'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
         'role' => ['required', 'string', 'in:admin,salesperson,dealer,carpenter'],
         'phone' => ['required', 'string', 'max:20'],
         'whatsapp_number' => ['required', 'string', 'max:20'],
         'address' => ['required', 'string'],
         'pincode' => ['required', 'string', 'max:10'],
         'date_of_joining' => ['required', 'date'],
         'status' => ['required', 'boolean'],
      ]);

      $user->update($validated);

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'User  updated successfully',
            'user' => $user
         ]);
      }

      return back()->with('success', 'User  updated successfully');
   }

   /**
    * Delete user
    */
   public function deleteUser(Request $request, User $user)
   {
      $user->delete();

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'User  deleted successfully'
         ]);
      }

      return redirect()->route('admin.users')->with('success', 'User  deleted successfully');
   }

   /**
    * Get attendance overview data for API
    */
   public function getAttendanceOverview(Request $request)
   {
      try {
         $date = $request->get('date') ? Carbon::parse($request->get('date')) : Carbon::today();
         $userId = $request->get('user_id');
         $status = $request->get('status');

         // Get salesperson role
         $salespersonRole = Role::where('slug', 'salesperson')->first();
         if (!$salespersonRole) {
            throw new \Exception('Salesperson role not found');
         }

         $query = Attendance::query();

         if ($userId) {
            $query->where('user_id', $userId);
         }

         if ($status) {
            $query->where('status', $status);
         }

         // Get total users for percentage calculation
         $totalUsers = User::where('role_id', $salespersonRole->id)->count();

         // Get today's stats
         $todayStats = $query->clone()
            ->whereDate('date', $date)
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

         $presentToday = $todayStats['present'] ?? 0;
         $absentToday = $todayStats['absent'] ?? 0;
         $lateToday = $todayStats['late'] ?? 0;

         // Get historical data for charts
         $days = 30;
         $labels = [];
         $present = [];
         $absent = [];
         $late = [];

         for ($i = $days - 1; $i >= 0; $i--) {
            $currentDate = $date->copy()->subDays($i);
            $labels[] = $currentDate->format('M d');

            $dailyStats = $query->clone()
               ->whereDate('date', $currentDate)
               ->selectRaw('status, COUNT(*) as count')
               ->groupBy('status')
               ->pluck('count', 'status')
               ->toArray();

            $present[] = $dailyStats['present'] ?? 0;
            $absent[] = $dailyStats['absent'] ?? 0;
            $late[] = $dailyStats['late'] ?? 0;
         }

         return response()->json([
            'labels' => $labels,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'todayAttendance' => $totalUsers > 0 ? round(($presentToday / $totalUsers) * 100) : 0,
            'presentCount' => $presentToday,
            'absentCount' => $absentToday,
            'lateCount' => $lateToday
         ]);

      } catch (\Exception $e) {
         Log::error('Error in attendance overview: ' . $e->getMessage());
         return response()->json(['error' => 'Failed to load attendance data'], 500);
      }
   }

   /**
    * Get performance overview data for API
    */
   public function getPerformanceOverview(Request $request)
   {
      $filter = $request->get('filter', 'month');
      $months = match ($filter) {
         'week' => 1,
         'month' => 1,
         'year' => 12,
         default => 1
      };

      $data = [];
      $labels = [];

      for ($i = $months - 1; $i >= 0; $i--) {
         $date = Carbon::now()->subMonths($i);
         $labels[] = $date->format('M Y');
         $data[] = Sale::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->sum('amount');
      }

      return response()->json([
         'labels' => $labels,
         'data' => $data
      ]);
   }

   /**
    * Get recent activities for API
    */
   public function getRecentActivities(Request $request)
   {
      $activities = Activity::with('user')
         ->orderBy('created_at', 'desc')
         ->limit(10)
         ->get();

      return response()->json($activities);
   }

   /**
    * Tasks Management
    */
   public function tasks(Request $request)
   {
      $query = Task::with('assignee')
         ->when($request->status, function ($query, $status) {
            $query->where('status', $status);
         })
         ->when($request->type, function ($query, $type) {
            $query->where('type', $type);
         })
         ->when($request->assignee, function ($query, $assignee) {
            $query->where('assignee_id', $assignee);
         })
         ->when($request->search, function ($query, $search) {
            $query->where('title', 'like', "%{$search}%")
               ->orWhere('description', 'like', "%{$search}%");
         })
         ->latest();

      $tasks = $query->paginate(10);
      $salespersons = User::where('role_id', Role::where('slug', 'salesperson')->first()->id)->get();

      if ($request->wantsJson()) {
         return response()->json($tasks);
      }

      return view('admin.tasks.index', compact('tasks', 'salespersons'));
   }

   /**
    * Show the task edit form
    */
   public function editTask(Task $task)
   {
       $salespersons = User::where('role_id', Role::where('slug', 'salesperson')->first()->id)->get();
       return view('admin.tasks.edit', compact('task', 'salespersons'));
   }

   /**
    * Show a single task
    */
   public function showTask(Task $task)
   {
       $task->load('assignee');
       return view('admin.tasks.show', compact('task'));
   }

   /**
    * Create a new task
    */
   public function createTask(Request $request)
   {
       $validated = $request->validate([
           'title' => ['required', 'string', 'max:255'],
           'description' => ['required', 'string'],
           'type' => ['required', 'string', 'in:lead,sale,meeting'],
           'assignee_id' => ['required', 'exists:users,id'],
           'due_date' => ['required', 'date', 'after:today'],
       ]);

       $task = Task::create($validated);

       if ($request->wantsJson()) {
           return response()->json([
               'message' => 'Task created successfully',
               'task' => $task
           ], 201);
       }

       return redirect()->back()->with('success', 'Task created successfully');
   }

   /**
    * Update a task
    */
   public function updateTask(Request $request, Task $task)
   {

       $validated = $request->validate([
           'title' => ['required', 'string', 'max:255'],
           'description' => ['required', 'string'],
           'type' => ['required', 'string', 'in:lead,sale,meeting'],
           'assignee_id' => ['required', 'exists:users,id'],
           'due_date' => ['required', 'date', 'after_or_equal:today'],
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],

      ]);

       $task->update($validated);

       if ($validated['status'] === 'done') {
           $task->update(['completed_at' => now()]);
       }

       if ($request->wantsJson()) {
           return response()->json([
               'message' => 'Task updated successfully',
               'task' => $task
           ]);
       }

       return redirect()->back()->with('success', 'Task updated successfully');
   }

   /**
    * Delete a task
    */
   public function deleteTask(Request $request, Task $task)
   {
       $task->delete();

       if ($request->wantsJson()) {
           return response()->json([
               'message' => 'Task deleted successfully'
           ]);
       }

       return redirect()->back()->with('success', 'Task deleted successfully');
   }

   /**
    * Update task status
    */
   public function updateTaskStatus(Request $request, Task $task)
   {
       $validated = $request->validate([
            'status' => ['required', 'string', 'in:pending,in_progress,completed'],
       ]);

       $task->update($validated);

       if ($validated['status'] === 'done') {
           $task->update(['completed_at' => now()]);
       }

       return response()->json([
           'message' => 'Task status updated successfully',
           'task' => $task
       ]);
   }

   /**
    * Attendance Management
    */
   public function attendance(Request $request)
   {
      try {
         // Get salesperson role first
         $salespersonRole = Role::where('slug', 'salesperson')->first();
         if (!$salespersonRole) {
            throw new \Exception('Salesperson role not found');
         }
         
         // Get users with salesperson role
         $users = User::where('role_id', $salespersonRole->id)->get();
         
         // Get today's stats
         $today = Carbon::today();
         $totalUsers = $users->count();
         
         $todayAttendance = Attendance::whereDate('date', $today)->get();
         $presentToday = $todayAttendance->where('status', 'present')->count();
         $absentToday = $todayAttendance->where('status', 'absent')->count();
         $lateToday = $todayAttendance->where('status', 'late')->count();
         
         $todayAttendancePercentage = $totalUsers > 0 
             ? round(($presentToday / $totalUsers) * 100) 
             : 0;

         // Handle DataTables AJAX request
         if ($request->ajax()) {
            $query = Attendance::with('user');
            
            // Apply filters if provided
            if ($request->get('user_id')) {
                $query->where('user_id', $request->get('user_id'));
            }
            if ($request->get('date')) {
                $query->whereDate('date', $request->get('date'));
            }
            if ($request->get('status')) {
                $query->where('status', $request->get('status'));
            }

            return DataTables::of($query)
                ->addColumn('user_name', function($attendance) {
                    return $attendance->user->name ?? 'N/A';
                })
                ->addColumn('formatted_date', function($attendance) {
                    return Carbon::parse($attendance->date)->format('M d, Y');
                })
                ->addColumn('formatted_status', function($attendance) {
                    $statusClass = [
                        'present' => 'success',
                        'absent' => 'danger',
                        'late' => 'warning'
                    ][$attendance->status];
                    
                    return '<span class="badge bg-'.$statusClass.'-subtle text-'.$statusClass.'">'.
                        ucfirst($attendance->status).'</span>';
                })
                ->addColumn('formatted_check_in', function($attendance) {
                    return $attendance->check_in ? Carbon::parse($attendance->check_in)->format('h:i A') : '-';
                })
                ->addColumn('formatted_check_out', function($attendance) {
                    return $attendance->check_out ? Carbon::parse($attendance->check_out)->format('h:i A') : '-';
                })
                ->addColumn('action', function($attendance) {
                    return '<button class="btn btn-sm btn-light edit-attendance" data-id="'.$attendance->id.'">
                        <i class="fas fa-edit"></i>
                    </button>';
                })
                ->rawColumns(['formatted_status', 'action'])
                ->make(true);
         }

         // Get attendance data for charts
         $chartData = $this->getAttendanceChartData();

         return view('dashboard.admin.attendance', [
             'users' => $users,
             'todayAttendance' => $todayAttendancePercentage,
             'presentCount' => $presentToday,
             'absentCount' => $absentToday,
             'lateCount' => $lateToday,
             'chartData' => $chartData
         ]);

      } catch (\Exception $e) {
          Log::error('Error in attendance page: ' . $e->getMessage());
          return back()->with('error', 'An error occurred while loading the attendance page.');
      }
   }

   private function getAttendanceChartData()
   {
      $days = 30;
      $labels = [];
      $present = [];
      $absent = [];
      $late = [];

      for ($i = $days - 1; $i >= 0; $i--) {
          $date = Carbon::today()->subDays($i);
          $labels[] = $date->format('M d');

          $dailyStats = Attendance::whereDate('date', $date)
              ->selectRaw('status, COUNT(*) as count')
              ->groupBy('status')
              ->pluck('count', 'status')
              ->toArray();

          $present[] = $dailyStats['present'] ?? 0;
          $absent[] = $dailyStats['absent'] ?? 0;
          $late[] = $dailyStats['late'] ?? 0;
      }

      return [
          'labels' => $labels,
          'present' => $present,
          'absent' => $absent,
          'late' => $late
      ];
   }

   public function showAttendance(Attendance $attendance)
   {
      return response()->json($attendance);
   }

   public function updateAttendance(Request $request, Attendance $attendance)
   {
      $validated = $request->validate([
         'status' => ['required', 'string', 'in:present,absent,late'],
         'check_in' => ['nullable', 'date_format:H:i'],
         'check_out' => ['nullable', 'date_format:H:i'],
      ]);

      $attendance->update($validated);

      return response()->json([
         'message' => 'Attendance updated successfully',
         'attendance' => $attendance
      ]);
   }

   public function exportAttendance(Request $request)
   {
      try {
         $fileName = 'attendance_' . Carbon::now()->format('Y_m_d_His') . '.csv';
         
         $headers = [
             'Content-Type' => 'text/csv',
             'Content-Disposition' => 'attachment; filename="' . $fileName . '"'
         ];

         $query = Attendance::with('user')
             ->when($request->user_id, function ($query, $userId) {
                 $query->where('user_id', $userId);
             })
             ->when($request->date, function ($query, $date) {
                 $query->whereDate('date', $date);
             })
             ->when($request->status, function ($query, $status) {
                 $query->where('status', $status);
             })
             ->orderBy('date', 'desc');

         $callback = function() use ($query) {
             $file = fopen('php://output', 'w');
             
             // Add headers
             fputcsv($file, [
                 'Employee Name',
                 'Date',
                 'Status',
                 'Check In Time',
                 'Check Out Time',
                 'Working Hours'
             ]);

             // Add data rows
             $query->chunk(100, function($attendances) use ($file) {
                 foreach ($attendances as $record) {
                     // Format check in time
                     $checkIn = $record->check_in ? Carbon::parse($record->check_in)->format('h:i A') : '-';
                     
                     // Format check out time
                     $checkOut = $record->check_out ? Carbon::parse($record->check_out)->format('h:i A') : '-';
                     
                     // Get working hours from model attribute
                     $workingHours = $record->working_hours ?? '-';

                     fputcsv($file, [
                         $record->user->name ?? 'N/A',
                         Carbon::parse($record->date)->format('M d, Y'),
                         ucfirst($record->status),
                         $checkIn,
                         $checkOut,
                         $workingHours
                     ]);
                 }
             });

             fclose($file);
         };

         return response()->stream($callback, 200, $headers);

      } catch (\Exception $e) {
          Log::error('Error exporting attendance: ' . $e->getMessage());
          return back()->with('error', 'Failed to export attendance data.');
      }
   }

   public function bulkUpdateAttendance(Request $request)
   {
      $validated = $request->validate([
         'date' => ['required', 'date'],
         'attendances' => ['required', 'array'],
         'attendances.*.user_id' => ['required', 'exists:users,id'],
         'attendances.*.status' => ['required', 'string', 'in:present,absent,late'],
      ]);

      foreach ($validated['attendances'] as $attendance) {
         Attendance::updateOrCreate(
            [
               'user_id' => $attendance['user_id'],
               'date' => $validated['date'],
            ],
            ['status' => $attendance['status']]
         );
      }

      return response()->json([
         'message' => 'Attendance updated successfully'
      ]);
   }

   /**
    * Sales Management
    */
   public function sales(Request $request)
   {
      $sales = Sale::with('user')
         ->when($request->start_date, function ($query, $date) {
            $query->whereDate('created_at', '>=', $date);
         })
         ->when($request->end_date, function ($query, $date) {
            $query->whereDate('created_at', '<=', $date);
         })
         ->latest()
         ->paginate(10);

      if ($request->wantsJson()) {
         return response()->json($sales);
      }

      return view('admin.sales.index', compact('sales'));
   }

   public function exportSales(Request $request)
   {
      $startDate = $request->get('start_date', now()->startOfMonth());
      $endDate = $request->get('end_date', now()->endOfMonth());

      $sales = Sale::with('user')
         ->whereBetween('created_at', [$startDate, $endDate])
         ->get();

      // Generate Excel/CSV file
      // Implementation depends on your export library
   }

   public function salesAnalytics(Request $request)
   {
      $period = $request->get('period', 'month');
      $data = $this->getPerformanceData();

      return response()->json($data);
   }

   /**
    * Leads Management
    */
   public function leads(Request $request)
   {
      $leads = Lead::with('user')
         ->when($request->status, function ($query, $status) {
            $query->where('status', $status);
         })
         ->when($request->search, function ($query, $search) {
            $query->where('name', 'like', "%{$search}%")
               ->orWhere('phone', 'like', "%{$search}%");
         })
         ->latest()
         ->paginate(10);

      if ($request->wantsJson()) {
         return response()->json($leads);
      }

      return view('admin.leads.index', compact('leads'));
   }

   public function exportLeads(Request $request)
   {
      $startDate = $request->get('start_date', now()->startOfMonth());
      $endDate = $request->get('end_date', now()->endOfMonth());

      $leads = Lead::with('user')
         ->whereBetween('created_at', [$startDate, $endDate])
         ->get();

      // Generate Excel/CSV file
      // Implementation depends on your export library
   }

   public function leadsAnalytics(Request $request)
   {
      $period = $request->get('period', 'month');
      $data = [
         'total' => Lead::count(),
         'converted' => Lead::where('status', 'converted')->count(),
         'pending' => Lead::where('status', 'pending')->count(),
         'lost' => Lead::where('status', 'lost')->count(),
      ];

      return response()->json($data);
   }

   /**
    * Settings Management
    */
   public function settings(Request $request)
   {
      $settings = Setting::all()->pluck('value', 'key');

      if ($request->wantsJson()) {
         return response()->json($settings);
      }

      return view('admin.settings.index', compact('settings'));
   }

   public function updateSettings(Request $request)
   {
      $validated = $request->validate([
         'company_name' => ['required', 'string', 'max:255'],
         'company_address' => ['required', 'string'],
         'company_phone' => ['required', 'string', 'max:20'],
         'company_email' => ['required', 'email'],
         'working_hours' => ['required', 'string'],
         'attendance_time' => ['required', 'date_format:H:i'],
      ]);

      foreach ($validated as $key => $value) {
         Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
         );
      }

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'Settings updated successfully'
         ]);
      }

      return back()->with('success', 'Settings updated successfully');
   }

   /**
    * Location Tracking Management
    */
   public function locations(Request $request)
   {
      $tracks = LocationTrack::with('user')
         ->when($request->user_id, function ($query, $userId) {
            $query->where('user_id', $userId);
         })
         ->when($request->date, function ($query, $date) {
            $query->whereDate('tracked_at', $date);
         })
         ->latest()
         ->paginate(10);

      if ($request->wantsJson()) {
         return response()->json($tracks);
      }

      return view('admin.locations.index', compact('tracks'));
   }

   public function createLocation(Request $request)
   {
      $validated = $request->validate([
         'user_id' => ['required', 'exists:users,id'],
         'latitude' => ['required', 'numeric', 'between:-90,90'],
         'longitude' => ['required', 'numeric', 'between:-180,180'],
         'address' => ['nullable', 'string'],
         'speed' => ['nullable', 'string'],
         'accuracy' => ['nullable', 'string'],
         'tracked_at' => ['required', 'date'],
      ]);

      $track = LocationTrack::create($validated);

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'Location tracked successfully',
            'track' => $track
         ], 201);
      }

      return redirect()->route('admin.locations.index')->with('success', 'Location tracked successfully');
   }

   public function showLocation(Request $request, LocationTrack $track)
   {
      $track->load('user');

      if ($request->wantsJson()) {
         return response()->json($track);
      }

      return view('admin.locations.show', compact('track'));
   }

   public function updateLocation(Request $request, LocationTrack $track)
   {
      $validated = $request->validate([
         'latitude' => ['required', 'numeric', 'between:-90,90'],
         'longitude' => ['required', 'numeric', 'between:-180,180'],
         'address' => ['nullable', 'string'],
         'speed' => ['nullable', 'string'],
         'accuracy' => ['nullable', 'string'],
         'tracked_at' => ['required', 'date'],
      ]);

      $track->update($validated);

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'Location track updated successfully',
            'track' => $track
         ]);
      }

      return redirect()->route('admin.locations.index')->with('success', 'Location track updated successfully');
   }

   public function deleteLocation(Request $request, LocationTrack $track)
   {
      $track->delete();

      if ($request->wantsJson()) {
         return response()->json([
            'message' => 'Location track deleted successfully'
         ]);
      }

      return redirect()->route('admin.locations.index')->with('success', 'Location track deleted successfully');
   }
}
